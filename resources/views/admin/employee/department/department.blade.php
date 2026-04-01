@extends('admin.layouts.app')

@section('title') Abteilung @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<style>
    /* ====== PAGE WRAPPER / CARD ====== */

#table-hover-animation .card {
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
    overflow: visible; /* IMPORTANT: allow dropdown to escape */
}

#table-hover-animation .card-body {
    padding: 18px 20px 20px;
}

/* ====== TOP BAR: SEARCH / FILTER / BUTTONS ====== */

.department-topbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.department-topbar-left {
    display: flex;
    flex-direction: column;
}

.department-topbar-title {
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #111827;
}

.department-topbar-subtitle {
    font-size: 0.8rem;
    color: #6b7280;
}

.department-topbar-right {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.department-topbar-right .input-group {
    min-width: 230px;
}

.department-topbar-right .form-control {
    border-radius: 999px 0 0 999px;
}

.department-topbar-right .input-group-append .btn {
    border-radius: 0 999px 999px 0;
}

/* branch filter + create button row */
.department-topbar-right .branch-select-wrapper,
.department-topbar-right .create-button-wrapper {
    display: flex;
    align-items: center;
}

/* Select2 styling for branch filter */
#branchFilter + .select2-container--default .select2-selection--single {
    border-radius: 999px;
    border-color: rgba(148, 163, 184, 0.9);
    height: 34px;
}

#branchFilter + .select2-container--default
.select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    font-size: 0.8rem;
}

/* Create button */
.department-create-btn {
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.45rem 0.9rem;
}

/* ====== TABLE ====== */

#departmentTable {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

#departmentTable thead th {
    border-bottom: 1px solid rgba(203, 213, 225, 0.9);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: #6b7280;
    background: #f9fafb;
    padding-top: 10px;
    padding-bottom: 10px;
}

#departmentTable tbody tr.department-row {
    transition: background-color .12s ease, box-shadow .12s ease, transform .08s ease;
}

#departmentTable tbody tr.department-row:hover {
    background-color: #f3f4ff;
    box-shadow: 0 6px 16px rgba(148, 163, 184, 0.45);
    transform: translateY(-1px);
}

#departmentTable tbody td,
#departmentTable tbody th {
    vertical-align: middle;
    font-size: 0.85rem;
}

/* nicer first column (ID) */
#departmentTable tbody th[scope="row"] {
    font-weight: 600;
    color: #4b5563;
}

/* ====== HIERARCHY VISUALS ====== */

/* little vertical guideline on indented children */
#departmentTable tbody tr[data-parent]:not([data-parent=""]) td:first-child {
    position: relative;
}

#departmentTable tbody tr[data-parent]:not([data-parent=""]) td:first-child::before {
    content: "";
    position: absolute;
    left: 6px;
    top: -6px;
    bottom: -6px;
    width: 1px;
    background: linear-gradient(to bottom, rgba(148, 163, 184, 0.7), transparent);
}

/* label in front of child department text (optional, can remove) */
#departmentTable tbody tr[data-parent]:not([data-parent=""]) td:nth-child(2) a::before {
    content: "↳ ";
    color: #9ca3af;
}

/* toggle arrow next to departments with children */
.toggle-children {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    margin-right: 6px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.7);
    background: #f9fafb;
    transition: background .12s, transform .12s, box-shadow .12s, border-color .12s;
}

.toggle-children i {
    font-size: 0.75rem;
    color: #4b5563;
}

.toggle-children:hover {
    background: #e5e7eb;
    border-color: #4f46e5;
    box-shadow: 0 4px 10px rgba(148, 163, 184, 0.6);
    transform: translateY(-1px);
}

/* department name link */
#departmentTable tbody td a {
    color: #111827;
    font-weight: 500;
}

#departmentTable tbody td a:hover {
    text-decoration: underline;
}

/* ====== HEAD / REPRESENTATIVE ====== */

#departmentTable tbody td:nth-child(3),
#departmentTable tbody td:nth-child(4) {
    white-space: nowrap;
}

.department-head-name,
.department-rep-name {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* optional tiny colored dot to mark head/rep */
.department-head-name::before,
.department-rep-name::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 999px;
    display: inline-block;
}

.department-head-name::before {
    background: #22c55e;
}

.department-rep-name::before {
    background: #3b82f6;
}

/* ====== STATUS BADGES ====== */

.badge.badge-success {
    background: rgba(22, 163, 74, 0.12);
    color: #15803d;
    border-radius: 999px;
    padding: 0.25rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge.badge-danger {
    background: rgba(248, 113, 113, 0.12);
    color: #b91c1c;
    border-radius: 999px;
    padding: 0.25rem 0.6rem;
    font-size: 0.75rem;
    font-weight: 600;
}

/* ====== ACTION BUTTONS ====== */

#departmentTable tbody td:last-child .btn {
    border-radius: 999px;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

#departmentTable tbody td:last-child .btn i {
    font-size: 0.9rem;
}

#departmentTable tbody td:last-child .btn + .btn {
    margin-left: 4px;
}

/* ====== MODAL: "Neu Abteilung" ====== */

#default .modal-content {
    border-radius: 16px;
    border: 1px solid rgba(148, 163, 184, 0.45);
}

#default .modal-header {
    border-bottom: 1px solid rgba(226, 232, 240, 0.9);
}

#default .modal-title {
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
}

#default .modal-body label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #6b7280;
}

#default .modal-body .form-control {
    border-radius: 10px;
}

/* select2 inside modal */
#default .select2-container--default .select2-selection--single {
    border-radius: 10px;
}
/* ====== ANALYTICS CARDS ====== */

.department-analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.department-analytics-card {
    border-radius: 14px;
    border: 1px solid rgba(148, 163, 184, 0.35);
    background: linear-gradient(135deg, #f9fafb, #eef2ff);
    padding: 10px 12px;
    position: relative;
    overflow: hidden;
}

.department-analytics-label {
    font-size: 0.7rem;
    letter-spacing: .09em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 2px;
}

.department-analytics-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
}

