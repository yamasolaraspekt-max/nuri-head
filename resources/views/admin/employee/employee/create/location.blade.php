<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Standortdienst</h4>
                <button class="btn btn-primary" id="createNewPostcode">+ Neue Postleitzahl</button>
                <input type="text" id="searchPostcode" class="form-control mt-2" placeholder="Postleitzahl suchen...">
            </div>

            <div class="card-body" id="postcodeListArea">
                @include('admin.employee.employee.create.location_data')
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="postcodeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form id="postcodeForm">
            @csrf
            <input type="hidden" name="_method" id="method" value="POST">
            <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee_id }}">
            <input type="hidden" name="id" id="postcode_id">


            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Postleitzahl</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group mb-2">
                    <label>Postleitzahl Von</label>
                    <input type="text" name="postcode_from" class="form-control" maxlength="2" required>
                </div>

                <div class="form-group mb-2">
                    <label>Postleitzahl Nach</label>
                    <input type="text" name="postcode_to" class="form-control" maxlength="2">
                </div>

                <div class="form-group mb-2">
                    <label>Land</label>
                    <input type="text" name="country" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button  class="btn btn-danger" data-dismiss="modal">abbrechen</button> 
                <button type="submit" class="btn btn-success" id="savePostcode">speichern</button>
            </div>

        </form>
    </div>
  </div>
</div>

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Load Data
    loadPostcodeData();

    function loadPostcodeData(search = '', highlightId = null) {
        $.ajax({
            url: "{{ url('admin/employee/employee') }}/{{ $employee_id }}/postcodes", // << Add employee ID in URL
            data: { search: search },
            success: function(data) {
                $('#postcodeListArea').html(data);

                if (highlightId) {
                    setTimeout(function() {
                        $('#row-' + highlightId).addClass('table-success');
                        $('html, body').animate({
                            scrollTop: $("#row-" + highlightId).offset().top - 150
                        }, 600);
                        setTimeout(function() {
                            $('#row-' + highlightId).removeClass('table-success');
                        }, 2000);
                    }, 200);
                }
            }
        });
    }

    // Search Postcodes
    $('#searchPostcode').on('keyup', function() {
        var search = $(this).val();
        loadPostcodeData(search);
    });

    // Open Create Modal
    $('#createNewPostcode').click(function() {
        $('#postcodeForm').trigger('reset');
        $('#method').val('POST');
        $('#postcode_id').val('');
        $('#modalTitle').text('Neue Postleitzahl hinzufügen');
        $('#postcodeModal').modal('show');
    });

    // Save or Update Postcode
    $('#postcodeForm').submit(function(e) {
        e.preventDefault();
        var id = $('#postcode_id').val();
        var method = id ? 'PUT' : 'POST';
        var url = id ? "{{ url('admin/employee/employee/postcodes') }}/" + id : "{{ route('employee-postcodes.store') }}";

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(res) {
                $('#postcodeModal').modal('hide');
                loadPostcodeData('', res.id); // Pass new or updated ID to highlight
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: id ? 'Postleitzahl aktualisiert' : 'Postleitzahl erstellt',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var error = xhr.responseJSON.error;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Achtung!',
                        text: error,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: 'Beim Speichern ist ein unbekannter Fehler aufgetreten!',
                    });
                }
            }
        });
    });

    // Edit Postcode
    $(document).on('click', '.editPostcode', function() {
        var id = $(this).data('id');
        $.get("{{ url('admin/employee/employee/postcodes') }}/" + id, function(data) {
            $('#postcodeForm').trigger('reset');
            $('input[name=postcode_from]').val(data.postcode_from);
            $('input[name=postcode_to]').val(data.postcode_to);
            $('input[name=country]').val(data.country);
            $('#postcode_id').val(data.id);
            $('#method').val('PUT');
            $('#modalTitle').text('Postleitzahl bearbeiten');
            $('#postcodeModal').modal('show');
        });
    });

    // Delete Postcode
    $(document).on('click', '.deletePostcode', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Bist du sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/employee/employee/postcodes') }}/" + id,
                    type: 'DELETE',
                    data: { "_token": "{{ csrf_token() }}" },
                    success: function(res) {
                        loadPostcodeData();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Postleitzahl gelöscht',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler!',
                            text: 'Beim Löschen ist ein Fehler aufgetreten!',
                        });
                    }
                });
            }
        });
    });

});
</script>
@endpush


