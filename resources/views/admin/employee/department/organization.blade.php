@extends('admin.layouts.app')

@section('title')
Organigramm
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    :root {
        --org-bg: #020617;
        --org-border: #1f2937;
        --org-muted: #6b7280;
        --org-accent: #8fc73e;
        --org-node-bg: #020617;
        --org-node-border: #1f2937;
        --org-node-shadow: 0 10px 30px rgba(15, 23, 42, .6);
        --org-text: #e5e7eb;
        --org-text-strong: #f9fafb;
        --org-chip-bg: #111827;
    }

    .org-wrapper {
        background: white;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        padding: 0;
        overflow: hidden;
        position: relative;
    }

    .org-inner {
        position: relative;
        z-index: 1;
        padding: 14px 16px 18px;
    }

    .org-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .75rem .75rem .5rem;
        margin: -0.25rem -0.25rem .5rem;
        border-radius: 14px;
        background: linear-gradient(120deg, #74b2d4, #74b2d4);
        backdrop-filter: blur(10px);
    }

    .org-toolbar-left {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .75rem;
    }

    .org-toolbar-title {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .org-toolbar-title span:first-child {
        font-size: .7rem;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #ffffffff;
    }

    .org-toolbar-title span:last-child {
        font-size: .95rem;
        font-weight: 600;
        color: #e5e7eb;
    }

    .org-toolbar-branch {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .org-toolbar-label {
        font-size: .75rem;
        font-weight: 500;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .09em;
    }

    #branchSelect {
        min-width: 220px;
        background: #020617;
        border-radius: 999px;
        border: 1px solid rgba(55, 65, 81, .9);
        color: #e5e7eb;
        padding: .4rem .8rem;
        font-size: .8rem;
    }

    #branchSelect:focus {
        outline: none;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, .9);
        border-color: rgba(59, 130, 246, .9);
    }

    /* Select2 dark theme */
    .select2-container--default .select2-selection--single {
        background-color: #020617;
        border-radius: 999px;
        border: 1px solid rgba(55, 65, 81, .9);
        height: 32px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e5e7eb;
        line-height: 30px;
        padding-left: 12px;
        font-size: .8rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px;
    }

    .org-toolbar-right {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .org-zoom-display {
        font-size: .75rem;
        color: #9ca3af;
        min-width: 3ch;
        text-align: right;
    }

    .org-btn,
    .plus-button,
    .remove-button {
        border-radius: 999px;
        border: 1px solid rgba(55, 65, 81, .85);
        background: linear-gradient(90deg, #020617, #030712);
        color: #e5e7eb;
        padding: .3rem .7rem;
        font-size: .78rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        cursor: pointer;
        transition: background .15s, border-color .15s, transform .1s, box-shadow .15s;
        box-shadow: 0 1px 0 rgba(148, 163, 184, .2);
        white-space: nowrap;
    }

    .org-btn:hover,
    .plus-button:hover,
    .remove-button:hover {
        background: radial-gradient(circle at top, #111827 0, #020617 55%);
        border-color: rgba(129, 140, 248, .9);
        transform: translateY(-1px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, .9);
    }

    .plus-button {
        border-color: rgba(34, 197, 94, .65);
    }

    .remove-button {
        border-color: rgba(248, 113, 113, .75);
    }

    .org-viewport {
        margin-top: .75rem;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.9);
        background: #ffffffff;
        padding: 16px 16px 20px;
        overflow: auto;
        min-height: 420px;
        max-height: 70vh;
        position: relative;
    }

    .tree {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        transform-origin: top center;
        transition: transform .18s ease-out;
    }

    .rectangle {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .branch-div {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: .3rem;
        padding-bottom: 1rem;
    }

    .branch-div .name {
        background: #020617;
        border-radius: 999px;
        padding: .45rem 1.1rem;
        border: 1px solid rgba(148, 163, 184, 0.45);
        color: #e5e7eb;
        font-size: .85rem;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .branch-div .role {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .1rem .6rem;
        border-radius: 999px;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .14em;
        background: rgba(15, 23, 42, .9);
        border: 1px solid rgba(55, 65, 81, .9);
        color: #9ca3af;
    }

    .branch-div .role::before {
        content: "Branch";
    }

    .branch-div .level-button {
        display: flex;
        gap: .4rem;
        margin-top: .15rem;
    }

    /* ========== Nodes & children ========== */

    .node-container {
        position: relative;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 20px 12px 6px;
    }

    .node-card {
        background:
            radial-gradient(circle at top, rgba(30, 64, 175, 0.2), transparent 55%),
            radial-gradient(circle at bottom, rgba(22, 163, 74, 0.18), transparent 55%),
            var(--org-node-bg);
        color: var(--org-text);
        padding: 8px 10px 9px;
        width: 210px;
        text-align: left;
        position: relative;
        font-size: 11px;
        border-radius: 12px;
        border: 1px solid var(--org-node-border);
        box-shadow: var(--org-node-shadow);
        overflow: hidden;
        cursor: grab;
    }

    .org-ghost {
        opacity: .4;
        transform: scale(.98);
    }

    .node-header {
        font-size: 12px;
        font-weight: 600;
        color: var(--org-text-strong);
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .node-subtitle {
        font-size: 9px;
        color: var(--org-muted);
        text-transform: uppercase;
        letter-spacing: .13em;
        margin-bottom: 6px;
    }

    .node-actions {
        display: flex;
        justify-content: flex-end;
        gap: 4px;
        margin-bottom: 4px;
    }

    .node-actions button {
        width: 22px;
        height: 22px;
        padding: 0;
        border-radius: 999px;
        border: 1px solid rgba(55, 65, 81, .9);
        background: rgba(15, 23, 42, .95);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .15s, border-color .15s, transform .1s;
    }

    .node-actions button i {
        font-size: 11px;
        color: #9ca3af;
    }

    .node-actions button:hover {
        background: rgba(15, 23, 42, 1);
        border-color: rgba(129, 140, 248, .9);
        transform: translateY(-1px);
    }

    .node-head {
        display: flex;
        align-items: center;
        gap: .4rem;
        margin-top: 6px;
        padding-top: 5px;
        border-top: 1px dashed rgba(148, 163, 184, 0.4);
    }

    .node-head .avatar img {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, .75);
    }

    .node-head span {
        font-size: 10px;
        color: #e5e7eb;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .node-head-main { }

    .node-head-repr {
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px dashed rgba(148, 163, 184, 0.25);
        opacity: 0.95;
    }

    .node-label {
        font-size: 9px;
        color: #9ca3af;
        display: block;
        margin-top: 2px;
    }

    .employees {
        margin-top: 6px;
    }

    .employees ul {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        min-height: 28px;
    }

    .employees .avatar {
        border-radius: 999px;
        border: 1px solid rgba(55, 65, 81, .9);
        padding: 1px;
        cursor: grab;
        background: var(--org-chip-bg);
    }

    .employees .avatar img {
        border-radius: 999px;
    }

    .employees .avatar:hover {
        transform: translateY(-1px) scale(1.03);
        box-shadow: 0 7px 18px rgba(15, 23, 42, .9);
        border-color: rgba(96, 165, 250, .9);
    }

    /* --- children wrapper: what we toggle & sort --- */

    .children-wrapper {
        margin-top: 8px;
        padding-top: 18px;
        position: relative;
    }

    .children-list {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 16px;
    }

    .horizontal-line {
        position: absolute;
        top: 8px;
        left: 10px;
        right: 10px;
        height: 0;
        border-top: 1px dashed rgba(148, 163, 184, .5);
        z-index: 0;
    }

    .vertical-line {
        position: absolute;
        top: -18px;
        height: 18px;
        width: 0;
        border-left: 1px dashed rgba(148, 163, 184, .7);
        z-index: 0;
    }

    #department-wrapper > .horizontal-line {
        display: none;
    }

    .hidden {
        display: none !important;
    }

    @media (max-width: 992px) {
        .org-toolbar {
            align-items: flex-start;
        }
        .org-toolbar-left {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 768px) {
        .org-inner {
            padding: 10px 10px 14px;
        }
        .org-viewport {
            padding: 10px;
        }
        .node-card {
            width: 190px;
        }
    }

    @media (max-width: 480px) {
        .node-card {
            width: 160px;
        }
    }

    .drop-child-zone {
        margin-top: 6px;
        padding: 3px 6px;
        font-size: 9px;
        text-align: center;
        border-radius: 999px;
        border: 1px dashed rgba(148, 163, 184, 0.5);
        color: #9ca3af;
        background: rgba(15, 23, 42, 0.6);
        opacity: 0.25;
        transition: opacity .15s, border-color .15s, background .15s;
    }

    .drop-child-zone.is-active {
        opacity: 1;
        border-color: rgba(129, 140, 248, 0.9);
        background: rgba(15, 23, 42, 0.9);
    }

    /* ==========================
     * RIGHT SIDEBAR: EMPLOYEE POOL
     * ========================== */

    .employee-sidebar {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        padding: 10px 10px 12px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .employee-sidebar-header {
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .13em;
        color: #4b5563;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .4rem;
    }

    .employee-sidebar-header span {
        font-size: .7rem;
        font-weight: 400;
        text-transform: none;
        letter-spacing: normal;
        color: #9ca3af;
    }

    .employee-sidebar-body {
        flex: 1;
        overflow-y: auto;
        padding-right: 2px;
        max-height: 70vh;
    }

    .employees-pool {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        padding-left: 0;
        margin: 0;
    }

    .employees-pool .avatar {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, .9);
        padding: 1px;
        cursor: grab;
        background: #f9fafb;
        transition: transform .12s, box-shadow .12s, border-color .12s;
    }

    .employees-pool .avatar img {
        border-radius: 999px;
    }

    .employees-pool .avatar:hover {
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 6px 16px rgba(148, 163, 184, .8);
        border-color: rgba(59, 130, 246, .95);
    }

    .employees-pool-empty {
        font-size: .75rem;
        color: #9ca3af;
        padding-top: .25rem;
    }

    .employee-bin {
    margin-top: 8px;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px dashed rgba(239, 68, 68, 0.6);
    background: rgba(248, 250, 252, 0.95);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: default;
    transition: background .15s, border-color .15s, transform .1s;
}

.employee-bin-icon {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(239, 68, 68, 0.9);
    background: rgba(248, 113, 113, 0.1);
}

.employee-bin-icon i {
    font-size: 16px;
    color: #ef4444;
}

.employee-bin-text {
    font-size: 11px;
    line-height: 1.3;
    color: #6b7280;
}

.employee-bin.is-active {
    background: rgba(254, 242, 242, 1);
    border-color: rgba(220, 38, 38, 0.9);
    transform: translateY(-1px);
}

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">ABTEILUNGEN</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Organigramm</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <!-- LEFT: Org Chart -->
                <div class="col-lg-9 col-md-8">
                    <div class="org-wrapper mt-1">
                        <div class="org-inner">
                            <div class="org-toolbar">
                                <div class="org-toolbar-left">
                                    <div class="org-toolbar-title">
                                        <span>Organigramm</span>
                                        <span>Struktur nach Bereichen &amp; Abteilungen</span>
                                    </div>
                                    <div class="org-toolbar-branch">
                                        <span class="org-toolbar-label text-white">Bereich</span>
                                        <select id="branchSelect" onchange="loadDepartments()"></select>
                                    </div>
                                </div>

                                <div class="org-toolbar-right">
                                    <span class="org-zoom-display" id="zoomValue">100%</span>
                                    <button type="button" class="org-btn" onclick="orgZoomOut()">-</button>
                                    <button type="button" class="org-btn" onclick="orgZoomReset()">100%</button>
                                    <button type="button" class="org-btn" onclick="orgZoomIn()">+</button>
                                    <button type="button" class="plus-button" onclick="addBranch()">+ Bereich</button>
                                </div>
                            </div>

                            <div class="org-viewport">
                                <div class="tree" id="tree"></div>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- RIGHT: Employee Pool Sidebar -->
                <div class="col-lg-3 col-md-4 mt-1">
                    <div class="employee-sidebar">
                        <div class="employee-sidebar-header">
                            Mitarbeiter-Pool
                            <span>Per Drag &amp; Drop in Abteilungen ziehen</span>
                        </div>
                        <div class="employee-sidebar-body">
                            <ul id="allEmployeesList" class="employees-pool list-unstyled">
                                <li class="employees-pool-empty">Lade Mitarbeiter...</li>
                            </ul>
                        </div> 
                        <div id="employeeRecycleBin" class="employee-bin mt-1">
                            <div class="employee-bin-icon">
                                <i class="feather icon-trash"></i>
                            </div>
                            <div class="employee-bin-text">
                                Mitarbeiter hierher ziehen, um<br>
                                die Zuordnung zur Abteilung zu entfernen
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /row -->
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    const CSRF = "{{ csrf_token() }}";
    const IMG_EMP = "{{ asset('images/employee/') }}/";
    const IMG_DEFAULT = "{{ asset('images/gender/male.png') }}";
    const POSITIONS = @json($positions ?? []);


    function showToast(icon, title, text = '') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true
        });
    }

    let orgZoomLevel = 1;

    document.addEventListener('DOMContentLoaded', function () {
        $('#branchSelect').select2({ placeholder: 'Bereich auswählen', width: 'style' });
        loadBranches();
        orgApplyZoom();
        loadAllEmployees(); // <-- NEW: load employees into right sidebar
    });

    /* ==========================
     * ZOOM
     * ========================== */
    function orgApplyZoom() {
        const tree = document.getElementById('tree');
        if (!tree) return;
        tree.style.transform = 'scale(' + orgZoomLevel + ')';
        const label = document.getElementById('zoomValue');
        if (label) label.textContent = Math.round(orgZoomLevel * 100) + '%';
    }

    function orgZoomIn() {
        orgZoomLevel = Math.min(1.6, orgZoomLevel + 0.1);
        orgApplyZoom();
    }

    function orgZoomOut() {
        orgZoomLevel = Math.max(0.5, orgZoomLevel - 0.1);
        orgApplyZoom();
    }

    function orgZoomReset() {
        orgZoomLevel = 1;
        orgApplyZoom();
    }

    /* ==========================
     * LOAD BRANCHES & DEPARTMENTS
     * ========================== */
    function loadBranches() {
        $.ajax({
            url: "chart/branches",
            type: "GET",
            success: function (response) {
                if (!response.success) return;

                let branchSelect = document.getElementById("branchSelect");
                branchSelect.innerHTML = "";
                response.branches.forEach(branch => {
                    let option = document.createElement("option");
                    option.value = branch.id;
                    option.textContent = branch.branch;
                    branchSelect.appendChild(option);
                });

                $('#branchSelect').trigger('change.select2');
                loadDepartments();
            }
        });
    }

    function loadDepartments() {
        let branchSelect = document.getElementById("branchSelect");
        if (!branchSelect || !branchSelect.value) return;

        let branchId = branchSelect.value;
        let treeContainer = document.getElementById("tree");
        treeContainer.innerHTML = "";

        let branchNodeContainer = document.createElement("div");
        branchNodeContainer.className = "rectangle";
        branchNodeContainer.id = `branch-${branchId}`;

        const branchName = branchSelect.selectedOptions[0]?.text || '';

        branchNodeContainer.innerHTML = `
            <div class="branch-div">
                <div class="name">${branchName}</div>
                <div class="role"></div>
                <div class="level-button">
                    <button class="remove-button" onclick="removeBranch(${branchId})">- Bereich löschen</button>
                    <button class="plus-button" onclick="addNode(null, ${branchId})">+ Abteilung</button>
                </div>
            </div>
        `;

        const rootWrapper = document.createElement('div');
        rootWrapper.className = 'children-wrapper';
        rootWrapper.id = 'department-wrapper';

        const rootLine = document.createElement('div');
        rootLine.className = 'horizontal-line';

        const rootList = document.createElement('div');
        rootList.className = 'children-list';
        rootList.id = 'department-tree';

        rootWrapper.appendChild(rootLine);
        rootWrapper.appendChild(rootList);
        branchNodeContainer.appendChild(rootWrapper);
        treeContainer.appendChild(branchNodeContainer);

        $.ajax({
            url: `/departments?branch_id=${branchId}`,
            type: "GET",
            success: function (response) {
                if (response.success) {
                    renderTree(response.departments, null, rootList);
                    initializeDragAndDrop();
                    initializeEmployeeDragAndDrop();
                }
            }
        });
    }

    /* ==========================
     * RENDER TREE
     * ========================== */
    function renderTree(departments, parentId, parentListEl) {
        const children = departments.filter(d => d.parent_id == parentId);
        if (!children.length) return;

        children.forEach(dept => {
            const nodeContainer = document.createElement('div');
            nodeContainer.className = 'node-container';
            nodeContainer.id = `node-${dept.id}`;
            nodeContainer.dataset.id = dept.id;
            nodeContainer.dataset.parentId = dept.parent_id ?? '';

            const imageSrc = (dept.department_head && dept.empImage) ? (IMG_EMP + dept.empImage) : IMG_DEFAULT;
            const headName = dept.head_name ?? '';

            const repImageSrc = (dept.head_representative && dept.repImage) ? (IMG_EMP + dept.repImage) : IMG_DEFAULT;
            const repName = dept.rep_name ?? '';

            const repDeleteAttrs = dept.head_representative
                ? `class="delete-representative" data-department-id="${dept.id}"`
                : '';

            const nodeCard = document.createElement('div');
            nodeCard.className = 'node-card';
            nodeCard.innerHTML = `
                <div class="node-header">${dept.department_name}</div>
                <div class="node-subtitle">Department</div>
                <div class="node-actions">
                    <button type="button" onclick="addNode(${dept.id}, ${dept.branch_id})" title="Unterabteilung hinzufügen">
                        <i class="feather icon-plus"></i>
                    </button>
                    <button type="button" onclick="toggleEmployees(this)" title="Mitarbeiter ein-/ausblenden">
                        <i class="feather icon-eye"></i>
                    </button>
                    <button type="button" onclick="assignDepartmentHead(${dept.id})" class="department_head" title="Abteilungsleiter festlegen">
                        <i class="feather icon-user"></i>
                    </button>
                    <button type="button" onclick="assignRepresentative(${dept.id})" class="department_representative" title="Stellvertretung festlegen">
                        <i class="feather icon-user-plus"></i>
                    </button>
                    <button type="button" onclick="removeNode(${dept.id})" title="Löschen">
                        <i class="feather icon-trash"></i>
                    </button>
                    <button type="button" onclick="toggleChildren(this)" title="Unterabteilungen ein-/ausblenden">
                        <i class="feather icon-chevron-down"></i>
                    </button>
                </div>

                <!-- Head of department -->
                <div class="node-head node-head-main">
                    <div class="avatar mr-1">
                        <img src="${imageSrc}" alt="Head Image" height="32" width="32"
                             class="delete-leader" data-department-id="${dept.id}">
                    </div>
                    <span>${headName}</span>
                </div>
                <span class="node-label">Abteilungsleiter</span>

                <!-- Representative -->
                <div class="node-head node-head-repr">
                    <div class="avatar mr-1">
                        <img src="${repImageSrc}" alt="Representative" height="28" width="28" ${repDeleteAttrs}>
                    </div>
                    <span>${repName}</span>
                </div>
                <span class="node-label">Stellvertretung</span>

                <div class="employees" id="employees-${dept.id}">
                    <ul class="list-unstyled users-list m-0 d-flex align-items-center"></ul>
                </div>

                <div class="drop-child-zone" data-drop-child="1">
                    Als Unterabteilung hier ablegen
                </div>
            `;

            const verticalLine = document.createElement('div');
            verticalLine.className = 'vertical-line';

            nodeContainer.appendChild(verticalLine);
            nodeContainer.appendChild(nodeCard);
            parentListEl.appendChild(nodeContainer);

            const wrapper = document.createElement('div');
            wrapper.className = 'children-wrapper';

            const line = document.createElement('div');
            line.className = 'horizontal-line';

            const list = document.createElement('div');
            list.className = 'children-list';

            wrapper.appendChild(line);
            wrapper.appendChild(list);
            nodeContainer.appendChild(wrapper);

            loadEmployees(dept.id);
            renderTree(departments, dept.id, list);
        });
    }

    /* ==========================
     * TOGGLE CHILDREN & EMPLOYEES
     * ========================== */

    function toggleChildren(btn) {
        const nodeContainer = btn.closest('.node-container');
        if (!nodeContainer) return;

        const wrapper = Array.from(nodeContainer.children)
            .find(el => el.classList && el.classList.contains('children-wrapper'));

        if (!wrapper) return;

        const icon = btn.querySelector('i');

        if (wrapper.classList.contains('hidden')) {
            wrapper.classList.remove('hidden');
            if (icon) icon.className = 'feather icon-chevron-down';
        } else {
            wrapper.classList.add('hidden');
            if (icon) icon.className = 'feather icon-chevron-up';
        }
    }

    function toggleEmployees(btn) {
        const nodeCard = btn.closest('.node-card');
        if (!nodeCard) return;

        const employees = nodeCard.querySelector('.employees');
        if (!employees) return;

        const icon = btn.querySelector('i');

        if (employees.classList.contains('hidden')) {
            employees.classList.remove('hidden');
            if (icon) icon.className = 'feather icon-eye';
        } else {
            employees.classList.add('hidden');
            if (icon) icon.className = 'feather icon-eye-off';
        }
    }

    /* ==========================
     * DRAG & DROP: DEPARTMENTS
     * ========================== */

    function orgOnMove(evt, originalEvent) {
        const rel = evt.related || evt.to;
        if (!rel) return true;

        const node = rel.classList && rel.classList.contains('node-container')
            ? rel
            : rel.closest('.node-container');

        if (!node) return true;

        const rect = node.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        if (originalEvent.clientY > midY) {
            return false; // bottom half → no swap
        }

        return true;
    }

    function initializeDragAndDrop() {
        document.querySelectorAll('.children-list').forEach(list => {
            if (list.dataset.sortableInit === '1') return;
            list.dataset.sortableInit = '1';

            new Sortable(list, {
                group: "departments",
                animation: 180,
                draggable: ".node-container",
                handle: ".node-card",
                ghostClass: "org-ghost",
                fallbackOnBody: true,
                swapThreshold: 0.45,

                onStart: function () {
                    document.querySelectorAll('.drop-child-zone').forEach(z => {
                        z.classList.add('is-active');
                    });
                },

                onMove: orgOnMove,

                onEnd: function (evt) {
                    document.querySelectorAll('.drop-child-zone').forEach(z => {
                        z.classList.remove('is-active');
                    });

                    const draggedNode = evt.item;
                    const draggedNodeId = draggedNode?.dataset?.id;
                    if (!draggedNodeId) {
                        console.error("Dragged node id missing");
                        return;
                    }

                    let newParentId = null;
                    let newOrder = 0;

                    const pointerEvent = evt.originalEvent || evt.event;
                    let dropChildZone = null;

                    if (pointerEvent && pointerEvent.clientX != null && pointerEvent.clientY != null) {
                        const el = document.elementFromPoint(pointerEvent.clientX, pointerEvent.clientY);
                        if (el) {
                            dropChildZone = el.closest('.drop-child-zone');
                        }
                    }

                    if (dropChildZone) {
                        const targetNode = dropChildZone.closest('.node-container');

                        if (targetNode && targetNode !== draggedNode) {
                            newParentId = targetNode.dataset.id || null;

                            let childrenWrapper = Array.from(targetNode.children)
                                .find(el => el.classList && el.classList.contains('children-wrapper'));

                            if (!childrenWrapper) {
                                childrenWrapper = document.createElement('div');
                                childrenWrapper.className = 'children-wrapper';

                                const line = document.createElement('div');
                                line.className = 'horizontal-line';

                                const listEl = document.createElement('div');
                                listEl.className = 'children-list';

                                childrenWrapper.appendChild(line);
                                childrenWrapper.appendChild(listEl);
                                targetNode.appendChild(childrenWrapper);

                                new Sortable(listEl, {
                                    group: "departments",
                                    animation: 180,
                                    draggable: ".node-container",
                                    handle: ".node-card",
                                    ghostClass: "org-ghost",
                                    fallbackOnBody: true,
                                    swapThreshold: 0.45,
                                    onStart: this.options.onStart,
                                    onEnd: this.options.onEnd,
                                    onMove: this.options.onMove
                                });
                            }

                            let childrenList = childrenWrapper.querySelector('.children-list');
                            if (!childrenList) {
                                childrenList = document.createElement('div');
                                childrenList.className = 'children-list';
                                childrenWrapper.appendChild(childrenList);
                            }

                            childrenList.appendChild(draggedNode);

                            const siblings = Array.from(childrenList.children)
                                .filter(el => el.classList && el.classList.contains('node-container'));
                            newOrder = siblings.indexOf(draggedNode);

                            updateNodeParent(draggedNodeId, newParentId, newOrder);
                            return;
                        }
                    }

                    const parentNode = evt.to.closest('.node-container');
                    newParentId = parentNode ? parentNode.dataset.id : null;

                    const siblings = Array.from(evt.to.children)
                        .filter(el => el.classList && el.classList.contains('node-container'));
                    newOrder = siblings.indexOf(draggedNode);

                    updateNodeParent(draggedNodeId, newParentId, newOrder);
                }
            });
        });
    }

    function updateNodeParent(nodeId, newParentId, newOrder) {
        $.ajax({
            url: "/update/node",
            type: "POST",
            data: {
                id: nodeId,
                parent_id: newParentId,
                order: newOrder,
                _token: CSRF
            },
            success: function (response) {
                if (response.success) {
                    loadDepartments();
                    showToast("success", "Hierarchy updated successfully!");
                } else {
                    showToast("error", response.message || "Error updating hierarchy");
                }
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                showToast("error", "Something went wrong!");
            }
        });
    }

    /* ==========================
     * EMPLOYEE POOL (RIGHT SIDEBAR)
     * ========================== */

    function loadAllEmployees() {
        $.ajax({
            url: "{{ route('orgnaization.get.employee') }}",
            type: "GET",
            success: function (response) {
                const list = document.getElementById('allEmployeesList');
                if (!list) return;

                list.innerHTML = "";

                if (!response.length) {
                    list.innerHTML = "<li class='employees-pool-empty'>Keine aktiven Mitarbeiter gefunden.</li>";
                    return;
                }

               response.forEach(emp => {
                    const empImage = emp.image ? (IMG_EMP + emp.image) : IMG_DEFAULT;

                    const li = document.createElement('li');
                    li.className = 'avatar employee-pool-item';
                    li.setAttribute('data-employee-id', emp.id);

                    // 👇 NEW: store working hours from employees table
                    li.setAttribute('data-working-hour', emp.working_hour ?? 0);

                    li.innerHTML = `
                        <img class="media-object rounded-circle"
                            src="${empImage}"
                            alt="${emp.name} ${emp.lastname}"
                            height="30" width="30"
                            title="${emp.name} ${emp.lastname}">
                    `;
                    list.appendChild(li);
                });


                initializeEmployeePoolDrag();
            },
            error: function (xhr) {
                console.error("Error loading all employees:", xhr.responseText);
                const list = document.getElementById('allEmployeesList');
                if (list) {
                    list.innerHTML = "<li class='employees-pool-empty text-danger'>Fehler beim Laden der Mitarbeiter.</li>";
                }
            }
        });
    }

    function initializeEmployeePoolDrag() {
        const pool = document.getElementById('allEmployeesList');
        if (!pool || pool.dataset.sortableInit === '1') return;
        pool.dataset.sortableInit = '1';

        new Sortable(pool, {
            group: {
                name: 'employees',
                pull: 'clone',   // drag copy OUT of pool
                put: false       // nothing can be dropped BACK into pool
            },
            sort: false,
            animation: 150,
            draggable: '.avatar',
            ghostClass: 'org-ghost'
        });
    }

    /* ==========================
     * EMPLOYEES PER NODE
     * ========================== */

    function initializeEmployeeDragAndDrop() {
        document.querySelectorAll(".employees ul").forEach(container => {
            if (container.dataset.sortableInit === '1') return;
            container.dataset.sortableInit = '1';

            new Sortable(container, {
                group: {
                    name: "employees",
                    pull: true,
                    put: true
                },
                animation: 150,
                draggable: ".avatar",
                ghostClass: "org-ghost",

                onAdd: function (evt) {
                    const employeeId = evt.item.getAttribute("data-employee-id");
                    const newDepartmentId = evt.to.closest(".node-container").getAttribute("data-id");

                    if (!employeeId || !newDepartmentId) {
                        console.error("Invalid drag and drop operation.");
                        return;
                    }

                    // Assign into department_positions table
                    assignEmployeeToDepartment(employeeId, newDepartmentId);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        $('#branchSelect').select2({ placeholder: 'Bereich auswählen', width: 'style' });
        loadBranches();
        orgApplyZoom();
        loadAllEmployees();

        // 🔥 initialize recycle bin
        initializeEmployeeRecycleBin();
    });

    function initializeEmployeeRecycleBin() {
        const bin = document.getElementById('employeeRecycleBin');
        if (!bin) return;
        if (bin.dataset.sortableInit === '1') return;
        bin.dataset.sortableInit = '1';

        new Sortable(bin, {
            group: {
                name: 'employees',
                pull: false,  // you can't drag FROM the bin
                put: true     // but you can drop INTO it
            },
            animation: 150,
            draggable: '.avatar', // li.avatar from departments
            ghostClass: 'org-ghost',

            onAdd: function (evt) {
                const item = evt.item;
                const employeeId = item.getAttribute('data-employee-id');

                // Came from which department?
                const fromList = evt.from; // the <ul> inside .employees
                const fromNode = fromList.closest('.node-container');
                const departmentId = fromNode ? fromNode.getAttribute('data-id') : null;

                if (!employeeId || !departmentId) {
                    console.error("Missing employeeId or departmentId on drop into bin.");
                    item.remove(); // clean up
                    return;
                }

                // Call backend to unassign the employee from that department
                unassignEmployeeFromDepartment(employeeId, departmentId)
                    .then(() => {
                        // Remove the dragged chip from the bin (we only use bin as a trigger)
                        item.remove();
                    })
                    .catch(() => {
                        // In case of error, move it back to original list
                        fromList.appendChild(item);
                    });
            },

            onChoose: function () {
                bin.classList.add('is-active');
            },
            onUnchoose: function () {
                bin.classList.remove('is-active');
            },
            onEnd: function () {
                bin.classList.remove('is-active');
            }
        });
    }


    function unassignEmployeeFromDepartment(employeeId, departmentId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "{{ route('department.positions.unassign') }}", // we'll define this route next
                type: "POST",
                data: {
                    employee_id:   employeeId,
                    department_id: departmentId,
                    _token:        CSRF
                },
                success: function (response) {
                    if (response.success) {
                        showToast("success", response.message || "Mitarbeiter-Zuordnung entfernt.");
                        resolve();
                    } else {
                        showToast("error", response.message || "Zuordnung konnte nicht entfernt werden.");
                        reject();
                    }
                },
                error: function (xhr) {
                    console.error("Error unassigning employee:", xhr.responseText);
                    showToast("error", "Fehler beim Entfernen der Zuordnung.");
                    reject();
                }
            });
        });
    }



    function loadEmployees(departmentId) {
        $.ajax({
            url: `/department/employees/${departmentId}`,
            type: "GET",
            success: function (response) {
                let employeesContainer = document.querySelector(`#employees-${departmentId} ul`);
                if (!employeesContainer) return;

                employeesContainer.innerHTML = "";

                if (!response.length) {
                    employeesContainer.innerHTML = "";
                } else {
                   response.forEach(emp => {
                        let empImage = emp.image ? (IMG_EMP + emp.image) : IMG_DEFAULT;
                        let li = document.createElement('li');
                        li.className = 'avatar pull-up';
                        li.setAttribute('data-employee-id', emp.id);

                        // 👇 NEW: if API returns working_hour, store it
                        if (typeof emp.working_hour !== 'undefined') {
                            li.setAttribute('data-working-hour', emp.working_hour ?? 0);
                        }

                        li.innerHTML = `<img class="media-object rounded-circle" src="${empImage}" alt="Employee Image" height="25" width="25" title="${emp.name} ${emp.lastname}">`;
                        employeesContainer.appendChild(li);
                    }); 
                }

                initializeEmployeeDragAndDrop();
            },
            error: function (xhr) {
                console.error("Error loading employees:", xhr.responseText);
            }
        });
    }

    // Call this both for pool → node drops and node → node moves
        function assignEmployeeToDepartment(employeeId, departmentId) {
            // find employee DOM element → get base working hours
            const empEl = document.querySelector('.avatar[data-employee-id="' + employeeId + '"]');
            const rawHours = parseFloat(empEl?.dataset.workingHour || '0');
            const baseHours = isNaN(rawHours) ? 0 : rawHours;
            const baseHoursLabel = baseHours.toFixed(2);

            // build options for position select
            const positionOptions = POSITIONS.map(p =>
                `<option value="${p.id}">${p.position}</option>`
            ).join('');

            // 🔥 FIRST: ask backend how much percent is still free
            $.ajax({
                url: "{{ route('department.positions.remaining') }}",
                type: "GET",
                data: { employee_id: employeeId },
                success: function (res) {
                    if (!res.success) {
                        showToast("error", res.message || "Fehler beim Prüfen der Auslastung.");
                        return;
                    }

                    const used      = parseFloat(res.used  ?? 0);
                    const remaining = parseFloat(res.remaining ?? 0);

                   if (remaining <= 0) {
                        // statt nur Fehlermeldung -> Editor öffnen
                        showToast("error", "Dieser Mitarbeiter ist bereits zu 100% verplant.");

                        openEmployeeAllocationEditor(employeeId);
                        return;
                    }


                    // Now open the SweetAlert with info about remaining %
                    Swal.fire({
                        title: "Position & Verteilung festlegen",
                        width: 650,
                        html: `
                            <div style="font-size:12px; text-align:left;">
                                <div style="margin-bottom:8px; padding:6px 10px; border-radius:8px; background:#ecfdf3; border:1px solid #bbf7d0;">
                                    <div style="font-size:11px; color:#166534;">
                                        Bereits verplant: <strong>${used.toFixed(2)}%</strong> –
                                        Verfügbar: <strong>${remaining.toFixed(2)}%</strong>
                                    </div>
                                </div>

                                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                    
                                    <!-- LEFT COLUMN -->
                                    <div style="flex:1 1 260px; min-width:260px;">
                                        <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">Position</label>
                                        <select id="swal-position" class="form-control" style="width:100%; margin-bottom:10px;">
                                            <option value="">-- Position auswählen --</option>
                                            ${positionOptions}
                                        </select>

                                        <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                            Prozent in dieser Abteilung (%) – max. ${remaining.toFixed(2)}%
                                        </label>
                                        <input id="swal-percent" type="number" min="0" max="100"
                                            class="swal2-input"
                                            placeholder="z.B. 50"
                                            style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                        <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                            Arbeitsstunden für diese Abteilung
                                        </label>
                                        <input id="swal-working-hours" type="number" step="0.01"
                                            class="swal2-input"
                                            placeholder="wird aus Prozent berechnet"
                                            style="width:100%; box-sizing:border-box; margin:4px 0 6px;"
                                        >

                                        <small style="color:#6b7280;">
                                            Basis-Arbeitszeit Mitarbeiter:
                                            <strong>${baseHoursLabel} h / Woche</strong>
                                        </small>
                                    </div>

                                    <!-- RIGHT COLUMN -->
                                    <div style="flex:1 1 260px; min-width:260px;">
                                        <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                            Montage-Anteil (%)
                                        </label>
                                        <input id="swal-montage-percent" type="number" min="0" max="100"
                                            class="swal2-input"
                                            placeholder="z.B. 60"
                                            style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                        <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                            Office-Anteil (%)
                                        </label>
                                        <input id="swal-office-percent" type="number" min="0" max="100"
                                            class="swal2-input"
                                            placeholder="z.B. 40"
                                            style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                        <div style="margin-top:4px; padding:8px 10px; border-radius:8px; background:#0b1120; border:1px solid #1f2937;">
                                            <div style="font-size:11px; font-weight:600; margin-bottom:4px;">Hinweis</div>
                                            <div style="font-size:11px; color:#9ca3af;">
                                                • <strong>Gesamt-Prozent</strong> über alle Abteilungen darf 100% nicht überschreiten.<br>
                                                • <strong>Montage% + Office% = Gesamt-Prozent</strong> (z.B. 25 + 25 = 50).<br> 
                                                • Arbeitsstunden werden automatisch aus Mitarbeiterstunden × Prozent berechnet.
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: "Speichern",
                        cancelButtonText: "Abbrechen",

                        didOpen: () => {
                            $('#swal-position').select2({
                                width: '100%',
                                dropdownParent: $('.swal2-container'),
                                placeholder: '-- Position auswählen --',
                                allowClear: true
                            });

                            const percentInput = document.getElementById('swal-percent');
                            const workingHoursInput = document.getElementById('swal-working-hours');

                            const recalc = () => {
                                const p = parseFloat(percentInput.value || '0');
                                const hours = baseHours * (p / 100);
                                if (!isNaN(hours)) {
                                    workingHoursInput.value = hours.toFixed(2);
                                }
                            };

                            percentInput.addEventListener('input', recalc);
                        },

                        preConfirm: () => {
                            const positionId      = $('#swal-position').val();
                            const percent         = parseFloat(document.getElementById('swal-percent').value || '0');
                            const montagePercent  = parseFloat(document.getElementById('swal-montage-percent').value || '0');
                            const officePercent   = parseFloat(document.getElementById('swal-office-percent').value || '0');
                            const workingHours    = parseFloat(document.getElementById('swal-working-hours').value || '0');

                            if (!positionId) {
                                Swal.showValidationMessage("Bitte eine Position auswählen");
                                return false;
                            }
                            if (percent <= 0 || percent > 100) {
                                Swal.showValidationMessage("Bitte einen gültigen Prozentwert (1–100) eingeben");
                                return false;
                            }
                            // 🔥 client-side check vs remaining
                            if (percent > remaining + 0.0001) {
                                Swal.showValidationMessage(
                                    "Dieser Mitarbeiter hat nur noch " + remaining.toFixed(2) + "% verfügbar."
                                );
                                return false;
                            }
                            // allow small float error
                            if (Math.abs((montagePercent + officePercent) - percent) > 0.0001) {
                                Swal.showValidationMessage(
                                    "Montage% + Office% müssen zusammen dem Gesamt-Prozent entsprechen (z.B. 25 + 25 = 50)."
                                );
                                return false;
                            }

                            if (workingHours < 0) {
                                Swal.showValidationMessage("Arbeitsstunden müssen ≥ 0 sein");
                                return false;
                            }

                            return {
                                position_id:      positionId,
                                percent:          percent,
                                montage_percent:  montagePercent,
                                office_percent:   officePercent,
                                working_hours:    workingHours
                            };
                        }
                    }).then(result => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        const payload = result.value;

                        $.ajax({
                            url: "{{ route('department.positions.assign') }}",
                            type: "POST",
                            data: {
                                employee_id:      employeeId,
                                department_id:    departmentId,
                                position_id:      payload.position_id,
                                percent:          payload.percent,
                                montage_percent:  payload.montage_percent,
                                office_percent:   payload.office_percent,
                                working_hours:    payload.working_hours,
                                _token:           CSRF
                            },
                            success: function (response) {
                                if (response.success) {
                                    showToast("success", response.message || "Zuordnung gespeichert.");
                                } else {
                                    showToast("error", response.message || "Zuordnung konnte nicht gespeichert werden.");
                                }
                            },
                            error: function (xhr) {
                                // If backend also complains about percent>remaining, show that
                                let msg = "Fehler beim Speichern der Zuordnung.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                console.error("Error assigning employee to department:", xhr.responseText);
                                showToast("error", msg);
                            }
                        });
                    });
                },
                error: function (xhr) {
                    console.error("Error getting remaining percent:", xhr.responseText);
                    showToast("error", "Fehler beim Prüfen der Auslastung.");
                }
            });
        }


        function openEmployeeAllocationEditor(employeeId) {
            // small helper to build URL with ID
            const urlShow   = "{{ route('department.positions.employee', ':id') }}".replace(':id', employeeId);
            const urlUpdate = "{{ route('department.positions.employee.update', ':id') }}".replace(':id', employeeId);

            $.ajax({
                url: urlShow,
                type: "GET",
                success: function (res) {
                    if (!res.success) {
                        showToast("error", res.message || "Fehler beim Laden der Auslastung.");
                        return;
                    }

                    const emp = res.employee;
                    const allocations = res.allocations;

                    if (!allocations.length) {
                        showToast("info", "Dieser Mitarbeiter hat noch keine Zuordnungen.");
                        return;
                    }

                    const empName = (emp.name || "") + " " + (emp.lastname || "");
                    const baseHours = emp.working_hour ?? 0;

                    const rowsHtml = allocations.map(a => `
                        <tr data-id="${a.id}">
                            <td>${a.department_name}</td>
                            <td>${a.position}</td>
                            <td style="width:90px;">
                                <input type="number" class="form-control form-control-sm alloc-percent"
                                    value="${a.percent}" min="0" max="100">
                            </td>
                            <td style="width:90px;">
                                <input type="number" class="form-control form-control-sm alloc-montage"
                                    value="${a.montage_percent}" min="0" max="100">
                            </td>
                            <td style="width:90px;">
                                <input type="number" class="form-control form-control-sm alloc-office"
                                    value="${a.office_percent}" min="0" max="100">
                            </td>
                        </tr>
                    `).join('');

                    Swal.fire({
                        title: "Auslastung bearbeiten",
                        width: 850,
                        html: `
                            <div style="text-align:left; font-size:12px;">
                                <p style="margin-bottom:6px;">
                                    <strong>${empName}</strong><br>
                                    Basis-Arbeitszeit: <strong>${baseHours.toFixed ? baseHours.toFixed(2) : baseHours} h / Woche</strong><br>
                                    Aktuelle Summe: <strong id="alloc-total">${res.total_percent.toFixed(2)}%</strong>
                                </p>

                                <table class="table table-sm table-bordered" style="font-size:11px;">
                                    <thead>
                                        <tr>
                                            <th>Abteilung</th>
                                            <th>Position</th>
                                            <th>Prozent (%)</th>
                                            <th>Montage (%)</th>
                                            <th>Office (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="alloc-rows">
                                        ${rowsHtml}
                                    </tbody>
                                </table>

                                <small class="text-muted">
                                    • Summe aller Prozentwerte darf 100% nicht überschreiten.<br>
                                    • Für jede Zeile muss Montage% + Office% = 100% sein.
                                </small>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: "Speichern",
                        cancelButtonText: "Abbrechen",

                        didOpen: () => {
                            const tbody = document.getElementById('alloc-rows');
                            const totalLabel = document.getElementById('alloc-total');

                            const recalc = () => {
                                let sum = 0;
                                tbody.querySelectorAll('.alloc-percent').forEach(input => {
                                    const v = parseFloat(input.value || '0');
                                    if (!isNaN(v)) sum += v;
                                });
                                totalLabel.textContent = sum.toFixed(2) + '%';
                                totalLabel.style.color = sum > 100.0001 ? 'red' : '';
                            };

                            tbody.addEventListener('input', (e) => {
                                if (e.target.classList.contains('alloc-percent')) {
                                    recalc();
                                }
                            });
                        },

                        preConfirm: () => {
                            const rows = Array.from(document.querySelectorAll('#alloc-rows tr'));

                            const allocationsPayload = rows.map(tr => {
                                return {
                                    id:               tr.dataset.id,
                                    percent:          parseFloat(tr.querySelector('.alloc-percent').value || '0'),
                                    montage_percent:  parseFloat(tr.querySelector('.alloc-montage').value || '0'),
                                    office_percent:   parseFloat(tr.querySelector('.alloc-office').value || '0'),
                                };
                            });

                            let sum = 0;
                            for (const r of allocationsPayload) {
                                if (isNaN(r.percent) || r.percent < 0 || r.percent > 100) {
                                    Swal.showValidationMessage("Alle Prozentwerte müssen zwischen 0 und 100 liegen.");
                                    return false;
                                }
                                if (Math.abs((r.montage_percent + r.office_percent) - r.percent) > 0.0001) {
                                    Swal.showValidationMessage(
                                        "Für jede Zeile muss gelten: Montage% + Office% = Gesamt-Prozent dieser Zeile."
                                    );
                                    return false;
                                }

                                sum += r.percent;
                            }

                            if (sum > 100.0001) {
                                Swal.showValidationMessage(
                                    "Gesamtsumme aller Prozentwerte darf 100% nicht überschreiten (aktuell: " + sum.toFixed(2) + "%)."
                                );
                                return false;
                            }

                            return { allocations: allocationsPayload };
                        }
                    }).then(result => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            url: urlUpdate,
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json'
                            },
                            contentType: 'application/json',
                            data: JSON.stringify({
                                allocations: result.value.allocations
                            }),
                            success: function (resp) {
                                if (resp.success) {
                                    showToast("success", resp.message || "Auslastung gespeichert.");
                                } else {
                                    showToast("error", resp.message || "Auslastung konnte nicht gespeichert werden.");
                                }
                            },
                            error: function (xhr) {
                                let msg = "Fehler beim Speichern der Auslastung.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                console.error("Update error:", xhr.responseText);
                                showToast("error", msg);
                            }
                        });
                    });
                },
                error: function (xhr) {
                    console.error("Error loading allocations:", xhr.responseText);
                    showToast("error", "Fehler beim Laden der Auslastung.");
                }
            });
        }

    /* ==========================
     * NODES / BRANCHES CRUD
     * ========================== */
    function addNode(parentId, branchId) {
        Swal.fire({
            title: parentId ? "Neue Unterabteilung" : "Neue Abteilung",
            input: "text",
            inputPlaceholder: "Name der Abteilung",
            showCancelButton: true,
            confirmButtonText: "Speichern",
            preConfirm: (name) => {
                if (!name) {
                    Swal.showValidationMessage("Bitte Namen eingeben");
                    return false;
                }
                return $.ajax({
                    url: "/departments",
                    type: "POST",
                    data: {
                        name: name,
                        parent_id: parentId,
                        branch_id: branchId,
                        _token: CSRF
                    }
                }).then(function (response) {
                    if (!response.success) {
                        throw new Error(response.message || "Fehler beim Speichern");
                    }
                    return response;
                }).catch(function (error) {
                    Swal.showValidationMessage(error.message);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                loadDepartments();
                showToast("success", "Department added successfully!");
            }
        });
    }

    function removeNode(nodeId) {
        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the department and all its sub-departments.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/departments/${nodeId}`,
                    type: "DELETE",
                    data: { _token: CSRF },
                    success: function (response) {
                        if (response.success) {
                            loadDepartments();
                            showToast("success", "Department has been removed.");
                        } else {
                            showToast("error", response.message || "Could not delete department.");
                        }
                    },
                    error: function () {
                        showToast("error", "Something went wrong!");
                    }
                });
            }
        });
    }

    function addBranch() {
        Swal.fire({
            title: "Neuen Bereich anlegen",
            input: "text",
            inputPlaceholder: "Name des Bereichs",
            showCancelButton: true,
            confirmButtonText: "Speichern",
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage("Bitte einen Namen eingeben");
                    return false;
                }
                return $.ajax({
                    url: "/branches",
                    type: "POST",
                    data: {
                        branch: value,
                        _token: CSRF
                    }
                }).then(function (response) {
                    if (!response.success) {
                        throw new Error(response.message || "Fehler beim Speichern");
                    }
                    return response;
                }).catch(function (err) {
                    Swal.showValidationMessage(err.message);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showToast("success", "Bereich wurde angelegt.");
                loadBranches();
            }
        });
    }

    function removeBranch(branchId) {
        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the branch and all departments under it.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `delete/branches/${branchId}`,
                    type: "DELETE",
                    data: { _token: CSRF },
                    success: function (response) {
                        if (response.success) {
                            loadBranches();
                            showToast("success", "Branch has been removed.");
                        } else {
                            showToast("error", response.message || "Could not delete branch.");
                        }
                    },
                    error: function () {
                        showToast("error", "Something went wrong!");
                    }
                });
            }
        });
    }

    /* ==========================
     * HEAD & REPRESENTATIVE
     * ========================== */

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-leader")) {
            let departmentId = event.target.getAttribute("data-department-id");

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie den Abteilungsleiter wirklich entfernen?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ja, löschen!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDepartmentLeader(departmentId);
                }
            });
        }

        if (event.target.classList.contains("delete-representative")) {
            let departmentId = event.target.getAttribute("data-department-id");

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie die Stellvertretung wirklich entfernen?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ja, löschen!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDepartmentRepresentative(departmentId);
                }
            });
        }
    });

    function deleteDepartmentLeader(departmentId) {
        fetch("{{ route('department.delete.leader', '') }}/" + departmentId, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": CSRF,
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("success", data.message || "Abteilungsleiter entfernt.");
                loadDepartments();
            } else {
                showToast("error", data.error || "Ein Fehler ist aufgetreten.");
            }
        })
        .catch(error => {
            console.error("Fehler:", error);
            showToast("error", "Ein unerwarteter Fehler ist aufgetreten.");
        });
    }

    function deleteDepartmentRepresentative(departmentId) {
        fetch("{{ route('department.delete.representative', '') }}/" + departmentId, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": CSRF,
                "Content-Type": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast("success", data.message || "Stellvertretung entfernt.");
                loadDepartments();
            } else {
                showToast("error", data.error || "Ein Fehler ist aufgetreten.");
            }
        })
        .catch(error => {
            console.error("Fehler:", error);
            showToast("error", "Ein unerwarteter Fehler ist aufgetreten.");
        });
    }

    function assignDepartmentHead(departmentId) {
        $.ajax({
            url: "{{ route('orgnaization.get.employee') }}",
            type: "GET",
            success: function (response) {
                if (!response.length) {
                    showToast("warning", "No active employees found.");
                    return;
                }

                let employeeOptions = response.map(emp =>
                    `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
                ).join("");

                Swal.fire({
                    title: "Abteilungsleiter festlegen",
                    html: `<select id="employeeSelect" class="swal2-select">${employeeOptions}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    preConfirm: () => {
                        let selectedEmployeeId = document.getElementById("employeeSelect").value;
                        if (!selectedEmployeeId) {
                            Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen");
                        }
                        return selectedEmployeeId;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('orgnaization.store.head.employee') }}",
                            type: "POST",
                            data: {
                                employee_id: result.value,
                                department_id: departmentId,
                                _token: CSRF
                            },
                            success: function () {
                                showToast("success", "Abteilungsleiter gespeichert.");
                                updateDepartmentHead(departmentId);
                            },
                            error: function () {
                                showToast("error", "Abteilungsleiter konnte nicht gespeichert werden.");
                            }
                        });
                    }
                });
            },
            error: function () {
                showToast("error", "Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    function assignRepresentative(departmentId) {
        $.ajax({
            url: "{{ route('orgnaization.get.employee') }}",
            type: "GET",
            success: function (response) {
                if (!response.length) {
                    showToast("warning", "No active employees found.");
                    return;
                }

                let employeeOptions = response.map(emp =>
                    `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
                ).join("");

                Swal.fire({
                    title: "Stellvertretung festlegen",
                    html: `<select id="representativeSelect" class="swal2-select">${employeeOptions}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    preConfirm: () => {
                        let selectedEmployeeId = document.getElementById("representativeSelect").value;
                        if (!selectedEmployeeId) {
                            Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen");
                        }
                        return selectedEmployeeId;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('orgnaization.store.representative.employee') }}",
                            type: "POST",
                            data: {
                                employee_id: result.value,
                                department_id: departmentId,
                                _token: CSRF
                            },
                            success: function () {
                                showToast("success", "Stellvertretung wurde gespeichert.");
                                updateDepartmentRepresentative(departmentId);
                            },
                            error: function () {
                                showToast("error", "Stellvertretung konnte nicht gespeichert werden.");
                            }
                        });
                    }
                });
            },
            error: function () {
                showToast("error", "Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    function updateDepartmentHead(departmentId) {
        $.ajax({
            url: `/department/employee/head/get/${departmentId}`,
            type: "GET",
            success: function (response) {
                if (response) {
                    let departmentCard = document.getElementById(`node-${departmentId}`);
                    if (departmentCard) {
                        let imageSrc = response.image ? (IMG_EMP + response.image) : IMG_DEFAULT;
                        const headEl = departmentCard.querySelector(".node-head-main");
                        if (headEl) {
                            headEl.innerHTML = `
                                <div class="avatar mr-1">
                                    <img src="${imageSrc}" alt="Head" height="32" width="32"
                                         class="delete-leader" data-department-id="${departmentId}">
                                </div>
                                <span>${response.name} ${response.lastname}</span>
                            `;
                        }
                    }
                }
            }
        });
    }

    function updateDepartmentRepresentative(departmentId) {
        $.ajax({
            url: `/department/employee/representative/get/${departmentId}`,
            type: "GET",
            success: function (response) {
                if (response) {
                    let departmentCard = document.getElementById(`node-${departmentId}`);
                    if (departmentCard) {
                        let imageSrc = response.image ? (IMG_EMP + response.image) : IMG_DEFAULT;
                        const repEl = departmentCard.querySelector(".node-head-repr");
                        if (repEl) {
                            repEl.innerHTML = `
                                <div class="avatar mr-1">
                                    <img src="${imageSrc}" alt="Representative" height="28" width="28"
                                         class="delete-representative" data-department-id="${departmentId}">
                                </div>
                                <span>${response.name} ${response.lastname}</span>
                            `;
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
