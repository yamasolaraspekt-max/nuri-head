{{-- resources/views/admin/product/partials/_technical_descriptions.blade.php --}}

<div class="card" id="technical-description-card" data-product-id="{{ $data->id }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            <i id="icon" class="fa fa-folder"></i> Technisches Beschreibung
        </h6>

        <div class="d-flex align-items-center">
            {{-- Add multiple technical data --}}
            <button type="button"
                    class="btn btn-sm btn-primary mr-1"
                    id="btn-open-add-tech-modal">
                <i class="feather icon-plus"></i> Neu
            </button>

            <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card-content collapse show">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm" id="technical-description-table">
                    <thead>
                        <tr>
                            <th>Feld</th>
                            <th>Beschreibung</th>
                            <th>Bemerkung</th>
                            <th>Status</th>
                            <th class="text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($descriptions as $descript)
                            <tr id="td-row-{{ $descript->id }}">
                                <td>{{ $descript->field }}</td>
                                <td>{{ $descript->description }}</td>
                                <td>{{ $descript->remark }}</td>
                                <td>{{ $descript->status }}</td>
                                <td class="text-right">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-edit-description"
                                            data-id="{{ $descript->id }}"
                                            data-field="{{ $descript->field }}"
                                            data-description="{{ $descript->description }}"
                                            data-remark="{{ $descript->remark }}"
                                            data-status="{{ $descript->status }}">
                                        <i class="feather icon-edit"></i>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-delete-description"
                                            data-id="{{ $descript->id }}">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        @if($descriptions->isEmpty())
                            <tr id="td-empty-row">
                                <td colspan="5" class="text-center text-muted">
                                    Keine technischen Daten vorhanden.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- ===================== ADD MULTIPLE DESCRIPTIONS MODAL ===================== --}}
<div class="modal fade" id="technicalDescriptionModal" tabindex="-1" role="dialog" aria-labelledby="technicalDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="technical-description-form">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="technicalDescriptionModalLabel">Technische Daten hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-1">
                        <small class="text-muted">
                            Mit <strong>+</strong> und <strong>−</strong> können mehrere technische Daten hinzugefügt/entfernt werden,
                            bevor alles gemeinsam gespeichert wird.
                        </small>
                    </div>

                    <div id="td-rows-container">
                        {{-- One default row --}}
                        <div class="td-row border rounded p-1 mb-1">
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label>Feld</label>
                                    <input type="text" name="field[]" class="form-control" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Beschreibung</label>
                                    <input type="text" name="description[]" class="form-control">
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Bemerkung</label>
                                    <input type="text" name="remark[]" class="form-control">
                                </div>

                                <div class="form-group col-md-2">
                                    <label>Status</label>
                                    <input type="text" name="status[]" class="form-control">
                                </div>

                                <div class="form-group col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger td-btn-remove-row">
                                        <i class="feather icon-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-secondary" id="td-btn-add-row">
                        <i class="feather icon-plus"></i> Zeile hinzufügen
                    </button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== EDIT SINGLE DESCRIPTION MODAL ===================== --}}
<div class="modal fade" id="editTechnicalDescriptionModal" tabindex="-1" role="dialog" aria-labelledby="editTechnicalDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="edit-technical-description-form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-description-id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTechnicalDescriptionModalLabel">Technische Beschreibung bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Feld</label>
                        <input type="text" class="form-control" id="edit-field" required>
                    </div>
                    <div class="form-group">
                        <label>Beschreibung</label>
                        <input type="text" class="form-control" id="edit-description">
                    </div>
                    <div class="form-group">
                        <label>Bemerkung</label>
                        <input type="text" class="form-control" id="edit-remark">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <input type="text" class="form-control" id="edit-status">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('script')
