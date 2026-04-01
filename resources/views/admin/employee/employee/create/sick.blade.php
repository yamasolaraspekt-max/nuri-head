<div class="card">
    <div class="card-header">
        <h4 class="card-title">Krankmeldung</h4>
    </div>
    <div class="card-body">  
        <button class="btn btn-primary float-right mb-1" data-toggle="modal" data-target="#sickModal" onclick="resetForm()">Krankmeldung erstellen</button>

        <table class="table table-bordered mt-3">
            <thead>
            <tr>
               <th>#</th> 
                <th>Startdatum</th>
                <th>Enddatum</th>
                <th>Tage gesamt</th>
                <th>Krankmeldung</th> 
                <th>Stunden gesamt</th>
                <th>Jahr</th>
                <th>Status</th>
                <th>Beschreibung</th>
                <th>Aktion</th>
                </tr>
            </thead>
            <tbody id="sickTable">
                @foreach($sicks as $sick)
                <tr id="row-{{ $sick->id }}">
                    <td>{{ $loop->iteration }}</td> 
                    <td>{{ $sick->start_date }}</td>
                    <td>{{ $sick->end_date ?? 'N/A' }}</td>
                    <td>{{ $sick->total_days }}</td>
                    <td>
                        @if($sick->document)
                            <a href="{{ asset($sick->document) }}" target="_blank">Dokument anzeigen</a>
                        @else
                            Keiner
                        @endif

                    </td> 
                    <td>{{ $sick->total_hours }}</td>
                    <td>{{ $sick->year }}</td>
                    <td>{{ $sick->status }}</td>
                    <td>{{ $sick->status_msg }}</td>
                    <td>
                        <button class="btn btn-icon btn-icon rounded-circle btn-warning  waves-effect waves-light edit" data-id="{{ $sick->id }}"><i class="feather icon-edit"></i></button>
                        <button class="btn btn-icon btn-icon rounded-circle btn-danger waves-effect waves-light btn-sm delete" data-id="{{ $sick->id }}"><i class="feather icon-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Modal -->
        <div class="modal fade" id="sickModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Krankmeldung</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       <form id="sickForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="sick_id">
                            <input type="hidden" id="emp_id" value="{{$data->id}}" name="emp_id"> 

                            <div class="form-group">
                                <label>Startdatum</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Enddatum</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Gesamte Tage</label>
                                <input type="number" id="total_days" name="total_days" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Gesamtzeit</label>
                                <input type="number" id="total_hours" name="total_hours" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Krankmeldung (PDF, JPG, PNG)</label>
                                <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.png">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="employee_applied">Employee Applied</option>
                                    <option value="admin_applied">Admin Applied</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Bechreibung</label>
                                <textarea id="status_msg" name="status_msg" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
 <script>
   $(document).ready(function () {
   
    function calculateDaysAndHours() {
        let startDate = new Date($('#start_date').val());
        let endDate = new Date($('#end_date').val()); 

        let totalDays = 0;
        let totalHours = 0;

        if (!isNaN(startDate) && !isNaN(endDate)) {
            // Count only weekdays (Monday-Friday)
            let currentDate = new Date(startDate);
            while (currentDate <= endDate) {
                let dayOfWeek = currentDate.getDay(); // 0 = Sunday, 6 = Saturday
                if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                    totalDays++;
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }

            totalHours = totalDays * 24; // Default 24 hours per day if no time provided

          
        }

        $('#total_days').val(totalDays);
        $('#total_hours').val(totalHours);
    }

    $('#start_date, #end_date').on('change', calculateDaysAndHours);

    $('#sickForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.append("emp_id", $('#emp_id').val()); // ✅ Ensure emp_id is included

        let url = $('#sick_id').val() ? `/employee-sick/update/${$('#sick_id').val()}` : "/employee-sick/store";

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            processData: false,  // ✅ Prevent jQuery from processing data
            contentType: false,  // ✅ Ensure multipart/form-data is sent
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // ✅ CSRF Token
            },
            success: function (response) {
                alert(response.success);
                location.reload();
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    });

   
    
    $('.edit').click(function () {
        let id = $(this).data('id');
        $.ajax({
            url: `/employee-sick/edit/${id}`,
            method: "GET",
            success: function (data) {
                $('#sick_id').val(data.id);
                $('#start_date').val(data.start_date);
                $('#end_date').val(data.end_date); 
                $('#total_days').val(data.total_days);
                $('#total_hours').val(data.total_hours);
                $('#status').val(data.status);
                $('#status_msg').val(data.status_msg);
                $('#sickModal').modal('show');
            }
        });
    });

    $('.delete').click(function () {
        let id = $(this).data('id');
        if (confirm("Are you sure?")) {
            $.ajax({
                url: `/employee-sick/destroy/${id}`,
                method: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function () {
                    location.reload();
                }
            });
        }
    });

    function resetForm() {
        $('#sick_id').val('');
        $('#sickForm')[0].reset();
    }
});


 </script>

@endpush