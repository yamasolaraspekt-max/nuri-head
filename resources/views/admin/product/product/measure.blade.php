@extends('admin.layouts.app')
@section('title', 'Einheiten')

@section('style')
    <style>
        :root {
            --mu-bg: #f3f4f6;
            --mu-card: #ffffff;
            --mu-text: #1f2937;
            --mu-muted: #6b7280;
            --mu-border: #e5e7eb;
            --mu-primary: #8fc73e;
            --mu-primary-hover: #7baa18;
            --mu-primary-light: #f4fae7;
            --mu-blue: #74b2d4;
            --mu-blue-light: #eff6ff;
            --mu-danger: #ef4444;
            --mu-danger-light: #fef2f2;
            --mu-success: #10b981;
            --mu-success-light: #ecfdf5;
            --mu-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --mu-shadow: 0 12px 32px rgba(15, 23, 42, .12);
            --mu-radius: 16px;
            --mu-transition: all .18s ease;
        }

        .mu-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--mu-text);
            margin: 20px auto;
            padding-right: 79px;
        }

        .mu-header {
            margin-bottom: 18px;
        }

        .mu-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .mu-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -.025em;
            color: #111827;
            text-transform: uppercase;
        }

        .mu-subtitle {
            margin-top: 4px;
            font-size: 14px;
            color: var(--mu-muted);
            font-weight: 600;
        }

        .mu-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--mu-muted);
        }

        .mu-breadcrumb a {
            color: var(--mu-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .mu-breadcrumb a:hover {
            color: #111827;
        }

        .mu-breadcrumb .current {
            color: #111827;
            font-weight: 900;
        }

        .mu-card {
            background: var(--mu-card);
            border: 1px solid var(--mu-border);
            border-radius: 18px;
            box-shadow: var(--mu-shadow-sm);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .mu-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--mu-border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .mu-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .mu-card-subtitle {
            margin-top: 4px;
            font-size: 12px;
            color: var(--mu-muted);
            font-weight: 700;
        }

        .mu-card-body {
            padding: 18px;
        }

        .mu-toolbar {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) auto;
            gap: 12px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .mu-toolbar {
                grid-template-columns: 1fr;
            }
        }

        .mu-form-group {
            min-width: 0;
        }

        .mu-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: var(--mu-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .mu-input {
            width: 100%;
            min-height: 42px;
            border: 1px solid var(--mu-border);
            border-radius: 10px;
            background: #fff;
            color: #111827;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: var(--mu-transition);
        }

        .mu-input:focus {
            border-color: var(--mu-primary);
            box-shadow: 0 0 0 3px rgba(143, 199, 62, .14);
        }

        .mu-btn,
        .mu-btn-soft,
        .mu-btn-blue,
        .mu-btn-danger,
        .mu-btn-success {
            border: 0;
            border-radius: 10px;
            padding: 10px 15px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            text-decoration: none;
            transition: var(--mu-transition);
            white-space: nowrap;
            min-height: 42px;
        }

        .mu-btn {
            background: var(--mu-primary);
            color: #fff;
        }

        .mu-btn:hover {
            background: var(--mu-primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .mu-btn-blue {
            background: var(--mu-blue);
            color: #fff;
        }

        .mu-btn-blue:hover {
            background: #559fc7;
            color: #fff;
            text-decoration: none;
        }

        .mu-btn-success {
            background: var(--mu-success);
            color: #fff;
        }

        .mu-btn-success:hover {
            background: #059669;
            color: #fff;
            text-decoration: none;
        }

        .mu-btn-danger {
            background: var(--mu-danger);
            color: #fff;
        }

        .mu-btn-danger:hover {
            background: #dc2626;
            color: #fff;
            text-decoration: none;
        }

        .mu-btn-soft {
            background: #fff;
            color: var(--mu-text);
            border: 1px solid var(--mu-border);
        }

        .mu-btn-soft:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .mu-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--mu-border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--mu-transition);
            text-decoration: none;
        }

        .mu-icon-btn.edit {
            background: var(--mu-blue-light);
            color: #075985;
            border-color: #c0d8ea;
        }

        .mu-icon-btn.delete {
            background: var(--mu-danger-light);
            color: var(--mu-danger);
            border-color: #fecaca;
        }

        .mu-icon-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .mu-table-wrap {
            overflow-x: auto;
        }

        .mu-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 720px;
        }

        .mu-table thead th {
            padding: 8px 12px;
            font-size: 11px;
            font-weight: 900;
            color: var(--mu-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            border: 0;
            white-space: nowrap;
        }

        .mu-table tbody td {
            padding: 13px 12px;
            border-top: 1px solid var(--mu-border);
            border-bottom: 1px solid var(--mu-border);
            background: #fff;
            vertical-align: middle;
        }

        .mu-table tbody td:first-child {
            border-left: 1px solid var(--mu-border);
            border-radius: 14px 0 0 14px;
            width: 100px;
            font-weight: 900;
            color: #111827;
        }

        .mu-table tbody td:last-child {
            border-right: 1px solid var(--mu-border);
            border-radius: 0 14px 14px 0;
            width: 160px;
        }

        .mu-table tbody tr {
            box-shadow: var(--mu-shadow-sm);
        }

        .mu-unit-name {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
        }

        .mu-action-row {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .mu-empty {
            padding: 34px 18px !important;
            text-align: center;
            color: var(--mu-muted);
            font-weight: 800;
            border: 1px dashed var(--mu-border) !important;
            border-radius: 16px !important;
            background: #fafafa !important;
        }

        .mu-pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .mu-error-box {
            border: 1px solid #fecaca;
            background: var(--mu-danger-light);
            color: #991b1b;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .mu-error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .mu-field-error {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            font-weight: 800;
            color: var(--mu-danger);
        }

        /* =========================================================
           CUSTOM MODAL - NO BOOTSTRAP MODAL
        ========================================================= */
        .mu-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, .58);
        }

        .mu-modal-backdrop.is-open {
            display: flex;
        }

        .mu-modal {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid var(--mu-border);
            border-radius: 20px;
            box-shadow: var(--mu-shadow);
            overflow: hidden;
            transform: translateY(10px) scale(.98);
            opacity: 0;
            transition: var(--mu-transition);
        }

        .mu-modal-backdrop.is-open .mu-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .mu-modal-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--mu-border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .mu-modal-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .mu-modal-subtitle {
            margin-top: 4px;
            font-size: 12px;
            color: var(--mu-muted);
            font-weight: 700;
        }

        .mu-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--mu-border);
            background: #fff;
            color: var(--mu-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .mu-modal-close:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .mu-modal-body {
            padding: 18px;
        }

        .mu-modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--mu-border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mu-delete-warning {
            border: 1px solid #fecaca;
            background: var(--mu-danger-light);
            color: #991b1b;
            border-radius: 14px;
            padding: 14px;
            font-weight: 800;
            line-height: 1.55;
        }

        @media (max-width: 768px) {
            .mu-wrap {
                padding: 18px;
                margin: 0;
            }

            .mu-header {
                margin-top: 70px;
            }

            .mu-title {
                font-size: 21px;
            }

            .mu-card-body {
                padding: 14px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="mu-wrap">
        <div class="mu-header">
            <div class="mu-titlebar">
                <div>
                    <div class="mu-title">Einheiten</div>
                    <div class="mu-subtitle">
                        Maßeinheiten für Produkte verwalten.
                    </div>

                    <div class="mu-breadcrumb">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <span>›</span>
                        <span class="current">Einheiten</span>
                    </div>
                </div>

                <button type="button" class="mu-btn" data-open-modal="createMeasureModal">
                    <i class="feather icon-plus"></i>
                    Neue Einheit
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="mu-error-box">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mu-card">
            <div class="mu-card-header">
                <div>
                    <h3 class="mu-card-title">Filter</h3>
                    <div class="mu-card-subtitle">Nach Einheit suchen</div>
                </div>
            </div>

            <div class="mu-card-body">
                <form action="{{ route('measure.info') }}" method="GET" class="mu-toolbar">
                    <div class="mu-form-group">
                        <label class="mu-label" for="search">Suche</label>
                        <input
                            type="text"
                            name="search"
                            id="search"
                            class="mu-input"
                            value="{{ request('search') }}"
                            placeholder="Einheit suchen..."
                        >
                    </div>

                    <div class="d-flex" style="gap:8px; flex-wrap:wrap;">
                        <button type="submit" class="mu-btn-blue">
                            <i class="feather icon-search"></i>
                            Suchen
                        </button>

                        @if(request('search'))
                            <a href="{{ route('measure.info') }}" class="mu-btn-soft">
                                <i class="feather icon-x"></i>
                                Zurücksetzen
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="mu-card">
            <div class="mu-card-header">
                <div>
                    <h3 class="mu-card-title">Einheitenliste</h3>
                    <div class="mu-card-subtitle">
                        {{ method_exists($data, 'total') ? $data->total() : $data->count() }} Datensätze gefunden
                    </div>
                </div>
            </div>

            <div class="mu-card-body">
                <div class="mu-table-wrap">
                    <table class="mu-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Einheit</th>
                                <th class="text-right">Aktion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($data as $item)
                                <tr>
                                    <td>#{{ $item->id }}</td>
                                    <td>
                                        <div class="mu-unit-name">{{ $item->measure }}</div>
                                    </td>
                                    <td>
                                        <div class="mu-action-row">
                                            <button
                                                type="button"
                                                class="mu-icon-btn edit"
                                                title="Bearbeiten"
                                                data-open-modal="editMeasureModal{{ $item->id }}"
                                            >
                                                <i class="feather icon-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="mu-icon-btn delete"
                                                title="Löschen"
                                                data-open-modal="deleteMeasureModal{{ $item->id }}"
                                            >
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="mu-empty">
                                        <i class="feather icon-box"></i>
                                        Keine Einheiten gefunden.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mu-pagination">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div class="mu-modal-backdrop" id="createMeasureModal" aria-hidden="true">
        <div class="mu-modal" role="dialog" aria-modal="true" aria-labelledby="createMeasureTitle">
            <form method="POST" action="{{ route('measure.store') }}">
                @csrf

                <div class="mu-modal-header">
                    <div>
                        <h3 class="mu-modal-title" id="createMeasureTitle">Neue Einheit</h3>
                        <div class="mu-modal-subtitle">Neue Maßeinheit hinzufügen</div>
                    </div>

                    <button type="button" class="mu-modal-close" data-close-modal="createMeasureModal">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="mu-modal-body">
                    <div class="mu-form-group">
                        <label class="mu-label" for="measure_create">Einheit</label>
                        <input
                            type="text"
                            class="mu-input"
                            id="measure_create"
                            name="measure"
                            value="{{ old('measure') }}"
                            placeholder="z. B. Stück, m², Meter, Paket"
                            required
                        >

                        @error('measure')
                            <span class="mu-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mu-modal-footer">
                    <button type="button" class="mu-btn-soft" data-close-modal="createMeasureModal">
                        Abbrechen
                    </button>

                    <button type="submit" class="mu-btn-success">
                        <i class="feather icon-save"></i>
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT + DELETE MODALS --}}
    @foreach($data as $item)
        <div class="mu-modal-backdrop" id="editMeasureModal{{ $item->id }}" aria-hidden="true">
            <div class="mu-modal" role="dialog" aria-modal="true" aria-labelledby="editMeasureTitle{{ $item->id }}">
                <form method="POST" action="{{ route('measure.update') }}">
                    @csrf

                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="mu-modal-header">
                        <div>
                            <h3 class="mu-modal-title" id="editMeasureTitle{{ $item->id }}">Einheit bearbeiten</h3>
                            <div class="mu-modal-subtitle">Datensatz #{{ $item->id }} aktualisieren</div>
                        </div>

                        <button type="button" class="mu-modal-close" data-close-modal="editMeasureModal{{ $item->id }}">
                            <i class="feather icon-x"></i>
                        </button>
                    </div>

                    <div class="mu-modal-body">
                        <div class="mu-form-group">
                            <label class="mu-label" for="measure_edit_{{ $item->id }}">Einheit</label>
                            <input
                                type="text"
                                class="mu-input"
                                id="measure_edit_{{ $item->id }}"
                                name="measure"
                                value="{{ old('measure', $item->measure) }}"
                                required
                            >

                            @error('measure')
                                <span class="mu-field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mu-modal-footer">
                        <button type="button" class="mu-btn-soft" data-close-modal="editMeasureModal{{ $item->id }}">
                            Abbrechen
                        </button>

                        <button type="submit" class="mu-btn-success">
                            <i class="feather icon-save"></i>
                            Aktualisieren
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mu-modal-backdrop" id="deleteMeasureModal{{ $item->id }}" aria-hidden="true">
            <div class="mu-modal" role="dialog" aria-modal="true" aria-labelledby="deleteMeasureTitle{{ $item->id }}">
                <div class="mu-modal-header">
                    <div>
                        <h3 class="mu-modal-title" id="deleteMeasureTitle{{ $item->id }}">Einheit löschen</h3>
                        <div class="mu-modal-subtitle">Diese Aktion kann nicht rückgängig gemacht werden</div>
                    </div>

                    <button type="button" class="mu-modal-close" data-close-modal="deleteMeasureModal{{ $item->id }}">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="mu-modal-body">
                    <div class="mu-delete-warning">
                        Möchten Sie die Einheit <strong>{{ $item->measure }}</strong> wirklich löschen?
                        <br>
                        Datensatznummer: <strong>#{{ $item->id }}</strong>
                    </div>
                </div>

                <div class="mu-modal-footer">
                    <button type="button" class="mu-btn-soft" data-close-modal="deleteMeasureModal{{ $item->id }}">
                        Abbrechen
                    </button>

                    <a href="{{ route('measure.destroy', ['id' => $item->id]) }}" class="mu-btn-danger">
                        <i class="feather icon-trash-2"></i>
                        Ja, löschen
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('script')
    <script>
    (function () {
        'use strict';

        function refreshIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            setTimeout(function () {
                const input = modal.querySelector('input:not([type="hidden"]), button, a');
                if (input) input.focus();
            }, 80);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeAllModals() {
            document.querySelectorAll('.mu-modal-backdrop.is-open').forEach(function (modal) {
                closeModal(modal.id);
            });
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
                closeModal(closeBtn.dataset.closeModal);
                return;
            }

            if (event.target.classList.contains('mu-modal-backdrop')) {
                closeModal(event.target.id);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllModals();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if(Session::has('update_msg'))
                if (window.toastr) toastr.success(@json(session('update_msg')));
            @endif

            @if(Session::has('save_msg'))
                if (window.toastr) toastr.success(@json(session('save_msg')));
            @endif

            @if(Session::has('delete_msg'))
                if (window.toastr) toastr.error(@json(session('delete_msg')));
            @endif

            @if($errors->any())
                openModal('createMeasureModal');
            @endif

            refreshIcons();
        });
    })();
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
                label: 'Einheiten',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush