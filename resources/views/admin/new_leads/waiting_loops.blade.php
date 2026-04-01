@extends('admin.layouts.app')

@section('title') PLANUNG @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
 <style>
    .timeline-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #fff;
    }

    .timeline-info {
        margin-left: 60px;
    }

    .timeline-info p {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }

    .timeline-info span {
        display: block;
        font-size: 14px;
        color: #6c757d;
    }

    small {
        margin-left: 60px;
        color: #999;
    }
</style>




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
                        <h2 class="content-header-title float-left mb-0">WARTESCHLEIFE LEADS</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="content-body">
                <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section -->  
                                        <!-- Search Section -->
                                        <div class="row mb-1 " style="    flex-direction: row-reverse;" > 
                                                <div class="col-4 float-right">
                                                    <form action="{{ action('App\Http\Controllers\NewLeadsController@waiting_leads') }}">
                                                        <fieldset>
                                                            <div class="input-group">
                                                                <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                                <div class="input-group-append" id="button-addon2">
                                                                    <button class="btn btn-primary" type="submit">Go</button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div> 
                                     
                                        </div>
 
                                        <!-- Contents Details of Customer -->
                                        <div class="row">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                         <tr style="background:white; "> 
                                                            <th style="width: 45px;" >ID</th> 
                                                            <th  class="bolders ">DATUM</th> 
                                                            <th  class="bolders ">NAME</th> 
                                                            <th  class="bolders ">KONTAKT</th> 
                                                            <th  class="bolders ">GEWERKE</th> 
                                                            <th style="width:20px !important" >STATUS</th>
                                                            <th>VERFASSER</th> 
                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)    
                                                            <tr style="background:white;border-bottom: 13px solid #f8f8f8;" class="mb-2"> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                
                                                                <td>
                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }} <br>
                                                                    <code> <strong> 
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}                                   
                                                                    </strong></code>  
                                                                </td>
                                                                <td>
                                                                    <a href="{{url('new_lead_profile/'.$item->lead_id )}}">
                                                                        {{ $item->name }}  {{ $item->lastname }}   
                                                                    </a>
                                                                    <p>
                                                                          <small class="m-0">
                                                                            <i class="feather icon-map-pin"></i> {{ $item->street }} {{ $item->postcode }}, {{ $item->city }}
                                                                        </small>
                                                                    </p>
                                                                </td>
                                                                    
                                                                <td>
                                                                    <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                </td> 
                                                                
                                                              <td>
                                                                <div class="employee-service-container">
                                                                    @php
                                                                        $servicesMap = [
                                                                            'complete' => 'Komplettlösung',
                                                                            'montage' => 'Montage',
                                                                            'product' => 'Produkt',
                                                                            'plan' => 'Planung',
                                                                            'maintenance' => 'Wartung',
                                                                            'repair' => 'Reparatur',
                                                                            'others' => 'Sonstiges',
                                                                        ];

                                                                        $matchedService = null;

                                                                        foreach ($service as $serv) {
                                                                            if ($serv->alternative_id == $item->alternative_id && $serv->customer_id == $item->lead_id) {
                                                                                $matchedService = $servicesMap[$serv->service] ?? $serv->service;
                                                                                break; // Stop loop once a match is found
                                                                            }
                                                                        }

                                                                        // Determine the default image based on gender
                                                                       $defaultImage = $item->emp_gender === "Male"  
                                                                            ? asset('images/gender/male.png') 
                                                                            : asset('images/gender/female.png');

                                                                        // Determine the actual image to use
                                                                        $employeeImage = file_exists('images/employee/' . $item->emp_image) && $item->emp_image 
                                                                            ? asset('images/employee/' . $item->emp_image) 
                                                                            : $defaultImage;
                                                                    @endphp 

                                                                    <div class="d-flex flex-column align-items-center">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="circle">{{ $item->initial }}</div>
                                                                            <div class="line"></div> 
                                                                            <div class="image" 
                                                                                data-toggle="tooltip" 
                                                                                data-original-title="{{ $item->emp_name && $item->emp_lastname ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                                                                                <img src="{{ $employeeImage }}" alt="{{ $item->status }}"  
                                                                                    data-employee-id="{{ $item->employee_id ?? '' }}" 
                                                                                    data-product-id="{{ $item->product_id }}" 
                                                                                    data-new-lead-id="{{ $item->lead_id }}" 
                                                                                    data-alternative-id="{{ $item->alternative_id }}" 
                                                                                    data-id="{{ $item->id }}"  
                                                                                    class="{{ $item->status == 'reject' ? 'profile-r' : 'profile-s' }} add_employees" >
                                                                            </div> 
                                                                        </div>
                                                                        <div class="text">{{ $matchedService ?? 'Kein Service zugewiesen' }}</div>
                                                                    </div>
                                                                </div>
                                                            </td>

                                                                
                                                                <td>
                                                                    @if($item->status=="reject")
                                                                    <div class="badge badge-danger"> Anfrage abgelehnt</div>
                                                                     @else
                                                                    <div class="badge badge-warning">Warten</div> 
                                                                    @endif
                                                                </td>
                                                               @php
                                                                    $employee = DB::table('employees')
                                                                        ->where('id', $item->contact_person)
                                                                        ->select('name', 'lastname', 'image')
                                                                        ->first();

                                                                    $c_image = $employee->image ?? 'default_image.png'; // Default image path
                                                                    $c_name = $employee->name ?? 'Unknown';
                                                                    $c_lastname = $employee->lastname ?? '';
                                                                @endphp
   
                                                                <td style="width:20px">
                                                                    <div class="image" >
                                                                        <div class="avatar mr-1 " >
                                                                            <img src="{{ asset('images/employee/'.$c_image )}}" alt="avtar img holder" height="32" width="32" data-toggle="tooltip" data-placement="top" title data-original-tiitle="{{ $c_name }} {{ $c_lastname}}">
                                                                        </div>
                                                                        <div class="text">
                                                                            <span class="font-weight-bold"></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            
                                                                <td>  
                                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                                        <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                            <i class="feather icon-menu "></i>
                                                                        </button>
                                                                        <div class="dropdown-menu">  
                                                                            @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first()) 
                                                                                <span class="dropdown-item">
                                                                                    <a  class="black history_modal" data-lead-id="{{$item->lead_id }}" data-responsible-id="{{$item->id}}" ><i class="feather icon-fast-forward black" ></i> Historie</a>
                                                                                </span> 
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
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div> 

        <!-- Notification History -->
         <!-- Modal for Notification Timeline -->
            <div class="modal fade" id="notificationTimelineModal" tabindex="-1" role="dialog" aria-labelledby="timelineModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="timelineModalLabel">Benachrichtigungshistorie</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <ul id="timelineContainer" class="activity-timeline timeline-left list-unstyled">
                                            <!-- Timeline items will be dynamically injected here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Notification History -->