.department-analytics-sub {
    font-size: 0.7rem;
    color: #6b7280;
}

/* simple inline progress "pill" */
.department-analytics-pill {
    position: absolute;
    right: 10px;
    bottom: 8px;
    font-size: 0.7rem;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(37, 99, 235, 0.08);
    color: #1d4ed8;
    border: 1px solid rgba(129, 140, 248, 0.55);
}

/* ====== FILTER + SORT CONTROLS ====== */

.department-filters-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

.department-filter-select {
    min-width: 140px;
}

.dept-sort-group {
    display: inline-flex;
    border-radius: 999px;
    overflow: hidden;
    border: 1px solid rgba(148, 163, 184, 0.7);
    background: #f9fafb;
}

.dept-sort-trigger {
    padding: 4px 10px;
    font-size: 0.7rem;
    border: none;
    background: transparent;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #6b7280;
}

.dept-sort-trigger + .dept-sort-trigger {
    border-left: 1px solid rgba(148, 163, 184, 0.5);
}

.dept-sort-trigger.active {
    background: #4f46e5;
    color: #f9fafb;
}

/* ====== CUSTOMER + COST CELLS ====== */

.dept-customers-main {
    font-weight: 600;
    font-size: 0.9rem;
}

.dept-customers-breakdown {
    font-size: 0.7rem;
    color: #6b7280;
}

.dept-cost-cell {
    white-space: nowrap;
    font-weight: 600;
    font-size: 0.9rem;
}
.department-topbar-right {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.department-topbar-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
    justify-content: flex-end;
}

.department-topbar-row-main {
    width: 100%;
}

.department-topbar-row-main .search-wrapper {
    min-width: 260px;
}

.department-topbar-row-main .create-button-wrapper {
    flex-shrink: 0;
}

.department-topbar-row-filters {
    width: 100%;
}

/* ====== ACTIONS DROPDOWN MENU ====== */
.dept-actions-cell {
    position: relative;
    white-space: nowrap;
}

.dept-actions-wrapper {
    position: relative;
    display: inline-block;
}

.dept-actions-trigger {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.7);
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    cursor: pointer;
}

.dept-actions-trigger i {
    font-size: 1rem;
    color: #4b5563;
}

.dept-actions-wrapper.open .dept-actions-trigger {
    background: #eef2ff;
    border-color: #4f46e5;
}

   .dept-actions-menu {
    position: absolute;
    right: 0;
    top: 110%;
    min-width: 190px;
    padding: 4px;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.18);
    border: 1px solid rgba(148, 163, 184, 0.35);
    z-index: 999;
    display: none;

    max-height: 260px;
    overflow-y: auto;
}

/* show when wrapper has .open */
.dept-actions-wrapper.open .dept-actions-menu {
    display: block;
}

/* drop-up variant */
.dept-actions-wrapper.drop-up .dept-actions-menu {
    top: auto;
    bottom: 110%;
}



.dept-actions-item {
    width: 100%;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #374151;
    cursor: pointer;
}

.dept-actions-wrapper.open .dept-actions-menu {
    display: block;
}

.dept-actions-item i {
    font-size: 0.9rem;
    color: #6b7280;
}

