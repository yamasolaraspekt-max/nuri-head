@extends('admin.layouts.app')

@section('title') Lead Task Management @stop

@section('style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <style>
        :root {
            --ltm-bg: #f8fafc;
            --ltm-card: #ffffff;
            --ltm-border: #e5e7eb;
            --ltm-text: #111827;
            --ltm-muted: #6b7280;
            --ltm-blue: #74b2d4;
            --ltm-green: #93c21c;
            --ltm-orange: #f8ac00;
            --ltm-red: #ef4444;
            --ltm-shadow: 0 20px 55px rgba(15, 23, 42, .14);
        }

        .ltm-page {
            min-height: calc(100vh - 120px);
            padding: 14px 0 28px;
            background: radial-gradient(circle at top left, rgba(116, 178, 212, .22), transparent 30%), radial-gradient(circle at bottom right, rgba(147, 194, 28, .16), transparent 30%), #f9fafb;
        }

        .ltm-shell {
            background: #fff;
            border: 1px solid var(--ltm-border);
            border-radius: 24px;
            box-shadow: 0 16px 42px rgba(15, 23, 42, .08);
            padding: 18px;
        }

        .ltm-topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
            padding: 16px;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(116, 178, 212, .18), rgba(147, 194, 28, .12)), #fff;
            border: 1px solid rgba(116, 178, 212, .28);
            margin-bottom: 16px;
        }

        .ltm-title {
            margin: 0;
            font-size: 23px;
            font-weight: 900;
            color: var(--ltm-text);
            text-transform: uppercase;
            letter-spacing: -.02em;
        }

        .ltm-subtitle {
            margin-top: 4px;
            font-size: 13px;
            color: var(--ltm-muted);
            font-weight: 700;
        }

        .ltm-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .ltm-toolbar-left,
        .ltm-toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ltm-btn,
        .ltm-btn-soft,
        .ltm-btn-blue,
        .ltm-btn-danger,
        .ltm-btn-warning {
            border: 0;
            border-radius: 12px;
            min-height: 40px;
            padding: 9px 13px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
            transition: all .18s ease;
        }

        .ltm-btn {
            background: var(--ltm-green);
            color: #fff;
        }

        .ltm-btn:hover {
            background: #7baa18;
            color: #fff;
            transform: translateY(-1px);
        }

        .ltm-btn-blue {
            background: var(--ltm-blue);
            color: #fff;
        }

        .ltm-btn-danger {
            background: var(--ltm-red);
            color: #fff;
        }

        .ltm-btn-warning {
            background: var(--ltm-orange);
            color: #111827;
        }

        .ltm-btn-soft {
            background: #fff;
            color: #374151;
            border: 1px solid var(--ltm-border);
        }

        .ltm-icon-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 10px;
            border: 1px solid var(--ltm-border);
            background: #fff;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .ltm-icon-btn:hover {
            background: #f3f4f6;
        }

        .ltm-icon-btn.danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .ltm-input,
        .ltm-select,
        .ltm-textarea {
            width: 100%;
            min-height: 40px;
            border: 1px solid var(--ltm-border);
            border-radius: 12px;
            padding: 9px 11px;
            outline: none;
            color: var(--ltm-text);
            background: #fff;
            font-size: 14px;
        }

        .ltm-textarea {
            min-height: 95px;
            resize: vertical;
        }

        .ltm-input:focus,
        .ltm-select:focus,
        .ltm-textarea:focus {
            border-color: var(--ltm-green);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .ltm-layout {
            display: grid;
            grid-template-columns: 380px minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }

        .ltm-left-panel,
        .ltm-detail {
            border: 1px solid var(--ltm-border);
            border-radius: 20px;
            background: #fff;
            min-height: 620px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .ltm-left-head,
        .ltm-detail-head {
            padding: 14px 16px;
            background: #fafafa;
            border-bottom: 1px solid var(--ltm-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ltm-left-title,
        .ltm-detail-title {
            font-size: 15px;
            font-weight: 900;
            color: var(--ltm-text);
        }

        .ltm-left-sub,
        .ltm-detail-sub {
            margin-top: 3px;
            font-size: 12px;
            color: var(--ltm-muted);
            font-weight: 700;
        }

        .ltm-stage-count {
            padding: 3px 8px;
            border-radius: 999px;
            background: #eff6ff;
            color: #075985;
            font-size: 11px;
            font-weight: 900;
        }

        .ltm-tree {
            padding: 14px;
            max-height: calc(100vh - 260px);
            overflow: auto;
        }

        .ltm-stage-accordion {
            border: 1px solid var(--ltm-border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 12px;
            background: #fff;
        }

        .ltm-stage-accordion-head {
            width: 100%;
            border: 0;
            background: #f8fafc;
            color: var(--ltm-text);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            text-align: left;
        }

        .ltm-stage-name {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 14px;
            font-weight: 900;
        }

        .ltm-stage-dot,
        .ltm-lane-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            flex: 0 0 auto;
            background: var(--ltm-blue);
        }

        .ltm-stage-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 0 0 auto;
        }

        .ltm-accordion-chevron {
            transition: transform .18s ease;
        }

        .ltm-stage-accordion.is-closed .ltm-accordion-chevron {
            transform: rotate(-90deg);
        }

        .ltm-stage-accordion-body {
            padding: 10px;
            background: #fff;
        }

        .ltm-stage-accordion.is-closed .ltm-stage-accordion-body {
            display: none;
        }

        .ltm-lane {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 14px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .ltm-lane-head {
            border: 0;
            width: 100%;
            padding: 9px 10px;
            background: #f9fafb;
            border-bottom: 1px solid #eef2f7;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            cursor: pointer;
            text-align: left;
        }

        .ltm-lane-title {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 900;
            color: #374151;
            min-width: 0;
        }

        .ltm-lane-body {
            padding: 8px;
        }

        .ltm-lane.is-closed .ltm-lane-body {
            display: none;
        }

        .ltm-lane.is-closed .ltm-accordion-chevron {
            transform: rotate(-90deg);
        }

        .ltm-task-list {
            min-height: 52px;
        }

        .ltm-task-card {
            border: 1px solid var(--ltm-border);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .06);
            margin-bottom: 8px;
            overflow: hidden;
            cursor: pointer;
        }

        .ltm-task-card:hover {
            border-color: rgba(147, 194, 28, .55);
            box-shadow: 0 10px 25px rgba(15, 23, 42, .10);
        }

        .ltm-task-card.is-selected {
            border-color: var(--ltm-green);
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .16);
        }

        .ltm-task-main {
            padding: 10px;
        }

        .ltm-task-title-row {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            justify-content: space-between;
        }

        .ltm-task-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--ltm-text);
            line-height: 1.35;
        }

        .ltm-task-actions {
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: .18s ease;
        }

        .ltm-task-card:hover .ltm-task-actions,
        .ltm-task-card.is-selected .ltm-task-actions {
            opacity: 1;
        }

        .ltm-task-meta {
            margin-top: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            color: var(--ltm-muted);
            font-size: 11px;
            font-weight: 800;
        }

        .ltm-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #374151;
        }

        .ltm-chip.green {
            background: #f0fdf4;
            color: #166534;
        }

        .ltm-chip.red {
            background: #fef2f2;
            color: #991b1b;
        }

        .ltm-activities {
            border-top: 1px solid #f3f4f6;
            padding: 8px 10px;
            background: #fcfcfd;
        }

        .ltm-activity-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .ltm-activity-row:last-child {
            border-bottom: 0;
        }

        .ltm-activity-title {
            font-size: 12px;
            color: #111827;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            cursor: pointer;
        }

        .ltm-child-activity {
            margin-left: 16px;
            color: #4b5563;
        }

        .ltm-empty {
            padding: 16px;
            text-align: center;
            border: 1px dashed #d1d5db;
            border-radius: 14px;
            color: var(--ltm-muted);
            font-size: 12px;
            font-weight: 800;
            background: #fafafa;
        }

        .ltm-loading {
            padding: 35px;
            text-align: center;
            color: var(--ltm-muted);
            font-weight: 900;
        }

        .ltm-placeholder {
            min-height: 54px;
            border: 2px dashed #93c5fd;
            background: rgba(59, 130, 246, .08);
            border-radius: 14px;
            margin-bottom: 8px;
        }

        .ltm-detail {
            position: sticky;
            top: 88px;
            display: flex;
            flex-direction: column;
        }

        .ltm-detail-head {
            align-items: flex-start;
        }

        .ltm-detail-body {
            padding: 16px;
            overflow: auto;
            max-height: calc(100vh - 210px);
        }

        .ltm-detail-section {
            border: 1px solid var(--ltm-border);
            border-radius: 16px;
            padding: 13px;
            margin-bottom: 12px;
        }

        .ltm-detail-kicker {
            font-size: 11px;
            color: var(--ltm-muted);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 7px;
        }

        .ltm-detail-text {
            font-size: 13px;
            color: var(--ltm-text);
            line-height: 1.55;
        }

        .ltm-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 100000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, .58);
        }

        .ltm-modal-backdrop.is-open {
            display: flex;
        }

        .ltm-modal {
            width: 100%;
            max-width: 720px;
            max-height: calc(100vh - 36px);
            background: #fff;
            border: 1px solid var(--ltm-border);
            border-radius: 24px;
            box-shadow: var(--ltm-shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ltm-modal.sm {
            max-width: 560px;
        }

        .ltm-modal-head {
            padding: 17px 20px;
            border-bottom: 1px solid var(--ltm-border);
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            gap: 14px;
        }

        .ltm-modal-title {
            margin: 0;
            font-size: 17px;
            color: var(--ltm-text);
            font-weight: 900;
            text-transform: uppercase;
        }

        .ltm-modal-sub {
            margin-top: 4px;
            font-size: 12px;
            color: var(--ltm-muted);
            font-weight: 700;
        }

        .ltm-modal-close {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--ltm-border);
            background: #fff;
            color: var(--ltm-muted);
            cursor: pointer;
        }

        .ltm-modal-body {
            padding: 20px;
            overflow-y: auto;
        }

        .ltm-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--ltm-border);
            background: #fafafa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ltm-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .ltm-form-group.full {
            grid-column: 1 / -1;
        }

        .ltm-label {
            display: block;
            margin-bottom: 7px;
            color: var(--ltm-muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .ltm-check-card {
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 9px;
            border: 1px solid var(--ltm-border);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 900;
        }

        .ltm-check-card input {
            width: 17px;
            height: 17px;
            accent-color: var(--ltm-green);
        }

        .ltm-error {
            display: none;
            margin-top: 12px;
            padding: 11px 12px;
            border-radius: 14px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
            font-size: 12px;
            font-weight: 800;
            white-space: pre-line;
        }

        .ltm-error.is-visible {
            display: block;
        }

        .ltm-admin-manager {
            border-top: 1px solid var(--ltm-border);
            margin-top: 12px;
            padding-top: 12px;
        }

        .ltm-admin-stage {
            border: 1px solid var(--ltm-border);
            border-radius: 16px;
            background: #fff;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .ltm-admin-stage-head {
            display: grid;
            grid-template-columns: 34px minmax(0, 1.2fr) minmax(0, .9fr) 80px 86px 86px 96px auto;
            gap: 8px;
            align-items: center;
            padding: 10px;
            background: #f9fafb;
            border-bottom: 1px solid #eef2f7;
        }

        .ltm-admin-sub-list {
            padding: 10px;
        }

        .ltm-admin-sub {
            display: grid;
            grid-template-columns: 34px minmax(0, 1.4fr) minmax(0, .9fr) 80px 90px auto;
            gap: 8px;
            align-items: center;
            padding: 8px;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            margin-bottom: 8px;
            background: #fcfcfd;
        }

        .ltm-drag-handle {
            cursor: grab;
            color: var(--ltm-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
        }

        .ltm-stage-admin-tools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .ltm-admin-small {
            font-size: 11px;
            font-weight: 900;
            color: var(--ltm-muted);
            text-transform: uppercase;
        }

        @media (max-width: 1100px) {

            .ltm-admin-stage-head,
            .ltm-admin-sub {
                grid-template-columns: 1fr;
            }

            .ltm-drag-handle {
                justify-content: flex-start;
            }
        }

        #ltmSelect2Portal {
            position: fixed;
            inset: 0;
            z-index: 1000060;
            pointer-events: none;
        }

        #ltmSelect2Portal .select2-container {
            pointer-events: auto;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--open {
            z-index: 1000061 !important;
        }

        .select2-dropdown {
            z-index: 1000062 !important;
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .18);
        }

        @media (max-width:1200px) {
            .ltm-layout {
                grid-template-columns: 1fr;
            }

            .ltm-detail {
                position: static;
                min-height: auto;
            }

            .ltm-detail-body {
                max-height: none;
            }
        }

        @media (max-width:768px) {

            .ltm-toolbar-left,
            .ltm-toolbar-right {
                width: 100%;
            }

            .ltm-toolbar-left .ltm-input {
                min-width: 0 !important;
            }

            .ltm-form-grid {
                grid-template-columns: 1fr;
            }

            .ltm-form-group.full {
                grid-column: auto;
            }
        }

        @media (max-width: 1200px) {
            .ltm-layout {
                grid-template-columns: 320px minmax(0, 1fr);
            }
        }

        @media (max-width: 992px) {
            .ltm-layout {
                grid-template-columns: 1fr;
            }

            .ltm-detail {
                min-height: auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="app-content">
        <div class="content-wrapper ltm-page">
            <div class="content-body ltm-shell" id="leadTaskApp" data-product-id="{{ $section->product_id }}"
                data-section-id="{{ $section->id }}">

                <div class="ltm-topbar">
                    <div>
                        <h2 class="ltm-title">Lead Task Management</h2>
                        <div class="ltm-subtitle">
                            {{ $productModel->article_group ?? ('Produkt #' . $section->product_id) }} ·
                            {{ $section->phase_section }} · Phasen als Collapse-Liste mit Detailansicht rechts.
                        </div>
                    </div>
                    <a href="{{ route('task_phase.index', ['product' => $section->product_id]) }}" class="ltm-btn-soft">
                        <i class="feather icon-arrow-left"></i> Zurück
                    </a>
                </div>

                <div class="ltm-toolbar">
                    <div class="ltm-toolbar-left">
                        <input type="text" class="ltm-input" id="ltmSearch"
                            placeholder="Phase, Aktivität oder Beschreibung suchen..." style="min-width:360px;">
                        <button type="button" class="ltm-btn-blue" id="ltmSearchClear"><i
                                class="feather icon-x"></i></button>
                    </div>
                    <div class="ltm-toolbar-right">
                        <button type="button" class="ltm-btn-soft" id="btnExpandAll"><i class="feather icon-maximize-2"></i>
                            Alle öffnen</button>
                        <button type="button" class="ltm-btn-soft" id="btnCollapseAll"><i
                                class="feather icon-minimize-2"></i> Alle schließen</button>
                        <button type="button" class="ltm-btn-warning" id="btnOpenStageAdmin"><i
                                class="feather icon-settings"></i> LeadStages verwalten</button>
                        <button type="button" class="ltm-btn" id="btnCreateTask"><i class="feather icon-plus"></i> Neue
                            Phase</button>
                        <button type="button" class="ltm-btn-soft" id="btnReloadBoard"><i
                                class="feather icon-refresh-cw"></i> Neu laden</button>
                    </div>
                </div>

                <div class="ltm-layout">
                    <section class="ltm-left-panel">
                        <div class="ltm-left-head">
                            <div>
                                <div class="ltm-left-title">Phasen / Lead-Stages</div>
                                <div class="ltm-left-sub">Alles in einer Spalte. Drag & Drop zwischen Stages und Sub-Stages
                                    bleibt aktiv.</div>
                            </div>
                            <span class="ltm-stage-count" id="ltmTotalCount">0 Phasen</span>
                        </div>
                        <div class="ltm-tree" id="ltmTree">
                            <div class="ltm-loading">Lade Phasen...</div>
                        </div>
                    </section>

                    <aside class="ltm-detail" id="ltmDetail">
                        <div class="ltm-detail-head">
                            <div>
                                <div class="ltm-detail-title">Details</div>
                                <div class="ltm-detail-sub">Klicke links auf eine Phase oder Aktivität.</div>
                            </div>
                        </div>
                        <div class="ltm-detail-body" id="ltmDetailBody">
                            <div class="ltm-empty">Noch nichts ausgewählt.</div>
                        </div>
                    </aside>
                </div>

                {{-- TASK MODAL --}}
                <div class="ltm-modal-backdrop" id="taskModal" aria-hidden="true">
                    <div class="ltm-modal sm" role="dialog" aria-modal="true">
                        <form id="taskForm">
                            <div class="ltm-modal-head">
                                <div>
                                    <h3 class="ltm-modal-title" id="taskModalTitle">Neue Phase</h3>
                                    <div class="ltm-modal-sub">Phase direkt in eine Lead-Stage oder Sub-Stage speichern.
                                    </div>
                                </div>
                                <button type="button" class="ltm-modal-close" data-close-ltm-modal>&times;</button>
                            </div>
                            <div class="ltm-modal-body">
                                <input type="hidden" name="task_id" id="task_id">
                                <input type="hidden" name="product_id" value="{{ $section->product_id }}">
                                <input type="hidden" name="section_id" value="{{ $section->id }}">
                                <div class="ltm-form-grid">
                                    <div class="ltm-form-group full">
                                        <label class="ltm-label">Titel *</label>
                                        <input type="text" class="ltm-input" name="phase_name" id="task_title" required>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Lead-Stage *</label>
                                        <select class="ltm-select" name="lead_stage_id" id="task_lead_stage_id" required>
                                            <option value="">Bitte wählen</option>
                                            @foreach($leadStages as $stage)
                                                <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Sub-Stage</label>
                                        <select class="ltm-select" name="lead_sub_stage_id" id="task_lead_sub_stage_id">
                                            <option value="">Hauptstage</option>
                                            @foreach($leadStages as $stage)
                                                @foreach($stage->activeSubStages as $subStage)
                                                    <option value="{{ $subStage->id }}" data-stage-id="{{ $stage->id }}">
                                                        {{ $stage->name }} · {{ $subStage->name }}</option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Status</label>
                                        <select class="ltm-select" name="status" id="task_status">
                                            <option value="Published">Aktiv</option>
                                            <option value="Unpublished">Inaktiv</option>
                                        </select>
                                    </div>
                                    <div class="ltm-form-group full">
                                        <label class="ltm-label">Beschreibung</label>
                                        <textarea class="ltm-textarea" name="description" id="task_description"></textarea>
                                    </div>
                                </div>
                                <div class="ltm-error" id="taskError"></div>
                            </div>
                            <div class="ltm-modal-footer">
                                <button type="button" class="ltm-btn-soft" data-close-ltm-modal>Abbrechen</button>
                                <button type="submit" class="ltm-btn"><i class="feather icon-save"></i> Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ACTIVITY MODAL --}}
                <div class="ltm-modal-backdrop" id="activityModal" aria-hidden="true">
                    <div class="ltm-modal" role="dialog" aria-modal="true">
                        <form id="activityForm">
                            <div class="ltm-modal-head">
                                <div>
                                    <h3 class="ltm-modal-title" id="activityModalTitle">Neue Aktivität</h3>
                                    <div class="ltm-modal-sub">Einfache Task-Aktivität ohne Master Set.</div>
                                </div>
                                <button type="button" class="ltm-modal-close" data-close-ltm-modal>&times;</button>
                            </div>
                            <div class="ltm-modal-body">
                                <input type="hidden" name="activity_id" id="activity_id">
                                <input type="hidden" name="phase_id" id="activity_phase_id">
                                <input type="hidden" name="parent_id" id="activity_parent_id">
                                <div class="ltm-form-grid">
                                    <div class="ltm-form-group full">
                                        <label class="ltm-label">Titel *</label>
                                        <input type="text" class="ltm-input" name="title" id="activity_title" required>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Qualifikation</label>
                                        <select class="ltm-select js-ltm-select2" name="qualification_ids[]"
                                            id="activity_qualification_ids" multiple>
                                            @foreach($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->position }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Abteilung</label>
                                        <select class="ltm-select js-ltm-select2" name="department_ids[]"
                                            id="activity_department_ids" multiple>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}">
                                                    {{ $department->department_name }}{{ $department->branch ? ' · ' . $department->branch : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Dauer</label>
                                        <input type="number" min="0" step="0.25" class="ltm-input" name="duration"
                                            id="activity_duration">
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Einheit</label>
                                        <select class="ltm-select" name="duration_type" id="activity_duration_type">
                                            <option value="minutes">Minuten</option>
                                            <option value="hours">Stunden</option>
                                            <option value="days">Tage</option>
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Status</label>
                                        <select class="ltm-select" name="status" id="activity_status">
                                            <option value="Published">Aktiv</option>
                                            <option value="Unpublished">Inaktiv</option>
                                        </select>
                                    </div>
                                    <div class="ltm-form-group">
                                        <label class="ltm-label">Foto</label>
                                        <label class="ltm-check-card"><input type="checkbox" name="photo_required"
                                                id="activity_photo_required" value="1"> Foto erforderlich</label>
                                    </div>
                                    <div class="ltm-form-group full">
                                        <label class="ltm-label">Artikel optional</label>
                                        <select class="ltm-select js-ltm-select2" name="article_ids[]"
                                            id="activity_article_ids" multiple>
                                            @foreach($articles as $article)
                                                <option value="{{ $article->id }}">{{ $article->article_no }} ·
                                                    {{ $article->product }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ltm-form-group full">
                                        <label class="ltm-label">Beschreibung</label>
                                        <textarea class="ltm-textarea" name="description"
                                            id="activity_description"></textarea>
                                    </div>
                                </div>
                                <div class="ltm-error" id="activityError"></div>
                            </div>
                            <div class="ltm-modal-footer">
                                <button type="button" class="ltm-btn-soft" data-close-ltm-modal>Abbrechen</button>
                                <button type="submit" class="ltm-btn"><i class="feather icon-save"></i> Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>


                {{-- LEAD STAGE ADMIN MODAL --}}
                <div class="ltm-modal-backdrop" id="leadStageAdminModal" aria-hidden="true">
                    <div class="ltm-modal" role="dialog" aria-modal="true" style="max-width:1100px;">
                        <div class="ltm-modal-head">
                            <div>
                                <h3 class="ltm-modal-title">LeadStages verwalten</h3>
                                <div class="ltm-modal-sub">
                                    Nur Benutzer mit UserRoll <strong>Administrator</strong> dürfen diese Liste ändern.
                                    LeadStages und Sub-Stages können per Drag & Drop sortiert werden.
                                </div>
                            </div>
                            <button type="button" class="ltm-modal-close" data-close-ltm-modal>&times;</button>
                        </div>

                        <div class="ltm-modal-body">
                            <div class="ltm-stage-admin-tools">
                                <input type="text" class="ltm-input" id="adminStageName"
                                    placeholder="Neue LeadStage, z. B. Angebot" style="max-width:260px;">
                                <input type="color" class="ltm-input" id="adminStageColor" value="#74b2d4"
                                    style="max-width:76px;padding:5px;">
                                <input type="text" class="ltm-input" id="adminStageIcon" placeholder="Icon optional"
                                    style="max-width:180px;">
                                <label class="ltm-check-card" style="min-height:40px;">
                                    <input type="checkbox" id="adminStageActive" checked> Aktiv
                                </label>
                                <button type="button" class="ltm-btn" id="btnCreateLeadStage">
                                    <i class="feather icon-plus"></i> LeadStage erstellen
                                </button>
                                <button type="button" class="ltm-btn-soft" id="btnReloadLeadStages">
                                    <i class="feather icon-refresh-cw"></i> Aktualisieren
                                </button>
                            </div>

                            <div class="ltm-error" id="leadStageAdminError"></div>

                            <div class="ltm-admin-manager">
                                <div class="ltm-admin-small" style="margin-bottom:8px;">LeadStages</div>
                                <div id="leadStageAdminList">
                                    <div class="ltm-loading">Lade LeadStages...</div>
                                </div>
                            </div>
                        </div>

                        <div class="ltm-modal-footer">
                            <button type="button" class="ltm-btn-soft" data-close-ltm-modal>Schließen</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    @php
        $leadTaskBoardUrl = url('/task-phase/ajax/board/' . $section->product_id . '/' . $section->id);
    @endphp

    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        (function ($) {
            'use strict';

            if (window.__LEAD_TASK_MANAGER_ACCORDION__) return;
            window.__LEAD_TASK_MANAGER_ACCORDION__ = true;

            const app = $('#leadTaskApp');
            const state = {
                productId: parseInt(app.data('product-id'), 10),
                sectionId: parseInt(app.data('section-id'), 10),
                board: [],
                selectedTask: null,
                selectedActivity: null,
                search: '',
                closedStages: new Set(),
                closedLanes: new Set()
            };

            const routes = {
                board: @json($leadTaskBoardUrl),
                taskStore: @json(url('/task-phase/ajax/tasks')),
                taskUpdateBase: @json(url('/task-phase/ajax/tasks')) + '/',
                taskDeleteBase: @json(url('/task-phase/ajax/tasks')) + '/',
                taskCloneBase: @json(url('/task-phase/ajax/tasks')) + '/',
                taskMoveFlat: @json(url('/task-phase/ajax/tasks/move')),
                taskMoveParamBase: @json(url('/task-phase/ajax/tasks')) + '/',
                taskReorder: @json(url('/task-phase/ajax/tasks/reorder')),
                activityStore: @json(url('/task-phase/ajax/activities')),
                activityUpdateBase: @json(url('/task-phase/ajax/activities')) + '/',
                activityDeleteBase: @json(url('/task-phase/ajax/activities')) + '/',
                activityCloneBase: @json(url('/task-phase/ajax/activities')) + '/',
                activityMoveFlat: @json(url('/task-phase/ajax/activities/move')),
                activityMoveParamBase: @json(url('/task-phase/ajax/activities')) + '/',
                activityReorder: @json(url('/task-phase/ajax/activities/reorder')),
                stageAdmin: {
                    index: @json(route('task.phase.ajax.stage-admin.stages.index')),
                    store: @json(route('task.phase.ajax.stage-admin.stages.store')),
                    reorder: @json(route('task.phase.ajax.stage-admin.stages.reorder')),
                    show: @json(route('task.phase.ajax.stage-admin.stages.show', ['leadStage' => '__STAGE_ID__'])),
                    update: @json(route('task.phase.ajax.stage-admin.stages.update', ['leadStage' => '__STAGE_ID__'])),
                    delete: @json(route('task.phase.ajax.stage-admin.stages.delete', ['leadStage' => '__STAGE_ID__'])),

                    subStore: @json(route('task.phase.ajax.stage-admin.sub-stages.store', ['leadStage' => '__STAGE_ID__'])),
                    subReorder: @json(route('task.phase.ajax.stage-admin.sub-stages.reorder', ['leadStage' => '__STAGE_ID__'])),
                    subShow: @json(route('task.phase.ajax.stage-admin.sub-stages.show', ['subStage' => '__SUB_ID__'])),
                    subUpdate: @json(route('task.phase.ajax.stage-admin.sub-stages.update', ['subStage' => '__SUB_ID__'])),
                    subDelete: @json(route('task.phase.ajax.stage-admin.sub-stages.delete', ['subStage' => '__SUB_ID__'])),
                },
            };

            function routeUrl(template, replacements) {
                let url = String(template);

                Object.keys(replacements || {}).forEach(function (key) {
                    url = url.replace(key, encodeURIComponent(replacements[key]));
                });

                return url;
            }
            function csrf() { return $('meta[name="csrf-token"]').attr('content') || @json(csrf_token()); }
            function iconRefresh() { if (window.feather) feather.replace(); }
            function esc(v) { return String(v ?? '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#039;'); }
            function notify(type, msg) { if (window.toastr && toastr[type]) toastr[type](msg); else console[type === 'error' ? 'error' : 'log'](msg); }
            function swalAsk(opts) { if (window.Swal) return Swal.fire(opts); return Promise.resolve({ isConfirmed: confirm(opts.text || opts.title || 'OK') }); }
            function apiError(xhr, fallback) { return xhr.responseJSON?.message || Object.values(xhr.responseJSON?.errors || {}).flat().join('\n') || fallback || 'Fehler'; }
            function openModal(id) { $('#' + id).addClass('is-open').attr('aria-hidden', 'false'); $('body').css('overflow', 'hidden'); setTimeout(() => $('#' + id).find('input,select,textarea,button').filter(':visible:first').focus(), 60); }
            function closeModal(id) { $('#' + id).removeClass('is-open').attr('aria-hidden', 'true'); if (!$('.ltm-modal-backdrop.is-open').length) $('body').css('overflow', ''); }
            function closeAllModals() { $('.ltm-modal-backdrop').removeClass('is-open').attr('aria-hidden', 'true'); $('body').css('overflow', ''); }

            function setupSelect2() {
                if (!$.fn.select2) return;
                if (!$('#ltmSelect2Portal').length) $('body').append('<div id="ltmSelect2Portal"></div>');
                $('.js-ltm-select2').each(function () {
                    if ($(this).hasClass('select2-hidden-accessible')) return;
                    $(this).select2({ width: '100%', dropdownParent: $('#ltmSelect2Portal'), placeholder: 'Bitte wählen', allowClear: true });
                });
            }


            function showStageAdminError(message) {
                $('#leadStageAdminError').toggleClass('is-visible', !!message).text(message || '');
            }

            function adminBool(value) {
                return value ? 'checked' : '';
            }

            function loadLeadStageAdmin() {
                showStageAdminError('');
                $('#leadStageAdminList').html('<div class="ltm-loading">Lade LeadStages...</div>');

                return $.get(routes.stageAdmin.index)
                    .done(function (res) {
                        renderLeadStageAdmin(res.stages || []);
                    })
                    .fail(function (xhr) {
                        $('#leadStageAdminList').html(
                            '<div class="ltm-empty">' + esc(apiError(xhr, 'LeadStages konnten nicht geladen werden.')) + '</div>'
                        );
                    });
            }

            function renderLeadStageAdmin(stages) {
                if (!stages.length) {
                    $('#leadStageAdminList').html('<div class="ltm-empty">Keine LeadStages vorhanden.</div>');
                    return;
                }

                const html = stages.map(function (stage) {
                    const subHtml = (stage.sub_stages || []).map(function (sub) {
                        return `
                                                    <div class="ltm-admin-sub" data-sub-stage-id="${sub.id}">
                                                        <span class="ltm-drag-handle" title="Sub-Stage ziehen"><i class="feather icon-menu"></i></span>
                                                        <input type="text" class="ltm-input js-admin-sub-name" value="${esc(sub.name)}" placeholder="Sub-Stage Name">
                                                        <input type="text" class="ltm-input js-admin-sub-key" value="${esc(sub.key)}" placeholder="Key">
                                                        <input type="color" class="ltm-input js-admin-sub-color" value="${esc(sub.color || '#93c21c')}" style="padding:5px;">
                                                        <label class="ltm-check-card"><input type="checkbox" class="js-admin-sub-active" ${adminBool(sub.is_active)}> Aktiv</label>
                                                        <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                                                            <button type="button" class="ltm-icon-btn js-save-sub-stage" title="Speichern"><i class="feather icon-save"></i></button>
                                                            <button type="button" class="ltm-icon-btn danger js-delete-sub-stage" title="Löschen"><i class="feather icon-trash"></i></button>
                                                        </div>
                                                    </div>
                                                `;
                    }).join('');

                    return `
                                                <div class="ltm-admin-stage" data-stage-id="${stage.id}">
                                                    <div class="ltm-admin-stage-head">
                                                        <span class="ltm-drag-handle" title="LeadStage ziehen"><i class="feather icon-menu"></i></span>
                                                        <input type="text" class="ltm-input js-admin-stage-name" value="${esc(stage.name)}" placeholder="LeadStage Name">
                                                        <input type="text" class="ltm-input js-admin-stage-key" value="${esc(stage.key)}" placeholder="Key">
                                                        <input type="color" class="ltm-input js-admin-stage-color" value="${esc(stage.color || '#74b2d4')}" style="padding:5px;">
                                                        <label class="ltm-check-card"><input type="checkbox" class="js-admin-stage-active" ${adminBool(stage.is_active)}> Aktiv</label>
                                                        <label class="ltm-check-card"><input type="checkbox" class="js-admin-stage-default" ${adminBool(stage.is_default)}> Default</label>
                                                        <span class="ltm-chip">${stage.usage_count || 0} Phasen</span>
                                                        <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                                                            <button type="button" class="ltm-icon-btn js-save-lead-stage" title="Speichern"><i class="feather icon-save"></i></button>
                                                            <button type="button" class="ltm-icon-btn js-add-sub-stage" title="Sub-Stage hinzufügen"><i class="feather icon-plus"></i></button>
                                                            <button type="button" class="ltm-icon-btn danger js-delete-lead-stage" title="Löschen"><i class="feather icon-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="ltm-admin-sub-list" data-stage-id="${stage.id}">
                                                        ${subHtml || '<div class="ltm-empty">Keine Sub-Stages vorhanden.</div>'}
                                                    </div>
                                                </div>
                                            `;
                }).join('');

                $('#leadStageAdminList').html(html);
                initLeadStageAdminSortables();
                iconRefresh();
            }

            function initLeadStageAdminSortables() {
                const stageList = $('#leadStageAdminList');

                if (stageList.data('ui-sortable')) {
                    stageList.sortable('destroy');
                }

                stageList.sortable({
                    items: '> .ltm-admin-stage',
                    handle: '> .ltm-admin-stage-head .ltm-drag-handle',
                    placeholder: 'ltm-placeholder',
                    forcePlaceholderSize: true,
                    stop: function () {
                        const items = stageList.children('.ltm-admin-stage').map(function () {
                            return parseInt($(this).attr('data-stage-id'), 10);
                        }).get().filter(Boolean);

                        $.ajax({
                            url: routes.stageAdmin.reorder,
                            method: 'POST',
                            data: {
                                _token: csrf(),
                                items: items
                            }
                        }).done(function (res) {
                            notify('success', res.message || 'LeadStages sortiert.');
                            loadLeadStageAdmin();
                            loadBoard();
                        }).fail(function (xhr) {
                            notify('error', apiError(xhr, 'LeadStage-Reihenfolge konnte nicht gespeichert werden.'));
                            loadLeadStageAdmin();
                        });
                    }
                });

                $('.ltm-admin-sub-list').each(function () {
                    const list = $(this);

                    if (list.data('ui-sortable')) {
                        list.sortable('destroy');
                    }

                    list.sortable({
                        items: '> .ltm-admin-sub',
                        handle: '.ltm-drag-handle',
                        placeholder: 'ltm-placeholder',
                        forcePlaceholderSize: true,
                        stop: function () {
                            const stageId = parseInt(list.attr('data-stage-id'), 10);

                            const items = list.children('.ltm-admin-sub').map(function () {
                                return parseInt($(this).attr('data-sub-stage-id'), 10);
                            }).get().filter(Boolean);

                            $.ajax({
                                url: routeUrl(routes.stageAdmin.subReorder, {
                                    '__STAGE_ID__': stageId
                                }),
                                method: 'POST',
                                data: {
                                    _token: csrf(),
                                    items: items
                                }
                            }).done(function (res) {
                                notify('success', res.message || 'Sub-Stages sortiert.');
                                loadLeadStageAdmin();
                                loadBoard();
                            }).fail(function (xhr) {
                                notify('error', apiError(xhr, 'Sub-Stage-Reihenfolge konnte nicht gespeichert werden.'));
                                loadLeadStageAdmin();
                            });
                        }
                    });
                });
            }

            function openLeadStageAdmin() {
                openModal('leadStageAdminModal');
                loadLeadStageAdmin();
            }

            $(document).on('click', '#btnOpenStageAdmin', function () {
                openLeadStageAdmin();
            });

            $(document).on('click', '#btnReloadLeadStages', function () {
                loadLeadStageAdmin();
            });

            $(document).on('click', '#btnCreateLeadStage', function () {
                showStageAdminError('');

                $.ajax({
                    url: routes.stageAdmin.store,
                    method: 'POST',
                    data: {
                        _token: csrf(),
                        name: $('#adminStageName').val(),
                        color: $('#adminStageColor').val(),
                        icon: $('#adminStageIcon').val(),
                        is_active: $('#adminStageActive').is(':checked') ? 1 : 0
                    }
                }).done(function (res) {
                    $('#adminStageName').val('');
                    $('#adminStageIcon').val('');
                    notify('success', res.message || 'LeadStage erstellt.');
                    loadLeadStageAdmin();
                    loadBoard();
                }).fail(function (xhr) {
                    showStageAdminError(apiError(xhr, 'LeadStage konnte nicht erstellt werden.'));
                });
            });

            $(document).on('click', '.js-save-lead-stage', function () {
                const card = $(this).closest('.ltm-admin-stage');
                const id = parseInt(card.attr('data-stage-id'), 10);

                $.ajax({
                    url: routeUrl(routes.stageAdmin.update, {
                        '__STAGE_ID__': id
                    }),
                    method: 'PUT',
                    data: {
                        _token: csrf(),
                        name: card.find('.js-admin-stage-name').val(),
                        key: card.find('.js-admin-stage-key').val(),
                        color: card.find('.js-admin-stage-color').val(),
                        is_active: card.find('.js-admin-stage-active').is(':checked') ? 1 : 0,
                        is_default: card.find('.js-admin-stage-default').is(':checked') ? 1 : 0
                    }
                }).done(function (res) {
                    notify('success', res.message || 'LeadStage gespeichert.');
                    loadLeadStageAdmin();
                    loadBoard();
                }).fail(function (xhr) {
                    notify('error', apiError(xhr, 'LeadStage konnte nicht gespeichert werden.'));
                });
            });

            $(document).on('click', '.js-delete-lead-stage', function () {
                const id = parseInt($(this).closest('.ltm-admin-stage').attr('data-stage-id'), 10);

                swalAsk({
                    title: 'LeadStage löschen?',
                    text: 'Die LeadStage kann nur gelöscht werden, wenn keine Phasen sie verwenden.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Löschen',
                    cancelButtonText: 'Abbrechen'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: routeUrl(routes.stageAdmin.delete, {
                            '__STAGE_ID__': id
                        }),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf() }
                    }).done(function (res) {
                        notify('success', res.message || 'LeadStage gelöscht.');
                        loadLeadStageAdmin();
                        loadBoard();
                    }).fail(function (xhr) {
                        notify('error', apiError(xhr, 'LeadStage konnte nicht gelöscht werden.'));
                    });
                });
            });

            $(document).on('click', '.js-add-sub-stage', function () {
                const stageId = parseInt($(this).closest('.ltm-admin-stage').attr('data-stage-id'), 10);
                const name = prompt('Name der neuen Sub-Stage:');

                if (!name) return;

                $.ajax({
                    url: routeUrl(routes.stageAdmin.subStore, {
                        '__STAGE_ID__': stageId
                    }),
                    method: 'POST',
                    data: {
                        _token: csrf(),
                        name: name,
                        color: '#93c21c',
                        is_active: 1
                    }
                }).done(function (res) {
                    notify('success', res.message || 'Sub-Stage erstellt.');
                    loadLeadStageAdmin();
                    loadBoard();
                }).fail(function (xhr) {
                    notify('error', apiError(xhr, 'Sub-Stage konnte nicht erstellt werden.'));
                });
            });

            $(document).on('click', '.js-save-sub-stage', function () {
                const row = $(this).closest('.ltm-admin-sub');
                const stageCard = $(this).closest('.ltm-admin-stage');
                const id = parseInt(row.attr('data-sub-stage-id'), 10);
                const stageId = parseInt(stageCard.attr('data-stage-id'), 10);

                $.ajax({
                    url: routeUrl(routes.stageAdmin.subUpdate, {
                        '__SUB_ID__': id
                    }),
                    method: 'PUT',
                    data: {
                        _token: csrf(),
                        lead_stage_id: stageId,
                        name: row.find('.js-admin-sub-name').val(),
                        key: row.find('.js-admin-sub-key').val(),
                        color: row.find('.js-admin-sub-color').val(),
                        is_active: row.find('.js-admin-sub-active').is(':checked') ? 1 : 0
                    }
                }).done(function (res) {
                    notify('success', res.message || 'Sub-Stage gespeichert.');
                    loadLeadStageAdmin();
                    loadBoard();
                }).fail(function (xhr) {
                    notify('error', apiError(xhr, 'Sub-Stage konnte nicht gespeichert werden.'));
                });
            });

            $(document).on('click', '.js-delete-sub-stage', function () {
                const id = parseInt($(this).closest('.ltm-admin-sub').attr('data-sub-stage-id'), 10);

                swalAsk({
                    title: 'Sub-Stage löschen?',
                    text: 'Die Sub-Stage kann nur gelöscht werden, wenn keine Phasen sie verwenden.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Löschen',
                    cancelButtonText: 'Abbrechen'
                }).then(function (result) {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: routeUrl(routes.stageAdmin.subDelete, {
                            '__SUB_ID__': id
                        }),
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf() }
                    }).done(function (res) {
                        notify('success', res.message || 'Sub-Stage gelöscht.');
                        loadLeadStageAdmin();
                        loadBoard();
                    }).fail(function (xhr) {
                        notify('error', apiError(xhr, 'Sub-Stage konnte nicht gelöscht werden.'));
                    });
                });
            });


            function filterSubStageOptions(stageId) {
                const select = $('#task_lead_sub_stage_id');
                select.find('option').each(function () {
                    const optionStageId = $(this).data('stage-id');
                    if (!optionStageId) { $(this).prop('hidden', false); return; }
                    $(this).prop('hidden', String(optionStageId) !== String(stageId));
                });
                const selected = select.find('option:selected');
                if (selected.data('stage-id') && String(selected.data('stage-id')) !== String(stageId)) select.val('');
            }

            function textMatches(...values) {
                const q = state.search.toLowerCase().trim();
                if (!q) return true;
                return values.join(' ').toLowerCase().includes(q);
            }

            function taskMatchesSearch(task) {
                if (textMatches(task.title, task.description)) return true;
                return (task.activities || []).some(a => activityMatchesSearch(a));
            }

            function activityMatchesSearch(activity) {
                if (textMatches(activity.title, activity.description)) return true;
                return (activity.children || []).some(child => activityMatchesSearch(child));
            }

            function filteredTasks(tasks) {
                return (tasks || []).filter(taskMatchesSearch);
            }

            function countFilteredTasks(stage) {
                return (stage.lanes || []).reduce((sum, lane) => sum + filteredTasks(lane.tasks || []).length, 0);
            }

            function renderActivity(activity, task, child) {
                if (!activityMatchesSearch(activity)) return '';
                return `
                                            <div class="ltm-activity-row ${child ? 'ltm-child-activity' : ''}" data-activity-id="${activity.id}" data-phase-id="${task.id}">
                                                <div class="ltm-activity-title js-show-activity-detail" data-activity-id="${activity.id}">
                                                    <i class="feather ${child ? 'icon-corner-down-right' : 'icon-check-circle'}"></i>
                                                    <span>${esc(activity.title)}</span>
                                                    ${activity.photo_required ? '<span class="ltm-chip red">Foto</span>' : ''}
                                                </div>
                                                <div style="display:flex;gap:4px;">
                                                    <button type="button" class="ltm-icon-btn js-create-child-activity" data-phase-id="${task.id}" data-parent-id="${activity.id}" title="Unteraktivität"><i class="feather icon-plus"></i></button>
                                                    <button type="button" class="ltm-icon-btn js-edit-activity" data-activity-id="${activity.id}" title="Bearbeiten"><i class="feather icon-edit"></i></button>
                                                    <button type="button" class="ltm-icon-btn js-clone-activity" data-activity-id="${activity.id}" title="Kopieren"><i class="feather icon-copy"></i></button>
                                                    <button type="button" class="ltm-icon-btn danger js-delete-activity" data-activity-id="${activity.id}" title="Löschen"><i class="feather icon-trash"></i></button>
                                                </div>
                                            </div>
                                            ${(activity.children || []).map(childActivity => renderActivity(childActivity, task, true)).join('')}
                                        `;
            }

            function renderTask(task) {
                if (!taskMatchesSearch(task)) return '';
                const active = task.status === 'Published';
                const selected = parseInt(state.selectedTask, 10) === parseInt(task.id, 10);
                return `
                                            <article class="ltm-task-card ${selected ? 'is-selected' : ''}" data-phase-id="${task.id}">
                                                <div class="ltm-task-main js-show-task-detail" data-phase-id="${task.id}">
                                                    <div class="ltm-task-title-row">
                                                        <div class="ltm-task-title">${esc(task.title)}</div>
                                                        <div class="ltm-task-actions">
                                                            <button type="button" class="ltm-icon-btn js-create-activity" data-phase-id="${task.id}" title="Aktivität"><i class="feather icon-plus"></i></button>
                                                            <button type="button" class="ltm-icon-btn js-edit-task" data-phase-id="${task.id}" title="Bearbeiten"><i class="feather icon-edit"></i></button>
                                                            <button type="button" class="ltm-icon-btn js-clone-task" data-phase-id="${task.id}" title="Kopieren"><i class="feather icon-copy"></i></button>
                                                            <button type="button" class="ltm-icon-btn danger js-delete-task" data-phase-id="${task.id}" title="Löschen"><i class="feather icon-trash"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="ltm-task-meta">
                                                        <span class="ltm-chip ${active ? 'green' : ''}">${active ? 'Aktiv' : 'Inaktiv'}</span>
                                                        <span class="ltm-chip">${task.activities_count || 0} Aktivitäten</span>
                                                    </div>
                                                </div>
                                                <div class="ltm-activities sortable-activities" data-phase-id="${task.id}">
                                                    ${(task.activities || []).map(activity => renderActivity(activity, task, false)).join('') || '<div class="ltm-empty" style="padding:9px;">Keine Aktivität</div>'}
                                                </div>
                                            </article>
                                        `;
            }

            function renderLane(stage, lane) {
                const laneKey = `${stage.id}:${lane.id || 'main'}`;
                const closed = state.closedLanes.has(laneKey);
                const tasks = filteredTasks(lane.tasks || []);
                if (state.search && tasks.length === 0) return '';
                return `
                                            <div class="ltm-lane ${closed ? 'is-closed' : ''}" data-lane-key="${esc(laneKey)}" data-lane-id="${lane.id || ''}">
                                                <button type="button" class="ltm-lane-head js-toggle-lane" data-lane-key="${esc(laneKey)}">
                                                    <div class="ltm-lane-title">
                                                        <span class="ltm-lane-dot" style="background:${esc(lane.color || stage.color || '#74b2d4')}"></span>
                                                        <span>${esc(lane.name || 'Hauptstage')}</span>
                                                        <span class="ltm-chip">${tasks.length}</span>
                                                    </div>
                                                    <div style="display:flex;align-items:center;gap:8px;">
                                                        <span class="ltm-accordion-chevron"><i class="feather icon-chevron-down"></i></span>
                                                    </div>
                                                </button>
                                                <div class="ltm-lane-body">
                                                    <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
                                                        <button type="button" class="ltm-btn-soft js-create-task-in-lane" data-stage-id="${stage.id}" data-sub-stage-id="${lane.id || ''}"><i class="feather icon-plus"></i> Phase</button>
                                                    </div>
                                                    <div class="ltm-task-list sortable-tasks" data-stage-id="${stage.id}" data-sub-stage-id="${lane.id || ''}">
                                                        ${tasks.map(renderTask).join('') || '<div class="ltm-empty">Keine Phasen</div>'}
                                                    </div>
                                                </div>
                                            </div>
                                        `;
            }

            function renderBoard() {
                let total = 0;
                const html = state.board.map(stage => {
                    const count = countFilteredTasks(stage);
                    total += count;
                    if (state.search && count === 0) return '';
                    const closed = state.closedStages.has(String(stage.id));
                    const lanes = (stage.lanes || []).map(lane => renderLane(stage, lane)).join('');
                    return `
                                                <section class="ltm-stage-accordion ${closed ? 'is-closed' : ''}" data-stage-id="${stage.id}">
                                                    <button type="button" class="ltm-stage-accordion-head js-toggle-stage" data-stage-id="${stage.id}">
                                                        <div class="ltm-stage-name">
                                                            <span class="ltm-stage-dot" style="background:${esc(stage.color || '#74b2d4')}"></span>
                                                            <span>${esc(stage.name)}</span>
                                                        </div>
                                                        <div class="ltm-stage-actions">
                                                            <span class="ltm-stage-count">${count}</span>
                                                            <span class="ltm-accordion-chevron"><i class="feather icon-chevron-down"></i></span>
                                                        </div>
                                                    </button>
                                                    <div class="ltm-stage-accordion-body">
                                                        ${lanes || '<div class="ltm-empty">Keine passenden Phasen</div>'}
                                                    </div>
                                                </section>
                                            `;
                }).join('');

                $('#ltmTree').html(html || '<div class="ltm-empty">Keine passenden Phasen gefunden.</div>');
                $('#ltmTotalCount').text(total + ' Phasen');
                initSortables();
                iconRefresh();
            }

            function loadBoard() {
                $('#ltmTree').html('<div class="ltm-loading">Lade Phasen...</div>');
                return $.get(routes.board, { product_id: state.productId, section_id: state.sectionId })
                    .done(res => { state.board = res.board || []; renderBoard(); })
                    .fail(xhr => { $('#ltmTree').html('<div class="ltm-empty">' + esc(apiError(xhr, 'Phasen konnten nicht geladen werden.')) + '</div>'); });
            }

            function findTask(taskId) {
                for (const stage of state.board) for (const lane of stage.lanes || []) for (const task of lane.tasks || []) if (parseInt(task.id, 10) === parseInt(taskId, 10)) return task;
                return null;
            }

            function findActivity(activityId) {
                function walk(list) {
                    for (const item of list || []) {
                        if (parseInt(item.id, 10) === parseInt(activityId, 10)) return item;
                        const found = walk(item.children || []);
                        if (found) return found;
                    }
                    return null;
                }
                for (const stage of state.board) for (const lane of stage.lanes || []) for (const task of lane.tasks || []) {
                    const found = walk(task.activities || []);
                    if (found) return found;
                }
                return null;
            }

            function renderTaskDetail(task) {
                if (!task) return;
                state.selectedTask = task.id;
                state.selectedActivity = null;
                $('#ltmDetailBody').html(`
                                            <div class="ltm-detail-section">
                                                <div class="ltm-detail-kicker">Phase</div>
                                                <div class="ltm-detail-text"><strong>${esc(task.title)}</strong></div>
                                                <div class="ltm-detail-text" style="margin-top:8px;">${esc(task.description || 'Keine Beschreibung')}</div>
                                            </div>
                                            <div class="ltm-detail-section">
                                                <div class="ltm-detail-kicker">Status</div>
                                                <div class="ltm-detail-text">${task.status === 'Published' ? 'Aktiv' : 'Inaktiv'} · ${task.activities_count || 0} Aktivitäten</div>
                                            </div>
                                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                                <button type="button" class="ltm-btn js-create-activity" data-phase-id="${task.id}"><i class="feather icon-plus"></i> Aktivität</button>
                                                <button type="button" class="ltm-btn-soft js-edit-task" data-phase-id="${task.id}"><i class="feather icon-edit"></i> Bearbeiten</button>
                                                <button type="button" class="ltm-btn-soft js-clone-task" data-phase-id="${task.id}"><i class="feather icon-copy"></i> Kopieren</button>
                                            </div>
                                        `);
                renderBoard();
                iconRefresh();
            }

            function renderActivityDetail(activity) {
                if (!activity) return;
                state.selectedActivity = activity.id;
                $('#ltmDetailBody').html(`
                                            <div class="ltm-detail-section">
                                                <div class="ltm-detail-kicker">Aktivität</div>
                                                <div class="ltm-detail-text"><strong>${esc(activity.title)}</strong></div>
                                                <div class="ltm-detail-text" style="margin-top:8px;">${esc(activity.description || 'Keine Beschreibung')}</div>
                                            </div>
                                            <div class="ltm-detail-section">
                                                <div class="ltm-detail-kicker">Planung</div>
                                                <div class="ltm-detail-text">Dauer: ${esc(activity.duration || 0)} ${esc(activity.duration_type || '')}</div>
                                                <div class="ltm-detail-text">Foto erforderlich: ${activity.photo_required ? 'Ja' : 'Nein'}</div>
                                                <div class="ltm-detail-text">Status: ${activity.status === 'Published' ? 'Aktiv' : 'Inaktiv'}</div>
                                            </div>
                                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                                <button type="button" class="ltm-btn-soft js-edit-activity" data-activity-id="${activity.id}"><i class="feather icon-edit"></i> Bearbeiten</button>
                                                <button type="button" class="ltm-btn-soft js-clone-activity" data-activity-id="${activity.id}"><i class="feather icon-copy"></i> Kopieren</button>
                                            </div>
                                        `);
                iconRefresh();
            }

            function resetTaskForm() { $('#taskForm')[0].reset(); $('#task_id').val(''); $('#taskError').removeClass('is-visible').text(''); filterSubStageOptions(''); }
            function openTaskForm(task, defaults) {
                resetTaskForm();
                $('#taskModalTitle').text(task ? 'Phase bearbeiten' : 'Neue Phase');
                if (task) {
                    $('#task_id').val(task.id);
                    $('#task_title').val(task.title);
                    $('#task_description').val(task.description || '');
                    $('#task_status').val(task.status || 'Published');
                    $('#task_lead_stage_id').val(task.lead_stage_id);
                    filterSubStageOptions(task.lead_stage_id);
                    $('#task_lead_sub_stage_id').val(task.lead_sub_stage_id || '');
                } else if (defaults) {
                    $('#task_lead_stage_id').val(defaults.stageId || '');
                    filterSubStageOptions(defaults.stageId || '');
                    $('#task_lead_sub_stage_id').val(defaults.subStageId || '');
                }
                openModal('taskModal');
            }

            function resetActivityForm() { $('#activityForm')[0].reset(); $('#activity_id').val(''); $('#activity_parent_id').val(''); $('#activityError').removeClass('is-visible').text(''); $('.js-ltm-select2').val(null).trigger('change'); }
            function openActivityForm(activity, defaults) {
                resetActivityForm();
                $('#activityModalTitle').text(activity ? 'Aktivität bearbeiten' : 'Neue Aktivität');
                if (activity) {
                    $('#activity_id').val(activity.id);
                    $('#activity_phase_id').val(activity.phase_id);
                    $('#activity_parent_id').val(activity.parent_id || '');
                    $('#activity_title').val(activity.title || '');
                    $('#activity_description').val(activity.description || '');
                    $('#activity_duration').val(activity.duration || '');
                    $('#activity_duration_type').val(activity.duration_type || 'minutes');
                    $('#activity_status').val(activity.status || 'Published');
                    $('#activity_photo_required').prop('checked', !!activity.photo_required);
                    $('#activity_qualification_ids').val(activity.qualification_ids || []).trigger('change');
                    $('#activity_department_ids').val(activity.department_ids || []).trigger('change');
                    $('#activity_article_ids').val(activity.article_ids || []).trigger('change');
                } else if (defaults) {
                    $('#activity_phase_id').val(defaults.phaseId || '');
                    $('#activity_parent_id').val(defaults.parentId || '');
                }
                openModal('activityModal');
            }

            function postMoveTask(phaseId, payload) {
                return $.post(routes.taskMoveParamBase + phaseId + '/move', payload)
                    .catch(xhr => xhr.status === 404 ? $.post(routes.taskMoveFlat, payload) : $.Deferred().reject(xhr).promise());
            }

            function initSortables() {
                $('.sortable-tasks').each(function () {
                    const list = $(this);
                    if (list.data('ui-sortable')) return;
                    list.sortable({
                        items: '> .ltm-task-card',
                        connectWith: '.sortable-tasks',
                        placeholder: 'ltm-placeholder',
                        forcePlaceholderSize: true,
                        tolerance: 'pointer',
                        scroll: true,
                        start: function (e, ui) { ui.item.data('fromList', ui.item.parent()); },
                        stop: function (e, ui) {
                            const toList = ui.item.parent();
                            const fromList = ui.item.data('fromList');
                            const phaseId = ui.item.data('phase-id');
                            const stageId = toList.data('stage-id');
                            const subStageId = toList.data('sub-stage-id') || '';
                            const ordered = toList.children('.ltm-task-card').map(function () { return $(this).data('phase-id'); }).get();

                            if (toList.is(fromList)) {
                                $.post(routes.taskReorder, { _token: csrf(), lead_stage_id: stageId, lead_sub_stage_id: subStageId, phase_ids: ordered })
                                    .fail(xhr => notify('error', apiError(xhr, 'Sortierung fehlgeschlagen.')));
                                return;
                            }

                            postMoveTask(phaseId, { _token: csrf(), phase_id: phaseId, lead_stage_id: stageId, lead_sub_stage_id: subStageId, target_index: ui.item.index() })
                                .done(() => loadBoard())
                                .fail(xhr => { notify('error', apiError(xhr, 'Verschieben fehlgeschlagen.')); loadBoard(); });
                        }
                    });
                });
            }

            $(document).on('click', '[data-close-ltm-modal]', function () { closeAllModals(); });
            $(document).on('click', '.ltm-modal-backdrop', function (e) { if (e.target === this) closeModal(this.id); });
            $(document).on('keydown', function (e) { if (e.key === 'Escape') closeAllModals(); });

            $(document).on('click', '.js-toggle-stage', function (e) {
                if ($(e.target).closest('button:not(.js-toggle-stage)').length) return;
                const id = String($(this).data('stage-id'));
                state.closedStages.has(id) ? state.closedStages.delete(id) : state.closedStages.add(id);
                renderBoard();
            });

            $(document).on('click', '.js-toggle-lane', function () {
                const key = String($(this).data('lane-key'));
                state.closedLanes.has(key) ? state.closedLanes.delete(key) : state.closedLanes.add(key);
                renderBoard();
            });

            $('#btnExpandAll').on('click', function () { state.closedStages.clear(); state.closedLanes.clear(); renderBoard(); });
            $('#btnCollapseAll').on('click', function () {
                state.board.forEach(stage => {
                    state.closedStages.add(String(stage.id));
                    (stage.lanes || []).forEach(lane => state.closedLanes.add(`${stage.id}:${lane.id || 'main'}`));
                });
                renderBoard();
            });

            $('#btnCreateTask').on('click', () => openTaskForm(null, null));
            $('#btnReloadBoard').on('click', () => loadBoard());
            $('#ltmSearch').on('input', function () { state.search = $(this).val(); renderBoard(); });
            $('#ltmSearchClear').on('click', function () { $('#ltmSearch').val(''); state.search = ''; renderBoard(); });
            $('#task_lead_stage_id').on('change', function () { filterSubStageOptions($(this).val()); });

            $(document).on('click', '.js-create-task-in-lane', function (e) { e.stopPropagation(); openTaskForm(null, { stageId: $(this).data('stage-id'), subStageId: $(this).data('sub-stage-id') || '' }); });
            $(document).on('click', '.js-edit-task', function (e) { e.stopPropagation(); openTaskForm(findTask($(this).data('phase-id')), null); });
            $(document).on('click', '.js-show-task-detail', function (e) { if ($(e.target).closest('button').length) return; renderTaskDetail(findTask($(this).data('phase-id'))); });
            $(document).on('click', '.js-create-activity', function (e) { e.stopPropagation(); openActivityForm(null, { phaseId: $(this).data('phase-id') }); });
            $(document).on('click', '.js-create-child-activity', function (e) { e.stopPropagation(); openActivityForm(null, { phaseId: $(this).data('phase-id'), parentId: $(this).data('parent-id') }); });
            $(document).on('click', '.js-edit-activity', function (e) { e.stopPropagation(); openActivityForm(findActivity($(this).data('activity-id')), null); });
            $(document).on('click', '.js-show-activity-detail', function (e) { e.stopPropagation(); renderActivityDetail(findActivity($(this).data('activity-id'))); });

            $('#taskForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#task_id').val();
                const url = id ? routes.taskUpdateBase + id : routes.taskStore;
                $.ajax({ url, method: 'POST', data: $(this).serialize(), headers: { 'X-CSRF-TOKEN': csrf() } })
                    .done(res => { closeAllModals(); notify('success', res.message || 'Gespeichert.'); loadBoard(); })
                    .fail(xhr => $('#taskError').addClass('is-visible').text(apiError(xhr, 'Speichern fehlgeschlagen.')));
            });

            $('#activityForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#activity_id').val();
                const url = id ? routes.activityUpdateBase + id : routes.activityStore;
                $.ajax({ url, method: 'POST', data: $(this).serialize(), headers: { 'X-CSRF-TOKEN': csrf() } })
                    .done(res => { closeAllModals(); notify('success', res.message || 'Gespeichert.'); loadBoard(); })
                    .fail(xhr => $('#activityError').addClass('is-visible').text(apiError(xhr, 'Speichern fehlgeschlagen.')));
            });

            $(document).on('click', '.js-delete-task', function (e) {
                e.stopPropagation();
                const id = $(this).data('phase-id');
                swalAsk({ title: 'Phase löschen?', text: 'Alle Aktivitäten dieser Phase werden gelöscht.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Löschen', cancelButtonText: 'Abbrechen' })
                    .then(result => { if (!result.isConfirmed) return; $.ajax({ url: routes.taskDeleteBase + id, method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf() } }).done(res => { notify('success', res.message); loadBoard(); }).fail(xhr => notify('error', apiError(xhr, 'Löschen fehlgeschlagen.'))); });
            });

            $(document).on('click', '.js-clone-task', function (e) {
                e.stopPropagation();
                $.post(routes.taskCloneBase + $(this).data('phase-id') + '/clone', { _token: csrf() }).done(res => { notify('success', res.message); loadBoard(); }).fail(xhr => notify('error', apiError(xhr, 'Kopieren fehlgeschlagen.')));
            });

            $(document).on('click', '.js-delete-activity', function (e) {
                e.stopPropagation();
                const id = $(this).data('activity-id');
                swalAsk({ title: 'Aktivität löschen?', text: 'Unteraktivitäten werden ebenfalls gelöscht.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Löschen', cancelButtonText: 'Abbrechen' })
                    .then(result => { if (!result.isConfirmed) return; $.ajax({ url: routes.activityDeleteBase + id, method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf() } }).done(res => { notify('success', res.message); loadBoard(); }).fail(xhr => notify('error', apiError(xhr, 'Löschen fehlgeschlagen.'))); });
            });

            $(document).on('click', '.js-clone-activity', function (e) {
                e.stopPropagation();
                $.post(routes.activityCloneBase + $(this).data('activity-id') + '/clone', { _token: csrf() }).done(res => { notify('success', res.message); loadBoard(); }).fail(xhr => notify('error', apiError(xhr, 'Kopieren fehlgeschlagen.')));
            });

            $(function () {
                setupSelect2();
                loadBoard();
                iconRefresh();
            });
        })(jQuery);
    </script>
@endsection