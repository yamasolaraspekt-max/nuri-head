@extends('admin.layouts.app')

@section('title') Arbeitsschritte – Produkte & Leistungen @stop

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --pm-bg: #f3f4f6;
            --pm-card: #ffffff;
            --pm-text: #111827;
            --pm-muted: #6b7280;
            --pm-border: #e5e7eb;
            --pm-green: #93c21c;
            --pm-green-soft: #cfe09b;
            --pm-blue: #74b2d4;
            --pm-blue-soft: #eff6ff;
            --pm-orange: #f8ac00;
            --pm-orange-soft: #fffbeb;
            --pm-red: #ef4444;
            --pm-red-soft: #fef2f2;
            --pm-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --pm-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            --pm-radius: 20px;
            --pm-transition: all .18s ease;
        }

        .pm-page {
            min-height: calc(100vh - 120px);
            padding: 10px 0 26px;
        }

        .pm-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding: 16px 18px;
            border: 1px solid rgba(116, 178, 212, .28);
            border-radius: 20px;
            background: #fff;
            box-shadow: var(--pm-shadow-sm);
        }

        .pm-title {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            color: var(--pm-text);
            letter-spacing: -.025em;
        }

        .pm-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: var(--pm-muted);
            font-weight: 700;
        }

        .pm-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 9px;
            font-size: 13px;
            color: var(--pm-muted);
        }

        .pm-breadcrumb a {
            color: var(--pm-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .pm-breadcrumb a:hover {
            color: var(--pm-text);
        }

        .pm-breadcrumb span.current {
            color: var(--pm-text);
            font-weight: 900;
        }

        .pm-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pm-btn,
        .pm-btn-soft,
        .pm-btn-blue,
        .pm-btn-danger,
        .pm-btn-warning {
            border: 0;
            border-radius: 12px;
            padding: 10px 15px;
            font-weight: 900;
            font-size: 13px;
            line-height: 1.2;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: var(--pm-transition);
            white-space: nowrap;
        }

        .pm-btn {
            background: var(--pm-green);
            color: #fff;
            box-shadow: 0 12px 24px rgba(147, 194, 28, .22);
        }

        .pm-btn:hover {
            background: #7baa18;
            color: #fff;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .pm-btn-blue {
            background: var(--pm-blue);
            color: #fff;
        }

        .pm-btn-blue:hover {
            background: #559fc7;
            color: #fff;
            text-decoration: none;
        }

        .pm-btn-soft {
            background: #fff;
            color: #374151;
            border: 1px solid var(--pm-border);
        }

        .pm-btn-soft:hover {
            background: #f9fafb;
            color: var(--pm-text);
            text-decoration: none;
        }

        .pm-btn-danger {
            background: var(--pm-red);
            color: #fff;
        }

        .pm-btn-danger:hover {
            background: #dc2626;
            color: #fff;
        }

        .pm-btn-warning {
            background: var(--pm-orange);
            color: #111827;
        }

        .pm-btn-warning:hover {
            background: #e89b00;
            color: #111827;
        }

        .pm-layout {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .pm-panel {
            background: var(--pm-card);
            border: 1px solid var(--pm-border);
            border-radius: var(--pm-radius);
            box-shadow: var(--pm-shadow-sm);
            overflow: hidden;
        }

        .pm-panel-left {
            min-height: 620px;
            display: flex;
            flex-direction: column;
        }

        .pm-panel-head {
            padding: 16px 18px;
            border-bottom: 1px solid var(--pm-border);
            background: #fafafa;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .pm-panel-title {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            color: var(--pm-text);
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pm-panel-subtitle {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 700;
            color: var(--pm-muted);
        }

        .pm-count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--pm-blue-soft);
            color: #075985;
            border: 1px solid #c0d8ea;
            font-size: 11px;
            font-weight: 900;
        }

        .pm-search {
            padding: 14px 18px 10px;
        }

        .pm-search-row {
            display: flex;
            gap: 8px;
        }

        .pm-input,
        .pm-select {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--pm-border);
            border-radius: 12px;
            background: #fff;
            color: var(--pm-text);
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: var(--pm-transition);
        }

        .pm-input:focus,
        .pm-select:focus {
            border-color: var(--pm-green);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .pm-product-list {
            padding: 8px 12px 14px;
            overflow-y: auto;
            max-height: 650px;
            flex: 1;
        }

        .pm-product-item {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 16px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 11px;
            text-align: left;
            cursor: pointer;
            transition: var(--pm-transition);
            margin-bottom: 8px;
        }

        .pm-product-item:hover {
            background: #f8fafc;
            border-color: #dbeafe;
        }

        .pm-product-item.is-active {
            background: linear-gradient(135deg, var(--pm-green), #7baa18);
            border-color: transparent;
            box-shadow: 0 12px 26px rgba(147, 194, 28, .25);
        }

        .pm-product-avatar,
        .pm-service-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: var(--pm-blue-soft);
            color: #075985;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex: 0 0 auto;
            border: 1px solid rgba(116, 178, 212, .28);
        }

        .pm-product-avatar img,
        .pm-service-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pm-product-name {
            font-size: 14px;
            font-weight: 900;
            color: var(--pm-text);
            margin-bottom: 2px;
        }

        .pm-product-meta {
            font-size: 12px;
            color: var(--pm-muted);
            font-weight: 700;
        }

        .pm-product-item.is-active .pm-product-name,
        .pm-product-item.is-active .pm-product-meta {
            color: #fff;
        }

        .pm-empty {
            border: 1px dashed var(--pm-border);
            border-radius: 18px;
            background: #fafafa;
            padding: 28px;
            text-align: center;
            font-size: 13px;
            color: var(--pm-muted);
            font-weight: 800;
        }

        .pm-services-head {
            padding: 16px 18px;
            border-bottom: 1px solid var(--pm-border);
            background: #fafafa;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .pm-services-head.is-visible {
            display: flex;
        }

        .pm-selected-product {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .pm-selected-name {
            font-size: 16px;
            font-weight: 900;
            color: var(--pm-text);
            line-height: 1.2;
        }

        .pm-selected-meta {
            font-size: 12px;
            color: var(--pm-muted);
            font-weight: 700;
            margin-top: 3px;
        }

        .pm-services-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pm-services-body {
            padding: 18px;
        }

        .pm-services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 14px;
        }

        .pm-service-card {
            position: relative;
            background: #fff;
            border: 1px solid var(--pm-border);
            border-radius: 18px;
            box-shadow: var(--pm-shadow-sm);
            overflow: hidden;
            transition: var(--pm-transition);
        }

        .pm-service-card:hover {
            transform: translateY(-2px);
            border-color: rgba(147, 194, 28, .55);
            box-shadow: var(--pm-shadow);
        }

        .pm-service-link {
            display: block;
            padding: 16px 14px 12px;
            text-decoration: none !important;
            color: inherit;
            text-align: center;
        }

        .pm-service-number {
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pm-green-soft);
            color: #365314;
            font-weight: 900;
            border: 1px solid rgba(147, 194, 28, .35);
        }

        .pm-service-label {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-size: 13px;
            line-height: 1.35;
            font-weight: 900;
            color: var(--pm-text);
            word-break: break-word;
        }

        .pm-service-date {
            margin-top: 8px;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
        }

        .pm-service-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: 900;
            background: var(--pm-blue-soft);
            color: #075985;
            border: 1px solid #c0d8ea;
        }

        .pm-service-actions {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 10px 12px 14px;
            border-top: 1px solid #f3f4f6;
        }

        .pm-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            border: 1px solid var(--pm-border);
            background: #fff;
            color: var(--pm-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--pm-transition);
        }

        .pm-icon-btn:hover {
            background: #f9fafb;
        }

        .pm-icon-btn.edit {
            background: var(--pm-blue-soft);
            color: #075985;
            border-color: #c0d8ea;
        }

        .pm-icon-btn.edit:hover {
            background: #dff0fb;
        }

        .pm-icon-btn.delete {
            background: var(--pm-red-soft);
            color: #b91c1c;
            border-color: #fecaca;
        }

        .pm-icon-btn.delete:hover {
            background: #fee2e2;
        }

        .pm-loading {
            border: 1px dashed var(--pm-border);
            border-radius: 18px;
            background: #fafafa;
            padding: 30px;
            text-align: center;
            color: var(--pm-muted);
            font-size: 13px;
            font-weight: 800;
        }

        .pm-duplicate-banner {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px;
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid #fde68a;
            background: var(--pm-orange-soft);
            color: #92400e;
            font-size: 13px;
            font-weight: 850;
        }

        .pm-duplicate-banner.is-visible {
            display: flex;
        }

        .pm-duplicate-banner strong {
            color: #78350f;
        }

        .pm-toast-wrap {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 100050;
            width: min(380px, calc(100vw - 40px));
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pm-toast {
            background: #fff;
            border: 1px solid var(--pm-border);
            border-left: 5px solid var(--pm-green);
            border-radius: 14px;
            box-shadow: var(--pm-shadow);
            padding: 13px 14px;
            font-size: 13px;
            color: var(--pm-text);
            font-weight: 800;
        }

        .pm-toast.error {
            border-left-color: var(--pm-red);
        }

        .pm-toast.warning {
            border-left-color: var(--pm-orange);
        }

        .pm-toast.info {
            border-left-color: var(--pm-blue);
        }

        /* ===============================
               CUSTOM MODALS
            =============================== */
        .pm-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, .58);
        }

        .pm-modal-backdrop.is-open {
            display: flex;
        }

        .pm-modal {
            width: 100%;
            max-width: 620px;
            max-height: calc(100vh - 36px);
            background: #fff;
            border-radius: 22px;
            border: 1px solid var(--pm-border);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            transform: translateY(12px) scale(.98);
            transition: var(--pm-transition);
        }

        .pm-modal.sm {
            max-width: 520px;
        }

        .pm-modal-backdrop.is-open .pm-modal {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .pm-modal-head {
            padding: 17px 20px;
            border-bottom: 1px solid var(--pm-border);
            background: #fafafa;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .pm-modal-title {
            margin: 0;
            color: var(--pm-text);
            font-size: 17px;
            font-weight: 900;
        }

        .pm-modal-sub {
            margin-top: 4px;
            font-size: 12px;
            font-weight: 700;
            color: var(--pm-muted);
        }

        .pm-modal-close {
            width: 40px;
            height: 40px;
            border: 1px solid var(--pm-border);
            border-radius: 12px;
            background: #fff;
            color: var(--pm-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--pm-transition);
        }

        .pm-modal-close:hover {
            background: #f3f4f6;
            color: var(--pm-text);
        }

        .pm-modal-body {
            padding: 20px;
            overflow: auto;
        }

        .pm-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--pm-border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pm-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .pm-form-group.full {
            grid-column: 1 / -1;
        }

        .pm-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: var(--pm-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .pm-note {
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--pm-blue-soft);
            border: 1px solid #c0d8ea;
            color: #075985;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.5;
        }

        .pm-warning-box,
        .pm-danger-box {
            border-radius: 16px;
            padding: 15px;
            line-height: 1.55;
            font-size: 13px;
            font-weight: 800;
        }

        .pm-warning-box {
            background: var(--pm-orange-soft);
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .pm-danger-box {
            background: var(--pm-red-soft);
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .pm-modal-error {
            display: none;
            margin-top: 12px;
            border-radius: 14px;
            border: 1px solid #fecaca;
            background: var(--pm-red-soft);
            color: #991b1b;
            padding: 11px 12px;
            font-size: 12px;
            font-weight: 800;
            white-space: pre-line;
        }

        .pm-modal-error.is-visible {
            display: block;
        }

        .pm-file {
            min-height: 42px;
            padding: 9px 12px;
        }

        @media (max-width: 1200px) {
            .pm-layout {
                grid-template-columns: 320px minmax(0, 1fr);
            }
        }

        @media (max-width: 992px) {
            .pm-layout {
                grid-template-columns: 1fr;
            }

            .pm-panel-left {
                min-height: auto;
            }

            .pm-product-list {
                max-height: none;
            }
        }

        @media (max-width: 700px) {
            .pm-form-grid {
                grid-template-columns: 1fr;
            }

            .pm-form-group.full {
                grid-column: auto;
            }

            .pm-header,
            .pm-services-head,
            .pm-panel-head {
                align-items: stretch;
                flex-direction: column;
            }

            .pm-header-actions,
            .pm-services-actions {
                width: 100%;
            }

            .pm-btn,
            .pm-btn-soft,
            .pm-btn-blue,
            .pm-btn-danger,
            .pm-btn-warning {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $initialProductId = request('product', optional($currentProduct ?? null)->id);
    @endphp

    <div class="app-content">

        <div class="content-wrapper">
            <div class="content-body pm-page">
                <section id="phase-manager">
                    <div class="pm-header">
                        <div>
                            <h2 class="pm-title">Arbeitsschritte – Produkte & Leistungen</h2>
                            <div class="pm-subtitle">
                                Produkte auswählen, Leistungen verwalten und direkt in die Phasenverwaltung wechseln.
                            </div>

                            <div class="pm-breadcrumb">
                                <a href="{{ url('/') }}">Dashboard</a>
                                <span>›</span>
                                <a href="{{ url('product') }}">Produktliste</a>
                                <span>›</span>
                                <span class="current">Arbeitsschritte</span>
                            </div>
                        </div>

                        <div class="pm-header-actions">
                            <a href="{{ url('product') }}" class="pm-btn-soft">
                                <i class="feather icon-arrow-left"></i>
                                Produktliste
                            </a>

                            <button type="button" class="pm-btn" data-open-modal="modalCreateProduct">
                                <i class="feather icon-plus"></i>
                                Neues Produkt
                            </button>
                        </div>
                    </div>

                    <div class="pm-layout">
                        {{-- LEFT: PRODUCT LIST --}}
                        <aside class="pm-panel pm-panel-left" data-initial-product-id="{{ $initialProductId }}">
                            <div class="pm-panel-head">
                                <div>
                                    <h3 class="pm-panel-title">
                                        Produkte
                                        <span class="pm-count-pill">{{ $articles->count() }}</span>
                                    </h3>
                                    <div class="pm-panel-subtitle">
                                        Wähle ein Produkt, um seine Leistungen zu sehen.
                                    </div>
                                </div>
                            </div>

                            <form method="GET" action="{{ route('task_phase.index') }}" class="pm-search">
                                <div class="pm-search-row">
                                    <input type="text" name="search" class="pm-input" placeholder="Produkt suchen…"
                                        value="{{ $search }}">

                                    <button class="pm-btn-soft" type="submit" style="min-width:44px;padding-inline:12px;">
                                        <i class="feather icon-search"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="pm-product-list">
                                @if($articles->isEmpty())
                                    <div class="pm-empty">
                                        Noch keine Produkte vorhanden. Füge das erste Produkt hinzu.
                                    </div>
                                @else
                                    @foreach($articles as $product)
                                        @php
                                            $isActive = (string) $initialProductId === (string) $product->id;
                                        @endphp

                                        <button type="button" class="pm-product-item {{ $isActive ? 'is-active' : '' }}"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ e($product->article_group) }}"
                                            data-product-image="{{ $product->image ? asset('images/articles/' . $product->image) : '' }}">
                                            <span class="pm-product-avatar">
                                                @if($product->image)
                                                    <img src="{{ asset('images/articles/' . $product->image) }}" alt="">
                                                @else
                                                    <i class="feather icon-box"></i>
                                                @endif
                                            </span>

                                            <span style="min-width:0;">
                                                <div class="pm-product-name">
                                                    {{ $product->article_group }}
                                                </div>

                                                <div class="pm-product-meta">
                                                    @if($product->min_value || $product->max_value)
                                                        {{ $product->min_value ?? '–' }} – {{ $product->max_value ?? '–' }}
                                                    @else
                                                        {{ $product->initial ?: 'kein Kürzel' }}
                                                    @endif
                                                </div>
                                            </span>
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </aside>

                        {{-- RIGHT: SERVICES --}}
                        <main class="pm-panel">
                            <div id="services-header" class="pm-services-head">
                                <div class="pm-selected-product">
                                    <div class="pm-service-avatar" id="services-product-avatar">
                                        <i class="feather icon-box"></i>
                                    </div>

                                    <div style="min-width:0;">
                                        <div class="pm-selected-name" id="services-product-name"></div>
                                        <div class="pm-selected-meta" id="services-product-meta">
                                            Leistungen / Arbeitsschritte für dieses Produkt
                                        </div>
                                    </div>
                                </div>

                                <div class="pm-services-actions">
                                    <select name="service_sort" id="service-sort-select" class="pm-select"
                                        style="width:150px;">
                                        <option value="asc">A–Z</option>
                                        <option value="desc">Z–A</option>
                                    </select>

                                    <a href="#" class="pm-btn-soft" id="btn-phase-versions">
                                        <i class="feather icon-layers"></i>
                                        Versionen
                                    </a>

                                    <button type="button" class="pm-btn" id="btn-open-create-service">
                                        <i class="feather icon-plus"></i>
                                        Neue Leistung
                                    </button>
                                </div>
                            </div>

                            <div class="pm-services-body">
                                <div id="services-empty" class="pm-empty">
                                    Wähle links ein Produkt aus oder lege ein neues an, um Leistungen zu verwalten.
                                </div>

                                <div class="pm-duplicate-banner" id="duplicate-services-banner">
                                    <span id="duplicate-services-text">Doppelte Leistungen gefunden.</span>
                                    <button type="button" class="pm-btn-warning" id="btn-clean-duplicate-services">
                                        <i class="feather icon-zap"></i>
                                        Duplikate bereinigen
                                    </button>
                                </div>

                                <div class="pm-services-grid" id="services-grid"></div>
                            </div>
                        </main>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- CREATE PRODUCT MODAL --}}
    <div class="pm-modal-backdrop" id="modalCreateProduct" aria-hidden="true">
        <div class="pm-modal" role="dialog" aria-modal="true" aria-labelledby="modalCreateProductTitle">
            <form method="POST" action="{{ route('article-groups.save') }}" enctype="multipart/form-data">
                @csrf

                <div class="pm-modal-head">
                    <div>
                        <h3 class="pm-modal-title" id="modalCreateProductTitle">Neues Produkt anlegen</h3>
                        <div class="pm-modal-sub">
                            Beim Speichern werden automatisch Standard-Leistungen erstellt.
                        </div>
                    </div>

                    <button type="button" class="pm-modal-close" data-close-modal>
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="pm-modal-body">
                    <div class="pm-form-grid">
                        <div class="pm-form-group full">
                            <label class="pm-label">Produktname *</label>
                            <input type="text" name="article_group" class="pm-input" required>
                        </div>

                        <div class="pm-form-group full">
                            <label class="pm-label">Kürzel</label>
                            <input type="text" name="initial" class="pm-input" placeholder="Optional">
                        </div>

                        <div class="pm-form-group">
                            <label class="pm-label">Min. Wert</label>
                            <input type="number" step="0.01" name="min_value" class="pm-input">
                        </div>

                        <div class="pm-form-group">
                            <label class="pm-label">Max. Wert</label>
                            <input type="number" step="0.01" name="max_value" class="pm-input">
                        </div>

                        <div class="pm-form-group full">
                            <label class="pm-label">Bild</label>
                            <input type="file" name="image" class="pm-input pm-file" accept="image/*">
                        </div>
                    </div>

                    <div class="pm-note">
                        Standard-Leistungen: Komplettlösung, Montage, Produkt, Planung, Wartung, Reparatur,
                        Reklamation und Sonstiges.
                    </div>
                </div>

                <div class="pm-modal-footer">
                    <button type="button" class="pm-btn-soft" data-close-modal>
                        Abbrechen
                    </button>

                    <button type="submit" class="pm-btn">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CREATE SERVICE MODAL --}}
    <div class="pm-modal-backdrop" id="modalCreateService" aria-hidden="true">
        <div class="pm-modal sm" role="dialog" aria-modal="true" aria-labelledby="create-service-title">
            <form method="POST" action="{{ route('phase.section.store') }}" id="create-service-form">
                @csrf

                <div class="pm-modal-head">
                    <div>
                        <h3 class="pm-modal-title" id="create-service-title">Neue Leistung</h3>
                        <div class="pm-modal-sub" id="create-service-sub">
                            Neue Leistung für das ausgewählte Produkt speichern.
                        </div>
                    </div>

                    <button type="button" class="pm-modal-close" data-close-modal>
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="pm-modal-body">
                    <input type="hidden" name="product_id" id="create-service-product-id" value="">

                    <div class="pm-form-group">
                        <label class="pm-label">Leistungsname *</label>
                        <input type="text" name="phase_section" id="create-service-name" class="pm-input"
                            placeholder="z. B. Beratung, Inbetriebnahme, Service" required>
                    </div>

                    <div class="pm-modal-error" id="create-service-error"></div>
                </div>

                <div class="pm-modal-footer">
                    <button type="button" class="pm-btn-soft" data-close-modal>
                        Abbrechen
                    </button>

                    <button type="submit" class="pm-btn" id="create-service-submit">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT SERVICE MODAL --}}
    <div class="pm-modal-backdrop" id="modalEditService" aria-hidden="true">
        <div class="pm-modal sm" role="dialog" aria-modal="true" aria-labelledby="editServiceTitle">
            <form id="edit-service-form">
                @csrf

                <div class="pm-modal-head">
                    <div>
                        <h3 class="pm-modal-title" id="editServiceTitle">Leistung umbenennen</h3>
                        <div class="pm-modal-sub">
                            Der Name wird direkt per AJAX aktualisiert.
                        </div>
                    </div>

                    <button type="button" class="pm-modal-close" data-close-modal>
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="pm-modal-body">
                    <input type="hidden" id="edit-service-id">

                    <div class="pm-form-group">
                        <label class="pm-label">Leistungsname *</label>
                        <input type="text" id="edit-service-name" class="pm-input" required>
                    </div>

                    <div class="pm-modal-error" id="edit-service-error"></div>
                </div>

                <div class="pm-modal-footer">
                    <button type="button" class="pm-btn-soft" data-close-modal>
                        Abbrechen
                    </button>

                    <button type="submit" class="pm-btn" id="edit-service-submit">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE SERVICE MODAL --}}
    <div class="pm-modal-backdrop" id="modalDeleteService" aria-hidden="true">
        <div class="pm-modal sm" role="dialog" aria-modal="true" aria-labelledby="deleteServiceTitle">
            <div class="pm-modal-head">
                <div>
                    <h3 class="pm-modal-title" id="deleteServiceTitle">Leistung löschen</h3>
                    <div class="pm-modal-sub" id="delete-service-sub">
                        Bitte prüfen Sie den Vorgang.
                    </div>
                </div>

                <button type="button" class="pm-modal-close" data-close-modal>
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="pm-modal-body">
                <input type="hidden" id="delete-service-id">

                <div class="pm-danger-box">
                    Möchten Sie <strong id="delete-service-label">diese Leistung</strong> wirklich löschen?
                    Wenn die Leistung bereits in Phasen, Aktivitäten oder Stages verwendet wird, zeigt das System eine
                    Warnung.
                </div>

                <div class="pm-modal-error" id="delete-service-error"></div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn-soft" data-close-modal>
                    Abbrechen
                </button>

                <button type="button" class="pm-btn-danger" id="confirm-delete-service">
                    <i class="feather icon-trash-2"></i>
                    Ja, löschen
                </button>
            </div>
        </div>
    </div>

    {{-- WARNING MODAL --}}
    <div class="pm-modal-backdrop" id="modalWarning" aria-hidden="true">
        <div class="pm-modal sm" role="dialog" aria-modal="true" aria-labelledby="warningModalTitle">
            <div class="pm-modal-head">
                <div>
                    <h3 class="pm-modal-title" id="warningModalTitle">Hinweis</h3>
                    <div class="pm-modal-sub">Der Vorgang konnte nicht abgeschlossen werden.</div>
                </div>

                <button type="button" class="pm-modal-close" data-close-modal>
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="pm-modal-body">
                <div class="pm-warning-box" id="warningModalMessage">
                    Diese Leistung wird bereits verwendet.
                </div>
            </div>

            <div class="pm-modal-footer">
                <button type="button" class="pm-btn-warning" data-close-modal>
                    Verstanden
                </button>
            </div>
        </div>
    </div>

    <div class="pm-toast-wrap" id="pmToastWrap"></div>
