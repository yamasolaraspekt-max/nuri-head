@extends('admin.layouts.app')
@section('title') Anfragevorschläge @stop

@section('style')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
  :root{
    --app-bg:#f6f8fb;
    --card-bg:#fff;
    --muted:#6b7280;
    --shade:#eef2f7;
    --ring:rgba(0,0,0,.06);
    --radius:14px;
  }
  body{ background:var(--app-bg)!important; }

  /* App shell */
  .app-shell{
    display:grid;
    grid-template-columns: 1fr;
    gap: 1.2rem;
  }
  @media (min-width: 1200px){
    .app-shell{
      grid-template-columns: minmax(0,1fr) 340px; /* main + sidebar */
      align-items: start;
    }
  }

  /* Cards */
  .app-card{
    background:var(--card-bg);
    border-radius:var(--radius);
    border:1px solid var(--ring);
    box-shadow:0 8px 28px -18px rgba(0,0,0,.25);
  }
  .app-card .card-head{
    padding: 1rem 1.2rem;
    border-bottom:1px solid var(--ring);
    display:flex; align-items:center; justify-content:space-between; gap:.75rem;
    position: sticky; top: 0; background: var(--card-bg); z-index: 5;
    border-top-left-radius: var(--radius); border-top-right-radius: var(--radius);
  }

  /* Toolbar */
  .toolbar{
    display:flex; gap:.5rem; flex-wrap:wrap; align-items:center;
  }
  .btn-app{
    border-radius:10px; display:inline-flex; align-items:center; gap:.4rem;
    padding:.45rem .7rem;
  }

  /* Inputs */
  .searchbar{
    border-radius:10px!important; border-color:#d1d9e6!important;
  }
  .select2-container .select2-selection{
    height:42px!important; border-radius:10px!important; border-color:#d1d9e6!important;
  }

  /* Tables */
  .table-wrap{ padding: .8rem 1.2rem 1.2rem; }
  .table thead th{
    background:#f2f5fa; color:#475569; font-weight:700; border-top:0; position:sticky; top:0; z-index:2;
  }
  .table td, .table th{ vertical-align:middle; }
  .table-rounded{
    border-radius:12px; overflow:hidden; border:1px solid var(--ring);
  }

  /* Badges */
  .chip{ display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .5rem;
         border-radius:999px; font-size:.75rem; border:1px solid var(--ring); background:#fafbff; }
  .chip-muted{ background:#eef2f7; }
  .chip-int{ background:#eef6ff; }
  .chip-ext{ background:#eefbf3; }

  /* Empty state */
  .empty{
    border:1px dashed #cdd6e4; border-radius:12px; padding:1rem; text-align:center; color:var(--muted);
    background:#fafcff;
  }

  /* Sidebar employees */
  .emp-item{ display:flex; gap:.75rem; padding:.5rem; border-radius:.75rem; border:1px solid var(--ring); background:#fff; }
  .emp-img{ width:38px; height:38px; border-radius:999px; object-fit:cover; }

  /* Modal polish */
  .modal-content{ border-radius:14px; }

  /* Saved filter bar */
  .saved-filters{
    background:#f9fafb;
    border-radius:12px;
    border:1px solid rgba(148,163,184,.25);
    padding:.75rem .9rem;
    margin-bottom:.75rem;
  }
  .saved-filters .form-control,
  .saved-filters .select2-container--default .select2-selection--single,
  .saved-filters .select2-container--default .select2-selection--multiple{
    font-size:.8rem;
  }

  /* Position dropdown with avatars (Select2 templates) */
  .pos-option{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:.5rem;
  }
  .pos-option-label{
    font-size:.85rem;
    color:#111827;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .pos-option-avatars{
    display:flex;
    align-items:center;
    gap:2px;
  }
  .pos-avatar{
    width:22px;
    height:22px;
    border-radius:999px;
    object-fit:cover;
    border:1px solid #e5e7eb;
  }
  .pos-avatar-more{
    font-size:.7rem;
    color:#6b7280;
    padding:0 4px;
  }

  .form-check-inline{
    display:flex;
    align-items:center;
    gap:.25rem;
  }
</style>
@endsection

@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
           <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Anfragevorschläge</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>      
            <div class="content-body"> 
                     <div class="app-shell"> 
                        <!-- =========================
                            MAIN: Builder + Records
                        ========================== -->
                        <section class="app-card">

                            <div class="card-head">
                                <h4 class="m-0 d-flex align-items-center gap-2">
                                    <i data-feather="layers"></i>
                                    <span>Anfragevorschläge</span> 
                                </h4>

                                <div class="toolbar">
                                    <input id="savedRecordsSearch" class="form-control searchbar" style="min-width:260px"
                                        placeholder="🔍 Nach Stufe, Produkt, Abteilung, Leistung, Position suchen…">

                                    <button id="addRow" class="btn btn-outline-primary btn-app">
                                        <i data-feather="plus"></i> Hinzufügen
                                    </button>
                                    <button id="saveRows" class="btn btn-success btn-app">
                                        <i data-feather="save"></i> Speichern
                                    </button>

                                    <div class="form-check form-check-inline ms-1">
                                        <input class="form-check-input" type="checkbox" id="copyInquiryToStages">
                                        <label class="form-check-label small text-muted" for="copyInquiryToStages">
                                            Anfrage auf andere Stufen kopieren
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="table-wrap">
                                <!-- Builder table -->
                                <div class="table-responsive table-rounded mb-3">
                                    <table class="table align-middle mb-0" id="dynamicTable">
                                        <thead>
                                            <tr>
                                                <th style="width:12rem">Stufe</th>
                                                <th style="width:16rem">Produkt</th>
                                                <th style="width:16rem">Leistung</th>
                                                <th style="width:16rem">Abteilung</th>
                                                <th>Positionen</th>
                                                <th class="text-center" style="width:6rem">Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody id="rowContainer">
                                            <tr>
                                                <td colspan="6">
                                                    <div class="empty">
                                                        Noch keine Eingaben. Klicken Sie auf <strong>Hinzufügen</strong>, um eine Zeile zu erstellen.
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Saved records header + filters -->
                                <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
                                    <h5 class="m-0 text-secondary d-flex align-items-center gap-2">
                                        <i data-feather="clock"></i> Gespeicherte Vorschläge
                                    </h5>
                                    <div class="d-flex align-items-center gap-2">
                                        <button id="btnBulkDuplicate" class="btn btn-sm btn-outline-primary" disabled>
                                            <i data-feather="copy"></i> Duplizieren
                                        </button>
                                        <button id="btnBulkDelete" class="btn btn-sm btn-outline-danger" disabled>
                                            <i data-feather="trash-2"></i> Löschen
                                        </button>
                                    </div>
                                </div>

                                <div class="saved-filters">
                                    <div class="row g-2">
                                        <div class="col-md-3 col-sm-6">
                                            <label class="small text-muted mb-1">Produkt</label>
                                            <select id="savedFilterProduct" class="form-control">
                                                <option value="">Alle Produkte</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="small text-muted mb-1">Leistung</label>
                                            <select id="savedFilterService" class="form-control">
                                                <option value="">Alle Leistungen</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="small text-muted mb-1">Abteilung</label>
                                            <select id="savedFilterDepartment" class="form-control">
                                                <option value="">Alle Abteilungen</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="small text-muted mb-1">Sortierung</label>
                                            <div class="d-flex gap-1">
                                                <select id="savedSortBy" class="form-control">
                                                    <option value="stage">Stufe</option>
                                                    <option value="product">Produkt</option>
                                                    <option value="service">Leistung</option>
                                                    <option value="department">Abteilung</option>
                                                </select>
                                                <button id="savedSortDirToggle" type="button" class="btn btn-light btn-sm" title="Sortierreihenfolge">
                                                    <span data-dir="asc">↑</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-2">
                                        <div class="col-md-6">
                                            <label class="small text-muted mb-1">Position Intern (Innendienst)</label>
                                            <select id="savedFilterPosInternal" class="form-control" multiple></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small text-muted mb-1">Position Extern (Außendienst)</label>
                                            <select id="savedFilterPosExternal" class="form-control" multiple></select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Saved records table -->
                                <div class="table-responsive table-rounded" id="savedRecordsTable">
                                    <div class="empty">Keine Datensätze gefunden.</div>
                                </div>
                            </div>

                        </section>

                        <!-- =========================
                            SIDEBAR: Mitarbeiter
                        ========================== -->
                        <aside class="app-card p-3 p-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="m-0 d-flex align-items-center gap-2">
                                    <i data-feather="users"></i> Mitarbeiter
                                </h5>
                                <span id="employeeBadge" class="chip chip-muted">0 gefunden</span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="chip chip-int"><i data-feather="shield"></i> Intern</span>
                                    <span id="employeeCountInternal" class="text-muted small">0</span>
                                </div>
                                <div id="employeeListInternal" class="d-grid gap-2">
                                    <div class="empty">Keine internen Mitarbeiter ausgewählt.</div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="chip chip-ext"><i data-feather="users"></i> Extern</span>
                                    <span id="employeeCountExternal" class="text-muted small">0</span>
                                </div>
                                <div id="employeeListExternal" class="d-grid gap-2">
                                    <div class="empty">Keine externen Mitarbeiter ausgewählt.</div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>

            <!-- =========================
                Edit Modal
            ========================== -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <form id="editForm">
                            <div class="modal-header">
                                <h5 class="modal-title d-flex align-items-center gap-2">
                                    <i data-feather="edit-3"></i> Vorschlag bearbeiten
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                            </div>

                            <div class="modal-body">
                                <input type="hidden" id="editId">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Stufe</label>
                                        <select id="editStage" class="form-control" style="width:100%">
                                            <option value="">Stufe wählen</option>
                                            <option value="inquiry">Anfrage</option>
                                            <option value="lead">Lead</option>
                                            <option value="offer">Angebot</option>
                                            <option value="deals">Abschluss</option>
                                            <option value="project">Projekt</option>
                                            <option value="ticket">Ticket</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Produkt</label>
                                        <select id="editProduct" class="form-control" style="width:100%"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Leistung</label>
                                        <select id="editService" class="form-control" style="width:100%"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Abteilung</label>
                                        <select id="editDepartment" class="form-control" style="width:100%"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"><i data-feather="shield"></i> Intern</label>
                                        <select id="editPositionsInternal" class="form-control" multiple style="width:100%"></select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"><i data-feather="users"></i> Extern</label>
                                        <select id="editPositionsExternal" class="form-control" multiple style="width:100%"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    <i data-feather="check"></i> Aktualisieren
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>
    <!-- END: Content-->
@stop
 @section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (window.feather) feather.replace();
  });
</script>

<script>
   $(document).ready(function(){
        $('#phase_id').select2();

        @if(Session::has('update_msg'))
            toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif
        @if(Session::has('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
   });
</script> 

<script>
let articleGroups = [];
let departments = [];
let allSavedRecords = [];

// Map: departmentId -> { positionName => [ employees ] }
let positionEmployeeMap = {};

const STAGES_ALL = ['inquiry','lead','offer','deals','project','ticket'];
const STAGES_COPY_TARGETS = ['lead','offer','deals','project','ticket'];

$(document).ready(function() {
    fetchData();
    fetchSavedRecords();

    $('#addRow').on('click', function() {
        addRow();
    });

    $('#saveRows').on('click', function() {
        saveRows();
    });

    // Copy Anfrage master -> other stages toggle
    $('#copyInquiryToStages').on('change', handleCopyInquiryToggle);

    // Search saved records (free text)
    $(document).on('input', '#savedRecordsSearch', function() {
        applySavedFiltersAndSort();
    });

    // Filters + sort controls
    $('#savedFilterProduct, #savedFilterService, #savedFilterDepartment').on('change', function(){
        applySavedFiltersAndSort();
    });

    $('#savedFilterPosInternal, #savedFilterPosExternal').on('change', function(){
        applySavedFiltersAndSort();
    });

    $('#savedSortBy').on('change', function(){
        applySavedFiltersAndSort();
    });

    $('#savedSortDirToggle').on('click', function(){
        const span = $(this).find('span');
        const current = span.data('dir') || 'asc';
        const next = current === 'asc' ? 'desc' : 'asc';
        span.data('dir', next);
        span.text(next === 'asc' ? '↑' : '↓');
        applySavedFiltersAndSort();
    });

    $('#btnBulkDuplicate').on('click', handleBulkDuplicate);
    $('#btnBulkDelete').on('click', handleBulkDelete);

    // init filter selects with Select2
    $('#savedFilterProduct, #savedFilterService, #savedFilterDepartment').select2({
        width: '100%',
        placeholder: 'Alle',
        allowClear: true
    });
    $('#savedFilterPosInternal, #savedFilterPosExternal').select2({
        width: '100%',
        placeholder: 'Alle Positionen',
        allowClear: true
    });
});

// ---------- COPY ANFRAGE MASTER -> OTHER STAGES ----------

// Checkbox change handler
function handleCopyInquiryToggle() {
    const checked = $('#copyInquiryToStages').is(':checked');

    if (!checked) {
        // remove cloned rows again
        removeInquiryClones();
        return;
    }

    // first data row = master
    const $rows = $('#rowContainer tr');
    if (!$rows.length) {
        $('#copyInquiryToStages').prop('checked', false);
        Swal.fire({
            icon: 'info',
            title: 'Keine Anfrage-Zeile',
            text: 'Legen Sie zuerst eine Anfrage-Zeile an und füllen Sie alle Felder aus.',
            timer: 2200,
            showConfirmButton: false
        });
        return;
    }

    const $master = $rows.first();
    if (!isInquiryRowComplete($master)) {
        $('#copyInquiryToStages').prop('checked', false);
        Swal.fire({
            icon: 'info',
            title: 'Anfrage unvollständig',
            text: 'Die erste Zeile muss Stufe "Anfrage" haben und Produkt, Abteilung und mindestens eine Position (Mitarbeiter) enthalten.',
            timer: 2600,
            showConfirmButton: false
        });
        return;
    }

    // Confirm before cloning
    Swal.fire({
        icon: 'question',
        title: 'Auf andere Stufen kopieren?',
        html: 'Die Anfrage-Zeile wird als Vorlage für <strong>Lead, Angebot, Abschluss, Projekt und Ticket</strong> dupliziert.',
        showCancelButton: true,
        confirmButtonText: 'Ja, kopieren',
        cancelButtonText: 'Abbrechen'
    }).then(res => {
        if (!res.isConfirmed) {
            $('#copyInquiryToStages').prop('checked', false);
            return;
        }
        removeInquiryClones(); // zur Sicherheit alte Clones entfernen
        cloneInquiryRowToOtherStages($master);
    });
}

// Prüft, ob erste Zeile wirklich eine komplette Anfrage ist
function isInquiryRowComplete($row) {
    const stage      = $row.find('.stage-select').val();
    const produkt    = $row.find('.produkt-select').val();
    const department = $row.find('.department-select').val();
    const internal   = $row.find('.position-internal-select').val() || [];
    const external   = $row.find('.position-external-select').val() || [];

    if (stage !== 'inquiry') return false;
    if (!produkt || !department) return false;
    if (!internal.length && !external.length) return false;

    return true;
}

// Entfernt alle automatisch erzeugten Stufenzeilen
function removeInquiryClones() {
    $('#rowContainer tr[data-auto-inquiry-clone="1"]').remove();
    // Placeholder wieder einfügen, falls gar nichts mehr da ist
    if (!$('#rowContainer tr').length) {
        $('#rowContainer').append(`
            <tr>
                <td colspan="6">
                    <div class="empty">
                        Noch keine Eingaben. Klicken Sie auf <strong>Hinzufügen</strong>, um eine Zeile zu erstellen.
                    </div>
                </td>
            </tr>`);
    }
}

// Klont die Anfrage-Zeile wirklich 1:1 für alle Ziel-Stufen
function cloneInquiryRowToOtherStages($sourceRow) {
    const produktVal    = $sourceRow.find('.produkt-select').val();
    const serviceVal    = $sourceRow.find('.service-select').val();
    const departmentVal = $sourceRow.find('.department-select').val();
    const internalVals  = $sourceRow.find('.position-internal-select').val() || [];
    const externalVals  = $sourceRow.find('.position-external-select').val() || [];

    const $masterService   = $sourceRow.find('.service-select');
    const $masterPosInt    = $sourceRow.find('.position-internal-select');
    const $masterPosExt    = $sourceRow.find('.position-external-select');

    STAGES_COPY_TARGETS.forEach(stageVal => {
        const $row = addRow();
        $row.attr('data-auto-inquiry-clone', '1');

        const $stageSelect   = $row.find('.stage-select');
        const $produktSelect = $row.find('.produkt-select');
        const $serviceSelect = $row.find('.service-select');
        const $deptSelect    = $row.find('.department-select');
        const $posInt        = $row.find('.position-internal-select');
        const $posExt        = $row.find('.position-external-select');

        // Stufe setzen
        $stageSelect.val(stageVal).trigger('change.select2');

        // Produkt / Abteilung setzen (Optionen sind durch addRow schon geladen)
        if (produktVal) {
            $produktSelect.val(produktVal).trigger('change.select2');
        }
        if (departmentVal) {
            $deptSelect.val(departmentVal).trigger('change.select2');
        }

        // Service-Options + Auswahl 1:1 vom Master kopieren
        $serviceSelect.html($masterService.html());
        if (serviceVal) {
            $serviceSelect.val(serviceVal).trigger('change.select2');
        }

        // Positions-Options + Auswahl 1:1 kopieren
        $posInt.html($masterPosInt.html());
        $posExt.html($masterPosExt.html());

        if (internalVals.length) {
            $posInt.val(internalVals).trigger('change.select2');
        }
        if (externalVals.length) {
            $posExt.val(externalVals).trigger('change.select2');
        }
    });
}

// --- Helpers for employees sidebar ---
function renderEmployeesList(employees, containerSelector) {
  let html = '';
  if (!employees || !employees.length) {
    html = '<div class="empty">Keine Mitarbeiter gefunden.</div>';
  } else {
    html = employees.map(emp => {
      const badges = (emp.positions || [])
        .map(p => `<span class="badge bg-primary me-1">${p}</span>`).join('');
      return `
        <div class="emp-item">
          <img class="emp-img" src="/images/employee/${emp.image}" alt="${emp.name}">
          <div>
            <div><strong>${emp.name} ${emp.lastname}</strong></div>
            <div>${badges}</div>
          </div>
        </div>`;
    }).join('');
  }
  $(containerSelector).html(html);
}

// Fetch article groups and departments
function fetchData() {
    $.get('/product-position/article-groups', function(data) {
        articleGroups = data;
    });

    $.get('/product-position/departments', function(data) {
        departments = data;
    });
}

// ---------- POSITION SELECT2 TEMPLATES WITH AVATARS ----------
function getPositionEmployeesForOption(optionElement) {
    if (!optionElement) return [];
    const $opt     = $(optionElement);
    const deptId   = $opt.data('department-id');
    const posName  = $.trim($opt.text());
    if (!deptId || !posName) return [];
    const mapDept  = positionEmployeeMap[deptId] || {};
    return mapDept[posName] || [];
}

function positionTemplateResult(state) {
    if (!state.id) {
        return state.text;
    }
    const employees = getPositionEmployeesForOption(state.element);
    const $container = $('<span class="pos-option"></span>');
    const $label = $('<span class="pos-option-label"></span>').text(state.text);
    const $avatarsWrap = $('<span class="pos-option-avatars"></span>');

    if (employees && employees.length) {
        employees.slice(0,3).forEach(emp => {
            const img = $('<img class="pos-avatar" />')
                .attr('src', '/images/employee/' + emp.image)
                .attr('alt', emp.name);
            $avatarsWrap.append(img);
        });
        if (employees.length > 3) {
            const more = $('<span class="pos-avatar-more"></span>')
                .text('+' + (employees.length - 3));
            $avatarsWrap.append(more);
        }
    }

    $container.append($label).append($avatarsWrap);
    return $container;
}

function positionTemplateSelection(state) {
    if (!state.id) {
        return state.text;
    }
    const employees = getPositionEmployeesForOption(state.element);
    const $container = $('<span class="pos-option"></span>');
    const $label = $('<span class="pos-option-label"></span>').text(state.text);
    const $avatarsWrap = $('<span class="pos-option-avatars"></span>');

    if (employees && employees.length) {
        employees.slice(0,2).forEach(emp => {
            const img = $('<img class="pos-avatar" />')
                .attr('src', '/images/employee/' + emp.image)
                .attr('alt', emp.name);
            $avatarsWrap.append(img);
        });
        if (employees.length > 2) {
            const more = $('<span class="pos-avatar-more"></span>')
                .text('+' + (employees.length - 2));
            $avatarsWrap.append(more);
        }
    }

    $container.append($label).append($avatarsWrap);
    return $container;
}

function initPositionSelect2($select) {
    $select.select2({
        width: '100%',
        templateResult: positionTemplateResult,
        templateSelection: positionTemplateSelection,
        escapeMarkup: function(markup){ return markup; }
    });
}

// --- Build map: departmentId -> { positionName => employees[] } ---
function buildPositionEmployeeMapForDepartment(departmentId, positionsArray) {
    const ids = positionsArray.map(p => p.id);
    if (!ids.length) return;

    $.post('/product-position/employees', {
        position_ids: { internal: ids, external: [] },
        _token: '{{ csrf_token() }}'
    }, function(employees){
        const mapByName = {};
        (employees || []).forEach(emp => {
            (emp.positions || []).forEach(posName => {
                posName = $.trim(posName);
                if (!mapByName[posName]) mapByName[posName] = [];
                mapByName[posName].push(emp);
            });
        });
        positionEmployeeMap[departmentId] = mapByName;
    });
}

// Add new row (returns jQuery row)
function addRow() {
    let row = `
        <tr>
            <td>
                <select class="form-control stage-select">
                    <option value="">Stufe wählen</option>
                    <option value="inquiry">Anfrage</option>
                    <option value="lead">Lead</option>
                    <option value="offer">Angebot</option>
                    <option value="deals">Abschluss</option>
                    <option value="project">Projekt</option>
                    <option value="ticket">Ticket</option>
                </select>
            </td>

            <td>
                <select class="form-control produkt-select">
                    <option value="">Produkt wählen</option>
                </select>
            </td>

            <td>
                <select class="form-control service-select">
                    <option value="">Leistung wählen</option>
                </select>
            </td>

            <td>
                <select class="form-control department-select">
                    <option value="">Abteilung wählen</option>
                </select>
            </td>

            <td>
                <div class="space-y-2">
                    <div>
                        <label class="form-label mb-1 small text-muted">
                            <i data-feather="shield"></i> Intern (Innendienst)
                        </label>
                        <select class="form-control position-internal-select" multiple="multiple" style="width:100%"></select>
                    </div>

                    <div>
                        <label class="form-label mb-1 small text-muted">
                            <i data-feather="users"></i> Extern (Außendienst)
                        </label>
                        <select class="form-control position-external-select" multiple="multiple" style="width:100%"></select>
                    </div>
                </div>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger removeRow" title="Zeile entfernen">
                    <i data-feather="trash-2"></i>
                </button>
            </td>
        </tr>`;

    $('#rowContainer').find('.empty').closest('tr').remove();
    $('#rowContainer').append(row);

    let $newRow = $('#rowContainer tr:last');

    // Populate Produkt
    articleGroups.forEach(group => {
        $newRow.find('.produkt-select').append(`<option value="${group.id}">${group.article_group}</option>`);
    });

    // Populate Departments
    departments.forEach(dept => {
        $newRow.find('.department-select').append(`<option value="${dept.id}">${dept.department_name}</option>`);
    });

    // Initialize base Select2
    $newRow.find('.stage-select, .produkt-select, .service-select, .department-select')
        .select2({ width: '100%' });

    // Positions select2
    initPositionSelect2($newRow.find('.position-internal-select'));
    initPositionSelect2($newRow.find('.position-external-select'));

    if (window.feather) feather.replace();

    // Remove Row
    $newRow.find('.removeRow').on('click', function() {
        const $tr = $(this).closest('tr');
        const isClone = $tr.attr('data-auto-inquiry-clone') === '1';
        $tr.remove();

        if (!$('#rowContainer tr').length) {
            $('#rowContainer').append(`
                <tr>
                    <td colspan="6">
                        <div class="empty">
                            Noch keine Eingaben. Klicken Sie auf <strong>Hinzufügen</strong>, um eine Zeile zu erstellen.
                        </div>
                    </td>
                </tr>`);
        }

        // Wenn alle Clones weg sind, Checkbox zurücksetzen
        if (isClone && !$('#rowContainer tr[data-auto-inquiry-clone="1"]').length) {
            $('#copyInquiryToStages').prop('checked', false);
        }
    });

    // Load Services when product changes
    $newRow.find('.produkt-select').on('change', function() {
        let productId = $(this).val();
        let serviceSelect = $(this).closest('tr').find('.service-select');
        serviceSelect.empty().append(`<option value="">Leistung wählen</option>`);

        if (productId) {
            $.get(`/product-position/services/${productId}`, function(data) {
                data.forEach(service => {
                    serviceSelect.append(`<option value="${service.id}">${service.label}</option>`);
                });
                serviceSelect.trigger('change.select2');
            });
        }
    });

    // Fetch Positions when department selected
    $newRow.find('.department-select').on('change', function() {
        const departmentId = $(this).val();
        const $row = $(this).closest('tr');
        const $posInt = $row.find('.position-internal-select');
        const $posExt = $row.find('.position-external-select');

        $posInt.empty();
        $posExt.empty();

        if (!departmentId) {
            $posInt.trigger('change');
            $posExt.trigger('change');
            return;
        }

        $.get(`/product-position/positions/${departmentId}`, function(data) {
            data.forEach(pos => {
                const opt = `<option value="${pos.id}" data-department-id="${departmentId}">${pos.position}</option>`;
                $posInt.append(opt);
                $posExt.append(opt);
            });

            initPositionSelect2($posInt);
            initPositionSelect2($posExt);

            buildPositionEmployeeMapForDepartment(departmentId, data);
        });
    });

    return $newRow;
}

// Collect rows and handle duplicates / override / ignore
function saveRows() {
    const rows = collectRowsFromBuilder();

    if (!rows.length) {
        Swal.fire({
            icon: 'info',
            title: 'Unvollständige Eingabe',
            text: 'Bitte füllen Sie mindestens eine vollständige Zeile aus.',
            timer: 1800,
            showConfirmButton: false
        });
        return;
    }

    const conflicts = findConflictingRows(rows);

    if (!conflicts.length) {
        doSaveRows(rows);
        return;
    }

    const htmlList = conflicts.map(c => {
        const rec = c.existing[0];
        const svc = rec.service ? translateService(rec.service.phase_section) : '-';
        const prodName = rec.article_group ? rec.article_group.article_group : '-';
        const deptName = rec.department ? rec.department.department_name : '-';
        return `<li><strong>${stageLabelDe(c.row.stage)}</strong> / ${prodName} / ${svc} / ${deptName}</li>`;
    }).join('');

    Swal.fire({
        icon: 'warning',
        title: 'Doppelte Vorschläge gefunden',
        html: `<p>Für einige Kombinationen existieren bereits Vorschläge:</p>
               <ul style="text-align:left;">${htmlList}</ul>
               <p>Wie möchten Sie fortfahren?</p>`,
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Überschreiben',
        denyButtonText: 'Ignorieren',
        cancelButtonText: 'Abbrechen'
    }).then(result => {
        if (result.isConfirmed) {
            const idsToDelete = [];
            conflicts.forEach(c => c.existing.forEach(e => idsToDelete.push(e.id)));
            const uniqueIds = [...new Set(idsToDelete)];
            $.ajax({
                url: '/product-position/bulk-delete',
                method: 'POST',
                data: { ids: uniqueIds },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function() {
                    doSaveRows(rows);
                }
            });
        } else if (result.isDenied) {
            const conflictIndexes = new Set(conflicts.map(c => c.rowIndex));
            const filteredRows = rows.filter((row, idx) => !conflictIndexes.has(idx));
            if (!filteredRows.length) {
                Swal.fire({
                    icon: 'info',
                    title: 'Keine neuen Vorschläge',
                    text: 'Alle Zeilen sind bereits vorhanden.',
                    timer: 1800,
                    showConfirmButton: false
                });
                return;
            }
            doSaveRows(filteredRows);
        }
    });
}

function collectRowsFromBuilder() {
    const rows = [];
    $('#rowContainer tr').each(function() {
        const stage      = $(this).find('.stage-select').val();
        const produkt    = $(this).find('.produkt-select').val();
        const service    = $(this).find('.service-select').val();
        const department = $(this).find('.department-select').val(); 
        const internal   = $(this).find('.position-internal-select').val() || [];
        const external   = $(this).find('.position-external-select').val() || [];

        if (stage && produkt && department && (internal.length || external.length)) {
            rows.push({
                stage,
                produkt,
                service,
                department,
                positions: { internal, external }
            });
        }
    });
    return rows;
}

function sameArray(a, b) {
    a = a || [];
    b = b || [];
    if (a.length !== b.length) return false;
    const aSorted = [...a].sort();
    const bSorted = [...b].sort();
    for (let i=0;i<aSorted.length;i++) {
        if (String(aSorted[i]) !== String(bSorted[i])) return false;
    }
    return true;
}

function sameRecordKey(existing, row) {
    if ((existing.stage || '') !== row.stage) return false;
    if (String(existing.article_group_id || '') !== String(row.produkt || '')) return false;
    if (String(existing.service_id || '') !== String(row.service || '')) return false;
    if (String(existing.department_id || '') !== String(row.department || '')) return false;

    const posExisting = existing.position_ids || {};
    const exInt = posExisting.internal || [];
    const exExt = posExisting.external || [];
    const rInt  = row.positions.internal || [];
    const rExt  = row.positions.external || [];

    return sameArray(exInt, rInt) && sameArray(exExt, rExt);
}

function findConflictingRows(rows) {
    const conflicts = [];
    (rows || []).forEach((row, idx) => {
        const matches = (allSavedRecords || []).filter(rec => sameRecordKey(rec, row));
        if (matches.length) {
            conflicts.push({ rowIndex: idx, row, existing: matches });
        }
    });
    return conflicts;
}

function doSaveRows(rows) {
  if (!rows.length) return;

  $.ajax({
    url: '/product-position/save',
    method: 'POST',
    data: { rows },
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    success: function(resp) {
        Swal.fire({ icon:'success', title:'Aktualisiert', text: resp.message, timer: 1500, showConfirmButton:false });
        $('#rowContainer').empty().append(`
            <tr>
                <td colspan="6">
                    <div class="empty">
                        Noch keine Eingaben. Klicken Sie auf <strong>Hinzufügen</strong>, um eine Zeile zu erstellen.
                    </div>
                </td>
            </tr>`);
        $('#copyInquiryToStages').prop('checked', false);
        fetchSavedRecords();
    }
  });
}

// Fetch and show saved records
function fetchSavedRecords() {
    $.get('/product-position/records', function(records) {
        allSavedRecords = records;
        buildSavedFilterOptions(records);
        applySavedFiltersAndSort();
    });
}

function translateService(value) {
    const map = {
        'complete': 'Komplett',
        'montage': 'Montage',
        'product': 'Produkt',
        'plan': 'Planung',
        'maintenance': 'Wartung',
        'repair': 'Reparatur',
        'others': 'Others'
    };
    return map[value?.toLowerCase()] || value;
}

function stageLabelDe(v) {
  const map = {
    inquiry: 'Anfrage',
    lead: 'Lead',
    offer: 'Angebot',
    deals: 'Abschluss',
    project: 'Projekt',
    ticket: 'Ticket'
  };
  return map[String(v || '').toLowerCase()] || v || '-';
}

// --------- Build filter options based on saved records ----------
function buildSavedFilterOptions(records) {
    const products = new Set();
    const services = new Set();
    const departmentsSet = new Set();
    const posIntSet = new Set();
    const posExtSet = new Set();

    records.forEach(r => {
        if (r.article_group && r.article_group.article_group) {
            products.add(r.article_group.article_group);
        }
        if (r.service && r.service.phase_section) {
            services.add(translateService(r.service.phase_section));
        }
        if (r.department && r.department.department_name) {
            departmentsSet.add(r.department.department_name);
        }
        (r.position_internal || []).forEach(n => posIntSet.add(n));
        (r.position_external || []).forEach(n => posExtSet.add(n));
    });

    const fillSelect = (selector, set, placeholder) => {
        const $sel = $(selector);
        const currentVal = $sel.val();
        $sel.empty();
        if (!Array.isArray(currentVal)) {
            $sel.append(`<option value="">${placeholder}</option>`);
        }
        Array.from(set).sort().forEach(v => {
            $sel.append(`<option value="${v}">${v}</option>`);
        });
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.trigger('change.select2');
        }
    };

    fillSelect('#savedFilterProduct', products, 'Alle Produkte');
    fillSelect('#savedFilterService', services, 'Alle Leistungen');
    fillSelect('#savedFilterDepartment', departmentsSet, 'Alle Abteilungen');

    const fillMulti = (selector, set) => {
        const $sel = $(selector);
        const selected = $sel.val() || [];
        $sel.empty();
        Array.from(set).sort().forEach(v => {
            const selectedAttr = selected.includes(v) ? 'selected' : '';
            $sel.append(`<option value="${v}" ${selectedAttr}>${v}</option>`);
        });
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.trigger('change.select2');
        }
    };

    fillMulti('#savedFilterPosInternal', posIntSet);
    fillMulti('#savedFilterPosExternal', posExtSet);
}

// --------- Apply text search + filters + sort ----------
function applySavedFiltersAndSort() {
    let filtered = (allSavedRecords || []).slice();

    const search = ($('#savedRecordsSearch').val() || '').toLowerCase();
    const fProduct    = $('#savedFilterProduct').val() || '';
    const fService    = $('#savedFilterService').val() || '';
    const fDepartment = $('#savedFilterDepartment').val() || '';
    const posIntVals  = $('#savedFilterPosInternal').val() || [];
    const posExtVals  = $('#savedFilterPosExternal').val() || [];
    const sortBy      = $('#savedSortBy').val() || 'stage';
    const sortDir     = $('#savedSortDirToggle span').data('dir') || 'asc';

    filtered = filtered.filter(record => {
        const stage      = (record.stage || '').toLowerCase();
        const produkt    = (record.article_group?.article_group || '');
        const produktLc  = produkt.toLowerCase();
        const department = (record.department?.department_name || '');
        const departmentLc = department.toLowerCase();
        const serviceRaw = record.service ? translateService(record.service.phase_section) : '';
        const serviceLc  = serviceRaw.toLowerCase();

        const posIntNames = (record.position_internal || []);
        const posExtNames = (record.position_external || []);

        const positionsCombined = `${posIntNames.join(' ')} ${posExtNames.join(' ')}`.toLowerCase();
        const haystack = `${stage} ${produktLc} ${departmentLc} ${serviceLc} ${positionsCombined}`;
        if (search && !haystack.includes(search)) return false;

        if (fProduct && produkt !== fProduct) return false;
        if (fService && serviceRaw !== fService) return false;
        if (fDepartment && department !== fDepartment) return false;

        if (posIntVals.length) {
            const matchInt = posIntVals.every(v => posIntNames.includes(v));
            if (!matchInt) return false;
        }
        if (posExtVals.length) {
            const matchExt = posExtVals.every(v => posExtNames.includes(v));
            if (!matchExt) return false;
        }
        return true;
    });

    filtered.sort((a,b) => {
        const getKey = (rec) => {
            if (sortBy === 'stage') return (rec.stage || '').toString();
            if (sortBy === 'product') return (rec.article_group?.article_group || '').toString();
            if (sortBy === 'service') return rec.service ? translateService(rec.service.phase_section).toString() : '';
            if (sortBy === 'department') return (rec.department?.department_name || '').toString();
            return '';
        };
        const av = (getKey(a) || '').toLowerCase();
        const bv = (getKey(b) || '').toLowerCase();
        if (av < bv) return sortDir === 'asc' ? -1 : 1;
        if (av > bv) return sortDir === 'asc' ? 1 : -1;
        return 0;
    });

    renderSavedRecords(filtered);
}

function renderSavedRecords(records) {
    if (!records.length) {
        $('#savedRecordsTable').html('<div class="empty">Keine Datensätze gefunden.</div>');
        return;
    }

    let html = `<table class="table table-bordered mb-0">
        <thead>
            <tr>
                <th style="width:30px;">
                    <input type="checkbox" id="savedSelectAll">
                </th>
                <th>Stufe</th>
                <th>Produkt</th>
                <th>Leistung</th>
                <th>Abteilung</th>
                <th>Intern</th>
                <th>Extern</th>
                <th class="text-nowrap">Aktion</th>
            </tr>
        </thead>
        <tbody>`;

    records.forEach(r => {
        const svc = r.service ? translateService(r.service.phase_section) : '-';

        const chipsInt = (r.position_internal || []).map(n =>
            `<span class="badge bg-secondary me-1">${n}</span>`).join('') || '-';
        const chipsExt = (r.position_external || []).map(n =>
            `<span class="badge bg-info me-1">${n}</span>`).join('') || '-';

        html += `<tr>
            <td>
                <input type="checkbox" class="savedRowCheckbox" data-id="${r.id}">
            </td>
            <td>${stageLabelDe(r.stage)}</td>
            <td>${r.article_group ? r.article_group.article_group : '-'}</td>
            <td>${svc}</td>
            <td>${r.department ? r.department.department_name : '-'}</td>
            <td>${chipsInt}</td>
            <td>${chipsExt}</td>
            <td class="text-nowrap">
                <button class="btn btn-sm btn-outline-info viewEmployees"
                        title="Mitarbeiter ansehen"
                        data-positions='${JSON.stringify(r.position_ids)}'>
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning editRecord"
                        title="Bearbeiten"
                        data-id="${r.id}"
                        data-stage="${r.stage}"
                        data-produkt="${r.article_group_id}"
                        data-service-id="${r.service_id}"
                        data-department="${r.department_id}"
                        data-positions='${JSON.stringify(r.position_ids)}'>
                    <i data-feather="edit-3"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger deleteRecord"
                        title="Löschen"
                        data-id="${r.id}">
                    <i data-feather="trash-2"></i>
                </button>
            </td>
        </tr>`;
    });

    html += `</tbody></table>`;
    $('#savedRecordsTable').html(html);
    if (window.feather) feather.replace();
    bindRecordButtons();
    initSavedBulkSelectionHandlers();
}

// Bulk selection handlers
function initSavedBulkSelectionHandlers() {
    $('#savedSelectAll').off('change').on('change', function(){
        const checked = $(this).is(':checked');
        $('.savedRowCheckbox').prop('checked', checked);
        updateBulkActionState();
    });

    $('.savedRowCheckbox').off('change').on('change', function(){
        const all = $('.savedRowCheckbox');
        const checkedCount = all.filter(':checked').length;
        $('#savedSelectAll').prop('checked', checkedCount === all.length && all.length > 0);
        updateBulkActionState();
    });

    updateBulkActionState();
}

function updateBulkActionState() {
    const anyChecked = $('.savedRowCheckbox:checked').length > 0;
    $('#btnBulkDuplicate, #btnBulkDelete').prop('disabled', !anyChecked);
}

function getSelectedSavedIds() {
    return $('.savedRowCheckbox:checked').map(function(){ return $(this).data('id'); }).get();
}

function handleBulkDuplicate() {
    const ids = getSelectedSavedIds();
    if (!ids.length) return;

    Swal.fire({
        icon: 'question',
        title: 'Duplizieren',
        text: 'Ausgewählte Vorschläge duplizieren?',
        showCancelButton: true,
        confirmButtonText: 'Ja, duplizieren',
        cancelButtonText: 'Abbrechen'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.ajax({
            url: '/product-position/bulk-duplicate',
            method: 'POST',
            data: { ids },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Dupliziert',
                    text: resp.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchSavedRecords();
            }
        });
    });
}

function handleBulkDelete() {
    const ids = getSelectedSavedIds();
    if (!ids.length) return;

    Swal.fire({
        icon: 'warning',
        title: 'Mehrfach löschen',
        text: 'Ausgewählte Vorschläge wirklich löschen?',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then(res => {
        if (!res.isConfirmed) return;

        $.ajax({
            url: '/product-position/bulk-delete',
            method: 'POST',
            data: { ids },
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Gelöscht',
                    text: resp.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                fetchSavedRecords();
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: 'Die Datensätze konnten nicht gelöscht werden.',
                    timer: 1800,
                    showConfirmButton: false
                });
            }
        });
    });
}

// Bind View, Edit, Delete Buttons
function bindRecordButtons() {
    $('.viewEmployees').off('click').on('click', function() {
        const pos = $(this).data('positions') || { internal:[], external:[] };
        const idsInternal = pos.internal || [];
        const idsExternal = pos.external || [];

        $('#employeeBadge').text('Lädt…');
        $('#employeeCountInternal').text('0');
        $('#employeeCountExternal').text('0');
        $('#employeeListInternal').html('<div class="empty">Lädt…</div>');
        $('#employeeListExternal').html('<div class="empty">Lädt…</div>');

        const reqInternal = (ids) => $.post('/product-position/employees',
            { position_ids: { internal: ids, external: [] }, _token: '{{ csrf_token() }}' });

        const reqExternal = (ids) => $.post('/product-position/employees',
            { position_ids: { internal: [], external: ids }, _token: '{{ csrf_token() }}' });

        $.when(reqInternal(idsInternal), reqExternal(idsExternal)).done(function(internalResp, externalResp){
            const employeesInternal = internalResp[0] || [];
            const employeesExternal = externalResp[0] || [];

            renderEmployeesList(employeesInternal, '#employeeListInternal');
            renderEmployeesList(employeesExternal, '#employeeListExternal');

            $('#employeeCountInternal').text(employeesInternal.length);
            $('#employeeCountExternal').text(employeesExternal.length);
            $('#employeeBadge').text((employeesInternal.length + employeesExternal.length) + ' gesamt');
        }).fail(function(){
            renderEmployeesList([], '#employeeListInternal');
            renderEmployeesList([], '#employeeListExternal');
            $('#employeeBadge').text('0 gesamt');
        });
    });

    $('.editRecord').off('click').on('click', function() {
        const recordId  = $(this).data('id');
        const stage     = $(this).data('stage');
        const produkt   = $(this).data('produkt');
        const serviceId = $(this).data('service-id');
        const department= $(this).data('department');
        const posIds    = $(this).data('positions') || {}; // { internal:[], external:[] }

        $('#editId').val(recordId);
        $('#editStage').val(stage);

        $('#editProduct').empty();
        articleGroups.forEach(g => $('#editProduct').append(`<option value="${g.id}">${g.article_group}</option>`));
        $('#editProduct').val(produkt);

        $('#editDepartment').empty();
        departments.forEach(d => $('#editDepartment').append(`<option value="${d.id}">${d.department_name}</option>`));
        $('#editDepartment').val(department);

        $('#editPositionsInternal').empty();
        $('#editPositionsExternal').empty();

        if (produkt) {
            $.get(`/product-position/services/${produkt}`, function(data) {
                $('#editService').empty().append(`<option value="">Leistung wählen</option>`);
                data.forEach(s => $('#editService').append(`<option value="${s.id}">${s.label}</option>`));
                $('#editService').val(serviceId);
                $('#editService').trigger('change.select2');
            });
        } else {
            $('#editService').empty().append(`<option value="">Leistung wählen</option>`);
        }

        if (department) {
            $.get(`/product-position/positions/${department}`, function(data) {
                data.forEach(p => {
                    const opt = `<option value="${p.id}" data-department-id="${department}">${p.position}</option>`;
                    $('#editPositionsInternal').append(opt);
                    $('#editPositionsExternal').append(opt);
                });

                initPositionSelect2($('#editPositionsInternal'));
                initPositionSelect2($('#editPositionsExternal'));

                buildPositionEmployeeMapForDepartment(department, data);

                $('#editPositionsInternal').val(posIds.internal || []).trigger('change.select2');
                $('#editPositionsExternal').val(posIds.external || []).trigger('change.select2');
            });
        }

        $('#editStage, #editProduct, #editService, #editDepartment').select2({ width:'100%' });

        $('#editModal').modal('show');
    });

    $('.deleteRecord').off('click').on('click', function() {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: 'Dieser Vorgang kann nicht rückgängig gemacht werden.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/product-position/delete/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Gelöscht',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        fetchSavedRecords();
                        $('#employeeListInternal').html('<div class="empty">Keine internen Mitarbeiter ausgewählt.</div>');
                        $('#employeeListExternal').html('<div class="empty">Keine externen Mitarbeiter ausgewählt.</div>');
                        $('#employeeBadge').text('0 gefunden');
                        $('#employeeCountInternal').text('0');
                        $('#employeeCountExternal').text('0');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler',
                            text: 'Der Datensatz konnte nicht gelöscht werden.',
                            timer: 1800,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    });
}

// Update Record from Edit Modal
$('#editForm').on('submit', function(e) {
    e.preventDefault();

    const id = $('#editId').val();
    const payload = {
        stage: $('#editStage').val(),
        produkt: $('#editProduct').val(),
        service: $('#editService').val(),
        department: $('#editDepartment').val(),
        positions: {
            internal: $('#editPositionsInternal').val() || [],
            external: $('#editPositionsExternal').val() || []
        }
    };

    const $btn = $(this).find('button[type=submit]').prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm"></span> Aktualisieren…');

    $.ajax({
        url: `/product-position/update/${id}`,
        method: 'POST',
        data: payload,
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(resp) {
            Swal.fire({ icon:'success', title:'Aktualisiert', text: resp.message, timer: 1500, showConfirmButton:false });
            $('#editModal').modal('hide');
            fetchSavedRecords();
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i data-feather="check"></i> Aktualisieren');
            if (window.feather) feather.replace();
        }
    });
});
</script>
@endsection

