@extends('admin.layouts.app')

@section('title') Produkte @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

<style>
    :root{
        --products-bg:#ffffff;
        --products-soft:#f8fafc;
        --products-line:rgba(15,23,42,.08);
        --products-line-2:#e5e7eb;
        --products-text:#111827;
        --products-muted:#6b7280;
        --products-brand:#74b2d4;
        --products-green:#93c21c;
        --products-green-dark:#7baa18;
        --products-green-soft:#f4fae7;
        --products-blue:#2563eb;
        --products-blue-soft:#eff6ff;
        --products-success:#16a34a;
        --products-success-soft:#ecfdf5;
        --products-danger:#dc2626;
        --products-danger-soft:#fef2f2;
        --products-shadow:0 18px 45px rgba(15,23,42,.08);
        --products-shadow-lg:0 24px 60px rgba(15,23,42,.10);
    }

    .products-layout{
        display:grid;
        grid-template-columns:minmax(0,1fr) 390px;
        gap:1rem;
        align-items:start;
    }

    @media (max-width: 1399.98px){
        .products-layout{
            grid-template-columns:minmax(0,1fr) 350px;
        }
    }

    @media (max-width: 1199.98px){
        .products-layout{
            grid-template-columns:1fr;
        }
    }

    .product-main-pane{
        min-width:0;
    }

    .products-shell{
        border-radius:18px;
        background:var(--products-bg);
        box-shadow:var(--products-shadow);
        border:1px solid rgba(15,23,42,.06);
        padding:1.25rem 1.5rem;
    }

    @media (max-width: 991.98px){
        .products-shell{
            padding:1rem;
        }
    }

    .products-header{
        display:flex;
        flex-wrap:wrap;
        justify-content:space-between;
        align-items:center;
        gap:.75rem;
        margin-bottom:1rem;
    }

    .products-header-title h2{
        margin:0;
        font-size:1.3rem;
        color:var(--products-text);
        font-weight:800;
    }

    .products-header-title small{
        color:var(--products-muted);
        font-size:.8rem;
    }

    .products-header-actions{
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        align-items:center;
    }

    .products-meta-pill{
        font-size:.75rem;
        padding:.15rem .6rem;
        border-radius:999px;
        background:#eff6ff;
        color:var(--products-brand);
        display:inline-flex;
        align-items:center;
        gap:.35rem;
    }

    .products-filters-shell{
        margin-bottom:1rem;
    }

    #product-list-loading{
        display:none;
        text-align:center;
        padding:2rem 0;
        color:var(--products-muted);
    }

    #product-pagination{
        margin-top:.75rem;
    }

    .view-toggle-group{
        border-radius:999px;
        background:#f3f4f6;
        padding:2px;
        display:inline-flex;
        align-items:center;
    }

    .view-toggle-btn{
        border-radius:999px;
        border:0;
        background:transparent;
        padding:.25rem .65rem;
        font-size:.75rem;
        display:inline-flex;
        align-items:center;
        gap:.25rem;
        color:#4b5563;
        cursor:pointer;
    }

    .view-toggle-btn.active{
        background:#ffffff;
        box-shadow:0 0 0 1px rgba(148,163,184,.6);
        color:#111827;
    }

    .bulk-bar{
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:.5rem;
        font-size:.78rem;
        padding:.35rem .75rem;
        border-radius:999px;
        background:#f9fafb;
        border:1px dashed rgba(148,163,184,.7);
    }

    .bulk-bar .badge-count{
        font-weight:600;
        padding:0 .2rem;
    }

    .bulk-check{
        position:relative;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:20px;
        height:20px;
        cursor:pointer;
    }

    .bulk-check input[type="checkbox"]{
        position:absolute;
        opacity:0;
        cursor:pointer;
        width:100%;
        height:100%;
        margin:0;
    }

    .bulk-check span{
        width:18px;
        height:18px;
        border-radius:6px;
        border:1px solid rgba(148,163,184,.9);
        background:#f9fafb;
        display:inline-block;
        box-shadow:0 1px 2px rgba(15,23,42,.18);
        transition:all .12s ease-out;
        position:relative;
    }

    .bulk-check span::after{
        content:"";
        position:absolute;
        width:9px;
        height:5px;
        border-left:2px solid #ffffff;
        border-bottom:2px solid #ffffff;
        transform:rotate(-45deg) scale(0.4);
        top:4px;
        left:4px;
        opacity:0;
        transition:all .12s ease-out;
    }

    .bulk-check input:checked + span{
        background:linear-gradient(135deg,#2563eb,#22c55e);
        border-color:#74b2d4;
        box-shadow:0 0 0 1px rgba(37,99,235,.45);
    }

    .bulk-check input:checked + span::after{
        opacity:1;
        transform:rotate(-45deg) scale(1);
    }

    .product-thumb-table{
        width:52px;
        height:52px;
        border-radius:12px;
        object-fit:cover;
        border:1px solid rgba(148,163,184,.22);
        background:#f8fafc;
        box-shadow:0 4px 12px rgba(15,23,42,.08);
    }

    .product-thumb-table-placeholder{
        width:52px;
        height:52px;
        border-radius:12px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        background:#f8fafc;
        border:1px dashed rgba(148,163,184,.45);
        color:#94a3b8;
        font-size:.9rem;
    }

    .product-card-image-wrap{
        margin:-.25rem -.25rem .9rem;
        border-radius:14px;
        overflow:hidden;
        border:1px solid rgba(148,163,184,.18);
        background:linear-gradient(180deg,#f8fafc 0%,#eef2f7 100%);
        aspect-ratio:16 / 10;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .product-card-image{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .product-card-image-placeholder{
        width:100%;
        height:100%;
        display:flex;
        flex-direction:column;
        gap:.35rem;
        align-items:center;
        justify-content:center;
        color:#94a3b8;
        font-size:.8rem;
    }

    .product-duplicated-flash{
        position:relative;
        border-color:#f97316 !important;
        box-shadow:0 0 0 2px rgba(249,115,22,.45),0 18px 45px rgba(15,23,42,.25);
        background-image:linear-gradient(90deg,rgba(249,115,22,.04),rgba(59,130,246,.03));
        animation:productClonePulse .9s ease-in-out 2;
    }

    .product-duplicated-badge{
        display:inline-flex;
        align-items:center;
        font-size:.7rem;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.03em;
        padding:.08rem .45rem;
        margin-left:.35rem;
        border-radius:999px;
        color:#9a3412;
        background:rgba(254,215,170,.95);
        border:1px solid rgba(248,153,73,.7);
        box-shadow:0 1px 2px rgba(15,23,42,.25);
    }

    .product-updated-flash{
        position:relative;
        border-color:#22c55e !important;
        box-shadow:0 0 0 2px rgba(34,197,94,.35),0 18px 45px rgba(15,23,42,.22);
        background-image:linear-gradient(90deg,rgba(34,197,94,.06),rgba(59,130,246,.04));
        animation:productUpdatedPulse .9s ease-in-out 2;
    }

    .product-updated-badge{
        display:inline-flex;
        align-items:center;
        font-size:.7rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:.03em;
        padding:.08rem .45rem;
        margin-left:.35rem;
        border-radius:999px;
        color:#14532d;
        background:rgba(187,247,208,.95);
        border:1px solid rgba(34,197,94,.55);
        box-shadow:0 1px 2px rgba(15,23,42,.18);
    }

    @keyframes productClonePulse{
        0%{ transform:translateY(0); }
        50%{ transform:translateY(-1px); }
        100%{ transform:translateY(0); }
    }

    @keyframes productUpdatedPulse{
        0%{ transform:translateY(0); }
        50%{ transform:translateY(-1px); }
        100%{ transform:translateY(0); }
    }

    .product-card-grid{
        margin-bottom:1.25rem;
    }

    .product-card{
        position:relative;
        border-radius:16px;
        border:1px solid rgba(15,23,42,.06);
        box-shadow:0 14px 40px rgba(15,23,42,.08);
        transition:transform .12s ease-out, box-shadow .12s ease-out, border-color .12s ease-out;
        cursor:pointer;
        z-index:1;
    }

    .product-card:hover{
        transform:translateY(-2px);
        box-shadow:0 18px 48px rgba(15,23,42,.14);
        border-color:rgba(37,99,235,.3);
        z-index:5;
    }

    .product-card-header{
        margin-bottom:.75rem;
        gap:.75rem;
    }

    .product-card-meta{
        font-size:.75rem;
        color:#6b7280;
    }

    .product-card-title{
        margin:.1rem 0;
        font-size:1rem;
    }

    .product-card-title a{
        text-decoration:none;
    }

    .product-card-title a:hover{
        text-decoration:underline;
    }

    .product-card-submeta{
        font-size:.8rem;
        color:#6b7280;
    }

    .product-brand-badge{
        font-size:.7rem;
        background:rgba(37,99,235,.06);
        color:#74b2d4;
        border-radius:999px;
        padding:.1rem .5rem;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }

    .product-card-taxonomy{
        font-size:.8rem;
        margin-bottom:.5rem;
    }

    .product-card-lists{
        gap:.25rem;
        font-size:.7rem;
        margin-bottom:.5rem;
    }

    .product-card-no-lists{
        font-size:.75rem;
    }

    .product-card-main{
        cursor:pointer;
        margin-bottom:.65rem;
    }

    .product-card-description{
        font-size:.78rem;
        color:#6b7280;
        max-height:56px;
        overflow:hidden;
    }

    .product-card-foot{
        margin-top:auto;
        border-top:1px solid rgba(148,163,184,.25);
        padding-top:.5rem;
        margin-top:.5rem;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:.4rem;
        flex-wrap:wrap;
    }

    .product-card-dist{
        font-size:.7rem;
        margin-bottom:.4rem;
        max-height:40px;
        overflow:hidden;
    }

    .product-dist-badge{
        font-size:.7rem;
        margin-right:.25rem;
        margin-bottom:.25rem;
        background:rgba(37,99,235,.08);
        color:#1f2937;
        border-radius:999px;
        padding:.1rem .5rem;
    }

    .product-no-distributor{
        font-size:.75rem;
    }

    .product-card-actions{
        gap:.25rem;
    }

    .product-card-status{
        display:inline-flex;
        align-items:center;
        padding:.15rem .5rem;
        border-radius:999px;
        font-size:.7rem;
        font-weight:500;
        border:1px solid transparent;
    }

    .product-card-status.published{
        color:#166534;
        background:rgba(22,101,52,.06);
        border-color:rgba(22,101,52,.18);
    }

    .product-card-status.unpublished{
        color:#991b1b;
        background:rgba(153,27,27,.06);
        border-color:rgba(153,27,27,.18);
    }

    .product-table-shell{
        margin-bottom:1rem;
    }

    .product-list-table th,
    .product-list-table td{
        vertical-align:middle;
        font-size:.8rem;
    }

    .product-list-table thead th{
        font-size:.8rem;
        text-transform:uppercase;
        color:#6b7280;
        border-top:none;
        border-bottom-width:1px;
    }

    .product-list-table tbody td{
        font-size:.82rem;
        vertical-align:middle;
    }

    .product-list-table tbody tr:hover{
        background:#f9fafb;
    }

    .product-list-name{
        font-weight:600;
        color:#111827;
    }

    .product-list-name a{
        font-weight:500;
        text-decoration:none;
    }

    .product-list-name a:hover{
        text-decoration:underline;
    }

    .product-list-sub{
        font-size:.75rem;
        color:#6b7280;
    }

    .product-table-lists{
        gap:.15rem;
        font-size:.7rem;
    }

    .product-table-actions{
        gap:.25rem;
    }

    .custom-menu{
        display:none !important;
    }

    .list-menu-container{
        position:relative;
    }

    .product-menu-float{
        position:fixed;
        min-width:220px;
        max-width:260px;
        background-color:#ffffff;
        color:#111827;
        padding:.5rem 0;
        border-radius:12px;
        border:1px solid rgba(15,23,42,.12);
        box-shadow:0 18px 45px rgba(15,23,42,.25);
        z-index:9999;
        opacity:0;
        transform:scale(.95);
        transform-origin:top right;
        pointer-events:none;
    }

    .product-menu-float.show{
        opacity:1;
        transform:scale(1);
        pointer-events:auto;
        animation:productMenuFadeIn .16s ease-out;
    }

    .product-menu-float.drop-up{
        transform-origin:bottom right;
    }

    .product-menu-float .dropdown-item{
        padding:.45rem 1rem;
        font-size:.85rem;
        cursor:pointer;
        white-space:nowrap;
    }

    .product-menu-float .dropdown-item:hover{
        background:rgba(15,23,42,.04);
    }

    .product-menu-float .dropdown-divider{
        height:1px;
        margin:.35rem 0;
        background:rgba(148,163,184,.5);
    }

    @keyframes productMenuFadeIn{
        from{ opacity:0; transform:scale(.95); }
        to{ opacity:1; transform:scale(1); }
    }

    /* =========================================================
       CART SIDEBAR
    ========================================================= */
    .product-cart-sidebar{
        position:sticky;
        top:90px;
        border-radius:18px;
        background:#ffffff;
        box-shadow:var(--products-shadow);
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
    }

    .product-cart-head{
        padding:1rem 1rem .9rem;
        border-bottom:1px solid #e5e7eb;
        background:linear-gradient(180deg,#fff 0%,#fafafa 100%);
    }

    .product-cart-head h4{
        margin:0;
        font-size:1rem;
        font-weight:800;
        color:#111827;
    }

    .product-cart-head small{
        display:block;
        margin-top:.25rem;
        color:#6b7280;
        font-size:.76rem;
        line-height:1.45;
    }

    .product-cart-body{
        padding:1rem;
        display:flex;
        flex-direction:column;
        gap:.9rem;
        max-height:calc(100vh - 150px);
        overflow:auto;
    }

    .product-cart-group{
        border:1px solid #e5e7eb;
        border-radius:16px;
        padding:.9rem;
        background:#fff;
    }

    .product-cart-label{
        display:block;
        font-size:.72rem;
        font-weight:800;
        color:#6b7280;
        margin-bottom:.38rem;
        text-transform:uppercase;
        letter-spacing:.04em;
    }

    .product-cart-input,
    .product-cart-select,
    .product-cart-textarea{
        width:100%;
        min-height:40px;
        border:1px solid #dbe2ea;
        border-radius:12px;
        background:#fff;
        color:#111827;
        padding:.7rem .8rem;
        outline:none;
        transition:all .15s ease;
    }

    .product-cart-textarea{
        min-height:84px;
        resize:vertical;
    }

    .product-cart-input:focus,
    .product-cart-select:focus,
    .product-cart-textarea:focus{
        border-color:#93c21c;
        box-shadow:0 0 0 4px rgba(147,194,28,.15);
    }

    .product-cart-row{
        display:grid;
        grid-template-columns:1fr 94px;
        gap:.6rem;
    }

    .product-cart-btn{
        border:none;
        border-radius:12px;
        min-height:40px;
        padding:.7rem .95rem;
        font-weight:800;
        cursor:pointer;
        transition:all .15s ease;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.45rem;
    }

    .product-cart-btn-primary{
        background:#93c21c;
        color:#fff;
    }

    .product-cart-btn-primary:hover{
        background:#7baa18;
    }

    .product-cart-btn-soft{
        background:#fff;
        color:#111827;
        border:1px solid #e5e7eb;
    }

    .product-cart-btn-soft:hover{
        background:#f9fafb;
    }

    .product-cart-btn-danger{
        background:#fef2f2;
        color:#dc2626;
        border:1px solid rgba(239,68,68,.18);
    }

    .product-cart-meta-grid{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:.55rem;
    }

    .product-cart-stat{
        border:1px solid #e5e7eb;
        border-radius:14px;
        background:#f8fafc;
        padding:.75rem;
    }

    .product-cart-stat-label{
        font-size:.68rem;
        font-weight:800;
        color:#6b7280;
        text-transform:uppercase;
        letter-spacing:.04em;
        margin-bottom:.2rem;
    }

    .product-cart-stat-value{
        font-size:.92rem;
        font-weight:900;
        color:#111827;
    }

    .product-cart-sections{
        display:flex;
        flex-direction:column;
        gap:.7rem;
    }

    .product-cart-section{
        border:1px solid #e5e7eb;
        border-radius:16px;
        overflow:hidden;
        background:#fff;
    }

    .product-cart-section-head{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:.7rem;
        padding:.8rem .9rem;
        border-bottom:1px solid #eef2f7;
        background:#fbfdff;
    }

    .product-cart-section-title{
        display:flex;
        align-items:center;
        gap:.55rem;
        min-width:0;
    }

    .product-cart-section-dot{
        width:12px;
        height:12px;
        border-radius:999px;
        flex:0 0 auto;
    }

    .product-cart-section-name{
        font-size:.86rem;
        font-weight:800;
        color:#111827;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .product-cart-items{
        padding:.8rem;
        display:flex;
        flex-direction:column;
        gap:.6rem;
    }

    .product-cart-item{
        border:1px solid #e5e7eb;
        border-radius:14px;
        background:#fff;
        overflow:hidden;
    }

    .product-cart-item.sub{
        margin-left:18px;
        border-left:3px solid #cbd5e1;
    }

    .product-cart-item-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:.7rem;
        padding:.75rem;
    }

    .product-cart-item-left{
        min-width:0;
        flex:1;
    }

    .product-cart-item-title{
        font-size:.82rem;
        font-weight:800;
        color:#111827;
        line-height:1.35;
        margin-bottom:.2rem;
    }

    .product-cart-item-meta{
        font-size:.72rem;
        color:#6b7280;
        line-height:1.5;
    }

    .product-cart-item-controls{
        display:flex;
        align-items:center;
        gap:.35rem;
        flex-wrap:wrap;
        margin-top:.45rem;
    }

    .product-cart-mini-input{
        width:84px;
        min-height:32px;
        border:1px solid #dbe2ea;
        border-radius:10px;
        padding:.35rem .5rem;
        font-size:.76rem;
    }

    .product-cart-empty{
        text-align:center;
        padding:1rem;
        border:1px dashed #dbe2ea;
        border-radius:14px;
        color:#94a3b8;
        font-size:.8rem;
        background:#fcfcfd;
    }

    .product-cart-pill{
        display:inline-flex;
        align-items:center;
        padding:.18rem .45rem;
        border-radius:999px;
        font-size:.65rem;
        font-weight:800;
        background:#eff6ff;
        color:#2563eb;
        margin-left:.35rem;
    }

    .product-cart-helper{
        font-size:.72rem;
        color:#6b7280;
        line-height:1.55;
    }

    .product-add-cart-btn{
        border:1px solid rgba(147,194,28,.25);
        background:#f4fae7;
        color:#6d8c12;
    }

    .product-add-cart-btn:hover{
        border-color:#93c21c;
        background:#ebf6cf;
        color:#5d7710;
    }

    .btn-block{
        width:100%;
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
                        <h2 class="content-header-title float-left mb-0">Produkte</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Produktliste</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="products-layout">

                <div class="product-main-pane">
                    <div class="products-shell">

                        <div class="products-header">
                            <div class="products-header-title">
                                <h2>Artikel & Produkte</h2>
                                <small>Verwalten Sie alle Artikel, Wärmepumpen, PV-Komponenten und Zubehör zentral.</small>
                            </div>

                            <div class="products-header-actions">
                                <span class="products-meta-pill">
                                    <i class="feather icon-layers"></i>
                                    <span><span id="total-products-label">0</span> Einträge</span>
                                </span>

                                <div class="view-toggle-group">
                                    <button type="button" class="view-toggle-btn active" data-view="card" id="view-card-btn">
                                        <i class="feather icon-grid"></i> Karten
                                    </button>
                                    <button type="button" class="view-toggle-btn" data-view="list" id="view-list-btn">
                                        <i class="feather icon-list"></i> Liste
                                    </button>
                                </div>

                                <div class="bulk-bar">
                                    <span>Auswahl: <span class="badge-count" id="selected-count-label">0</span> Produkte</span>
                                    <select id="bulk-action" class="form-control form-control-sm" style="width:150px;">
                                        <option value="">Aktion wählen</option>
                                        <option value="publish">Veröffentlichen</option>
                                        <option value="unpublish">Deaktivieren</option>
                                        <option value="delete">Löschen</option>
                                    </select>
                                    <button type="button" id="bulk-apply-btn" class="btn btn-sm btn-outline-primary" disabled>
                                        Anwenden
                                    </button>
                                </div>

                                <a href="{{ route('product.create') }}" class="btn btn-primary">
                                    <i class="feather icon-plus mr-25"></i> Neues Produkt
                                </a>
                            </div>
                        </div>

                        <div class="products-filters-shell">
                            <form id="product-filter-form">
                                <div class="row align-items-end">
                                    <div class="col-xl-4 col-lg-5 col-md-6 mb-1">
                                        <label for="search">Suche</label>
                                        <div class="input-group">
                                            <input type="text" id="search" name="search" class="form-control"
                                                   placeholder="🔍 Artikelname, Art.Nr., Hersteller, Gruppe ...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="submit">Suchen</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_brand">Hersteller</label>
                                        <select id="filter_brand" name="brand_id" class="form-control select2">
                                            <option value="">Alle</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_distributor">Lieferant</label>
                                        <select id="filter_distributor" name="distributor_id" class="form-control select2">
                                            <option value="">Alle</option>
                                            @foreach($distributors as $dist)
                                                <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_article_group">Artikel-Gruppe</label>
                                        <select id="filter_article_group" name="article_group_id" class="form-control select2">
                                            <option value="">Alle</option>
                                            @foreach($articleGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_status">Status</label>
                                        <select id="filter_status" name="status" class="form-control">
                                            <option value="">Alle</option>
                                            <option value="Published">Aktiv</option>
                                            <option value="Unpublished">Inaktiv</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_category">Kategorie</label>
                                        <select id="filter_category" name="category" class="form-control">
                                            <option value="">Alle</option>
                                            <option value="Produkt">Produkt</option>
                                            <option value="Dachziegel">Dachziegel</option>
                                            <option value="Ziegel">Ziegel</option>
                                            <option value="Fenster">Fenster</option>
                                            <option value="Tür">Tür</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_sort">Sortierung</label>
                                        <select id="filter_sort" class="form-control">
                                            <option value="created_at|desc">Neueste zuerst</option>
                                            <option value="created_at|asc">Älteste zuerst</option>
                                            <option value="product|asc">Name A–Z</option>
                                            <option value="product|desc">Name Z–A</option>
                                            <option value="brand|asc">Hersteller A–Z</option>
                                            <option value="brand|desc">Hersteller Z–A</option>
                                            <option value="article_no|asc">Art.Nr. aufsteigend</option>
                                            <option value="article_no|desc">Art.Nr. absteigend</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label for="filter_per_page">Pro Seite</label>
                                        <select id="filter_per_page" class="form-control">
                                            <option value="12">12</option>
                                            <option value="24">24</option>
                                            <option value="48">48</option>
                                        </select>
                                    </div>

                                    <div class="col-xl-2 col-lg-3 col-md-4 mb-1">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" id="filter-reset-btn" class="btn btn-outline-secondary btn-block">
                                                Filter zurücksetzen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div id="product-list-loading">
                            <i class="feather icon-loader"></i> Produkte werden geladen ...
                        </div>

                        <div id="product-list" class="mt-1"></div>
                        <div id="product-pagination" class="mt-1"></div>
                    </div>
                </div>

                <aside class="product-cart-sidebar">
                    <div class="product-cart-head">
                        <h4>Master-Set Cart</h4>
                        <small>
                            Produkte sammeln, in Sektionen organisieren und später in einen neuen oder bestehenden Master Set umwandeln.
                        </small>
                    </div>

                    <div class="product-cart-body">
                        <input type="hidden" id="ms-cart-id" value="">

                        <div class="product-cart-group">
                            <label class="product-cart-label">Artikelgruppe</label>
                            <select id="ms-cart-article-group" class="product-cart-select">
                                <option value="">Bitte wählen</option>
                                @foreach($articleGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                @endforeach
                            </select>

                            <div class="mt-1"></div>

                            <label class="product-cart-label">Modus</label>
                            <select id="ms-cart-mode" class="product-cart-select">
                                <option value="new">Neuer Master Set</option>
                                <option value="existing">In bestehenden Master Set einfügen</option>
                            </select>

                            <div id="ms-cart-new-wrap">
                                <div class="mt-1"></div>
                                <label class="product-cart-label">Name</label>
                                <input type="text" id="ms-cart-name" class="product-cart-input" placeholder="z. B. PV Set Premium">
                            </div>

                            <div id="ms-cart-existing-wrap" style="display:none;">
                                <div class="mt-1"></div>
                                <label class="product-cart-label">Bestehender Master Set</label>
                                <select id="ms-cart-master-set" class="product-cart-select">
                                    <option value="">Bitte zuerst Artikelgruppe wählen</option>
                                </select>
                            </div>

                            <div class="mt-1"></div>

                            <label class="product-cart-label">Beschreibung</label>
                            <textarea id="ms-cart-description" class="product-cart-textarea" placeholder="Kurze Beschreibung ..."></textarea>

                            <div class="mt-1"></div>

                            <button type="button" class="product-cart-btn product-cart-btn-primary btn-block" id="ms-cart-save-btn">
                                Cart speichern / starten
                            </button>
                        </div>

                        <div class="product-cart-group">
                            <div class="product-cart-meta-grid">
                                <div class="product-cart-stat">
                                    <div class="product-cart-stat-label">Main</div>
                                    <div class="product-cart-stat-value" id="ms-cart-main-total">0,00 €</div>
                                </div>
                                <div class="product-cart-stat">
                                    <div class="product-cart-stat-label">Sub</div>
                                    <div class="product-cart-stat-value" id="ms-cart-sub-total">0,00 €</div>
                                </div>
                                <div class="product-cart-stat">
                                    <div class="product-cart-stat-label">Gesamt</div>
                                    <div class="product-cart-stat-value" id="ms-cart-total">0,00 €</div>
                                </div>
                            </div>
                        </div>

                        <div class="product-cart-group">
                            <div class="product-cart-row">
                                <div>
                                    <label class="product-cart-label">Neue Sektion</label>
                                    <input type="text" id="ms-cart-section-name" class="product-cart-input" placeholder="z. B. Wechselrichter">
                                </div>
                                <div>
                                    <label class="product-cart-label">Farbe</label>
                                    <input type="color" id="ms-cart-section-color" class="product-cart-input" value="#93c21c" style="padding:.3rem;">
                                </div>
                            </div>

                            <div class="mt-1"></div>

                            <button type="button" class="product-cart-btn product-cart-btn-soft btn-block" id="ms-cart-add-section-btn">
                                Sektion hinzufügen
                            </button>
                        </div>

                        <div class="product-cart-group">
                            <label class="product-cart-label">Aktive Ziel-Sektion</label>
                            <select id="ms-cart-target-section" class="product-cart-select">
                                <option value="">Bitte Sektion wählen</option>
                            </select>
                            <div class="product-cart-helper" style="margin-top:.45rem;">
                                Produkte aus der Liste werden in diese Sektion als Hauptposition eingefügt.
                            </div>
                        </div>

                        <div class="product-cart-sections" id="ms-cart-sections">
                            <div class="product-cart-empty">Noch keine Cart-Daten geladen.</div>
                        </div>

                        <div class="product-cart-group">
                            <button type="button" class="product-cart-btn product-cart-btn-primary btn-block" id="ms-cart-convert-btn">
                                In Master Set umwandeln
                            </button>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="productListSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productListSelectModalLabel">Zu Liste hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @csrf
                <input type="hidden" id="list-modal-product-id">
                <input type="hidden" id="list-modal-type">

                <div class="form-group">
                    <label for="list-modal-select">Liste wählen</label>
                    <select id="list-modal-select" class="form-control select2-list-modal">
                        <option value="">Listen werden geladen...</option>
                    </select>
                </div>

                <small id="list-modal-footer-message" class="text-muted" style="font-size:.75rem;"></small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="button" id="list-modal-save-btn" class="btn btn-primary">
                    <i class="feather icon-save mr-25"></i> Hinzufügen
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
(function ($) {
    "use strict";

    const ROUTES = {
        list:   @json(route("products.list")),
        bulk:   @json(route("products.bulk")),
        dupBase:@json(url("products")),
        distributorsByBrand: @json(route("ajax.distributors.by-brand")),

        favoriteLists:   @json(route("ajax.products.favorite-lists")),
        stampLists:      @json(route("ajax.stamp.lists")),
        favoriteAttach:  @json(url("admin/ajax/products/favorite-lists")),
        favoriteDetach:  @json(url("admin/ajax/products/favorite-lists")),
        stampAttach:     @json(url("admin/ajax/stamp-articles/lists")),
        stampDetach:     @json(url("admin/ajax/stamp-articles/lists")),

        cartCreate: @json(route('admin.master-set-carts.store')),
        cartShowBase: @json(url('/admin/master-set-carts')),
        cartArticleGroupMasterSets: @json(route('admin.master-set-carts.article-group-master-sets')),
        cartSectionStoreBase: @json(url('/admin/master-set-carts')),
        cartItemStoreBase: @json(url('/admin/master-set-carts')),
        cartItemUpdateBase: @json(url('/admin/master-set-carts/items')),
        cartConvertBase: @json(url('/admin/master-set-carts'))
    };

    const CSRF = () => $('meta[name="csrf-token"]').attr("content") || "";

    let currentView = "card";
    let currentListAction = "add";
    let pendingHighlightId   = (new URLSearchParams(window.location.search).get("highlight") || "");
    let pendingHighlightType = (new URLSearchParams(window.location.search).get("hl") || "");
    let DIST_ALL_CACHE = null;

    const cartState = {
        cart: null,
        sections: [],
        items: []
    };

    const $el = {
        form:       () => $("#product-filter-form"),
        list:       () => $("#product-list"),
        pagination: () => $("#product-pagination"),
        loader:     () => $("#product-list-loading"),
        total:      () => $("#total-products-label"),

        bulkAction: () => $("#bulk-action"),
        bulkApply:  () => $("#bulk-apply-btn"),
        selectedCt: () => $("#selected-count-label"),

        modal:      () => $("#productListSelectModal"),
        modalTitle: () => $("#productListSelectModalLabel"),
        modalSelect:() => $("#list-modal-select"),
        modalPid:   () => $("#list-modal-product-id"),
        modalType:  () => $("#list-modal-type"),
        modalBtn:   () => $("#list-modal-save-btn"),
        modalMsg:   () => $("#list-modal-footer-message"),

        search:     () => $("#search"),
        brand:      () => $("#filter_brand"),
        group:      () => $("#filter_article_group"),
        dist:       () => $("#filter_distributor"),
        status:     () => $("#filter_status"),
        category:   () => $("#filter_category"),
        sort:       () => $("#filter_sort"),
        perPage:    () => $("#filter_per_page"),
        resetBtn:   () => $("#filter-reset-btn")
    };

    function escapeHtml(str) {
        return String(str ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function toastError(msg) {
        if (window.toastr) toastr.error(msg);
        else alert(msg);
    }

    function toastSuccess(msg) {
        if (window.toastr) toastr.success(msg);
        else alert(msg);
    }

    function toastInfo(msg) {
        if (window.toastr) toastr.info(msg);
        else alert(msg);
    }

    function moneyFormat(value) {
        const n = Number(value || 0);
        return n.toLocaleString('de-DE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' €';
    }

    function cacheAllDistributorsOnce() {
        if (DIST_ALL_CACHE) return;
        const $dist = $el.dist();

        DIST_ALL_CACHE = $dist.find("option").toArray().slice(1).map(opt => ({
            id: String(opt.value || ""),
            name: (opt.textContent || "").trim(),
        })).filter(x => x.id !== "");
    }

    function setDistributorOptions(items, selectedId) {
        const $dist = $el.dist();
        const keep = selectedId ? String(selectedId) : String($dist.val() || "");

        $dist.empty().append(new Option("Alle", "", false, false));

        (items || []).forEach(row => {
            $dist.append(new Option(row.name, String(row.id), false, false));
        });

        if (keep && $dist.find(`option[value="${CSS.escape(keep)}"]`).length) {
            $dist.val(keep);
        } else {
            $dist.val("");
        }

        $dist.trigger("change");
    }

    function reloadDistributorsForBrand(brandId, done) {
        const $dist = $el.dist();
        cacheAllDistributorsOnce();

        if (!brandId) {
            setDistributorOptions(DIST_ALL_CACHE, "");
            if (typeof done === "function") done();
            return;
        }

        $dist.prop("disabled", true);
        $dist.empty().append(new Option("Lade Lieferanten...", "", false, false)).trigger("change");

        $.ajax({
            url: ROUTES.distributorsByBrand,
            type: "GET",
            dataType: "json",
            data: { brand_id: brandId },
            success: function (res) {
                setDistributorOptions(res.items || [], "");
            },
            error: function (xhr) {
                setDistributorOptions(DIST_ALL_CACHE, "");
                toastError("Lieferanten konnten nicht geladen werden.");
                console.log("distributorsByBrand failed", xhr.status, xhr.responseText);
            },
            complete: function () {
                $dist.prop("disabled", false);
                if (typeof done === "function") done();
            }
        });
    }

    function highlightDuplicatedProduct(productId) {
        if (!productId) return;

        const $target = $('[data-product-id="' + productId + '"]').closest("tr, .product-card, .product-modern-item");
        if (!$target.length) return;

        if ($target[0] && $target[0].scrollIntoView) {
            $target[0].scrollIntoView({ behavior: "smooth", block: "center" });
        }

        $target.addClass("product-duplicated-flash");
        $target.find(".product-duplicated-badge").remove();

        const $slot = $target.find(".product-card-title, .product-list-name, .product-modern-title").first();
        if ($slot.length) {
            const $badge = $('<span class="product-duplicated-badge">Clone</span>');
            $slot.append($badge);
            setTimeout(() => $badge.fadeOut(200, function () { $(this).remove(); }), 2200);
        }

        setTimeout(() => $target.removeClass("product-duplicated-flash"), 2200);
    }

    function highlightUpdatedProduct(productId) {
        if (!productId) return;

        const $target = $('[data-product-id="' + productId + '"]').closest("tr, .product-card, .product-modern-item");
        if (!$target.length) return;

        if ($target[0] && $target[0].scrollIntoView) {
            $target[0].scrollIntoView({ behavior: "smooth", block: "center" });
        }

        $target.addClass("product-updated-flash");
        $target.find(".product-updated-badge").remove();

        const $slot = $target.find(".product-card-title, .product-list-name, .product-modern-title").first();
        if ($slot.length) {
            const $badge = $('<span class="product-updated-badge">Updated</span>');
            $slot.append($badge);
            setTimeout(() => $badge.fadeOut(200, function () { $(this).remove(); }), 2200);
        }

        setTimeout(() => $target.removeClass("product-updated-flash"), 2200);
    }

    function stripHighlightParamsOnce() {
        try {
            const url = new URL(window.location.href);
            url.searchParams.delete("highlight");
            url.searchParams.delete("hl");
            window.history.replaceState({}, "", url.toString());
        } catch (e) {}
    }

    function buildQueryData() {
        const sortVal = $el.sort().val() || "created_at|desc";
        const [sort_by = "created_at", sort_dir = "desc"] = String(sortVal).split("|");

        return {
            search:           $el.search().val() || "",
            brand_id:         $el.brand().val() || "",
            article_group_id: $el.group().val() || "",
            distributor_id:   $el.dist().val() || "",
            status:           $el.status().val() || "",
            category:         $el.category().val() || "",
            per_page:         $el.perPage().val() || 12,
            sort_by:          sort_by,
            sort_dir:         sort_dir,
            view_type:        currentView
        };
    }

    function resetSelection() {
        $el.selectedCt().text("0");
        $el.bulkAction().val("");
        $el.bulkApply().prop("disabled", true);
    }

    function loadProducts(pageUrl = null, highlightId = null) {
        const $list = $el.list();
        const $pagination = $el.pagination();
        const $loader = $el.loader();

        resetSelection();

        $.ajax({
            url: pageUrl || ROUTES.list,
            type: "GET",
            dataType: "json",
            data: pageUrl ? undefined : buildQueryData(),
            beforeSend: () => $loader.show(),
            complete:   () => $loader.hide(),
            success: (res) => {
                $list.html(res.html || "");
                $pagination.html(res.pagination || "");
                $el.total().text(res.total || 0);

                if (highlightId) {
                    setTimeout(() => highlightDuplicatedProduct(highlightId), 50);
                }

                if (pendingHighlightId) {
                    const id = pendingHighlightId;
                    const type = pendingHighlightType;
                    pendingHighlightId = "";

                    setTimeout(() => {
                        if (type === "updated") highlightUpdatedProduct(id);
                        else highlightDuplicatedProduct(id);
                        stripHighlightParamsOnce();
                    }, 80);
                }
            },
            error: () => toastError("Fehler beim Laden der Produkte.")
        });
    }

    function getSelectedIds() {
        const ids = [];
        $(".product-select:checked").each(function () { ids.push($(this).val()); });
        return ids;
    }

    function updateBulkState() {
        const ids = getSelectedIds();
        const count = ids.length;

        $el.selectedCt().text(count);

        const hasAction = ($el.bulkAction().val() || "") !== "";
        $el.bulkApply().prop("disabled", !(count > 0 && hasAction));

        const $selectAll = $("#select-all-page");
        if ($selectAll.length) {
            const $cbs = $(".product-select");
            const total = $cbs.length;
            const checked = $cbs.filter(":checked").length;
            $selectAll.prop("checked", total > 0 && total === checked);
        }
    }

    function applyBulkAction() {
        const action = $el.bulkAction().val();
        const ids = getSelectedIds();

        if (!action) return toastInfo("Bitte zuerst eine Aktion wählen.");
        if (!ids.length) return toastInfo("Keine Produkte ausgewählt.");

        const map = {
            publish:   { title: "Produkte veröffentlichen?", text: "Ausgewählte Produkte werden veröffentlicht.", icon: "question" },
            unpublish: { title: "Produkte deaktivieren?", text: "Ausgewählte Produkte werden deaktiviert.", icon: "question" },
            delete:    { title: "Produkte löschen?", text: "Ausgewählte Produkte werden dauerhaft gelöscht.", icon: "warning" }
        };

        const cfg = map[action];
        if (!cfg) return toastError("Ungültige Aktion.");

        Swal.fire({
            title: cfg.title,
            text: cfg.text,
            icon: cfg.icon,
            showCancelButton: true,
            confirmButtonText: "Ja, ausführen",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: ROUTES.bulk,
                type: "POST",
                dataType: "json",
                data: { _token: CSRF(), action, ids },
                success: (res) => {
                    if (res.status === "success") {
                        toastSuccess(res.message || "Aktion ausgeführt.");
                        loadProducts();
                    } else {
                        toastError(res.message || "Aktion fehlgeschlagen.");
                    }
                },
                error: (xhr) => {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Aktion fehlgeschlagen.";
                    toastError(msg);
                }
            });
        });
    }

    function configureModalHeader(listType, mode) {
        const isFav = listType === "favorite";

        const cfg = (mode === "remove")
            ? {
                title: isFav ? "Aus Favoriten-Liste entfernen" : "Aus Stempel-Liste entfernen",
                btnHtml: '<i class="feather icon-trash-2 mr-25"></i> Entfernen',
                btnClass: "btn btn-danger"
            }
            : {
                title: isFav ? "Zu Favoriten-Liste hinzufügen" : "Zu Stempel-Liste hinzufügen",
                btnHtml: '<i class="feather icon-save mr-25"></i> Hinzufügen',
                btnClass: "btn btn-primary"
            };

        $el.modalTitle().text(cfg.title);
        $el.modalBtn().html(cfg.btnHtml).attr("class", cfg.btnClass);
    }

    function loadListsForType(listType, cb) {
        const url = (listType === "favorite") ? ROUTES.favoriteLists : ROUTES.stampLists;
        const $select = $el.modalSelect();

        $select.empty().append('<option value="">Listen werden geladen...</option>');
        $el.modalMsg().text("");

        const productId = $el.modalPid().val();

        $.ajax({
            url,
            type: "GET",
            dataType: "json",
            data: { as: "select", product_id: productId },
            success: (res) => {
                const lists = res.lists || [];
                $select.empty();

                if (!lists.length) {
                    $select.append('<option value="">Keine Listen vorhanden</option>');
                    $el.modalMsg().text("Es sind noch keine Listen vorhanden. Bitte legen Sie zuerst eine Liste im entsprechenden Modul an.");
                } else {
                    $select.append('<option value="">Bitte Liste wählen...</option>');
                    lists.forEach((row) => {
                        const attached = row.is_attached ? 1 : 0;
                        const label = row.is_attached ? (row.name + " (enthält Produkt)") : row.name;

                        $select.append(
                            $("<option>", { value: row.id, text: label }).attr("data-attached", attached)
                        );
                    });
                }

                $select.trigger("change");
                if (typeof cb === "function") cb();
            },
            error: () => {
                toastError("Listen konnten nicht geladen werden.");
                $select.empty().append('<option value="">Fehler beim Laden</option>').trigger("change");
            }
        });
    }

    function openListModal(productId, listType, mode) {
        currentListAction = (mode === "remove") ? "remove" : "add";

        $el.modalPid().val(productId);
        $el.modalType().val(listType);

        configureModalHeader(listType, currentListAction);

        loadListsForType(listType, function () {
            $el.modal().modal("show");
        });
    }

    function addProductToList() {
        const productId = $el.modalPid().val();
        const listType  = $el.modalType().val();
        const listId    = $el.modalSelect().val();

        if (!listId) return toastInfo("Bitte zuerst eine Liste wählen.");

        let url, payload;

        if (listType === "favorite") {
            url = ROUTES.favoriteAttach + "/" + listId + "/products";
            payload = { _token: CSRF(), product_id: productId };
        } else {
            url = ROUTES.stampAttach + "/" + listId + "/attach";
            payload = { _token: CSRF(), stamp_article_id: productId };
        }

        $.ajax({
            url,
            type: "POST",
            dataType: "json",
            data: payload,
            success: (res) => {
                $el.modal().modal("hide");
                toastSuccess(res.message || "Produkt zur Liste hinzugefügt.");
                loadProducts();
            },
            error: (xhr) => {
                if (xhr.status === 409) toastInfo("Dieses Produkt ist bereits in dieser Liste.");
                else toastError("Produkt konnte nicht hinzugefügt werden.");
            }
        });
    }

    function removeProductFromList() {
        const productId = $el.modalPid().val();
        const listType  = $el.modalType().val();
        const listId    = $el.modalSelect().val();

        if (!listId) return toastInfo("Bitte zuerst eine Liste wählen.");

        let url;

        if (listType === "favorite") {
            url = ROUTES.favoriteDetach + "/" + listId + "/products/" + productId;
        } else {
            url = ROUTES.stampDetach + "/" + listId + "/detach-by-product/" + productId;
        }

        $.ajax({
            url,
            type: "DELETE",
            dataType: "json",
            data: { _token: CSRF() },
            success: (res) => {
                $el.modal().modal("hide");
                toastSuccess(res.message || "Produkt aus der Liste entfernt.");
                loadProducts();
            },
            error: (xhr) => {
                if (xhr.status === 404) toastInfo("Dieses Produkt ist nicht in dieser Liste.");
                else toastError("Produkt konnte nicht entfernt werden.");
            }
        });
    }

    function duplicateProduct(productId) {
        if (!productId) return;

        Swal.fire({
            title: "Produkt duplizieren?",
            text: "Es wird ein neues Produkt mit denselben Daten angelegt.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ja, duplizieren",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: ROUTES.dupBase + "/" + productId + "/duplicate",
                type: "POST",
                dataType: "json",
                data: { _token: CSRF() },
                success: (res) => {
                    if (res && res.success) {
                        toastSuccess(res.message || "Produkt dupliziert.");

                        const newId =
                            res.new_id ||
                            res.product_id ||
                            (res.product && res.product.id) ||
                            null;

                        loadProducts(null, newId || productId);
                    } else {
                        toastError((res && res.message) ? res.message : "Duplizieren fehlgeschlagen.");
                    }
                },
                error: (xhr) => {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Duplizieren fehlgeschlagen.";
                    toastError(msg);
                }
            });
        });
    }

    /* =========================================================
       CART
    ========================================================= */
    function cartModeToggle() {
        const mode = $("#ms-cart-mode").val() || "new";
        $("#ms-cart-new-wrap").toggle(mode === "new");
        $("#ms-cart-existing-wrap").toggle(mode === "existing");
    }

    function cartChildrenOf(parentId) {
        return (cartState.items || [])
            .filter(x => Number(x.parent_id || 0) === Number(parentId))
            .sort((a, b) => (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id)));
    }

    function cartRootsOf(sectionId) {
        return (cartState.items || [])
            .filter(x => Number(x.section_id || 0) === Number(sectionId) && !x.parent_id)
            .sort((a, b) => (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id)));
    }

    function renderCartSummary() {
        $("#ms-cart-main-total").text(moneyFormat(cartState.cart?.main_total || 0));
        $("#ms-cart-sub-total").text(moneyFormat(cartState.cart?.sub_total || 0));
        $("#ms-cart-total").text(moneyFormat(cartState.cart?.total || 0));
    }

    function renderCartTargetSections() {
        const $target = $("#ms-cart-target-section");
        $target.empty().append('<option value="">Bitte Sektion wählen</option>');

        (cartState.sections || []).forEach(section => {
            $target.append(new Option(section.name, section.id, false, false));
        });
    }

    function renderCartItemNode(item, isSub = false) {
        const lineTotal = Number(item.qty || 0) * Number(item.unit_price || 0);
        const children = cartChildrenOf(item.id);

        return `
            <div class="product-cart-item ${isSub ? 'sub' : ''}">
                <div class="product-cart-item-row">
                    <div class="product-cart-item-left">
                        <div class="product-cart-item-title">
                            ${escapeHtml(item.title || 'Ohne Titel')}
                            <span class="product-cart-pill">${item.parent_id ? 'Sub' : 'Main'}</span>
                        </div>

                        <div class="product-cart-item-meta">
                            Art.-Nr.: ${escapeHtml(item.article_no || '–')}<br>
                            Menge: ${Number(item.qty || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ·
                            Einzelpreis: ${moneyFormat(item.unit_price || 0)}<br>
                            Zeile: <strong>${moneyFormat(lineTotal)}</strong>
                        </div>

                        <div class="product-cart-item-controls">
                            <input type="number"
                                   min="0"
                                   step="0.01"
                                   value="${Number(item.qty || 0)}"
                                   class="product-cart-mini-input js-cart-item-qty"
                                   data-item-id="${item.id}">

                            <input type="number"
                                   min="0"
                                   step="0.01"
                                   value="${Number(item.unit_price || 0)}"
                                   class="product-cart-mini-input js-cart-item-price"
                                   data-item-id="${item.id}">

                            <button type="button"
                                    class="product-cart-icon-btn js-cart-add-sub"
                                    data-item-id="${item.id}"
                                    title="Unterposition hinzufügen">
                                <i class="feather icon-plus"></i>
                            </button>

                            <button type="button"
                                    class="product-cart-icon-btn js-cart-remove-item"
                                    data-item-id="${item.id}"
                                    title="Entfernen">
                                <i class="feather icon-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                ${children.length ? `
                    <div class="product-cart-items" style="padding-top:0;">
                        ${children.map(child => renderCartItemNode(child, true)).join('')}
                    </div>
                ` : ``}
            </div>
        `;
    }

    function renderCartSections() {
        const $wrap = $("#ms-cart-sections");

        if (!cartState.sections.length) {
            $wrap.html('<div class="product-cart-empty">Noch keine Sektionen vorhanden.</div>');
            renderCartSummary();
            renderCartTargetSections();
            return;
        }

        const html = cartState.sections
            .sort((a, b) => (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id)))
            .map(section => {
                const roots = cartRootsOf(section.id);

                return `
                    <div class="product-cart-section">
                        <div class="product-cart-section-head">
                            <div class="product-cart-section-title">
                                <span class="product-cart-section-dot" style="background:${escapeHtml(section.color || '#93c21c')}"></span>
                                <span class="product-cart-section-name">${escapeHtml(section.name || 'Sektion')}</span>
                            </div>
                        </div>

                        <div class="product-cart-items">
                            ${roots.length
                                ? roots.map(item => renderCartItemNode(item)).join('')
                                : '<div class="product-cart-empty">Noch keine Produkte in dieser Sektion.</div>'
                            }
                        </div>
                    </div>
                `;
            }).join('');

        $wrap.html(html);
        renderCartSummary();
        renderCartTargetSections();

        if (window.feather) feather.replace();
    }

    function fillCartConfigForm() {
        if (!cartState.cart) return;

        $("#ms-cart-id").val(cartState.cart.id || "");
        $("#ms-cart-article-group").val(cartState.cart.article_group_id || "");
        $("#ms-cart-mode").val(cartState.cart.mode || "new");
        $("#ms-cart-name").val(cartState.cart.name || "");
        $("#ms-cart-description").val(cartState.cart.description || "");
        cartModeToggle();
    }

    function loadCartMasterSets(articleGroupId, selectedId = "") {
        const $select = $("#ms-cart-master-set");
        $select.empty().append('<option value="">Lade Master Sets ...</option>');

        if (!articleGroupId) {
            $select.empty().append('<option value="">Bitte zuerst Artikelgruppe wählen</option>');
            return;
        }

        $.ajax({
            url: ROUTES.cartArticleGroupMasterSets,
            type: "GET",
            dataType: "json",
            data: { article_group_id: articleGroupId },
            success: function (res) {
                const items = res.items || [];
                $select.empty().append('<option value="">Master Set wählen</option>');

                items.forEach(row => {
                    const opt = new Option(row.text || row.name || ("Set #" + row.id), row.id, false, String(selectedId) === String(row.id));
                    $select.append(opt);
                });
            },
            error: function () {
                $select.empty().append('<option value="">Fehler beim Laden</option>');
                toastError("Master Sets konnten nicht geladen werden.");
            }
        });
    }

    function loadCart(cartId, cb) {
        if (!cartId) {
            cartState.cart = null;
            cartState.sections = [];
            cartState.items = [];
            $("#ms-cart-id").val("");
            renderCartSections();
            if (typeof cb === "function") cb();
            return;
        }

        $.ajax({
            url: ROUTES.cartShowBase + "/" + cartId,
            type: "GET",
            dataType: "json",
            success: function (res) {
                cartState.cart = res.cart || null;
                cartState.sections = res.sections || [];
                cartState.items = res.items || [];

                fillCartConfigForm();

                if (cartState.cart?.article_group_id && ($("#ms-cart-mode").val() === "existing")) {
                    loadCartMasterSets(cartState.cart.article_group_id, cartState.cart.target_master_set_id || "");
                }

                renderCartSections();

                if (typeof cb === "function") cb();
            },
            error: function () {
                toastError("Cart konnte nicht geladen werden.");
            }
        });
    }

    function saveCartConfig() {
        const cartId = $("#ms-cart-id").val() || "";
        const articleGroupId = $("#ms-cart-article-group").val() || "";
        const mode = $("#ms-cart-mode").val() || "new";
        const name = ($("#ms-cart-name").val() || "").trim();
        const description = ($("#ms-cart-description").val() || "").trim();
        const targetMasterSetId = $("#ms-cart-master-set").val() || "";

        if (!articleGroupId) {
            return toastInfo("Bitte zuerst eine Artikelgruppe wählen.");
        }

        if (mode === "new" && !name) {
            return toastInfo("Bitte einen Namen für den neuen Master Set eingeben.");
        }

        if (mode === "existing" && !targetMasterSetId) {
            return toastInfo("Bitte einen bestehenden Master Set wählen.");
        }

        const payload = {
            article_group_id: articleGroupId,
            mode: mode,
            name: mode === "new" ? name : null,
            description: description,
            target_master_set_id: mode === "existing" ? targetMasterSetId : null
        };

        $.ajax({
            url: cartId ? (ROUTES.cartShowBase + "/" + cartId) : ROUTES.cartCreate,
            type: cartId ? "PUT" : "POST",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify(payload),
            headers: {
                "X-CSRF-TOKEN": CSRF(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            success: function (res) {
                if (res.success === false) {
                    return toastError(res.message || "Cart konnte nicht gespeichert werden.");
                }

                if (!cartId && res.cart_id) {
                    $("#ms-cart-id").val(res.cart_id);
                    loadCart(res.cart_id, function () {
                        toastSuccess(res.message || "Cart erstellt.");
                    });
                } else {
                    loadCart(cartId, function () {
                        toastSuccess(res.message || "Cart gespeichert.");
                    });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Cart konnte nicht gespeichert werden.";
                toastError(msg);
            }
        });
    }

    function addCartSection() {
        const cartId = $("#ms-cart-id").val() || "";
        const name = ($("#ms-cart-section-name").val() || "").trim();
        const color = $("#ms-cart-section-color").val() || "#93c21c";

        if (!cartId) return toastInfo("Bitte zuerst den Cart speichern / starten.");
        if (!name) return toastInfo("Bitte einen Sektionsnamen eingeben.");

        $.ajax({
            url: ROUTES.cartSectionStoreBase + "/" + cartId + "/sections",
            type: "POST",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify({ name, color }),
            headers: {
                "X-CSRF-TOKEN": CSRF(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            success: function (res) {
                if (res.success === false) {
                    return toastError(res.message || "Sektion konnte nicht erstellt werden.");
                }

                $("#ms-cart-section-name").val("");
                loadCart(cartId, function () {
                    toastSuccess(res.message || "Sektion hinzugefügt.");
                });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Sektion konnte nicht erstellt werden.";
                toastError(msg);
            }
        });
    }

    function addProductToCart(productId, parentId = null) {
        const cartId = $("#ms-cart-id").val() || "";
        const sectionId = $("#ms-cart-target-section").val() || "";

        if (!cartId) return toastInfo("Bitte zuerst den Cart speichern / starten.");
        if (!parentId && !sectionId) return toastInfo("Bitte zuerst eine Ziel-Sektion wählen.");

        $.ajax({
            url: ROUTES.cartItemStoreBase + "/" + cartId + "/items",
            type: "POST",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify({
                product_id: productId,
                section_id: parentId ? null : sectionId,
                parent_id: parentId || null,
                source_type: "product",
                node_type: parentId ? "sub" : "main",
                qty: 1
            }),
            headers: {
                "X-CSRF-TOKEN": CSRF(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            success: function (res) {
                if (res.success === false) {
                    return toastError(res.message || "Produkt konnte nicht in den Cart eingefügt werden.");
                }

                loadCart(cartId, function () {
                    toastSuccess(res.message || "Produkt zum Cart hinzugefügt.");
                });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Produkt konnte nicht in den Cart eingefügt werden.";
                toastError(msg);
            }
        });
    }

    function updateCartItem(itemId, payload) {
        $.ajax({
            url: ROUTES.cartItemUpdateBase + "/" + itemId,
            type: "PUT",
            dataType: "json",
            contentType: "application/json",
            data: JSON.stringify(payload),
            headers: {
                "X-CSRF-TOKEN": CSRF(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            success: function (res) {
                if (res.success === false) {
                    return toastError(res.message || "Cart-Item konnte nicht aktualisiert werden.");
                }

                const cartId = $("#ms-cart-id").val() || "";
                loadCart(cartId);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Cart-Item konnte nicht aktualisiert werden.";
                toastError(msg);
            }
        });
    }

    function removeCartItem(itemId) {
        $.ajax({
            url: ROUTES.cartItemUpdateBase + "/" + itemId,
            type: "DELETE",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": CSRF(),
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            success: function (res) {
                if (res.success === false) {
                    return toastError(res.message || "Cart-Item konnte nicht gelöscht werden.");
                }

                const cartId = $("#ms-cart-id").val() || "";
                loadCart(cartId, function () {
                    toastSuccess(res.message || "Item entfernt.");
                });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Cart-Item konnte nicht gelöscht werden.";
                toastError(msg);
            }
        });
    }

    function promptAddSubItem(parentId) {
        const productId = window.prompt("Produkt-ID für Unterposition eingeben:");
        if (!productId) return;
        addProductToCart(productId, parentId);
    }

    function convertCartToMasterSet() {
        const cartId = $("#ms-cart-id").val() || "";
        if (!cartId) return toastInfo("Bitte zuerst einen Cart erstellen.");

        Swal.fire({
            title: "Cart umwandeln?",
            text: "Der aktuelle Cart wird in einen Master Set umgewandelt.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ja, umwandeln",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: ROUTES.cartConvertBase + "/" + cartId + "/convert",
                type: "POST",
                dataType: "json",
                headers: {
                    "X-CSRF-TOKEN": CSRF(),
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                success: function (res) {
                    if (res.success === false) {
                        return toastError(res.message || "Cart konnte nicht umgewandelt werden.");
                    }

                    toastSuccess(res.message || "Cart erfolgreich umgewandelt.");
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || "Cart konnte nicht umgewandelt werden.";
                    toastError(msg);
                }
            });
        });
    }

    $(function () {
        $(".select2").select2({ width: "100%" });

        $el.modalSelect().select2({
            width: "100%",
            dropdownParent: $el.modal()
        });

        loadProducts();

        $el.form().on("submit", function (e) {
            e.preventDefault();
            loadProducts();
        });

        $("#filter_article_group, #filter_distributor, #filter_status, #filter_category, #filter_sort, #filter_per_page").on("change", function () {
            loadProducts();
        });

        $el.brand().on("change", function () {
            const brandId = $(this).val() || "";
            reloadDistributorsForBrand(brandId, function () {
                loadProducts();
            });
        });

        $el.resetBtn().on("click", function () {
            $el.search().val("");
            $el.brand().val("").trigger("change");
            $el.group().val("").trigger("change");
            $el.status().val("");
            $el.category().val("");
            $el.sort().val("created_at|desc");
            $el.perPage().val("12");
        });

        $(document).on("click", ".view-toggle-btn", function () {
            const view = $(this).data("view") || "card";
            if (view === currentView) return;

            currentView = view;
            $(".view-toggle-btn").removeClass("active");
            $(this).addClass("active");
            loadProducts();
        });

        $(document).on("click", "#product-pagination a", function (e) {
            e.preventDefault();
            const url = $(this).attr("href");
            if (!url || url === "#") return;
            loadProducts(url);
        });

        $(document).on("click", ".product-card-main", function () {
            const url = $(this).data("details-url");
            if (url) window.location.href = url;
        });

        $(document).on("click", ".js-duplicate-product", function (e) {
            e.preventDefault();
            e.stopPropagation();
            duplicateProduct($(this).data("product-id"));
        });

        $(document).on("change", ".product-select", updateBulkState);

        $(document).on("change", "#select-all-page", function () {
            const checked = $(this).is(":checked");
            $(".product-select").prop("checked", checked);
            updateBulkState();
        });

        $el.bulkAction().on("change", updateBulkState);
        $el.bulkApply().on("click", applyBulkAction);

        $(document).on("click", ".js-add-to-list", function (e) {
            e.preventDefault();
            e.stopPropagation();
            const pid = $(this).data("product-id");
            const type = $(this).data("list-type");
            if (pid && type) openListModal(pid, type, "add");
        });

        $(document).on("click", ".js-remove-from-list", function (e) {
            e.preventDefault();
            e.stopPropagation();
            const pid = $(this).data("product-id");
            const type = $(this).data("list-type");
            if (pid && type) openListModal(pid, type, "remove");
        });

        $el.modalBtn().on("click", function () {
            if (currentListAction === "remove") removeProductFromList();
            else addProductToList();
        });

        $("#ms-cart-mode").on("change", function () {
            cartModeToggle();
            if ($(this).val() === "existing") {
                loadCartMasterSets($("#ms-cart-article-group").val() || "");
            }
        });

        $("#ms-cart-article-group").on("change", function () {
            if ($("#ms-cart-mode").val() === "existing") {
                loadCartMasterSets($(this).val() || "");
            }
        });

        $("#ms-cart-save-btn").on("click", saveCartConfig);
        $("#ms-cart-add-section-btn").on("click", addCartSection);
        $("#ms-cart-convert-btn").on("click", convertCartToMasterSet);

        $(document).on("click", ".js-add-product-to-cart", function (e) {
            e.preventDefault();
            e.stopPropagation();
            addProductToCart($(this).data("product-id"));
        });

        $(document).on("change", ".js-cart-item-qty", function () {
            updateCartItem($(this).data("item-id"), { qty: $(this).val() });
        });

        $(document).on("change", ".js-cart-item-price", function () {
            updateCartItem($(this).data("item-id"), { unit_price: $(this).val() });
        });

        $(document).on("click", ".js-cart-remove-item", function () {
            removeCartItem($(this).data("item-id"));
        });

        $(document).on("click", ".js-cart-add-sub", function () {
            promptAddSubItem($(this).data("item-id"));
        });

        cartModeToggle();
        renderCartSections();
    });

})(jQuery);
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    "use strict";

    var activeMenu = null;

    function getOrCreateFloatMenu() {
        var el = document.getElementById("product-menu-float");
        if (!el) {
            el = document.createElement("div");
            el.id = "product-menu-float";
            el.className = "product-menu-float";
            document.body.appendChild(el);
        }
        return el;
    }

    function closeMenu() {
        var el = document.getElementById("product-menu-float");
        if (el) {
            el.classList.remove("show");
            el.classList.remove("drop-up");
        }
        activeMenu = null;
    }

    function openMenu(toggle) {
        var container = toggle.closest(".list-menu-container");
        if (!container) return;

        var template = container.querySelector(".custom-menu");
        if (!template) return;

        var floatMenu = getOrCreateFloatMenu();
        floatMenu.innerHTML = template.innerHTML;

        var rect = toggle.getBoundingClientRect();
        var vw = window.innerWidth;
        var vh = window.innerHeight;

        floatMenu.style.visibility = "hidden";
        floatMenu.style.display = "block";
        floatMenu.classList.add("show");
        var menuWidth  = floatMenu.offsetWidth  || 240;
        var menuHeight = floatMenu.offsetHeight || 180;
        floatMenu.classList.remove("show");
        floatMenu.style.display = "";
        floatMenu.style.visibility = "";

        var top  = rect.bottom + 8;
        var left = rect.right - menuWidth;

        if (left < 8) left = 8;
        if (left + menuWidth > vw - 8) left = vw - menuWidth - 8;

        var dropUp = false;
        if (top + menuHeight > vh - 8) {
            top = rect.top - menuHeight - 8;
            dropUp = true;
        }

        floatMenu.style.top = top + "px";
        floatMenu.style.left = left + "px";

        if (dropUp) floatMenu.classList.add("drop-up");
        else floatMenu.classList.remove("drop-up");

        floatMenu.classList.add("show");
        activeMenu = floatMenu;
    }

    document.addEventListener("click", function (e) {
        var toggle = e.target.closest(".js-menu-toggle");
        if (toggle) {
            e.preventDefault();
            e.stopPropagation();

            if (activeMenu && activeMenu.classList.contains("show")) {
                closeMenu();
                return;
            }

            closeMenu();
            openMenu(toggle);
            return;
        }

        if (e.target.closest("#product-menu-float")) return;
        closeMenu();
    });

    window.addEventListener("resize", closeMenu);
    window.addEventListener("scroll", closeMenu, true);
});
</script>
@endsection