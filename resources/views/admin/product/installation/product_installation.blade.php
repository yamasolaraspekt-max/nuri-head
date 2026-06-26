@extends('admin.layouts.app')
@section('title', 'Produktinstallation')

@php
    $productId = request()->route('id') ?? request()->id ?? optional($data->first())->productid;

    $productName = optional($data->first())->product;

    if (!$productName && $productId) {
        $productName = \Illuminate\Support\Facades\DB::table('products')
            ->where('id', $productId)
            ->value('product');
    }

    $caseOptions = [
        'Einfach',
        'Normal',
        'Schwer',
        'Anspruchsvoll',
        'Kompliziert',
    ];

    $searchValue = request('search');
@endphp

@section('content')
    <div class="app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
             

            <style>
                .pi-page {
                    --pi-primary: #8fc73e;
                    --pi-primary-dark: #79aa31;
                    --pi-blue: #74b2d4;
                    --pi-bg: #f8fafc;
                    --pi-border: #e5e7eb;
                    --pi-text: #111827;
                    --pi-muted: #6b7280;
                    --pi-danger: #ef4444;
                    --pi-warning: #f59e0b;
                    --pi-success: #10b981;
                }

                .pi-hero {
                    background: linear-gradient(135deg, #ffffff 0%, #f2fae8 100%);
                    border: 1px solid var(--pi-border);
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

                .pi-title {
                    font-size: 22px;
                    font-weight: 900;
                    color: var(--pi-text);
                    margin: 0;
                    letter-spacing: -.02em;
                }

                .pi-subtitle {
                    color: var(--pi-muted);
                    font-size: 13px;
                    margin-top: 5px;
                }

                .pi-product-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 7px;
                    background: #eff6ff;
                    color: #1d4ed8;
                    border-radius: 999px;
                    padding: 7px 12px;
                    font-size: 12px;
                    font-weight: 900;
                    margin-top: 9px;
                }

                .pi-actions {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    flex-wrap: wrap;
                }

                .pi-btn {
                    border: none;
                    border-radius: 12px;
                    padding: 10px 15px;
                    font-weight: 900;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    transition: all .18s ease;
                    text-decoration: none !important;
                    cursor: pointer;
                }

                .pi-btn-primary {
                    background: var(--pi-primary);
                    color: #fff !important;
                }

                .pi-btn-primary:hover {
                    background: var(--pi-primary-dark);
                    color: #fff !important;
                }

                .pi-btn-light {
                    background: #fff;
                    color: var(--pi-text) !important;
                    border: 1px solid var(--pi-border);
                }

                .pi-btn-light:hover {
                    background: #f9fafb;
                    color: var(--pi-text) !important;
                }

                .pi-btn-danger {
                    background: #fef2f2;
                    color: #b91c1c !important;
                    border: 1px solid rgba(239, 68, 68, .2);
                }

                .pi-btn-danger:hover {
                    background: #fee2e2;
                    color: #991b1b !important;
                }

                .pi-toolbar {
                    background: #fff;
                    border: 1px solid var(--pi-border);
                    border-radius: 16px;
                    padding: 14px;
                    margin-bottom: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    flex-wrap: wrap;
                }

                .pi-search {
                    flex: 1;
                    min-width: 260px;
                    display: flex;
                    gap: 8px;
                }

                .pi-search input {
                    border: 1px solid var(--pi-border);
                    border-radius: 12px;
                    padding: 10px 12px;
                    min-height: 42px;
                    width: 100%;
                    outline: none;
                    transition: all .18s ease;
                }

                .pi-search input:focus {
                    border-color: var(--pi-primary);
                    box-shadow: 0 0 0 3px #f2fae8;
                }

                .pi-card {
                    border: 1px solid var(--pi-border);
                    border-radius: 18px;
                    background: #fff;
                    overflow: hidden;
                    box-shadow: 0 10px 25px -20px rgba(15, 23, 42, .35);
                }

                .pi-card-header {
                    padding: 16px 18px;
                    border-bottom: 1px solid var(--pi-border);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    flex-wrap: wrap;
                    background: #fcfcfd;
                }

                .pi-card-title {
                    font-size: 15px;
                    font-weight: 900;
                    color: var(--pi-text);
                    margin: 0;
                }

                .pi-count {
                    background: #f3f4f6;
                    color: #374151;
                    border-radius: 999px;
                    padding: 5px 10px;
                    font-size: 12px;
                    font-weight: 900;
                }

                .pi-table {
                    margin: 0;
                }

                .pi-table thead th {
                    background: #f9fafb;
                    color: #6b7280;
                    font-size: 11px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: .06em;
                    border-bottom: 1px solid var(--pi-border);
                    white-space: nowrap;
                }

                .pi-table tbody td,
                .pi-table tbody th {
                    vertical-align: middle;
                    border-top: 1px solid #f3f4f6;
                }

                .pi-id {
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

                .pi-case {
                    display: inline-flex;
                    align-items: center;
                    border-radius: 999px;
                    padding: 6px 10px;
                    font-size: 12px;
                    font-weight: 900;
                    background: #f3f4f6;
                    color: #374151;
                }

                .pi-rate {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 34px;
                    height: 34px;
                    border-radius: 999px;
                    background: #ecfdf5;
                    color: #047857;
                    font-weight: 900;
                }

                .pi-desc {
                    max-width: 520px;
                    color: #4b5563;
                    font-size: 13px;
                    line-height: 1.45;
                    white-space: normal;
                }

                .pi-icon-btn {
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

                .pi-icon-edit {
                    background: #eff6ff;
                    color: #1d4ed8;
                }

                .pi-icon-edit:hover {
                    background: #dbeafe;
                    color: #1e40af;
                }

                .pi-icon-delete {
                    background: #fef2f2;
                    color: #b91c1c;
                }

                .pi-icon-delete:hover {
                    background: #fee2e2;
                    color: #991b1b;
                }

                .pi-empty {
                    padding: 45px 20px;
                    text-align: center;
                    color: var(--pi-muted);
                    background: #fff;
                }

                .pi-modal-label {
                    font-weight: 800;
                    color: #374151;
                    font-size: 13px;
                }

                .pi-help {
                    color: #6b7280;
                    font-size: 12px;
                    margin-top: 4px;
                }

                .pi-pagination {
                    margin-top: 16px;
                }

                .pi-pagination .pagination {
                    justify-content: flex-end;
                    flex-wrap: wrap;
                    gap: 5px;
                }

                .pi-pagination .page-link {
                    border-radius: 10px !important;
                    border: 1px solid var(--pi-border);
                    color: #374151;
                }

                .pi-pagination .page-item.active .page-link {
                    background: var(--pi-primary);
                    border-color: var(--pi-primary);
                    color: #fff;
                }

                @media(max-width: 768px) {
                    .pi-hero,
                    .pi-toolbar,
                    .pi-actions,
                    .pi-search {
                        flex-direction: column;
                        align-items: stretch;
                    }

                    .pi-actions .pi-btn,
                    .pi-search .pi-btn {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>

            <div class="content-body pi-page">
                <div class="pi-hero">
                    <div>
                        <h3 class="pi-title">Produktinstallation</h3>

                        <div class="pi-subtitle">
                            Installationsfälle, Schwierigkeitsgrad und Bewertung für dieses Produkt verwalten.
                        </div>

                        <div class="pi-product-badge">
                            <i class="feather icon-box"></i>
                            {{ $productName ?: 'Produkt #' . $productId }}
                        </div>
                    </div>

                    <div class="pi-actions">
                        @if($productId)
                            <a href="{{ url('product_details/' . $productId) }}" class="pi-btn pi-btn-light">
                                <i class="feather icon-arrow-left"></i>
                                Zurück zu Produktdetails
                            </a>
                        @endif

                        <button type="button" class="pi-btn pi-btn-primary" data-toggle="modal" data-target="#createInstallationModal">
                            <i class="feather icon-plus"></i>
                            Installation erstellen
                        </button>
                    </div>
                </div>

                <div class="pi-toolbar">
                    <form action="{{ route('product.installation', $productId) }}" method="GET" class="pi-search">
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchValue }}"
                            placeholder="Suchen nach Fall oder Beschreibung..."
                        >

                        <button class="pi-btn pi-btn-primary" type="submit">
                            <i class="feather icon-search"></i>
                            Suchen
                        </button>

                        @if($searchValue)
                            <a href="{{ route('product.installation', $productId) }}" class="pi-btn pi-btn-light">
                                <i class="feather icon-x"></i>
                                Zurücksetzen
                            </a>
                        @endif
                    </form>
                </div>

                <div class="pi-card">
                    <div class="pi-card-header">
                        <h4 class="pi-card-title">
                            Installationsfälle
                        </h4>

                        <span class="pi-count">
                            {{ $data->total() }} Eintrag(e)
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table pi-table">
                            <thead>
                                <tr>
                                    <th style="width:90px;">ID</th>
                                    <th>Produkt</th>
                                    <th>Fall</th>
                                    <th>Beschreibung</th>
                                    <th style="width:120px;">Bewertung</th>
                                    <th style="width:130px;" class="text-right">Aktion</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                    @php
                                        $isDefaultCase = in_array($item->case, $caseOptions, true);
                                        $editSelectedCase = $isDefaultCase ? $item->case : 'Benutzerdefiniert';
                                        $editCustomCase = $isDefaultCase ? '' : $item->case;
                                    @endphp

                                    <tr>
                                        <th>
                                            <span class="pi-id">#{{ $item->id }}</span>
                                        </th>

                                        <td>
                                            <strong>{{ $item->product }}</strong>
                                        </td>

                                        <td>
                                            <span class="pi-case">{{ $item->case }}</span>
                                        </td>

                                        <td>
                                            <div class="pi-desc">
                                                {{ $item->description }}
                                            </div>
                                        </td>

                                        <td>
                                            <span class="pi-rate">{{ $item->rate }}</span>
                                        </td>

                                        <td class="text-right">
                                            <button
                                                type="button"
                                                class="pi-icon-btn pi-icon-edit"
                                                data-toggle="modal"
                                                data-target="#editInstallationModal{{ $item->id }}"
                                                title="Bearbeiten"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="pi-icon-btn pi-icon-delete"
                                                data-toggle="modal"
                                                data-target="#deleteInstallationModal{{ $item->id }}"
                                                title="Löschen"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade text-left" id="editInstallationModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('product.installation.update') }}">
                                                    @csrf

                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Installationsfall bearbeiten</h4>

                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $productId }}">

                                                        <div class="form-group">
                                                            <label class="pi-modal-label">Produkt</label>
                                                            <input type="text" disabled class="form-control" value="{{ $productName ?: $item->product }}">
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="pi-modal-label">Schwierigkeitsgrad</label>

                                                            <select class="form-control js-case-select" name="case">
                                                                @foreach($caseOptions as $option)
                                                                    <option value="{{ $option }}" @selected($editSelectedCase === $option)>
                                                                        {{ $option }}
                                                                    </option>
                                                                @endforeach

                                                                <option value="Benutzerdefiniert" @selected($editSelectedCase === 'Benutzerdefiniert')>
                                                                    Benutzerdefiniert
                                                                </option>
                                                            </select>

                                                            @error('case')
                                                                <p class="text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group js-custom-case-wrap" style="{{ $editSelectedCase === 'Benutzerdefiniert' ? '' : 'display:none;' }}">
                                                            <label class="pi-modal-label">Benutzerdefinierter Fall</label>

                                                            <input
                                                                type="text"
                                                                class="form-control js-custom-case"
                                                                name="custom"
                                                                value="{{ old('custom', $editCustomCase) }}"
                                                                placeholder="sonstiges"
                                                            >

                                                            <div class="pi-help">
                                                                Wird nur gespeichert, wenn Schwierigkeitsgrad „Benutzerdefiniert“ gewählt ist.
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="pi-modal-label">Beschreibung</label>

                                                            <textarea class="form-control" name="description" rows="4" required>{{ old('description', $item->description) }}</textarea>

                                                            @error('description')
                                                                <p class="text-danger mt-1">{{ $message }}</p>
                                                            @enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="pi-modal-label">Bewertung</label>

                                                            <select class="form-control" name="rate">
                                                                @for($rate = 1; $rate <= 6; $rate++)
                                                                    <option value="{{ $rate }}" @selected((string) $item->rate === (string) $rate)>
                                                                        {{ $rate }}
                                                                    </option>
                                                                @endfor
                                                            </select>

                                                            @error('rate')
                                                                <p class="text-danger mt-1">{{ $message }}</p>
                                                            @enderror
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

                                    {{-- Delete Modal --}}
                                    <div class="modal fade text-left" id="deleteInstallationModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Datensatz löschen</h4>

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <p class="mb-1">
                                                        Möchten Sie diesen Installationsfall wirklich löschen?
                                                    </p>

                                                    <p class="mb-0 text-muted">
                                                        Datensatznummer: <strong>#{{ $item->id }}</strong>
                                                    </p>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                                        Abbrechen
                                                    </button>

                                                    <a href="{{ route('product.installation.destroy', $item->id) }}" class="btn btn-danger">
                                                        Ja, löschen
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="pi-empty">
                                                <i class="feather icon-info mb-1" style="width:32px;height:32px;"></i>
                                                <div>Keine Installationsfälle gefunden.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($data->hasPages())
                    <div class="pi-pagination">
                        {{ $data->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade text-left" id="createInstallationModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('product.installation.save') }}">
                    @csrf

                    <div class="modal-header">
                        <h4 class="modal-title">Neuen Installationsfall erstellen</h4>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" value="{{ $productId }}">

                        <div class="form-group">
                            <label class="pi-modal-label">Produkt</label>
                            <input type="text" disabled class="form-control" value="{{ $productName ?: 'Produkt #' . $productId }}">
                        </div>

                        <div class="form-group">
                            <label class="pi-modal-label">Schwierigkeitsgrad</label>

                            <select class="form-control js-case-select" name="case">
                                @foreach($caseOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach

                                <option value="Benutzerdefiniert">Benutzerdefiniert</option>
                            </select>

                            @error('case')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group js-custom-case-wrap" style="display:none;">
                            <label class="pi-modal-label">Benutzerdefinierter Fall</label>

                            <input
                                type="text"
                                class="form-control js-custom-case"
                                name="custom"
                                value="{{ old('custom') }}"
                                placeholder="sonstiges"
                            >

                            <div class="pi-help">
                                Wird nur gespeichert, wenn Schwierigkeitsgrad „Benutzerdefiniert“ gewählt ist.
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="pi-modal-label">Beschreibung</label>

                            <textarea class="form-control" name="description" rows="4" required>{{ old('description') }}</textarea>

                            @error('description')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="pi-modal-label">Bewertung</label>

                            <select class="form-control" name="rate">
                                @for($rate = 1; $rate <= 6; $rate++)
                                    <option value="{{ $rate }}">{{ $rate }}</option>
                                @endfor
                            </select>

                            @error('rate')
                                <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                            Abbrechen
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Einreichen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        $(document).ready(function () {
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

            $(document).on('change', '.js-case-select', function () {
                const $form = $(this).closest('form');
                const $customWrap = $form.find('.js-custom-case-wrap');
                const $customInput = $form.find('.js-custom-case');

                if ($(this).val() === 'Benutzerdefiniert') {
                    $customWrap.slideDown(150);
                } else {
                    $customWrap.slideUp(150);
                    $customInput.val('');
                }
            });

            $('.js-case-select').trigger('change');

            if (window.feather) {
                feather.replace();
            }
        });
    </script>
@endsection