@endsection

@section('script')
    <script>
        (function ($) {
            'use strict';

            const csrfToken = $('meta[name="csrf-token"]').attr('content') || '';

            const routes = {
                servicesListBase: @json(url('/phase-sections/by-product')),
                updateBase: @json(url('/phase-sections')) + '/',
                deleteBase: @json(url('/phase-sections')) + '/',
                cleanupDuplicatesBase: @json(url('/phase-sections/duplicates')) + '/',
                phaseMgmtBase: @json(url('phase_management')),
                taskPhaseDetailsBase: @json(url('/task_phase_details')),
            };

            const protectedDefaults = [
                'complete',
                'montage',
                'product',
                'plan',
                'maintenance',
                'repair',
                'reclaim',
                'others'
            ];

            let currentProductId = null;
            let currentProductName = '';
            let currentProductImage = '';
            let currentSort = 'asc';

            const $servicesHeader = $('#services-header');
            const $servicesEmpty = $('#services-empty');
            const $servicesGrid = $('#services-grid');
            const $sortSelect = $('#service-sort-select');
            const $btnPhaseVersions = $('#btn-phase-versions');
            const $createProductId = $('#create-service-product-id');

            function refreshIcons() {
                if (window.feather) {
                    window.feather.replace();
                }
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function toast(message, type = 'success') {
                const wrap = document.getElementById('pmToastWrap');
                if (!wrap) return;

                const item = document.createElement('div');
                item.className = 'pm-toast ' + type;
                item.textContent = message;

                wrap.appendChild(item);

                setTimeout(function () {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(8px)';
                    setTimeout(function () {
                        item.remove();
                    }, 220);
                }, 2800);
            }

            function openModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';

                setTimeout(function () {
                    const focusable = modal.querySelector('input:not([type="hidden"]), select, button');
                    if (focusable) focusable.focus();
                }, 80);

                refreshIcons();
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                if (!modal) return;

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                if (!document.querySelector('.pm-modal-backdrop.is-open')) {
                    document.body.style.overflow = '';
                }
            }

            function closeAllModals() {
                document.querySelectorAll('.pm-modal-backdrop.is-open').forEach(function (modal) {
                    closeModal(modal.id);
                });
            }

            function showModalError(selector, message) {
                const el = document.querySelector(selector);
                if (!el) return;

                el.textContent = message || '';
                el.classList.toggle('is-visible', !!message);
            }

            function clearModalErrors() {
                [
                    '#create-service-error',
                    '#edit-service-error',
                    '#delete-service-error'
                ].forEach(function (selector) {
                    showModalError(selector, '');
                });
            }

            function showWarning(message) {
                $('#warningModalMessage').text(message || 'Der Vorgang konnte nicht abgeschlossen werden.');
                openModal('modalWarning');
            }

            function setLoading(button, isLoading, loadingText, normalText, icon) {
                if (!button) return;

                button.disabled = isLoading;
                button.innerHTML = isLoading
                    ? '<i class="feather icon-loader"></i> ' + loadingText
                    : '<i class="feather ' + icon + '"></i> ' + normalText;

                refreshIcons();
            }

            function mapLabel(phaseSection) {
                switch (phaseSection) {
                    case 'complete': return 'Komplettlösung';
                    case 'montage': return 'Montage';
                    case 'product': return 'Produkt';
                    case 'plan': return 'Planung';
                    case 'maintenance': return 'Wartung';
                    case 'repair': return 'Reparatur';
                    case 'reclaim': return 'Reklamation';
                    case 'others': return 'Sonstiges';
                    default: return phaseSection;
                }
            }

            function setProductHeader() {
                if (!currentProductId) {
                    $servicesHeader.removeClass('is-visible');
                    $servicesEmpty
                        .removeClass('d-none')
                        .text('Wähle links ein Produkt aus oder lege ein neues an, um Leistungen zu verwalten.');
                    $('#duplicate-services-banner').removeClass('is-visible');
                    return;
                }

                $('#services-product-name').text(currentProductName || 'Produkt');
                $('#services-product-meta').text('Leistungen / Arbeitsschritte für dieses Produkt');

                if (currentProductImage) {
                    $('#services-product-avatar').html('<img src="' + escapeHtml(currentProductImage) + '" alt="">');
                } else {
                    $('#services-product-avatar').html('<i class="feather icon-box"></i>');
                }

                $btnPhaseVersions.attr('href', routes.taskPhaseDetailsBase + '/' + currentProductId);
                $servicesHeader.addClass('is-visible');
                $servicesEmpty.addClass('d-none');

                refreshIcons();
            }

            function renderDuplicateBanner(duplicates) {
                const groups = Array.isArray(duplicates) ? duplicates : [];
                const duplicateRecords = groups.reduce(function (sum, group) {
                    return sum + Math.max(0, (parseInt(group.count, 10) || 0) - 1);
                }, 0);

                if (!duplicateRecords) {
                    $('#duplicate-services-banner').removeClass('is-visible');
                    $('#duplicate-services-text').text('');
                    return;
                }

                $('#duplicate-services-text').html(
                    '<strong>' + duplicateRecords + '</strong> doppelte Leistung(en) in <strong>' + groups.length + '</strong> Gruppe(n) gefunden. Die Liste zeigt nur eindeutige Leistungen.'
                );
                $('#duplicate-services-banner').addClass('is-visible');
                refreshIcons();
            }

            function renderServices(services) {
                if (!services || !services.length) {
                    $servicesGrid.html('');
                    $servicesEmpty
                        .removeClass('d-none')
                        .text('Noch keine Leistungen hinterlegt. Lege die erste Leistung an.');
                    return;
                }

                const html = services.map(function (service, index) {
                    const isDefault = protectedDefaults.includes(service.phase_section);
                    const label = service.label || mapLabel(service.phase_section);
                    const createdAt = service.created_at_formatted || service.created_at || '';

                    return `
                        <div class="pm-service-wrap">
                            <article class="pm-service-card" data-service-id="${escapeHtml(service.id)}">
                                ${isDefault ? '<span class="pm-service-badge">Standard</span>' : ''}

                                <a href="${routes.phaseMgmtBase}/${encodeURIComponent(currentProductId)}/${encodeURIComponent(service.id)}"
                                   class="pm-service-link">
                                    <div class="pm-service-number">${index + 1}</div>
                                    <h4 class="pm-service-label">${escapeHtml(label)}</h4>
                                    <div class="pm-service-date">${escapeHtml(createdAt || '—')}</div>
                                </a>

                                <div class="pm-service-actions">
                                    <button type="button"
                                            class="pm-icon-btn edit js-edit-service"
                                            data-id="${escapeHtml(service.id)}"
                                            data-name="${escapeHtml(service.phase_section)}"
                                            data-label="${escapeHtml(label)}"
                                            title="Bearbeiten">
                                        <i class="feather icon-edit"></i>
                                    </button>

                                    <button type="button"
                                            class="pm-icon-btn delete js-delete-service"
                                            data-id="${escapeHtml(service.id)}"
                                            data-name="${escapeHtml(service.phase_section)}"
                                            data-label="${escapeHtml(label)}"
                                            title="Löschen">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </div>
                            </article>
                        </div>
                    `;
                }).join('');

                $servicesEmpty.addClass('d-none');
                $servicesGrid.html(html);

                refreshIcons();
            }

            function loadServices() {
                if (!currentProductId) return;

                setProductHeader();

                $servicesGrid.html(`
                    <div class="pm-loading" style="grid-column:1/-1;">
                        Leistungen werden geladen…
                    </div>
                `);

                $.ajax({
                    url: routes.servicesListBase + '/' + currentProductId,
                    type: 'GET',
                    data: {
                        sort: currentSort
                    },
                    success: function (resp) {
                        if (!resp || resp.success === false) {
                            $servicesGrid.html(`
                                <div class="pm-loading" style="grid-column:1/-1;color:#b91c1c;">
                                    Fehler beim Laden der Leistungen.
                                </div>
                            `);
                            return;
                        }

                        renderDuplicateBanner(resp.duplicates || []);
                        renderServices(resp.services || []);
                    },
                    error: function () {
                        $servicesGrid.html(`
                            <div class="pm-loading" style="grid-column:1/-1;color:#b91c1c;">
                                Fehler beim Laden der Leistungen.
                            </div>
                        `);
                    }
                });
            }

            function selectProduct($btn) {
                const id = $btn.data('product-id');
                if (!id) return;

                currentProductId = id;
                currentProductName = $btn.data('product-name') || '';
                currentProductImage = $btn.data('product-image') || '';

                $('.pm-product-item').removeClass('is-active');
                $btn.addClass('is-active');

                loadServices();
            }

            function handleAjaxError(xhr, fallbackMessage, modalErrorSelector = null) {
                const message =
                    xhr.responseJSON?.message ||
                    xhr.responseJSON?.error ||
                    fallbackMessage ||
                    'Es ist ein Fehler aufgetreten.';

                if (xhr.status === 409 || xhr.status === 422) {
                    if (modalErrorSelector) {
                        showModalError(modalErrorSelector, message);
                    }

                    showWarning(message);
                    return;
                }

                if (modalErrorSelector) {
                    showModalError(modalErrorSelector, message);
                } else {
                    showWarning(message);
                }
            }

            function openCreateServiceModal() {
                clearModalErrors();

                if (!currentProductId) {
                    showWarning('Bitte zuerst ein Produkt auswählen.');
                    return;
                }

                $createProductId.val(currentProductId);
                $('#create-service-title').text('Neue Leistung');
                $('#create-service-sub').text('Neue Leistung für ' + currentProductName + ' speichern.');
                $('#create-service-name').val('');

                openModal('modalCreateService');
            }

            function openEditServiceModal(id, name, label) {
                clearModalErrors();

                $('#edit-service-id').val(id);
                $('#edit-service-name').val(name || label || '');
                $('#editServiceTitle').text('Leistung umbenennen');

                openModal('modalEditService');
            }

            function openDeleteServiceModal(id, label) {
                clearModalErrors();

                $('#delete-service-id').val(id);
                $('#delete-service-label').text(label || 'diese Leistung');
                $('#delete-service-sub').text(label || 'Leistung löschen');

                openModal('modalDeleteService');
            }

            function updateServiceLabel(id, newName) {
                const label = mapLabel(newName);
                const $card = $('.pm-service-card[data-service-id="' + id + '"]');

                $card.find('.pm-service-label').text(label);
                $('.js-edit-service[data-id="' + id + '"]').data('name', newName).attr('data-name', newName);
                $('.js-edit-service[data-id="' + id + '"]').data('label', label).attr('data-label', label);
                $('.js-delete-service[data-id="' + id + '"]').data('label', label).attr('data-label', label);
            }

            document.addEventListener('click', function (event) {
                const openBtn = event.target.closest('[data-open-modal]');
                if (openBtn) {
                    event.preventDefault();
                    openModal(openBtn.dataset.openModal);
                    return;
                }

                const closeBtn = event.target.closest('[data-close-modal]');
                if (closeBtn) {
                    event.preventDefault();
                    const modal = closeBtn.closest('.pm-modal-backdrop');
                    if (modal) closeModal(modal.id);
                    return;
                }

                if (event.target.classList.contains('pm-modal-backdrop')) {
                    closeModal(event.target.id);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAllModals();
                }
            });

            $(document).on('click', '.pm-product-item', function () {
                selectProduct($(this));
            });

            $sortSelect.on('change', function () {
                currentSort = $(this).val() || 'asc';

                if (currentProductId) {
                    loadServices();
                }
            });

            $('#btn-open-create-service').on('click', function () {
                openCreateServiceModal();
            });

            $(document).on('click', '.js-edit-service', function (event) {
                event.preventDefault();

                openEditServiceModal(
                    $(this).data('id'),
                    $(this).data('name'),
                    $(this).data('label')
                );
            });

            $(document).on('click', '.js-delete-service', function (event) {
                event.preventDefault();

                openDeleteServiceModal(
                    $(this).data('id'),
                    $(this).data('label')
                );
            });

            $('#create-service-form').on('submit', function (event) {
                event.preventDefault();

                const btn = document.getElementById('create-service-submit');
                const name = $('#create-service-name').val();

                if (!currentProductId || !name) {
                    showModalError('#create-service-error', 'Bitte Produkt und Leistungsnamen prüfen.');
                    return;
                }

                clearModalErrors();
                setLoading(btn, true, 'Speichern...', 'Speichern', 'icon-save');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        product_id: currentProductId,
                        phase_section: name
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            const msg = resp?.message || 'Fehler beim Speichern.';
                            showModalError('#create-service-error', msg);
                            showWarning(msg);
                            return;
                        }

                        closeModal('modalCreateService');
                        $('#create-service-name').val('');
                        toast(resp.message || 'Leistung wurde gespeichert.');
                        loadServices();
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Fehler beim Speichern.', '#create-service-error');
                    },
                    complete: function () {
                        setLoading(btn, false, 'Speichern...', 'Speichern', 'icon-save');
                    }
                });
            });

            $('#edit-service-form').on('submit', function (event) {
                event.preventDefault();

                const id = $('#edit-service-id').val();
                const name = $('#edit-service-name').val();
                const btn = document.getElementById('edit-service-submit');

                if (!id || !name) {
                    showModalError('#edit-service-error', 'Bitte geben Sie einen Leistungsnamen ein.');
                    return;
                }

                clearModalErrors();
                setLoading(btn, true, 'Speichern...', 'Speichern', 'icon-save');

                $.ajax({
                    url: routes.updateBase + id + '/ajax-update',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        phase_section: name
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            const msg = resp?.message || 'Fehler beim Speichern.';
                            showModalError('#edit-service-error', msg);
                            showWarning(msg);
                            return;
                        }

                        updateServiceLabel(id, resp.name || name);
                        closeModal('modalEditService');
                        toast('Leistung wurde aktualisiert.');
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Fehler beim Speichern.', '#edit-service-error');
                    },
                    complete: function () {
                        setLoading(btn, false, 'Speichern...', 'Speichern', 'icon-save');
                    }
                });
            });

            $('#confirm-delete-service').on('click', function () {
                const id = $('#delete-service-id').val();
                const btn = document.getElementById('confirm-delete-service');

                if (!id) {
                    showModalError('#delete-service-error', 'Keine Leistung ausgewählt.');
                    return;
                }

                clearModalErrors();
                setLoading(btn, true, 'Löschen...', 'Ja, löschen', 'icon-trash-2');

                $.ajax({
                    url: routes.deleteBase + id + '/ajax-delete',
                    type: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            const msg = resp?.message || 'Fehler beim Löschen.';
                            showModalError('#delete-service-error', msg);
                            showWarning(msg);
                            return;
                        }

                        $('.pm-service-card[data-service-id="' + id + '"]').closest('.pm-service-wrap').remove();

                        if (!$('.pm-service-card').length) {
                            $servicesGrid.html('');
                            $servicesEmpty
                                .removeClass('d-none')
                                .text('Noch keine Leistungen hinterlegt. Lege die erste Leistung an.');
                        }

                        closeModal('modalDeleteService');
                        toast('Leistung wurde gelöscht.');
                        loadServices();
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Fehler beim Löschen.', '#delete-service-error');
                    },
                    complete: function () {
                        setLoading(btn, false, 'Löschen...', 'Ja, löschen', 'icon-trash-2');
                    }
                });
            });

            $('#btn-clean-duplicate-services').on('click', function () {
                if (!currentProductId) {
                    showWarning('Bitte zuerst ein Produkt auswählen.');
                    return;
                }

                const btn = document.getElementById('btn-clean-duplicate-services');
                setLoading(btn, true, 'Bereinigen...', 'Duplikate bereinigen', 'icon-zap');

                $.ajax({
                    url: routes.cleanupDuplicatesBase + currentProductId,
                    type: 'DELETE',
                    data: {
                        _token: csrfToken
                    },
                    success: function (resp) {
                        if (!resp || !resp.success) {
                            showWarning(resp?.message || 'Duplikate konnten nicht bereinigt werden.');
                            return;
                        }

                        toast(resp.message || 'Duplikate wurden bereinigt.');
                        loadServices();
                    },
                    error: function (xhr) {
                        handleAjaxError(xhr, 'Duplikate konnten nicht bereinigt werden.');
                    },
                    complete: function () {
                        setLoading(btn, false, 'Bereinigen...', 'Duplikate bereinigen', 'icon-zap');
                    }
                });
            });

            $(function () {
                const initialProductId = $('.pm-panel-left').data('initial-product-id');

                if (initialProductId) {
                    const $btn = $('.pm-product-item[data-product-id="' + initialProductId + '"]');

                    if ($btn.length) {
                        selectProduct($btn);
                    }
                }

                @if(Session::has('save_msg'))
                    toast(@json(session('save_msg')));
                @endif

                @if(Session::has('delete_msg'))
                    toast(@json(session('delete_msg')), 'error');
                @endif

                @if(Session::has('error'))
                    toast(@json(session('error')), 'error');
                @endif

                @if(Session::has('success'))
                    toast(@json(session('success')));
                @endif

                refreshIcons();
            });
        })(jQuery);
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
                label: 'Arbeitsschritte',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush