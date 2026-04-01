 <div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table   mb-0">
            <thead>
                <tr>  
                    <th>#</th> 
                    <th colspan="3">Termin</th>  
                    <th>Zweck</th>    
                    <th>Treffpunkt</th>      
                    <th>Kontakt</th>   
                    <th>Verfasser</th> 
                    <th>Teilnehmer</th> 
                    <th><i class="ficon feather icon-clock" style="font-size: 16px;"></i></th> 
                    <th><i class="ficon feather icon-repeat" style="font-size: 16px;"></i></th>
                    <th>Status</th>
                    <th>Bearbeitung</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item) 
                    <tr style="border-bottom: 10px solid #f8f8f8; ">   
                        <td  style="  border-left: 9px solid {{ $item->color }};     padding-left: 11px;"> 
                            <div class="icons">
                                <div class="number">
                                    {{ $item->id}}
                                </div>
                                
                                <div class="ring">
                                    @php 

                                        $now = \Carbon\Carbon::now(); // Current time
                                        $isOutdated = DB::table('main_appointments')
                                            ->where('id', $item->id)
                                            ->where('end_date', '<=', $now) // Check for overdue tasks
                                            ->exists(); // Check if any record exists
                                    @endphp

                                    @if($isOutdated)
                                        <i class="feather icon-bell warning out-date"></i>
                                    @endif   
                                </div>
                                <div class="lock"> 
                                     @if($item->public!= 1)
                                    <i class="feather icon-lock danger"></i>
                                    @else
                                    <i class="feather icon-unlock primary"></i>

                                    @endif
                                </div>

                                <div class="priority">
                                    @if($item->priority=="medium") 
                                        <i class="fa fa-battery-half"></i>  
                                    @elseif($item->priority=="high") 
                                      <i class="fa fa-battery-full"></i>  
                                    @elseif($item->priority=="very high")  
                                       <i class="fa fa-fire warning"></i>  
                                    @else  
                                        <i class="fa fa-battery-empty"></i> 
                                    @endif 
                                </div>
                            </div> 
                        </td>
                        <!-- Task Details -->
                        <td colspan="3">  
                              <a href="{{ url('appointment_details/' . $item->id) }}">
                                <p class="task" > 
                                    {{ $item->name }}
                                </p>
                                <p class="task_description p-0 m-0">
                                    {{ Str::limit($item->note, 140, '...') }}
                                </p>
                                <div class="description_details d-flex">
                                    <p class="m-0 p-0 task_date mr-1"><i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->end_date)->format('d.m.Y') }}</p>
                                    <p class=" task_date m-0 p-0 mr-1 "><i class="feather icon-clock"></i>  {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}</p>  

                                        @php
                                            $typeMap = [
                                                'consultation' => 'Beratung',
                                                'training'     => 'Training',
                                                'customer'     => 'Kundentreffen',
                                                'others'       => 'Sonstiges'
                                            ];

                                            $executionMap = [
                                                'internal'  => 'Intern',
                                                'external'  => 'Extern',
                                                'online'    => 'Online',
                                                'telephone' => 'Telefon'
                                            ];
                                        @endphp

                                        <!-- Display translated values -->
                                 
                                        <p class="m-0 p-0 task_date mr-1"><i class="feather icon-cast"></i> {{ $executionMap[$item->execution_type] ?? $item->execution_type }}</p> 
                                        <p class="task_date m-0 p-0 mr-1 "> <i class="feather icon-map-pin"></i> {{$item->branch  }}</p>
                                </div> 
                            </a>
                        </td> 
                        <td> 
                            {{ $typeMap[$item->appointment_type] ?? $item->appointment_type }} 
                        </td> 
                         <td> 
                     @foreach($contact_list as $contact)
                            @if($contact['main_id'] == $item->contact_id)
                                <p>{{ $contact['name'] }} {{ $contact['lastname'] }}</p>
                                <p> 
                                    <i class="feather icon-map"></i> 
                                    <small>
                                        <a class="p-0 m-0 map_modal" 
                                        data-latitude="{{ $contact['latitude'] ?? '-' }}" 
                                        data-longitude="{{ $contact['longitude'] ?? '-' }}">
                                            {{ $contact['street'] ?? '-' }} 
                                            {{ $contact['postcode'] ?? '-' }}
                                            {{ $contact['city'] ?? '-' }}
                                        </a>
                                    </small>
                                </p>
                            @endif
                        @endforeach 

                        </td> 
                       
                            @php
                            $customer_id = $item->customer_id ?? null;
                            $customer = DB::table('new_leads')
                                            ->where('id', $customer_id)
                                            ->select('id', 'name', 'lastname', 'street', 'postcode', 'city', 'latitude', 'longitude')
                                            ->first();
                            @endphp     

                            @if ($customer)
                                <td>
                                   <p class="m-0 p-0"> {{ $customer->name }} {{ $customer->lastname }}</p>
                                    <i class="feather icon-map"></i> <small>
                                        <a class="map_modal" data-latitude="{{ $customer->latitude }}" data-longitude="{{ $customer->longitude }}">
                                            {{ $customer->street }} {{ $customer->postcode }}, {{ $customer->city }}
                                        </a>
                                    </small>
                                </td>
                            @else
                                <td>-</td>
                            @endif 
                        <td>
                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $item->cname }} {{$item->clastname}}" class="avatar pull-up"> 
                                <img class="media-object rounded-circle " src="{{ asset('images/employee/'.$item->cimage)}}" alt="Ramin Sadid" height="25" width="25">
                            </li>
                        </td>

                        <td class="p-1">
                            <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                    @php
                                    // Group employees by task_id and filter unique employees
                                    $uniqueEmployees = $task_employee->where('appointment_id', $item->id)
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
                                                data-appointment-id="{{ $t_emp->appointment_id }}" 
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
                                    'confirm'   => 'BESTÄTIGT', 
                                    'completed'  => 'Vollendet',
                                    'expired'      => 'Abgelaufen',
                                    'cancel'     => 'absagen',
                                    'GELÖSCHT'     => 'GELÖSCHT',
                                ];
                            @endphp

                            {{ $progressStatusMap[$item->status] ?? 'Status nicht gefunden' }}
                        </td>

                        <td >
                            
                        <div class="btn-group dropup dropdown-icon-wrapper"  > 
                            <button type="button" class="btn   dropdown-toggle  waves-effect waves-light " data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                <i class="feather icon-menu dropdown-icon"></i>
                            </button>
                            <div class="dropdown-menu" >
                                <span class="ml-1 "> 
                                     <a class="ml-1 black" data-id="{{ $item->id }}" data-status="edit" href="{{ url('appointment/'.$item->id.'/edit') }}" >Bearbeiten</a>
                                </span>
                                       @php
                                        $currentUserTask = $task_employee->where('appointment_id', $item->id)
                                                ->where('employee_id', auth()->user()->name)
                                                ->first();
                                    @endphp
                              
                                    @if($currentUserTask) 
                                       @if($currentUserTask->status != 'reject')
                                            <span class="dropdown-item"> 
                                                <a class="dropdown-item accept-request-btn black" data-appointment-id="{{ $item->id }}" data-employee-id="{{ auth()->user()->name }}" data-toggle="modal" data-target="#acceptModal">Einladung ablehnen</a>

                                            </span>
                                        @else
                                        <span class="dropdown-item"> 
                                            <a class="dropdown-item change-btn black" data-appointment-id="{{ $item->id }}" data-employee-id="{{ auth()->user()->name }}" data-old-employee-id="{{$currentUserTask->employee_id}}" data-toggle="modal" data-target="#addEmployeeModal">Neuen Mitarbeiter anfragen</a>
                                        </span>
                                        @endif
                                    @endif

                                @if($item->deleted_at == Null)
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="delete">Löschen</a>
                                </span>
                                @else
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="recovery">Wiederherstellen</a>
                                </span>
                                @endif
                                @if($item->status != 'confirm')
                                <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="confirm">BESTÄTIGT</a>
                                </span>
                                @endif

                                @if($item->status != 'cancel')
                                 <span class="dropdown-item">
                                    <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="cancel">Absagen</a>
                                </span> 
                                @else
                                    <span class="dropdown-item">
                                        <a class="dropdown-item black" data-id="{{ $item->id }}" data-status="start">Starten</a>
                                    </span> 
                                @endif
 
                                @if(!in_array($item->status, ['cancel', 'pause']))
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
    </div>

  
