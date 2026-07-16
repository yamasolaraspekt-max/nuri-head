@extends('admin.layouts.app')

@section('title') PROJEKT @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

 <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" /> 
<link rel="stylesheet" href="{{ asset('css/project-sider.css')}}" /> 
<link rel="stylesheet" href="{{ asset('css/project-karban.css')}}" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}">  
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
 

 <style>
    .circle {
      width: 35px;
      height: 35px;
      background-color: #7DC242;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .line {
         width: 9px;
            height: 4px;
            background-color: #7DC242;
            margin-left: -3px;
            margin-right: -2px;
            position: relative;
            top: 2px;
    }
    .profile {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7DC242;
    }

    .profile-s {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f4a459;
    }
    .profile-r {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ea5455;
    }
    .text {
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
    }


    .modal {
            z-index: 1050 !important;
        }

    
        .modal-backdrop{
            display:none !important;
        }

        body.modal-open {
            overflow: hidden;
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
                            <div class="col-12">
                                <h2 class="content-header-title float-left mb-0">PROJEKT</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="content-body">
                                <section id="basic-tabs-components">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="cards overflow-hidden"> 
                                                <div class="card-content">
                                                    <div class="card-body"> 
                                                        <ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Kanban</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Liste</a>
                                                            </li> 
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" aria-controls="about" role="tab" aria-selected="false">Kalendar</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                <section> 
                                                                    <div class="col-12">
                                                                        <div class="row">
                                                                            <div class="col-md-6 col-12 mb-1">
                                                                                <form id="kanbanSearchForm">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <input type="text" class="form-control" id="searchInput" placeholder="Geben Sie die Details Ihrer Suche ein" name="search">
                                                                                            <div class="input-group-append">
                                                                                                <button class="btn btn-primary waves-effect waves-light" type="button" id="searchButton">
                                                                                                    <i class="feather icon-search"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div> 
                                                                                    </fieldset>
                                                                                </form> 
                                                                            </div>
                                                                            <div class="col-md-6 col-12 mb-1">   </div>
                                                                        </div>
                                                                        <div class="row"> 
                                                                            <div class="kanban-container" id="kanban">
                                                                                <div class="column" id="new" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>NEW</h3>
                                                                                </div>
                                                                                <div class="column" id="plan" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Planung</h3>
                                                                                </div>
                                                                                <div class="column" id="process" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Prozess</h3>
                                                                                </div> 
                                                                                <div class="column" id="completed" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Abgeschlossen</h3>
                                                                                </div>
                                                                                <div class="column" id="junk" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Junk</h3>
                                                                                </div>
                                                                                <div class="column" id="pause" ondrop="drop(event)" ondragover="allowDrop(event)">
                                                                                    <h3>Pause </h3>
                                                                                </div>
                                                                            </div> 
                                                                        </div> 
                                                                    </div> 
                                                                </section>
                                                            </div>
                                                            <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                                <p>Pudding candy canes sugar plum cookie chocolate cake powder croissant. Carrot cake tiramisu danish
                                                                    candy cake muffin croissant tart dessert. Tiramisu caramels candy canes chocolate cake sweet roll
                                                                    liquorice icing cupcake.</p>
                                                            </div>
                                                            
                                                            <div class="tab-pane" id="dropdown32" role="tabpanel" aria-labelledby="dropdown32-tab" aria-expanded="false">
                                                                <p>Chocolate croissant cupcake croissant jelly donut. Cheesecake toffee apple pie chocolate bar biscuit
                                                                    tart croissant. Lemon drops danish cookie. Oat cake macaroon icing tart lollipop cookie sweet bear
                                                                    claw.</p>
                                                            </div>
                                                            <div class="tab-pane" id="about" aria-labelledby="about-tab" role="tabpanel">
                                                                <p>Carrot cake dragée chocolate. Lemon drops ice cream wafer gummies dragée. Chocolate bar liquorice
                                                                    cheesecake cookie chupa chups marshmallow oat cake biscuit. Dessert toffee fruitcake ice cream
                                                                    powder
                                                                    tootsie roll cake.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="project-profile-overlay" onclick="closeSidebar()"></div>
        <div id="project-profile" class="project-profile">
            <div class="project-profile-header">
                <h2 id="customer_name">Muller PV</h2>
                <div class="header-buttons">
                <button class="maximize-btn" onclick="toggleMaximizeSidebar()">&#x26F6;</button>
                <button class="close-btn" onclick="closeSidebar()">&times;</button>
                </div>
            </div>

            <div class="project-meta">
                <p><strong>Status:</strong> <span class="badge not-started" id="customer_status">Backlog</span></p>
                <p id="contact_person"><strong>Contact Person:</strong> Leo</p> 
                <p id="request_date"><strong>Start Date:</strong> 28. Februar 2025</p>
            </div>
            <div class="task-checklist" id="checklistSelectWrapper" style="display: none;">
                <select name="checklist" id="checklistSelect" class="form-control">
                    <option value=""></option>
                </select>
            </div>
            <div class="task-list" id="accordion-tasks">
                <h3>Tasks</h3>
            </div>



        <div class="task-list">
            <h3>Project Tasks</h3>

            <!-- Tabs -->
            <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                <button onclick="showTab('board')" id="tab-board" class="tab-button active"><i class="feather icon-clipboard"></i> Board</button>
                <button onclick="showTab('list')" id="tab-list" class="tab-button"><i class="feather icon-list"></i> Tasks</button>
            </div>

            <!-- Board View -->
            <div id="board-view" class="tab-view">
                <div style="display: flex; gap: 20px;">
                    <!-- Not started column -->
                    <div class="task-cards">
                        <strong class="badge not-started">Not started</strong>
                        <div class="task">Task</div>
                        <div class="task">Task2<br><small>Test Sadid</small></div>
                    </div> 
                </div>
            </div>

            <!-- List View -->
            <div id="list-view" class="tab-view" style="display: none;">
                <table class="task-table">
                <thead>
                    <tr>
                    <th>Task name</th>
                    <th>Status</th>
                    <th>Assignee</th>
                    <th>Due</th>
                    <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <td>Task 3</td>
                    <td><span class="badge done">Done</span></td>
                    <td>Test Sadid</td>
                    <td>19. März 2025</td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>Task2</td>
                    <td><span class="badge not-started">Not started</span></td>
                    <td>Test Sadid</td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>T</td>
                    <td><span class="badge in-progress">In progress</span></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>Task</td>
                    <td><span class="badge not-started">Not started</span></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td>Task</td>
                    <td><span class="badge done">Done</span></td>
                    <td>Test Sadid</td>
                    <td></td>
                    <td><span class="badge medium">Medium</span></td>
                    </tr>
                </tbody>
                </table>
            </div>
        </div>
<!-- END: Content-->


        <!-- Accept Request Modal  -->
        <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-hidden="false">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Stellenanfrage</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('project.task.accept') }}" method="post" id="accept-request-form">
                        @csrf
                        <div class="modal-body">
                            <p><i class="feather icon-info warning"></i> Sie wurden als Verantwortlicher für den folgenden Kunden ausgewählt</p>
                            <div class="row">
                                <input type="hidden" name="project_id" id="accept_project_id" value="">
                                <input type="hidden" name="employee_id" id="accept_employee_id" value="">
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <fieldset class="form-group">
                                        <label for="response">Antwort anfordern</label>
                                        <select name="response" class="form-control" required>
                                            <option value="accept">Akzeptieren</option>
                                            <option value="reject">Ablehnen</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-12 col-md-12 col-12 mb-1">
                                    <fieldset class="form-group">
                                        <label for="reason">Notiz</label>
                                        <textarea name="reason" class="form-control" rows="5" placeholder="Optional"></textarea>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
            <!-- Modal for Adding Employee -->
                       
        <div class="modal fade text-left" id="employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter hinzufügen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('add.employee.to.project')}}" method="post" id="add_employe_form">
                        @csrf
                        <input type="hidden" name="project_id" id="modal_project_id" value="">
                        <input type="hidden" name="old_employee" id="modal_old_employee" value="">
                        <input type="hidden" name="phase_id" id="modal_phase_id" value="">
                        <input type="hidden" name="activity_id" id="modal_activity_id" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id[]" id="employee_id_select" class="form-control employee" style="width: 100%;" multiple> 
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}" 
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="save-add-employee">Hinzufügen</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
            

        <!-- Change Employee  -->
        <div class="modal fade text-left" id="change_employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h5 class="modal-title" id="myModalLabel160">Mitarbeiter ändern</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('update.employee.project') }}" method="post" id="change_employee_form">
                        @csrf
                        <input type="hidden" name="project_id" id="change_project_id" value="">
                        <input type="hidden" name="old_employee" id="change_old_employee" value="">
                        <div class="modal-body">
                            <label for="employee_id">Mitarbeiter auswählen</label>
                            <select name="employee_id" id="employee_id" class="form-control employee" style="width: 100%;">
                                @foreach ($employees as $emp)
                                    <option value="{{$emp->id}}" 
                                            data-image="{{asset('images/employee/'.$emp->image)}}">
                                        {{$emp->name}} {{$emp->lastname}}
                                    </option>
                                @endforeach
                            </select>

                            <label for="employee_roll">Mitarbeiterfunktion</label>
                            <select name="employee_roll" id="employee_roll" class="form-control" style="width: 100%;">
                                <option value="member">Mitglied</option>
                                <option value="guest">Gast</option>
                                <option value="comentator">Kommentator(in)</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                            <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



             