<script>
    (function ($) {
        $(document).ready(function () {
            var card      = $('#technical-description-card');
            var productId = card.data('product-id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // ----------------- ADD MODAL: OPEN -----------------
            $('#btn-open-add-tech-modal').on('click', function () {
                // clear all rows and add one fresh row
                var container = $('#td-rows-container');
                container.empty();
                container.append(buildRowHtml());
                $('#technicalDescriptionModal').modal('show');
            });

            // ----------------- ADD MODAL: ADD ROW (+) -----------------
            $('#td-btn-add-row').on('click', function () {
                $('#td-rows-container').append(buildRowHtml());
            });

            // ----------------- ADD MODAL: REMOVE ROW (−) -----------------
            $('#td-rows-container').on('click', '.td-btn-remove-row', function () {
                var rows = $('#td-rows-container .td-row');
                if (rows.length > 1) {
                    $(this).closest('.td-row').remove();
                } else {
                    // just clear the inputs if only one row left
                    $(this).closest('.td-row').find('input').val('');
                }
            });

            // ----------------- ADD MODAL: SUBMIT (AJAX) -----------------
            $('#technical-description-form').on('submit', function (e) {
                e.preventDefault();

                var url  = "{{ route('products.descriptions.bulkStore', ['product' => '__ID__']) }}".replace('__ID__', productId);
                var form = $(this);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        if (response.success) {
                            // remove "empty" row if present
                            $('#td-empty-row').remove();

                            response.descriptions.forEach(function (d) {
                                appendDescriptionRow(d);
                            });

                            $('#technicalDescriptionModal').modal('hide');
                        } else {
                            alert(response.message || 'Fehler beim Speichern.');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        alert('Fehler beim Speichern der technischen Daten.');
                    }
                });
            });

            // ----------------- EDIT MODAL: OPEN -----------------
            $('#technical-description-table').on('click', '.btn-edit-description', function () {
                var btn   = $(this);
                var id    = btn.data('id');

                $('#edit-description-id').val(id);
                $('#edit-field').val(btn.data('field') || '');
                $('#edit-description').val(btn.data('description') || '');
                $('#edit-remark').val(btn.data('remark') || '');
                $('#edit-status').val(btn.data('status') || '');

                $('#editTechnicalDescriptionModal').modal('show');
            });

            // ----------------- EDIT MODAL: SUBMIT (AJAX) -----------------
            $('#edit-technical-description-form').on('submit', function (e) {
                e.preventDefault();

                var id  = $('#edit-description-id').val();
                var url = "{{ route('products.descriptions.update', ['description' => '__ID__']) }}".replace('__ID__', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'PUT',
                        field: $('#edit-field').val(),
                        description: $('#edit-description').val(),
                        remark: $('#edit-remark').val(),
                        status: $('#edit-status').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            var d   = response.description;
                            var row = $('#td-row-' + d.id);

                            row.find('td:nth-child(1)').text(d.field);
                            row.find('td:nth-child(2)').text(d.description ?? '');
                            row.find('td:nth-child(3)').text(d.remark ?? '');
                            row.find('td:nth-child(4)').text(d.status ?? '');

                            var editBtn = row.find('.btn-edit-description');
                            editBtn.data('field', d.field);
                            editBtn.data('description', d.description);
                            editBtn.data('remark', d.remark);
                            editBtn.data('status', d.status);

                            $('#editTechnicalDescriptionModal').modal('hide');
                        } else {
                            alert(response.message || 'Fehler beim Aktualisieren.');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        alert('Fehler beim Aktualisieren der technischen Beschreibung.');
                    }
                });
            });

            // ----------------- DELETE (AJAX) -----------------
            $('#technical-description-table').on('click', '.btn-delete-description', function () {
                if (!confirm('Eintrag wirklich löschen?')) {
                    return;
                }

                var id  = $(this).data('id');
                var url = "{{ route('products.descriptions.destroy', ['description' => '__ID__']) }}".replace('__ID__', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#td-row-' + id).remove();

                            if ($('#technical-description-table tbody tr').length === 0) {
                                $('#technical-description-table tbody').append(
                                    '<tr id="td-empty-row">' +
                                        '<td colspan="5" class="text-center text-muted">Keine technischen Daten vorhanden.</td>' +
                                    '</tr>'
                                );
                            }
                        } else {
                            alert(response.message || 'Fehler beim Löschen.');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr);
                        alert('Fehler beim Löschen der technischen Beschreibung.');
                    }
                });
            });

            // ----------------- HELPER: BUILD NEW ROW HTML -----------------
            function buildRowHtml() {
                return '' +
                    '<div class="td-row border rounded p-1 mb-1">' +
                        '<div class="form-row">' +
                            '<div class="form-group col-md-3">' +
                                '<label>Feld</label>' +
                                '<input type="text" name="field[]" class="form-control" required>' +
                            '</div>' +
                            '<div class="form-group col-md-3">' +
                                '<label>Beschreibung</label>' +
                                '<input type="text" name="description[]" class="form-control">' +
                            '</div>' +
                            '<div class="form-group col-md-3">' +
                                '<label>Bemerkung</label>' +
                                '<input type="text" name="remark[]" class="form-control">' +
                            '</div>' +
                            '<div class="form-group col-md-2">' +
                                '<label>Status</label>' +
                                '<input type="text" name="status[]" class="form-control">' +
                            '</div>' +
                            '<div class="form-group col-md-1 d-flex align-items-end">' +
                                '<button type="button" class="btn btn-sm btn-danger td-btn-remove-row">' +
                                    '<i class="feather icon-minus"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
            }

            // ----------------- HELPER: APPEND CREATED ROW TO TABLE -----------------
            function appendDescriptionRow(d) {
                var tbody = $('#technical-description-table tbody');

                var rowHtml =
                    '<tr id="td-row-' + d.id + '">' +
                        '<td>' + (d.field ?? '') + '</td>' +
                        '<td>' + (d.description ?? '') + '</td>' +
                        '<td>' + (d.remark ?? '') + '</td>' +
                        '<td>' + (d.status ?? '') + '</td>' +
                        '<td class="text-right">' +
                            '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-description" ' +
                                'data-id="' + d.id + '" ' +
                                'data-field="' + (d.field ?? '') + '" ' +
                                'data-description="' + (d.description ?? '') + '" ' +
                                'data-remark="' + (d.remark ?? '') + '" ' +
                                'data-status="' + (d.status ?? '') + '">' +
                                '<i class="feather icon-edit"></i>' +
                            '</button> ' +
                            '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-description" ' +
                                'data-id="' + d.id + '">' +
                                '<i class="feather icon-trash"></i>' +
                            '</button>' +
                        '</td>' +
                    '</tr>';

                tbody.append(rowHtml);
            }
        });
    })(jQuery);
</script>
@endpush
