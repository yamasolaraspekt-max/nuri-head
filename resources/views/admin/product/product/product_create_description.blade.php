@extends('admin.layouts.app')

@section('title', 'Produktbeschreibung')

@php
    $productId = $data->id ?? request()->route('id');
    $brandName = optional($brand)->name ?? 'Ohne Hersteller';
    $productTitle = trim(($data->product ?? '') . ' - ' . ($data->model ?? ''));
    $searchValue = request('search');
@endphp

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">
                                Produktbeschreibung
                            </h2>

                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/') }}">Dashboard</a>
                                    </li>

                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/product') }}">Produkte</a>
                                    </li>

                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/product_details/' . $productId) }}">
                                            Produktdetails
                                        </a>
                                    </li>

                                    <li class="breadcrumb-item active">
                                        Beschreibung
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .pd-page {
                    --pd-primary: #8fc73e;
                    --pd-primary-dark: #79aa31;
                    --pd-blue: #74b2d4;
                    --pd-border: #e5e7eb;
                    --pd-text: #111827;
                    --pd-muted: #6b7280;
                    --pd-danger: #ef4444;
                    --pd-success: #10b981;
                }

                .pd-hero {
                    background: linear-gradient(135deg, #ffffff 0%, #f2fae8 100%);
                    border: 1px solid var(--pd-border);
                    border-radius: 18px;
                    padding: 18px;
                    margin-bottom: 18px;
                    box-shadow: 0 10px 25px -18px rgba(15, 23, 42, .35);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 16px;
                    flex-wrap: wrap;
                }

                .pd-title {
                    font-size: 22px;
                    font-weight: 900;
                    color: var(--pd-text);
                    margin: 0;
                    letter-spacing: -.02em;
                }

                .pd-subtitle {
                    color: var(--pd-muted);
                    font-size: 13px;
                    margin-top: 5px;
                }

                .pd-badges {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    flex-wrap: wrap;
                    margin-top: 10px;
                }

                .pd-product-badge,
                .pd-brand-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 7px;
                    border-radius: 999px;
                    padding: 7px 12px;
                    font-size: 12px;
                    font-weight: 900;
                }

                .pd-product-badge {
                    background: #eff6ff;
                    color: #1d4ed8;
                }

                .pd-brand-badge {
                    background: #ecfdf5;
                    color: #047857;
                }

                .pd-actions {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    flex-wrap: wrap;
                }

                .pd-btn {
                    border: none;
                    border-radius: 12px;
                    padding: 10px 15px;
                    font-weight: 900;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    transition: all .18s ease;
                    text-decoration: none !important;
                    cursor: pointer;
                }

                .pd-btn-primary {
                    background: var(--pd-primary);
                    color: #fff !important;
                }

                .pd-btn-primary:hover {
                    background: var(--pd-primary-dark);
                    color: #fff !important;
                }

                .pd-btn-light {
                    background: #fff;
                    color: var(--pd-text) !important;
                    border: 1px solid var(--pd-border);
                }

                .pd-btn-light:hover {
                    background: #f9fafb;
                    color: var(--pd-text) !important;
                }

                .pd-btn-danger {
                    background: #fef2f2;
                    color: #b91c1c !important;
                    border: 1px solid rgba(239, 68, 68, .2);
                }

                .pd-btn-danger:hover {
                    background: #fee2e2;
                    color: #991b1b !important;
                }

                .pd-toolbar {
                    background: #fff;
                    border: 1px solid var(--pd-border);
                    border-radius: 16px;
                    padding: 14px;
                    margin-bottom: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    flex-wrap: wrap;
                }

                .pd-search {
                    flex: 1;
                    min-width: 260px;
                    display: flex;
                    gap: 8px;
                }

                .pd-search input {
                    border: 1px solid var(--pd-border);
                    border-radius: 12px;
                    padding: 10px 12px;
                    min-height: 42px;
                    width: 100%;
                    outline: none;
                    transition: all .18s ease;
                }

                .pd-search input:focus {
                    border-color: var(--pd-primary);
                    box-shadow: 0 0 0 3px #f2fae8;
                }

                .pd-card {
                    border: 1px solid var(--pd-border);
                    border-radius: 18px;
                    background: #fff;
                    overflow: hidden;
                    box-shadow: 0 10px 25px -20px rgba(15, 23, 42, .35);
                    margin-bottom: 18px;
                }

                .pd-card-header {
                    padding: 16px 18px;
                    border-bottom: 1px solid var(--pd-border);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    flex-wrap: wrap;
                    background: #fcfcfd;
                }

                .pd-card-title {
                    font-size: 15px;
                    font-weight: 900;
                    color: var(--pd-text);
                    margin: 0;
                }

                .pd-count {
                    background: #f3f4f6;
                    color: #374151;
                    border-radius: 999px;
                    padding: 5px 10px;
                    font-size: 12px;
                    font-weight: 900;
                }

                .pd-table {
                    margin: 0;
                }

                .pd-table thead th {
                    background: #f9fafb;
                    color: #6b7280;
                    font-size: 11px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: .06em;
                    border-bottom: 1px solid var(--pd-border);
                    white-space: nowrap;
                }

                .pd-table tbody td,
                .pd-table tbody th {
                    vertical-align: middle;
                    border-top: 1px solid #f3f4f6;
                }

                .pd-id {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    min-width: 52px;
                    height: 32px;
                    background: #eff6ff;
                    color: #1d4ed8;
                    border-radius: 10px;
                    font-weight: 900;
                }

                .pd-field {
                    display: inline-flex;
                    align-items: center;
                    border-radius: 999px;
                    padding: 6px 10px;
                    font-size: 12px;
                    font-weight: 900;
                    background: #f3f4f6;
                    color: #374151;
                    max-width: 260px;
                    white-space: normal;
                }

                .pd-desc {
                    max-width: 430px;
                    color: #4b5563;
                    font-size: 13px;
                    line-height: 1.45;
                    white-space: normal;
                }

                .pd-remark {
                    max-width: 300px;
                    color: #6b7280;
                    font-size: 13px;
                    line-height: 1.45;
                    white-space: normal;
                }

                .pd-icon-btn {
                    width: 36px;
                    height: 36px;
                    border-radius: 999px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border: none;
                    transition: all .18s ease;
                    margin-right: 5px;
                    cursor: pointer;
                }

                .pd-icon-edit {
                    background: #eff6ff;
                    color: #1d4ed8;
                }

                .pd-icon-edit:hover {
                    background: #dbeafe;
                    color: #1e40af;
                }

                .pd-icon-delete {
                    background: #fef2f2;
                    color: #b91c1c;
                }

                .pd-icon-delete:hover {
                    background: #fee2e2;
                    color: #991b1b;
                }

                .pd-empty {
                    padding: 45px 20px;
                    text-align: center;
                    color: var(--pd-muted);
                    background: #fff;
                }

                .pd-form-table input,
                .pd-form-table textarea {
                    min-width: 180px;
                }

                .pd-form-table textarea {
                    min-height: 74px;
                    resize: vertical;
                }

                .pd-modal-label {
                    font-weight: 800;
                    color: #374151;
                    font-size: 13px;
                }

                .pd-pagination {
                    margin-top: 16px;
                }

                .pd-pagination .pagination {
                    justify-content: flex-end;
                    flex-wrap: wrap;
                    gap: 5px;
                }

                .pd-pagination .page-link {
                    border-radius: 10px !important;
                    border: 1px solid var(--pd-border);
                    color: #374151;
                }

                .pd-pagination .page-item.active .page-link {
                    background: var(--pd-primary);
                    border-color: var(--pd-primary);
                    color: #fff;
                }

                @media(max-width: 768px) {
                    .pd-hero,
                    .pd-toolbar,
                    .pd-actions,
                    .pd-search {
                        flex-direction: column;
                        align-items: stretch;
                    }

                    .pd-actions .pd-btn,
                    .pd-search .pd-btn {
                        width: 100%;
                    }
                }
            </style>

            <div class="content-body pd-page">
                <div class="pd-hero">
                    <div>
                        <h3 class="pd-title">Produktbeschreibung</h3>

                        <div class="pd-subtitle">
                            Technische Überschriften, Beschreibungen und Anmerkungen für dieses Produkt verwalten.
                        </div>

                        <div class="pd-badges">
                            <span class="pd-product-badge">
                                <i class="feather icon-box"></i>
                                {{ $productTitle ?: 'Produkt #' . $productId }}
                            </span>

                            <span class="pd-brand-badge">
                                <i class="feather icon-tag"></i>
                                {{ $brandName }}
                            </span>
                        </div>
                    </div>

                    <div class="pd-actions">
                        <a href="{{ url('/product_details/' . $productId) }}" class="pd-btn pd-btn-light">
                            <i class="feather icon-arrow-left"></i>
                            Zurück zu Produktdetails
                        </a>

                        <button type="button" class="pd-btn pd-btn-primary" id="scrollToCreateForm">
                            <i class="feather icon-plus"></i>
                            Beschreibung erstellen
                        </button>
                    </div>
                </div>

                <div class="pd-toolbar">
                    <form action="{{ route('product.create.description', $productId) }}" method="GET" class="pd-search">
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchValue }}"
                            placeholder="Suchen nach Überschrift, Beschreibung oder Anmerkung..."
                        >

                        <button class="pd-btn pd-btn-primary" type="submit">
                            <i class="feather icon-search"></i>
                            Suchen
                        </button>

                        @if($searchValue)
                            <a href="{{ route('product.create.description', $productId) }}" class="pd-btn pd-btn-light">
                                <i class="feather icon-x"></i>
                                Zurücksetzen
                            </a>
                        @endif
                    </form>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Bitte prüfen Sie die Eingaben.</strong>

                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="pd-card" id="createDescriptionCard">
                    <div class="pd-card-header">
                        <h4 class="pd-card-title">
                            Neue Beschreibungen hinzufügen
                        </h4>

                        <span class="pd-count">
                            Mehrere Zeilen möglich
                        </span>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('product.description.store') }}" method="POST" id="descriptionCreateForm">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered pd-form-table" id="descriptionRowsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Hersteller</th>
                                            <th>Art.name</th>
                                            <th>Überschrift</th>
                                            <th>Beschreibung</th>
                                            <th>Anmerkung</th>
                                            <th style="width:90px;">Aktion</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr>
                                            <input type="hidden" name="product[0][product_id]" value="{{ $productId }}">

                                            <td>
                                                <input type="text" class="form-control" disabled value="{{ $brandName }}">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control" disabled value="{{ $productTitle }}">
                                            </td>

                                            <td>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    name="product[0][field]"
                                                    placeholder="Überschrift"
                                                    required
                                                >
                                            </td>

                                            <td>
                                                <textarea
                                                    class="form-control"
                                                    name="product[0][description]"
                                                    placeholder="Beschreibung"
                                                    required
                                                ></textarea>
                                            </td>

                                            <td>
                                                <textarea
                                                    class="form-control"
                                                    name="product[0][remark]"
                                                    placeholder="Anmerkung"
                                                ></textarea>
                                            </td>

                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between flex-wrap mt-2" style="gap:10px;">
                                <div class="d-flex flex-wrap" style="gap:10px;">
                                    <button type="submit" class="pd-btn pd-btn-primary">
                                        <i class="feather icon-save"></i>
                                        Datensatz speichern
                                    </button>

                                    <button type="button" class="pd-btn pd-btn-light" id="addDescriptionRow">
                                        <i class="feather icon-plus"></i>
                                        Zeile hinzufügen
                                    </button>
                                </div>

                                <button type="button" class="pd-btn pd-btn-light" id="clearDescriptionRows">
                                    <i class="feather icon-refresh-cw"></i>
                                    Neue Zeilen leeren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="pd-card">
                    <div class="pd-card-header">
                        <h4 class="pd-card-title">
                            Bestehende Beschreibungen
                        </h4>

                        <span class="pd-count">
                            {{ $description->total() }} Eintrag(e)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table pd-table">
                            <thead>
                                <tr>
                                    <th style="width:90px;">ID</th>
                                    <th>Hersteller</th>
                                    <th>Art.name</th>
                                    <th>Überschrift</th>
                                    <th>Beschreibung</th>
                                    <th>Anmerkung</th>
                                    <th style="width:130px;" class="text-right">Aktion</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($description as $desk)
                                    <tr>
                                        <th>
                                            <span class="pd-id">#{{ $desk->id }}</span>
                                        </th>

                                        <td>
                                            <strong>{{ $brandName }}</strong>
                                        </td>

                                        <td>
                                            {{ $desk->product }} - {{ $desk->model }}
                                        </td>

                                        <td>
                                            <span class="pd-field">{{ $desk->field }}</span>
                                        </td>

                                        <td>
                                            <div class="pd-desc">
                                                {{ $desk->description }}
                                            </div>
                                        </td>

                                        <td>
                                            <div class="pd-remark">
                                                {{ $desk->remark ?: '—' }}
                                            </div>
                                        </td>

                                        <td class="text-right">
                                            <button
                                                type="button"
                                                class="pd-icon-btn pd-icon-edit"
                                                data-toggle="modal"
                                                data-target="#editDescriptionModal{{ $desk->id }}"
                                                title="Bearbeiten"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="pd-icon-btn pd-icon-delete"
                                                data-toggle="modal"
                                                data-target="#deleteDescriptionModal{{ $desk->id }}"
                                                title="Löschen"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="pd-empty">
                                                <i class="feather icon-info mb-1" style="width:32px;height:32px;"></i>
                                                <div>Keine Beschreibungen gefunden.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($description->hasPages())
                    <div class="pd-pagination">
                        {{ $description->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit/Delete Modals --}}
    @foreach($description as $desk)
        <div class="modal fade text-left" id="editDescriptionModal{{ $desk->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('product.description.update') }}">
                        @csrf

                        <input type="hidden" name="id" value="{{ $desk->id }}">

                        <div class="modal-header">
                            <h4 class="modal-title">Beschreibung bearbeiten</h4>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label class="pd-modal-label">Produkt</label>
                                <input type="text" disabled class="form-control" value="{{ $desk->product }} - {{ $desk->model }}">
                            </div>

                            <div class="form-group">
                                <label class="pd-modal-label">Überschrift</label>
                                <input type="text" name="field" class="form-control" value="{{ old('field', $desk->field) }}" required>
                            </div>

                            <div class="form-group">
                                <label class="pd-modal-label">Beschreibung</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ old('description', $desk->description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="pd-modal-label">Anmerkung</label>
                                <textarea name="remark" class="form-control" rows="3">{{ old('remark', $desk->remark) }}</textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                Abbrechen
                            </button>

                            <button type="submit" class="btn btn-primary">
                                Speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade text-left" id="deleteDescriptionModal{{ $desk->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Beschreibung löschen</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-1">
                            Möchten Sie diese Produktbeschreibung wirklich löschen?
                        </p>

                        <p class="mb-0 text-muted">
                            Datensatznummer: <strong>#{{ $desk->id }}</strong>
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            Abbrechen
                        </button>

                        <a href="{{ route('product.discription.destroy', ['id' => $desk->id]) }}" class="btn btn-danger">
                            Ja, löschen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@stop

@section('script')
    <script>
        $(document).ready(function () {
            const productId = @json($productId);
            const brandName = @json($brandName);
            const productTitle = @json($productTitle);

            let rowIndex = 0;

            function buildRow(index) {
                return `
                    <tr>
                        <input type="hidden" name="product[${index}][product_id]" value="${productId}">

                        <td>
                            <input type="text" class="form-control" disabled value="${brandName}">
                        </td>

                        <td>
                            <input type="text" class="form-control" disabled value="${productTitle}">
                        </td>

                        <td>
                            <input
                                type="text"
                                class="form-control"
                                name="product[${index}][field]"
                                placeholder="Überschrift"
                                required
                            >
                        </td>

                        <td>
                            <textarea
                                class="form-control"
                                name="product[${index}][description]"
                                placeholder="Beschreibung"
                                required
                            ></textarea>
                        </td>

                        <td>
                            <textarea
                                class="form-control"
                                name="product[${index}][remark]"
                                placeholder="Anmerkung"
                            ></textarea>
                        </td>

                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-description-row">
                                <i class="feather icon-trash-2"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }

            $('#addDescriptionRow').on('click', function () {
                rowIndex++;
                $('#descriptionRowsTable tbody').append(buildRow(rowIndex));

                if (window.feather) {
                    feather.replace();
                }
            });

            $(document).on('click', '.remove-description-row', function () {
                $(this).closest('tr').remove();
            });

            $('#clearDescriptionRows').on('click', function () {
                $('#descriptionRowsTable tbody').find('tr:not(:first)').remove();
                $('#descriptionRowsTable tbody').find('tr:first input[type="text"]:not(:disabled), tr:first textarea').val('');
                rowIndex = 0;
            });

            $('#scrollToCreateForm').on('click', function () {
                const target = document.getElementById('createDescriptionCard');

                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });

            @if(Session::has('update_msg'))
                toastr.success("{{ session('update_msg') }}");
            @endif

            @if(Session::has('updated_msg'))
                toastr.success("{{ session('updated_msg') }}");
            @endif

            @if(Session::has('save_msg'))
                toastr.success("{{ session('save_msg') }}");
            @endif

            @if(Session::has('delete_msg'))
                toastr.error("{{ session('delete_msg') }}");
            @endif

            @if($errors->any())
                toastr.error("Bitte prüfen Sie die Eingaben.");
            @endif

            if (window.feather) {
                feather.replace();
            }
        });
    </script>
@endsection