@extends('admin.layouts.app')
@section('title')
PERSONAL AUFGABEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
 <style>
 
  #calendar {
    width: 100%;
    min-height: 600px;
        background: #f4f4f4;
}

/* Make Calendar Cells Responsive */
.fc .fc-daygrid-day {
    min-width: auto;
    height: auto !important;
    max-width: 100%;
}

/* Adjust the Grid and Cell Sizing */
.fc-scrollgrid {
    width: 100%;
}

.fc .fc-daygrid-day-frame {
    display: flex !important;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    height: auto !important;
    padding: 10px !important;
}

/* Ensure Events Fit in Cells */
.fc-daygrid-event-harness {
    width: 100% !important;
    margin: 5px 0 !important;
}

.fc-daygrid-day-events {
    width: 100% !important;
}

/* Custom Event Design */
.fc-event-custom {
    padding: 8px !important;
    background: white;
    border-left: 4px solid #007bff;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    text-align: left;
    transition: all 0.3s ease;
    height: 118px;
}

/* Responsive Adjustments for Mobile Screens */
@media (max-width: 768px) {
    #calendar {
        min-height: 400px;
    }

    .fc-daygrid-day {
        min-width: 100px !important;
    }

    .fc-event-custom {
        padding: 6px;
        font-size: 12px;
    }

    .custom-event-header {
        font-size: 12px !important;
    }

    .custom-event-time {
        font-size: 11px !important;
    }

    .fc .fc-daygrid-day-frame {
        padding: 5px !important;
    }
}

@media (max-width: 576px) {
    #calendar {
        min-height: 300px;
    }

    .fc-daygrid-day {
        min-width: 80px !important;
    }

    .fc-event-custom {
        padding: 4px;
        font-size: 10px;
    }

    .custom-event-header {
        font-size: 10px;
    }

    .custom-event-time {
        font-size: 9px;
    }

    .fc .fc-daygrid-day-frame {
        padding: 3px !important;
    }
}