<!-- END: Content-->
@endsection
 
@section('script')  
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
 <!-- Notification History: start  -->
 <script>
    $(document).ready(function() {
        $('.history_modal').on('click', function (e) {
            e.preventDefault();

            const leadId = $(this).data('lead-id');
            const responsibleId = $(this).data('responsible-id');
            console.log('Lead ID:', leadId, 'Responsible ID:', responsibleId);

            const modal = $('#notificationTimelineModal');
            const timelineContainer = $('#timelineContainer');

            // Clear the timeline container
            timelineContainer.empty();

            // Show the modal
            modal.modal('show');

            // Fetch notifications using AJAX
            $.ajax({
                url: `/notifications/timeline/${leadId}/${responsibleId}?t=${new Date().getTime()}`, // Cache-busting
                method: 'GET',
                success: function (response) {
                    if (response.status === 'success') {
                        const notifications = response.notifications;

                        if (notifications.length > 0) {
                            notifications.forEach(notification => {
                                const title = notification.data.title;
                                const message = notification.data.message;
                                const performedAt = new Date(notification.data.performed_at).toLocaleString();

                                const timelineItem = `
                                    <li>
                                        <div class="timeline-icon bg-primary">
                                            <i class="feather icon-check font-medium-2"></i>
                                        </div>
                                        <div class="timeline-info">
                                            <p class="font-weight-bold">${title}</p>
                                            <span>${message}</span>
                                        </div>
                                        <small>${performedAt}</small>
                                    </li>
                                `;
                                timelineContainer.append(timelineItem);
                            });
                        } else {
                            timelineContainer.html('<p>Keine Benachrichtigungen gefunden.</p>');
                        }
                    }
                },
                error: function (err) {
                    console.error('Error fetching notifications:', err);
                    timelineContainer.html('<p>Ein Fehler ist aufgetreten.</p>');
                }
            });
        });
    });
