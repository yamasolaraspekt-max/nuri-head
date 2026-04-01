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



@endsection
@section('content')
 
<!-- End::app-content -->

    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ALLGEMEINE AUFGABEN</h2>
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
                                           
                                            $data = DB::table('personal_tasks')->select('task_status')->get();
                                            $new_count = $data->where('task_status', 'new')->count();
                                            $complete = $data->where('task_status', 'complete')->count();
                                            $pending = $data->where('task_status', 'pending')->count();
                                            $progress = $data->where('task_status', 'on_going')->count();
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
                                        <p>Warteschleife Aufgaben</p>
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
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75 active" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                                                <i class="feather icon-star mr-50 font-medium-3"></i>
                                                 NEUE 
                                            </a>
                                        </li>
                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-task" data-toggle="pill" href="#account-vertical-task" aria-expanded="true">
                                                <i class="feather icon-globe mr-50 font-medium-3"></i>
                                              IN BEARBEITUNG
                                            </a>
                                        </li>
                                       
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                ÜBERFÄLLIGE 
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-job" data-toggle="pill" href="#account-vertical-job" aria-expanded="false">
                                                <i class="feather icon-user mr-50 font-medium-3"></i>
                                                ERSTELLTE AUFGABEN
                                            </a>
                                        </li>

                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                PAUSIERTE AUFGABEN 
                                            </a>
                                        </li>

                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                                <i class="feather icon-info mr-50 font-medium-3"></i>
                                                STONIERETE 
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-job" data-toggle="pill" href="#account-vertical-job" aria-expanded="false">
                                                <i class="feather icon-check mr-50 font-medium-3"></i>
                                                ABGESCHLOSSEN
                                            </a>
                                        </li>


                                         <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                                                <i class="feather icon-calendar mr-50 font-medium-3"></i>
                                                MEIN KALENDAR
                                            </a>
                                        </li>
                                          
                                    </ul>
                                </div>
                                <!-- right content section -->
                                <div class="col-md-9">
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
                                                                                <h4 class="mb-0">Persönliche Aufgabe</h4> 
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
                                                                                                <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#create">
                                                                                                    Erstellen
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset> 
                                                                                </div>
                                                                            </div>
                                                                            <div id="search-results" class="card-content mt-2"></div> 
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
                                                                                            <div class="input-group-append">
                                                                                                   <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#create">
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
                                                                                            @foreach ($created_task as $item) 
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
                                                                                                                            
                                                                                                                            <img class="media-object rounded-circle @if($t_emp->status == 'send') send @elseif($t_emp->status== 'accept') accept @else reject @endif" 
                                                                                                                                src="{{ $profile_image }}" 
                                                                                                                                alt="{{ $t_emp->name }} {{ $t_emp->lastname }}" height="30" width="30">
                                                                                                                        </li>
                                                                                                                       
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
                                                                                            <div class="input-group-append">
                                                                                                   <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#create">
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
                                                                                            @foreach ($open_task as $item) 
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
                                                                                                                            
                                                                                                                            <img class="media-object rounded-circle @if($t_emp->status == 'send') send @elseif($t_emp->status== 'accept') accept @else reject @endif" 
                                                                                                                                src="{{ $profile_image }}" 
                                                                                                                                alt="{{ $t_emp->name }} {{ $t_emp->lastname }}" height="30" width="30">
                                                                                                                        </li>
                                                                                                                        
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
                                                                                                   <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#create">
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
                                                                                            @foreach ($byYouTask as $item) 
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
                                                                                                            
                                                                                                                  @php
                                                                                                                    // Fetch Assigned Employee Data
                                                                                                                    $assignedEmp = DB::table('employees')
                                                                                                                        ->where('id', $item->assigned_by)
                                                                                                                        ->select('name', 'lastname', 'image', 'gender')
                                                                                                                        ->first();

                                                                                                                    if ($assignedEmp) {
                                                                                                                        $assignedName = $assignedEmp->name . ' ' . $assignedEmp->lastname;

                                                                                                                        // Determine Gender Default Image
                                                                                                                        $genderIcon = $assignedEmp->gender === 'Male' 
                                                                                                                            ? asset('images/gender/male.png') 
                                                                                                                            : asset('images/gender/female.png');

                                                                                                                        // Fallback Image Check
                                                                                                                        $profileImage = !empty($assignedEmp->image) 
                                                                                                                            ? asset('images/employee/' . $assignedEmp->image) 
                                                                                                                            : $genderIcon;
                                                                                                                    } else {
                                                                                                                        // Default Values for Null Employee
                                                                                                                        $assignedName = 'Unknown Employee';
                                                                                                                        $profileImage = asset('images/gender/default.png'); // Default fallback image
                                                                                                                    }
                                                                                                                @endphp

                                                                                                                <div>
                                                                                                                    <li 
                                                                                                                        data-toggle="tooltip" 
                                                                                                                        data-popup="tooltip-custom" 
                                                                                                                        data-placement="bottom" 
                                                                                                                        data-original-title="{{ $assignedName }}" 
                                                                                                                        class="avatar pull-up">
                                                                                                                        <img 
                                                                                                                            class="media-object rounded-circle accept" 
                                                                                                                            src="{{ $profileImage }}" 
                                                                                                                            alt="{{ $assignedName }}" 
                                                                                                                            height="30" 
                                                                                                                            width="30">
                                                                                                                    </li>
                                                                                                                </div> 
                                                                                                        </ul>
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
                                                                                                                            
                                                                                                                            <img class="media-object rounded-circle @if($t_emp->status == 'send') send @elseif($t_emp->status== 'accept') accept @else reject @endif" 
                                                                                                                                src="{{ $profile_image }}" 
                                                                                                                                alt="{{ $t_emp->name }} {{ $t_emp->lastname }}" height="30" width="30">
                                                                                                                        </li>
                                                                                                                        
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

                                <!-- Correctly Opened Form Tag -->
                                <form action="{{ route('personal.task.accept') }}" method="post" id="accept-request-form">
                                    @csrf

                                    <div class="modal-body">
                                        <p><i class="feather icon-info warning"></i> Sie wurden als Verantwortlicher für den folgenden Kunden ausgewählt</p>
                                        <div class="row">
                                            <input type="hidden" name="task_id" value="">
                                            <input type="hidden" name="employee_id" value="">

                                            <!-- Response Selection -->
                                            <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                <fieldset class="form-group">
                                                    <label for="response">Antwort anfordern</label>
                                                    <select name="response" class="form-control" required>
                                                        <option value="accept">Akzeptieren</option>
                                                        <option value="reject">Ablehnen</option>
                                                    </select>
                                                </fieldset>
                                            </div>

                                            <!-- Reason Field -->
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


                <!-- Add Employee Modal  -->
                   <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-hidden="false">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary white">
                                    <h5 class="modal-title">Stellenanfrage</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="add-employee-modal">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" name="task_id" id="task_id" value="">
                                        <label for="employee_id">Neuen Mitarbeiter zur Aufgabe hinzufügen</label>
                                        <select name="employee_id" class="form-control employee" required>
                                            @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="save-add-emp" class="btn btn-primary">Speichern</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- accept button modal -->
        
                        
                    <!-- Create Modal -->
                    <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-hidden="false">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel17">Aufgaben Erstellen</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <form  id="task-store-form">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <label for="priority">Priorität</label>
                                                    <select name="priority" class="form-control">
                                                        <option value="high">Hoch</option>
                                                        <option value="medium">Mittel</option>
                                                        <option value="low">Niedrig</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <label for="color">Farbe</label>
                                                    <select name="color" id="color-select" class="form-control">
                                                        <option value="#FF0000" data-color="#FF0000">Rot</option>
                                                            <option value="#0000FF" data-color="#0000FF">Blau</option>
                                                            <option value="#008000" data-color="#008000">Grün</option>
                                                            <option value="#FFFF00" data-color="#FFFF00">Gelb</option>
                                                            <option value="#FFA500" data-color="#FFA500">Orange</option>
                                                            <option value="#800080" data-color="#800080">Lila</option>
                                                            <option value="#FFC0CB" data-color="#FFC0CB">Pink</option>
                                                            <option value="#A52A2A" data-color="#A52A2A">Braun</option>
                                                            <option value="#808080" data-color="#808080">Grau</option>
                                                            <option value="#FFFFFF" data-color="#FFFFFF">Weiß</option>
                                                            <option value="#000000" data-color="#000000">Schwarz</option>
                                                            <option value="#00FFFF" data-color="#00FFFF">Cyan</option>
                                                            <option value="#FF00FF" data-color="#FF00FF">Magenta</option>
                                                            <option value="#ADD8E6" data-color="#ADD8E6">Hellblau</option>
                                                            <option value="#00008B" data-color="#00008B">Dunkelblau</option>
                                                            <option value="#90EE90" data-color="#90EE90">Hellgrün</option>
                                                            <option value="#006400" data-color="#006400">Dunkelgrün</option>
                                                            <option value="#F5F5DC" data-color="#F5F5DC">Beige</option>
                                                            <option value="#C0C0C0" data-color="#C0C0C0">Silber</option>
                                                            <option value="#FFD700" data-color="#FFD700">Gold</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-12 col-12">
                                                    <label for="task_title">Aufgabentitel</label>
                                                    <input type="text" id="task_title" class="form-control" name="task_title">
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <label for="start_date">Startdatum</label>
                                                    <input type="date" id="start_date" class="form-control" name="start_date">
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <label for="end_date">Enddatum</label>
                                                    <input type="date" id="end_date" class="form-control" name="end_date">
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <label for="start_time">Startzeit</label>
                                                    <input type="time" id="start_time" class="form-control" name="start_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}">
                                                </div>

                                                <div class="col-md-6 col-12">
                                                    <label for="end_time">Endzeit</label>
                                                    <input type="time" id="end_time" class="form-control" name="end_time">
                                                </div>

                                                <div class="col-md-12 col-12">
                                                    <label for="employee">Zugewiesen an</label>
                                                    <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                        @foreach ($employees as $emp)
                                                        <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}" @if($emp->id == auth()->user()->name) selected @endif>{{ $emp->name }} {{ $emp->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-12 col-12">
                                                    <label for="description">Beschreibung</label>
                                                    <textarea name="description" class="form-control" rows="5"></textarea>
                                                </div>

                                                <div class="col-md-6 col-12">  
                                                    <div class="table-responsive">
                                                        <table class="table" id="key_task"> 
                                                            <thead>
                                                                <tr>
                                                                    <th>Aufgabenschritte</th>
                                                                    <th>Aktion</th> 
                                                                </tr>
                                                            </thead>
                                                            <tbody> 
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" name="key[0][task]" value="" class="form-control">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-icon btn-primary add-task">
                                                                                <i class="fa fa-plus"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr> 
 
                                                            </tbody>
                                                        </table>
                                                    </div> 
                                                </div>

                                                <!-- Sub Task Table -->
                                                <div class="col-md-6 col-12">  
                                                <div class="table-responsive">
                                                    <table class="table" id="sub_task"> 
                                                        <thead>
                                                            <tr>
                                                                <th>Link-titel</th>
                                                                <th>Beschreibung</th>
                                                                <th>Aktion</th> 
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                          
                                                              
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" name="sub_task[0][sub]" value="" class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="sub_task[0][description]" value="" class="form-control">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-icon btn-primary add-sub-task">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr> 
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                        <button type="button" class="btn btn-primary save-task">Speichern</button>
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