/* Simplified Event for Mobile View */
.fc-event-mobile {
    font-size: 14px;
    font-weight: bold;
    color: white;
    background-color: #007bff;
    border-radius: 4px;
    padding: 5px;
    text-align: center;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Adjust padding for mobile cells */
@media (max-width: 768px) {
    .fc-daygrid-day-frame {
        padding: 5px !important;
    }
}
.time {
    color: #545454;
    font-size: 10px;
}

.accept_request {
    border: 3px solid #8fc73e !important;
}

.reject_request {
    border: 3px solid #ea5555 !important;
}

.send_request {
    border: 3px solid rgb(222, 158, 47) !important;
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
 
<!-- End::app-content -->

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">PROJEKT-AUFGABEN</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">   
                <section>
                <div class="row">
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        @php
                                           
                                            $data = DB::table('projects')->select('project_status')->get();
                                            $new_count = $data->where('project_status', 'new')->count();
                                            $complete = $data->where('project_status', 'complete')->count();
                                            $pending = $data->where('project_status', 'pending')->count();
                                            $progress = $data->where('project_status', 'on_going')->count();
                                        @endphp
                                        <h2 class="text-bold-700 mb-0">{{$new_count}}</h2>
                                        <p>Gesamt Aufgaben</p>
                                    </div>
                                    <div class="avatar bg-rgba-primary p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-cpu text-primary font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{ $complete }}</h2>
                                        <p>Abgeschlossene</p>
                                    </div>
                                    <div class="avatar bg-rgba-success p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-server text-success font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                 
                        <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{$progress}}</h2>
                                        <p>In Bearbeitung</p>
                                    </div>
                                    <div class="avatar bg-rgba-warning p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-alert-octagon text-warning font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                               <div class="col-lg-3 col-sm-6 col-12">
                            <div class="card">
                                <div class="card-header d-flex align-items-start pb-0">
                                    <div>
                                        <h2 class="text-bold-700 mb-0">{{$pending}}</h2>
                                        <p>Ausstehend</p>
                                    </div>
                                    <div class="avatar bg-rgba-danger p-50 m-0">
                                        <div class="avatar-content">
                                            <i class="feather icon-activity text-danger font-medium-5"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>

                    <section>
                    <div class="content-body">
                        <!-- account setting page start -->
                        <section id="page-account-settings">
                            <div class="row">
                                <!-- left menu section -->
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <ul class="nav nav-pills flex-column mt-md-0 mt-1"> 
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                                                <i class="feather icon-globe mr-50 font-medium-3"></i>
                                                NEUE
                                            </a>
                                        </li>
                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-task" data-toggle="pill" href="#account-vertical-task" aria-expanded="true">
                                                <i class="feather icon-globe mr-50 font-medium-3"></i>
                                              IN BEARBEITEN
                                            </a>
                                        </li>
                                       
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                ABGESCHLOSSEN
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                PAUSIERTE 
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                STONIERETE 
                                            </a>
                                        </li>
 
                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                                                <i class="feather icon-calendar mr-50 font-medium-3"></i>
                                                 KALENDAR
                                            </a>
                                        </li>
                                          
                                    </ul>
                                </div>
                                <!-- right content section -->
                                <div class="col-md-10">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                                            <section>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header">
                                                                                <h4 class="mb-0"> Kunde in der Projektphase</h4> 
                                                                                <div class="details d-flex">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <form id="search-task-form" method="GET">
                                                                                                <div class="d-flex"> 
                                                                                                    <div class="input-group-prepend">
                                                                                                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                                                                            <i class="feather icon-search"></i>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <input type="text" class="form-control" placeholder="Suchen" aria-label="search" name="search" id="search-input"> 
                                                                                                </div>
                                                                                            </form>
                                                                                            <div class="input-group-append">
                                                                                             
                                                                                            </div> 
                                                                                           
                                                                                        </div>
                                                                                    </fieldset> 
                                                                                </div>
                                                                            </div>

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
                                                                                               
                                                                                                <th>Kunden#</th>
                                                                                                <th>Kunde</th> 
                                                                                                <th>Objekt-name</th> 
                                                                                                <th>Verantwortlicher</th>   
                                                                                                <th>Beteiligte Personen</th>   
                                                                                                <th>Projektstatus</th>  
                                                                                                <th>Aktion</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody> 
                                                                                            @foreach ($customers as $cus )
                                                                                                <tr>
                                                                                                    <td> 
                                                                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                            <input type="checkbox" value="false">
                                                                                                            <span class="vs-checkbox vs-checkbox-sm">
                                                                                                                <span class="vs-checkbox--check">
                                                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                                                </span>
                                                                                                            </span> 
                                                                                                        </div> 
                                                                                                    </td>
                                                                                                    <th scope="row">{{ $cus->customer_no }}</th>
                                                                                                    <td>
                                                                                                        <a href="{{url('new_lead_profile/'.$cus->customer_id)}}">
                                                                                                            <p  style="font-weight:bold">{{$cus->name}} {{$cus->lastname}}</p>
                                                                                                            <p>{{$cus->street}} {{$cus->postcode}}, {{$cus->city}}</p>
                                                                                                        </a>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        {{ $cus->object_name }}
                                                                                                    </td>
                                                                                                     <td>
                                                                                                        <div style="justify-items: center;display: flex;align-items: center;justify-content: flex-start;flex-wrap: nowrap;"> 
                                    
                                                                                                            @php
                                                                                                                $services = [
                                                                                                                    'complete' => 'Komplettlösung',
                                                                                                                    'montage' => 'Montage',
                                                                                                                    'product' => 'Produkt',
                                                                                                                    'plan' => 'Planung',
                                                                                                                    'maintenance' => 'Wartung',
                                                                                                                    'repair' => 'Reparatur',
                                                                                                                    'others' => 'Sonstiges',
                                                                                                                ]; 
                                                                                                                $service = $services[$cus->service] ?? $cus->service;  
                                                                                                            @endphp
                                                                                                
                                
                                                                                                                @php
                                                                                                                        // Determine the default image based on gender
                                                                                                                        $defaultImage = $cus->gender === "Male" 
                                                                                                                            ? asset('images/gender/male.png') 
                                                                                                                            : asset('images/gender/female.png');

                                                                                                                        // Determine the actual image to use
                                                                                                                        $employeeImage = file_exists('images/employee/'.$cus->emp_image) && $cus->emp_image 
                                                                                                                            ? asset('images/employee/'.$cus->emp_image) 
                                                                                                                            : $defaultImage;
                                                                                                                    @endphp 

                                                                                                                    <div class="d-flex flex-column align-items-center mr-1">
                                                                                                                        <div class="d-flex align-items-center">
                                                                                                                            <div class="circle">{{ $cus->initial }}</div>
                                                                                                                            <div class="line"></div> 
                                                                                                                            <div class="image" data-toggle="tooltip" 
                                                                                                                                data-original-title="{{ $cus->emp_name && $cus->emp_lastname ? $cus->emp_name . ' ' . $cus->emp_lastname : 'Nicht zugewiesen' }}">
                                                                                                                                <img src="{{ $employeeImage }}" alt="Profile"  
                                                                                                                                
                                                                                                                                class="profile">
                                                                                                                            </div> 
                                                                                                                        </div>
                                                                                                                    <div class="text">{{ $service }}</div>
                                                                                                                </div>
                                                                                                    </td> 
                                                                                                    <td>
                                                                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                                                                            @foreach ($project_employees as $p_emp) 
                                                                                                                @php  

                                                                                                                    $member_list = [
                                                                                                                        'member'    =>  'Mitglied',
                                                                                                                        'guest'     =>  'Gast',
                                                                                                                        'comentator'    =>  'Kommentator{in}'
                                                                                                                    ];
                                                                                                                    $member = $member_list[$p_emp->member_type] ?? 'Mitglied unbekannt';


                                                                                                                    $genderIcon = $p_emp->gender === 'Male' 
                                                                                                                        ? asset('images/gender/male.png') 
                                                                                                                        : asset('images/gender/female.png');

                                                                                                                    $profileImage = !empty($p_emp->image) 
                                                                                                                        ? asset('images/employee/' . $p_emp->image) 
                                                                                                                        : $genderIcon;

                                                                                                                  
                                                                                                                @endphp
                                                                                                                @if($p_emp->project_id == $cus->id)
                                                                                                                    <div class="change_employee" data-project="{{$cus->id}}" data-employee="{{$p_emp->employee_id}}" data-toggle="modal" data-target="#employee_change"> 
                                                                                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" 
                                                                                                                            data-original-title="{{ $p_emp->name }} {{ $p_emp->lastname}} ({{$member}})" 
                                                                                                                            class="avatar pull-up">
                                                                                                                            <img class="media-object rounded-circle @if($p_emp->status=='send') send_request @elseif($p_emp->status=='accept') accept_request @else reject_request @endif" src="{{ $profileImage }}" 
                                                                                                                                alt="Avatar" height="30" width="30">
                                                                                                                                @if($p_emp->status=='send')
                                                                                                                                <span class="avatar-status-away"></span>
                                                                                                                                @elseif($p_emp->status=='accept')
                                                                                                                                <span class="avatar-status-offline"></span>
                                                                                                                                @else
                                                                                                                                <span class="avatar-status-busy" style="width: 13px;height: 13px;">x</span>
                                                                                                                                @endif
                                                                                                                        </li>
                                                                                                                    </div>
                                                                                                                    @if($p_emp->employee_id == auth()->user()->name)  
                                                                                                                        @if($p_emp->status=='send')
                                                                                                                        <button type="button" class="btn btn-outline-warning square mr-1 mb-1 waves-effect waves-light btn-sm" id="accept_button" data-project="{{$p_emp->project_id}}" data-employee="{{$p_emp->employee_id}}">Antwort</button>
                                                                                                                       @else
                                                                                                                        <button type="button" 
                                                                                                                                class="btn btn-outline-warning square mr-1 mb-1 waves-effect waves-light btn-sm change_employee" 
                                                                                                                                data-project="{{$p_emp->project_id}}" 
                                                                                                                                data-employee="{{$p_emp->employee_id}}">
                                                                                                                            Change
                                                                                                                        </button>

                                                                                                                       @endif
                                                                                                                        
                                                                                                                    @endif
                                                                                                                    
                                                                                                                @endif
                                                                                                            @endforeach 
                                                                                                        </ul>
                                                                                                    </td>
                                                                                                 
                                                                                                    <td> 
                                                                                                        <div class="prograss">
                                                                                                            <div class="main-grid-cell-inner" style="justify-self: center !important;">
                                                                                                                <span class="main-grid-cell-content" data-prevent-default="false" >
                                                                                                                
                                                                                                                        <table class="crm-list-stage-bar-table">
                                                                                                                            <tbody>
                                                                                                                                <tr>  
                                                                                                                                    @php
                                                                                                                                        // Step 1: Initialize an array to store colors for each unique customer-product-phase if done is true
                                                                                                                                        $phaseColors = [];
                                                                                                                                    @endphp

                                                                                                                                    @foreach ($phases as $phase)
                                                                                                                                        @if ($phase->done == 'true')
                                                                                                                                            @php
                                                                                                                                                // Store the color specifically for each unique customer-product-phase combination
                                                                                                                                                $phaseColors[$phase->customer][$phase->product][$phase->phase_name] = $phase->color;
                                                                                                                                            @endphp
                                                                                                                                        @endif
                                                                                                                                    @endforeach

                                                                                                                                    <!-- Step 2: Display each phase, applying color only if done is true for that specific customer-product-phase -->
                                                                                                                                    @if (!empty($phases) && count($phases) > 0)
                                                                                                                                    @php $hasMatchingPhase = false; @endphp
                                                                                                                                    @foreach ($phases as $phase)
                                                                                                                                        @if ($phase->customer == $cus->customer_id && $phase->service == $cus->service)
                                                                                                                                            @php $hasMatchingPhase = true; @endphp
                                                                                                                                            <td class="crm-list-stage-bar-part"
                                                                                                                                                style="background: {{ $phaseColors[$phase->customer][$phase->product][$phase->phase_name] ?? '#FFFFFF' }}; padding:10px; border: 1px solid #afafaf;"
                                                                                                                                                data-toggle="tooltip" data-placement="top" 
                                                                                                                                                title="{{ $phase->phase_name }}">
                                                                                                                                                <span style="color:gray">{{ $phase->phase_name }}</span>
                                                                                                                                            </td>
                                                                                                                                        @endif
                                                                                                                                    @endforeach

                                                                                                                                    @if (!$hasMatchingPhase)
                                                                                                                                        <td class="crm-list-stage-bar-part" style="padding:10px; border: 1px solid #afafaf; text-align: center;">
                                                                                                                                            <span style="color: red;">No matching phases found for this customer and service</span>
                                                                                                                                        </td>
                                                                                                                                    @endif
                                                                                                                                    @else
                                                                                                                                        <td class="crm-list-stage-bar-part" style="padding:10px; border: 1px solid #afafaf; text-align: center;">
                                                                                                                                            <span style="color: red;">Not defined</span>
                                                                                                                                        </td>
                                                                                                                                    @endif 
                                                                                                                                </tr>  
                                                                                                                            </tbody>
                                                                                                                        </table>  
                                                                                                                        @foreach ($tasks as $task)
                                                                                                                            @if($task->customer_id == $cus->lead_id && $task->product_id == $cus->product_id) 
                                                                                                                                    <a href="{{ url('customer_product_details/'.$cus->id.'/'.$cus->product_id.'/'.$cus->alternative_id) }}#project-management"> {{ $task->task_title }} </a>
                                                                                                                            @endif
                                                                                                                        @endforeach 
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </td> 
                                                                                                    <td>
                                                                                                        <div class="btn-group">
                                                                                                            <div class="dropdown">
                                                                                                                <button class="btn btn-flat-primary dropdown-toggle mr-1 mb-1 waves-effect waves-light" type="button" id="dropdownMenuButton100" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                                    <i class="feather icon-menu"></i>
                                                                                                                </button>
                                                                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton100">
                                                                                                                    <a class="dropdown-item" href="{{ url('customer_project_management/'.$cus->id.'/'.$cus->customer_id.'/'.$cus->product_id.'/'.$cus->alternative_id)}}">Project-management</a>
                                                                                                                    <a class="dropdown-item add_employee" data-project="{{$cus->id}}" data-toggle="modal" data-target="#employee"> Mitarbeiter zur Aufgabe hinzufügen </a>                                                                                                                    <a class="dropdown-item" href="#">Projektpause</a>
                                                                                                                    <a class="dropdown-item" href="#">Junk-Projekt</a>
                                                                                                                    <form method="post" action="{{ route('customer.phase.manage.edit') }}">
                                                                                                                        @csrf
                                                                                                                        <input type="hidden" name="alternative_id" value="">
                                                                                                                        <input type="hidden" name="customer" value="">
                                                                                                                        <input type="hidden" name="product" value="">
                                                                                                                        <input type="hidden" name="service" value="">
                                                                                                                    <button class="dropdown-item" type="submit"  >Phase-Management</button>
                                                                                                                    </form>
                                                                                                                </div>
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
                                                            </section> 
                                                    </div>
                                          
                                                    <div role="tabpanel" class="tab-pane" id="account-vertical-task" aria-labelledby="account-pill-task" aria-expanded="false">
                                                            <section>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header">
                                                                                <h4 class="mb-0">Zugewiesene Aufgaben</h4>
                                                                                <div class="details d-flex">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <div class="input-group-prepend">
                                                                                                <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                                                                            </div>
                                                                                            <input type="text" class="form-control" placeholder="Suchen" aria-label="search" name="search">
                                                                                             
                                                                                        </div>
                                                                                    </fieldset> 
                                                                                </div>
                                                                            </div>
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
                                                                                                <th>Aufgabe</th>
                                                                                                <th>Aufgaben-ID</th> 
                                                                                                <th>Zugewiesen am</th> 
                                                                                                <th>Status</th> 
                                                                                                <th>Fälligkeitsdatum</th> 
                                                                                                <th>Priorität</th > 
                                                                                                <th>Zugewiesen an</th> 
                                                                                                <th>Aktion</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section> 
                                                    </div>
                                          
                                                    <div class="tab-pane fade" id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                                                         <div class="calender-section">
                                                                <div id='calendar'></div>
                                                        </div> 
                                                    </div>
                                                    <div class="tab-pane fade" id="account-vertical-info" role="tabpanel" aria-labelledby="account-pill-info" aria-expanded="false">
                                                        <section>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header">
                                                                                <h4 class="mb-0">Zugewiesene Aufgaben</h4>
                                                                                <div class="details d-flex">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <div class="input-group-prepend">
                                                                                                <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                                                                            </div>
                                                                                            <input type="text" class="form-control" placeholder="Suchen" aria-label="search" name="search">
                                                                                       
                                                                                        </div>
                                                                                    </fieldset> 
                                                                                </div>
                                                                            </div>
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
                                                                                                <th>Aufgabe</th>
                                                                                                <th>Aufgaben-ID</th> 
                                                                                                <th>Zugewiesen am</th> 
                                                                                                <th>Status</th> 
                                                                                                <th>Fälligkeitsdatum</th> 
                                                                                                <th>Priorität</th > 
                                                                                                <th>Zugewiesen an</th> 
                                                                                                <th>Aktion</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody> 
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section> 
                                                    </div>
                                                
                                                     <div class="tab-pane fade" id="account-vertical-job" role="tabpanel" aria-labelledby="account-pill-job" aria-expanded="false">
                                                        <section>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header">
                                                                                <h4 class="mb-0">Von Ihnen zugewiesene Aufträge</h4>
                                                                                <div class="details d-flex">
                                                                                    <fieldset>
                                                                                        <div class="input-group">
                                                                                            <div class="input-group-prepend">
                                                                                                <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                                                                            </div>
                                                                                            <input type="text" class="form-control" placeholder="Suchen" aria-label="search" name="search">
                                                                                            <div class="input-group-append">
                                                                                                   <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#new_record">
                                                                                                        Erstellen
                                                                                                    </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset> 
                                                                                </div>
                                                                            </div>
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
                                                                                                <th>Zugewiesen am</th> 
                                                                                                <th>Status</th> 
                                                                                                <th>Fälligkeitsdatum</th> 
                                                                                                <th>Priorität</th > 
                                                                                                <th>Zugewiesen von</th> 
                                                                                                <th>Zugewiesen an</th> 
                                                                                                <th>Aktion</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody> 
                                                                                        </tbody>
                                                                                    </table>
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
                                </div>
                            </div>
                        </section>
                        <!-- account setting page end -->

                    </div>
                    </section>

                        
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
                                    <div class="modal-body">
                                        <label for="employee_id">Mitarbeiter auswählen</label>
                                        <select name="employee_id[]" id="employee_id" class="form-control employee" style="width: 100%;" multiple="true">
                                            @foreach ($employees as $emp)
                                                <option value="{{$emp->id}}" 
                                                        data-image="{{asset('images/employee/'.$emp->image)}}">
                                                    {{$emp->name}} {{$emp->lastname}}
                                                </option>
                                            @endforeach
                                        </select>

                                        <label for="employee_roll">Mitarbeiterfunktion</label>
                                        <select name="employee_roll" id="employee_id" class="form-control employee" style="width: 100%;" >
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



                                                                                                                    
            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>

<script>
$(document).ready(function () {
    // Function to fetch data from the server
    function fetchTasks(searchTerm = '') {
        $.ajax({
            url: "{{ route('personal.task.search') }}",
            method: 'GET',
            data: { search: searchTerm },
            beforeSend: function () {
                $('#search-results').html('<p>Loading...</p>');
            },
            success: function (response) {
                $('#search-results').html(response);  // Load tasks
            },
            error: function (xhr) {
                $('#search-results').html('<p class="text-danger">Error loading tasks. Please try again.</p>');
                console.error(xhr.responseText);  // Log error
            }
        });
    }

    // Load all tasks on page load
    fetchTasks();

    // Search form submission
    $('#search-task-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Get search term and fetch tasks
        let searchTerm = $('#search-input').val();
        fetchTasks(searchTerm);  // Fetch filtered results
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
    $(document).ready(function() {
    var i = 0;

    // Add Task
    $(document).on('click', '.add-task', function() {
        i++;
        $('#key_task tbody').append(
            '<tr>' +
                '<td><input type="text" name="key['+i+'][task]" class="form-control" placeholder=""></td>' +
                '<td><button type="button" class="btn btn-icon btn-danger remove-task"><i class="fa fa-trash"></i></button></td>' +
            '</tr>'
        );
    });

    // Remove Task
    $(document).on('click', '.remove-task', function() {
        $(this).closest('tr').remove();
    });
});

</script>

<script>
    $(document).ready(function() {
    var j = 0;

    // Add Sub Task
    $(document).on('click', '.add-sub-task', function() {
        j++;
        $('#sub_task tbody').append(
            '<tr>' +
                '<td><input type="text" name="sub_task['+j+'][sub]" class="form-control" placeholder=""></td>' +
                '<td><input type="text" name="sub_task['+j+'][description]" class="form-control" placeholder=""></td>' +
                '<td><button type="button" class="btn btn-icon btn-danger remove-sub-task"><i class="fa fa-trash"></i></button></td>' +
            '</tr>'
        );
    });

    // Remove Sub Task
    $(document).on('click', '.remove-sub-task', function() {
        $(this).closest('tr').remove();
    });
});

</script>

<!-- Color: start  -->
 <script>
    $(document).ready(function() {
    $('#color-select').select2({
        templateResult: formatColor,
        templateSelection: formatColor,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    function formatColor(color) {
        if (!color.id) {
            return color.text;
        }

        var colorValue = $(color.element).data('color');
        var colorName = color.text;

        var markup = `
            <div style="display: flex; align-items: center;">
                <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                <span>${colorName}</span>
            </div>
        `;

        return markup;
    }
});

 </script>


<!-- Calender of employee  -->
<script>
 document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");
    const calendarTab = document.getElementById("account-pill-password");
    let calendarInstance;

    // Function to initialize the calendar
    function initializeCalendar() {
        if (!calendarEl) {
            console.error("Calendar element not found");
            return;
        }

        if (calendarInstance) {
            calendarInstance.destroy(); // Destroy existing instance before reinitializing
        }

        const isMobile = window.innerWidth < 768; // Check if mobile view

        calendarInstance = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? "listWeek" : "dayGridMonth",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: isMobile ? "" : "dayGridMonth,timeGridWeek,timeGridDay",
            },
            locale: "en",
            events: function (fetchInfo, successCallback, failureCallback) {
                fetch('/get_personal_task_calendar')
                    .then(response => response.json())
                    .then(data => {
                        const events = data.tasks.map(task => {
                            // Calculate event width based on days
                            const startDate = new Date(`${task.start_date}T${task.start_time}`);
                            const endDate = task.end_date
                                ? new Date(new Date(task.end_date).getTime() + 86400000) // Add 1 day to make it inclusive
                                : startDate;

                            const daysSpan = Math.max(1, Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)));

                            return {
                                id: task.id,
                                title: task.title,
                                start: `${task.start_date}T${task.start_time}`,
                                end: `${task.end_date}T${task.end_time}`,
                                backgroundColor: task.backgroundColor || "#007bff",
                                extendedProps: {
                                    employees: task.employees || [],
                                    daysSpan: daysSpan, // Store the span of days
                                },
                            };
                        });
                        successCallback(events);
                    })
                    .catch(error => {
                        console.error("Error fetching events:", error);
                        failureCallback(error);
                    });
            },
            eventContent: function (info) {
                const { event } = info;

                if (isMobile) {
                    // For mobile, show a simplified event with just the title
                    return {
                        html: `<div class="fc-event-mobile">${event.title}</div>`,
                    };
                }

                // For desktop, show the full HTML design
                const employees = event.extendedProps.employees || [];
                const employeeAvatars = employees.map(emp => {
                    const imageUrl = emp.image
                        ? `{{ asset('images/employee/${emp.image}') }}`
                        : (emp.gender === "Male"
                            ? `{{ asset('images/gender/male.png') }}`
                            : `{{ asset('images/gender/female.png') }}`);

                    return `
                        <li class="avatar pull-up" title="${emp.name} ${emp.lastname}">
                            <img class="media-object rounded-circle" 
                                 src="${imageUrl}" 
                                 alt="${emp.name}" height="25" width="25">
                        </li>
                    `;
                }).join('');

                // Dynamically adjust width based on the span of days
                const daysSpan = event.extendedProps.daysSpan || 1;
                const eventWidth = daysSpan * 100; // Adjust 100px per day (customize as needed)

                return {
                    html: `
                        <div class="fc-event-custom" style="width: ${eventWidth}px; border-left: 4px solid ${event.backgroundColor};">
                            <a href="/personal_task_details/${event.id}">
                                <div class="custom-event-header d-flex justify-content-between">
                                    <span class="custom-event-status">${event.title}</span>
                                    <i class="feather icon-more-vertical menu"></i>
                                </div>
                            </a>
                            <div class="custom-event-product-status">
                                <ul class="list-unstyled users-list d-flex align-items-center">
                                    ${employeeAvatars}
                                </ul>
                            </div>
                            <div class="date d-flex">
                                <div class="custom-event-time d-flex flex-wrap justify-content-start"> 
                                    <div class="time">
                                        <div>  
                                            <p class="m-0"><i class="feather icon-clock"></i>  ${new Date(event.start).toLocaleTimeString()}</p>
                                            <p><i class="feather icon-clock danger"></i> ${event.end ? new Date(event.end).toLocaleTimeString() : ""}</p>
                                        </div>  
                                    </div>
                                </div> 
                            </div>
                        </div>
                    `
                };
            },
        });

        calendarInstance.render();
    }

    // Add listener for the tab click to reinitialize the calendar
    calendarTab?.addEventListener("click", () => {
        setTimeout(() => {
            initializeCalendar();
        }, 300);
    });

    // Initialize calendar if the tab is already active
    if (calendarTab?.classList.contains("active")) {
        initializeCalendar();
    }

    // Reinitialize calendar on window resize
    let resizeTimeout;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimeout); // Prevent rapid reinitialization
        resizeTimeout = setTimeout(() => {
            initializeCalendar();
        }, 300);
    });

    // Initialize the calendar on page load
    initializeCalendar();
});
</script> 

 


<!-- Saving New Task Script: Start  -->
 <script>
   $(document).ready(function() {
    $('.save-task').on('click', function(e) {
        e.preventDefault();

        let form = $('#task-store-form');
        let formData = form.serialize();
        
        // Use the route directly if form action is removed
        let actionUrl = '{{ route('personal.task.store') }}';

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('.save-task').prop('disabled', true).text('Speichern...');
            },
            success: function(response) {
                $('.save-task').prop('disabled', false).text('Speichern');
                form.trigger('reset');
                $('#create').modal('hide');
                toastr.success('Aufgabe erfolgreich gespeichert!');
                location.reload();
            },
            error: function(xhr) {
                $('.save-task').prop('disabled', false).text('Speichern');

                let errors = xhr.responseJSON.errors;
                let errorMessages = '';

                if (errors) {
                    $.each(errors, function(key, value) {
                        errorMessages += value + '\n';
                    });
                    toastr.error('Fehler beim Speichern:\n' + errorMessages);
                } else {
                    toastr.error('Es ist ein unerwarteter Fehler aufgetreten.');
                }
            }
        });
    });
});

 </script>
<!-- Saving New Task Script: end  -->

 

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




@endpush