.dept-actions-item:hover {
    background: #eff6ff;
    color: #111827;
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
                        <h2 class="content-header-title float-left mb-0">ABTEILUNG</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="javascript:void(0)">Abteilung Liste</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

        <div class="content-body">
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">

                              <div class="department-analytics-grid">
                                        <div class="department-analytics-card">
                                        <div class="department-analytics-label">Abteilungen (Betrieb)</div>
                                            <div class="department-analytics-value" id="deptTotalCount">
                                                {{ $analytics['total'] ?? 0 }}
                                            </div>
                                            <div class="department-analytics-sub">
                                                Gesamtzahl im ausgewählten Betrieb
                                            </div>
                                            <span class="department-analytics-pill" id="deptActivePctPill">
                                                {{-- filled by JS --}}
                                            </span>
                                        </div>

                                        <div class="department-analytics-card">
                                            <div class="department-analytics-label">Aktiv</div>
                                            <div class="department-analytics-value" id="deptActiveCount">
                                                {{ $analytics['active'] ?? 0 }}
                                            </div>
                                            <div class="department-analytics-sub">
                                                Inaktive: <span id="deptInactiveCount">{{ $analytics['inactive'] ?? 0 }}</span>
                                            </div>
                                        </div>

                                        <div class="department-analytics-card">
                                            <div class="department-analytics-label">Leiter / Ohne Leiter</div>
                                            <div class="department-analytics-value">
                                                <span id="deptWithHeadCount">{{ $analytics['with_head'] ?? 0 }}</span>
                                                <span style="font-size:0.9rem; color:#6b7280;">&nbsp;/&nbsp;</span>
                                                <span id="deptWithoutHeadCount">{{ $analytics['without_head'] ?? 0 }}</span>
                                            </div>
                                            <div class="department-analytics-sub">
                                                Abteilungen mit definiertem Leiter / ohne Leiter
                                            </div>
                                        </div>

                                        <div class="department-analytics-card">
                                            <div class="department-analytics-label">Mitarbeiter im Betrieb</div>
                                            <div class="department-analytics-value" id="deptEmployeesCount">
                                                {{ $analytics['employees'] ?? 0 }}
                                            </div>
                                            <div class="department-analytics-sub">
                                                Über alle Abteilungen des Betriebs
                                            </div>
                                        </div>
                                    </div>
                                    <div class="department-topbar">
                                        {{-- LEFT: Title --}}
                                        <div class="department-topbar-left">
                                            <div class="department-topbar-title">Abteilungen</div>
                                            <div class="department-topbar-subtitle">
                                                Hierarchische Struktur &amp; Verantwortliche
                                            </div>
                                        </div>

                                        {{-- RIGHT: Controls (2 rows) --}}
                                        <div class="department-topbar-right">

                                            {{-- ROW 1: Sort + Search + New Department --}}
                                            <div class="department-topbar-row department-topbar-row-main">
                                                {{-- SORT GROUP --}}
                                                <div class="dept-sort-group" id="deptSortGroup">
                                                    <button type="button"
                                                            class="dept-sort-trigger {{ ($sortBy ?? 'order') === 'order' ? 'active' : '' }}"
                                                            data-sort-by="order"
                                                            data-sort-dir="asc">
                                                        Standard
                                                    </button>
                                                    <button type="button"
                                                            class="dept-sort-trigger {{ ($sortBy ?? '') === 'name' && ($sortDir ?? '') === 'asc' ? 'active' : '' }}"
                                                            data-sort-by="name"
                                                            data-sort-dir="asc">
                                                        Name A–Z
                                                    </button>
                                                    <button type="button"
                                                            class="dept-sort-trigger {{ ($sortBy ?? '') === 'name' && ($sortDir ?? '') === 'desc' ? 'active' : '' }}"
                                                            data-sort-by="name"
                                                            data-sort-dir="desc">
                                                        Name Z–A
                                                    </button>
                                                    <button type="button"
                                                            class="dept-sort-trigger {{ ($sortBy ?? '') === 'created_at' && ($sortDir ?? '') === 'desc' ? 'active' : '' }}"
                                                            data-sort-by="created_at"
                                                            data-sort-dir="desc">
                                                        Neueste
                                                    </button>
                                                </div>

                                                {{-- SEARCH --}}
                                                <div class="search-wrapper">
                                                    <div class="input-group">
                                                        <input type="text" id="searchDepartment" name="search" class="form-control"
                                                            placeholder="Abteilung suchen…">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary" type="button" onclick="loadDepartments()">Suchen</button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- CREATE BUTTON --}}
                                                <div class="create-button-wrapper">
                                                    <button type="button"
                                                            class="btn btn-outline-primary department-create-btn"
                                                            data-toggle="modal"
                                                            data-target="#default">
                                                        + Neue Abteilung
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- ROW 2: Branch + Dates + Status + Leiter --}}
                                            <div class="department-topbar-row department-topbar-row-filters">
                                                {{-- BRANCH SELECT --}}
                                                <div class="branch-select-wrapper">
                                                    <select name="filter" id="branchFilter" class="form-control">
                                                        @foreach ($branch as $br)
                                                            <option value="{{ $br->id }}" {{ $selectedBranch == $br->id ? 'selected' : '' }}>
                                                                {{ $br->branch }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- DATE RANGE --}}
                                                <div class="department-filters-wrapper">
                                                    <input type="date"
                                                        id="dateFrom"
                                                        class="form-control department-filter-select"
                                                        value="{{ $dateFrom ?? '' }}"
                                                        placeholder="Von Datum">

                                                    <input type="date"
                                                        id="dateTo"
                                                        class="form-control department-filter-select"
                                                        value="{{ $dateTo ?? '' }}"
                                                        placeholder="Bis Datum">
                                                </div>

                                                {{-- STATUS + HEAD FILTERS --}}
                                                <div class="department-filters-wrapper">
                                                    <select id="statusFilter"
                                                            class="form-control department-filter-select">
                                                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Status: Alle</option>
                                                        <option value="active" {{ ($statusFilter ?? 'all') === 'active' ? 'selected' : '' }}>Nur aktiv</option>
                                                        <option value="inactive" {{ ($statusFilter ?? 'all') === 'inactive' ? 'selected' : '' }}>Nur inaktiv</option>
                                                    </select>

                                                    <select id="headFilter"
                                                            class="form-control department-filter-select">
                                                        <option value="all" {{ ($headFilter ?? 'all') === 'all' ? 'selected' : '' }}>Leiter: Alle</option>
                                                        <option value="with_head" {{ ($headFilter ?? 'all') === 'with_head' ? 'selected' : '' }}>Nur mit Leiter</option>
                                                        <option value="without_head" {{ ($headFilter ?? 'all') === 'without_head' ? 'selected' : '' }}>Nur ohne Leiter</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                {{-- TABLE --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                         <table class="table table-striped mb-0">
                                           <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Abteilung</th>
                                                    <th scope="col">Abteilungsleiter</th>
                                                    <th scope="col">Stellvertretung</th>
                                                    <th scope="col">Kunden</th>
                                                    <th scope="col">Kosten (Monat)</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Aktion</th>
                                                </tr>
                                            </thead>

                                           <tbody id="sortable">
                                                @include('admin.employee.department.partials.table_rows', [
                                                    'structuredDepartments' => $structuredDepartments ?? collect(),
                                                    'employeeData'          => $employeeData ?? collect(),
                                                    'departmentStats'       => $departmentStats ?? [],
                                                ])
                                            </tbody>

                                        </table>

                                        </div>
                                    </div>
                                </div>

                                {{-- CREATE MODAL --}}
                                <div class="modal fade text-left" id="createDepartmentModal" tabindex="-1" role="dialog"
                                     aria-labelledby="createDepartmentLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="createDepartmentLabel">Neue Abteilung</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <form id="createDepartmentForm" novalidate>
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Abteilung</label>
                                                        <input type="text" class="form-control" name="department_name" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Betrieb</label>
                                                        <select name="branch_id"
                                                                id="branchFilters"
                                                                class="selectables form-control"
                                                                style="width:100%">
                                                            <option value="">-- Betrieb wählen --</option>
                                                            @foreach ($branch as $br)
                                                                <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Master-Abteilung</label>
                                                        <select name="parent_id"
                                                                id="parentDepartmentFilter"
                                                                class="selectables form-control"
                                                                style="width:100%">
                                                            <option value="">-- Master-Abteilung wählen --</option>
                                                        </select>
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
                                {{-- /CREATE MODAL --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

    </div>
</div>
@endsection
@section('script')
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    const CSRF_TOKEN        = '{{ csrf_token() }}';
    const ROUTE_INDEX       = '{{ route('department.info') }}';
    const ROUTE_CREATE      = '{{ route('department.create') }}';
    const ROUTE_UPDATE      = '{{ route('department.update') }}';
    const ROUTE_DESTROY     = '{{ route('department.destroy', ['id' => '___ID___']) }}'.replace('%5B%22id%22%5D', '___ID___'); // ugly but works
    const ROUTE_ORDER       = '{{ route('departments.update.order') }}';
    const ROUTE_FILTER_BRANCH = '{{ route('departments.filterByBranch') }}';
    const ROUTE_GET_EMP     = '{{ route('orgnaization.get.employee') }}';
    const ROUTE_STORE_HEAD  = '{{ route('orgnaization.store.head.employee') }}';
    const ROUTE_STORE_REP = '{{ route('orgnaization.store.representative.employee') }}';
    const ROUTE_POSITIONS_ASSIGN    = '{{ route('department.positions.assign') }}';
    const ROUTE_POSITIONS_REMAINING = '{{ route('department.positions.remaining') }}';
    const POSITIONS              = @json($positions ?? []);
    const ROUTE_DEPT_EMPLOYEES   = '{{ route('departments.employees', ['department' => '___ID___']) }}';   
    const ROUTE_POSITIONS_UPDATE   = '{{ route('department.positions.update') }}';

        const INITIAL_ANALYTICS = @json($analytics ?? []);
        window.DEPT_SORT_BY  = @json($sortBy ?? 'order');
        window.DEPT_SORT_DIR = @json($sortDir ?? 'asc');

        function updateAnalyticsCards(analytics) {
            if (!analytics) return;

            const total        = Number(analytics.total ?? 0);
            const active       = Number(analytics.active ?? 0);
            const inactive     = Number(analytics.inactive ?? 0);
            const withHead     = Number(analytics.with_head ?? 0);
            const withoutHead  = Number(analytics.without_head ?? 0);
            const employees    = Number(analytics.employees ?? 0);

            $('#deptTotalCount').text(total);
            $('#deptActiveCount').text(active);
            $('#deptInactiveCount').text(inactive);
            $('#deptWithHeadCount').text(withHead);
            $('#deptWithoutHeadCount').text(withoutHead);
            $('#deptEmployeesCount').text(employees);

            const pctActive = total > 0 ? Math.round((active / total) * 100) : 0;
            $('#deptActivePctPill').text(pctActive + '% aktiv');
        }


    // ======= TOAST HELPERS ===================================
    function toastSuccess(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: msg,
            showConfirmButton: false,
            timer: 2000
        });
    }

    function toastError(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: msg,
            showConfirmButton: false,
            timer: 2500
        });
    }

    // ======= GLOBAL: toggleChildren ==========================
    window.toggleChildren = function (parentId) {
        // toggle all children of this row (and hide sub-children when collapsing)
        const firstChild = document.querySelector(`tr[data-parent='${parentId}']`);
        if (!firstChild) return;

        const shouldShow = firstChild.style.display === 'none';

        const toggleRecursive = (id, show) => {
            const rows = document.querySelectorAll(`tr[data-parent='${id}']`);
            rows.forEach(row => {
                row.style.display = show ? 'table-row' : 'none';
                if (!show) {
                    const childId = row.getAttribute('data-id');
                    toggleRecursive(childId, false);
                }
            });
        };

        toggleRecursive(parentId, shouldShow);

        // change icon
        const icon = document.querySelector(`tr[data-id='${parentId}'] .toggle-children i`);
        if (icon) {
            icon.classList.toggle('icon-chevron-down',  shouldShow);
            icon.classList.toggle('icon-chevron-up',    !shouldShow);
        }
    };

    // ======= GLOBAL: drag & drop =============================
    window.initializeDragAndDrop = function () {
        const sortableTable = document.getElementById('sortable');
        if (!sortableTable || typeof Sortable === 'undefined') {
            return;
        }

        new Sortable(sortableTable, {
            group: "departments",
            animation: 150,
            draggable: "tr",
            onEnd: function () {
                const rows = document.querySelectorAll("#sortable tr");
                const departmentData = [];

                rows.forEach((row, index) => {
                    const rowId     = row.getAttribute("data-id");
                    let rowParentId = row.getAttribute("data-parent");
                    rowParentId     = rowParentId && rowParentId !== "null" ? rowParentId : null;

                    departmentData.push({
                        id: rowId,
                        parent_id: rowParentId,
                        order: index + 1
                    });
                });

                $.ajax({
                    url: ROUTE_ORDER,
                    type: "POST",
                    data: {
                        departments: departmentData,
                        _token: CSRF_TOKEN
                    },
                    success: function (response) {
                        if (response.success) {
                            toastSuccess("Die Abteilungsstruktur wurde aktualisiert.");
                        } else {
                            toastError(response.message || "Fehler beim Aktualisieren der Struktur.");
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        toastError("Fehler beim Aktualisieren der Struktur.");
                    }
                });
            }
        });
    };

    // ======= AJAX: reload department table ===================
        function loadDepartments() {
            const branchId = $('#branchFilter').val() || '{{ $selectedBranch ?? '' }}';
            const search   = $('#searchDepartment').val() || '';
            const status   = $('#statusFilter').val() || 'all';
            const head     = $('#headFilter').val() || 'all';

            // NEW: date range
            const dateFrom = $('#dateFrom').val() || '';
            const dateTo   = $('#dateTo').val() || '';

            $.ajax({
                url: ROUTE_INDEX,
                type: 'GET',
                data: {
                    branch_id: branchId,
                    search:    search,
                    status:    status,
                    head:      head,
                    sort_by:   window.DEPT_SORT_BY  || 'order',
                    sort_dir:  window.DEPT_SORT_DIR || 'asc',
                    date_from: dateFrom,
                    date_to:   dateTo
                },
                success: function (resp) {
                    if (!resp.success) {
                        toastError(resp.message || 'Abteilungen konnten nicht geladen werden.');
                        return;
                    }
                    $('#sortable').html(resp.html);
                    initializeDragAndDrop();

                    if (resp.analytics) {
                        updateAnalyticsCards(resp.analytics);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastError('Fehler beim Laden der Abteilungen.');
                }
            });
        }

    // ======= CRUD: delete ====================================
    window.deleteDepartment = function (departmentId) {
        Swal.fire({
            title: "Sind Sie sicher?",
            text: "Diese Abteilung (und ggf. Unterabteilungen) wird gelöscht.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ja, löschen!",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (!result.isConfirmed) return;

            const url = ROUTE_DESTROY.replace('___ID___', departmentId);

            $.ajax({
                url: url,
                type: "DELETE",
                data: { _token: CSRF_TOKEN },
                success: function (resp) {
                    if (resp.success) {
                        toastSuccess(resp.message || 'Abteilung wurde gelöscht.');
                        loadDepartments();
                    } else {
                        toastError(resp.message || 'Abteilung konnte nicht gelöscht werden.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastError('Fehler beim Löschen der Abteilung.');
                }
            });
        });
    };

    // ======= CRUD: edit ======================================
    window.editDepartment = function (departmentId) {
        const currentName = $(`tr[data-id='${departmentId}'] td:eq(0)`).text().trim() ||
                            $(`tr[data-id='${departmentId}'] a`).first().text().trim();

        Swal.fire({
            title: "Abteilung bearbeiten",
            input: "text",
            inputValue: currentName,
            showCancelButton: true,
            confirmButtonText: "Speichern",
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage("Bitte einen Namen eingeben");
                    return false;
                }

                return $.ajax({
                    url: ROUTE_UPDATE,
                    type: "POST",
                    data: {
                        id: departmentId,
                        department_name: value,
                        _token: CSRF_TOKEN
                    }
                }).catch((xhr) => {
                    Swal.showValidationMessage("Fehler beim Speichern");
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                toastSuccess("Abteilung wurde aktualisiert.");
                loadDepartments();
            }
        });
    };

    // Mitarbeiterliste je Abteilung anzeigen
        // Mitarbeiterliste je Abteilung anzeigen
        $(document).on('click', '.view_department_employees', function () {
            const deptId = $(this).data('department-id');
            const url    = ROUTE_DEPT_EMPLOYEES.replace('___ID___', deptId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (resp) {
                    if (!resp.success) {
                        toastError(resp.message || 'Mitarbeiter konnten nicht geladen werden.');
                        return;
                    }

                    const rows = resp.data || [];

                    if (!rows.length) {
                        Swal.fire({
                            title: resp.department.name,
                            html: `
                                <p style="font-size:13px;">
                                    In dieser Abteilung sind aktuell keine Mitarbeiter zugeordnet.
                                </p>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        id="btn-add-employee-in-modal">
                                    + Mitarbeiter hinzufügen
                                </button>
                            `,
                            didOpen: () => {
                                $('#btn-add-employee-in-modal').on('click', function () {
                                    Swal.close();
                                    chooseEmployeeForDepartment(deptId);
                                });
                            },
                            icon: 'info'
                        });
                        return;
                    }

                    const htmlRows = rows.map(r => {
                        const percent        = Number(r.percent ?? 0).toFixed(2);
                        const montagePercent = Number(r.montage_percent ?? 0).toFixed(2);
                        const officePercent  = Number(r.office_percent ?? 0).toFixed(2);
                        const hours          = Number(r.working_hours ?? 0).toFixed(2);
                        const baseHours      = Number(r.base_working_hour ?? 0).toFixed(2);

                        return `
                            <tr
                                class="dept-emp-row"
                                data-dp-id="${r.id}"
                                data-employee-id="${r.employee_id}"
                                data-position-id="${r.position_id}"
                                data-percent="${percent}"
                                data-montage-percent="${montagePercent}"
                                data-office-percent="${officePercent}"
                                data-working-hours="${hours}"
                                data-base-hours="${baseHours}"
                            >
                                <td>${r.name} ${r.lastname}</td>
                                <td>${r.position}</td>
                                <td>${percent}%</td>
                                <td>${montagePercent}%</td>
                                <td>${officePercent}%</td>
                                <td>${hours}</td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-edit-dept-emp">
                                        <i class="feather icon-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    Swal.fire({
                        title: `Mitarbeiter – ${resp.department.name}`,
                        width: 900,
                        html: `
                            <div class="mb-1" style="text-align:right;">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        id="btn-add-employee-in-modal">
                                    + Mitarbeiter hinzufügen
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Mitarbeiter</th>
                                            <th>Position</th>
                                            <th>Gesamt %</th>
                                            <th>Montage %</th>
                                            <th>Office %</th>
                                            <th>Std.</th>
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody>${htmlRows}</tbody>
                                </table>
                            </div>
                            <small style="font-size:11px; color:#6b7280;">
                                Tipp: Über den Stift-Button können Sie die Verteilung einer Zuordnung ändern.
                            </small>
                        `,
                        didOpen: () => {
                            // "Mitarbeiter hinzufügen" direkt aus dieser Liste
                            $('#btn-add-employee-in-modal').on('click', function () {
                                Swal.close();
                                chooseEmployeeForDepartment(deptId);
                            });
                        }
                    });
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    toastError('Fehler beim Laden der Mitarbeiter.');
                }
            });
        });


        // Edit an existing department_positions row from the list modal
        $(document).on('click', '.btn-edit-dept-emp', function () {
            const $row = $(this).closest('tr.dept-emp-row');

            const cfg = {
                dpId:            $row.data('dp-id'),
                employeeId:      $row.data('employee-id'),
                positionId:      $row.data('position-id'),
                percent:         parseFloat($row.data('percent') || '0'),
                montagePercent:  parseFloat($row.data('montage-percent') || '0'),
                officePercent:   parseFloat($row.data('office-percent') || '0'),
                workingHours:    parseFloat($row.data('working-hours') || '0'),
                baseHours:       parseFloat($row.data('base-hours') || '0'),
            };

            openAllocationEditModal(cfg);
        });


        function openAllocationEditModal(cfg) {
                const {
                    dpId,
                    employeeId,     // for backend checks if needed
                    positionId,
                    percent,
                    montagePercent,
                    officePercent,
                    workingHours,
                    baseHours
                } = cfg;

                const baseHoursLabel = (baseHours || 0).toFixed(2);

                const positionOptions = POSITIONS.map(p =>
                    `<option value="${p.id}">${p.position}</option>`
                ).join('');

                Swal.fire({
                    title: "Zuordnung bearbeiten",
                    width: 650,
                    html: `
                        <div style="font-size:12px; text-align:left;">
                            <div style="display:flex; gap:16px; flex-wrap:wrap;">

                                <!-- LEFT -->
                                <div style="flex:1 1 260px; min-width:260px;">
                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Position
                                    </label>
                                    <select id="swal-position-edit" class="form-control" style="width:100%; margin-bottom:10px;">
                                        <option value="">-- Position auswählen --</option>
                                        ${positionOptions}
                                    </select>

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Prozent in dieser Abteilung (%)
                                    </label>
                                    <input id="swal-percent-edit" type="number" min="0" max="100"
                                        class="swal2-input"
                                        value="${percent}"
                                        style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Arbeitsstunden für diese Abteilung
                                    </label>
                                    <input id="swal-working-hours-edit" type="number" step="0.01"
                                        class="swal2-input"
                                        value="${workingHours}"
                                        style="width:100%; box-sizing:border-box; margin:4px 0 6px;">

                                    <small style="color:#6b7280;">
                                        Basis-Arbeitszeit Mitarbeiter:
                                        <strong>${baseHoursLabel} h / Woche</strong>
                                    </small>
                                </div>

                                <!-- RIGHT -->
                                <div style="flex:1 1 260px; min-width:260px;">
                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Montage-Anteil (%)
                                    </label>
                                    <input id="swal-montage-percent-edit" type="number" min="0" max="100"
                                        class="swal2-input"
                                        value="${montagePercent}"
                                        style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                    <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                        Office-Anteil (%)
                                    </label>
                                    <input id="swal-office-percent-edit" type="number" min="0" max="100"
                                        class="swal2-input"
                                        value="${officePercent}"
                                        style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                    <div style="margin-top:4px; padding:8px 10px; border-radius:8px; background:#0b1120; border:1px solid #1f2937;">
                                        <div style="font-size:11px; font-weight:600; margin-bottom:4px;">Hinweis</div>
                                        <div style="font-size:11px; color:#9ca3af;">
                                            • <strong>Gesamt-Prozent</strong> über alle Abteilungen darf 100% nicht überschreiten (wird im Backend geprüft).<br>
                                            • <strong>Montage% + Office% = Gesamt-Prozent</strong> dieser Zeile.<br>
                                            • Arbeitsstunden können automatisch aus Prozent berechnet werden.
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
                        // Select2 für Position
                        $('#swal-position-edit').select2({
                            width: '100%',
                            dropdownParent: $('.swal2-container'),
                            placeholder: '-- Position auswählen --',
                            allowClear: true
                        });

                        // aktuelle Position vorauswählen
                        $('#swal-position-edit').val(String(positionId)).trigger('change');

                        const percentInput      = document.getElementById('swal-percent-edit');
                        const workingHoursInput = document.getElementById('swal-working-hours-edit');

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
                        const newPositionId   = $('#swal-position-edit').val();
                        const newPercent      = parseFloat(document.getElementById('swal-percent-edit').value || '0');
                        const newMontage      = parseFloat(document.getElementById('swal-montage-percent-edit').value || '0');
                        const newOffice       = parseFloat(document.getElementById('swal-office-percent-edit').value || '0');
                        const newWorkingHours = parseFloat(document.getElementById('swal-working-hours-edit').value || '0');

                        if (!newPositionId) {
                            Swal.showValidationMessage("Bitte eine Position auswählen");
                            return false;
                        }
                        if (newPercent <= 0 || newPercent > 100) {
                            Swal.showValidationMessage("Bitte einen gültigen Prozentwert (1–100) eingeben");
                            return false;
                        }

                        // Montage + Office müssen zusammen dem Zeilen-Prozent entsprechen
                        if (Math.abs((newMontage + newOffice) - newPercent) > 0.0001) {
                            Swal.showValidationMessage(
                                "Montage% + Office% müssen zusammen dem Gesamt-Prozent dieser Zeile entsprechen."
                            );
                            return false;
                        }

                        if (newWorkingHours < 0) {
                            Swal.showValidationMessage("Arbeitsstunden müssen ≥ 0 sein");
                            return false;
                        }

                        return {
                            id:              dpId,
                            position_id:     newPositionId,
                            percent:         newPercent,
                            montage_percent: newMontage,
                            office_percent:  newOffice,
                            working_hours:   newWorkingHours,
                            employee_id:     employeeId
                        };
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;

                    const payload = result.value;

                    $.ajax({
                        url: ROUTE_POSITIONS_UPDATE,
                        type: "POST",
                        data: {
                            id:              payload.id,
                            position_id:     payload.position_id,
                            percent:         payload.percent,
                            montage_percent: payload.montage_percent,
                            office_percent:  payload.office_percent,
                            working_hours:   payload.working_hours,
                            employee_id:     payload.employee_id,
                            _token:          CSRF_TOKEN
                        },
                        success: function (resp) {
                            if (resp.success) {
                                toastSuccess(resp.message || "Zuordnung aktualisiert.");
                                // Modal schließen – bei Bedarf kannst du danach die Liste neu laden
                                Swal.close();
                            } else {
                                toastError(resp.message || "Zuordnung konnte nicht aktualisiert werden.");
                            }
                        },
                        error: function (xhr) {
                            let msg = "Fehler beim Aktualisieren der Zuordnung.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            console.error(xhr.responseText);
                            toastError(msg);
                        }
                    });
                });
            }


    // ======= Department head assignment ======================
    function assignDepartmentHead(departmentId) {
        $.ajax({
            url: ROUTE_GET_EMP,
            type: "GET",
            success: function (employees) {
                if (!employees.length) {
                    toastError("Keine aktiven Mitarbeiter gefunden.");
                    return;
                }

                const options = employees.map(emp =>
                    `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
                ).join("");

                Swal.fire({
                    title: "Abteilungsleiter festlegen",
                    html: `<select id="employeeSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    preConfirm: () => {
                        const selected = document.getElementById('employeeSelect').value;
                        if (!selected) {
                            Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                        }
                        return selected;
                    }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTE_STORE_HEAD,
                        type: "POST",
                        data: {
                            employee_id:  result.value,
                            department_id: departmentId,
                            _token: CSRF_TOKEN
                        },
                        success: function (resp) {
                            toastSuccess("Abteilungsleiter gespeichert.");
                            loadDepartments();
                        },
                        error: function () {
                            toastError("Abteilungsleiter konnte nicht gespeichert werden.");
                        }
                    });
                });
            },
            error: function () {
                toastError("Mitarbeiter konnten nicht geladen werden.");
            }
        });
    }

    // delegate click for dynamically loaded buttons
    $(document).on('click', '.change_employee', function () {
        const deptId = $(this).data('department-id');
        assignDepartmentHead(deptId);
    });

    $(document).on('click', '.change_representative', function () {
        const deptId = $(this).data('department-id');
        assignDepartmentRepresentative(deptId);
    });


    function assignDepartmentRepresentative(departmentId) {
    $.ajax({
        url: ROUTE_GET_EMP,
        type: "GET",
        success: function (employees) {
            if (!employees.length) {
                toastError("Keine aktiven Mitarbeiter gefunden.");
                return;
            }

            const options = employees.map(emp =>
                `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`
            ).join("");

            Swal.fire({
                title: "Stellvertretung festlegen",
                html: `<select id="repSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                showCancelButton: true,
                confirmButtonText: "Speichern",
                preConfirm: () => {
                    const selected = document.getElementById('repSelect').value;
                    if (!selected) {
                        Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                    }
                    return selected;
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: ROUTE_STORE_REP,
                    type: "POST",
                    data: {
                        employee_id: result.value,
                        department_id: departmentId,
                        _token: CSRF_TOKEN
                    },
                    success: function (resp) {
                        toastSuccess(resp.message || "Stellvertretung gespeichert.");
                        loadDepartments();
                    },
                    error: function () {
                        toastError("Stellvertretung konnte nicht gespeichert werden.");
                    }
                });
            });
        },
        error: function () {
            toastError("Mitarbeiter konnten nicht geladen werden.");
        }
    });
}

// Click on "Mitarbeiter zuweisen" in table
$(document).on('click', '.assign_employee', function () {
    const departmentId = $(this).data('department-id');
    chooseEmployeeForDepartment(departmentId);
});

// Step 1: choose employee
function chooseEmployeeForDepartment(departmentId) {
    $.ajax({
        url: ROUTE_GET_EMP,
        type: "GET",
        success: function (employees) {
            if (!employees.length) {
                toastError("Keine aktiven Mitarbeiter gefunden.");
                return;
            }

            const options = employees.map(emp =>
                `<option value="${emp.id}" data-working-hour="${emp.working_hour ?? 0}">
                    ${emp.name} ${emp.lastname}
                 </option>`
            ).join("");

            Swal.fire({
                title: "Mitarbeiter auswählen",
                html: `<select id="empSelect" class="swal2-select" style="width:100%;">${options}</select>`,
                showCancelButton: true,
                confirmButtonText: "Weiter",
                didOpen: () => {
                    $('#empSelect').select2({
                        width: '100%',
                        dropdownParent: $('.swal2-container'),
                        placeholder: 'Mitarbeiter wählen'
                    });
                },
                preConfirm: () => {
                    const id = $('#empSelect').val();
                    if (!id) {
                        Swal.showValidationMessage("Bitte einen Mitarbeiter auswählen.");
                        return false;
                    }
                    // get working hours from selected option
                    const baseHours = parseFloat(
                        $('#empSelect option:selected').data('working-hour') || '0'
                    );
                    return { id, baseHours };
                }
            }).then(result => {
                if (!result.isConfirmed) return;
                const empId     = result.value.id;
                const baseHours = result.value.baseHours;
                openAllocationModal(empId, departmentId, baseHours);
            });
        },
        error: function () {
            toastError("Mitarbeiter konnten nicht geladen werden.");
        }
    });
}

// Step 2: check remaining % and let user enter percent/montage/office
function openAllocationModal(employeeId, departmentId, baseHours) {
    const baseHoursLabel = (baseHours || 0).toFixed(2);

    // 1) Ask backend how much percent is already used
    $.ajax({
        url: ROUTE_POSITIONS_REMAINING,
        type: "GET",
        data: { employee_id: employeeId },
        success: function (res) {
            if (!res.success) {
                toastError(res.message || "Fehler beim Prüfen der Auslastung.");
                return;
            }

            const used      = parseFloat(res.used  ?? 0);
            const remaining = parseFloat(res.remaining ?? 0);

            if (remaining <= 0) {
                toastError("Dieser Mitarbeiter ist bereits zu 100% verplant.");
                return;
            }

            const positionOptions = POSITIONS.map(p =>
                `<option value="${p.id}">${p.position}</option>`
            ).join('');

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

                            <!-- LEFT -->
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
                                    style="width:100%; box-sizing:border-box; margin:4px 0 6px;">

                                <small style="color:#6b7280;">
                                    Basis-Arbeitszeit Mitarbeiter:
                                    <strong>${baseHoursLabel} h / Woche</strong>
                                </small>
                            </div>

                            <!-- RIGHT -->
                            <div style="flex:1 1 260px; min-width:260px;">
                                <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                    Montage-Anteil (%)
                                </label>
                                <input id="swal-montage-percent" type="number" min="0" max="100"
                                    class="swal2-input"
                                    placeholder="z.B. 25"
                                    style="width:100%; box-sizing:border-box; margin:4px 0 10px;">

                                <label style="font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:.08em;">
                                    Office-Anteil (%)
                                </label>
                                <input id="swal-office-percent" type="number" min="0" max="100"
                                    class="swal2-input"
                                    placeholder="z.B. 25"
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

                    const percentInput      = document.getElementById('swal-percent');
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
                    const positionId     = $('#swal-position').val();
                    const percent        = parseFloat(document.getElementById('swal-percent').value || '0');
                    const montagePercent = parseFloat(document.getElementById('swal-montage-percent').value || '0');
                    const officePercent  = parseFloat(document.getElementById('swal-office-percent').value || '0');
                    const workingHours   = parseFloat(document.getElementById('swal-working-hours').value || '0');

                    if (!positionId) {
                        Swal.showValidationMessage("Bitte eine Position auswählen");
                        return false;
                    }
                    if (percent <= 0 || percent > 100) {
                        Swal.showValidationMessage("Bitte einen gültigen Prozentwert (1–100) eingeben");
                        return false;
                    }
                    // check vs remaining from backend
                    if (percent > remaining + 0.0001) {
                        Swal.showValidationMessage(
                            "Dieser Mitarbeiter hat nur noch " + remaining.toFixed(2) + "% verfügbar."
                        );
                        return false;
                    }
                    // Montage + Office must equal the row percent
                    if (Math.abs((montagePercent + officePercent) - percent) > 0.0001) {
                        Swal.showValidationMessage(
                            "Montage% + Office% müssen zusammen dem Gesamt-Prozent dieser Zeile entsprechen."
                        );
                        return false;
                    }
                    if (workingHours < 0) {
                        Swal.showValidationMessage("Arbeitsstunden müssen ≥ 0 sein");
                        return false;
                    }

                    return {
                        position_id:     positionId,
                        percent:         percent,
                        montage_percent: montagePercent,
                        office_percent:  officePercent,
                        working_hours:   workingHours
                    };
                }
            }).then(result => {
                if (!result.isConfirmed) return;

                const payload = result.value;

                $.ajax({
                    url: ROUTE_POSITIONS_ASSIGN,
                    type: "POST",
                    data: {
                        employee_id:     employeeId,
                        department_id:   departmentId,
                        position_id:     payload.position_id,
                        percent:         payload.percent,
                        montage_percent: payload.montage_percent,
                        office_percent:  payload.office_percent,
                        working_hours:   payload.working_hours,
                        _token:          CSRF_TOKEN
                    },
                    success: function (resp) {
                        if (resp.success) {
                            toastSuccess(resp.message || "Mitarbeiter-Zuordnung gespeichert.");
                        } else {
                            toastError(resp.message || "Zuordnung konnte nicht gespeichert werden.");
                        }
                    },
                    error: function (xhr) {
                        let msg = "Fehler beim Speichern der Zuordnung.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        console.error(xhr.responseText);
                        toastError(msg);
                    }
                });
            });
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            toastError("Fehler beim Prüfen der Auslastung.");
        }
    });
}




    // ======= Branch + search handlers ========================
         $(function () {
        // select2
        $('.selectables').select2({
            placeholder: "Wählen...",
            allowClear: true,
            width: '100%'
        });

        $('#branchFilter').select2({
            placeholder: "Betrieb wählen",
            allowClear: true,
            width: '100%'
        });

        // initial analytics from PHP
        updateAnalyticsCards(INITIAL_ANALYTICS);

        // branch filter: reload table
        $('#branchFilter').on('change', function () {
            loadDepartments();
        });

        // date range filter: reload on change
            $('#dateFrom, #dateTo').on('change', function () {
                loadDepartments();
            });

        // status + head filters
        $('#statusFilter, #headFilter').on('change', function () {
            loadDepartments();
        });

        // search input: reload on Enter
        $('#searchDepartment').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                loadDepartments();
            }
        });

        // custom sort buttons – no Bootstrap dropdowns
        $(document).on('click', '.dept-sort-trigger', function (e) {
            e.preventDefault();

            const $btn = $(this);
            const sortBy  = $btn.data('sortBy');
            const sortDir = $btn.data('sortDir');

            window.DEPT_SORT_BY  = sortBy;
            window.DEPT_SORT_DIR = sortDir;

            $('.dept-sort-trigger').removeClass('active');
            $btn.addClass('active');

            loadDepartments();
        });

        // initial DnD on first render
        initializeDragAndDrop();
    });

    // ======= CUSTOM ACTIONS MENU (3-dots) ==========================
// ======= CUSTOM ACTIONS MENU (3-dots) ==========================
document.addEventListener('click', function (e) {
    const trigger = e.target.closest('.dept-actions-trigger');
    const wrappers = document.querySelectorAll('.dept-actions-wrapper');

    if (trigger) {
        const wrapper = trigger.closest('.dept-actions-wrapper');
        const isOpen = wrapper.classList.contains('open');

        // close all first
        wrappers.forEach(w => {
            w.classList.remove('open');
            w.classList.remove('drop-up');
        });

        if (!isOpen) {
            // open current
            wrapper.classList.add('open');

            // decide if we should open up or down
            const menu = wrapper.querySelector('.dept-actions-menu');
            if (menu) {
                // force visible for measurement
                menu.style.display = 'block';
                const rect = menu.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.top;
                const needed = rect.height + 16; // some padding

                // if not enough space below, open upwards
                if (spaceBelow < needed) {
                    wrapper.classList.add('drop-up');
                } else {
                    wrapper.classList.remove('drop-up');
                }

                // keep it visible via .open class only
                menu.style.display = '';
            }
        }

        return;
    }

    // click inside menu: let the onclick on items run, then close
    if (e.target.closest('.dept-actions-menu')) {
        wrappers.forEach(w => {
            w.classList.remove('open');
            w.classList.remove('drop-up');
        });
        return;
    }

    // click outside -> close all
    wrappers.forEach(w => {
        w.classList.remove('open');
        w.classList.remove('drop-up');
    });
});


// Reuse existing delegated handler for .view_department_employees
    // by creating a temporary button and triggering the click.
    window.viewDepartmentEmployeesFromMenu = function (deptId) {
        const tempBtn = document.createElement('button');
        tempBtn.type = 'button';
        tempBtn.style.display = 'none';
        tempBtn.className = 'view_department_employees';
        tempBtn.setAttribute('data-department-id', String(deptId));

        document.body.appendChild(tempBtn);
        $(tempBtn).trigger('click'); // uses your existing $(document).on('click', '.view_department_employees', ...) logic
        tempBtn.remove();
    };


</script>
@endsection