<script>
$(document).on('submit', 'form', function(e) {
    e.preventDefault();

    // Disable the submit button to prevent double submissions
    let $form = $(this);
    let $submitButton = $form.find('button[type="submit"]');
    $submitButton.prop('disabled', true).text('Submitting...');

    $.ajax({
        url: "{{ route('personal.task.store') }}",
        method: "POST",
        data: $form.serialize(),
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || "Task created successfully!");
                setTimeout(function() {
                    window.location.href = "{{ url()->previous() }}";  // Redirect back after 2 seconds
                }, 2000);
            } else {
                toastr.error(response.message || "Error saving task.");
                // Re-enable the submit button on failure
                $submitButton.prop('disabled', false).text('Submit');
            }
        },
        error: function(xhr) {
            if (xhr.status === 422) {  // Validation error from Laravel
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, messages) {
                    $.each(messages, function(index, message) {
                        toastr.error(message);
                    });
                });
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                toastr.error(xhr.responseJSON.error);  // Show custom error message
            } else {
                toastr.error("Something went wrong! Please try again.");
                console.error(xhr.responseText);  // Log error details
            }
            // Re-enable the submit button on error
            $submitButton.prop('disabled', false).text('Submit');
        }
    });
});
</script>


<!-- Saving New Task Script: Start  -->
  <script>
   $(document).ready(function () {
       $('.save-task').on('click', function (e) {
           e.preventDefault();

           let form = $('#task-store-form');
           let formData = form.serialize();

           // Basic validations
           let startDate = $('#start_date').val();
           let endDate = $('#end_date').val();
           let endTime = $('#end_time').val();
           let employee = $('#employee').val();
           let keyTaskRows = $('#key_task tbody tr');

           let errors = [];

           // Validate key_task
           if (keyTaskRows.length === 0 || !keyTaskRows.find('input[name^="key"]').val()) {
               errors.push('Bitte geben Sie mindestens einen Aufgabenschritt ein!');
           }

           // Validate start_date and end_date
           if (!startDate) {
               errors.push('Das Startdatum darf nicht leer sein.');
           }
          
           if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
               errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
           }

           

           // Validate employee
           if (!employee || employee.length === 0) {
               errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
           }

           // Show errors using SweetAlert if any
           if (errors.length > 0) {
               Swal.fire({
                   icon: 'error',
                   title: 'Validierungsfehler',
                   html: `<ul style="text-align: left;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`,
               });
               return;
           }

           // AJAX request
           let actionUrl = '{{ route('personal.task.store') }}';

           $.ajax({
               url: actionUrl,
               type: 'POST',
               data: formData,
               beforeSend: function () {
                   $('.save-task').prop('disabled', true).text('Speichern...');
               },
               success: function (response) {
                   $('.save-task').prop('disabled', false).text('Speichern');
                   form.trigger('reset');
                   $('#create').modal('hide');
                   Swal.fire({
                       icon: 'success',
                       title: 'Erfolg',
                       text: 'Aufgabe erfolgreich gespeichert!',
                   }).then(() => {
                       location.reload();
                   });
               },
               error: function (xhr) {
                   $('.save-task').prop('disabled', false).text('Speichern');

                   let serverErrors = xhr.responseJSON.errors;
                   let errorMessages = '';

                   if (serverErrors) {
                       $.each(serverErrors, function (key, value) {
                           errorMessages += `<li>${value}</li>`;
                       });

                       Swal.fire({
                           icon: 'error',
                           title: 'Serverfehler',
                           html: `<ul style="text-align: left;">${errorMessages}</ul>`,
                       });
                   } else {
                       Swal.fire({
                           icon: 'error',
                           title: 'Fehler',
                           text: 'Es ist ein unerwarteter Fehler aufgetreten.',
                       });
                   }
               },
           });
       });

       // Add a new key_task row dynamically
       $(document).on('click', '.add-task', function () {
           let rowCount = $('#key_task tbody tr').length;
           let newRow = `
               <tr>
                   <td>
                       <input type="text" name="key[${rowCount}][task]" value="" class="form-control">
                   </td>
                   <td>
                       <button type="button" class="btn btn-icon btn-danger remove-task">
                           <i class="fa fa-trash"></i>
                       </button>
                   </td>
               </tr>
           `;
           $('#key_task tbody').append(newRow);
       });

       // Remove a key_task row
       $(document).on('click', '.remove-task', function () {
           $(this).closest('tr').remove();
       });

       // Add a new sub_task row dynamically
       $(document).on('click', '.add-sub-task', function () {
           let rowCount = $('#sub_task tbody tr').length;
           let newRow = `
               <tr>
                   <td>
                       <input type="text" name="sub_task[${rowCount}][sub]" value="" class="form-control">
                   </td>
                   <td>
                       <input type="text" name="sub_task[${rowCount}][description]" value="" class="form-control">
                   </td>
                   <td>
                       <button type="button" class="btn btn-icon btn-danger remove-sub-task">
                           <i class="fa fa-trash"></i>
                       </button>
                   </td>
               </tr>
           `;
           $('#sub_task tbody').append(newRow);
       });

       // Remove a sub_task row
       $(document).on('click', '.remove-sub-task', function () {
           $(this).closest('tr').remove();
       });
   });
</script>


<!-- Saving New Task Script: end  -->

 


 
<!-- moving from menu to kalender tab  -->
<script>
    $(document).ready(function () {
        // Check if the URL contains a hash
        if (window.location.hash) {
            let tabHash = window.location.hash;

            // Find the tab and activate it
            let targetTab = $(`a[href="${tabHash}"]`);
            if (targetTab.length) {
                targetTab.tab('show'); // Bootstrap's tab method to show the tab
            }
        }

        // Update the URL hash when switching tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            let target = $(e.target).attr("href");
            history.replaceState(null, null, target);
        });
    });
</script>


@endpush