</div>

 <script>
    $(document).ready(function () {
    // Handle button click to populate the modal fields
    $('.accept-request-btn').on('click', function () {
        let appointmentId = $(this).data('appointment-id');
        let employeeId = $(this).data('employee-id');

        // Populate hidden fields in the static modal
        $('#acceptModal input[name="appointment_id"]').val(appointmentId);
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
        const appointment = event.target.getAttribute('data-appointment-id');
        const oldEmployee = event.target.getAttribute('data-old-employee-id');

        console.log('appintment ID from element:', appointment);
        console.log('Old Employee ID from element:', oldEmployee);

        // Find and set values in the hidden inputs
        const appointmentInput = document.getElementById('appointment_id');
        const oldEmployeeInput = document.getElementById('old_employee');

        if (appointmentInput) {
            appointmentInput.value = appointment;  // Set appointment ID
            console.log('appointment assigned ', appointmentInput.value);
        } else {
            console.error('Task ID input not found.');
        }

        if (oldEmployeeInput) {
            oldEmployeeInput.value = oldEmployee;  // Set old employee ID
            console.log('Old Employee set in input:', oldEmployeeInput.value);
        } else {
            console.error('Old Employee input not found.');
        }

        $('#addEmployeeModal').modal('show');  // Show the modal
    }
});

// Handle form submission when the save button is clicked
document.getElementById('save-add-emp').addEventListener('click', function () {
    const form = document.getElementById('add-employee-modal');
    const formData = new FormData(form);  // Serialize form data

    // Debugging form data
    console.log('Form Data:', Array.from(formData.entries()));

    fetch("{{ route('appointment.add.employee') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,  // Include CSRF token
        },
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            console.log('Server Response:', data);  // Debug server response
            if (data.success) {
                $('#addEmployeeModal').modal('hide');  // Close modal
                toastr.success('Mitarbeiter erfolgreich hinzugefügt!');

                // Reload after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);  // 1.5-second delay
            } else {
                toastr.error(data.error || 'Fehler beim Hinzufügen des Mitarbeiters.');
            }
        })
        .catch(error => {
            console.error('Fehler beim Senden der Anfrage:', error);  // Log fetch error
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
            const deleteUrl = "{{ url('/appointments/destroy/') }}/" + taskId; // Task delete URL
            const recoverUrl = "{{ url('/appointments/restore/') }}/" + taskId; // Task delete URL
            const statusUrl = "{{ route('appointment.status') }}"; // Status change URL

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
            } 
            
            else if (taskStatus === 'recovery') {
                // SweetAlert for deleting a task
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Möchten Sie diesen Termin wiederherstellen',
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
                            url: recoverUrl,
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
            } 
            else if (taskStatus) {
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
            const noReminderUrl = "{{ route('appointment.no.reminder') }}"; // No Reminder URL
            const noRepeatUrl = "{{ route('appointment.no.repeat') }}"; // No Repeat URL

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
<script>
    $('.map_modal').on('click', function () {
        const destinationLat = parseFloat($(this).attr('data-latitude'));
        const destinationLng = parseFloat($(this).attr('data-longitude'));
        const destinationAddress = $(this).text();

        // Show SweetAlert2 waiting message
        Swal.fire({
            title: 'Warten...',
            text: 'Die Karte wird geladen...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const currentLat = position.coords.latitude;
                const currentLng = position.coords.longitude;

                // Create a temporary container for the map
                const mapContainer = document.createElement('div');
                mapContainer.id = 'map-container';
                mapContainer.style.width = '100%';
                mapContainer.style.height = '400px';

                // Initialize Google Maps
                const map = new google.maps.Map(mapContainer, {
                    zoom: 7,
                    center: { lat: currentLat, lng: currentLng }
                });

                // Display the map and direction
                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                // Calculate and display the route
                directionsService.route({
                    origin: { lat: currentLat, lng: currentLng },
                    destination: { lat: destinationLat, lng: destinationLng },
                    travelMode: 'DRIVING'
                }, (result, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                        const duration = result.routes[0].legs[0].duration.text;

                        // Show SweetAlert2 with the map and travel details
                        Swal.fire({
                            title: 'Routeninformationen',
                            html: `
                                <div id="map-container-wrapper" style="width: 100%; height: 400px; margin-bottom: 10px;"></div>
                                <p>Ziel: <strong>${destinationAddress}</strong></p>
                                <p>Geschätzte Fahrzeit: <strong>${duration}</strong></p>
                                <p>Möchten Sie die Route in Google Maps öffnen?</p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Wegbeschreibung anzeigen',
                            cancelButtonText: 'Abbrechen',
                            didRender: () => {
                                // Append the map to SweetAlert2's HTML container
                                document.getElementById('map-container-wrapper').appendChild(mapContainer);
                            },
                            icon: 'info',
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.open(`https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${destinationLat},${destinationLng}`, '_blank');
                            }
                        });
                    } else {
                        Swal.fire('Fehler', 'Konnte die Route nicht abrufen. Bitte versuchen Sie es später erneut.', 'error');
                    }
                });
            }, error => {
                Swal.fire('Geolocation Fehler', 'Konnte den aktuellen Standort nicht abrufen.', 'error');
            });
        } else {
            Swal.fire('Geolocation nicht verfügbar', 'Ihr Browser unterstützt keine Standortdienste.', 'error');
        }
    });
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    // Get the task_id parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const taskId = urlParams.get('task_id');
    if (taskId) {
        // Look for the row that has a matching data attribute.
        const editedRow = document.querySelector('[data-appointment-id="' + taskId + '"]');
        if (editedRow) {
            // Add a temporary class for highlighting
            editedRow.classList.add('edited');

            // Smooth scroll to the row
            editedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Remove the highlight after 3 seconds
            setTimeout(() => {
                editedRow.classList.remove('edited');
            }, 3000);
        }
    }
});
</script>
 

{{ $data->links() }}