</script>


 <!-- Notification History: end  -->
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

 

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
 
 <script src="{{ asset('js/select2.min.js') }}"></script>  

 <script>
   $(document).ready(function () {
    let newLeadId = null;
    let productId = null;
    let alternative = null;

    // Open the modal and load employees
    $('[data-target="#addEmployee"]').on('click', function () {
        const employeeId = $(this).data('employee-id'); // Current employee ID
        newLeadId = $(this).data('new-lead-id'); // Lead ID
        productId = $(this).data('product-id'); // Product ID
        alternative = $(this).data('alternative-id'); // Product ID
        id = $(this).data('id'); // Product ID

        // Populate hidden inputs in the modal
        $('#modalEmployeeId').val(employeeId);
        $('#modalProductId').val(productId);
        $('#modalLeadId').val(newLeadId);
        $('#modalAlternativeId').val(alternative);
        $('#modalId').val(id);

        // Fetch available employees via AJAX
        $.ajax({
            url: '/checkEmployeeAvailability',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function (response) {
                // Clear previous options
                $('#employeeSelect').empty();

                // Populate select2 with available employees or fallback
                const employees = response.availableEmployees.length > 0 
                    ? response.availableEmployees 
                    : response.inCaseEmployees;

                if (employees.length > 0) {
                    employees.forEach(employee => {
                        $('#employeeSelect').append(new Option(
                            `${employee.name} ${employee.lastname}`,
                            employee.id
                        ));
                    });
                } else {
                    toastr.warning('No employees found for this product.');
                }

                // Initialize or refresh select2
                $('#employeeSelect').select2();
            },
            error: function (xhr) {
                toastr.error('Failed to fetch employees. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });
});

 </script>


<script>
$(document).on('click', '.add_employees', function () {
    const productId = $(this).data('product-id');
    const leadId = $(this).data('new-lead-id');
    const altId = $(this).data('alternative-id');

    $.post('/getEmployees', {_token: '{{ csrf_token() }}'}, function (employees) {
        let html = '<select id="employeeSelect" class="swal2-select">';
        html += `<option value="">-- Kein Mitarbeiter --</option>`; // Add null option

        employees.forEach(emp => {
            html += `<option value="${emp.id}">${emp.name} ${emp.lastname}</option>`;
        });
        html += '</select>';

        Swal.fire({
            title: 'Mitarbeiter zuweisen',
            html: html,
            showCancelButton: true,
            confirmButtonText: 'Zuweisen',
            preConfirm: () => {
                return $('#employeeSelect').val(); // This can be empty string
            }
        }).then(result => {
            if (result.isConfirmed) {
                const employeeId = result.value; // can be null (empty string)

                $.ajax({
                    url: '/update-lead-employee',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: employeeId,
                        product_id: productId,
                        alternative_id: altId,
                        customer_id: leadId
                    },
                    success: function (res) {
                        Swal.fire('Erfolgreich zugewiesen!', '', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire('Fehler beim Speichern', '', 'error');
                    }
                });
            }
        });
    });
});
</script>

@endsection
