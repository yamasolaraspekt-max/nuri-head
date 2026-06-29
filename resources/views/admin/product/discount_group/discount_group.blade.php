@extends('admin.layouts.app')
@section('title', 'Rabattgruppen')

@section('style')
    <style>
        :root {
            --dg-bg: #f3f4f6;
            --dg-card: #ffffff;
            --dg-text: #1f2937;
            --dg-muted: #6b7280;
            --dg-border: #e5e7eb;
            --dg-primary: #8fc73e;
            --dg-primary-hover: #7baa18;
            --dg-primary-light: #f4fae7;
            --dg-blue: #74b2d4;
            --dg-blue-light: #eff6ff;
            --dg-danger: #ef4444;
            --dg-danger-light: #fef2f2;
            --dg-success: #10b981;
            --dg-success-light: #ecfdf5;
            --dg-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --dg-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --dg-radius: 16px;
            --dg-transition: all .2s ease-in-out;
        }

        .dg-wrap {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--dg-text); 
        }

        .dg-header {
            margin-bottom: 18px;
        }

        .dg-titlebar {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dg-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -.025em;
            color: #111827;
            text-transform: uppercase;
        }

        .dg-sub {
            font-size: 14px;
            color: var(--dg-muted);
            margin-top: 4px;
        }

        .dg-breadcrumb {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--dg-muted);
        }

        .dg-breadcrumb a {
            color: var(--dg-muted);
            text-decoration: none;
            font-weight: 800;
        }

        .dg-breadcrumb a:hover {
            color: #111827;
        }

        .dg-breadcrumb span.current {
            color: #111827;
            font-weight: 900;
        }

        .dg-btn,
        .dg-btn-soft,
        .dg-btn-blue,
        .dg-btn-danger,
        .dg-btn-success {
            border: 0;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--dg-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            line-height: 1.2;
            white-space: nowrap;
        }

        .dg-btn {
            background: var(--dg-primary);
            color: #fff;
        }

        .dg-btn:hover {
            background: var(--dg-primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .dg-btn-blue {
            background: var(--dg-blue);
            color: #fff;
        }

        .dg-btn-blue:hover {
            background: #559fc7;
            color: #fff;
            text-decoration: none;
        }

        .dg-btn-success {
            background: var(--dg-success);
            color: #fff;
        }

        .dg-btn-success:hover {
            background: #059669;
            color: #fff;
            text-decoration: none;
        }

        .dg-btn-danger {
            background: var(--dg-danger);
            color: #fff;
        }

        .dg-btn-danger:hover {
            background: #dc2626;
            color: #fff;
            text-decoration: none;
        }

        .dg-btn-soft {
            background: #fff;
            color: var(--dg-text);
            border: 1px solid var(--dg-border);
        }

        .dg-btn-soft:hover {
            background: #f9fafb;
            color: var(--dg-text);
            text-decoration: none;
        }

        .dg-card {
            background: #fff;
            border: 1px solid var(--dg-border);
            border-radius: 18px;
            box-shadow: var(--dg-shadow-sm);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .dg-card-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--dg-border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dg-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .dg-card-sub {
            font-size: 12px;
            color: var(--dg-muted);
            margin-top: 4px;
            font-weight: 700;
        }

        .dg-card-body {
            padding: 18px;
        }

        .dg-toolbar {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 18px;
        }

        @media(max-width: 768px) {
            .dg-toolbar {
                grid-template-columns: 1fr;
            }
        }

        .dg-form-group {
            min-width: 0;
        }

        .dg-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: var(--dg-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 7px;
        }

        .dg-input {
            width: 100%;
            padding: 11px 12px;
            border-radius: 10px;
            border: 1px solid var(--dg-border);
            background: #fff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition: var(--dg-transition);
            min-height: 42px;
        }

        .dg-input:focus {
            border-color: var(--dg-primary);
            box-shadow: 0 0 0 3px var(--dg-primary-light);
        }

        .dg-error {
            display: block;
            color: var(--dg-danger);
            font-size: 12px;
            font-weight: 800;
            margin-top: 5px;
        }

        .dg-error-box {
            border: 1px solid #fecaca;
            background: var(--dg-danger-light);
            color: #991b1b;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .dg-error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .dg-table-wrap {
            overflow-x: auto;
        }

        .dg-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            min-width: 760px;
        }

        .dg-table thead th {
            font-size: 11px;
            font-weight: 900;
            color: var(--dg-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 8px 10px;
            border: 0;
            white-space: nowrap;
            text-align: left;
        }

        .dg-table tbody tr {
            background: #fff;
            box-shadow: var(--dg-shadow-sm);
        }

        .dg-table tbody td,
        .dg-table tbody th {
            padding: 13px 10px;
            border-top: 1px solid var(--dg-border);
            border-bottom: 1px solid var(--dg-border);
            vertical-align: middle;
            background: #fff;
        }

        .dg-table tbody td:first-child,
        .dg-table tbody th:first-child {
            border-left: 1px solid var(--dg-border);
            border-radius: 14px 0 0 14px;
        }

        .dg-table tbody td:last-child {
            border-right: 1px solid var(--dg-border);
            border-radius: 0 14px 14px 0;
        }

        .dg-id {
            font-weight: 900;
            color: #111827;
            width: 80px;
        }

        .dg-name {
            font-weight: 900;
            color: #111827;
        }

        .dg-percent {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: var(--dg-primary-light);
            color: #365314;
            font-size: 12px;
            font-weight: 900;
            min-width: 66px;
        }

        .dg-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .dg-icon-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--dg-border);
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--dg-transition);
            color: var(--dg-muted);
            text-decoration: none;
        }

        .dg-icon-btn:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .dg-icon-btn.edit {
            background: var(--dg-blue-light);
            color: #075985;
            border-color: #c0d8ea;
        }

        .dg-icon-btn.delete {
            background: var(--dg-danger-light);
            color: var(--dg-danger);
            border-color: #fecaca;
        }

        .dg-empty {
            padding: 36px 16px !important;
            text-align: center;
            color: var(--dg-muted);
            font-weight: 800;
        }

        .dg-pagination {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
        }

        /* custom modal */
        .dg-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(15, 23, 42, .56);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .dg-modal-backdrop.is-open {
            display: flex;
        }

        .dg-modal {
            width: 100%;
            max-width: 540px;
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--dg-border);
            box-shadow: var(--dg-shadow);
            overflow: hidden;
            transform: translateY(10px) scale(.98);
            opacity: 0;
            transition: var(--dg-transition);
        }

        .dg-modal-backdrop.is-open .dg-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        .dg-modal-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--dg-border);
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .dg-modal-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111827;
            text-transform: uppercase;
        }

        .dg-modal-sub {
            font-size: 12px;
            color: var(--dg-muted);
            margin-top: 4px;
            font-weight: 700;
        }

        .dg-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--dg-border);
            background: #fff;
            cursor: pointer;
            color: var(--dg-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dg-modal-close:hover {
            background: #f9fafb;
            color: #111827;
        }

        .dg-modal-body {
            padding: 18px;
        }

        .dg-modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--dg-border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dg-delete-box {
            border: 1px solid #fecaca;
            background: var(--dg-danger-light);
            color: #991b1b;
            border-radius: 14px;
            padding: 14px;
            font-weight: 800;
            line-height: 1.55;
        }

        @media(max-width: 768px) {
            .dg-wrap {
                padding: 18px;
                margin: 0;
            }

            .dg-header {
                margin-top: 70px;
            }

            .dg-title {
                font-size: 21px;
            }

            .dg-card-body {
                padding: 14px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>

        <div class="content-wrapper">
            <div class="content-body">
                <div class="dg-wrap">
                    <div class="dg-header">
                        <div class="dg-titlebar">
                            <div>
                                <div class="dg-title">Rabattgruppen</div>
                                <div class="dg-sub">
                                    Rabattgruppen und prozentuale Rabatte verwalten.
                                </div>

                                <div class="dg-breadcrumb">
                                    <a href="{{ url('/') }}">Dashboard</a>
                                    <span>›</span>
                                    <span class="current">Rabattgruppen</span>
                                </div>
                            </div>

                            <button type="button" class="dg-btn" data-open-modal="createDiscountModal">
                                <i class="feather icon-plus"></i>
                                Neue Rabattgruppe
                            </button>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="dg-error-box">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="dg-card">
                        <div class="dg-card-header">
                            <div>
                                <h3 class="dg-card-title">Filter & Übersicht</h3>
                                <div class="dg-card-sub">Rabattgruppe nach Titel oder Rabatt suchen</div>
                            </div>
                        </div>

                        <div class="dg-card-body">
                            <form action="{{ route('discount_group.info') }}" method="GET" class="dg-toolbar">
                                <div class="dg-form-group">
                                    <label class="dg-label" for="search">Suche</label>
                                    <input
                                        type="text"
                                        name="search"
                                        id="search"
                                        class="dg-input"
                                        placeholder="Rabattgruppe oder Rabatt suchen..."
                                        value="{{ request('search') }}"
                                    >
                                </div>

                                <div class="dg-form-group">
                                    <button class="dg-btn-blue" type="submit">
                                        <i class="feather icon-search"></i>
                                        Suchen
                                    </button>

                                    @if(request('search'))
                                        <a href="{{ route('discount_group.info') }}" class="dg-btn-soft ml-50">
                                            Zurücksetzen
                                        </a>
                                    @endif
                                </div>
                            </form>

                            <div class="dg-table-wrap">
                                <table class="dg-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Rabattgruppe</th>
                                            <th>Rabatt</th>
                                            <th class="text-right">Aktion</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($data as $item)
                                            <tr>
                                                <th class="dg-id">#{{ $item->id }}</th>

                                                <td>
                                                    <div class="dg-name">{{ $item->discount_group }}</div>
                                                </td>

                                                <td>
                                                    <span class="dg-percent">
                                                        {{ number_format((float) $item->discount, 2, ',', '.') }}%
                                                    </span>
                                                </td>

                                                <td>
                                                    <div class="dg-actions">
                                                        <button
                                                            type="button"
                                                            class="dg-icon-btn edit"
                                                            data-open-modal="editDiscountModal{{ $item->id }}"
                                                            title="Bearbeiten"
                                                        >
                                                            <i class="feather icon-edit"></i>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="dg-icon-btn delete"
                                                            data-open-modal="deleteDiscountModal{{ $item->id }}"
                                                            title="Löschen"
                                                        >
                                                            <i class="feather icon-trash-2"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- EDIT MODAL --}}
                                            <div class="dg-modal-backdrop" id="editDiscountModal{{ $item->id }}" aria-hidden="true">
                                                <div class="dg-modal" role="dialog" aria-modal="true">
                                                    <form method="POST" action="{{ route('discount_group.update') }}">
                                                        @csrf

                                                        <input type="hidden" name="id" value="{{ $item->id }}">

                                                        <div class="dg-modal-header">
                                                            <div>
                                                                <h3 class="dg-modal-title">Rabattgruppe bearbeiten</h3>
                                                                <div class="dg-modal-sub">Datensatz #{{ $item->id }}</div>
                                                            </div>

                                                            <button type="button" class="dg-modal-close" data-close-modal>
                                                                <i class="feather icon-x"></i>
                                                            </button>
                                                        </div>

                                                        <div class="dg-modal-body">
                                                            <div class="dg-form-group mb-1">
                                                                <label class="dg-label">Titel der Rabattgruppe</label>
                                                                <input
                                                                    type="text"
                                                                    class="dg-input"
                                                                    name="discount_group"
                                                                    value="{{ old('discount_group', $item->discount_group) }}"
                                                                    required
                                                                >
                                                            </div>

                                                            <div class="dg-form-group">
                                                                <label class="dg-label">Rabatt (%)</label>
                                                                <input
                                                                    type="number"
                                                                    class="dg-input"
                                                                    name="discount"
                                                                    value="{{ old('discount', $item->discount) }}"
                                                                    min="0"
                                                                    max="100"
                                                                    step="0.01"
                                                                    placeholder="%"
                                                                    required
                                                                >
                                                            </div>
                                                        </div>

                                                        <div class="dg-modal-footer">
                                                            <button type="button" class="dg-btn-soft" data-close-modal>
                                                                Abbrechen
                                                            </button>

                                                            <button type="submit" class="dg-btn-success">
                                                                <i class="feather icon-save"></i>
                                                                Speichern
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- DELETE MODAL --}}
                                            <div class="dg-modal-backdrop" id="deleteDiscountModal{{ $item->id }}" aria-hidden="true">
                                                <div class="dg-modal" role="dialog" aria-modal="true">
                                                    <div class="dg-modal-header">
                                                        <div>
                                                            <h3 class="dg-modal-title">Datensatz löschen</h3>
                                                            <div class="dg-modal-sub">Diese Aktion kann nicht rückgängig gemacht werden.</div>
                                                        </div>

                                                        <button type="button" class="dg-modal-close" data-close-modal>
                                                            <i class="feather icon-x"></i>
                                                        </button>
                                                    </div>

                                                    <div class="dg-modal-body">
                                                        <div class="dg-delete-box">
                                                            Möchten Sie die Rabattgruppe
                                                            <strong>„{{ $item->discount_group }}“</strong>
                                                            wirklich löschen?
                                                            <br>
                                                            Datensatznummer: <strong>#{{ $item->id }}</strong>
                                                        </div>
                                                    </div>

                                                    <div class="dg-modal-footer">
                                                        <button type="button" class="dg-btn-soft" data-close-modal>
                                                            Abbrechen
                                                        </button>

                                                        <form action="{{ route('discount_group.destroy', ['id' => $item->id]) }}" method="POST" style="display:inline-flex;margin:0;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dg-btn-danger">
                                                                <i class="feather icon-trash-2"></i>
                                                                Ja, löschen
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="dg-empty">
                                                    Keine Rabattgruppen gefunden.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="dg-pagination">
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CREATE MODAL --}}
        <div class="dg-modal-backdrop" id="createDiscountModal" aria-hidden="true">
            <div class="dg-modal" role="dialog" aria-modal="true">
                <form method="POST" action="{{ route('discount_group.store') }}">
                    @csrf

                    <div class="dg-modal-header">
                        <div>
                            <h3 class="dg-modal-title">Neue Rabattgruppe</h3>
                            <div class="dg-modal-sub">Rabattgruppe mit prozentualem Wert erstellen</div>
                        </div>

                        <button type="button" class="dg-modal-close" data-close-modal>
                            <i class="feather icon-x"></i>
                        </button>
                    </div>

                    <div class="dg-modal-body">
                        <div class="dg-form-group mb-1">
                            <label class="dg-label">Titel der Rabattgruppe</label>
                            <input
                                type="text"
                                class="dg-input"
                                name="discount_group"
                                value="{{ old('discount_group') }}"
                                placeholder="z. B. Standardrabatt"
                                required
                            >
                            @if ($errors->has('discount_group'))
                                <span class="dg-error">{{ $errors->first('discount_group') }}</span>
                            @endif
                        </div>

                        <div class="dg-form-group">
                            <label class="dg-label">Rabatt (%)</label>
                            <input
                                type="number"
                                class="dg-input"
                                name="discount"
                                value="{{ old('discount') }}"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="z. B. 10"
                                required
                            >
                            @if ($errors->has('discount'))
                                <span class="dg-error">{{ $errors->first('discount') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="dg-modal-footer">
                        <button type="button" class="dg-btn-soft" data-close-modal>
                            Abbrechen
                        </button>

                        <button type="submit" class="dg-btn-success">
                            <i class="feather icon-save"></i>
                            Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                const firstInput = modal.querySelector('input:not([type="hidden"]), button');
                if (firstInput) firstInput.focus();
            }, 80);

            refreshIcons();
        }

        function closeModal(modal) {
            if (!modal) return;

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function closeAllModals() {
            document.querySelectorAll('.dg-modal-backdrop.is-open').forEach(function (modal) {
                closeModal(modal);
            });
        }

        document.addEventListener('click', function (event) {
            const openBtn = event.target.closest('[data-open-modal]');
            if (openBtn) {
                event.preventDefault();
                openModal(openBtn.dataset.openModal);
                return;
            }

            if (event.target.closest('[data-close-modal]')) {
                closeModal(event.target.closest('.dg-modal-backdrop'));
                return;
            }

            if (event.target.classList.contains('dg-modal-backdrop')) {
                closeModal(event.target);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAllModals();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if(Session::has('update_msg'))
                toastr.success(@json(session('update_msg')));
            @endif

            @if(Session::has('save_msg'))
                toastr.success(@json(session('save_msg')));
            @endif

            @if(Session::has('delete_msg'))
                toastr.error(@json(session('delete_msg')));
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
                label: 'Rabattgruppen',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush