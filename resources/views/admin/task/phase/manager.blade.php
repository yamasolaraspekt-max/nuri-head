@extends('admin.layouts.app')

@section('title') Arbeitsschritte – Produkte & Leistungen @stop

@section('style')
<style>
    .phase-layout-shell {
        background: radial-gradient(circle at top left, #f3f4f6 0, #ffffff 45%, #f9fafb 100%);
        min-height: calc(100vh - 120px);
        padding: 8px 0 24px;
    }

    .phase-layout {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .phase-panel {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        padding: 16px 18px;
    }

    .phase-panel-left {
        flex: 0 0 340px;
        max-width: 360px;
        min-height: 420px;
        display: flex;
        flex-direction: column;
    }

    .phase-panel-right {
        flex: 1 1 0;
        min-width: 0;
        min-height: 420px;
    }

    .phase-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 12px;
    }

    .phase-panel-title {
        font-size: 16px;
        font-weight: 600;
        letter-spacing: .02em;
        text-transform: uppercase;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .phase-panel-subtitle {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .phase-panel-title span.badge-soft {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .phase-search {
        margin-bottom: 8px;
    }

    .phase-search .form-control {
        border-radius: 999px;
        font-size: 13px;
        border-color: #e5e7eb;
    }

    .phase-search .input-group-text,
    .phase-search .btn {
        border-radius: 999px;
        font-size: 12px;
        padding-inline: 12px;
    }

    .product-list {
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
        margin-top: 8px;
    }

    .product-item {
        width: 100%;
        text-align: left;
        padding: 8px 10px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
        transition: all .15s ease-in-out;
        cursor: pointer;
    }

    .product-item:hover {
        background: #f3f4ff;
        border-color: #c7d2fe;
    }

    .product-item.is-active {
        background: #cfe09b;
        border-color: transparent; 
        color: #022c22;
    }

    .product-item.is-active .product-title {
        color: #ffffff;
    }

    .product-item.is-active .product-meta {
        color: #e5e7eb;
    }

    .product-avatar {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        background: #eef2ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .product-avatar img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .product-title {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }

    .product-meta {
        font-size: 11px;
        color: #6b7280;
    }

    .product-empty {
        font-size: 13px;
        color: #6b7280;
        text-align: center;
        margin-top: 32px;
    }

    .phase-sort-select {
        max-width: 150px;
        font-size: 12px;
        border-radius: 999px;
        padding-inline: 12px;
    }

    .services-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 4px;
    }

    .services-product {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .services-product-main {
        display: flex;
        flex-direction: column;
    }

    .services-product-name {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
    }

    .services-product-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .services-actions .btn {
        border-radius: 999px;
        font-size: 12px;
        padding-inline: 13px;
    }

    .services-empty {
        border-radius: 18px;
        border: 1px dashed #d1d5db;
        padding: 18px;
        text-align: center;
        font-size: 13px;
        color: #6b7280;
        margin-top: 12px;
        background: #f9fafb;
    }

    /* SERVICE CARD – similar to your old style */
    .service-card {
        background: #f5f5f5;
        border-radius: 20px !important;
        margin: 5px;
        border: 1px solid #cbc3c3;
        transition: all .15s ease-in-out;
    }

    .service-card:hover {
        background: #cfe09b;
        cursor: pointer;
        color: #ffffff;
    }

    .service-card:hover .service-label {
        color: #ffffff !important;
    }

    .service-card-body {
        position: relative;
        padding-top: 18px !important;
        padding-bottom: 14px !important;
    }

    .service-card-link {
        display: block;
        text-decoration: none !important;
        color: inherit;
    }

    .service-label {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
    }

    .service-actions {
        margin-top: 6px;
        display: flex;
        justify-content: center;
        gap: 6px;
    }

    .service-actions .btn {
        border-radius: 999px;
        font-size: 11px;
        padding: 4px 9px;
    }

    .btn-service-edit {
        border-color: #74b2d4;
        color: #1f2937;
        background: #ffffff;
    }

    .btn-service-edit:hover {
        background: #74b2d4;
        color: #ffffff;
    }

    .btn-service-delete {
        border-color: #ef4444;
        color: #b91c1c;
        background: #fff1f2;
    }

    .btn-service-delete:hover {
        background: #ef4444;
        color: #ffffff;
    }

    @media (max-width: 991.98px) {
        .phase-layout {
            flex-direction: column;
        }
        .phase-panel-left {
            flex: 1 1 100%;
            max-width: 100%;
        }
        .phase-panel-right {
            flex: 1 1 100%;
        }
        .product-list {
            max-height: none;
        }
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Arbeitsschritte – Produkte & Leistungen</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Arbeitsschritte
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body phase-layout-shell">
            <section id="phase-manager">
                <div class="row">
                    <div class="col-12">
                        <div class="phase-layout">

                            {{-- LEFT: PRODUCT LIST --}}
                            <div class="phase-panel phase-panel-left"
                                 data-initial-product-id="{{ request('product') }}">
                                <div class="phase-panel-header">
                                    <div>
                                        <div class="phase-panel-title">
                                            Produkte
                                            <span class="badge-soft">
                                                {{ $articles->count() }} aktiv
                                            </span>
                                        </div>
                                        <div class="phase-panel-subtitle">
                                            Wähle ein Produkt, um seine Leistungen zu sehen.
                                        </div>
                                    </div>
                                    <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-toggle="modal"
                                            data-target="#modal-create-product">
                                        <i class="feather icon-plus"></i> Neues Produkt
                                    </button>
                                </div>

                                <form method="GET" action="{{ route('task_phase.index') }}" class="phase-search">
                                    <div class="input-group input-group-sm">
                                        <input type="text"
                                               name="search"
                                               class="form-control"
                                               placeholder="Produkt suchen…"
                                               value="{{ $search }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="feather icon-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="product-list">
                                    @if($articles->isEmpty())
                                        <div class="product-empty">
                                            Noch keine Produkte vorhanden. Füge das erste Produkt hinzu.
                                        </div>
                                    @else
                                        @foreach($articles as $product)
                                            <button type="button"
                                                    class="product-item"
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->article_group }}"
                                                    data-product-image="{{ $product->image ? asset('images/articles/'.$product->image) : '' }}">
                                                <span class="product-avatar">
                                                    @if($product->image)
                                                        <img src="{{ asset('images/articles/'.$product->image) }}" alt="">
                                                    @else
                                                        <i class="feather icon-box"></i>
                                                    @endif
                                                </span>
                                                <span>
                                                    <div class="product-title">
                                                        {{ $product->article_group }}
                                                    </div>
                                                    <div class="product-meta">
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
                            </div>

                            {{-- RIGHT: SERVICES / PHASE SECTIONS --}}
                            <div class="phase-panel phase-panel-right">
                                {{-- Header is static, content is filled by JS --}}
                                <div id="services-header" class="services-header d-none">
                                    <div class="services-product">
                                        <div class="product-avatar" id="services-product-avatar">
                                            <i class="feather icon-box"></i>
                                        </div>
                                        <div class="services-product-main">
                                            <div class="services-product-name" id="services-product-name">
                                                <!-- Produktname via JS -->
                                            </div>
                                            <div class="services-product-meta" id="services-product-meta">
                                                Leistungen / Arbeitsschritte für dieses Produkt
                                            </div>
                                        </div>
                                    </div>
                                    <div class="services-actions d-flex align-items-center">
                                        <form class="mr-1" id="service-sort-form">
                                            <select name="service_sort"
                                                    id="service-sort-select"
                                                    class="form-control form-control-sm phase-sort-select">
                                                <option value="asc">A–Z</option>
                                                <option value="desc">Z–A</option>
                                            </select>
                                        </form>
 

                                        <button type="button"
                                            class="btn btn-sm btn-primary"
                                            id="btn-open-create-service">
                                        <i class="feather icon-plus"></i> Neue Leistung
                                    </button>
                                    </div>
                                </div>

                                <div id="services-empty" class="services-empty">
                                    Wähle links ein Produkt aus oder lege ein neues an, um Leistungen zu verwalten.
                                </div>

                                <div class="row mt-1" id="services-grid">
                                    {{-- Cards will be injected via AJAX --}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

{{-- CREATE PRODUCT MODAL --}}
<div class="modal fade text-left"
     id="modal-create-product"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-title">Neues Produkt anlegen</h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form"
                  method="POST"
                  action="{{ route('article-groups.save') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Produktname</label>
                        <input type="text"
                               name="article_group"
                               class="form-control"
                               required>
                    </div>
                    <div class="form-group">
                        <label>Kürzel (optional)</label>
                        <input type="text"
                               name="initial"
                               class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Min. Wert</label>
                            <input type="number"
                                   step="0.01"
                                   name="min_value"
                                   class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label>Max. Wert</label>
                            <input type="number"
                                   step="0.01"
                                   name="max_value"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Bild (optional)</label>
                        <input type="file"
                               name="image"
                               class="form-control-file">
                    </div>
                    <p class="text-muted mb-0" style="font-size: 12px;">
                        Hinweis: Beim Erstellen eines Produkts werden automatisch die Standard-Leistungen
                        (Komplettlösung, Montage, Produkt, Planung, Wartung, Reparatur, Reklamation, Sonstiges) angelegt.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CREATE SERVICE MODAL (global, product_id is set via JS) --}}
<div class="modal fade text-left"
     id="modal-create-service"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-title" id="create-service-title">Neue Leistung</h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form form-horizontal"
                  method="POST"
                  action="{{ action('App\Http\Controllers\PhaseSectionController@store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-body">
                        <div class="form-group">
                            <label>Phasen-Name</label>
                            <input type="text"
                                   name="phase_section"
                                   class="form-control"
                                   required>
                            <input type="hidden"
                                   name="product_id"
                                   id="create-service-product-id"
                                   value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- GLOBAL EDIT SERVICE MODAL --}}
<div class="modal fade text-left"
     id="modal-edit-service"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary white">
                <h5 class="modal-title">Leistung umbenennen</h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-service-form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="edit-service-id">
                    <div class="form-group">
                        <label>Phasen-Name</label>
                        <input type="text"
                               id="edit-service-name"
                               class="form-control"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="btn btn-primary">
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- GLOBAL DELETE SERVICE MODAL --}}
<div class="modal fade"
     id="modal-delete-service"
     tabindex="-1"
     role="dialog"
     aria-hidden="true"
     data-backdrop="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h5 class="modal-title" id="delete-service-title"></h5>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Leistung löschen</h5>
                <p>
                    Möchten Sie diese Leistung wirklich löschen?
                    Die Standard-Leistungen bleiben immer erhalten.
                </p>
                <input type="hidden" id="delete-service-id">
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Abbrechen
                </button>
                <button type="button"
                        class="btn btn-danger"
                        id="confirm-delete-service">
                    Ja, löschen
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        const csrfToken            = $('meta[name="csrf-token"]').attr('content');
        const servicesListBaseUrl  = "{{ url('/phase-sections/by-product') }}"; // GET /phase-sections/by-product/{product}?sort=asc|desc
        const updateBaseUrl        = "{{ url('/phase-sections') }}/";           // POST /phase-sections/{id}/ajax-update
        const deleteBaseUrl        = "{{ url('/phase-sections') }}/";           // POST /phase-sections/{id}/ajax-delete
        const phaseMgmtBaseUrl     = "{{ url('phase_management') }}";           // /phase_management/{product}/{service}
        const taskPhaseDetailsBase = "{{ url('/task_phase_details') }}";        // /task_phase_details/{product}

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

        const $servicesHeader  = $('#services-header');
        const $servicesEmpty   = $('#services-empty');
        const $servicesGrid    = $('#services-grid');
        const $sortSelect      = $('#service-sort-select');
        const $btnPhaseVersion = $('#btn-phase-versions');
        const $btnCreateServ   = $('#btn-open-create-service');
        const $createProductId = $('#create-service-product-id');
        const $createTitle     = $('#create-service-title');

        function mapLabel(phaseSection) {
            switch (phaseSection) {
                case 'complete':    return 'Komplettlösung';
                case 'montage':     return 'Montage';
                case 'product':     return 'Produkt';
                case 'plan':        return 'Planung';
                case 'maintenance': return 'Wartung';
                case 'repair':      return 'Reparatur';
                case 'reclaim':     return 'Reklamation';
                case 'others':      return 'Sonstiges';
                default:            return phaseSection;
            }
        }

        function setProductHeader() {
            if (!currentProductId) {
                $servicesHeader.addClass('d-none');
                $servicesEmpty.removeClass('d-none')
                    .text('Wähle links ein Produkt aus oder lege ein neues an, um Leistungen zu verwalten.');
                return;
            }

            $('#services-product-name').text(currentProductName);
            if (currentProductImage) {
                $('#services-product-avatar').html(
                    '<img src="' + currentProductImage + '" alt="" />'
                );
            } else {
                $('#services-product-avatar').html('<i class="feather icon-box"></i>');
            }

            $btnPhaseVersion.attr('href', taskPhaseDetailsBase + '/' + currentProductId);
            $servicesHeader.removeClass('d-none');
            $servicesEmpty.addClass('d-none');
        }

        function renderServices(services) {
            if (!services || services.length === 0) {
                $servicesGrid.html('');
                $servicesEmpty.removeClass('d-none')
                    .text('Noch keine Leistungen hinterlegt. Lege die erste Leistung an.');
                return;
            }

            let html = '';
            services.forEach(function (service, index) {
                const isDefault = protectedDefaults.indexOf(service.phase_section) !== -1;
                const label     = service.label ? service.label : mapLabel(service.phase_section);
                const createdAt = service.created_at_formatted || service.created_at || '';

                html += `
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="card text-center service-card" data-service-id="${service.id}">
                            <div class="card-content">
                                <div class="card-body service-card-body">
                                    <a href="${phaseMgmtBaseUrl}/${currentProductId}/${service.id}" class="service-card-link">
                                        <div class="avatar bg-rgba-secondary p-50 m-0 mb-1">
                                            <div class="avatar-content">
                                                ${index + 1}
                                            </div>
                                        </div>
                                        <h6 class="text-bold-500 service-label">
                                            ${label}
                                        </h6>
                                    </a>
                                    ${!isDefault ? `
                                    <div class="service-actions">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary btn-service-edit js-edit-service"
                                                data-id="${service.id}"
                                                data-name="${service.phase_section}">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-service-delete js-delete-service"
                                                data-id="${service.id}"
                                                data-label="${label}">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </div>` : ''}
                                    <div class="mt-50" style="font-size: 11px; color:#9ca3af;">
                                        ${createdAt}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            $servicesEmpty.addClass('d-none');
            $servicesGrid.html(html);
        }

        function loadServices() {
            if (!currentProductId) return;

            setProductHeader();
            $servicesGrid.html(
                '<div class="col-12 text-center text-muted py-2">Leistungen werden geladen…</div>'
            );

            $.ajax({
                url: servicesListBaseUrl + '/' + currentProductId,
                type: 'GET',
                data: { sort: currentSort },
                success: function (resp) {
                    if (!resp || resp.success === false) {
                        $servicesGrid.html(
                            '<div class="col-12 text-center text-danger py-2">Fehler beim Laden der Leistungen.</div>'
                        );
                        return;
                    }

                    // Expect: resp.services = [ {id, phase_section, label?, created_at_formatted?}, ... ]
                    renderServices(resp.services || []);
                },
                error: function () {
                    $servicesGrid.html(
                        '<div class="col-12 text-center text-danger py-2">Fehler beim Laden der Leistungen.</div>'
                    );
                }
            });
        }

        // Product selection
        $('.product-panel, .phase-panel-left'); // just to ensure existing container

        $('.product-item').on('click', function () {
            const $btn = $(this);
            const id   = $btn.data('product-id');

            if (!id) return;

            currentProductId    = id;
            currentProductName  = $btn.data('product-name') || '';
            currentProductImage = $btn.data('product-image') || '';

            $('.product-item').removeClass('is-active');
            $btn.addClass('is-active');

            loadServices();
        });

        // Sort change
        $sortSelect.on('change', function () {
            currentSort = $(this).val() || 'asc';
            if (currentProductId) {
                loadServices();
            }
        });

        // "Neue Leistung" button
        $btnCreateServ.on('click', function () {
            if (!currentProductId) {
                alert('Bitte zuerst ein Produkt auswählen.');
                return;
            }

            $createProductId.val(currentProductId);
            $createTitle.text('Neue Leistung für ' + currentProductName);
            $('#modal-create-service').modal('show');
        });

        // EDIT – open modal
        $(document).on('click', '.js-edit-service', function (e) {
            e.preventDefault();
            const id   = $(this).data('id');
            const name = $(this).data('name');

            $('#edit-service-id').val(id);
            $('#edit-service-name').val(name);
            $('#modal-edit-service').modal('show');
        });

        // EDIT – submit AJAX
        $('#edit-service-form').on('submit', function (e) {
            e.preventDefault();

            const id   = $('#edit-service-id').val();
            const name = $('#edit-service-name').val();

            $.ajax({
                url:  updateBaseUrl + id + '/ajax-update',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    phase_section: name
                },
                success: function (resp) {
                    if (!resp || !resp.success) {
                        alert(resp && resp.message ? resp.message : 'Fehler beim Speichern.');
                        return;
                    }

                    // Update label in card
                    const labelText = resp.name || name;
                    const $card = $('.service-card[data-service-id="' + id + '"]');

                    $card.find('.service-label').text(labelText);

                    // Update data attributes for future edits/deletes
                    $('.js-edit-service[data-id="' + id + '"]').data('name', resp.name);
                    $('.js-delete-service[data-id="' + id + '"]').data('label', resp.name);

                    $('#modal-edit-service').modal('hide');
                },
                error: function () {
                    alert('Fehler beim Speichern.');
                }
            });
        });

        // DELETE – open modal
        $(document).on('click', '.js-delete-service', function (e) {
            e.preventDefault();
            const id    = $(this).data('id');
            const label = $(this).data('label');

            $('#delete-service-id').val(id);
            $('#delete-service-title').text(label);
            $('#modal-delete-service').modal('show');
        });

        // DELETE – confirm
        $('#confirm-delete-service').on('click', function () {
            const id = $('#delete-service-id').val();

            $.ajax({
                url:  deleteBaseUrl + id + '/ajax-delete',
                type: 'POST',
                data: {
                    _token: csrfToken,
                    _method: 'DELETE'
                },
                success: function (resp) {
                    if (!resp || !resp.success) {
                        alert(resp && resp.message ? resp.message : 'Fehler beim Löschen.');
                        return;
                    }

                    $('.service-card[data-service-id="' + id + '"]')
                        .closest('.col-xl-2, .col-md-4, .col-sm-6')
                        .remove();

                    if ($('.service-card').length === 0) {
                        $servicesEmpty.removeClass('d-none')
                            .text('Noch keine Leistungen hinterlegt. Lege die erste Leistung an.');
                    }

                    $('#modal-delete-service').modal('hide');
                },
                error: function () {
                    alert('Fehler beim Löschen.');
                }
            });
        });

        // Initial product (after redirect with ?product=ID)
        const initialProductId = $('.phase-panel-left').data('initial-product-id');
        if (initialProductId) {
            const $btn = $('.product-item[data-product-id="' + initialProductId + '"]');
            if ($btn.length) {
                $btn.trigger('click');
            }
        }
    });
</script>
@endsection
