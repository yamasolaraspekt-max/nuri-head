<div class="card">
    <div class="card-header">
        <h4 class="card-title">Krankenstand</h4>
    </div>
    <div class="card-body">  
        <button class="btn btn-primary" data-toggle="modal" data-target="#sickModal" onclick="resetForm()">Add Sick Record</button>

        <table class="table table-bordered mt-3">
            <thead>
            <tr>
               <th>#</th> 
                <th>Startdatum</th>
                <th>Enddatum</th>
                <th>Tage gesamt</th>
                <th>Startzeit</th>
                <th>Endzeit</th>
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
                    <td>{{ $sick->start_time ?? 'N/A' }}</td>
                    <td>{{ $sick->end_time ?? 'N/A' }}</td>
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
                        <h5 class="modal-title">Krankenakte</h5>
                        <button class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="sickForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="sick_id">
                            <input type="hidden" id="emp_id" value="{{$data->id}}"> 

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
 <script>
   $(document).ready(function () {
    function calculateDaysAndHours() {
        let startDate = new Date($('#start_date').val());
        let endDate = new Date($('#end_date').val());
        let startTime = $('#start_time').val();
        let endTime = $('#end_time').val();

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

            // If start_time and end_time are provided, calculate hours ONLY for ONE day
            if (startTime && endTime) {
                totalDays = 1; // Ignore multiple days when time is provided
                let startDateTime = new Date($('#start_date').val() + 'T' + startTime);
                let endDateTime = new Date($('#start_date').val() + 'T' + endTime); // Always use start date

                // If end time is before start time (next-day case), adjust accordingly
                if (endDateTime < startDateTime) {
                    endDateTime.setDate(endDateTime.getDate() + 1);
                }

                totalHours = (endDateTime - startDateTime) / (1000 * 60 * 60); // Convert milliseconds to hours
            }
        }

        $('#total_days').val(totalDays);
        $('#total_hours').val(totalHours);
    }

    $('#start_date, #end_date, #start_time, #end_time').on('change', calculateDaysAndHours);

    $('#sickForm').on('submit', function (e) {
        e.preventDefault();
        let id = $('#sick_id').val();
        let url = id ? `/employee-sick/update/${id}` : "/employee-sick/store";
        
        $.ajax({
            url: url,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                emp_id: $('#emp_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                total_days: $('#total_days').val(),
                total_hours: $('#total_hours').val(),
                status: $('#status').val(),
                status_msg: $('#status_msg').val()
            },
            success: function () {
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
                $('#start_time').val(data.start_time);
                $('#end_time').val(data.end_time);
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
