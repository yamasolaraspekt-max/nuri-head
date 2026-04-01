<style>
    .department_tables tr td {
        font-size: 12px !important;
    }
</style>

<section id="nav-justified">
    <div class="row">
        <div class="col-sm-12">
            <div class="card overflow-hidden">
                <div class="card-content">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab-justified"
                                   data-toggle="tab" href="#home-just" role="tab"
                                   aria-controls="home-just" aria-selected="true">
                                    Abteilung & Jobs
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content pt-1">
                            <div class="tab-pane active" id="home-just" role="tabpanel"
                                 aria-labelledby="home-tab-justified">
                                <div class="card mb-0">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <h4 class="card-title mb-0">Abteilungen & Positionen</h4>

                                            <button type="button"
                                                    class="btn btn-outline-primary square waves-effect waves-light"
                                                    data-target="#newDept" data-toggle="modal">
                                                Abteilung zuweisen
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body pt-0">
                                        <div class="table-responsive" id="departmentTableWrapper">
                                            <table class="table table-striped table-sm department_tables mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Abteilung</th>
                                                    <th>Position</th>
                                                    <th>% gesamt</th>
                                                    <th>Montage %</th>
                                                    <th>Büro %</th>
                                                    <th>Hauptstelle</th>
                                                    <th style="width: 140px;">Aktionen</th>
                                                </tr>
                                                </thead>
                                                <tbody id="departmentTableBody">
                                                {{-- rows loaded via AJAX --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- CREATE MODAL --}}
                                <div class="modal fade text-left" id="newDept" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel17" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel17">
                                                    Abteilung dem Mitarbeiter zuweisen
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>

                                            <form class="form-horizontal">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $employee_id }}">

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <label for="department">Abteilung</label>
                                                            <select class="form-control department-select"
                                                                    id="create_department"
                                                                    style="width:100% !important;">
                                                                <option value="" disabled selected>Abteilung auswählen</option>
                                                                @foreach ($all_departments as $all_dept)
                                                                    <option value="{{ $all_dept->id }}">
                                                                        {{ $all_dept->department_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <label for="position">Position</label>
                                                            <select class="form-control position-select"
                                                                    id="create_position"
                                                                    style="width:100%">
                                                                <option value="" disabled selected>Position auswählen</option>
                                                                @foreach ($all_positions as $pos)
                                                                    <option value="{{ $pos->id }}">
                                                                        {{ $pos->position }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <label for="percent">Prozentsatz</label>
                                                            <input type="number"
                                                                   id="create_percent"
                                                                   class="form-control percent-input"
                                                                   min="1" max="100">
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-12 mt-1">
                                                            <label for="montage_percent">Montage</label>
                                                            <input type="number"
                                                                   id="create_montage_percent"
                                                                   class="form-control montage-input"
                                                                   min="0" max="100">
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-12 mt-1">
                                                            <label for="office_percent">Büroanteil</label>
                                                            <input type="number"
                                                                   id="create_office_percent"
                                                                   class="form-control office-input"
                                                                   min="0" max="100">
                                                        </div>

                                                        <div class="col-lg-4 col-md-6 col-sm-12 mt-1">
                                                            <label>Verbleibender Prozentsatz</label>
                                                            <input type="number"
                                                                   id="remain_percentage"
                                                                   class="form-control"
                                                                   readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn btn-danger waves-effect waves-light"
                                                            data-dismiss="modal">
                                                        abbrechen
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-primary waves-effect waves-light"
                                                            id="save_button_dept_single">
                                                        speichern
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- EDIT MODAL (generic) --}}
                                <div class="modal fade text-left" id="editDeptModal" tabindex="-1" role="dialog"
                                     aria-labelledby="editDeptLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="editDeptLabel">
                                                    Abteilungszuordnung bearbeiten
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form id="editDeptForm">
                                                @csrf
                                                <input type="hidden" id="edit_dp_id">

                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Abteilung</label>
                                                            <input type="text" id="edit_department_name" class="form-control" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Position</label>
                                                            <input type="text" id="edit_position_name" class="form-control" readonly>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>% gesamt</label>
                                                            <input type="number" id="edit_percent" class="form-control" min="1" max="100">
                                                        </div>
                                                        <div class="col-md-6 mt-1">
                                                            <label>Montage %</label>
                                                            <input type="number" id="edit_montage_percent" class="form-control" min="0" max="100">
                                                        </div>
                                                        <div class="col-md-6 mt-1">
                                                            <label>Büro %</label>
                                                            <input type="number" id="edit_office_percent" class="form-control" min="0" max="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        abbrechen
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        speichern
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- .tab-pane --}}
                        </div> {{-- .tab-content --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
if (!window.__EMP_DEPARTMENT_INIT__) {
    window.__EMP_DEPARTMENT_INIT__ = true;

    $(function () {
        const empId  = {{ $employee_id }};
        let isSaving = false;

        const baseMainActivateUrl   = '{{ url("/employee/department/position/main") }}';
        const baseMainDeactivateUrl = '{{ url("/employee/department/position/main/deactive") }}';

        // ---------- SELECT2 ----------
        function initSelect2In($container) {
            if (!$.fn.select2) return;

            $container.find('.department-select, .position-select').each(function () {
                const $s = $(this);
                if ($s.hasClass('select2-hidden-accessible')) return;

                const parentModal = $s.closest('.modal');
                $s.select2({
                    width: '100%',
                    dropdownParent: parentModal.length ? parentModal : $(document.body),
                    placeholder: $s.find('option[disabled]:first').text() || ''
                });
            });
        }

        initSelect2In($(document.body));

        // ---------- TABLE RENDER ----------
        function renderDepartmentRows(positions) {
            const $tbody = $('#departmentTableBody');
            $tbody.empty();

            if (!positions || !positions.length) {
                $tbody.append(
                    '<tr><td colspan="7" class="text-center text-muted">Keine Abteilungen zugewiesen.</td></tr>'
                );
                return;
            }

            positions.forEach(function (row) {
                const isMain = row.main === 'active';

                const mainBadge = isMain
                    ? '<span class="badge badge-success">Haupt</span>'
                    : '<span class="badge badge-light">-</span>';

                const mainToggleBtn = isMain
                    ? `<button type="button"
                                class="btn btn-sm btn-outline-warning btn-main-toggle"
                                data-dp-id="${row.dp_id}"
                                data-mode="deactivate">
                            Haupt entfernen
                       </button>`
                    : `<button type="button"
                                class="btn btn-sm btn-outline-success btn-main-toggle"
                                data-dp-id="${row.dp_id}"
                                data-mode="activate">
                            Als Haupt setzen
                       </button>`;

                const tr = `
                    <tr data-dp-id="${row.dp_id}">
                        <td>${row.department_name}</td>
                        <td>${row.position}</td>
                        <td>${row.percent ?? ''}</td>
                        <td>${row.montage_percent ?? ''}</td>
                        <td>${row.office_percent ?? ''}</td>
                        <td>${mainBadge}</td>
                        <td>
                            ${mainToggleBtn}
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary btn-edit-department"
                                    data-dp-id="${row.dp_id}"
                                    data-department-name="${row.department_name}"
                                    data-position-name="${row.position}"
                                    data-percent="${row.percent}"
                                    data-montage="${row.montage_percent}"
                                    data-office="${row.office_percent}">
                                Bearbeiten
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger btn-delete-department"
                                    data-dp-id="${row.dp_id}">
                                Löschen
                            </button>
                        </td>
                    </tr>
                `;
                $tbody.append(tr);
            });
        }

        // ---------- LOAD TABLE ----------
        function loadDepartmentTable() {
            $.get('{{ route("emp.department.table", ":id") }}'.replace(':id', empId), function (res) {
                // expect JSON: { success: true, positions: [...] }
                if (res.success) {
                    renderDepartmentRows(res.positions);
                } else {
                    console.error('Fehler beim Laden der Abteilungen', res);
                }
            }).fail(function (xhr) {
                console.error('Fehler beim Laden der Abteilungen', xhr);
            });
        }

        loadDepartmentTable();

        // ---------- REMAINING PERCENT ----------
        function fetchRemainingPercentage() {
            $.ajax({
                url: '{{ route("get.dept.remaining.percentage", ":id") }}'.replace(':id', empId),
                type: 'GET',
                success: function (response) {
                    if (response && response.remaining_percentage !== undefined) {
                        $('#remain_percentage').val(response.remaining_percentage);
                    } else {
                        $('#remain_percentage').val('');
                    }
                },
                error: function () {
                    console.error('Fehler beim Abrufen des verbleibenden Prozentsatzes.');
                    $('#remain_percentage').val('');
                }
            });
        }

        function updateMontageOfficeInputs(percentInput) {
            let percent = parseFloat($(percentInput).val()) || 0;
            let half    = Math.round(percent / 2);

            $('#create_montage_percent').val(half);
            $('#create_office_percent').val(percent - half);
        }

        $('#create_percent').on('input', function () {
            updateMontageOfficeInputs(this);
        });

        $('#create_montage_percent, #create_office_percent').on('input', function () {
            let percent = parseFloat($('#create_percent').val()) || 0;
            let montage = parseFloat($('#create_montage_percent').val()) || 0;
            let office  = parseFloat($('#create_office_percent').val()) || 0;

            // you *could* enforce montage+office==percent here if wanted
        });

        // ---------- CREATE MODAL ----------
        $('#newDept').on('shown.bs.modal', function () {
            initSelect2In($(this));
            fetchRemainingPercentage();
        });

        $('#newDept').on('hidden.bs.modal', function () {
            $('#create_department').val('').trigger('change');
            $('#create_position').val('').trigger('change');
            $('#create_percent').val('');
            $('#create_montage_percent').val('');
            $('#create_office_percent').val('');
            $('#remain_percentage').val('');
        });

        $(document).off('click', '#save_button_dept_single');
        $(document).on('click', '#save_button_dept_single', function (e) {
            e.preventDefault();
            if (isSaving) return;
            isSaving = true;

            const $btn = $(this);
            $btn.prop('disabled', true);

            const department     = $('#create_department').val();
            const position       = $('#create_position').val();
            const percent        = $('#create_percent').val();
            const montagePercent = $('#create_montage_percent').val();
            const officePercent  = $('#create_office_percent').val();

            if (!department || !position || !percent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Achtung!',
                    text: 'Bitte Abteilung, Position und Prozent ausfüllen.'
                });
                $btn.prop('disabled', false);
                isSaving = false;
                return;
            }

            const payload = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: empId,
                department: department,
                position: position,
                percent: percent,
                montage_percent: montagePercent,
                office_percent: officePercent
            };

            $.ajax({
                url: '{{ route("emp.add.department") }}',
                type: 'POST',
                data: payload,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Erfolgreich gespeichert!',
                            text: response.message || 'Neue Abteilung wurde gespeichert.',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            $('#newDept').modal('hide');
                            loadDepartmentTable();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler!',
                            text: response.message || 'Es gab ein Problem beim Speichern.'
                        });
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: xhr.responseJSON?.message || 'Es gab ein Problem beim Speichern.'
                    });
                },
                complete: function () {
                    $btn.prop('disabled', false);
                    isSaving = false;
                }
            });
        });

        // ---------- EDIT ----------
        $(document).on('click', '.btn-edit-department', function () {
            const $btn   = $(this);
            const dpId   = $btn.data('dp-id');
            const dept   = $btn.data('department-name');
            const pos    = $btn.data('position-name');
            const perc   = $btn.data('percent');
            const mont   = $btn.data('montage');
            const office = $btn.data('office');

            $('#edit_dp_id').val(dpId);
            $('#edit_department_name').val(dept);
            $('#edit_position_name').val(pos);
            $('#edit_percent').val(perc);
            $('#edit_montage_percent').val(mont);
            $('#edit_office_percent').val(office);

            $('#editDeptModal').modal('show');
        });

        $('#editDeptForm').on('submit', function (e) {
            e.preventDefault();
            if (isSaving) return;
            isSaving = true;

            const dpId           = $('#edit_dp_id').val();
            const percent        = $('#edit_percent').val();
            const montagePercent = $('#edit_montage_percent').val();
            const officePercent  = $('#edit_office_percent').val();

            $.ajax({
                url: '{{ route("emp.update.department") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    dp_id: dpId,
                    percent: percent,
                    montage_percent: montagePercent,
                    office_percent: officePercent
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Erfolgreich!',
                            text: response.message || 'Die Abteilung wurde erfolgreich aktualisiert.',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            $('#editDeptModal').modal('hide');
                            loadDepartmentTable();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler!',
                            text: response.message || 'Aktualisierung fehlgeschlagen.'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Etwas ist schief gelaufen!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: errorMessage
                    });
                },
                complete: function () {
                    isSaving = false;
                }
            });
        });

        // ---------- DELETE ----------
        $(document).on('click', '.btn-delete-department', function () {
            const dpId = $(this).data('dp-id');

            Swal.fire({
                title: 'Sind Sie sicher?',
                text: 'Möchten Sie diese Abteilung und ihre Position wirklich löschen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route("emp.delete.department", ":id") }}'.replace(':id', dpId),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Gelöscht!',
                                text: data.message || 'Die Abteilung wurde erfolgreich gelöscht.',
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                loadDepartmentTable();
                            });
                        } else {
                            Swal.fire({
                                title: 'Fehler!',
                                text: data.message || 'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        Swal.fire({
                            title: 'Fehler!',
                            text: 'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                            icon: 'error'
                        });
                    }
                });
            });
        });

        // ---------- HAUPTSTELLE TOGGLE ----------
        $(document).on('click', '.btn-main-toggle', function () {
            const dpId       = $(this).data('dp-id');
            const mode       = $(this).data('mode');    // "activate" or "deactivate"
            const isActivate = mode === 'activate';

            const url = (isActivate ? baseMainActivateUrl : baseMainDeactivateUrl)
                + '/' + dpId + '/' + empId;

            $.ajax({
                url: url,
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Erfolgreich!',
                            text: res.message || (isActivate
                                ? 'Hauptplanstelle gesetzt.'
                                : 'Hauptplanstelle entfernt.'),
                            timer: 1200,
                            showConfirmButton: false
                        });
                        loadDepartmentTable();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler!',
                            text: res.message || 'Aktion konnte nicht ausgeführt werden.'
                        });
                    }
                },
                error: function (xhr) {
                    let msg = 'Etwas ist schief gelaufen.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: msg
                    });
                }
            });
        });

    });
}
</script>
@endpush

