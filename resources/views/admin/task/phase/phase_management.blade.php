 @extends('admin.layouts.app')

@section('title') Arbeitsschritte Details @stop

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">

<style>
    /* PAGE SHELL */
    .phase-layout-shell {
        background: radial-gradient(circle at top left, #f1f5f9 0, #ffffff 45%, #f9fafb 100%);
        min-height: calc(100vh - 120px);
        padding: 8px 0 24px;
    }

    .phase-card-shell {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        padding: 16px;
    }

    @media (min-width: 992px) {
        .phase-card-shell {
            padding: 20px 22px;
        }
    }

    /* HEADER */
    .phase-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px 16px;
        margin-bottom: 10px;
    }

    .phase-page-title {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: #111827;
    }

    .phase-page-subtitle {
        font-size: 12px;
        color: #6b7280;
    }

    /* LEFT SIDEBAR: STAGE / PHASE TREE */
    .phase-sidebar {
        border-radius: 16px;
        background: linear-gradient(135deg, #f1f5f9, #ffffff);
        border: 1px solid #e5e7eb;
        padding: 10px 10px 12px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .phase-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .phase-sidebar-title {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #6b7280;
    }

    .phase-sidebar-meta {
        font-size: 11px;
        color: #9ca3af;
    }

    .scrollable-container {
        max-height: calc(100vh - 220px);
        overflow-y: auto;
        padding-right: 4px;
    }

    .scrollable-container::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        background-color: rgba(148, 163, 184, 0.7);
        border-radius: 999px;
    }

    /* CARDS INSIDE SIDEBAR (STAGE GROUPS) */
    .stage-card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        margin-bottom: 6px;
        overflow: hidden;
        background-color: #ffffff;
    }

    .stage-card-header {
        cursor: pointer;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: linear-gradient(90deg, #f9fafb, #f3f4f6);
        border-bottom: 1px solid #e5e7eb;
    }

    .stage-name {
        font-size: 12px;
        font-weight: 600;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .stage-badge {
        font-size: 10px;
        border-radius: 999px;
        padding: 2px 6px;
        background: rgba(59, 130, 246, 0.08);
        color: #2563eb;
        font-weight: 500;
    }

    .stage-chevron {
        font-size: 14px;
        color: #9ca3af;
    }

    /* PHASE ROWS IN SIDEBAR */
    .sortable-phases {
        padding: 4px 0;
    }

    .phase-item-row {
        border-bottom: 1px dashed #e5e7eb;
    }

    .phase-item-row:last-child {
        border-bottom: none;
    }

    .folder-toggle {
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        position: relative;
        transition: background 0.15s ease, box-shadow 0.15s ease;
    }

    .folder-toggle:hover {
        background: #f9fafb;
    }

    .folder-toggle.active {
        background: #eef2ff;
        box-shadow: inset 2px 0 0 #4f46e5;
    }

    .folder-icon {
        width: 20px;
        text-align: center;
        font-weight: 700;
        font-size: 16px;
        color: #9ca3af;
    }

    .folder-label {
        font-size: 12px;
        font-weight: 500;
        color: #111827;
    }

    .folder-label.heading {
        font-size: 12px;
        font-weight: 600;
        color: #93c21c;
    }

    .total-sub-tasks {
        margin-left: auto;
        font-size: 11px;
        color: #6b7280;
    }

    .button-container {
        display: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        position: absolute;
        right: 6px;
        top: 3px;
        display: flex;
        gap: 4px;
    }

    .folder-toggle:hover .button-container,
    .folder-toggle.active .button-container {
        opacity: 1;
    }

    .subfolder {
        margin-left: 20px;
        padding-left: 4px;
        border-left: 1px dashed #e5e7eb;
    }

    @media (max-width: 576px) {
        .subfolder {
            margin-left: 10px;
        }
    }

    /* BADGES */
    .badge {
        padding: 2px 6px;
        font-size: 10px;
        font-weight: 600;
        border-radius: 999px;
    }

    .badge-success {
        background-color: #22c55e;
        color: #fff;
    }

    .badge-secondary {
        background-color: #6b7280;
        color: #fff;
    }

    .badge-primary {
        background-color: #3b82f6;
        color: #fff;
    }

    /* RIGHT PANEL: TABLE CARD */
        .phase-detail-card {
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            box-shadow: 0 8px 28px rgba(15,23,42,0.05);
            padding: 10px 10px 14px;
            height: 100%;
            display: flex;
            flex-direction: column;
            min-width: 0;               /* IMPORTANT for flex children */
        }

        .sortable-placeholder{
            height: 38px;
            margin: 4px 8px;
            border: 2px dashed #93c5fd;
            background: rgba(59,130,246,0.08);
            border-radius: 10px;
            }

            .sortable-phases, .sortable-activities{
            min-height: 18px; /* allow dropping into empty lists */
            }


    .phase-detail-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 8px 12px;
        margin-bottom: 8px;
    }

    .phase-detail-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .phase-detail-meta {
        font-size: 11px;
        color: #6b7280;
    }

    .table-responsive {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        display: block;
        width: 100%;
        max-width: 100%;
        overflow-x: auto;           /* allow horizontal scroll */
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
    }

    #detailed_table thead {
        background: #f3f4f6;
    }

    #detailed_table thead th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280 !important;
        border-bottom-width: 1px !important;
        white-space: nowrap;
    }

    #detailed_table tbody td {
        font-size: 12px;
        vertical-align: middle;
    }

    @media (max-width: 991.98px) {
        .phase-card-shell {
            padding: 12px;
        }
        .phase-sidebar {
            margin-bottom: 10px;
        }
        .table-responsive {
            max-height: 420px;
            overflow-y: auto;      /* vertical scroll as well */
        }
    }


    /* MINIMIZE BUTTON */
    .minimize-button {
        border-radius: 999px !important;
        padding: 4px 8px !important;
    }
</style>

