 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table table-hover-animation mb-0">
            <thead>
                <tr>
                    <th>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" value="false">
                            <span class="vs-checkbox vs-checkbox-sm">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span> 
                        </div>
                    </th> 
                    <th>Titel</th>
                    <th>ID</th> 
                    <th>Erstellt am</th> 
                    <th>Status</th> 
                    <th>Dauer</th>  
                    <th>Priorität</th > 
                    <th>Zugewiesen an</th> 
                    <th>Ändern</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item) 
                    <tr>
                        <td>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" value="{{ $item->id }}">
                                <span class="vs-checkbox vs-checkbox-sm">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span> 
                            </div>
                        </td>

                        <!-- Task Details -->
                        <td><a href="{{ url('personal_task_details/' . $item->id) }}">{{ $item->task_title }}</a></td>
                        <td>#{{ $item->task_id }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->start_date)->format('d.m.Y') }}</td>

                        <!-- Progress with Status Indicator -->
                        <td>
                            <i class="fa fa-circle font-small-3 text-success mr-50"></i>
                            {{ strtoupper($item->progress) }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->end_date)->format('d.m.Y') }}</td>

                            <td>
                            <div class="badge 
                                @if($item->priority == 'high') badge-danger 
                                @elseif($item->priority == 'medium') badge-warning 
                                @else badge-success 
                                @endif">
                                {{ ucfirst($item->priority) }}
                            </div>
                        </td> 
                        
                        <td class="p-1">
                            <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                @foreach ($task_employee->where('task_id', $item->id) as $t_emp) 
                                        @php
                                            // Determine Gender Default Image
                                            $gender_icon = $t_emp->gender === "Male" 
                                                ? asset('images/gender/male.png') 
                                                : asset('images/gender/female.png');

                                            // Fallback Image Check
                                            $profile_image = !empty($t_emp->image) 
                                                ? asset('images/employee/'.$t_emp->image) 
                                                : $gender_icon;
                                        @endphp
                                        <div>
                                            <li data-toggle="tooltip" 
                                                data-popup="tooltip-custom" 
                                                data-placement="bottom" 
                                                data-original-title="{{ $t_emp->name }} {{ $t_emp->lastname }}" 
                                                class="avatar pull-up">
                                                
                                                <img 
                                                    class="media-object rounded-circle change-btn 
                                                        @if($t_emp->status == 'send') send_request 
                                                        @elseif($t_emp->status== 'accept') accept_request 
                                                        @else reject_request 
                                                        @endif" 
                                                    src="{{ $profile_image }}" 
                                                    alt="{{ $t_emp->name }} {{ $t_emp->lastname }}" 
                                                    height="30" 
                                                    width="30"
                                                    data-task-id="{{ $t_emp->id }}" 
                                                    data-toggle="modal" 
                                                    data-target="#addEmployeeModal"
                                                    style="cursor: pointer;"
                                                >

                                            </li>
                                                    @if($t_emp->employee_id == auth()->user()->name && $t_emp->status=='send')
                                                    <button type="button" class="btn mr-1 mb-1 btn-outline-primary btn-sm waves-effect waves-light accept-request-btn" 
                                                        data-task-id="{{ $t_emp->task_id }}"
                                                        data-employee-id="{{ $t_emp->employee_id }}"  
                                                        data-toggle="modal" data-target="#acceptModal">Antwort</button>

                                                    @elseif($t_emp->status=='reject') 
                                                        <button type="button" class="btn btn-outline-primary btn-sm change-btn mr-1 mb-1"  data-task-id="{{$t_emp->id}}" data-toggle="modal" data-target="#addEmployeeModal">
                                                             Neuen Mitarbeiter anfragen
                                                            </button>
                                                    
                                                    @endif
                                        </div>

                                    @endforeach 
                            </ul>
                        </td>
                        <td>
                                <a type="button" 
                                        class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light" 
                                        href="{{ url('personal_task/'.$item->id.'/edit') }}">
                                    <i class="feather icon-edit"></i>
                                </a>
                            <a type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light" href="{{ url('personal_task_delete/'.$item->id) }}"><i class="feather icon-trash"></i></button>  
                        </td> 

                    </tr>  
                
                @endforeach 
            </tbody>
        </table>
    </div>
</div>

 <script>
    $(document).ready(function () {
    // Handle button click to populate the modal fields
    $('.accept-request-btn').on('click', function () {
        let taskId = $(this).data('task-id');
        let employeeId = $(this).data('employee-id');

        // Populate hidden fields in the static modal
        $('#acceptModal input[name="task_id"]').val(taskId);
        $('#acceptModal input[name="employee_id"]').val(employeeId);

        // Open the modal (optional if `data-toggle="modal"` is working)
        $('#acceptModal').modal('show');
    });

    // Handle form submission via AJAX
    $('#accept-request-form').on('submit', function (e) {
        e.preventDefault();

        let formData = $(this).serialize();

        // AJAX request
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function (response) {
                toastr.success('Anfrage erfolgreich übermittelt!');
                $('#acceptModal').modal('hide'); // Close the modal on success
                location.reload(); // Optionally reload the page or update the UI
            },
            error: function (xhr) {
                toastr.error('Es ist ein Fehler aufgetreten.');
            }
        });
    });
});

 </script>


<script>
    document.addEventListener('click', function (event) {
    if (event.target.matches('.change-btn')) {
        const taskId = event.target.getAttribute('data-task-id');
        console.log('Task ID from element:', taskId);

        const taskInput = document.getElementById('task_id');
        if (taskInput) {
            taskInput.value = taskId;
            console.log('Task ID set in input:', taskInput.value);
        } else {
            console.error('Task ID input not found.');
        }

        $('#addEmployeeModal').modal('show');
    }
});

document.getElementById('save-add-emp').addEventListener('click', function () {
    const form = document.getElementById('add-employee-modal');
    const formData = new FormData(form); // Serialize form data

    // Debug: Log form data before submission
    console.log('Form Data:', Array.from(formData.entries()));

    fetch("{{ route('personal.task.add.employee') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value, // Include CSRF token
        },
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            console.log('Server Response:', data); // Debug: Log server response
            if (data.success) {
                $('#addEmployeeModal').modal('hide'); // Close the modal
                toastr.success('Mitarbeiter erfolgreich hinzugefügt!');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500); // 1.5-second delay to show success message
            } else {
                toastr.error(data.error || 'Fehler beim Hinzufügen des Mitarbeiters.');
            }
        })
        .catch(error => {
            console.error('Fehler beim Senden der Anfrage:', error); // Debug: Log fetch error
            toastr.error('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
        });
});



</script>   
 


{{ $data->links() }}