@endsection
 
@section('script')  
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 

<script>
    $(document).ready(function(){
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
    $(document).ready(function() {
        $('.articles input[type="radio"]').on('change', function() {
            // Reset styles for all labels
            $('.articles input[type="radio"] + label').css({
                'background': '#b1aaaa',
                'color': 'inherit',
                'border-radius': '50%'
            });

            // Apply styles for the selected label
            if (this.checked) {
                $(this).next('label').css({
                    'background': '#92b532',
                    'color': 'white',
                    'border-radius': '50%'
                });

                // Send AJAX request
                let articleGroup = $(this).val();
                $.ajax({
                    url: '/customer_details', // Your endpoint for searching article group
                    method: 'GET',
                    data: { search: articleGroup, is_ajax: true },
                    success: function(response) {
                        // Handle the response here
                        console.log(response);
                        // Update the page content based on the response
                        $('#results').html(response); // Assuming 'results' is the id of the element where you want to display the results
                    },
                    error: function(error) {
                        // Handle the error here
                        console.error(error);
                    }
                });
            }
        });
    });
</script> 
<!-- Add Employee to proejct: start  -->
 
<script>
    $(document).ready(function () {
        // Initialize Select2 with custom template for displaying employee image
        $('#employee_id').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Add Employee button click handler
        $('.add_employee').on('click', function () {
            let projectId = $(this).data('project');
            $('#modal_project_id').val(projectId); // Set project_id in hidden input
        });

        // Function to format employee dropdown with image
        function formatEmployee(emp) {
            if (!emp.id) {
                return emp.text;
            }
            var imageUrl = $(emp.element).data('image');
            var markup = `
                <div class="d-flex align-items-center">
                    <img src="${imageUrl}" alt="" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 10px;">
                    <span>${emp.text}</span>
                </div>
            `;
            return markup;
        }

        // AJAX form submission
        $('#add_employe_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            let form = $(this);
            let url = form.attr('action'); // Get form action URL
            let data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success message with SweetAlert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page after success
                        $('#employee').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show validation errors with SweetAlert
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Validierungsfehler',
                            html: errorMessages, // Display errors in HTML format
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

 <!-- Add Employee to proejct: end  -->

<!-- accepting request modal: start -->
 <script>
    $(document).ready(function () {
        // Open Modal and Populate Data
        $(document).on('click', '#accept_button', function () {
            const projectId = $(this).data('project');
            const employeeId = $(this).data('employee');

            // Populate hidden inputs in the modal
            $('#accept_project_id').val(projectId);
            $('#accept_employee_id').val(employeeId);

            // Open the modal
            $('#acceptModal').modal('show');
        });

        // Submit Form with AJAX
        $('#accept-request-form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const url = form.attr('action'); // Get form action URL
            const data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success alert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page
                        $('#acceptModal').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show error messages
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Fehler',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>

<!-- accepting request modal: end -->
<script>
    $(document).ready(function () {
        // Open Modal and Populate Data
        $(document).on('click', '.change_employee', function () {
            const projectId = $(this).data('project'); // Get data-project value
            const employeeId = $(this).data('employee'); // Get data-employee value

            // Debugging to ensure values are captured
            console.log("Project ID:", projectId);
            console.log("Employee ID:", employeeId);

             $('#change_project_id').val(projectId);
            $('#change_old_employee').val(employeeId);
            $('#change_employee').modal('show');

        });

        // Submit Form with AJAX
        $('#change_employee_form').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const url = form.attr('action'); // Get form action URL
            const data = form.serialize(); // Serialize form data

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function (response) {
                    // Show success alert
                    Swal.fire({
                        title: 'Erfolg',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Close modal and refresh the page
                        $('#change_employee').modal('hide');
                        location.reload();
                    });
                },
                error: function (xhr) {
                    // Show error messages
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let field in errors) {
                            errorMessages += `${errors[field][0]}<br>`;
                        }

                        Swal.fire({
                            title: 'Fehler',
                            html: errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Fehler',
                            text: 'Ein unerwarteter Fehler ist aufgetreten.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
    $('.employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
});

function formatEmployee(employee) {
    if (!employee.id) {
        return employee.text;
    }

    const imageUrl = $(employee.element).data('image');
    const employeeName = employee.text;

    const markup = `
        <div style="display: flex; align-items: center;">
            <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
            <span>${employeeName}</span>
        </div>
    `;

    return markup;
}

</script>

<script>
  function showTab(tab) {
    document.getElementById('board-view').style.display = tab === 'board' ? 'block' : 'none';
    document.getElementById('list-view').style.display = tab === 'list' ? 'block' : 'none';

    document.getElementById('tab-board').classList.toggle('active', tab === 'board');
    document.getElementById('tab-list').classList.toggle('active', tab === 'list');
  }
</script>

   
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
   
    $(document).ready(function(){
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

        let loadedFromDefault = false;
        let alreadySaved = false;
        let selectedChecklistId = null;
        let loadedFromChecklist = false;
        let checklistToSave = null; // stores the checklist + task structure for save 
        let selectedCustomerId = null;
        let selectedAlternativeId = null;
        let selectedProductId = null;
        let selectedSectionName = null;
        let selectedProjectId = null;
    let selectedService = null;


        const emp_src = "{{ asset('images/employee/') }}";
            const statusMap = {
                "published": "not-started",
                "pending": "in-progress",
                "completed": "done"
            };

        const projectStageNames = {
            "new": "Neu",
            "plan": "Planung",
            "process": "Prozess",
            "completed": "Abgeschlossen",
            "junk": "Junk",
            "pause": "Pausiert"
        };

        document.addEventListener("DOMContentLoaded", function () {
            loadProjectKanban();

            document.getElementById("searchButton").addEventListener("click", function () {
                let query = document.getElementById("searchInput").value.trim();
                if (query === "") {
                    loadProjectKanban();
                } else {
                    searchProjectKanban(query);
                }
            });

            document.getElementById("searchInput").addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    let query = this.value.trim();
                    if (query === "") {
                        loadProjectKanban();
                    } else {
                        searchProjectKanban(query);
                    }
                }
            });
        });

        document.getElementById("closeSidebarBtn").addEventListener("click", function () {
            closeSidebar();
        });


        function loadProjectKanban() {
            fetch('{{ route("project.get.list") }}')
                .then(res => res.json())
                .then(data => renderProjectKanban(data))
                .catch(err => console.error("Fehler beim Laden der Projekte:", err));
        }

    function loadProjectTasks(productId) {
        fetch(`/project/checklist/${productId}`)
            .then(res => {
                if (!res.ok) throw new Error("Keine Checkliste gefunden");
                return res.json();
            })
            .then(data => {
                console.log("Checklist API Result:", data);

                // ✅ Set selectedChecklistId from backend response
                if (data.project_montage_id) {
                    selectedChecklistId = data.project_montage_id;
                    console.log("✅ Set selectedChecklistId:", selectedChecklistId);
                } else {
                    selectedChecklistId = null;
                }

                if (!data.phases || !Array.isArray(data.phases)) {
                    console.warn("Phases not found or not an array.");
                    return;
                }

                renderTaskBoard(data.phases);
                renderTaskList(data.phases);
                renderTaskAccordion(data.phases);
            })
            .catch(err => {
                console.warn("Fehler beim Laden der Aufgaben:", err);

                Swal.fire({
                    title: "Keine Aufgaben gefunden",
                    text: "Für dieses Projekt wurden keine Aufgaben gefunden. Möchtest du eine neue Checkliste erstellen oder manuell Aufgaben hinzufügen?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Checkliste erstellen",
                    cancelButtonText: "Manuell hinzufügen"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/checklist/create";
                    } else {
                        renderEmptyTaskViews();
                    }
                });
            });
    }



        function renderEmptyTaskViews() {
            const boardView = document.getElementById("board-view");
            const listView = document.querySelector("#list-view .task-table tbody");
            const accordion = document.getElementById("accordion-tasks");

            boardView.innerHTML = `
                <div style="text-align:center; margin:20px;">
                    <p>Keine Aufgaben vorhanden.</p>
                    <button onclick="addMainTask()">+ Hauptaufgabe hinzufügen</button>
                    <button onclick="addSubTask()" >+ Unteraufgabe hinzufügen</button>
                    <button onclick="saveManualChecklist()">💾 Später speichern</button>
                </div>
            `;

            listView.innerHTML = `<tr><td colspan="5" style="text-align:center;">Keine Aufgaben</td></tr>`;
            accordion.innerHTML = `<h3>Tasks</h3><p>Keine Aufgaben vorhanden.</p>`;
        }



        function addMainTask() {
            Swal.fire({
                title: 'Neue Hauptaufgabe',
                input: 'text',
                inputLabel: 'Phasenname',
                inputPlaceholder: 'Gib den Namen der Phase ein',
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                preConfirm: (phaseName) => {
                    if (!phaseName) {
                        Swal.showValidationMessage('Phasenname ist erforderlich');
                    }
                    return phaseName;
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const phaseName = result.value;

                    // ✅ Use global vars from visitProfile
                    const productId = selectedProductId;
                    const sectionName = selectedSectionName;

                    if (!productId || !sectionName) {
                        Swal.fire("Fehler", "Produkt oder Sektion nicht gefunden", "error");
                        return;
                    }

                    // ✅ No need to fetch section first — handled in backend
                    fetch("{{ route('task.phase.store.new') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        },
                        body: JSON.stringify({
                            phase_name: phaseName,
                            product_id: productId,
                            section_name: sectionName
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Erfolg", "Phase gespeichert", "success");
                            loadProjectTasks(productId); // 🔄 Refresh phase/task view
                        } else {
                            Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen", "error");
                        }
                    })
                    .catch(err => {
                        console.error("Fehler beim Speichern der Phase:", err);
                        Swal.fire("Fehler", "Fehler beim Speichern der Phase", "error");
                    });
                }
            });
        }



        function addSubTask() {
                const selectedCard = document.querySelector(".card.selected");

                if (!selectedCard) {
                    Swal.fire("Fehler", "Kein Projekt ausgewählt", "error");
                    return;
                }

                const productId = selectedCard.getAttribute("data-product-id");
                const sectionName = selectedCard.getAttribute("data-service");

                if (!productId || !sectionName) {
                    Swal.fire("Fehler", "Produkt oder Service nicht gefunden", "error");
                    return;
                }

                fetch(`/get-phases/${productId}`)
                    .then(res => res.json())
                    .then(phases => {
                        if (!phases.length) {
                            Swal.fire("Hinweis", "Bitte zuerst eine Hauptaufgabe (Phase) erstellen.", "info");
                            return;
                        }

                        let options = phases.map(p => `<option value="${p.id}">${p.phase_name}</option>`).join('');

                        Swal.fire({
                            title: "Neue Aufgabe hinzufügen",
                            html: `
                                <select id="phase_id" class="swal2-input">${options}</select>
                                <input id="task_title" class="swal2-input" placeholder="Titel">
                                <textarea id="task_desc" class="swal2-textarea" placeholder="Beschreibung"></textarea>
                            `,
                            showCancelButton: true,
                            confirmButtonText: "Speichern",
                            preConfirm: () => {
                                return {
                                    phase_id: document.getElementById("phase_id").value,
                                    title: document.getElementById("task_title").value,
                                    description: document.getElementById("task_desc").value
                                };
                            }
                        }).then(result => {
                            if (result.isConfirmed) {
                                const formData = result.value;

                                fetch("{{ route('activities.store.new') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                                    },
                                    body: JSON.stringify({
                                        ...formData,
                                        product_id: productId,
                                        section_name: sectionName,
                                        status: "published"
                                    })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.fire("Gespeichert", "Aufgabe erfolgreich hinzugefügt", "success");
                                    loadProjectTasks(productId);
                                })
                                .catch(err => {
                                    console.error("Fehler:", err);
                                    Swal.fire("Fehler", "Aufgabe konnte nicht gespeichert werden", "error");
                                });
                            }
                        });
                    });
            }


            function saveManualChecklist() {
                Swal.fire("Gespeichert", "Manuelle Aufgaben wurden zwischengespeichert", "success");
            }




        
        
            function renderTaskBoard(phases) {
                if (!phases || !Array.isArray(phases)) return;

                const boardView = document.getElementById("board-view");
                boardView.innerHTML = "";

                const statuses = ["not-started", "in-progress", "done"];
                const statusLabels = {
                    "not-started": "Not started",
                    "in-progress": "In progress",
                    "done": "Done"
                };

            let container = document.createElement("div");
            container.style.display = "flex";
            container.style.gap = "20px";

            statuses.forEach(status => {
                let column = document.createElement("div");
                column.style.flex = "1";
                column.style.background = "#f8f8f8";
                column.style.borderRadius = "8px";
                column.style.padding = "10px";
                column.ondrop = (e) => dropTask(e, status);
                column.ondragover = (e) => e.preventDefault();
                column.innerHTML = `<strong class="badge ${status}">${statusLabels[status]}</strong>`;

                phases.forEach(phase => {
                    (phase.activities || []).forEach(task => {
                        const taskStatusRaw = task.status?.toLowerCase() || "published";
                        const taskStatus = statusMap[taskStatusRaw] || "not-started";

                        if (taskStatus === status) {
                            const taskDiv = document.createElement("div");
                            taskDiv.className = "task";
                            taskDiv.draggable = true;
                            taskDiv.textContent = task.title_activity;
                            taskDiv.ondragstart = (e) => {
                                e.dataTransfer.setData("task", JSON.stringify({
                                    title: task.title_activity,
                                    phase_id: phase.phase_id
                                }));
                            };
                            column.appendChild(taskDiv);
                        }
                    });
                });

                container.appendChild(column);
            });

            boardView.appendChild(container);
        }

    
    
        function renderTaskList(phases) {
            if (!phases || !Array.isArray(phases)) return; 
            const tbody = document.querySelector("#list-view .task-table tbody");
            tbody.innerHTML = "";

            phases.forEach((phase, phaseIndex) => {
                const phaseId = `phase-row-${phaseIndex}`;
                const totalTasks = phase.activities.length;
                const completedTasks = phase.activities.filter(task => task.status === "completed").length;
                const progress = totalTasks === 0 ? 0 : Math.round((completedTasks / totalTasks) * 100);
                const totalDuration = phase.activities.reduce((sum, task) => sum + parseFloat(task.duration || 0), 0);

                const trPhase = document.createElement("tr");
                trPhase.style.cursor = "pointer";
                trPhase.innerHTML = `
                    <td colspan="5" onclick="togglePhaseList('${phaseId}')">
                        <strong>📂 ${phase.phase_name}</strong> 
                        <small style="margin-left:10px;">(${totalDuration.toFixed(2)} Std)</small>
                        <div class="progress" style="height: 8px; width: 200px; margin-top: 5px;">
                            <div class="progress-bar bg-success" style="width: ${progress}%;">${progress}%</div>
                        </div>
                    </td>
                `;
                tbody.appendChild(trPhase);

                (phase.activities || []).forEach((task, taskIndex) => {
                    const taskStatusRaw = task.status?.toLowerCase() || "published";
                    const taskStatus = statusMap[taskStatusRaw] || "not-started";

                    const tr = document.createElement("tr");
                    tr.classList.add(phaseId);
                    tr.style.display = "none";

                    const ulId = `employee_list_${phaseIndex}_${taskIndex}`;

                    tr.innerHTML = `
                        <td>
                            ${task.title_activity}
                            <br><small style="color:#aaa;">📄 ${task.description || "Keine Beschreibung"}</small>
                        </td>
                        <td><span class="badge ${taskStatus}">${taskStatus.replace("-", " ")}</span></td>
                        <td>
                            <button 
                                type="button" 
                                class="btn btn-primary btn-sm waves-effect waves-light add-employees-btn" 
                                data-project-id="${selectedProjectId}"
                                data-phase-id="${phaseId}"
                                data-activity-id="${task.activity_id}"
                                >Mitarbeiter
                            </button>
                            <ul id="${ulId}" class="list-unstyled users-list m-0 d-flex align-items-center employee_list"></ul>
                        </td>
                        <td>🕒 ${task.duration || "0"} Std</td>
                        <td><span class="badge">${task.position_id ? "Medium" : "Low"}</span></td>
                    `;

                    tbody.appendChild(tr);

                    // 🔄 Load and populate employees for this project
                    fetch(`/project/employee/get/${selectedProjectId}`)
                        .then(res => res.json())
                        .then(employees => {
                            const ul = document.getElementById(ulId);
                            if (!ul) return;
                            ul.innerHTML = ""; // Clear in case

                            employees.forEach(emp => {
                                const li = document.createElement("li");
                                li.setAttribute("data-toggle", "tooltip");
                                li.setAttribute("data-popup", "tooltip-custom");
                                li.setAttribute("data-placement", "bottom");
                                li.setAttribute("data-original-title", `${emp.name} ${emp.lastname}`);
                                li.className = "avatar pull-up";
                                li.innerHTML = `
                                    <img class="media-object rounded-circle" src="${emp_src}/${emp.image}" alt="${emp.name}" height="30" width="30">
                                `;
                                ul.appendChild(li);
                            });
                        });
                });
            });
        }




        function dropTask(event, newStatus) {
            event.preventDefault();
            const taskData = JSON.parse(event.dataTransfer.getData("task"));
            
            fetch('/project/update-task-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    title: taskData.title,
                    phase_id: taskData.phase_id,
                    status: newStatus
                })
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadProjectTasks(data.product_id); // Refresh task board and list
                    renderTaskAccordion(data.phases);

                }
            }).catch(error => console.error("Fehler beim Aktualisieren:", error));
        }

        function renderTaskAccordion(phases) {
            const container = document.getElementById("accordion-tasks");
            container.innerHTML = "<h3>Tasks</h3>";

            if (!phases || !phases.length) {
                container.innerHTML += "<p>Keine Aufgaben vorhanden.</p>";
                return;
            }

            phases.forEach((phase, index) => {
                const phaseUniqueId = `phase-${index}`;
                const contentId = `phase-content-${phase.phase_id}`;

                const totalTasks = phase.activities.length;
                const completedTasks = phase.activities.filter(t => t.status === "completed").length;
                const percent = totalTasks === 0 ? 0 : Math.round((completedTasks / totalTasks) * 100);
                const totalDuration = phase.activities.reduce((sum, t) => {
                    return sum + parseFloat(t.duration || 0);
                }, 0);
                // Accordion Header
                const header = document.createElement("div");
                header.className = "accordion-header d-flex justify-content-between align-items-center";
                header.style.cursor = "pointer";
                header.style.fontWeight = "bold";
                header.style.marginTop = "10px";
                header.innerHTML = `
                    <div onclick="document.getElementById('${contentId}').style.display = 
                        document.getElementById('${contentId}').style.display === 'none' ? 'block' : 'none'">
                        📂 ${phase.phase_name} <small style="font-weight:normal;">(${totalTasks} Tasks · ⏱️ ${totalDuration.toFixed(1)} hrs)</small> 
                    </div>
                    <div class="progress" style="width: 200px; height: 10px;">
                        <div id="progress-${phaseUniqueId}" class="progress-bar bg-success" 
                            role="progressbar" style="width: ${percent}%;">${percent}%
                        </div>
                    </div>
                `;

                // Accordion Content
                const content = document.createElement("div");
                content.id = contentId;
                content.className = "accordion-content";
                content.style.display = "none";
                content.style.marginLeft = "15px";
                content.setAttribute("data-phase-id", phase.phase_id);

                // Tasks inside phase
                phase.activities.forEach(task => {
                    const taskStatusRaw = task.status?.toLowerCase() || "published";
                    const taskStatus = statusMap[taskStatusRaw] || "not-started";
                    const priority = task.position_id ? "Medium" : "Low";
                    const isChecked = task.status === "completed";

                    const taskEl = document.createElement("div");
                    taskEl.className = "task d-flex align-items-center justify-content-between";

                    const activityId = task.activity_id || task.id || null;

                    if (activityId) {
                        taskEl.setAttribute("data-activity-id", activityId);
                        console.log("✅ Binding activity:", task.title_activity, "| ID:", activityId);
                    } else {
                        console.warn("❌ Activity missing ID:", task.title_activity);
                    }

                    taskEl.setAttribute("data-customer-id", selectedCustomerId);
                    taskEl.setAttribute("data-product-id", selectedProductId);
                    taskEl.setAttribute("data-alternative-id", selectedAlternativeId);

                taskEl.innerHTML = `
                        <div style="flex-grow:1;">
                            <div>
                                <input type="checkbox" class="task-checkbox" data-phase="${phase.phase_id}" ${isChecked ? "checked" : ""}>
                                <strong>${task.title_activity}</strong> <small>(${task.duration || "0"} Std)</small>
                            </div>
                            <div style="margin-left:25px; color:#ccc; font-size: 0.9em;">
                                📄 ${task.description || "Keine Beschreibung"}
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <span class="badge ${taskStatus}">${taskStatus.replace("-", " ")}</span>
                            <span class="badge ${priority.toLowerCase()}">${priority}</span>
                        </div>
                        `;


                    content.appendChild(taskEl);
                });

                container.appendChild(header);
                container.appendChild(content);

                // Progress bar interaction
                setTimeout(() => {
                    const checkboxes = content.querySelectorAll(".task-checkbox");
                    checkboxes.forEach(cb => {
                        cb.addEventListener("change", () => {
                            const total = checkboxes.length;
                            const checked = Array.from(checkboxes).filter(c => c.checked).length;
                            updatePhaseProgress(phaseUniqueId, total, checked);
                        });
                    });
                }, 0);
            });
        }


        function togglePhaseList(phaseClass) {
            document.querySelectorAll(`.${phaseClass}`).forEach(row => {
                row.style.display = row.style.display === "none" ? "" : "none";
            });
        }


        function updatePhaseProgress(phaseId, total, checked) {
            const percent = total === 0 ? 0 : Math.round((checked / total) * 100);
            const progressBar = document.getElementById(`progress-${phaseId}`);
            if (progressBar) {
                progressBar.style.width = `${percent}%`;
                progressBar.textContent = `${percent}%`;
            }
        }




        function searchProjectKanban(query) {
            fetch(`/project/search/status?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => renderProjectKanban(data))
                .catch(err => console.error("Fehler bei der Suche:", err));
        }

        function allowDrop(event) {
            event.preventDefault();
        }


        function renderProjectKanban(data) {
            let kanbanBoard = document.getElementById("kanban");
            kanbanBoard.innerHTML = "";

            Object.keys(projectStageNames).forEach(stageKey => {
                let stageColumn = document.createElement("div");
                stageColumn.className = "column";
                stageColumn.id = stageKey;
                stageColumn.setAttribute("ondrop", "drop(event)");
                stageColumn.setAttribute("ondragover", "allowDrop(event)");
                stageColumn.innerHTML = `<h3>${projectStageNames[stageKey]}</h3><div class="column-content"></div>`;
                kanbanBoard.appendChild(stageColumn);
            });

            data.forEach(project => {
                let updatedDate = new Date(project.updated_at).toLocaleDateString("de-DE", {
                    day: "2-digit", month: "2-digit", year: "numeric"
                });

                let employee = project.employee && project.employee.employee_id
                    ? {
                        employee_id: project.employee.employee_id,
                        name: project.employee.name,
                        lastname: project.employee.lastname,
                        image: project.employee.image
                    }
                    : null;

                let stage = project.stage.toLowerCase();
                if (!projectStageNames[stage]) stage = "new";

                addCard(
                    stage,
                    project.initial,
                    `${project.customer_name} ${project.customer_lastname}`,
                    `Email: ${project.email}`,
                    `<i class="feather icon-calendar warning"></i> ${updatedDate}`,
                    `${project.street}, ${project.postcode}, ${project.city}`,
                    project.customer_id,
                    project.alternative_id,
                    project.product_id,
                    project.service,
                    employee,
                    project.project_id
                );
            });

            document.querySelectorAll(".column").forEach(col => {
                if (!col.querySelector(".card")) {
                    col.innerHTML += `<small>Keine Daten</small>`;
                }
            });
        }

        function addCard(columnId, product, customerName, customerDetails, date, address, customerId, alternativeId, productId, service, employee, projectId) {
            let column = document.getElementById(columnId);
            if (!column) return;

            let card = document.createElement("div");
            card.className = "card";
            card.id = "card-" + Math.random().toString(36).substr(2, 9);
            card.draggable = true;
            card.ondragstart = drag;
            card.onclick = (event) => selectCard(event, card);

            let employee_id = employee && employee.employee_id ? employee.employee_id : 0;
            card.setAttribute("data-customer-id", customerId);
            card.setAttribute("data-alternative-id", alternativeId);
            card.setAttribute("data-product-id", productId);
            card.setAttribute("data-service", service);
            card.setAttribute("data-employee-id", employee_id);
            card.setAttribute("data-lead-product-id", projectId);

            let employeeHtml = employee && employee.image
                ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
                        <li class="avatar pull-up" data-toggle="tooltip" title="${employee.name} ${employee.lastname}">
                            <img class="media-object rounded-circle" src="${emp_src}/${employee.image}" alt="${employee.name}" height="30" width="30">
                        </li>
                </ul>`
                : `<small>Kein Mitarbeiter zugewiesen</small>`;

            card.innerHTML = `
                <div class="card-header">
                    <strong>${customerName}</strong>
                    <div class='circle'>${product}</div>
                </div>
                <div>
                    <small>${customerDetails}</small><br>
                    <small>${date}</small><br>
                    <small>${address}</small>
                </div>
                <div class="employeeList">${employeeHtml}</div>
                <div class='card-actions'>
                <button class="profile" 
                    id="visitProfileButton"
                    onclick="visitProfile(this)"
                    data-project-id="${projectId}" 
                    data-customer-id="${customerId}" 
                    data-alternative-id="${alternativeId}" 
                    data-service="${service}" 
                    data-product-id="${productId}">
                    <i class="feather icon-eye"></i>
                </button>


                    <button onclick="editCard('${card.id}')"><i class="feather icon-edit"></i></button>
                    <button onclick="deleteCard('${card.id}')"><i class="feather icon-trash"></i></button>
                </div>
            `;

            column.querySelector(".column-content").appendChild(card);
        }

        function visitProfile(button) {
            // Mark clicked card as selected
            document.querySelectorAll(".card").forEach(c => c.classList.remove("selected"));
            const card = button.closest(".card");
            card.classList.add("selected");

            // ✅ Assign values FIRST
            const customerId = button.getAttribute("data-customer-id");
            const alternativeId = button.getAttribute("data-alternative-id");
            const productId = button.getAttribute("data-product-id");
            const projectId = button.getAttribute("data-project-id");
            const serviceName = button.getAttribute("data-service");

            selectedCustomerId = customerId;
            selectedAlternativeId = alternativeId;
            selectedProductId = productId;
            selectedProjectId = projectId;
            selectedService = serviceName;
            selectedSectionName = serviceName;

            const url = `/project/customer/${customerId}/${alternativeId}`;
            const checklistSelectWrapper = document.getElementById("checklistSelectWrapper");
            if (checklistSelectWrapper) checklistSelectWrapper.style.display = "none"; // Hide initially

            document.getElementById("project-profile").classList.add("active");
            document.querySelector(".project-profile-overlay").classList.add("active");

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("customer_name").textContent = `${data.name} ${data.lastname}`;
                    document.getElementById("customer_status").textContent = data.stage;
                    document.getElementById("customer_status").className = `badge ${data.stage}`;
                    document.getElementById("contact_person").innerHTML = `<strong>Contact Person:</strong> ${data.emp_name} ${data.emp_lastname}`;
                    document.getElementById("request_date").innerHTML = `<strong>Start Date:</strong> ${new Date(data.request_date).toLocaleDateString("de-DE")}`;

                    return fetch(`/customer/project/phase/get/${customerId}/${alternativeId}/${productId}`);
                })
                .then(res => {
                    if (!res.ok) throw new Error("No existing tasks");
                    return res.json();
                })
                .then(taskData => {
                    // ✅ Existing project_tasks found – HIDE checklist select
                    if (checklistSelectWrapper) checklistSelectWrapper.style.display = "none";

                    loadedFromChecklist = false;
                    checklistToSave = null;
                    selectedChecklistId = taskData.project_montage_id || null;

                    renderTaskBoard(taskData.phases);
                    renderTaskList(taskData.phases);
                    renderTaskAccordion(taskData.phases);
                })
                .catch(() => {
                    // ❌ No saved project_tasks – fallback to default checklist
                    if (checklistSelectWrapper) checklistSelectWrapper.style.display = "block";

                    loadedFromChecklist = true;
                    fetch(`/project/checklist/${productId}`)
                        .then(res => res.json())
                        .then(phaseData => {
                            if (phaseData.phases && Array.isArray(phaseData.phases)) {
                                checklistToSave = { ...phaseData, phases: phaseData.phases };
                                selectedChecklistId = phaseData.project_montage_id;

                                renderTaskBoard(phaseData.phases);
                                renderTaskList(phaseData.phases);
                                renderTaskAccordion(phaseData.phases);
                                loadAllChecklists(selectedProductId);

                                Swal.fire({
                                    title: "Hinweis",
                                    text: "Standard-Checkliste wurde geladen. Du kannst sie beim Verlassen speichern.",
                                    icon: "info",
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Diese Standard-Checkliste enthält keine Phasen.",
                                    html: `  <a href="/checklist/create" class="btn btn-sm btn-outline-primary mt-2">neue Checkliste zu erstellen</a>`,
                                    showConfirmButton: false
                                });

                            }
                        })
                        .catch(err => {
                            console.error("❌ Fehler beim Laden der Default-Checkliste:", err);
                            Swal.fire("Fehler", "Standard-Checkliste konnte nicht geladen werden.", "error");
                        });
                });
        }





        function closeSidebar() {
            if (loadedFromChecklist && checklistToSave) {
                Swal.fire({
                    title: "Speichern?",
                    text: "Möchtest du die Aufgaben als Projekt speichern?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Ja, speichern",
                    cancelButtonText: "Nein"
                }).then(result => {
                    if (result.isConfirmed) {
                        saveCurrentChecklistToProjectTasks(); // ⬅️ Next step
                    } else {
                        actuallyCloseSidebar();
                    }
                });
            } else {
                actuallyCloseSidebar();
            }
        }

        function saveCurrentChecklistToProjectTasks() {
            const checklistSelect = document.getElementById("checklistSelect");
            const checklistId = checklistSelect?.value || null;

            const selectedCard = document.querySelector(".card.selected");
            if (!selectedCard) {
                Swal.fire("Fehler", "Bitte wähle zuerst ein Projekt aus", "error");
                return;
            }

            const customerId = selectedCard.getAttribute("data-customer-id");
            const alternativeId = selectedCard.getAttribute("data-alternative-id");
            const productId = selectedCard.getAttribute("data-product-id");
            const projectId = selectedCard.getAttribute("data-project-id") || null;
            const service = selectedCard.getAttribute("data-service") || null;

            const missing = [];
            if (!checklistId) missing.push("Checkliste");
            if (!customerId) missing.push("Kunde");
            if (!alternativeId) missing.push("Alternative");
            if (!productId) missing.push("Produkt");

            if (missing.length > 0) {
                Swal.fire({
                    title: "Fehlende Angaben",
                    html: `Bitte wähle zuerst:<br><strong>${missing.join(", ")}</strong>`,
                    icon: "warning"
                });
                return;
            }

            const accordion = document.getElementById("accordion-tasks");
            const phaseSections = accordion.querySelectorAll(".accordion-content");

            const phases = [];

            phaseSections.forEach(section => {
                const phaseId = parseInt(section.getAttribute("data-phase-id"));
                const activities = [];

                section.querySelectorAll(".task").forEach(task => {
                    const checkbox = task.querySelector("input[type=checkbox]");
                    const activityId = task.getAttribute("data-activity-id");
                    const parsedId = parseInt(activityId);

                    if (!isNaN(parsedId)) {
                        activities.push({
                            id: parsedId,
                            done: checkbox?.checked ? "true" : "false"
                        });
                    }
                });

                if (activities.length > 0) {
                    phases.push({
                        phase_id: phaseId,
                        activities: activities
                    });
                }
            });

            if (phases.length === 0) {
                Swal.fire("Keine Aufgaben", "Bitte füge zuerst Aufgaben hinzu.", "info");
                return;
            }

        const payload = {
            checklist_id: parseInt(checklistId),
            customer_id: parseInt(customerId),
            alternative_id: parseInt(alternativeId),
            product_id: parseInt(productId),
            project_id: selectedProjectId ? parseInt(selectedProjectId) : null,
            service: selectedService || null,
            phases: phases
        };


            console.log("📦 Sending Payload to Server:", payload);

            fetch("/customer/project/phase/save", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", "Checkliste wurde gespeichert", "success");
                } else {
                    Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen", "error");
                }
            })
            .catch(async err => {
                console.error("❌ Fehler beim Speichern:", err);
                try {
                    const errorData = await err.json();
                    const html = Object.entries(errorData.errors || {}).map(
                        ([key, val]) => `<b>${key}:</b> ${val}`
                    ).join("<br>");
                    Swal.fire("Validierungsfehler", html || "Unbekannter Fehler", "error");
                } catch {
                    Swal.fire("Fehler", "Unbekannter Fehler beim Speichern", "error");
                }
            });
        }

    
        function actuallyCloseSidebar() {
            document.getElementById("project-profile").classList.remove("active", "fullscreen");
            document.querySelector(".project-profile-overlay").classList.remove("active");
        }


        function toggleMaximizeSidebar() {
            document.getElementById("project-profile").classList.toggle("fullscreen");
        }

        


        function saveProjectPhase() {
            const card = document.querySelector(".card.selected");
            if (!card) return;

            const productId = card.getAttribute("data-product-id");
            const customerId = card.getAttribute("data-customer-id");
            const alternativeId = card.getAttribute("data-alternative-id");

            fetch("{{ route('customer.project.phase.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", "Phase wurde gespeichert", "success");
                } else {
                    Swal.fire("Fehler", data.message, "error");
                }
            })
            .catch(err => {
                console.error("Fehler beim Speichern:", err);
                Swal.fire("Fehler", "Speichern fehlgeschlagen", "error");
            });
        }





        function selectCard(event, card) {
            if (event.ctrlKey || event.metaKey) {
                card.classList.toggle("selected");
            } else {
                document.querySelectorAll(".card.selected").forEach(c => c.classList.remove("selected"));
                card.classList.add("selected");
            }
        }

        function drag(event) {
            event.dataTransfer.setData("text", event.target.id);
        }

    
        function drop(event) {
            event.preventDefault();
            let cardId = event.dataTransfer.getData("text");
            let card = document.getElementById(cardId);
            let column = event.target.closest(".column");

            if (card && column) {
                const newStatus = column.id;
                const projectId = card.getAttribute("data-lead-product-id");

                // Move the card visually
                column.querySelector(".column-content").appendChild(card);
                card.classList.remove("selected");

                // Send new status to backend
                fetch("{{ route('project.change.status') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({
                        project_id: projectId,
                        new_status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        Swal.fire("Fehler", data.message || "Status konnte nicht geändert werden.", "error");
                    }
                })
                .catch(err => {
                    console.error("Fehler beim Aktualisieren des Status:", err);
                    Swal.fire("Fehler", "Beim Ändern des Status ist ein Fehler aufgetreten.", "error");
                });
            }
        }


    

        function editCard(cardId) {
            let card = document.getElementById(cardId);
            if (!card) return;
            let customerId = card.getAttribute("data-customer-id");
            let alternativeId = card.getAttribute("data-alternative-id");
            window.location.href = `/new_lead_edit/${customerId}/${alternativeId}`;
        }

        function deleteCard(cardId) {
            let card = document.getElementById(cardId);
            if (!card) return;
            let projectId = card.getAttribute("data-lead-product-id");

            Swal.fire({
                title: "Bist du sicher?",
                text: "Projekt wird dauerhaft gelöscht.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ja, löschen",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/delete_lead_product/${projectId}`;
                }
            });
        }



        function loadChecklistSelect(productId) {
                if (!productId) return;

                fetch(`/checklist/all/${productId}`)
                    .then(res => {
                        if (!res.ok) throw new Error("Checklisten konnten nicht geladen werden.");
                        return res.json();
                    })
                    .then(data => {
                        const select = document.getElementById("checklistSelect");
                        if (!select) return;

                        select.innerHTML = `<option value="">Bitte wählen</option>`;

                        data.forEach(item => {
                            const option = document.createElement("option");
                            option.value = item.id;
                            option.textContent = item.list_name;

                            // Auto-select the default checklist
                            if (item.default_stage === "yes") {
                                option.selected = true;
                                fetch(`/checklist/by-id/${item.id}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.phases && Array.isArray(data.phases)) {
                                            renderTaskBoard(data.phases);
                                            renderTaskList(data.phases);
                                            renderTaskAccordion(data.phases);
                                        }
                                    });
                            }

                            select.appendChild(option);
                        });


                        bindChecklistChangeEvent(); // ✅ bind after populating

                    })
                    .catch(err => {
                        console.error("Fehler beim Laden der Checklisten:", err);
                    });
            }


            document.addEventListener("DOMContentLoaded", function () {
                const checklistSelect = document.getElementById("checklistSelect");
                if (checklistSelect) {
                    checklistSelect.addEventListener("change", function () {
                        const checklistId = this.value;
                        if (checklistId) {
                            fetch(`/checklist/by-id/${checklistId}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.phases && Array.isArray(data.phases)) {
                                        renderTaskBoard(data.phases);
                                        renderTaskList(data.phases);
                                        renderTaskAccordion(data.phases);
                                    } else {
                                        renderEmptyTaskViews();
                                    }
                                })
                                .catch(err => {
                                    console.error("Fehler beim Laden der ausgewählten Checkliste:", err);
                                    renderEmptyTaskViews();
                                });
                        } else {
                            // If empty, fallback to default checklist
                            loadProjectTasks(selectedProductId);
                        }
                    });
                }
            });





        // Global function to bind checklist change listener
        function bindChecklistChangeEvent() {
            const checklistSelect = document.getElementById("checklistSelect");
            if (checklistSelect) {
                checklistSelect.addEventListener("change", function () {
                    const checklistId = this.value;

                    if (checklistId) {
                        fetch(`/checklist/by-id/${checklistId}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.phases && Array.isArray(data.phases)) {
                                    renderTaskBoard(data.phases);
                                    renderTaskList(data.phases);
                                    renderTaskAccordion(data.phases);
                                } else {
                                    renderEmptyTaskViews();
                                }
                            })
                            .catch(err => {
                                console.error("Fehler beim Laden der ausgewählten Checkliste:", err);
                                renderEmptyTaskViews();
                            });
                    } else {
                        // fallback to default
                        loadProjectTasks(selectedProductId);
                    }
                });
            }
        }

        function saveCurrentChecklistToProjectTasks() {
            const checklistSelect = document.getElementById("checklistSelect");
            const checklistId = checklistSelect?.value || null;

            const customerId = selectedCustomerId || null;
            const alternativeId = selectedAlternativeId || null;
            const productId = selectedProductId || null;

            const missing = [];
            if (!checklistId) missing.push("Checkliste");
            if (!customerId) missing.push("Kunde");
            if (!alternativeId) missing.push("Alternative");
            if (!productId) missing.push("Produkt");

            if (missing.length > 0) {
                Swal.fire({
                    title: "Fehlende Angaben",
                    html: `Bitte wähle zuerst:<br><strong>${missing.join(", ")}</strong>`,
                    icon: "warning"
                });
                return;
            }

            const accordion = document.getElementById("accordion-tasks");
            const phaseSections = accordion.querySelectorAll(".accordion-content");

            const phases = [];

            phaseSections.forEach(section => {
                const phaseMatch = section.id.match(/phase-content-(\d+)/);
                if (!phaseMatch) return;

                const phaseId = parseInt(phaseMatch[1]);
                const activities = [];

                section.querySelectorAll(".task").forEach(task => {
                    const checkbox = task.querySelector("input[type=checkbox]");
                    const activityId = task.getAttribute("data-activity-id");
                    const parsedId = parseInt(activityId);

                    if (!isNaN(parsedId)) {
                        activities.push({
                            id: parsedId,
                            done: checkbox?.checked ? "true" : "false"
                        });
                    }
                });



                if (activities.length > 0) {
                    phases.push({
                        phase_id: phaseId,
                        activities: activities
                    });
                }
            });

            if (phases.length === 0) {
                Swal.fire("Keine Aufgaben", "Bitte füge zuerst Aufgaben hinzu.", "info");
                return;
            }

            const payload = {
                checklist_id: parseInt(checklistId),
                customer_id: parseInt(customerId),
                alternative_id: parseInt(alternativeId),
                product_id: parseInt(productId),
                project_id: selectedProjectId ? parseInt(selectedProjectId) : null,
                service: selectedService || null,
                phases: phases
            };


            console.log("📦 Sending Payload to Server:", payload);

            fetch("/customer/project/phase/save", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire("Gespeichert", "Checkliste wurde gespeichert", "success");
                } else {
                    Swal.fire("Fehler", data.message || "Speichern fehlgeschlagen", "error");
                }
            })
            .catch(async err => {
                console.error("❌ Fehler beim Speichern:", err);
                try {
                    const errorData = await err.json();
                    const html = Object.entries(errorData.errors || {}).map(
                        ([key, val]) => `<b>${key}:</b> ${val}`
                    ).join("<br>");
                    Swal.fire("Validierungsfehler", html || "Unbekannter Fehler", "error");
                } catch {
                    Swal.fire("Fehler", "Unbekannter Fehler beim Speichern", "error");
                }
            });
        }




        function loadAllChecklists(productId) {
            if (!productId) return;

            fetch(`/checklist/all/${productId}`)
                .then(res => {
                    if (!res.ok) throw new Error("Checklisten konnten nicht geladen werden.");
                    return res.json();
                })
                .then(data => {
                    const select = document.getElementById("checklistSelect");
                    if (!select) return;

                    // Clear existing options
                    select.innerHTML = `<option value="">Bitte wählen</option>`;

                    // Populate new options
                    data.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.textContent = item.list_name;
                        select.appendChild(option);
                    });

                    // Optional: bind onChange event here
                    select.addEventListener("change", function () {
                        const checklistId = this.value;
                        if (checklistId) {
                            fetch(`/checklist/by-id/${checklistId}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.phases && Array.isArray(data.phases)) {
                                        renderTaskBoard(data.phases);
                                        renderTaskList(data.phases);
                                        renderTaskAccordion(data.phases);
                                    } else {
                                        renderEmptyTaskViews();
                                    }
                                })
                                .catch(err => {
                                    console.error("Fehler beim Laden der Checkliste:", err);
                                    renderEmptyTaskViews();
                                });
                        } else {
                            renderEmptyTaskViews(); // If user resets to blank
                        }
                    });
                })
                .catch(err => {
                    console.error("❌ Fehler beim Laden der Checklisten:", err);
                    Swal.fire("Fehler", "Checklisten konnten nicht geladen werden.", "error");
                });
        }

    

        document.getElementById("add_employe_form").addEventListener("submit", function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: "✅ Erfolgreich",
                    text: data.message,
                    icon: "success"
                });

                // Close modal and reset form
                $("#employee").modal("hide");
                form.reset();
            })
            .catch(async (err) => {
                try {
                    const errorData = await err.json();
                    const html = Object.entries(errorData.errors || {}).map(
                        ([key, val]) => `<b>${key}:</b> ${val.join(', ')}`
                    ).join("<br>");
                    Swal.fire("Fehler", html, "error");
                } catch {
                    Swal.fire("Fehler", "Unbekannter Fehler beim Hinzufügen", "error");
                }
            });
        });

 
</script>


<script>
$(document).on('click', '.add-employees-btn', function () {
    const projectId = $(this).data('project-id');
    
    // Set the project ID inside the modal
    $('#modal_project_id').val(projectId);

    // Optional: reset old employee or set it if needed
    $('#modal_old_employee').val('');

    // Show the modal
    $('#employee').modal('show');
});


</script>

 

 
 
@endsection