<style>
    /* ... Your existing styles ... */
    .phase-layout-shell { background: radial-gradient(circle at top left, #f3f4f6 0, #ffffff 45%, #f9fafb 100%); min-height: calc(100vh - 120px); padding: 8px 0 24px; }
    
    /* MASTER SET DRAWER STYLES */
    .master-set-drawer {
        position: fixed;
        top: 0; right: 0; bottom: 0; left: 0;
        z-index: 999999 !important; /* Force on top of everything */
        visibility: hidden;
        pointer-events: none;
    }

    .master-set-drawer.open {
        visibility: visible;
        pointer-events: auto;
    }

    .master-set-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .master-set-drawer.open .master-set-overlay { opacity: 1; }

    .master-set-panel {
        position: absolute;
        top: 0; right: 0; height: 100%; width: 1000px;
        max-width: 90vw;
        background: #ffffff;
        box-shadow: -5px 0 25px rgba(0,0,0,0.2);
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex; flex-direction: column;
        z-index: 1000000;
    }

    .master-set-drawer.open .master-set-panel { transform: translateX(0); }

    .master-set-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; }
    .master-set-body { padding: 0; overflow-y: auto; flex: 1 1 auto; }

    .set-item-btn { width: 100%; text-align: left; padding: 15px 20px; border: none; background: #fff; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.15s; }
    .set-item-btn:hover { background: #f0f9ff; }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper phase-layout-shell">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ARBEITSSCHRITTE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                                </li>
                                 
                                <li class="breadcrumb-item">
                                    <a href="{{ url('task_phase' ) }}">
                                        Arbeitsschritte
                                    </a>
                                </li>

                                 <li class="breadcrumb-item active"> 
                                        Liste 
                                </li> 
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body phase-card-shell">
            @if ($errors->any())
                <div class="alert alert-danger mb-1">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 12px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- PAGE HEADER LINE --}}
            <div class="phase-page-header">
                <div>
                    <div class="phase-page-title">Arbeitsschritte</div>
                    <div class="phase-page-subtitle">
                        Phasen & Aktivitäten je Stage und Version verwalten.
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-end">
                    <button type="button"
                            class="btn btn-icon btn-outline-primary mr-1 mb-1 waves-effect waves-light minimize-button">
                        <i class="feather icon-minus"></i>
                    </button>
                </div>
            </div>

            {{-- SEARCH + VERSION --}}
            <section id="basic-horizontal-layouts">
                <div class="row align-items-center mb-1">
                    <div class="col-md-5 mb-1 mb-md-0">
                        <form action="{{ action('App\Http\Controllers\TaskPhaseController@index') }}">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Phase oder Produkt suchen" aria-describedby="search-btn">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit" id="search-btn">Go</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-7 d-flex flex-wrap justify-content-end align-items-center">
                        <div class="mr-1 mb-1">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#primary">
                                Neue Phase
                            </button>
                        </div>

                        <input type="hidden" id="filter_product_id" value="{{ request()->product }}">
                        <input type="hidden" id="currentVersion" value="{{ $currentVersion ?? '' }}">
                        <input type="hidden" name="section_id" id="section_id" value="{{ request()->section_id }}">

                        <div class="stage-button">
                            <div class="form-group row mb-0">
                                <label class="col-md-4 col-form-label col-form-label-sm text-right">Version</label>
                                <div class="col-md-8">
                                    <select name="version" id="version_id" class="form-control form-control-sm select2">
                                        <option value="">-- Bitte wählen --</option>
                                        @foreach ($groupedStages as $version => $stagesInVersion)
                                            <option value="{{ $version }}">
                                                Version: {{ $version }} ({{ $stagesInVersion->count() }} Stages)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL: NEUE PHASE --}}
                @include('admin.task.phase.partials.modal-create-phase', [
                    'section'       => $section,
                    'groupedStages' => $groupedStages
                ])

                {{-- COPY MODAL --}}
                @include('admin.task.phase.partials.modal-copy-activities')

                {{-- MAIN TWO-COLUMN LAYOUT --}}
                <div class="row match-height">
                    {{-- LEFT SIDEBAR: TREE --}}
                    <div class="col-lg-4 col-md-5 col-12 side-bar mb-1 mb-md-0">
                        <div class="phase-sidebar">
                            <div class="phase-sidebar-header">
                                <div>
                                    <div class="phase-sidebar-title">Stages & Phasen</div>
                                    <div class="phase-sidebar-meta">
                                        Produkt #{{ $section->product_id }} · Sektion: {{ $translatePhase[$section->phase_section] ?? $section->phase_section }}
                                    </div>
                                </div>
                                <div class="badge badge-primary">
                                    {{ $phases->count() }} Phasen
                                </div>
                            </div>

                            <div id="folderStructure" class="scrollable-container">
                                @include('admin.task.phase.partials.folder-structure-initial', [
                                    'groupedStages'  => $groupedStages,
                                    'phases'         => $phases ?? collect(),
                                    'currentVersion' => $currentVersion
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT PANEL: ACTIVITIES TABLE --}}
                    <div class="col-lg-8 col-md-7 col-12 activity-page" id="activities">
                        <div class="phase-detail-card">
                            <div class="phase-detail-header">
                                <div>
                                    <div class="phase-detail-title">
                                        Aktivitäten-Details
                                    </div>
                                    <div class="phase-detail-meta">
                                        Klicken Sie links auf eine Phase oder Aktivität, um Details hier zu sehen.
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped mb-0" id="detailed_table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Titel</th>
                                            <th>Beschreibung</th>
                                            <th>Produkt</th>
                                            <th>Leistung</th>
                                            <th>Abteilung</th>
                                            <th>Qualifikation</th>
                                            <th>Artikel</th>
                                            <th>Phase</th>
                                            <th>Hinweis</th>
                                            <th>Dauer</th>
                                            <th>Status</th>
                                            <th>Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Aktivitäten werden per AJAX geladen --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="masterSetDrawer" class="master-set-drawer">
                    <div class="master-set-overlay" id="masterSetDrawerOverlay"></div>
                    <div class="master-set-panel">
                        <div class="master-set-header">
                            <h5 class="master-set-title mb-0 font-weight-bold">Master Set verknüpfen</h5>
                            <button type="button" class="close" id="masterSetDrawerClose" style="font-size: 2rem;">&times;</button>
                        </div>
                        <div class="master-set-body" id="master-set-list-container"></div>
                    </div>
                </div>


                @php
                    $user = DB::table('employees')
                        ->select('name', 'lastname', 'image')
                        ->where('id', auth()->user()->name)
                        ->first();
                    $creator = $user->name . ' '. $user->lastname;
                @endphp

                {{-- ACTIVITY MODAL --}}
                @include('admin.task.phase.partials.modal-activity', [
                    'user'        => $user,
                    'departments' => $departments,
                    'positions'   => $positions,
                    'articles'    => $articles
                ])

            </section>
        </div>
    </div>

    {{-- EDIT PHASE MODAL --}}
    @include('admin.task.phase.partials.modal-edit-phase', [
        'groupedStages' => $groupedStages
    ])
</div>


@endsection


 @section('script')

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script> <!-- REQUIRED for sortable -->

<script>
/**
 * Single, conflict-free script bootstrap:
 * - Keeps ALL your features
 * - Prevents double-binding
 * - Makes loadStagesAndPhases truly global
 * - Avoids "reload phases on every reload" by only autoloading if folderStructure is empty
 */
