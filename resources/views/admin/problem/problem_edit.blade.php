@extends('admin.layouts.app')
@section('title') Ticket - Bearbeiten @stop

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --tc-bg: #f3f4f6;
            --tc-card: #ffffff;
            --tc-text: #1f2937;
            --tc-muted: #6b7280;
            --tc-border: #e5e7eb;
            --tc-primary: #93c21c;
            --tc-primary-hover: #7baa18;
            --tc-primary-light: #f4fae7;
            --tc-blue: #74b2d4;
            --tc-blue-light: #eff6ff;
            --tc-success: #10b981;
            --tc-success-light: #ecfdf5;
            --tc-warning: #f59e0b;
            --tc-warning-light: #fffbeb;
            --tc-danger: #ef4444;
            --tc-danger-light: #fef2f2;
            --tc-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --tc-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --tc-radius: 16px;
            --tc-transition: all .2s ease-in-out;
        }

        .tc-wrap {
            max-width: 1680px;
            margin: 0 auto;
            padding: 0 4px 30px;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--tc-text);
        }

        .tc-header {
            margin-bottom: 18px;
        }

        .tc-titlebar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .tc-title {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            letter-spacing: -.035em;
            margin: 0;
        }

        .tc-sub {
            font-size: 14px;
            color: var(--tc-muted);
            margin-top: 5px;
        }

        .tc-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
            color: var(--tc-muted);
            font-size: 13px;
        }

        .tc-breadcrumb a,
        .tc-breadcrumb a:hover {
            color: inherit;
            font-weight: 800;
            text-decoration: none;
        }

        .tc-breadcrumb .current {
            color: #111827;
            font-weight: 900;
        }

        .tc-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tc-btn,
        .tc-btn-soft,
        .tc-icon-btn,
        .tc-mini-btn {
            border-radius: 10px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--tc-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 900;
            text-decoration: none;
            line-height: 1;
        }

        .tc-btn {
            background: var(--tc-primary);
            color: #fff;
            padding: 11px 16px;
        }

        .tc-btn:hover {
            background: var(--tc-primary-hover);
            color: #fff;
            text-decoration: none;
        }

        .tc-btn-soft {
            background: #fff;
            color: #111827;
            border-color: var(--tc-border);
            padding: 11px 14px;
        }

        .tc-btn-soft:hover {
            background: #f9fafb;
            color: #111827;
            text-decoration: none;
        }

        .tc-icon-btn {
            width: 36px;
            height: 36px;
            background: #fff;
            color: var(--tc-muted);
            border-color: var(--tc-border);
        }

        .tc-mini-btn {
            min-height: 30px;
            padding: 7px 10px;
            border-color: #dbeafe;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
        }

        .tc-mini-btn:hover {
            background: #dbeafe;
            color: #1e40af;
            text-decoration: none;
        }

        .tc-layout {
            display: grid;
            grid-template-columns: minmax(760px, 1fr) 420px;
            gap: 22px;
            align-items: start;
        }

        @media(max-width: 1450px) {
            .tc-layout {
                grid-template-columns: 1fr;
            }
        }

        .tc-main,
        .tc-sidebar {
            min-width: 0;
        }

        .tc-card {
            background: #fff;
            border: 1px solid var(--tc-border);
            border-radius: var(--tc-radius);
            box-shadow: var(--tc-shadow-sm);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .tc-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: linear-gradient(180deg, #fff, #fafafa);
            border-bottom: 1px solid var(--tc-border);
        }

        .tc-card-titlebox {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tc-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            background: var(--tc-blue-light);
            color: var(--tc-blue);
        }

        .tc-card-icon.green {
            background: var(--tc-primary-light);
            color: var(--tc-primary);
        }

        .tc-card-icon.orange {
            background: var(--tc-warning-light);
            color: #d97706;
        }

        .tc-card-icon.red {
            background: var(--tc-danger-light);
            color: var(--tc-danger);
        }

        .tc-card-title {
            font-size: 15px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            line-height: 1.2;
        }

        .tc-card-sub {
            font-size: 12px;
            color: var(--tc-muted);
            margin-top: 3px;
        }

        .tc-card-body {
            padding: 22px;
        }

        .tc-card-toggle {
            border: none;
            background: transparent;
            padding: 0;
        }

        .tc-card.is-collapsed .tc-card-head {
            border-bottom: none;
        }

        .tc-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 18px 20px;
        }

        .tc-col-12 {
            grid-column: span 12;
        }

        .tc-col-9 {
            grid-column: span 9;
        }

        .tc-col-8 {
            grid-column: span 8;
        }

        .tc-col-6 {
            grid-column: span 6;
        }

        .tc-col-4 {
            grid-column: span 4;
        }

        .tc-col-3 {
            grid-column: span 6;
        }

        @media(max-width: 920px) {

            .tc-col-9,
            .tc-col-8,
            .tc-col-6,
            .tc-col-4,
            .tc-col-3 {
                grid-column: span 12;
            }
        }

        .tc-field {
            min-width: 0;
            width: 100%;
        }

        .tc-field label,
        .tc-label {
            display: block;
            font-size: 12px;
            font-weight: 900;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .035em;
            margin-bottom: 8px;
        }

        .tc-field .form-control,
        .tc-field input,
        .tc-field select,
        .tc-field textarea,
        .tc-select {
            width: 100% !important;
            min-height: 50px !important;
            height: auto !important;
            padding: 12px 14px !important;
            font-size: 15px !important;
            line-height: 1.45 !important;
            border-radius: 12px !important;
            border: 1px solid var(--tc-border) !important;
            background: #fff;
        }

        .tc-field textarea.form-control {
            min-height: 120px !important;
        }

        .tc-field .form-control:focus,
        .tc-field input:focus,
        .tc-field select:focus,
        .tc-field textarea:focus {
            border-color: var(--tc-primary) !important;
            box-shadow: 0 0 0 3px var(--tc-primary-light) !important;
        }

        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
        }

        .select2-dropdown {
            z-index: 100500 !important;
        }

        .swal2-container {
            z-index: 200000 !important;
        }

        .select2-container--default .select2-selection--single {
            min-height: 50px !important;
            height: 50px !important;
            border-radius: 12px !important;
            border: 1px solid var(--tc-border) !important;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 50px !important;
            border-radius: 12px !important;
            padding: 5px 8px !important;
            border: 1px solid var(--tc-border) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            font-size: 15px !important;
            padding-left: 14px !important;
            padding-right: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 6px !important;
            padding: 5px 9px !important;
            font-size: 13px !important;
            border-radius: 999px !important;
        }

        .tc-check-panel,
        .ticket-stage-card,
        .ticket-availability-card {
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #dbeafe;
            background: linear-gradient(135deg, #f8fafc, #ffffff);
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
        }

        .tc-check-panel {
            border-color: var(--tc-border);
            background: #f9fafb;
            box-shadow: none;
        }

        .tc-switch-line {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .tc-switch-line .vs-checkbox-con {
            margin: 0;
        }

        .tc-pill,
        .ticket-stage-pill,
        .ticket-availability-pill,
        .ticket-age-badge,
        .ticket-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--tc-border);
            background: #fff;
            font-size: 11px;
            font-weight: 900;
            margin: 3px 4px 3px 0;
            white-space: nowrap;
        }

        .ticket-stage-pill {
            border-left-width: 4px;
        }

        .ticket-status-badge {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .ticket-age-badge {
            background: #f9fafb;
            color: #4b5563;
            border-color: #e5e7eb;
        }

        .ticket-status-badge.status-completed {
            background: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }

        .ticket-status-badge.status-junk {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .ticket-stage-title,
        .ticket-availability-title {
            font-size: 12px;
            font-weight: 900;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: .04em;
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 10px;
        }

        .ticket-availability-alert {
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 800;
            margin-top: 10px;
        }

        .ticket-availability-alert.is-ok {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .ticket-availability-alert.is-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
        }

        .ticket-availability-alert.is-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .ticket-slot-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .ticket-slot-btn {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--tc-transition);
        }

        .ticket-slot-btn:hover,
        .ticket-slot-btn.is-selected {
            background: var(--tc-blue);
            color: #fff;
            border-color: var(--tc-blue);
        }

        .ticket-employee-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            margin-top: 3px;
        }

        .ticket-employee-badge.ok {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .ticket-employee-badge.warning {
            background: #ffedd5;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .ticket-employee-badge.danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .tc-history-tabs .nav-link {
            border-radius: 999px !important;
            font-weight: 900;
            font-size: 12px;
            padding: 8px 12px;
        }

        .tc-table-wrap {
            max-height: 460px;
            overflow: auto;
            border: 1px solid var(--tc-border);
            border-radius: 12px;
        }

        .tc-table-wrap table {
            margin-bottom: 0;
        }

        .tc-table-wrap thead th {
            position: sticky;
            top: 0;
            background: #f9fafb;
            z-index: 1;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        #editor {
            min-height: 420px;
            border-radius: 12px;
            background: #fff;
        }

        .ql-toolbar.ql-snow {
            border-radius: 12px 12px 0 0;
            border-color: var(--tc-border);
        }

        .ql-container.ql-snow {
            border-radius: 0 0 12px 12px;
            border-color: var(--tc-border);
            min-height: 420px;
            font-size: 15px;
        }

        .ql-editor {
            min-height: 400px;
            font-size: 15px;
            line-height: 1.7;
        }

        .tc-submitbar {
            position: sticky;
            bottom: 18px;
            z-index: 30;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
            border: 1px solid var(--tc-border);
            border-radius: 16px;
            box-shadow: var(--tc-shadow);
            padding: 12px 14px;
            margin-top: 16px;
        }

        .tc-submitbar small {
            color: var(--tc-muted);
            font-weight: 700;
        }

        .tc-danger-link {
            background: var(--tc-danger-light);
            color: #b91c1c;
            border-color: #fecaca;
        }

        .tc-danger-link:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .tc-modal .modal-content {
            border-radius: 16px;
            border: 1px solid var(--tc-border);
            box-shadow: var(--tc-shadow);
            overflow: hidden;
        }

        .tc-modal .modal-header {
            background: #fafafa;
            border-bottom: 1px solid var(--tc-border);
        }

        .tc-modal .modal-footer {
            background: #fafafa;
            border-top: 1px solid var(--tc-border);
        }

        .text-danger {
            display: block;
            font-size: 12px;
            font-weight: 800;
            margin-top: 6px;
        }
    </style>
@endsection

@section('content')
    @php
        $problem = $problem ?? ($problems ?? null);
        abort_if(!$problem, 404);

        $problem->loadMissing(['customer', 'alternative', 'product', 'employees', 'errors', 'leadStage', 'leadStageSubStage', 'leadProductList']);

        $selectedCustomer = $problem->customer;
        $selectedProduct = $problem->product;
        $selectedAlternative = $problem->alternative;
        $selectedEmployees = collect(old('responsible') ? [] : ($problem->employees ?? collect()));

        $customerLabel = trim((($selectedCustomer->firma ?? '') ? ($selectedCustomer->firma . ' - ') : '') . (($selectedCustomer->name ?? '') . ' ' . ($selectedCustomer->lastname ?? '')));
        $customerLabel = $customerLabel ?: (!empty($selectedCustomer->customer_no) ? '#' . $selectedCustomer->customer_no : 'Kunde #' . $problem->customer_id);

        $productLabel = $selectedProduct->article_group ?? ('Produkt #' . $problem->product_id);

        $selectedErrorIds = old('error_code');
        if ($selectedErrorIds === null) {
            $selectedErrorIds = $problem->errors?->pluck('id')->all() ?? [];
            if (empty($selectedErrorIds) && !empty($problem->error_code)) {
                $selectedErrorIds = collect(explode(',', (string) $problem->error_code))->map(fn($id) => trim($id))->filter()->all();
            }
        }
        $selectedErrorIds = collect((array) $selectedErrorIds)->map(fn($id) => (string) $id)->all();
        $selectedErrors = collect($error_code ?? [])->filter(fn($error) => in_array((string) ($error->id ?? $error->error_id ?? ''), $selectedErrorIds, true));

        $leadStageCollection = collect($leadStages ?? []);
        $montageStage = $leadStageCollection->first(function ($stage) {
            $name = \Illuminate\Support\Str::lower((string) ($stage->name ?? ''));
            $key = \Illuminate\Support\Str::lower((string) ($stage->key ?? ''));
            return $name === 'montage' || $key === 'montage' || str_contains($name, 'montage') || str_contains($key, 'montage');
        });
        $montageStageId = $montageStage->id ?? '';
        $oldNuriva = old('nuriva') || old('is_nuriva');
        if (!session()->hasOldInput()) {
            $stageName = \Illuminate\Support\Str::lower((string) ($problem->leadStage->name ?? ''));
            $stageKey = \Illuminate\Support\Str::lower((string) ($problem->leadStage->key ?? ''));
            $oldNuriva = $stageName === 'montage' || $stageKey === 'montage' || str_contains($stageName, 'montage') || str_contains($stageKey, 'montage');
        }
    @endphp

    <div class="app-content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-body">
                <div class="tc-wrap">
                    <div class="tc-header">
                        <div class="tc-titlebar">
                            <div>
                                <h1 class="tc-title">Ticket bearbeiten</h1>
                                <div class="tc-sub">Kunde, Produkt, Nuriva-Montage, Zuständigkeit und Ticketdetails
                                    bearbeiten.</div>
                                <div class="tc-breadcrumb">
                                    <a href="{{ url('/employee_dashboard') }}">Home</a>
                                    <span>›</span>
                                    <a href="{{ url('problem_view') }}">Tickets</a>
                                    <span>›</span>
                                    <span class="current">Bearbeiten</span>
                                </div>
                            </div>

                            <div class="tc-actions">
                                <button type="button" class="tc-btn-soft" id="tcCollapseAll">
                                    <i class="feather icon-minimize-2"></i> Alles schließen
                                </button>
                                <button type="button" class="tc-btn-soft" id="tcExpandAll">
                                    <i class="feather icon-maximize-2"></i> Alles öffnen
                                </button>
                                <a href="{{ url('problem_view') }}" class="tc-btn-soft">
                                    <i class="feather icon-list"></i> Ticketliste
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Fehler!</strong> Bitte prüfe die Eingaben:<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="wizard-circle" method="POST" action="{{ route('problem.update', $problem->id) }}"
                        id="ticketCreateForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="start_user"
                            value="{{ old('start_user', $problem->start_user ?? auth()->user()->name) }}">
                        <input type="hidden" name="lead_stage_id" id="lead_stage_id"
                            value="{{ old('lead_stage_id', $problem->lead_stage_id) }}"
                            data-montage-stage-id="{{ $montageStageId }}">
                        <input type="hidden" name="lead_stage_sub_stage_id" id="lead_stage_sub_stage_id"
                            value="{{ old('lead_stage_sub_stage_id', $problem->lead_stage_sub_stage_id) }}">

                        <div class="tc-layout">
                            <div class="tc-main">
                                <div class="tc-card tc-card-collapsible">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon blue"><i class="feather icon-alert-circle"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Ticket Basisdaten</h3>
                                                <div class="tc-card-sub">Fehlercode, Quelle, Tickettyp, Datum und Priorität
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>

                                    <div class="tc-card-body">
                                        <div class="tc-grid">
                                            <div class="tc-field tc-col-6">
                                                <label for="error_code">* Fehlercode</label>
                                                <select class="select2 form-control" multiple name="error_code[]"
                                                    id="error_code">
                                                    @foreach($selectedErrors as $selectedError)
                                                        <option value="{{ $selectedError->id ?? $selectedError->error_id }}"
                                                            selected>
                                                            {{ $selectedError->problem_types ?? $selectedError->error_code ?? ('Fehler #' . ($selectedError->id ?? $selectedError->error_id)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('error_code')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-6">
                                                <label for="source">* Quelle</label>
                                                <select class="select2 form-control" name="source" id="source">
                                                    <option value="Kunde" {{ old('source', $problem->source) == 'Kunde' ? 'selected' : '' }}>Kunde</option>
                                                    <option value="Mitarbeiter" {{ old('source', $problem->source) == 'Mitarbeiter' ? 'selected' : '' }}>Mitarbeiter
                                                    </option>
                                                    <option value="System" {{ old('source', $problem->source) == 'System' ? 'selected' : '' }}>System</option>
                                                    <option value="Telefonisch" {{ old('source', $problem->source) == 'Telefonisch' ? 'selected' : '' }}>Telefonisch
                                                    </option>
                                                    <option value="E-Mail" {{ old('source', $problem->source) == 'E-Mail' ? 'selected' : '' }}>E-Mail</option>
                                                    <option value="Vor Ort" {{ old('source', $problem->source) == 'Vor Ort' ? 'selected' : '' }}>Vor Ort</option>
                                                    <option value="Intern" {{ old('source', $problem->source) == 'Intern' ? 'selected' : '' }}>Intern</option>
                                                    <option value="Extern" {{ old('source', $problem->source) == 'Extern' ? 'selected' : '' }}>Extern</option>
                                                    <option value="Webformular" {{ old('source', $problem->source) == 'Webformular' ? 'selected' : '' }}>Webformular
                                                    </option>
                                                    <option value="Support-Portal" {{ old('source', $problem->source) == 'Support-Portal' ? 'selected' : '' }}>
                                                        Support-Portal</option>
                                                    <option value="Live-Chat" {{ old('source', $problem->source) == 'Live-Chat' ? 'selected' : '' }}>Live-Chat
                                                    </option>
                                                    <option value="API" {{ old('source', $problem->source) == 'API' ? 'selected' : '' }}>API</option>
                                                    <option value="Monitoring" {{ old('source', $problem->source) == 'Monitoring' ? 'selected' : '' }}>Monitoring
                                                    </option>
                                                    <option value="Social Media" {{ old('source', $problem->source) == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                                                    <option value="WhatsApp" {{ old('source', $problem->source) == 'WhatsApp' ? 'selected' : '' }}>WhatsApp</option>
                                                    <option value="Fax" {{ old('source', $problem->source) == 'Fax' ? 'selected' : '' }}>Fax</option>
                                                    <option value="Slack" {{ old('source', $problem->source) == 'Slack' ? 'selected' : '' }}>Slack</option>
                                                    <option value="Teams" {{ old('source', $problem->source) == 'Teams' ? 'selected' : '' }}>Teams</option>
                                                    <option value="Besuch" {{ old('source', $problem->source) == 'Besuch' ? 'selected' : '' }}>Besuch</option>
                                                    <option value="Manuell erstellt" {{ old('source', $problem->source) == 'Manuell erstellt' ? 'selected' : '' }}>Manuell
                                                        erstellt</option>
                                                    <option value="Weitergeleitet" {{ old('source', $problem->source) == 'Weitergeleitet' ? 'selected' : '' }}>
                                                        Weitergeleitet</option>
                                                </select>
                                                @error('source')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-6">
                                                <label for="error_type">* Tickettyp</label>
                                                <select class="select2 form-control" name="error_type" id="error_type">
                                                    <option value="complaint" {{ old('error_type', $problem->error_type) == 'complaint' ? 'selected' : '' }}>REKLAMATION
                                                    </option>
                                                    <option value="emergency_service" {{ old('error_type', $problem->error_type) == 'emergency_service' ? 'selected' : '' }}>
                                                        NOTDIENST</option>
                                                    <option value="repair" {{ old('error_type', $problem->error_type) == 'repair' ? 'selected' : '' }}>REPARATUR
                                                    </option>
                                                    <option value="maintenance" {{ old('error_type', $problem->error_type) == 'maintenance' ? 'selected' : '' }}>WARTUNG
                                                    </option>
                                                    <option value="malfunction" {{ old('error_type', $problem->error_type) == 'malfunction' ? 'selected' : '' }}>STÖRUNG
                                                    </option>
                                                    <option value="installation" {{ old('error_type', $problem->error_type) == 'installation' ? 'selected' : '' }}>
                                                        INSTALLATION</option>
                                                    <option value="configuration_error" {{ old('error_type', $problem->error_type) == 'configuration_error' ? 'selected' : '' }}>
                                                        KONFIGURATION</option>
                                                    <option value="system_outage" {{ old('error_type', $problem->error_type) == 'system_outage' ? 'selected' : '' }}>
                                                        SYSTEMAUSFALL</option>
                                                    <option value="security_issue" {{ old('error_type', $problem->error_type) == 'security_issue' ? 'selected' : '' }}>
                                                        SICHERHEITSPROBLEM</option>
                                                    <option value="user_error" {{ old('error_type', $problem->error_type) == 'user_error' ? 'selected' : '' }}>
                                                        BEDIENUNGSFEHLER</option>
                                                    <option value="network_problem" {{ old('error_type', $problem->error_type) == 'network_problem' ? 'selected' : '' }}>
                                                        NETZWERKFEHLER</option>
                                                    <option value="software_bug" {{ old('error_type', $problem->error_type) == 'software_bug' ? 'selected' : '' }}>
                                                        SOFTWAREFEHLER</option>
                                                    <option value="hardware_defect" {{ old('error_type', $problem->error_type) == 'hardware_defect' ? 'selected' : '' }}>
                                                        HARDWAREFEHLER</option>
                                                    <option value="spare_part_request" {{ old('error_type', $problem->error_type) == 'spare_part_request' ? 'selected' : '' }}>
                                                        ERSATZTEILANFRAGE</option>
                                                    <option value="timeout" {{ old('error_type', $problem->error_type) == 'timeout' ? 'selected' : '' }}>
                                                        ZEITÜBERSCHREITUNG</option>
                                                    <option value="communication_failure" {{ old('error_type', $problem->error_type) == 'communication_failure' ? 'selected' : '' }}>
                                                        KOMMUNIKATIONSPROBLEM</option>
                                                    <option value="power_outage" {{ old('error_type', $problem->error_type) == 'power_outage' ? 'selected' : '' }}>
                                                        ENERGIEAUSFALL</option>
                                                    <option value="update_failure" {{ old('error_type', $problem->error_type) == 'update_failure' ? 'selected' : '' }}>
                                                        UPDATEFEHLER</option>
                                                    <option value="access_issue" {{ old('error_type', $problem->error_type) == 'access_issue' ? 'selected' : '' }}>
                                                        ZUGRIFFSPROBLEM</option>
                                                    <option value="other" {{ old('error_type', $problem->error_type) == 'other' ? 'selected' : '' }}>SONSTIGES
                                                    </option>
                                                </select>
                                                @error('error_type')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-3">
                                                <label for="date">Datum</label>
                                                <input type="date" class="form-control required" id="date" name="date"
                                                    value="{{ old('date', optional($problem->date)->format('Y-m-d') ?: date('Y-m-d')) }}">
                                                @error('date')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-3">
                                                <label for="periority">Priorität</label>
                                                <select name="priority" id="periority" class="form-control select2">
                                                    <option value="normal" data-icon="fa fa-battery-empty" {{ old('priority', $problem->priority ?? 'normal') == 'normal' ? 'selected' : '' }}}>Keine</option>
                                                    <option value="Dringend" data-icon="fa fa-battery-full" {{ old('priority', $problem->priority) == 'Dringend' ? 'selected' : '' }}}>Dringend</option>
                                                    <option value="Sehr Dringend" data-icon="fa fa-fire text-danger" {{ old('priority', $problem->priority) == 'Sehr Dringend' ? 'selected' : '' }}}>Sehr Dringend</option>
                                                </select>
                                                @error('priority')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-12">
                                                <div class="tc-check-panel">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <input type="checkbox" name="repeated" value="1" {{ old('repeated', $problem->repeated) ? 'checked' : '' }}>
                                                            <span class="vs-checkbox"><span class="vs-checkbox--check"><i
                                                                        class="vs-icon feather icon-check"></i></span></span>
                                                            <span class="font-weight-bold">Dieses Problem ist schon einmal
                                                                aufgetreten</span>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-card tc-card-collapsible">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon green"><i class="feather icon-briefcase"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Kunde, Produkt & Nuriva</h3>
                                                <div class="tc-card-sub">Kundenprodukt wählen und optional automatisch auf
                                                    Montage setzen</div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>

                                    <div class="tc-card-body">
                                        <div class="tc-grid">
                                            <div class="tc-field tc-col-6">
                                                <label for="customer_id">* Kunden</label>
                                                <a href="{{ url('new_lead_create') }}" target="_blank" id="add-customer-btn"
                                                    style="display:none; font-size:12px; font-weight:900; margin-bottom:6px;">
                                                    <i class="feather icon-plus"></i> Neuen Kunden hinzufügen
                                                </a>
                                                <select class="select2 form-control" id="customer_id" name="customer_id">
                                                    @if($problem->customer_id)
                                                        <option value="{{ $problem->customer_id }}" selected>
                                                            {{ $customerLabel }}</option>
                                                    @else
                                                        <option disabled selected>Kunde suchen...</option>
                                                    @endif
                                                </select>
                                                @error('customer_id')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-6">
                                                <label for="product_id">* Produkt</label>
                                                <input type="hidden"
                                                    value="{{ old('alternative_id', $problem->alternative_id) }}"
                                                    name="alternative_id" id="alternative_id">
                                                <input type="hidden"
                                                    value="{{ old('lead_product_list_id', $problem->lead_product_list_id) }}"
                                                    name="lead_product_list_id" id="lead_product_list_id">
                                                <select class="select2 form-control" name="product_id" id="product_id">
                                                    @if($problem->product_id)
                                                        <option value="{{ $problem->product_id }}" selected
                                                            data-lp-id="{{ $problem->lead_product_list_id }}"
                                                            data-lead-product-list-id="{{ $problem->lead_product_list_id }}"
                                                            data-alternative-id="{{ $problem->alternative_id }}"
                                                            data-lead-stage-id="{{ $problem->lead_stage_id }}"
                                                            data-lead-sub-stage-id="{{ $problem->lead_stage_sub_stage_id }}"
                                                            data-lead-stage-name="{{ $problem->leadStage->name ?? '' }}"
                                                            data-lead-stage-color="{{ $problem->leadStage->color ?? '#74b2d4' }}">
                                                            {{ $productLabel }}
                                                        </option>
                                                    @else
                                                        <option disabled selected>Bitte wählen Sie zuerst einen Kunden</option>
                                                    @endif
                                                </select>
                                                @error('product_id')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-12">
                                                <div class="ticket-stage-card">
                                                    <div class="ticket-stage-title"><i
                                                            class="feather icon-check-circle"></i> Nuriva Montage</div>

                                                    <div class="tc-switch-line">
                                                        <fieldset>
                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input type="checkbox" name="nuriva" id="nuriva" value="1"
                                                                    {{ $oldNuriva ? 'checked' : '' }}>
                                                                <span class="vs-checkbox"><span
                                                                        class="vs-checkbox--check"><i
                                                                            class="vs-icon feather icon-check"></i></span></span>
                                                                <span class="font-weight-bold">Nuriva</span>
                                                            </div>
                                                        </fieldset>
                                                        <small class="text-muted">Wenn aktiviert, wird das Ticket
                                                            automatisch der CRM-Stage <b>Montage</b> zugeordnet.</small>
                                                    </div>

                                                    <div id="ticketStagePreview" class="mt-1">
                                                        <span class="text-muted">Stage wird nach Produktwahl oder
                                                            Nuriva-Auswahl gesetzt.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-card tc-card-collapsible" data-collapsed="true">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon orange"><i class="feather icon-shield"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Artikel & Garantie</h3>
                                                <div class="tc-card-sub">Seriennummer, Installation, Gewährleistung und
                                                    Kostenübernahme</div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>

                                    <div class="tc-card-body">
                                        <div class="tc-grid">
                                            <div class="tc-field tc-col-3">
                                                <label for="article_name">Artikelname</label>
                                                <input type="text" name="article_name" class="form-control"
                                                    value="{{ old('article_name') }}">
                                                @error('article_name')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="tc-field tc-col-3">
                                                <label for="article_sn">Artikel Seriennummer</label>
                                                <input type="text" name="article_sn" class="form-control"
                                                    value="{{ old('article_sn') }}">
                                                @error('article_sn')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="tc-field tc-col-3">
                                                <label for="installation_date">Installationsdatum</label>
                                                <input type="date" name="installation_date" class="form-control"
                                                    value="{{ old('installation_date') }}">
                                                @error('installation_date')<p class="text-danger">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="tc-field tc-col-3">
                                                <label for="warranty_type">Garantie / Gewährleistung</label>
                                                <select name="warranty_type" class="form-control">
                                                    <option value="">Wählen</option>
                                                    <option value="guarantee" {{ old('warranty_type') === 'guarantee' ? 'selected' : '' }}}>Garantie</option>
                                                    <option value="warranty" {{ old('warranty_type') === 'warranty' ? 'selected' : '' }}}>Gewährleistung</option>
                                                </select>
                                                @error('warranty_type')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="tc-field tc-col-4">
                                                <label for="warranty_duration">Gewährleistungsdauer</label>
                                                <select name="warranty_duration" class="form-control">
                                                    <option value="">Wählen</option>
                                                    <option value="1 week" {{ old('warranty_duration', $problem->warranty_duration) == '1 week' ? 'selected' : '' }}>1 Woche
                                                    </option>
                                                    <option value="2 weeks" {{ old('warranty_duration', $problem->warranty_duration) == '2 weeks' ? 'selected' : '' }}>2
                                                        Wochen</option>
                                                    <option value="1 month" {{ old('warranty_duration', $problem->warranty_duration) == '1 month' ? 'selected' : '' }}>1 Monat
                                                    </option>
                                                    <option value="2 months" {{ old('warranty_duration', $problem->warranty_duration) == '2 months' ? 'selected' : '' }}>2
                                                        Monate</option>
                                                    <option value="3 months" {{ old('warranty_duration', $problem->warranty_duration) == '3 months' ? 'selected' : '' }}>3
                                                        Monate</option>
                                                    <option value="6 months" {{ old('warranty_duration', $problem->warranty_duration) == '6 months' ? 'selected' : '' }}>6
                                                        Monate</option>
                                                    <option value="9 months" {{ old('warranty_duration', $problem->warranty_duration) == '9 months' ? 'selected' : '' }}>9
                                                        Monate</option>
                                                    <option value="1 year" {{ old('warranty_duration', $problem->warranty_duration) == '1 year' ? 'selected' : '' }}>1 Jahr
                                                    </option>
                                                    <option value="18 months" {{ old('warranty_duration', $problem->warranty_duration) == '18 months' ? 'selected' : '' }}>18
                                                        Monate</option>
                                                    <option value="2 years" {{ old('warranty_duration', $problem->warranty_duration) == '2 years' ? 'selected' : '' }}>2 Jahre
                                                    </option>
                                                    <option value="3 years" {{ old('warranty_duration', $problem->warranty_duration) == '3 years' ? 'selected' : '' }}>3 Jahre
                                                    </option>
                                                    <option value="5 years" {{ old('warranty_duration', $problem->warranty_duration) == '5 years' ? 'selected' : '' }}>5 Jahre
                                                    </option>
                                                    <option value="10 years" {{ old('warranty_duration', $problem->warranty_duration) == '10 years' ? 'selected' : '' }}>10
                                                        Jahre</option>
                                                    <option value="Lifetime" {{ old('warranty_duration', $problem->warranty_duration) == 'Lifetime' ? 'selected' : '' }}>
                                                        Lebenslange Garantie</option>
                                                </select>
                                            </div>
                                            <div class="tc-field tc-col-4">
                                                <label for="warranty_remaining">Gewährleistung Restzeit</label>
                                                <input type="number" name="warranty_remaining" class="form-control" min="0"
                                                    value="{{ old('warranty_remaining') }}">
                                                <code id="warranty_status_label"
                                                    style="font-size:11px;display:none;"></code>
                                            </div>
                                            <div class="tc-field tc-col-4">
                                                <label for="finance_to">Kostenübernahme</label>
                                                <select name="finance_to" class="form-control">
                                                    <option value="">Wählen</option>
                                                    <option value="customer" {{ old('finance_to', $problem->finance_to) == 'customer' ? 'selected' : '' }}>Kunde
                                                    </option>
                                                    <option value="our_company" {{ old('finance_to', $problem->finance_to) == 'our_company' ? 'selected' : '' }}>Unser
                                                        Unternehmen</option>
                                                    <option value="product_company" {{ old('finance_to', $problem->finance_to) == 'product_company' ? 'selected' : '' }}>
                                                        Hersteller</option>
                                                    <option value="third_party" {{ old('finance_to', $problem->finance_to) == 'third_party' ? 'selected' : '' }}>
                                                        Drittanbieter</option>
                                                    <option value="expired" {{ old('finance_to', $problem->finance_to) == 'expired' ? 'selected' : '' }}>Abgelaufen
                                                    </option>
                                                    <option value="extended" {{ old('finance_to', $problem->finance_to) == 'extended' ? 'selected' : '' }}>Erweiterte
                                                        Garantie</option>
                                                    <option value="none" {{ old('finance_to', $problem->finance_to) == 'none' ? 'selected' : '' }}>Keine Garantie</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-card tc-card-collapsible">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon blue"><i class="feather icon-users"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Zuständigkeit & Terminprüfung</h3>
                                                <div class="tc-card-sub">Mitarbeiter auswählen und optional Verfügbarkeit
                                                    prüfen</div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>

                                    <div class="tc-card-body">
                                        <div class="tc-grid">
                                            <div class="tc-field tc-col-6">
                                                <label for="responsible">* Zuständig</label>
                                                <select class="select2 form-control" multiple name="responsible[]"
                                                    id="responsible">
                                                    @foreach($selectedEmployees as $employee)
                                                        <option value="{{ $employee->id }}" selected>
                                                            {{ trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('responsible')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-6">
                                                <label for="first_contact">Erstellt von</label>
                                                <select class="select2 form-control" name="first_contact"
                                                    id="first_contact">
                                                    @foreach($responsible as $res)
                                                        <option value="{{ $res->id }}" {{ old('first_contact', $problem->first_contact ?? auth()->user()->name) == $res->id ? 'selected' : '' }}}>
                                                            {{ $res->name }} {{ $res->lastname }}
                                                            @if(auth()->user()->name == $res->id) (Aktueller Benutzer) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('first_contact')<p class="text-danger">{{ $message }}</p>@enderror
                                            </div>

                                            <div class="tc-field tc-col-12">
                                                <div class="tc-check-panel">
                                                    <fieldset>
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <input type="checkbox" name="create_appointment"
                                                                id="create_appointment" value="1" {{ old('create_appointment') ? 'checked' : '' }}>
                                                            <span class="vs-checkbox"><span class="vs-checkbox--check"><i
                                                                        class="vs-icon feather icon-check"></i></span></span>
                                                            <span class="font-weight-bold">Dieses Ticket automatisch als
                                                                Termin erstellen</span>
                                                        </div>
                                                    </fieldset>

                                                    <div id="appointment_fields" class="tc-grid mt-2" style="display:none;">
                                                        <div class="tc-field tc-col-3">
                                                            <label>Termin Datum</label>
                                                            <input type="date" name="appointment_date" id="appointment_date"
                                                                class="form-control"
                                                                value="{{ old('appointment_date', date('Y-m-d')) }}">
                                                        </div>
                                                        <div class="tc-field tc-col-3">
                                                            <label>Startzeit</label>
                                                            <input type="time" name="appointment_start_time"
                                                                id="appointment_start_time" class="form-control"
                                                                value="{{ old('appointment_start_time', '09:00') }}">
                                                        </div>
                                                        <div class="tc-field tc-col-3">
                                                            <label>Endzeit</label>
                                                            <input type="time" name="appointment_end_time"
                                                                id="appointment_end_time" class="form-control"
                                                                value="{{ old('appointment_end_time', '10:00') }}">
                                                        </div>
                                                        <div class="tc-field tc-col-3">
                                                            <label>Dauer</label>
                                                            <select name="appointment_duration_minutes"
                                                                id="appointment_duration_minutes" class="form-control">
                                                                <option value="30">30 Minuten</option>
                                                                <option value="45">45 Minuten</option>
                                                                <option value="60" selected>60 Minuten</option>
                                                                <option value="90">90 Minuten</option>
                                                                <option value="120">120 Minuten</option>
                                                            </select>
                                                        </div>
                                                        <div class="tc-field tc-col-12">
                                                            <button type="button" class="tc-btn"
                                                                id="check_ticket_appointment_availability">
                                                                <i class="feather icon-calendar"></i> Verfügbarkeit prüfen
                                                            </button>
                                                        </div>

                                                        <input type="hidden" name="appointment_checked"
                                                            id="appointment_checked" value="0">
                                                        <input type="hidden" name="appointment_force" id="appointment_force"
                                                            value="0">

                                                        <div class="tc-col-12">
                                                            <div class="ticket-availability-card">
                                                                <div class="ticket-availability-title"><i
                                                                        class="feather icon-calendar"></i>
                                                                    Mitarbeiter-Verfügbarkeit & freie Slots</div>
                                                                <div id="ticketAvailabilityResult">
                                                                    <div class="ticket-availability-alert is-warning">Bitte
                                                                        Datum und zuständige Mitarbeiter wählen, danach
                                                                        Verfügbarkeit prüfen.</div>
                                                                </div>
                                                                <div id="ticketAvailableSlots" class="ticket-slot-grid">
                                                                </div>
                                                                <small class="text-muted d-block mt-1">Slots werden mit 20
                                                                    Minuten Abstand zwischen den Terminen generiert.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-card tc-card-collapsible">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon red"><i class="feather icon-edit-3"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Beschreibung</h3>
                                                <div class="tc-card-sub">Problem, Ursache, Hinweise oder erste Diagnose
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>
                                    <div class="tc-card-body">
                                        <div class="tc-field tc-col-12">
                                            <label for="editor">Beschreibung</label>
                                            <div id="editor" class="form-control">
                                                {!! clean(old('editor_text', $problem->problem)) !!}</div>
                                            <textarea name="editor_text" hidden id="editor_text" cols="30"
                                                rows="10">{{ old('editor_text', $problem->problem) }}</textarea>
                                            @error('editor_text')<p class="text-danger">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-submitbar">
                                    <small><i class="feather icon-info"></i> Ticket wird mit Kunde, Produkt, Nuriva-Stage
                                        und optionalem Termin gespeichert.</small>
                                    <div class="tc-actions">
                                        <a type="button" class="tc-btn-soft tc-danger-link"
                                            href="{{ url('problem_view') }}">
                                            <i class="feather icon-x"></i> Abbrechen
                                        </a>
                                        <button type="submit" class="tc-btn">
                                            <i class="feather icon-save"></i> Änderungen speichern
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <aside class="tc-sidebar">
                                <div class="tc-card tc-card-collapsible">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon blue"><i class="feather icon-clock"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Kundenhistorie</h3>
                                                <div class="tc-card-sub">Alte Tickets und alle Produkte des Kunden</div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>
                                    <div class="tc-card-body">
                                        <ul class="nav nav-pills nav-active-bordered-pill tc-history-tabs">
                                            <li class="nav-item"><a class="nav-link active" id="base-pill31"
                                                    data-toggle="pill" href="#pill31" aria-expanded="true">Alte Tickets</a>
                                            </li>
                                            <li class="nav-item"><a class="nav-link" id="base-pill32" data-toggle="pill"
                                                    href="#pill32" aria-expanded="false">Alle Produkte</a></li>
                                        </ul>
                                        <div class="tab-content mt-1">
                                            <div role="tabpanel" class="tab-pane active" id="pill31" aria-expanded="true"
                                                aria-labelledby="base-pill31">
                                                <div class="tc-table-wrap">
                                                    <table class="table table-bordered" id="old_ticket">
                                                        <thead>
                                                            <tr>
                                                                <th>Ticket#</th>
                                                                <th>Status</th>
                                                                <th>Kunde</th>
                                                                <th>Produkt</th>
                                                                <th>Registriert</th>
                                                                <th>Aktion</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">Kunde
                                                                    wählen...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="pill32" aria-labelledby="base-pill32">
                                                <div class="tc-table-wrap">
                                                    <table class="table" id="all_products">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Adresse</th>
                                                                <th>Produkt</th>
                                                                <th>Service</th>
                                                                <th>Status</th>
                                                                <th>Aktion</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">Kunde
                                                                    wählen...</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tc-card tc-card-collapsible" data-collapsed="true">
                                    <div class="tc-card-head">
                                        <div class="tc-card-titlebox">
                                            <div class="tc-card-icon green"><i class="feather icon-help-circle"></i></div>
                                            <div>
                                                <h3 class="tc-card-title">Workflow Hinweis</h3>
                                                <div class="tc-card-sub">Empfohlene Reihenfolge</div>
                                            </div>
                                        </div>
                                        <button type="button" class="tc-icon-btn tc-card-toggle"><i
                                                class="feather icon-chevron-up tc-collapse-icon"></i></button>
                                    </div>
                                    <div class="tc-card-body">
                                        <div class="tc-pill">1. Kunde wählen</div>
                                        <div class="tc-pill">2. Produkt wählen</div>
                                        <div class="tc-pill">3. Nuriva prüfen</div>
                                        <div class="tc-pill">4. Mitarbeiter wählen</div>
                                        <div class="tc-pill">5. Termin prüfen</div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>

                    <div class="modal tc-modal" id="addErrorModal" tabindex="-1" role="dialog"
                        aria-labelledby="addErrorModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addErrorModalLabel">Neuen Fehler</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                </div>
                                <form id="addErrorForm">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="new_error_code">Fehlercode</label>
                                            @php
                                                $rend_temp = mt_rand(1000, 9999);
                                                $error_temp = 'SA-' . $rend_temp;
                                            @endphp
                                            <input type="text" class="form-control" id="new_error_code" name="error_code"
                                                value="{{ $error_temp }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_problem_types">Fehlerbeschreibung</label>
                                            <input type="text" class="form-control" id="new_problem_types"
                                                name="problem_types" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_solution">Fehlerursache</label>
                                            <textarea class="form-control" id="new_solution" name="solution"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="new_reason">Lösung</label>
                                            <textarea class="form-control" id="new_reason" name="reason"></textarea>
                                        </div>
                                        <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee_id }}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Abbrechen</button>
                                        <button type="submit" class="btn btn-primary">Speichern</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>

    <script>
        window.TICKET_LEAD_STAGE_OPTIONS = @json($leadStageOptions ?? []);
        window.TICKET_STAGE_CONTEXT_URL = "{{ url('/tickets/lead-stage-context') }}";
        window.TICKET_APPOINTMENT_AVAILABILITY_URL = "{{ url('/tickets/appointment-availability') }}";
        window.TICKET_URL_BASE = "{{ url('problem/profile') }}";
        window.CUSTOMER_URL_BASE = "{{ url('new_lead_profile') }}";
        window.CSRF_TOKEN = "{{ csrf_token() }}";
        window.TICKET_EDIT_BOOT = {
            ticketId: {{ (int) $problem->id }},
            customerId: "{{ $problem->customer_id }}",
            productId: "{{ $problem->product_id }}",
            alternativeId: "{{ $problem->alternative_id }}",
            leadProductListId: "{{ $problem->lead_product_list_id }}",
            leadStageId: "{{ $problem->lead_stage_id }}",
            leadSubStageId: "{{ $problem->lead_stage_sub_stage_id }}",
            customerText: @json($customerLabel),
            productText: @json($productLabel),
            problemHtml: @json(old('editor_text', $problem->problem))
        };
    </script>

    <script>
        (function () {
            "use strict";

            let lastProductsReq = null;
            let lastTypedTerm = '';
            let quill = null;

            const serviceNames = {
                complete: 'Komplettlösung',
                montage: 'Montage',
                product: 'Produkt',
                plan: 'Planung',
                maintenance: 'Wartung',
                repair: 'Reparatur',
                emergency: 'Notdienst',
                reclaim: 'Reklamation',
                others: 'Sonstiges'
            };

            const statusNames = {
                lead: 'Lead',
                plan: 'Planung',
                offer: 'Angebot',
                deal: 'Deal',
                project: 'Projekt',
                completed: 'Abgeschlossen',
                archive: 'Archiv',
                junk: 'Müll',
                ticket: 'Ticket'
            };

            function e(value) {
                return $('<div>').text(value == null ? '' : String(value)).html();
            }

            function asArray(value) {
                return Array.isArray(value) ? value : [];
            }

            function normalizeId(value) {
                value = String(value || '').trim();
                return /^\d+$/.test(value) ? value : '';
            }

            function valueOrFallback(value, fallback) {
                value = String(value || '').trim();
                return value.length ? value : fallback;
            }

            function fullName(first, last, fallback) {
                const name = `${first || ''} ${last || ''}`.trim();
                return name || fallback;
            }

            function stageOptions() {
                return asArray(window.TICKET_LEAD_STAGE_OPTIONS);
            }

            function findMontageStage() {
                const fixedId = normalizeId($('#lead_stage_id').data('montage-stage-id'));
                if (fixedId) {
                    const fixedStage = stageOptions().find(stage => String(stage.id) === fixedId);
                    if (fixedStage) return fixedStage;
                    return { id: fixedId, name: 'Montage', key: 'montage', color: '#93c21c', sub_stages: [] };
                }

                return stageOptions().find(function (stage) {
                    const name = String(stage.name || '').toLowerCase();
                    const key = String(stage.key || '').toLowerCase();
                    return name === 'montage' || key === 'montage' || name.includes('montage') || key.includes('montage');
                }) || null;
            }

            function setStage(stageId, subStageId, label, color) {
                stageId = normalizeId(stageId);
                subStageId = normalizeId(subStageId);

                $('#lead_stage_id').val(stageId);
                $('#lead_stage_sub_stage_id').val(subStageId);

                if (!stageId) {
                    $('#ticketStagePreview').html('<span class="text-muted">Keine Stage gewählt.</span>');
                    return;
                }

                const stage = stageOptions().find(stage => String(stage.id) === String(stageId));
                const stageLabel = label || (stage ? stage.name : 'Stage');
                const stageColor = color || (stage ? stage.color : '#74b2d4');

                $('#ticketStagePreview').html(
                    `<span class="ticket-stage-pill" style="border-left-color:${e(stageColor)}">Stage: ${e(stageLabel)}</span>` +
                    ($('#nuriva').is(':checked') ? '<span class="ticket-status-badge status-completed">Nuriva aktiv</span>' : '')
                );
            }

            function applyNurivaStage() {
                const montage = findMontageStage();

                if (!montage) {
                    $('#lead_stage_id').val('');
                    $('#lead_stage_sub_stage_id').val('');
                    $('#ticketStagePreview').html('<div class="ticket-availability-alert is-danger"><b>Montage Stage nicht gefunden.</b> Bitte im Controller $leadStages oder $leadStageOptions mit der Stage Montage laden.</div>');
                    return;
                }

                setStage(montage.id, '', montage.name || 'Montage', montage.color || '#93c21c');
            }

            function applyStageFromProductOption($option) {
                if ($('#nuriva').is(':checked')) {
                    applyNurivaStage();
                    return;
                }

                const stageId = normalizeId($option.data('lead-stage-id'));
                const subStageId = normalizeId($option.data('lead-sub-stage-id'));
                const stageName = $option.data('lead-stage-name') || '';
                const stageColor = $option.data('lead-stage-color') || '#74b2d4';

                if (stageId) {
                    setStage(stageId, subStageId, stageName, stageColor);
                    return;
                }

                const leadProductListId = normalizeId($option.data('lead-product-list-id') || $option.data('lp-id'));
                if (!leadProductListId) {
                    setStage('', '', '', '');
                    return;
                }

                $.get(window.TICKET_STAGE_CONTEXT_URL, { lead_product_list_id: leadProductListId })
                    .done(function (response) {
                        const context = response.context || {};
                        const stage = response.stage || {};
                        setStage(context.lead_stage_id, context.lead_stage_sub_stage_id, stage.name || '', stage.color || '');
                    })
                    .fail(function () {
                        setStage('', '', '', '');
                    });
            }

            function currentSelectedProductOption() {
                return $('#product_id').find('option:selected');
            }

            function resetAvailability() {
                $('#appointment_checked').val('0');
                $('#ticketAvailableSlots').empty();
            }

            function timeAgo(input) {
                if (!input) return 'unbekannt';

                let raw = String(input).trim();
                if (!raw) return 'unbekannt';
                if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) raw += 'T00:00:00';
                if (raw.includes(' ') && !raw.includes('T')) raw = raw.replace(' ', 'T');

                const date = new Date(raw);
                if (isNaN(date.getTime())) return 'unbekannt';

                const seconds = Math.max(0, Math.floor((Date.now() - date.getTime()) / 1000));
                if (seconds < 60) return 'gerade eben';

                const minutes = Math.floor(seconds / 60);
                if (minutes < 60) return `vor ${minutes} Min.`;

                const hours = Math.floor(minutes / 60);
                if (hours < 24) return `vor ${hours} Std.`;

                const days = Math.floor(hours / 24);
                if (days < 30) return `vor ${days} Tag${days === 1 ? '' : 'en'}`;

                const months = Math.floor(days / 30);
                if (months < 12) return `vor ${months} Monat${months === 1 ? '' : 'en'}`;

                const years = Math.floor(months / 12);
                return `vor ${years} Jahr${years === 1 ? '' : 'en'}`;
            }

            function statusBadge(status, dateValue) {
                const raw = String(status || '').trim();
                const label = statusNames[raw] || raw || 'Unbekannt';
                const safeClass = raw.toLowerCase().replace(/[^a-z0-9_-]+/g, '-');
                return `<span class="ticket-status-badge status-${e(safeClass)}">${e(label)}</span><br><span class="ticket-age-badge">Seit ${e(timeAgo(dateValue))}</span>`;
            }

            function ticketOpenUrl(ticket) {
                const id = ticket.id || ticket.problem_id || ticket.ticket_id;
                return ticket.open_url || ticket.url || ticket.profile_url || (id ? `${window.TICKET_URL_BASE}/${id}` : '');
            }

            function productOpenUrl(item) {
                const customerId = item.customer_id || $('#customer_id').val();
                return item.open_url || item.url || item.profile_url || item.customer_url || (customerId ? `${window.CUSTOMER_URL_BASE}/${customerId}` : '');
            }

            function confirmLeave(url) {
                if (!url) {
                    Swal.fire('Fehlt', 'Für diesen Datensatz wurde keine Öffnen-URL gefunden.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Seite verlassen?',
                    text: 'Do you want to leave this page?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, öffnen',
                    cancelButtonText: 'Abbrechen'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.open(url, '_blank');
                    }
                });
            }

            window.ticketConfirmLeave = confirmLeave;

            function initCollapsibleCards() {
                function setCollapsed($card, collapsed) {
                    $card.toggleClass('is-collapsed', collapsed);
                    $card.find('> .tc-card-body').first().stop(true, true)[collapsed ? 'slideUp' : 'slideDown'](180);
                    $card.find('.tc-collapse-icon').first()
                        .toggleClass('feather-chevron-down', collapsed)
                        .toggleClass('feather-chevron-up', !collapsed);
                }

                $(document).on('click', '.tc-card-toggle', function () {
                    const $card = $(this).closest('.tc-card-collapsible');
                    setCollapsed($card, !$card.hasClass('is-collapsed'));
                });

                $('#tcCollapseAll').on('click', function () {
                    $('.tc-card-collapsible').each(function () { setCollapsed($(this), true); });
                });

                $('#tcExpandAll').on('click', function () {
                    $('.tc-card-collapsible').each(function () { setCollapsed($(this), false); });
                });

                $('.tc-card-collapsible[data-collapsed="true"]').each(function () { setCollapsed($(this), true); });
            }

            function initQuill() {
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ header: 1 }, { header: 2 }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ script: 'sub' }, { script: 'super' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    [{ direction: 'rtl' }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    [{ header: [1, 2, 3, 4, 5, 6, false] }],
                    [{ color: [] }, { background: [] }],
                    [{ font: [] }],
                    [{ align: [] }],
                    ['link', 'image', 'video', 'formula'],
                    ['clean']
                ];

                quill = new Quill('#editor', { modules: { toolbar: toolbarOptions }, theme: 'snow' });

                const oldContent = $('#editor_text').val();
                if (oldContent) {
                    quill.root.innerHTML = oldContent;
                }

                quill.on('text-change', function () {
                    $('#editor_text').val(quill.root.innerHTML);
                });
            }

            function initBaseSelect2() {
                $('#product_id').select2({ placeholder: 'Produkt wählen', width: '100%' });
                $('#error_type').select2({ placeholder: 'Tickettyp wählen', width: '100%' });
                $('#first_contact').select2({ width: '100%' });

                $('#source').select2({
                    tags: true,
                    placeholder: 'Quelle auswählen oder neue eingeben',
                    allowClear: true,
                    width: '100%',
                    language: { noResults: function () { return 'Keine Ergebnisse gefunden'; } }
                });

                $('#periority').select2({
                    placeholder: 'Priorität wählen',
                    width: '100%',
                    templateResult: formatPriority,
                    templateSelection: formatPriority,
                    escapeMarkup: function (markup) { return markup; }
                });

                function formatPriority(state) {
                    if (!state.id) return state.text;
                    const icon = $(state.element).data('icon') || 'fa fa-circle';
                    return `<span><i class="${e(icon)}" style="margin-right: 8px;"></i>${e(state.text)}</span>`;
                }
            }

            function initErrorCodes() {
                $('#error_code').select2({
                    placeholder: 'Fehlercode auswählen',
                    allowClear: true,
                    width: '100%',
                    tags: false,
                    minimumInputLength: 0,
                    ajax: {
                        url: '/get-error-codes',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term || '' }; },
                        processResults: function (data) {
                            let results = asArray(data.results);
                            if (!results.some(row => row.id === 'add_new_error')) {
                                results.push({ id: 'add_new_error', text: '+ Neuen Fehler erstellen' });
                            }
                            return { results: results };
                        },
                        cache: true
                    }
                });

                $(document).on('keyup', '.select2-search__field', function () {
                    lastTypedTerm = $(this).val();
                });

                $('#error_code').on('select2:select', function (event) {
                    if (event.params.data.id !== 'add_new_error') return;
                    $('#new_problem_types').val(lastTypedTerm);
                    $('#new_error_code').val('SA-' + Math.floor(1000 + Math.random() * 9000));
                    $('#addErrorModal').modal('show');
                });

                $('#addErrorForm').on('submit', function (event) {
                    event.preventDefault();

                    $.ajax({
                        url: '/add-new-error',
                        method: 'POST',
                        data: {
                            error_code: $('#new_error_code').val(),
                            problem_types: $('#new_problem_types').val(),
                            solution: $('#new_solution').val(),
                            reason: $('#new_reason').val(),
                            employee_id: $('#employee_id').val(),
                            _token: window.CSRF_TOKEN
                        },
                        success: function (response) {
                            if (!response.success) {
                                Swal.fire('Fehler', 'Fehler beim Speichern.', 'error');
                                return;
                            }

                            $('#error_code option[value="add_new_error"]').remove();
                            const newOption = new Option(response.text || $('#new_error_code').val(), response.id, true, true);
                            $('#error_code').append(newOption).trigger('change');
                            $('#addErrorForm')[0].reset();
                            $('#addErrorModal').modal('hide');
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Ein Fehler ist aufgetreten.', 'error');
                        }
                    });
                });
            }

            function initCustomerSearch() {
                $('#customer_id').select2({
                    placeholder: 'Kunde suchen...',
                    width: '100%',
                    ajax: {
                        url: '{{ route("problem.all.customer") }}',
                        dataType: 'json',
                        delay: 300,
                        processResults: function (data) {
                            if (data.status === 'empty') {
                                $('#add-customer-btn').show();
                                return { results: [] };
                            }

                            $('#add-customer-btn').hide();
                            return {
                                results: asArray(data).map(function (item) {
                                    return {
                                        id: item.customer_id || item.id,
                                        text: `${valueOrFallback(item.customer_name || item.name || item.firma, 'Unbekannter Kunde')} ${item.customer_lastname || item.lastname || ''} - ${item.city || ''}`.trim()
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                $('#customer_id').on('change', function () {
                    const customerId = $(this).val();
                    $('#alternative_id').val('');
                    $('#lead_product_list_id').val('');
                    $('#lead_stage_id').val('');
                    $('#lead_stage_sub_stage_id').val('');
                    $('#ticketStagePreview').html('<span class="text-muted">Stage wird nach Produktwahl oder Nuriva-Auswahl gesetzt.</span>');
                    loadCustomerProducts(customerId);
                    loadOldTickets(customerId);
                });

                $('#add-customer-btn').on('click', function (event) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Neuen Kunden hinzufügen?',
                        text: 'Du wirst zur Seite zur Kundenerstellung weitergeleitet.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, weiter',
                        cancelButtonText: 'Abbrechen'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.open('{{ url("new_lead_create") }}', '_blank');
                        }
                    });
                });
            }

            function loadCustomerProducts(customerId) {
                if (!customerId) return;

                if (lastProductsReq && lastProductsReq.readyState !== 4) {
                    lastProductsReq.abort();
                }

                lastProductsReq = $.ajax({
                    url: '/check/ticket/products',
                    method: 'POST',
                    dataType: 'json',
                    data: { customer_id: customerId, _token: window.CSRF_TOKEN },
                    beforeSend: function () {
                        $('#product_id').html('<option disabled selected>Produkte werden geladen...</option>').trigger('change.select2');
                        $('#all_products tbody').html('<tr><td colspan="6" class="text-center text-muted">Produkte werden geladen...</td></tr>');
                    },
                    success: function (data) {
                        const list = asArray(data);
                        $('#product_id').empty();
                        $('#all_products tbody').empty();

                        if (!list.length) {
                            $('#product_id').append('<option disabled selected>Keine Produkte gefunden</option>').trigger('change.select2');
                            $('#all_products tbody').html('<tr><td colspan="6" class="text-center text-muted">Keine Produkte gefunden</td></tr>');
                            return;
                        }

                        $('#product_id').append('<option disabled selected>Bitte wählen Sie ein Produkt</option>');

                        list.forEach(function (item) {
                            const productName = valueOrFallback(item.article_group || item.product_name || item.name, 'Unbekanntes Produkt');
                            const address = [item.street, [item.postcode, item.city].filter(Boolean).join(' ')].filter(Boolean).join(', ');
                            const service = serviceNames[item.service] || item.service || '—';
                            const status = item.status || '—';
                            const leadProductListId = item.lp_id || item.lead_product_list_id || item.id || '';
                            const updatedDate = item.updated_at || item.created_at || item.date || '';

                            $('#product_id').append(
                                `<option value="${e(item.product_id || item.id || '')}"
                                        data-lp-id="${e(leadProductListId)}"
                                        data-lead-product-list-id="${e(leadProductListId)}"
                                        data-alternative-id="${e(item.alternative_id || '')}"
                                        data-status="${e(status)}"
                                        data-lead-stage-id="${e(item.lead_stage_id || '')}"
                                        data-lead-stage-name="${e(item.lead_stage_name || '')}"
                                        data-lead-stage-color="${e(item.lead_stage_color || '#74b2d4')}"
                                        data-lead-sub-stage-id="${e(item.lead_stage_sub_stage_id || '')}"
                                        data-lead-sub-stage-name="${e(item.lead_stage_sub_stage_name || '')}"
                                        data-lead-sub-stage-color="${e(item.lead_stage_sub_stage_color || '#93c21c')}">
                                        ${e(productName)} – ${e(statusNames[status] || status)} (${e(address || 'keine Adresse')})
                                    </option>`
                            );

                            const openUrl = productOpenUrl(item);
                            $('#all_products tbody').append(`
                                    <tr>
                                        <td>${e(fullName(item.customer_name || item.name || item.firma, item.customer_lastname || item.lastname, 'Unbekannter Kunde'))}</td>
                                        <td>${e(address || '—')}</td>
                                        <td>${e(productName)}</td>
                                        <td>${e(service)}</td>
                                        <td>${statusBadge(status, updatedDate)}</td>
                                        <td><button type="button" class="tc-mini-btn" onclick="ticketConfirmLeave('${e(openUrl)}')"><i class="feather icon-external-link"></i> Öffnen</button></td>
                                    </tr>
                                `);
                        });

                        const boot = window.TICKET_EDIT_BOOT || {};
                        if (normalizeId(boot.productId)) {
                            $('#product_id').val(String(boot.productId)).trigger('change.select2');
                            if (boot.alternativeId) $('#alternative_id').val(boot.alternativeId);
                            if (boot.leadProductListId) $('#lead_product_list_id').val(boot.leadProductListId);
                        } else {
                            $('#product_id').trigger('change.select2');
                        }
                    },
                    error: function (xhr, status) {
                        if (status === 'abort') return;
                        $('#product_id').html('<option disabled selected>Fehler beim Laden der Produkte</option>').trigger('change.select2');
                        $('#all_products tbody').html('<tr><td colspan="6" class="text-center text-danger">Fehler beim Laden der Produkte</td></tr>');
                    }
                });
            }

            function loadOldTickets(customerId) {
                if (!customerId) return;

                $.ajax({
                    url: '{{ route("problem.check.ticket") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: { customer_id: customerId, _token: window.CSRF_TOKEN },
                    beforeSend: function () {
                        $('#old_ticket tbody').html('<tr><td colspan="6" class="text-center text-muted">Tickets werden geladen...</td></tr>');
                    },
                    success: function (data) {
                        const list = asArray(data);
                        const $body = $('#old_ticket tbody').empty();

                        if (!list.length) {
                            $body.html('<tr><td colspan="6" class="text-center text-muted">Keine früheren Tickets gefunden</td></tr>');
                            return;
                        }

                        list.forEach(function (ticket) {
                            const customerName = fullName(
                                ticket.customer_name || ticket.customer?.name || ticket.name || ticket.firma,
                                ticket.customer_lastname || ticket.customer?.lastname || ticket.lastname,
                                'Unbekannter Kunde'
                            );
                            const productName = valueOrFallback(ticket.product_name || ticket.product?.article_group || ticket.article_group || ticket.article_name, 'Unbekanntes Produkt');
                            const dateValue = ticket.updated_at || ticket.created_at || ticket.date || ticket.progress_date || ticket.end_date || '';
                            const openUrl = ticketOpenUrl(ticket);

                            $body.append(`
                                    <tr>
                                        <td><b>${e(ticket.ticket_no || ticket.id || '—')}</b></td>
                                        <td>${statusBadge(ticket.status, dateValue)}</td>
                                        <td>${e(customerName)}</td>
                                        <td>${e(productName)}</td>
                                        <td>${e(ticket.date || ticket.created_at || '—')}</td>
                                        <td><button type="button" class="tc-mini-btn" onclick="ticketConfirmLeave('${e(openUrl)}')"><i class="feather icon-external-link"></i> Öffnen</button></td>
                                    </tr>
                                `);
                        });
                    },
                    error: function () {
                        $('#old_ticket tbody').html('<tr><td colspan="6" class="text-center text-danger">Fehler beim Laden der alten Tickets.</td></tr>');
                    }
                });
            }

            function initProductSelection() {
                $('#product_id').on('change', function () {
                    const $selected = currentSelectedProductOption();
                    const alternativeId = $selected.data('alternative-id') || '';
                    const leadProductListId = $selected.data('lead-product-list-id') || $selected.data('lp-id') || '';

                    $('#alternative_id').val(alternativeId);
                    $('#lead_product_list_id').val(leadProductListId);
                    applyStageFromProductOption($selected);
                    resetAvailability();
                });

                $('#nuriva').on('change', function () {
                    if ($(this).is(':checked')) {
                        applyNurivaStage();
                    } else {
                        applyStageFromProductOption(currentSelectedProductOption());
                    }
                });

                if ($('#nuriva').is(':checked')) {
                    applyNurivaStage();
                }
            }

            function initEditBoot() {
                const boot = window.TICKET_EDIT_BOOT || {};
                const customerId = normalizeId(boot.customerId);
                const productId = normalizeId(boot.productId);

                if (customerId && !$('#customer_id option[value="' + customerId + '"]').length) {
                    $('#customer_id').append(new Option(boot.customerText || ('Kunde #' + customerId), customerId, true, true));
                }
                if (customerId) {
                    $('#customer_id').val(customerId).trigger('change.select2');
                    loadCustomerProducts(customerId);
                    loadOldTickets(customerId);
                }

                if (productId && !$('#product_id option[value="' + productId + '"]').length) {
                    const option = new Option(boot.productText || ('Produkt #' + productId), productId, true, true);
                    $('#product_id').append(option);
                }

                if (productId) {
                    setTimeout(function () {
                        const boot = window.TICKET_EDIT_BOOT || {};
                        $('#product_id').val(String(boot.productId)).trigger('change.select2');

                        if (boot.alternativeId) $('#alternative_id').val(boot.alternativeId);
                        if (boot.leadProductListId) $('#lead_product_list_id').val(boot.leadProductListId);
                        if (boot.leadStageId && !$('#nuriva').is(':checked')) $('#lead_stage_id').val(boot.leadStageId);
                        if (boot.leadSubStageId && !$('#nuriva').is(':checked')) $('#lead_stage_sub_stage_id').val(boot.leadSubStageId);

                        if ($('#nuriva').is(':checked')) {
                            applyNurivaStage();
                        } else {
                            renderStagePreview();
                        }
                    }, 500);
                }
            }

            function initResponsibleSelect() {
                $('#responsible').select2({
                    placeholder: 'Verantwortlich auswählen.',
                    width: '100%',
                    ajax: {
                        url: '{{ route("problem.get.responsible") }}',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return {
                                term: params.term || '',
                                date: $('#appointment_date').val() || $('#date').val()
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: asArray(data).map(function (emp) {
                                    return {
                                        id: emp.id,
                                        text: `${emp.name || ''} ${emp.lastname || ''}`.trim(),
                                        full: emp
                                    };
                                })
                            };
                        }
                    },
                    templateResult: function (emp) {
                        if (!emp.full) return emp.text;

                        const departments = asArray(emp.full.departments).join(', ');
                        const positions = asArray(emp.full.positions).join(', ');
                        let statusHtml = '<span class="ticket-employee-badge ok">Verfügbar</span>';

                        if (emp.full.availability_badges && emp.full.availability_badges.length) {
                            statusHtml = emp.full.availability_badges.map(function (badge) {
                                const cls = badge.level === 'danger' ? 'danger' : (badge.level === 'warning' ? 'warning' : 'ok');
                                return `<span class="ticket-employee-badge ${e(cls)}">${e(badge.label)}</span>`;
                            }).join(' ');
                        } else if (emp.full.on_leave) {
                            statusHtml = `<span class="ticket-employee-badge danger">Abwesend (${e(emp.full.leave_info?.from || '')} bis ${e(emp.full.leave_info?.to || '')})</span>`;
                        }

                        const imageTag = emp.full.image
                            ? `<img src="${e(emp.full.image)}" style="width:30px;height:30px;border-radius:50%;margin-right:10px;object-fit:cover;">`
                            : `<div style="width:30px;height:30px;background:#e5e7eb;border-radius:50%;margin-right:10px;"></div>`;

                        return $(`
                                <div style="display:flex;align-items:center;gap:10px;">
                                    ${imageTag}
                                    <div style="line-height:1.2;">
                                        <strong>${e(emp.full.name)} ${e(emp.full.lastname)}</strong><br>
                                        <small>${e(emp.full.email || '')}</small><br>
                                        <small><b>Abteilung:</b> ${e(departments)}</small><br>
                                        <small><b>Positionen:</b> ${e(positions)}</small><br>
                                        ${statusHtml}
                                    </div>
                                </div>
                            `);
                    },
                    templateSelection: function (emp) {
                        if (!emp.full) return emp.text;
                        return `${emp.full.name || ''} ${emp.full.lastname || ''}`.trim();
                    },
                    escapeMarkup: function (markup) { return markup; }
                });

                $('#responsible').on('change', resetAvailability);
            }

            function selectedEmployeeIds() {
                return ($('#responsible').val() || []).map(v => normalizeId(v)).filter(Boolean);
            }

            function renderAvailability(response) {
                const result = response || {};
                const employees = asArray(result.employees);
                const slots = asArray(result.slots);
                let html = '';

                if (!employees.length) {
                    html += '<div class="ticket-availability-alert is-warning">Bitte mindestens einen zuständigen Mitarbeiter wählen.</div>';
                } else {
                    const blocked = employees.filter(emp => !emp.available);
                    html += blocked.length
                        ? '<div class="ticket-availability-alert is-danger"><b>Achtung:</b> Mindestens ein Mitarbeiter ist nicht verfügbar.</div>'
                        : '<div class="ticket-availability-alert is-ok">Alle ausgewählten Mitarbeiter sind für den gewählten Zeitraum verfügbar.</div>';

                    employees.forEach(function (emp) {
                        const badgeClass = emp.available ? 'ok' : 'danger';
                        const label = emp.available ? 'Verfügbar' : asArray(emp.reasons).join(', ');
                        html += `<span class="ticket-employee-badge ${e(badgeClass)}">${e(emp.name)}: ${e(label)}</span> `;
                    });
                }

                $('#ticketAvailabilityResult').html(html);

                const $slots = $('#ticketAvailableSlots').empty();
                if (!slots.length) {
                    $slots.html('<div class="ticket-availability-alert is-warning">Keine freien Slots für alle ausgewählten Mitarbeiter gefunden.</div>');
                    return;
                }

                slots.forEach(function (slot) {
                    const btn = $(`<button type="button" class="ticket-slot-btn" data-start="${e(slot.start)}" data-end="${e(slot.end)}">${e(slot.start)} – ${e(slot.end)}</button>`);
                    btn.on('click', function () {
                        $('.ticket-slot-btn').removeClass('is-selected');
                        $(this).addClass('is-selected');
                        $('#appointment_start_time').val(slot.start);
                        $('#appointment_end_time').val(slot.end);
                        $('#appointment_checked').val('1');
                        Swal.fire({ icon: 'success', title: 'Slot gewählt', text: `${slot.start} – ${slot.end}`, timer: 1200, showConfirmButton: false });
                    });
                    $slots.append(btn);
                });
            }

            function checkTicketAvailability() {
                const employeeIds = selectedEmployeeIds();
                const date = $('#appointment_date').val() || $('#date').val();
                const start = $('#appointment_start_time').val();
                const end = $('#appointment_end_time').val();
                const duration = $('#appointment_duration_minutes').val() || 60;

                if (!date) {
                    Swal.fire('Fehlt', 'Bitte Termin-Datum wählen.', 'warning');
                    return;
                }

                if (!employeeIds.length) {
                    Swal.fire('Fehlt', 'Bitte mindestens einen zuständigen Mitarbeiter wählen.', 'warning');
                    return;
                }

                $('#ticketAvailabilityResult').html('<div class="ticket-availability-alert is-warning">Verfügbarkeit wird geprüft...</div>');
                $('#ticketAvailableSlots').empty();

                $.ajax({
                    url: window.TICKET_APPOINTMENT_AVAILABILITY_URL,
                    method: 'GET',
                    dataType: 'json',
                    data: { date: date, start_time: start, end_time: end, duration_minutes: duration, employee_ids: employeeIds },
                    success: function (response) {
                        renderAvailability(response);
                        if (response.available) {
                            $('#appointment_checked').val('1');
                        } else {
                            $('#appointment_checked').val('0');
                            Swal.fire({ icon: 'warning', title: 'Nicht alle Mitarbeiter verfügbar', html: response.message || 'Bitte wählen Sie einen freien Slot oder andere Mitarbeiter.' });
                        }
                    },
                    error: function (xhr) {
                        $('#appointment_checked').val('0');
                        $('#ticketAvailabilityResult').html('<div class="ticket-availability-alert is-danger">Verfügbarkeit konnte nicht geprüft werden.</div>');
                        Swal.fire('Fehler', xhr.responseJSON?.message || 'Verfügbarkeit konnte nicht geprüft werden.', 'error');
                    }
                });
            }

            function initAppointment() {
                function syncAppointmentVisibility() {
                    if ($('#create_appointment').is(':checked')) {
                        $('#appointment_fields').slideDown();
                        if ($('#date').val()) $('#appointment_date').val($('#date').val());
                    } else {
                        $('#appointment_fields').slideUp();
                    }
                }

                $('#create_appointment').on('change', syncAppointmentVisibility);
                $('#date').on('change', function () {
                    if ($('#create_appointment').is(':checked')) $('#appointment_date').val($(this).val());
                    resetAvailability();
                });

                $('#appointment_date, #appointment_start_time, #appointment_end_time, #appointment_duration_minutes').on('change', function () {
                    resetAvailability();
                    if ($('#responsible').data('select2')) $('#responsible').trigger('change.select2');
                });

                $('#check_ticket_appointment_availability').on('click', checkTicketAvailability);
                syncAppointmentVisibility();
            }

            function initWarrantyCalculation() {
                const installInput = document.querySelector('[name="installation_date"]');
                const durationSelect = document.querySelector('[name="warranty_duration"]');
                const remainingField = document.querySelector('[name="warranty_remaining"]');
                const warrantyLabel = document.getElementById('warranty_status_label');

                if (!installInput || !durationSelect || !remainingField || !warrantyLabel) return;

                function addDuration(date, value) {
                    const endDate = new Date(date);
                    const map = {
                        '1 week': ['days', 7],
                        '2 weeks': ['days', 14],
                        '1 month': ['months', 1],
                        '2 months': ['months', 2],
                        '3 months': ['months', 3],
                        '6 months': ['months', 6],
                        '9 months': ['months', 9],
                        '1 year': ['years', 1],
                        '18 months': ['months', 18],
                        '2 years': ['years', 2],
                        '3 years': ['years', 3],
                        '5 years': ['years', 5],
                        '10 years': ['years', 10]
                    };

                    if (!map[value]) return null;
                    const [unit, amount] = map[value];
                    if (unit === 'days') endDate.setDate(endDate.getDate() + amount);
                    if (unit === 'months') endDate.setMonth(endDate.getMonth() + amount);
                    if (unit === 'years') endDate.setFullYear(endDate.getFullYear() + amount);
                    return endDate;
                }

                function updateWarrantyInfo() {
                    if (!installInput.value || !durationSelect.value) return;

                    if (durationSelect.value === 'Lifetime') {
                        remainingField.value = 9999;
                        warrantyLabel.style.display = 'inline-block';
                        warrantyLabel.innerText = 'Lebenslange Garantie';
                        return;
                    }

                    const installDate = new Date(installInput.value);
                    const endDate = addDuration(installDate, durationSelect.value);
                    if (!endDate) return;

                    const today = new Date();
                    const remainingDays = Math.ceil((endDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
                    remainingField.value = Math.max(remainingDays, 0);
                    warrantyLabel.style.display = 'inline-block';
                    warrantyLabel.innerText = remainingDays > 0 ? `Noch ${remainingDays} Tage gültig` : 'Garantie abgelaufen';
                }

                installInput.addEventListener('change', updateWarrantyInfo);
                durationSelect.addEventListener('change', updateWarrantyInfo);
            }

            function initSubmit() {
                $('#ticketCreateForm').on('submit', function (event) {
                    event.preventDefault();
                    $('.text-danger').remove();

                    const formData = new FormData(this);
                    formData.set('editor_text', quill ? quill.root.innerHTML : $('#editor_text').val());

                    if ($('#nuriva').is(':checked')) {
                        applyNurivaStage();
                    }

                    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

                    $.ajax({
                        url: "{{ route('problem.update', $problem->id) }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            Swal.fire({ icon: 'success', title: 'Erfolgreich', text: response.message || 'Ticket wurde gespeichert.' })
                                .then(function () { window.location.href = response.profile_url || '{{ route('problem.profile', $problem->id) }}'; });
                        },
                        error: function (xhr) {
                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                const errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function (fieldName) {
                                    let field = $(`[name="${fieldName}"]`);
                                    if (!field.length) field = $(`[name="${fieldName}[]"]`);
                                    if (!field.length) return;
                                    field.closest('.tc-field').append(`<span class="text-danger">${e(errors[fieldName][0])}</span>`);
                                });
                                Swal.fire({ icon: 'error', title: 'Fehler!', text: 'Bitte füllen Sie alle Pflichtfelder korrekt aus.' });
                                return;
                            }

                            Swal.fire('Fehler', xhr.responseJSON?.message || 'Ticket konnte nicht gespeichert werden.', 'error');
                        }
                    });
                });
            }

            $(document).ready(function () {
                initCollapsibleCards();
                initQuill();
                initBaseSelect2();
                initErrorCodes();
                initCustomerSearch();
                initProductSelection();
                initEditBoot();
                initResponsibleSelect();
                initAppointment();
                initWarrantyCalculation();
                initSubmit();
            });
        })();
    </script>
@endsection

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            { label: 'Dashboard', url: "{{ url('/') }}" },
            { label: 'Ticketliste', url: "{{ url('problem_view') }}", clickable: false },
            { label: 'Bearbeiten', url: "{{ url()->current() }}", clickable: false }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush