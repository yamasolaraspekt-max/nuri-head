 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table   mb-0">
            <thead>
                <tr> 
                    <th>#</th>
                    <th></th> 
                    <th>Aufgabe</th>  
                    <th>Beschreibung</th>  
                    <th>Fälligkeitsdatum</th>      
                    <th>Zugewiesen</th> 
                    <th>Erinnerung</th>
                    <th>Wiederholen</th>
                    <th>Status</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item) 
                    <tr style="border-bottom: 10px solid #f8f8f8; "> 
                        
                        <td  style="  border-left: 9px solid {{ $item->color }}">{{ $loop->index + 1}}</td>
                        @php
                            // Fetch the total and completed counts in a single query
                            $taskKeys = DB::table('personal_task_keys')
                                ->selectRaw('
                                    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_count,
                                    COUNT(*) as total_count
                                ')
                                ->where('personal_task_id', $item->id)
                                ->first();

                            // Determine if all tasks are completed
                            $check_if_completed = $taskKeys && $taskKeys->completed_count == $taskKeys->total_count;
                        @endphp


                        <td>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" {{ $check_if_completed ? 'checked' : '' }} disabled>
                                <span class="vs-checkbox vs-checkbox-sm">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span> 
                            </div>
                           @php 

                                $now = \Carbon\Carbon::now(); // Current time
                                $isOutdated = DB::table('employees_personal_tasks')
                                    ->where('task_id', $item->id) 
                                    ->exists(); // Check if any record exists
                            @endphp

                            @if($isOutdated)
                                <i class="feather icon-bell warning out-date"></i>
                            @endif

                        </td> 
 
                        <!-- Task Details -->
                        <td >
                            <a href="{{ url('personal_task_details/' . $item->id) }}">
                                <h6 class="{{ $check_if_completed ? 'mark-complete' : '' }}" >
                                    @if($item->public!='on')
                                    <i class="feather icon-lock danger"></i>
                                    @else
                                    <i class="feather icon-unlock primary"></i>

                                    @endif
                                    {{ $item->task_title }}
                                </h6>
                                <p class="mb-0">{{ $item->task_id }}  
                                      @if($item->priority=="medium")
                                    <div class="badge badge-warning">
                                        <i class="fa fa-battery-half"></i> Medium
                                    </div>
                                    @elseif($item->priority=="high")
                                    <div class="badge badge-warning">
                                      <i class="fa fa-battery-full"></i> Hoch
                                    </div>
                                    @elseif($item->priority=="very high") 
                                    <div class="badge badge-secondary">
                                       <i class="fa fa-fire warning"></i> Sehr Wichtig
                                    </div> 
                                    @else 
                                    <div class="badge badge-primary">
                                        <i class="fa fa-battery-empty"></i> Normal
                                    </div> 
                                    @endif 
                                </p>
                            </a>
                            
                        </td> 
                         <td>
                            {{ $item->description }}
                         </td> 
                        <td>
                            <p class="m-0 p-0 warning"><i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->due_date)->format('d.m.Y') }}</p>
                            <p class="warning"><i class="feather icon-clock"></i>  {{ \Carbon\Carbon::parse($item->due_time)->format('H:i') }}</p>
                        </td> 
                   
                        
                        <td class="p-1">
                            <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                    @php
                                    // Group employees by task_id and filter unique employees
                                    $uniqueEmployees = $task_employee->where('task_id', $item->id)
                                                                    ->unique('employee_id');
                                    @endphp

                                    @foreach ($uniqueEmployees as $t_emp)
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
                                                class="media-object rounded-circle @if($t_emp->status!='accept') change-btn @endif
                                                    @if($t_emp->status == 'send') send_request 
                                                    @elseif($t_emp->status== 'accept') accept_request 
                                                    @else 
                                                    reject_request 
                                                    @endif" 
                                                src="{{ $profile_image }}" 
                                                alt="{{ $t_emp->name }} {{ $t_emp->lastname }}" 
                                                height="25" 
                                                width="25"
                                                @if($t_emp->status != 'accept')
                                                data-task-id="{{ $t_emp->task_id }}" 
                                                data-old-employee-id="{{ $t_emp->employee_id }}" 
                                                data-toggle="modal"  
                                                data-target="#addEmployeeModal" 
                                                style="cursor: pointer;
                                                @endif"
                                            >
                                        </li>
                                        
                                    </div>
                                    @endforeach

                            </ul>
                        </td>
                        <td>
                            @if($item->reminder_date || $item->reminder_time)
                            <small class="no-reminder-icon" data-id="{{$item->id}}">
                                    <i class="feather icon-bell primary"></i> 
                                    {{ $item->reminder_date }} {{ $item->reminder_time }} 
                                </small>  
                            @else
                            -
                            @endif
                        </td> 
                        <td>
                            @if($item->repeat)
                            <small class="no-repeat-icon" data-id="{{$item->id}}">
                                    <i class="feather icon-refresh-ccw primary"></i> 
                                    {{ $item->repeat }}  
                                </small>  
                            @else
                            -
                            @endif     
                        </td>
                        <td>
                            @php
                                $progressStatusMap = [
                                    'new'        => 'Neue',
                                    'start'      => 'Starten',
                                    'on_going'   => 'Im Prozess',
                                    'on_review'  => 'Kurz vor Abschluss',
                                    'completed'  => 'Vollendet',
                                    'pause'      => 'Pause',
                                    'cancel'     => 'Abbrechen',
                                ];
                            @endphp

                            {{ $progressStatusMap[$item->task_status] ?? 'Status nicht gefunden' }}
                        </td>

                        <td style="position: absolute;right: 20px;">
                            
                        <div class="btn-group dropup dropdown-icon-wrapper"> 
                            <button type="button" class="btn   dropdown-toggle  waves-effect waves-light " data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                <i class="feather icon-menu dropdown-icon"></i>
                            </button>
                            <div class="dropdown-menu" >
                                <span class="ml-2"> 
                                    <i class="feather icon-edit"></i> <a class=" black" data-id="{{ $item->id }}" data-status="edit" href="{{ url('personal_task/'.$item->id.'/edit') }}" >Bearbeiten</a>
                                </span>
                                <span class="dropdown-item">
                                   
                                   @php
                                        $currentUserTask = $task_employee->where('task_id', $item->id)
                                                ->where('employee_id', auth()->user()->name)
                                                ->first();
                                    @endphp

                                    @if($currentUserTask)
                                        @if($currentUserTask->status == 'send')
                                            <a class="dropdown-item accept-request-btn black" data-task-id="{{ $item->id }}" data-employee-id="{{ auth()->user()->name }}" data-toggle="modal" data-target="#acceptModal">Aufgabe annehmen</a>
                                        @elseif($currentUserTask->status == 'reject')
                                            <a class="dropdown-item change-btn black" data-task-id="{{ $item->id }}" data-employee-id="{{ auth()->user()->name }}" data-old-employee-id="{{$currentUserTask->employee_id}}" data-toggle="modal" data-target="#addEmployeeModal">Neuen Mitarbeiter anfragen</a>
                                        @endif
                                    @endif

                                </span>
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="delete">Löschen</a>
                                </span>
                                @if($item->task_status != 'pause')
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="pause">Pausieren</a>
                                </span>
                                @else
                                  <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="start">Starten</a>
                                </span>
                                @endif

                                @if($item->task_status != 'cancel')
                                 <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="cancel">Stornierung</a>
                                </span>
                                @else
                                  <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="start">Starten</a>
                                </span>
                                @endif

                                @if($item->task_status != 'completed')
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="completed">Abgeschlossen</a>
                                </span>
                                @else
                                  <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="start">Starten</a>
                                </span>
                                @endif
                                
                                @if(!in_array($item->task_status, ['cancel', 'pause']))
                                    <div class="dropdown-divider"></div>  
                                    @if($item->reminder_date)
                                        <span class="dropdown-item">
                                            <a class="dropdown-item black reminder" data-id="{{$item->id}}" data-button="no_reminder">Erinnerung abbrechen</a>
                                        </span>
                                    @else
                                        <span class="dropdown-item">
                                            <a class="dropdown-item black reminder" data-id="{{$item->id}}" data-button="add_reminder">Erinnerung</a>
                                        </span>
                                    @endif 

                                    @if($item->repeat)
                                        <span class="dropdown-item">
                                            <a class="dropdown-item black repeat" data-id="{{$item->id}}" data-button="no_repeat">Abbrechen Wiederholen</a>
                                        </span>
                                    @else
                                        <span class="dropdown-item">
                                            <a class="dropdown-item black repeat" data-id="{{$item->id}}" data-button="repeat">Wiederholen</a>
                                        </span>
                                    @endif
                                @endif

                            </div>
                        </div>
                         
                        </td> 

                    </tr>  
                
                @endforeach 
            </tbody>
        </table>
        {{ $data->links() }}
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
        const oldEmployee = event.target.getAttribute('data-old-employee-id');
        console.log('Task ID from element:', taskId);

        const taskInput = document.getElementById('task_id');
        const oldEmployeeInput = document.getElementById('old_employee');
        if (taskInput) {
            taskInput.value = taskId;
            console.log('Task ID set in input:', taskInput.value);
        } else {
            console.error('Task ID input not found.');
        }

          if (oldEmployeeInput) {
            oldEmployeeInput.value = oldEmployee;
            console.log('Old Employee set in input:', oldEmployeeInput.value);
        } else {
            console.error('Old Employee input not found.');
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
 
<!-- Delete, puase, cancel operation : start  -->
 <script>
    $(document).ready(function () {
        // Attach click event to dropdown items with data attributes
        $(document).on('click', '.dropdown-item a', function (e) {
            e.preventDefault();

            const taskId = $(this).data('id'); // Get task ID
            const taskStatus = $(this).data('status'); // Get task status (if present)
            const deleteUrl = "{{ url('personal_task_delete') }}/" + taskId; // Task delete URL
            const statusUrl = "{{ route('personal.task.project.status') }}"; // Status change URL

            if (taskStatus === 'delete') {
                // SweetAlert for deleting a task
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Diese Aufgabe wird dauerhaft gelöscht. Möchten Sie fortfahren?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ja, löschen!',
                    cancelButtonText: 'Abbrechen',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform task deletion
                        $.ajax({
                            url: deleteUrl,
                            method: 'GET', // Assuming the delete route uses GET
                            success: function (response) {
                                Swal.fire(
                                    'Gelöscht!',
                                    'Die Aufgabe wurde erfolgreich gelöscht.',
                                    'success'
                                );

                                // Optionally, reload the page or update the UI
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            } else if (taskStatus) {
                // SweetAlert for changing task status
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: `Möchten Sie den Status der Aufgabe wirklich auf "${taskStatus}" ändern?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ja, ändern!',
                    cancelButtonText: 'Abbrechen',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform status change
                        $.ajax({
                            url: statusUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}', // Add CSRF token
                                id: taskId,
                                project_status: taskStatus,
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Erfolgreich!',
                                        response.message,
                                        'success'
                                    );

                                    // Optionally, reload the page or update the UI
                                    location.reload();
                                } else {
                                    Swal.fire(
                                        'Fehler!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            }
        });
    });
</script>


<!-- Reminder and Repeat Scripts:  -->
  <script>
    $(document).ready(function () {
        // Listen for clicks on reminder and repeat dropdown items
        $(document).on('click', '.reminder, .repeat', function (e) {
            e.preventDefault();

            const taskId = $(this).data('id'); // Task ID
            const action = $(this).data('button'); // Action type
            const noReminderUrl = "{{ route('personal.task.project.no.reminder') }}"; // No Reminder URL
            const noRepeatUrl = "{{ route('personal.task.project.no.repeat') }}"; // No Repeat URL

            // Reminder actions
            if (action === 'no_reminder') {
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Möchten Sie die Erinnerung wirklich abbrechen?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ja, abbrechen!',
                    cancelButtonText: 'Abbrechen',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to clear reminder
                        $.ajax({
                            url: noReminderUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: taskId,
                                reminder_date: null,
                                reminder_time: null,
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Erfolgreich!',
                                    response.message,
                                    'success'
                                );

                                // Reload the page or update UI
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            } else if (action === 'add_reminder') {
                Swal.fire({
                    title: 'Neue Erinnerung setzen',
                    html: `
                        <label>Datum:</label>
                        <input type="date" id="reminder_date" class="swal2-input">
                        <label>Uhrzeit:</label>
                        <input type="time" id="reminder_time" class="swal2-input">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen',
                    preConfirm: () => {
                        return {
                            reminder_date: document.getElementById('reminder_date').value,
                            reminder_time: document.getElementById('reminder_time').value,
                        };
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { reminder_date, reminder_time } = result.value;

                        // Send AJAX request to save reminder
                        $.ajax({
                            url: noReminderUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: taskId,
                                reminder_date: reminder_date,
                                reminder_time: reminder_time,
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Erfolgreich!',
                                    response.message,
                                    'success'
                                );

                                // Reload the page or update UI
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            }

            // Repeat actions
            if (action === 'no_repeat') {
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Möchten Sie das Wiederholen wirklich abbrechen?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ja, abbrechen!',
                    cancelButtonText: 'Abbrechen',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to clear repeat
                        $.ajax({
                            url: noRepeatUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: taskId,
                                repeat: false,
                                repeat_date: null,
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Erfolgreich!',
                                    response.message,
                                    'success'
                                );

                                // Reload the page or update UI
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            } else if (action === 'repeat') {
                Swal.fire({
                    title: 'Wiederholen aktivieren',
                    html: `
                        <label>Wiederholung:</label> 
                        <select name="repeat" class="form-control repeat-select" id="repeat_date">
                        <option value="">Häufigkeit auswählen</option>
                        <option value="minute">Minütlich</option>
                        <option value="hourly">Stündlich</option>
                        <option value="daily">Täglich</option>
                        <option value="weekly">Wöchentlich</option>
                        <option value="monthly">Monatlich</option>
                        <option value="quarterly">Vierteljährlich</option>
                        <option value="yearly">Jährlich</option>
                    </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    cancelButtonText: 'Abbrechen',
                    preConfirm: () => {
                        return {
                            repeat_date: document.getElementById('repeat_date').value,
                        };
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        const { repeat_date } = result.value;

                        // Send AJAX request to save repeat
                        $.ajax({
                            url: noRepeatUrl,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: taskId,
                                repeat: true,
                                repeat_date: repeat_date,
                            },
                            success: function (response) {
                                Swal.fire(
                                    'Erfolgreich!',
                                    response.message,
                                    'success'
                                );

                                // Reload the page or update UI
                                location.reload();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire(
                                    'Fehler!',
                                    'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
                                    'error'
                                );
                                console.error('Error:', xhr.responseText);
                            },
                        });
                    }
                });
            }
        });
    });
</script>