(function () {
    if (window.__PHASE_PAGE_BOOTSTRAPPED__) return;
    window.__PHASE_PAGE_BOOTSTRAPPED__ = true;

    // -----------------------------
    // Helpers
    // -----------------------------
    function csrf() {
        return $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
    }

    function isSelect2($el) {
        return $el && $el.length && $el.hasClass('select2-hidden-accessible');
    }

    function safeSelect2($el, opts) {
        if (!$el || !$el.length) return;
        if (!isSelect2($el)) $el.select2(opts || {});
    }

    function folderStructureLooksEmpty() {
        const $fs = $('#folderStructure');
        if (!$fs.length) return true;
        const html = ($fs.html() || '').trim();
        if (!html) return true;
        // common placeholder states
        if (html.includes('Bitte wählen Sie eine Version')) return true;
        if (html.includes('⚠️ Fehler beim Laden')) return true;
        return false;
    }

    // -----------------------------
    // Folder collapse + icons (works after AJAX too)
    // -----------------------------
    function wireFolderToggles(scope) {
        const root = scope || document;

        // bind collapse icon updates per collapse element (only once)
        root.querySelectorAll('.folder-toggle').forEach(toggle => {
            if (toggle.dataset.wired === '1') return;
            toggle.dataset.wired = '1';

            const targetId = toggle.getAttribute('data-target') || toggle.getAttribute('data-bs-target');
            if (!targetId) return;

            const collapseEl = root.querySelector(targetId);
            if (!collapseEl) return;

            const icon = toggle.querySelector('.folder-icon');

            // update icon when collapse changes
            if (icon && !collapseEl.dataset.iconWired) {
                collapseEl.dataset.iconWired = '1';

                collapseEl.addEventListener('show.bs.collapse', () => { icon.textContent = '−'; });
                collapseEl.addEventListener('hide.bs.collapse', () => { icon.textContent = '+'; });

                // initial state
                icon.textContent = collapseEl.classList.contains('show') ? '−' : '+';
            }

            // click handler (manual bootstrap collapse)
            toggle.addEventListener('click', (e) => {
                // allow inner buttons/links to work without toggling
                const prevent = e.target.closest('button,a,input,select,textarea,label');
                if (prevent) return;

                if (collapseEl.classList.contains('show')) {
                    new bootstrap.Collapse(collapseEl, { toggle: false }).hide();
                } else {
                    new bootstrap.Collapse(collapseEl, { toggle: false }).show();
                }
            });
        });
    }

    // delegated hover active state (keeps your behavior; works after AJAX)
    function wireFolderHoverActive() {
        let currentActive = null;

        $('#folderStructure')
            .off('mouseenter.folderActive', '.folder-toggle')
            .on('mouseenter.folderActive', '.folder-toggle', function () {
                if (currentActive && currentActive !== this) $(currentActive).removeClass('active');
                $(this).addClass('active');
                currentActive = this;
            });
    }
   
    function askMoveOrDuplicate(title, text) {
        return Swal.fire({
            title,
            text,
            icon: 'question',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: 'Move',
            denyButtonText: 'Duplicate',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(res => {
            if (res.isConfirmed) return 'move';
            if (res.isDenied) return 'duplicate';
            return null;
        });
        }


    // -----------------------------
    // Sortables (phases + activities)
    // -----------------------------
   function initSortables(scope) {
        const $scope = scope ? $(scope) : $(document);

        function reloadTree() {
            const productId = $('#filter_product_id').val();
            const sectionId = $('#section_id').val();
            const version   = $('#version_id').val() || $('#currentVersion').val();
            window.loadStagesAndPhases(version, productId, sectionId);
        }

        // -------- ACTIVITIES (cross-phase) ----------
        $scope.find('.sortable-activities').each(function () {
            const $list = $(this);
            if ($list.data('ui-sortable')) return;

            $list.sortable({
            handle: '.folder-label',
            items: '> .sortable-item',
            connectWith: '.sortable-activities',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            scroll: true,

            start: function (e, ui) {
                ui.item.data('__fromList', ui.item.parent());
                ui.item.data('__fromPhaseId', ui.item.parent().data('phase-id'));
            },

            stop: async function (e, ui) {
                const $toList   = ui.item.parent();
                const $fromList = $(ui.item.data('__fromList'));

                const activityId  = parseInt(ui.item.data('activity-id'), 10);
                const fromPhaseId = parseInt(ui.item.data('__fromPhaseId'), 10);
                const toPhaseId   = parseInt($toList.data('phase-id'), 10);

                if (!activityId || !toPhaseId) {
                return reloadTree();
                }

                // SAME PHASE => order only
                if ($toList.is($fromList)) {
                const orderedIds = $toList.children('.sortable-item').map(function () {
                    return $(this).data('activity-id');
                }).get();

                return $.post('{{ route("phase.task.activity.order") }}', {
                    _token: csrf(),
                    phase_id: toPhaseId,
                    activity_ids: orderedIds
                }).fail(() => Swal.fire('Fehler', 'Aktualisierung fehlgeschlagen.', 'error'));
                }

                // CROSS PHASE => ask + transfer
                const targetIndex = ui.item.index();
                const mode = await askMoveOrDuplicate('Aktivität verschieben?', 'Move oder Duplicate?');

                if (!mode) return reloadTree();

                $.ajax({
                url: '{{ route("task.activity.transfer") }}',
                method: 'POST',
                data: {
                    _token: csrf(),
                    mode,
                    activity_id: activityId,
                    from_phase_id: fromPhaseId,
                    to_phase_id: toPhaseId,
                    target_index: targetIndex,
                    target_parent_id: null
                }
                }).done(() => reloadTree())
                .fail((xhr) => {
                    Swal.fire('Fehler', xhr.responseJSON?.message || 'Transfer fehlgeschlagen.', 'error');
                    reloadTree();
                });
            }
            });
        });

        // -------- PHASES (cross-stage) ----------
        $scope.find('.sortable-phases').each(function () {
            const $list = $(this);
            if ($list.data('ui-sortable')) return;

            $list.sortable({
            handle: '.folder-label.heading',
            items: '> .phase-item',
            connectWith: '.sortable-phases',
            placeholder: 'sortable-placeholder',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            scroll: true,

            start: function (e, ui) {
                ui.item.data('__fromList', ui.item.parent());
            },

            stop: async function (e, ui) {
                const $toList   = ui.item.parent();
                const $fromList = $(ui.item.data('__fromList'));

                const phaseId   = parseInt(ui.item.data('phase-id'), 10);
                const toStageId = parseInt($toList.data('stage-id'), 10);

                if (!phaseId || !toStageId) {
                return reloadTree();
                }

                // SAME STAGE => order only
                if ($toList.is($fromList)) {
                const orderedIds = $toList.children('.phase-item').map(function () {
                    return $(this).data('phase-id');
                }).get();

                return $.post('{{ route("task.phase.updateOrder") }}', {
                    _token: csrf(),
                    order: orderedIds
                }).fail(() => Swal.fire('Fehler', 'Sortierung konnte nicht gespeichert werden.', 'error'));
                }

                // CROSS STAGE => ask + transfer
                const targetIndex = ui.item.index();
                const mode = await askMoveOrDuplicate('Phase verschieben?', 'Move oder Duplicate?');

                if (!mode) return reloadTree();

                $.ajax({
                url: '{{ route("task.phase.transfer") }}',
                method: 'POST',
                data: {
                    _token: csrf(),
                    mode,
                    phase_id: phaseId,
                    to_stage_id: toStageId,
                    target_index: targetIndex
                }
                }).done(() => reloadTree())
                .fail((xhr) => {
                    Swal.fire('Fehler', xhr.responseJSON?.message || 'Transfer fehlgeschlagen.', 'error');
                    reloadTree();
                });
            }
            });
        });
        }

    // -----------------------------
    // Global loader (must exist BEFORE any usage)
    // -----------------------------
    window.loadStagesAndPhases = function (version, productId, sectionId) {
        Swal.fire({
            title: 'Lade Phasen...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '/get-phases-by-version',
            method: 'GET',
            data: { version: version, product_id: productId, section_id: sectionId },
            success: function (html) {
                Swal.close();
                $('#folderStructure').html(html);

                // rewire toggles/icons + hover + sortables after DOM replacement
                wireFolderToggles(document);
                wireFolderHoverActive();
                initSortables(document);
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler beim Laden',
                    text: 'Die Phasen konnten nicht geladen werden. Bitte versuchen Sie es erneut.'
                });
                $('#folderStructure').html('<div class="text-danger p-2">⚠️ Fehler beim Laden der Phasen.</div>');
            }
        });
    };

    // -----------------------------
    // Globals used by inline onclick
    // -----------------------------
    window.showPhaseModal = function () {
        $('#phaseForm')[0].reset();
        $('#phase_id').val('');
        $('#phaseModal').modal('show');
    };

    window.showActivityModal = function (button) {
        const $btn = $(button);

        $('#activityForm')[0].reset();

        const productId   = $btn.data('product-id');
        const sectionId   = $btn.data('section-id');
        const sectionName = $btn.data('section-name');
        const phaseId     = $btn.data('phase-id');
        const parentId    = $btn.data('parent-id') || '';

        $('#activityModal #product_id').val(productId);
        $('#activityModal #section_id').val(sectionId);
        $('#activityModal #section_name').val(sectionName);
        $('#activityModal #phase_id').val(phaseId);
        $('#activityModal #parent_id').val(parentId);

        $('#activityModal input[name="title"]').val('');
        $('#activityModal input[name="duration"]').val('');
        $('#activityModal textarea[name="description"]').val('');
        $('#activityModal input[name="photo"]').prop('checked', false);
        $('#activityModal select[name="answered_by"]').val('2');
        $('#activityModal input[name="link"]').val('');
        $('#activityModal textarea[name="note"]').val('');

        $('#activityModal select[name="department_id[]"]').val(null).trigger('change');
        $('#activityModal select[name="position_id[]"]').val(null).trigger('change');
        $('#activityModal select[name="article_id[]"]').val(null).trigger('change');

        $('#activityModal').attr('aria-hidden', 'false');
        $('#activityModal').modal('show');
    };

    window.editActivity = function (id) {
        $.ajax({
            url: `/get/phase/activity/${id}`,
            method: 'GET',
            success: function (response) {
                let data = response.data;

                $('#activity_id').val(id);
                $('input[name="title"]').val(data.title);
                $('input[name="duration"]').val(data.duration);
                $('textarea[name="description"]').val(data.description);
                $('select[name="answered_by"]').val(data.answered_by);

                $('input[name="product_id"]').val(data.product_id ?? '');
                $('input[name="parent_id"]').val(data.parent_id ?? '');
                $('input[name="phase_id"]').val(data.phase_id ?? '');
                $('input[name="section_id"]').val(data.section_id ?? '');
                $('input[name="section_name"]').val(data.section_name ?? '');
                $('input[name="photo"]').prop('checked', data.photo === 'needed');
                $('input[name="link"]').val(data.link || '');
                $('textarea[name="note"]').val(data.note || '');

                $('select[name="department_id[]"]').val(data.department_ids ?? []).trigger('change');
                $('select[name="position_id[]"]').val(data.position_ids ?? []).trigger('change');
                $('select[name="article_id[]"]').val(data.article_ids ?? []).trigger('change');

                $('#activityModal').modal('show');
            }
        });
    };

    window.activePhase = function (btn) {
        const phaseId = btn.dataset.phaseId;

        fetch(`/phase/${phaseId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf()
            },
            body: JSON.stringify({ id: phaseId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status geändert',
                    text: `Phase ist jetzt ${data.status === 'Published' ? 'Aktiv' : 'Inaktiv'}`,
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: data.message || 'Status konnte nicht geändert werden.'
                });
            }
        });
    };

    // -----------------------------
    // Render helpers for table
    // -----------------------------
    function translateSectionName(sectionName) {
        const translations = {
            'complete': 'Komplettlösung',
            'montage': 'Montage',
            'product': 'Produkt',
            'plan': 'Planung',
            'maintenance': 'Wartung',
            'repair': 'Reparatur',
            'others': 'Sonstiges'
        };
        return translations[sectionName] ?? sectionName;
    }

    function renderActivityRow(item, index) {
        const statusOptions = `
            <select class="status-dropdown form-control" data-id="${item.id}">
                <option value="Published" ${item.status === 'Published' ? 'selected' : ''}>Aktiv</option>
                <option value="Unpublished" ${item.status === 'Unpublished' ? 'selected' : ''}>Inaktiv</option>
            </select>
        `;

        return `
            <tr>
                <td>${index}</td>
                <td>${item.title || ''}</td>
                <td>${item.description || ''}</td>
                <td>${item.article_group || ''}</td>
                <td>${translateSectionName(item.section_name)}</td>
                <td>${item.departments || ''}</td>
                <td>${item.positions || ''}</td>
                <td>${item.articles || ''}</td>
                <td>${item.phase_name || ''}</td>
                <td>${item.note || '–'}</td>
                <td>${item.duration || 0}</td>
                <td>${statusOptions}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" onclick="editActivity(${item.id})" title="Bearbeiten">
                            <i class="fas fa-edit"></i>
                        </button>
                        <span style="width: 5px;"></span>
                        <button class="btn btn-sm btn-danger btn-delete-activity" data-id="${item.id}" title="Löschen">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    // -----------------------------
    // Document ready: keep ALL your behaviors but conflict-free
    // -----------------------------
    $(function () {

        // 1) Open copy modal button (your original)
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('openCopyModalBtn');
            if (btn) {
                btn.addEventListener('click', function () {
                    const modal = new bootstrap.Modal(document.getElementById('copyModal'));
                    modal.show();
                });
            }
        });

        // 2) Toastr session messages (your original)
        @if(Session::has('update_msg'))
            toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif

        // 3) Select2 tags + prevent duplicates + save new tag (your original)
        (function initSelect2Tags() {
            const $select = $('.select2-tags');
            if (!$select.length) return;

            safeSelect2($select, {
                tags: true,
                placeholder: 'Wählen',
                allowClear: true,
                width: '100%',
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '') return null;

                    let exists = false;
                    $select.find('option').each(function () {
                        if ($.trim($(this).text()).toLowerCase() === term.toLowerCase()) {
                            exists = true;
                            return false;
                        }
                    });

                    if (exists) return null;

                    return { id: term, text: term, newTag: true };
                },
                templateResult: function (data) {
                    const $result = $("<span></span>").text(data.text);
                    if (data.newTag) $result.append(" <em>(Neue)</em>");
                    return $result;
                }
            });

            $select.off('select2:select.positionTag').on('select2:select.positionTag', function (e) {
                const data = e.params.data;

                if (!data.newTag) return;

                Swal.fire({
                    title: 'Neue Position hinzufügen?',
                    text: `"${data.text}" ist nicht in der Liste. Möchten Sie es speichern?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, speichern',
                    cancelButtonText: 'Abbrechen',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("position.store.json") }}',
                            method: 'POST',
                            data: { _token: csrf(), position: data.text },
                            success: function (response) {
                                Swal.fire({
                                    title: 'Gespeichert!',
                                    text: `"${response.text}" wurde erfolgreich gespeichert.`,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                const newOption = new Option(response.text, response.id, true, true);
                                $select.append(newOption).trigger('change');
                            },
                            error: function () {
                                Swal.fire({
                                    title: 'Fehler!',
                                    text: `"${data.text}" konnte nicht gespeichert werden.`,
                                    icon: 'error',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                let selected = $select.val() || [];
                                selected = selected.filter(val => val !== data.id);
                                $select.val(selected).trigger('change');
                            }
                        });
                    } else {
                        let selected = $select.val() || [];
                        selected = selected.filter(val => val !== data.id);
                        $select.val(selected).trigger('change');
                    }
                });
            });
        })();

        // 4) Initial folder wiring (your original folder-toggle behavior, but stable)
        wireFolderToggles(document);
        wireFolderHoverActive();

        // 5) Activity form submit (your original)
        $('#activityForm')
            .off('submit.activityForm')
            .on('submit.activityForm', function (e) {
                e.preventDefault();

                let id  = $('#activity_id').val();
                let url = id ? `/phase-activities/${id}/update` : '/phase-activities';
                let data = $(this).serialize();

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    headers: { 'X-CSRF-TOKEN': csrf() },
                    success: function () {
                        $('#activityModal').modal('hide');
                        Swal.fire('Gespeichert!', '', 'success').then(() => location.reload());
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMsg = Object.values(errors).flat().join('<br>');
                            Swal.fire('Fehler beim Speichern', errorMsg, 'error');
                        }
                    }
                });
            });

        // 6) Click on PHASE: load all activities (your original)
        $(document)
            .off('click.phaseDetails', '.folder-label.heading')
            .on('click.phaseDetails', '.folder-label.heading', function (e) {
                e.stopPropagation();
                const phaseId = $(this).closest('.folder-toggle').data('phase-id');
                if (!phaseId) return;

                $.ajax({
                    url: `/get/phase/all/activity/${phaseId}`,
                    method: 'GET',
                    success: function (response) {
                        let tbody = '';
                        let totalDauer = 0;
                        let activities = response.data;

                        if (activities.length > 0) {
                            activities.forEach((item, index) => {
                                tbody += renderActivityRow(item, index + 1);
                                totalDauer += parseFloat(item.duration || 0);
                            });

                            tbody += `
                                <tr>
                                    <td colspan="9" class="text-end"><strong>Gesamtdauer:</strong></td>
                                    <td><strong>${totalDauer.toFixed(2)} Uhr</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            `;
                        } else {
                            tbody = `<tr><td colspan="13" class="text-center text-muted">Keine Aktivitäten gefunden.</td></tr>`;
                        }

                        $('#detailed_table tbody').html(tbody);
                    },
                    error: function () {
                        alert('Fehler beim Laden der Aktivitäten.');
                    }
                });
            });

        // 7) Click on SUB-TASK: load single activity (your original)
        $(document)
            .off('click.subDetails', '.sub-data')
            .on('click.subDetails', '.sub-data', function (e) {
                e.stopPropagation();
                let activityId = $(this).data('activity-id');

                $.ajax({
                    url: `/get/phase/activity/${activityId}`,
                    method: 'GET',
                    success: function (response) {
                        let activity = response.data;
                        let tbody = renderActivityRow(activity, 1);
                        $('#detailed_table tbody').html(tbody);
                    },
                    error: function () {
                        alert('Fehler beim Laden der Aktivität.');
                    }
                });
            });

        // 8) Status dropdown change (your original)
        $(document)
            .off('change.activityStatus', '.status-dropdown')
            .on('change.activityStatus', '.status-dropdown', function () {
                let status = $(this).val();
                let id = $(this).data('id');

                $.ajax({
                    url: `/phase/activity/status/${id}`,
                    type: 'POST',
                    data: { _token: csrf(), status: status },
                    success: function () {
                        Swal.fire('Status aktualisiert', '', 'success');
                    }
                });
            });

        // 9) Delete phase + delete activity (your original)
        $(document)
            .off('click.deletePhase', '.btn-delete-phase')
            .on('click.deletePhase', '.btn-delete-phase', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/task_phase_destroy/' + id,
                        type: 'GET',
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response[1],
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                });
            });

        $(document)
            .off('click.deleteActivity', '.btn-delete-activity')
            .on('click.deleteActivity', '.btn-delete-activity', function () {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Delete this activity?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '/activities_destroy/' + id,
                        type: 'GET',
                        success: function (response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response[1],
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        },
                        error: function () {
                            Swal.fire('Error!', 'Could not delete the activity.', 'error');
                        }
                    });
                });
            });

        // 10) Sidebar minimize (your original)
        (function initSidebarMinimize() {
            let sidebarVisible = true;

            $('.minimize-button')
                .off('click.sidebarToggle')
                .on('click.sidebarToggle', function () {
                    const $sidebar = $('.side-bar');
                    const $activity = $('#activities');

                    if (sidebarVisible) {
                        $sidebar.hide();
                        $activity.removeClass('col-md-8').addClass('col-12');
                        $(this).find('i').removeClass('icon-minus').addClass('icon-maximize');
                    } else {
                        $sidebar.show();
                        $activity.removeClass('col-12').addClass('col-md-8');
                        $(this).find('i').removeClass('icon-maximize').addClass('icon-minus');
                    }

                    sidebarVisible = !sidebarVisible;
                });
        })();

        // 11) Tooltips (your original)
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // 12) Version select (#version_id) with safe autoload (prevents reload-conflicts)
        (function initVersionSelect() {
            const $versionSelect = $('#version_id');
            if (!$versionSelect.length) return;

            const productId      = $('#filter_product_id').val();
            const sectionId      = $('#section_id').val();
            const defaultVersion = $('#currentVersion').val();

            const storageKey = `selectedVersion:${productId}:${sectionId}`;

            safeSelect2($versionSelect, {
                placeholder: "Bitte wählen",
                width: '100%',
                templateResult: function (data) {
                    return !data.id ? data.text : $('<strong>' + data.text + '</strong>');
                }
            });

            $versionSelect
                .off('change.phaseVersion')
                .on('change.phaseVersion', function () {
                    const v = $(this).val();

                    if (!v) {
                        localStorage.removeItem(storageKey);
                        $('#folderStructure').html('<div class="text-muted p-2">Bitte wählen Sie eine Version.</div>');
                        return;
                    }

                    localStorage.setItem(storageKey, v);

                    // keep in URL for refresh
                    const url = new URL(window.location.href);
                    url.searchParams.set('version', v);
                    url.searchParams.delete('stage_id');
                    history.replaceState({}, '', url);

                    window.loadStagesAndPhases(v, productId, sectionId);
                });

            // initial selection: URL -> localStorage -> default
            const urlVersion    = (new URL(window.location.href)).searchParams.get('version');
            const storedVersion = localStorage.getItem(storageKey);
            const initial       = urlVersion || storedVersion || defaultVersion;

            if (initial && $versionSelect.find(`option[value="${initial}"]`).length) {
                // set without firing change (prevents “loads phases on every reload”)
                $versionSelect.val(initial).trigger('change.select2');

                // only autoload if folderStructure is empty (AJAX mode)
                if (folderStructureLooksEmpty()) {
                    window.loadStagesAndPhases(initial, productId, sectionId);
                }
            }

            // initial sortables on first paint
            initSortables(document);
        })();

        // 13) COPY DRAWER (your original, but avoids double-select2 init)
        (function initCopyDrawer() {
            let selectedCopyVersion = null;
            let copyContext = { sourceType: 'phase', phaseId: null, activityId: null };
            let prefillTarget = null;

            function updateTargetSummary() {
                const p  = $('#targetProduct option:selected').text() || '–';
                const s  = $('#targetSection option:selected').text() || '–';
                const v  = $('#targetVersion option:selected').text() || '–';
                const st = $('#targetStage option:selected').text() || '–';
                const ph = $('#targetPhase').find('option:selected').text() || '–';

                $('#targetSummary').html(
                    `<strong>Ziel:</strong> ${p} · ${s} · Version: ${v} · Stage: ${st} · Phase: ${ph}`
                );
            }

            function closeCopyDrawer() {
                $('#copyDrawer').removeClass('open');
            }

            function openCopyDrawer(sourceType, phaseId, activityId = null) {
                copyContext = { sourceType, phaseId, activityId };

                $('#copyForm')[0].reset();
                $('#targetProduct, #targetSection, #targetVersion, #targetStage, #targetPhase')
                    .val(null).trigger('change');

                $('#activitiesList').html('');
                $('#selectAllActivities').prop('checked', false);

                $('#copyDrawer').addClass('open');

                $.get(`/copy/load/${phaseId}`, function (res) {
                    const phase = res.phase;

                    prefillTarget = {
                        productId: phase.product_id,
                        sectionId: phase.section_id,
                        version:   phase.version,
                        stageId:   phase.stage_id,
                        phaseId:   phase.id
                    };

                    $('#sourcePhaseDetails').html(
                        `${phase.phase_name} · ${phase.stage || 'ohne Stage'} · Version ${phase.version || '-'}`
                    );

                    let html = '';
                    res.activities.forEach(a => {
                        const isPreSelected =
                            copyContext.sourceType === 'activity' &&
                            parseInt(copyContext.activityId, 10) === parseInt(a.id, 10);

                        html += `
                            <div class="custom-control custom-checkbox mb-0">
                                <input type="checkbox"
                                    class="custom-control-input activity-checkbox"
                                    id="activityCopy${a.id}"
                                    value="${a.id}"
                                    ${isPreSelected ? 'checked' : ''}>
                                <label class="custom-control-label small" for="activityCopy${a.id}">
                                    ${a.title || 'Ohne Titel'}
                                </label>
                            </div>
                        `;
                    });

                    $('#activitiesList').html(html || '<div class="p-1 text-muted small">Keine Aktivitäten vorhanden.</div>');

                    let productOptions = '<option value="">Produkt wählen</option>';
                    res.products.forEach(p => productOptions += `<option value="${p.id}">${p.article_group}</option>`);
                    $('#targetProduct').html(productOptions);

                    let sectionOptions = '<option value="">Bereich wählen</option>';
                    res.sections.forEach(s => sectionOptions += `<option value="${s.id}">${s.phase_section}</option>`);
                    $('#targetSection').html(sectionOptions);

                    if (prefillTarget.productId) $('#targetProduct').val(prefillTarget.productId).trigger('change');
                    if (prefillTarget.sectionId) $('#targetSection').val(prefillTarget.sectionId).trigger('change');

                    updateTargetSummary();
                });
            }

            // Drawer close events (your original)
            $('#copyDrawerClose, #copyDrawerCancel')
                .off('click.copyDrawerClose')
                .on('click.copyDrawerClose', closeCopyDrawer);

            $('.phase-copy-overlay')
                .off('click.copyDrawerOverlay')
                .on('click.copyDrawerOverlay', closeCopyDrawer);

            // Select2 init for drawer (your original intent)
            safeSelect2($('#targetVersion'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#copyDrawerPanel'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            safeSelect2($('#targetStage'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#copyDrawerPanel'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            // PHASE select2 (your original advanced config)
            if (!isSelect2($('#targetPhase'))) {
                $('#targetPhase').select2({
                    tags: true,
                    placeholder: 'Phase wählen oder neu eingeben',
                    width: '100%',
                    dropdownParent: $('#copyDrawerPanel'),
                    ajax: {
                        url: '/search-phases',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                product_id: $('#targetProduct').val(),
                                section_id: $('#targetSection').val()
                            };
                        },
                        processResults: function (data) {
                            return { results: data };
                        }
                    },
                    createTag: function (params) {
                        return { id: params.term, text: params.term, newPhase: true };
                    },
                    templateResult: function (data) {
                        return data.newPhase
                            ? `<span>➕ Neue Phase: <strong>${data.text}</strong></span>`
                            : data.text;
                    },
                    escapeMarkup: function (markup) { return markup; }
                });
            }

            // PRODUCT → VERSION (your original)
            $('#targetProduct')
                .off('change.copyProduct')
                .on('change.copyProduct', function () {
                    const productId = $(this).val();

                    $('#targetVersion').html('<option>Versionen werden geladen...</option>').trigger('change');
                    $('#targetStage').html('<option>Bitte Version wählen</option>').trigger('change');
                    $('#targetPhase').html('<option>Bitte Phase wählen</option>').trigger('change');

                    if (!productId) {
                        updateTargetSummary();
                        return;
                    }

                    $.get('/get-stage-versions', { product_id: productId }, function (versions) {
                        let options = '<option value="">Version wählen</option>';
                        versions.forEach(version => options += `<option value="${version}">${version}</option>`);
                        $('#targetVersion').html(options).trigger('change');

                        if (prefillTarget &&
                            prefillTarget.productId == productId &&
                            prefillTarget.version) {
                            $('#targetVersion').val(prefillTarget.version).trigger('change');
                        }

                        updateTargetSummary();
                    });
                });

            // VERSION → STAGES (your original)
            $('#targetVersion')
                .off('change.copyVersion')
                .on('change.copyVersion', function () {
                    selectedCopyVersion = $(this).val();
                    const productId = $('#targetProduct').val();

                    $('#targetStage').html('<option>Lade Stages...</option>');
                    $('#targetPhase').html('<option>Bitte Phase wählen</option>');

                    if (!selectedCopyVersion || !productId) {
                        updateTargetSummary();
                        return;
                    }

                    $.get('/get/stage/version', { version: selectedCopyVersion, product_id: productId }, function (stages) {
                        let options = '<option value="">-- Bitte wählen --</option>';
                        let defaultStageId = null;

                        if (!Array.isArray(stages)) {
                            console.warn('Ungültige Stage-Daten:', stages);
                            $('#targetStage').html('<option value="">Keine Stages gefunden</option>');
                            updateTargetSummary();
                            return;
                        }

                        stages.forEach(stage => {
                            if (stage.default === 'yes') defaultStageId = stage.id;
                            options += `<option value="${stage.id}">${stage.stage}${stage.default === 'yes' ? ' (Standard)' : ''}</option>`;
                        });

                        $('#targetStage').html(options).trigger('change');

                        if (prefillTarget &&
                            prefillTarget.productId == productId &&
                            prefillTarget.version == selectedCopyVersion &&
                            prefillTarget.stageId) {
                            $('#targetStage').val(prefillTarget.stageId).trigger('change');
                        } else if (defaultStageId) {
                            $('#targetStage').val(defaultStageId).trigger('change');
                        }

                        updateTargetSummary();
                    });
                });

            // STAGE → LOAD phases + activities (your original)
            $('#targetStage')
                .off('change.copyStage')
                .on('change.copyStage', function () {
                    const stageId = $(this).val();
                    const version = $('#targetVersion').val();
                    const productId = $('#targetProduct').val();

                    $('#targetPhase').html('<option>Phasen werden geladen...</option>');
                    $('#activitiesList').html('<div class="p-1 text-muted small">Aktivitäten werden geladen...</div>');

                    if (!stageId || !version || !productId) return;

                    $.get('/get-activities-by-stage', { stage: stageId, version: version, product_id: productId }, function (phases) {
                        let phaseOptions = '<option value="">Phase wählen oder neu eingeben</option>';
                        let activityHtml = '';

                        if (!Array.isArray(phases)) {
                            console.warn('Ungültige Phase/Activity-Daten:', phases);
                            $('#targetPhase').html('<option value="">Keine Phasen gefunden</option>');
                            $('#activitiesList').html('<div class="p-1 text-danger small">Keine Aktivitäten gefunden</div>');
                            return;
                        }

                        phases.forEach(phase => {
                            phaseOptions += `<option value="${phase.id}">${phase.phase_name}</option>`;

                            if (Array.isArray(phase.activities)) {
                                phase.activities.forEach(a => {
                                    activityHtml += `
                                        <div class="custom-control custom-checkbox mb-0">
                                            <input type="checkbox"
                                                   class="custom-control-input activity-checkbox"
                                                   id="activityCopy${a.id}"
                                                   value="${a.id}">
                                            <label class="custom-control-label small" for="activityCopy${a.id}">
                                                ${a.title || 'Ohne Titel'}
                                            </label>
                                        </div>
                                    `;
                                });
                            }
                        });

                        $('#targetPhase').html(phaseOptions).trigger('change');
                        $('#activitiesList').html(activityHtml || '<div class="text-muted">Keine Aktivitäten vorhanden</div>');

                        if (prefillTarget && prefillTarget.phaseId) {
                            const exists = $('#targetPhase option[value="' + prefillTarget.phaseId + '"]').length > 0;
                            if (exists) $('#targetPhase').val(prefillTarget.phaseId).trigger('change');
                        }

                        updateTargetSummary();
                    });
                });

            // NEW PHASE CREATE (your original)
            $('#targetPhase')
                .off('select2:select.copyNewPhase')
                .on('select2:select.copyNewPhase', function (e) {
                    const data = e.params.data;
                    if (!data.newPhase) return;

                    const phaseName = data.text;
                    const productId = $('#targetProduct').val();
                    const sectionId = $('#targetSection').val();

                    if (!productId || !sectionId) {
                        Swal.fire('Hinweis', 'Bitte wählen Sie zuerst Produkt und Bereich aus.', 'warning');
                        $('#targetPhase').val(null).trigger('change');
                        return;
                    }

                    $.ajax({
                        url: '/create-phase',
                        type: 'POST',
                        data: {
                            product_id: productId,
                            section_id: sectionId,
                            phase_name: phaseName,
                            _token: csrf()
                        },
                        success: function (res) {
                            const newOption = new Option(res.phase_name, res.id, true, true);
                            $('#targetPhase').append(newOption).trigger('change');
                            Swal.fire('Erfolgreich', 'Neue Phase wurde erstellt.', 'success');
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Phase konnte nicht erstellt werden.', 'error');
                            $('#targetPhase').val(null).trigger('change');
                        }
                    });
                });

            // SELECT ALL (your original)
            $('#selectAllActivities')
                .off('change.copyAll')
                .on('change.copyAll', function () {
                    $('.activity-checkbox').prop('checked', this.checked);
                });

            // Buttons open drawer (your original)
            $(document)
                .off('click.copyPhaseBtn', '.btn-copy-phase')
                .on('click.copyPhaseBtn', '.btn-copy-phase', function () {
                    const phaseId = $(this).data('phase-id');
                    if (!phaseId) return;
                    openCopyDrawer('phase', phaseId);
                });

            $(document)
                .off('click.copyActivityBtn', '.btn-copy-activity')
                .on('click.copyActivityBtn', '.btn-copy-activity', function () {
                    const phaseId = $(this).data('phase-id');
                    const activityId = $(this).data('id');
                    if (!phaseId || !activityId) return;
                    openCopyDrawer('activity', phaseId, activityId);
                });

            // Submit copy (your original)
            $('#copyForm')
                .off('submit.copySubmit')
                .on('submit.copySubmit', function (e) {
                    e.preventDefault();

                    const phaseId = $('#targetPhase').val();
                    if (!phaseId || isNaN(phaseId)) {
                        return Swal.fire('Fehler', 'Bitte wählen Sie eine gültige Phase aus.', 'error');
                    }

                    const data = {
                        target_product_id: $('#targetProduct').val(),
                        target_section_id: $('#targetSection').val(),
                        target_version: $('#targetVersion').val(),
                        target_stage_id: $('#targetStage').val(),
                        target_phase_id: parseInt(phaseId, 10),
                        activities: $('.activity-checkbox:checked').map(function () {
                            return $(this).val();
                        }).get()
                    };

                    if (!data.target_product_id || !data.target_section_id || !data.target_version || !data.target_stage_id) {
                        return Swal.fire('Hinweis', 'Bitte wählen Sie Produkt, Bereich, Version und Stage.', 'warning');
                    }

                    if (!data.activities.length) {
                        return Swal.fire('Hinweis', 'Bitte wählen Sie mindestens eine Aktivität aus.', 'warning');
                    }

                    $.ajax({
                        url: '/copy/do',
                        method: 'POST',
                        data: JSON.stringify(data),
                        contentType: 'application/json',
                        headers: { 'X-CSRF-TOKEN': csrf() },
                        success: function (res) {
                            closeCopyDrawer();
                            Swal.fire('Erfolgreich', res.message || 'Aktivitäten kopiert.', 'success');
                        },
                        error: function (xhr) {
                            let msg = 'Kopieren fehlgeschlagen.';
                            if (xhr.responseJSON?.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            }
                            Swal.fire('Fehler', msg, 'error');
                        }
                    });
                });

            $('#targetProduct, #targetSection, #targetVersion, #targetStage, #targetPhase')
                .off('change.copySummary')
                .on('change.copySummary', updateTargetSummary);
        })();

        // 14) Modal Version + Stage (your original)
        (function initCreatePhaseModalVersionStage() {
            safeSelect2($('#modal_version'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#primary'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            safeSelect2($('#modal_stage_id'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#primary'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            const productId = $('#product_id').val();

            $('#primary')
                .off('shown.bs.modal.modalVersionRestore')
                .on('shown.bs.modal.modalVersionRestore', function () {
                    const savedVersion = localStorage.getItem('modal_version');
                    const savedStageId = localStorage.getItem('modal_stage_id');

                    if (savedVersion) {
                        $('#modal_version').val(savedVersion).trigger('change');

                        setTimeout(() => {
                            if (savedStageId) $('#modal_stage_id').val(savedStageId).trigger('change');
                        }, 500);
                    }
                });

            $('#modal_version')
                .off('change.modalStages')
                .on('change.modalStages', function () {
                    const selectedVersion = $(this).val();
                    localStorage.setItem('modal_version', selectedVersion);
                    $('#modal_stage_id').html('<option value="">Lade Phasen...</option>');

                    if (!selectedVersion) return;

                    $.get('/get-stages-by-version', { version: selectedVersion, product_id: productId }, function (data) {
                        let options = '<option value="">-- Bitte wählen --</option>';
                        let defaultStageId = null;

                        data.forEach(stage => {
                            if (stage.default === 'yes') defaultStageId = stage.id;
                            options += `<option value="${stage.id}">${stage.stage}${stage.default === 'yes' ? ' (Standard)' : ''}</option>`;
                        });

                        $('#modal_stage_id').html(options);

                        const savedStageId = localStorage.getItem('modal_stage_id');
                        if (savedStageId && $(`#modal_stage_id option[value="${savedStageId}"]`).length) {
                            $('#modal_stage_id').val(savedStageId).trigger('change');
                        } else if (defaultStageId) {
                            $('#modal_stage_id').val(defaultStageId).trigger('change');
                        }
                    });
                });
        })();

        // 15) Edit phase modal (your original) + global function
        window.editPhase = function (button) {
            const btn = $(button);

            const phaseId   = btn.data('phase-id');
            const phaseName = btn.data('phase-name');
            const version   = btn.data('version');
            const stageId   = btn.data('stage-id');

            $('#edit_phase_id').val(phaseId);
            $('#edit_phase_name').val(phaseName);

            $('#modal_edit_version').val(version).trigger('change');

            setTimeout(() => {
                $('#modal_edit_stage_id').val(stageId).trigger('change');
            }, 500);

            $('#editPhaseModal').modal('show');
        };

        (function initEditPhaseModal() {
            const productId = $('#product_id').val();

            safeSelect2($('#modal_edit_version'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#editPhaseModal'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            safeSelect2($('#modal_edit_stage_id'), {
                placeholder: "Bitte wählen",
                width: '100%',
                dropdownParent: $('#editPhaseModal'),
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return $('<strong>' + data.text + '</strong>');
                }
            });

            $('#modal_edit_version')
                .off('change.editPhaseStages')
                .on('change.editPhaseStages', function () {
                    const selectedVersion = $(this).val();

                    $('#modal_edit_stage_id').html('<option value="">Lade Phasen...</option>');
                    if (!selectedVersion || !productId) return;

                    $.get('/get-stages-by-version', { version: selectedVersion, product_id: productId }, function (data) {
                        let options = '<option value="">-- Bitte wählen --</option>';
                        data.forEach(stage => {
                            options += `<option value="${stage.id}">${stage.stage}${stage.default === 'yes' ? ' (Standard)' : ''}</option>`;
                        });
                        $('#modal_edit_stage_id').html(options);
                    });
                });

            $('#editPhaseForm')
                .off('submit.editPhaseSave')
                .on('submit.editPhaseSave', function (e) {
                    e.preventDefault();

                    const phaseId   = $('#edit_phase_id').val();
                    const phaseName = $('#edit_phase_name').val();
                    const version   = $('#modal_edit_version').val();
                    const stageId   = $('#modal_edit_stage_id').val();

                    if (!phaseName || !version || !stageId) {
                        return Swal.fire('Fehler', 'Bitte füllen Sie alle Felder aus.', 'warning');
                    }

                    const url = $('#editPhaseForm').data('action-template').replace(':id', phaseId);

                   $.ajax({
                        url: url,
                        method: 'POST',
                        data: {
                            _token: csrf(),
                            phase_name: phaseName,
                            version: version,
                            stage_id: stageId
                        },
                        success: function () {
                            $('#editPhaseModal').modal('hide');
                            location.reload();
                        },
                        error: function (err) {
                            console.error('Fehler:', err.responseJSON);
                            Swal.fire('Fehler', 'Beim Speichern ist ein Fehler aufgetreten.', 'error');
                        }
                    });

                });
        })();

    }); // end ready
})();
</script>
 
  <script>
(() => {
  if (window.__MASTER_SET_DRAWER_BOOTSTRAPPED__) return;
  window.__MASTER_SET_DRAWER_BOOTSTRAPPED__ = true;

  const $drawer   = $('#masterSetDrawer');
  const $overlay  = $('#masterSetDrawerOverlay');
  const $closeBtn = $('#masterSetDrawerClose');
  const $body     = $('#master-set-list-container');

  const csrf = () => $('meta[name="csrf-token"]').attr('content');

  const state = {
    type: null,       // "phase" | "activity"
    targetId: null,
    productId: null,
    q: '',
    activeTab: 'search', // 'search' | 'linked'
    cacheDetails: new Map(), // setId -> {components,labor,totals}
  };

  function openDrawer() {
    $drawer.addClass('open');
    document.documentElement.style.overflow = 'hidden';
  }
  function closeDrawer() {
    $drawer.removeClass('open');
    document.documentElement.style.overflow = '';
  }

  function money(n) {
    const v = Number(n || 0);
    return v.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function renderShell() {
    $body.html(`
      <div style="padding:12px 14px;">
        <div class="d-flex align-items-center justify-content-between" style="gap:10px;">
          <div>
            <div class="small text-muted">Type: <strong>${state.type}</strong> · Target: <strong>#${state.targetId}</strong> · Product: <strong>#${state.productId}</strong></div>
          </div>
        </div>

        <div class="mt-2 d-flex" style="gap:8px;">
          <button class="btn btn-sm ${state.activeTab==='search'?'btn-primary':'btn-outline-primary'} js-ms-tab" data-tab="search">Search Sets</button>
          <button class="btn btn-sm ${state.activeTab==='linked'?'btn-primary':'btn-outline-primary'} js-ms-tab" data-tab="linked">Linked Sets</button>
        </div>

        <div class="mt-2 ${state.activeTab==='search' ? '' : 'd-none'}" id="msSearchBar">
          <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="msQ" placeholder="Search by name/description..." value="${escapeHtml(state.q)}">
            <div class="input-group-append">
              <button class="btn btn-primary" id="msDoSearch">Search</button>
            </div>
          </div>
          <div class="small text-muted mt-1">Search uses <code>/api/master-sets/search?product_id=…&q=…</code></div>
        </div>

        <div class="mt-2" id="msList">
          <div class="text-muted small p-2">Loading…</div>
        </div>
      </div>
    `);
  }

  function escapeHtml(s){
    return String(s ?? '')
      .replaceAll('&','&amp;').replaceAll('<','&lt;')
      .replaceAll('>','&gt;').replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }

  async function fetchSets() {
    const url = state.activeTab === 'search'
      ? `/api/master-sets/search?product_id=${encodeURIComponent(state.productId)}&q=${encodeURIComponent(state.q)}`
      : `/api/master-sets/search?product_id=${encodeURIComponent(state.productId)}&q=`; // fallback if you don't have linked endpoint

    // If you implement a dedicated linked endpoint later:
    // const url = `/api/master-sets/linked/list?type=${state.type}&target_id=${state.targetId}`;

    return $.get(url);
  }

  function isLinked(set) {
    if (state.type === 'phase') return String(set.task_phase_id || '') === String(state.targetId);
    return String(set.phase_activity_id || '') === String(state.targetId);
  }

  function renderSetRow(set) {
    const linked = isLinked(set);
    const total = (Number(set.components_total || 0) + Number(set.labor_total || 0));
    const badge = linked
      ? `<span class="badge badge-success">Linked</span>`
      : `<span class="badge badge-secondary">Not linked</span>`;

    return `
      <div class="border rounded-lg mb-1" style="border-radius:12px; overflow:hidden;">
        <div class="d-flex align-items-center justify-content-between" style="padding:10px 12px; background:#f8fafc;">
          <div style="min-width:0;">
            <div class="font-weight-bold" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              #${set.id} · ${escapeHtml(set.name || 'Untitled Set')}
            </div>
            <div class="small text-muted">
              Total: <strong>${money(total)} €</strong>
              · Components: ${money(set.components_total)} €
              · Labor: ${money(set.labor_total)} €
              · ${badge}
            </div>
          </div>

          <div class="d-flex" style="gap:6px; flex-shrink:0;">
            <button class="btn btn-sm btn-outline-primary js-ms-toggle-details" data-set-id="${set.id}">
              Details
            </button>
            ${linked
              ? `<button class="btn btn-sm btn-outline-danger js-ms-unlink" data-set-id="${set.id}">Unlink</button>`
              : `<button class="btn btn-sm btn-primary js-ms-link" data-set-id="${set.id}">Link</button>`
            }
          </div>
        </div>

        <div class="d-none" id="msDetails_${set.id}" style="padding:10px 12px; background:#fff;">
          <div class="text-muted small">Loading details…</div>
        </div>
      </div>
    `;
  }

function renderDetails(setId, details) {
  const comps = (details.components || []).map(c => `
    <div class="d-flex justify-content-between border-bottom py-1">
      <div>
        <div class="small font-weight-bold">
          ${escapeHtml(c.product_name || 'Product')}
          ${c.article_no ? `<span class="text-muted">(${escapeHtml(c.article_no)})</span>` : ''}
        </div>
        <div class="small text-muted">${escapeHtml(c.description || '')}</div>
      </div>
      <div class="small text-right">
        <div>Qty: ${c.qty}</div>
        <div>${money(c.unit_price)} €</div>
        <div><strong>${money(c.line_total)} €</strong></div>
      </div>
    </div>
  `).join('') || `<div class="small text-muted">No components.</div>`;

  const labor = (details.labor || []).map(l => `
    <div class="d-flex justify-content-between border-bottom py-1">
      <div class="small">
        ${escapeHtml(l.department_name || '–')} · ${escapeHtml(l.position_name || '–')}
        ${l.employee_name ? ` · ${escapeHtml(l.employee_name)}` : ''}
      </div>
      <div class="small text-right">
        <div>${l.hours} h</div>
        <div>${money(l.hourly_rate)} €/h</div>
        <div><strong>${money(l.line_total)} €</strong></div>
      </div>
    </div>
  `).join('') || `<div class="small text-muted">No labor.</div>`;

  const t = details.totals || {};
  const totalsBox = `
    <div class="p-2 mb-2" style="background:#f8fafc; border-radius:10px;">
      <div class="d-flex justify-content-between small">
        <div>Components</div><div><strong>${money(t.components_total)} €</strong></div>
      </div>
      <div class="d-flex justify-content-between small">
        <div>Labor</div><div><strong>${money(t.labor_total)} €</strong></div>
      </div>
      <div class="d-flex justify-content-between">
        <div><strong>Total</strong></div><div><strong>${money(t.total)} €</strong></div>
      </div>
    </div>
  `;

  $(`#msDetails_${setId}`).html(`
    ${totalsBox}
    <div class="row">
      <div class="col-12 col-md-7">
        <div class="font-weight-bold mb-1">Components</div>
        ${comps}
      </div>
      <div class="col-12 col-md-5 mt-2 mt-md-0">
        <div class="font-weight-bold mb-1">Labor</div>
        ${labor}
      </div>
    </div>
  `);
}

  async function loadAndRenderList() {
    $('#msList').html(`<div class="text-muted small p-2">Loading…</div>`);

    let sets = await fetchSets();

    // If user is on "Linked" tab but you don't have linked endpoint:
    if (state.activeTab === 'linked') {
      sets = (sets || []).filter(isLinked);
    }

    if (!sets || !sets.length) {
      $('#msList').html(`<div class="text-muted small p-2">No sets found.</div>`);
      return;
    }

    $('#msList').html(sets.map(renderSetRow).join(''));
  }

    async function fetchDetails(setId) {
    return $.get(`/api/master-sets/${setId}`);
    }


  async function linkSet(setId) {
    return $.ajax({
      url: `{{ route('task.link_master_set') }}`,
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf() },
      data: { master_set_id: setId, type: state.type, target_id: state.targetId }
    });
  }

  async function unlinkSet(setId) {
    return $.ajax({
      url: `{{ route('task.unlink_master_set') }}`,
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf() },
      data: { master_set_id: setId }
    });
  }

  // ✅ Global open function
  window.openMasterSetModal = function ({ type, targetId, productId }) {
    state.type = type;
    state.targetId = String(targetId);
    state.productId = String(productId);
    state.q = '';
    state.activeTab = 'search';

    openDrawer();
    renderShell();
    loadAndRenderList();
  };

  // ✅ Delegated open handler (works after AJAX)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-open-master-set');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    window.openMasterSetModal({
      type: btn.dataset.type,
      targetId: btn.dataset.targetId,
      productId: btn.dataset.productId
    });
  });

  // Close handlers
  $overlay.on('click', closeDrawer);
  $closeBtn.on('click', closeDrawer);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && $drawer.hasClass('open')) closeDrawer();
  });

  // Delegated inside drawer
  $drawer.on('click', '.js-ms-tab', function () {
    state.activeTab = this.dataset.tab;
    renderShell();
    loadAndRenderList();
  });

  $drawer.on('click', '#msDoSearch', function () {
    state.q = $('#msQ').val() || '';
    loadAndRenderList();
  });

  $drawer.on('keypress', '#msQ', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      state.q = $('#msQ').val() || '';
      loadAndRenderList();
    }
  });

  $drawer.on('click', '.js-ms-link', async function () {
    const setId = this.dataset.setId;
    try {
      await linkSet(setId);
      await loadAndRenderList();
    } catch (xhr) {
      const msg = xhr?.responseJSON?.message || 'Link failed';
      Swal.fire('Error', msg, 'error');
    }
  });

  $drawer.on('click', '.js-ms-unlink', async function () {
    const setId = this.dataset.setId;
    try {
      await unlinkSet(setId);
      await loadAndRenderList();
    } catch (xhr) {
      const msg = xhr?.responseJSON?.message || 'Unlink failed';
      Swal.fire('Error', msg, 'error');
    }
  });

  $drawer.on('click', '.js-ms-toggle-details', async function () {
    const setId = this.dataset.setId;
    const $panel = $(`#msDetails_${setId}`);

    $panel.toggleClass('d-none');
    if ($panel.hasClass('d-none')) return;

    // load once
    if (state.cacheDetails.has(setId)) {
      renderDetails(setId, state.cacheDetails.get(setId));
      return;
    }

    try {
      const details = await fetchDetails(setId);
      state.cacheDetails.set(setId, details);
      renderDetails(setId, details);
    } catch (e) {
      $panel.html(`<div class="text-danger small">Failed to load details.</div>`);
    }
  });

})();
</script>



@endsection