@extends('admin.layouts.app')
@section('title')
PERSONAL AUFGABEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link href="{{ asset('css/custom-menu.css') }}" rel='stylesheet' />
 <style>
 
   #deadline_area, .end_time_area, .repeated_area, .reminder_area ,.add_calendar_area{
            display: none;
        }
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
    .new_task {
        display: none; /* Hidden by default */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); /* Center the div */
        background: #f1f1f1;
        z-index: 10;
        width: 30% !important; /* Default width */
        max-width: 3-% !important;
        max-height: 85vh; /* Ensures it doesn't go beyond 80% of viewport height */
        overflow-y: auto; /* Enables scrolling inside */
        
    }

 

    /* Ensure modal content area scrolls separately */
    .new_task .modal-body {
        max-height: 85vh; /* Limit body height */
        overflow-y: auto; /* Enable scrolling */
        padding: 15px;
    }

    /* Sticky Header & Close Button */
    .new_task .modal-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .new_task .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-top: 1px solid #ddd;
    }

    /* Responsive styles for mobile */
    @media (max-width: 768px) {
        .new_task {
            width: 90% !important; /* 90% width on mobile */
            max-width: 90% !important;
        }
    }


.new_task_close {
   position: absolute;
    z-index: 4;
    left: -135px;
    top: 16%;
}
.card {
    box-shadow: 0 0 !important;
}

.odd_color {
        background: #e8e8e8; 
}
.mark-complete {

    text-decoration: line-through 3px black;
    color: #ff0000;

}


.progress {

    height: 23px !important;
    border: 1px solid gray !important;
    border-radius: 6px !important;

}

.progress-bar {
    width: 60%;
    height: 23px;
    border-radius: 0 !important;
    background-color: #e50056 !important;
}
@keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

.feather.icon-bell.warning.out-date {
    animation: blink 1s infinite; /* Blink animation with 1s duration, infinite loop */
}

.nav.nav-tabs .nav-item .nav-link.active {
    border: none;
    position: relative;
    color: #efffd8 !important;
    transition: all .2s ease;
    background-color: #8fc73e !important;
}

 
.dropup{
    position: absolute !important;
}

.form-control {
    display: block;
    width: 100%;
    height: calc(1.25em + 18px + 1px);
    padding: -1.3rem 0.7rem;
    font-size: 12px;
    font-weight: 400;
    line-height: 1.25;
    color: #4e5154;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    transition: border-color 0.15sease-in-out, box-shadow 0.15sease-in-out;
}


.select2-selection__choice {
    border:0 !important;
}
.line {
        width: 90%;
    border-bottom: 2px solid #b8b8b8;
    margin-top: 6px;
    margin-bottom: 6px;
}
</style>

<style>
/* Highlighting style for the edited row */
tr.edited {
    background-color: #ffcc00 !important;
    transition: background-color 0.5s ease;
}


.task {
    font-size:13px !important;
    margin-bottom:0;
        color:#3a3a3a;
            font-weight: bold;
}

.task_description {
    font-size:11px !important;
    color:#3a3a3a  !important;
}


.task_date {
    font-size:11px !important;
    color:#74b2d4  !important;
}

.table-responsive  {
    overflow: visible !important;
}
.appointment_menu {
    top: -52px !important;
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
                    <h2 class="content-header-title float-left mb-0">TERMINLISTE</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body"> 
                    
                       @php
                                           
                           $data = DB::table('main_appointments')
                                ->where('created_by', auth()->user()->name) // Ensure 'created_by' stores names, not IDs
                                ->whereNull('deleted_at') // Exclude soft-deleted records
                                ->select('status', 'created_by', 'deleted_at', 'end_date')
                                ->get();
                            $part = DB::table('main_appointment_employees')
                                        ->join('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                                        ->where('employee_id', auth()->user()->name)
                                        ->whereNull('main_appointments.deleted_at')
                                        ->count();
                                        
                            $open = $data->count();
                            $new_count = $data->where('status', 'start')->count();
                            $mine = $data->where('created_by', auth()->user()->name)->count();
                            $confirm = $data->where('status', 'confirm')
                                ->where('end_date', '>=', \Carbon\Carbon::today()) // Includes today and future
                                ->count();

                            $expired = $data->where('end_date', '<', \Carbon\Carbon::today())->count(); 
                            $cancel = $data->where('status', 'cancel')->count();
                            $deleted = $data->whereNotNull('deleted_at')->count();
                        @endphp
                    <section>
                    <div class="content-body">

                         <section id="page-account-settings">
                             <ul class="nav nav-tabs nav-fill mb-0" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab"  data-type="general"  aria-controls="home-fill" aria-selected="true">
                                         <i class="feather icon-star mr-50 font-medium-3"></i>
                                          OFFEN <span style="margin-right:5; margin-left:5;">|</span> {{ $new_count }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="confirm"   aria-selected="false">
                                         <i class="feather icon-check mr-50 font-medium-3"></i>BESTÄTIGT <span style="margin-right:5; margin-left:5;">|</span> {{ $confirm }}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="expire"   aria-selected="false">
                                           <i class="feather icon-info mr-50 font-medium-3"></i>
                                            ABGELAUFEN <span style="margin-right:5; margin-left:5;">|</span> {{ $expired }}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="cancel"   aria-selected="false">
                                          <i class="feather icon-x mr-50 font-medium-3"></i>ABSAGEN <span style="margin-right:5; margin-left:5;">|</span> {{ $cancel }}
                                    </a>
                                </li>
                           
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="deleted"   aria-selected="false">
                                            <i class="feather icon-trash mr-50 font-medium-3"></i>GELÖSCHT <span style="margin-right:5; margin-left:5;">|</span> {{ $deleted }}
                                    </a>
                                </li>

                                 <li class="nav-item  " style="margin-top:8px">
                                    <a href="{{route('personal.tasks.calendar')}}" >  
                                         <i class="feather icon-calendar mr-50 font-medium-3"></i>
                                    MEIN KALENDER</a>
                                </li>
                            </ul>
                           
                            <div class="tab-content">
                                <div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill"> 
                                    <section>
                                        
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card" style="height:100vh;">
                                                    <div class="card-header">
                                                        <h4 class="mb-0"> </h4> 
                                                                  <div class="col-md-6 col-12">
                                                                    <div class=" d-flex float-left">  
                                                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="created"   aria-selected="false"> 
                                                                                Ich als Verfasser <span style="margin-right:5; margin-left:5;">|</span> {{ $mine }}
                                                                        </a>

                                                                          <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill"  data-type="participant"   aria-selected="false"> 
                                                                                Ich als Teilnehmer <span style="margin-right:5; margin-left:5;">|</span> {{ $part }}
                                                                        </a> 
                                                                    </div>
                                                                </div> 

                                                                 <div class="col-md-6 col-12">
                                                                    <div class=" d-flex float-right">
                                                                         <form id="search-task-form" data-type="general" method="GET">
                                                                            <fieldset>
                                                                                <div class="input-group">
                                                                                    <input id="search-input" type="text" class="form-control" placeholder="Suchen" aria-describedby="button-addon2" value="{{ request('search') ?? '' }}">
                                                                                    <div class="input-group-append" id="button-addon2">
                                                                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Go</button>
                                                                                    </div>
                                                                                </div>
                                                                            </fieldset>
                                                                        </form>
                                                                     <button class="btn btn-primary waves-effect waves-light create_new_task" type="button"> Erstellen</button> 

                                                                    </div>
                                                                </div> 
                                                        
                                                    </div>
                                                    <div id="search-results" class="card-content mt-2"> </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </section> 
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
                                    <h5 class="modal-title" id="myModalLabel160">Einladung Ablehnen</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <!-- Correctly Opened Form Tag -->
                                <form action="{{ route('appointment.accept') }}" method="post" id="accept-request-form">
                                    @csrf

                                    <div class="modal-body">
                                        <p><i class="feather icon-info warning"></i> Sie wurden als Verantwortlicher für den folgenden Termin ausgewählt..</p>
                                        <div class="row">
                                            <input type="hidden" id="appointment" name="appointment_id" value="">
                                            <input type="hidden" name="employee_id" value="">

                                            <!-- Response Selection -->
                            
                                            <input type="hidden" value="reject" name="response">

                                            <!-- Reason Field -->
                                            <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                <fieldset class="form-group">
                                                    <label for="reason">Grund</label>
                                                    <textarea name="reason" class="form-control" rows="5" placeholder="Optional"></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-danger waves-effect waves-light">ablehnen</button>
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
                                    <h5 class="modal-title">Neuen Mitarbeiter hinzufügen</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form id="add-employee-modal">
                                    @csrf
                                    <div class="modal-body">
                                        <input type="hidden" id="appointment_id" name="appointment_id" value="">
                                        <input type="hidden" name="old_employee" id="old_employee" value=""> 
                                        <select name="employee_id" class="form-control selectables" required style="width:100%">
                                            @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="save-add-emp" class="btn btn-primary">speichern</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- accept button modal --> 
            </div>
        </div>  
    </div> 
</div>

   
                                                                                    
    <div class="cards new_task_card new_task" style="display:none">
        <div class="card-header" style="  border: 0;  background: transparent;  padding: 0;     justify-items: anchor-center;">
            <h3 class="title mt-1 ml-2" style="    color: #8fc73e !important; font-weight: bold;  justify-items: left;"> TERMIN ERSTELLEN</h3>
                <div class="line"  style="    border-bottom: 2px solid #8fc73e; width:90% !important"></div>
            
        </div>
        <div class="card-body p-0">
            <form  id="task-store-form">
                @csrf
                <div class="modal-body pt-0 pb-0">
                    <div class="cards p-1">
                

                        <div class="form-body">
                                <div class="row">
                                    <div class="col-md-10 col-10">
                                        <label for="task_title">Titel / Name *</label>
                                        <input type="text" id="name" class="form-control" name="name">
                                    </div>

                                    <div class="col-md-2">
                                            <input type="hidden" name="color" id="color" value="#8fc73e">
                                        <div class="btn-group dropup dropdown-icon-wrapper mt-1 " id="color_drop_down">
                                            <button type="button" class="btn btn-icon    waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                <i class="fa fa-square" id="colorIcon" style="color: #8fc73e;"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <span class="dropdown-item" data-value="#8fc73e">
                                                    <i class="fa fa-square" style="color: #8fc73e;"></i> Grün
                                                </span>
                                                <span class="dropdown-item" data-value="#ff0000">
                                                    <i class="fa fa-square" style="color: #ff0000;"></i> Rot
                                                </span>
                                                <span class="dropdown-item" data-value="#0000ff">
                                                    <i class="fa fa-square" style="color: #0000ff;"></i> Blau
                                                </span>
                                                <span class="dropdown-item" data-value="#ffff00">
                                                    <i class="fa fa-square" style="color: #ffff00;"></i> Gelb
                                                </span>
                                                <span class="dropdown-item" data-value="#ff00ff">
                                                    <i class="fa fa-square" style="color: #ff00ff;"></i> Magenta
                                                </span>
                                                <span class="dropdown-item" data-value="#00ffff">
                                                    <i class="fa fa-square" style="color: #00ffff;"></i> Cyan
                                                </span>
                                                <span class="dropdown-item" data-value="#000000">
                                                    <i class="fa fa-square" style="color: #1f2937;"></i> Schwarz
                                                </span>
                                                <span class="dropdown-item" data-value="#808080">
                                                    <i class="fa fa-square" style="color: #808080;"></i> Grau
                                                </span>
                                                <span class="dropdown-item" data-value="#ffa500">
                                                    <i class="fa fa-square" style="color: #ffa500;"></i> Orange
                                                </span>
                                                <span class="dropdown-item" data-value="#800080">
                                                    <i class="fa fa-square" style="color: #800080;"></i> Lila
                                                </span>
                                                <span class="dropdown-item" data-value="#8b4513">
                                                    <i class="fa fa-square" style="color: #8b4513;"></i> Braun
                                                </span>
                                                <span class="dropdown-item" data-value="#4682b4">
                                                    <i class="fa fa-square" style="color: #4682b4;"></i> Stahlblau
                                                </span>
                                                <span class="dropdown-item" data-value="#5f9ea0">
                                                    <i class="fa fa-square" style="color: #5f9ea0;"></i> Kadettenblau
                                                </span>
                                                <span class="dropdown-item" data-value="#d2691e">
                                                    <i class="fa fa-square" style="color: #d2691e;"></i> Schokoladenbraun
                                                </span>
                                                <span class="dropdown-item" data-value="#2e8b57">
                                                    <i class="fa fa-square" style="color: #2e8b57;"></i> Seegrün
                                                </span>
                                                <span class="dropdown-item" data-value="#dc143c">
                                                    <i class="fa fa-square" style="color: #dc143c;"></i> Karmesinrot
                                                </span>
                                                <span class="dropdown-item" data-value="#7fffd4">
                                                    <i class="fa fa-square" style="color: #7fffd4;"></i> Aquamarin
                                                </span>
                                                <span class="dropdown-item" data-value="#9932cc">
                                                    <i class="fa fa-square" style="color: #9932cc;"></i> Dunkles Lila
                                                </span>
                                                <span class="dropdown-item" data-value="#ff6347">
                                                    <i class="fa fa-square" style="color: #ff6347;"></i> Tomate
                                                </span>
                                            </div>
                                                
                                        </div>
                                    </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Startdatum *</label>
                                            <input type="hidden" name="same_id" value="same">
                                            <input type="date" id="start_date" class="form-control" name="start_date"  value=""> 

                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Enddatum *</label> 
                                            <input type="date" id="end_date" class="form-control" name="end_date" value="">

                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label for="start_time">Startzeit *</label>
                                            <input type="time" id="start_time" class="form-control" name="start_time" value="">
                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="end_time">Endzeit </label>
                                            <input type="time" id="end_time" class="form-control" name="end_time">
                                        </div>
                                        <div class="col-md-5 col-12 ">
                                            <label for="total_time">Dauer </label>
                                            <input type="number" id="total_time" class="form-control" name="total_time">
                                        </div>

                                        <div class="col-md-2 " >
                                            <div class="row">

                                                    <div class="col-md-6">
                                                        <label for="task_title">Öffentlich</label>
                                                        <div class="custom-control custom-switch   ">
                                                            <input type="checkbox" class="custom-control-input" id="customSwitch10" name="public" checked>
                                                            <label class="custom-control-label" for="customSwitch10">
                                                                <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                                                <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-12">
                                            <label for="task_title">Teilnehmer *</label>
                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}"  >{{ $emp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6"  >
                                            <label for="task_title">Kontakt</label>
                                            <select name="contact_id"   class="contact_list" style="width:100%">
                                            
                                            </select>
                                            <input type="hidden" name="contact_type" id="contact_type" value="">
                                        </div>

                                            <div class="col-md-6"  style="display:none;" id="link_section" >
                                                <span>Link *</span>
                                                <input type="text" class="form-control" value="{{ old('link') }}" id="link" name="link" >
                                        </div>
                                    
                                        <div class="col-md-6" id="intern" style="display: none;">
                                            <label for="task_title">Adress </label>
                                            <select name="branch_address_id" class="form-control" >
                                                <option ></option>
                                                @foreach ($branch_addresses as $address)
                                                    <option value="{{ $address->id }}"
                                                        data-street="{{ $address->street }}"
                                                        data-latitude="{{ $address->latitude }}"
                                                        data-longitude="{{ $address->longitude }}"
                                                        data-city="{{ $address->city }}"
                                                        data-postcode="{{ $address->postcode }}"
                                                    >{{ $address->branch_initial }} - {{ $address->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6" id="extern">
                                            <label for="task_title">Adress </label>
                                            <input id="full_address" type="text" class="form-control form-element"
                                                placeholder="Adresse eingeben"
                                                name="full_address"
                                                value="">

                                            <input type="hidden" id="street-input" name="street" value="">
                                            <input type="hidden" id="city-input" name="city" value="">
                                            <input type="hidden" id="latitude-input" name="latitude" value="">
                                            <input type="hidden" id="longitude-input" name="longitude" value="">
                                            <input type="hidden" id="postal_code-input" name="postcode" value="">
                                        </div>

                                            <div class="col-md-6"  >
                                            <label for="task_title">Telefon</label>
                                                <input type="text" class="form-control phone" value="{{ old('phone') }}"   name="phone"  >
                                        </div>

                                        <div class="col-md-6"  >
                                                <label for="task_title">Email <small>Optional</small></label>
                                                <input type="email" class="form-control email" value="{{ old('email') }}"  name="email"  >
                                        </div>

                                    


                                    <div class="col-md-6 col-12">
                                        <label for="task_title">Zweck</label>
                                            <input type="text" class="form-control" value="{{ old('appointment_type') }}" id="appointment_type" name="appointment_type" >
                                    </div>

                                        <div class="col-md-6 col-12">
                                        <label for="task_title">Ort des Termin </label>
                                        <select name="execution_type" id="execution_type" class="form-control">
                                                <option value="internal">Intern</option>
                                                <option value="external" selected>Extern</option>
                                                <option value="online">Online</option>
                                                <option value="telephone">Telefon</option>
                                        </select>
                                    </div>


                                    <div class="col-md-12 col-12 mb-1">
                                        <label for="task_title">Beschreibung</label>

                                        <textarea name="description" class="form-control" rows="1"></textarea>
                                    </div>
 
                                    
                                    <div class="col-md-4"  >
                                        <label for="task_title">Betrieb</label>
                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                            <option></option>
                                            @foreach($branches as $br)
                                                <option value="{{ $br->id}}">{{$br->branch}} </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 line"></div>
        
                                            <div class="col-md-6 col-12 ">
                                            <label for="month">Erinnerung</label>
                                                <input type="date" name="reminder_date" class="form-control">
                                        </div>

                                        <div class="col-md-6 col-12 ">
                                            <label for="month">Erinnerung</label>
                                            <input type="time" name="reminder_time" class="form-control">
                                        </div>

                                        <div class="col-md-6 col-12 ">
                                            <label for="priority">Priorität</label>
                                            <select name="priority" class="form-control" id="priority">
                                                <option value="normal" data-icon="fa fa-battery-empty">Keiner</option>
                                                <option value="medium" data-icon="fa fa-battery-half">Medium</option>
                                                <option value="high" data-icon="fa fa-battery-full">Hoch</option>
                                                <option value="very high" data-icon="fa fa-fire warning">Sehr Wichtig</option>
                                                
                                            </select>
                                        </div>

                                            <div class="col-md-6 col-12  ">
                                            <label for="date_type">Wiederholung</label>
                                            <select name="date_type" id="date_type" class="form-control"   style="width:100%">
                                                <option >Wählen</option>
                                            <option value="day" >Ganzer Tag</option>
                                                <option value="week" >7 Tage (Eine Woche)</option>
                                                <option value="daily" >Täglich</option>
                                                <option value="monthly" >Monatlich</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 from_day ">
                                            <label for="end_time">Von</label>
                                            <select name="from_day" id="from_day" class="form-control" style="width:100%">
                                                <option value="monday">Montag</option>
                                                <option value="tuesday">Dienstag</option>
                                                <option value="wednesday">Mittwoch</option>
                                                <option value="thursday">Donnerstag</option>
                                                <option value="friday">Freitag</option>
                                                <option value="saturday">Samstag</option>
                                                <option value="sunday">Sonntag</option>
                                            </select>
                                        </div>


                                        <div class="col-md-6 col-12 to_day ">
                                            <label for="end_time">Zu</label>
                                            <select name="to_day" id="to_day" class="form-control" style="width:100%">
                                                <option value="monday">Montag</option>
                                                <option value="tuesday">Dienstag</option>
                                                <option value="wednesday">Mittwoch</option>
                                                <option value="thursday">Donnerstag</option>
                                                <option value="friday">Freitag</option>
                                                <option value="saturday">Samstag</option>
                                                <option value="sunday">Sonntag</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 from_month ">
                                            <label for="month">Von (Monat)</label>
                                            <select name="from_month" id="from_month" class="form-control" style="width:100%">
                                                <option value="january">Januar</option>
                                                <option value="february">Februar</option>
                                                <option value="march">März</option>
                                                <option value="april">April</option>
                                                <option value="may">Mai</option>
                                                <option value="june">Juni</option>
                                                <option value="july">Juli</option>
                                                <option value="august">August</option>
                                                <option value="september">September</option>
                                                <option value="october">Oktober</option>
                                                <option value="november">November</option>
                                                <option value="december">Dezember</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 to_month ">
                                            <label for="month">Zu (Monat)</label>
                                            <select name="to_month" id="to_month" class="form-control" style="width:100%">
                                                <option value="january">Januar</option>
                                                <option value="february">Februar</option>
                                                <option value="march">März</option>
                                                <option value="april">April</option>
                                                <option value="may">Mai</option>
                                                <option value="june">Juni</option>
                                                <option value="july">Juli</option>
                                                <option value="august">August</option>
                                                <option value="september">September</option>
                                                <option value="october">Oktober</option>
                                                <option value="november">November</option>
                                                <option value="december">Dezember</option>
                                            </select>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="modal-footer" style="border:0; background:#f1f1f1 !important;">
                    <button type="button" class="btn btn-danger mr-1 waves-effect waves-light btn-sm close_task_window" data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                    <button type="button" class="btn btn-primary save-task btn-sm"><i class="feather icon-save"></i> speichern</button>
                </div>
            </form>
        </div>
    </div>
          
@endsection


@push('scripts')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
 
<!-- script for hidding the day and month drop down:  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateType = document.getElementById("date_type");
        const fromDay = document.querySelector(".from_day");
        const toDay = document.querySelector(".to_day");
        const fromMonth = document.querySelector(".from_month");
        const toMonth = document.querySelector(".to_month");

        function toggleFields() {
            const selectedValue = dateType.value;

            // Hide all fields by default
            fromDay.style.display = "none";
            toDay.style.display = "none";
            fromMonth.style.display = "none";
            toMonth.style.display = "none";

            // Show fields based on selection
            if (selectedValue === "daily") {
                fromDay.style.display = "block";
                toDay.style.display = "block";
            } else if (selectedValue === "monthly") {
                fromMonth.style.display = "block";
                toMonth.style.display = "block";
            }
        }

        // Run function on change and on load
        dateType.addEventListener("change", toggleFields);
        toggleFields();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Select2 for date_type
      

        function toggleFields() {
            const selectedValue = $("#date_type").val(); // Get selected value using Select2

            // Hide all fields by default
            $(".from_day, .to_day, .from_month, .to_month").hide();

            // Show fields based on selection
            if (selectedValue === "daily") {
                $(".from_day, .to_day").show();
            } else if (selectedValue === "monthly") {
                $(".from_month, .to_month").show();
            }
        }

        // Run function on change and on load
        $("#date_type").on("change", toggleFields);
        toggleFields(); // Run initially in case of preselected value
    });
</script>

<!-- script for hidding the day and month drop down: end  -->


 <script>
    $(document).ready(function () {
    let activeDataType = 'general'; // Default active tab is 'general'

    // Function to fetch tasks from the server (supports pagination)
    function fetchTasks(searchTerm = '', dataType = 'general', page = 1) {
        $.ajax({
            url: "{{ route('main.appointment.search') }}?page=" + page, // Append page number
            method: 'GET',
            data: { search: searchTerm, data_type: dataType },
            beforeSend: function () {
                $('#search-results').html('<p>Loading...</p>');
            },
            success: function (response) {
                $('#search-results').html(response); // Load tasks
            },
            error: function (xhr) {
                $('#search-results').html('<p class="text-danger">Error loading tasks. Please try again.</p>');
                console.error(xhr.responseText); // Log error
            }
        });
    }

    // Automatically load data for the default tab (general)
    fetchTasks('', activeDataType);

    // Handle tab click
    $('.nav-link').on('click', function (e) {
        e.preventDefault(); // Stop default action

        $('.nav-link').removeClass('active');
        $(this).addClass('active');

        let newDataType = $(this).data('type');
        activeDataType = newDataType; // Update active type

        fetchTasks($('#search-input').val(), activeDataType);
    });

    // Handle search form submission
    $('#search-task-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Get search term
        let searchTerm = $('#search-input').val();

        // Fetch tasks based on the search term and active tab's data-type
        fetchTasks(searchTerm, activeDataType);
    });

    // Handle pagination click (Dynamically added pagination links)
    $(document).on('click', '#search-results .pagination a', function (e) {
        e.preventDefault(); // Prevent default link action

        let page = $(this).attr('href').split('page=')[1]; // Get page number from URL
        fetchTasks($('#search-input').val(), activeDataType, page);
    });

});


 </script>
 
<script>
    $(document).ready(function(){
        $('.selectables').select2({
            tags: true, 
            placeholder: "Wählen",
            allowClear: true
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



<!-- Menu Close and Open Button: start  -->
<script>
    $(document).ready(function () {
        // Show the .new_task when the "Erstellen" button is clicked
        $('.create_new_task').on('click', function () {
            $('.new_task').css({
                display: 'block', // Ensure it's visible
                opacity: 1
            });
        });

        // Hide the .new_task when the "abbrechen" button is clicked
        $('.new_task').on('click', '.close_task_window', function () {
            $('.new_task').animate({
                opacity: 0, // Fade out
            }, 300, function () {
                $(this).hide(); // Hide after animation completes
            });
        });
    });
</script>

<!-- Menu Close and Open Button: end  -->
 
 
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


<!-- Menu Close and Open Button: start  -->
  <script>
    $(document).ready(function () {
        // Show the .new_task when the "Erstellen" button is clicked
        $('.create_new_task').on('click', function () {
            $('.new_task').css({
                right: '-100%', // Start offscreen (adjust based on your layout)
                display: 'block', // Ensure it's visible
            }).animate({
                right: '0', // Slide into view
            }, 500); // Animation duration in ms
        });

        // Hide the .new_task when the "abbrechen" button is clicked
        $('.new_task').on('click', '.close_task_window', function () {
            $('.new_task').animate({
                right: '-100%', // Slide out of view
            }, 500, function () {
                $(this).hide(); // Hide after animation completes
            });
        });
    });
</script>
<!-- Menu Close and Open Button: end  -->

<!-- save start  -->
<script>
    $(document).ready(function () {
    let rowIndex = 1; // Initialize the row index

    initSelect2(); // Initialize select2 for existing rows

    // Initialize Select2 for dynamically added rows
    function initSelect2() {
        $('.employee').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: function (markup) {
                return markup;
            },
        });
    }

    // Format employee in Select2 dropdown
    function formatEmployee(employee) {
        if (!employee.id) return employee.text;

        const imageUrl = $(employee.element).data('image');
        const employeeName = employee.text;

        return `
            <div style="display: flex; align-items: center;">
                <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
                <span>${employeeName}</span>
            </div>
        `;
    }

    // Handle save operation
    $('.save-task').on('click', function (e) {
        e.preventDefault();

        let form = $('#task-store-form');
        let formData = form.serialize();

        // Get form field values
        let title = $('#name').val();
        let appointmentType = $('#execution_type').val();
        let employee = $('#employee').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        let errors = [];

        // Validation checks
        if (!title) {
            errors.push('Der Titel darf nicht leer sein.');
        }

        if (!appointmentType) {
            errors.push('Bitte wählen Sie einen Termin-Typ aus.');
        }

        if (!employee || employee.length === 0) {
            errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
        }

        if (!startDate) {
            errors.push('Das Startdatum darf nicht leer sein.');
        }

        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
        }

        if (!endDate) {
            errors.push('Das Enddatum darf nicht leer sein.');
        }

        // Show validation errors if any
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: `<ul style="text-align: left;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`,
            });
            return;
        }

        // AJAX request to store the task
        let actionUrl = '{{ route('main.appointments.store') }}';

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $('.save-task').prop('disabled', true).text('speichern...');
            },
            success: function (response) {
                $('.save-task').prop('disabled', false).text('speichern');
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
                $('.save-task').prop('disabled', false).text('speichern');

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
});

</script>
<!-- save time area: end  -->
 


    <!-- Priority Script  -->
   <script>
    $(document).ready(function () {
        // Add click event listener to each dropdown-item
        $('#color_drop_down .dropdown-item').on('click', function () {
            // Get the selected color value from the data-value attribute
            const selectedColor = $(this).data('value');

            // Update the hidden input value
            $('#color').val(selectedColor);

            // Update the icon's color
            $('#colorIcon').css('color', selectedColor);
        });

          // Add click event listener to each dropdown-item
            $('#priority_select .dropdown-item').on('click', function () {
                // Get the selected priority value from the data-value attribute
                const selectedPriority = $(this).data('value');

                // Get the selected icon's HTML
                const selectedIcon = $(this).html();

                // Update the hidden input value
                $('input[name="priority"]').val(selectedPriority);

                // Update the button's icon
                $('#priority_select button').html(selectedIcon);
            });
        
    });


   </script>

    <!-- Priority Script end  -->



<!-- Deadline Script Toggle: start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get elements
        
        const repeatedButton = document.getElementById('repeated');
        const repeatedArea = document.querySelector('.repeated_area');
        const reminderButton = document.getElementById('reminder_check');
        const reminderArea = document.querySelector('.reminder_area');
        const addCalendarButton = document.getElementById('add_calendar');
        const addCalendarArea = document.getElementById('add_calendar_area');

        

        // Toggle repeated area
        repeatedButton.addEventListener('change', function () {
            if (this.checked) {
                repeatedArea.style.display = 'table-row';
            } else {
                repeatedArea.style.display = 'none';
            }
        });

        // Toggle reminder area
        reminderButton.addEventListener('change', function () {
            if (this.checked) {
                reminderArea.style.display = 'table-row';
            } else {
                reminderArea.style.display = 'none';
            }
        });

 
        // Initially hide all areas
       
        repeatedArea.style.display = 'none';
        reminderArea.style.display = 'none'; 
    });
</script>

<!-- showing online Link:  -->
  <script>
document.addEventListener("DOMContentLoaded", function () {
    const appointmentTypeDropdown = document.getElementById("execution_type");
    const internDiv = document.getElementById("intern");
    const externDiv = document.getElementById("extern");
    const linkDiv = document.getElementById("link_section");
    const branchSelect = document.querySelector("[name='branch_address_id']");
    const externInput = document.getElementById("full_address");

    function toggleSections() {
        const appointmentType = appointmentTypeDropdown.value;

        internDiv.style.display = "none";
        externDiv.style.display = "none";
        linkDiv.style.display = "none";

        resetHiddenInputs();

        if (appointmentType === "internal") {
            internDiv.style.display = "block";
            branchSelect.value = "";
        } 
        else if (appointmentType === "external") {
            externDiv.style.display = "block";
        } 
        else if (appointmentType === "online") {
            linkDiv.style.display = "block";
        }
        else if (appointmentType === "telephone") {
            // Do nothing for telephone appointments
        }
        else {
            externDiv.style.display = "block"; // Default to external
        }
    }

    function populateInternalAddress() {
        const selectedOption = branchSelect.options[branchSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            resetHiddenInputs();
            return;
        }

        document.getElementById("full_address").value = selectedOption.innerText;
        document.getElementById("street-input").value = selectedOption.getAttribute("data-street") || "";
        document.getElementById("city-input").value = selectedOption.getAttribute("data-city") || "";
        document.getElementById("postal_code-input").value = selectedOption.getAttribute("data-postcode") || "";
        document.getElementById("latitude-input").value = selectedOption.getAttribute("data-latitude") || "";
        document.getElementById("longitude-input").value = selectedOption.getAttribute("data-longitude") || "";
    }

    function resetHiddenInputs() {
        document.getElementById("full_address").value = "";
        document.getElementById("street-input").value = "";
        document.getElementById("city-input").value = "";
        document.getElementById("postal_code-input").value = "";
        document.getElementById("latitude-input").value = "";
        document.getElementById("longitude-input").value = "";
    }

    // Ensure initializeAutocomplete is globally accessible
    window.initializeAutocomplete = function() {
        if (!externInput) return;

        const autocomplete = new google.maps.places.Autocomplete(externInput, {
            types: ['geocode'],
            componentRestrictions: { country: 'DE' }
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.error("No details available for input: '" + place.name + "'");
                return;
            }

            let street = "", city = "", postalCode = "", latitude = "", longitude = "";

            place.address_components.forEach(component => {
                const types = component.types;

                if (types.includes("route")) {
                    street = component.long_name;
                }
                if (types.includes("locality") || types.includes("sublocality")) {
                    city = component.long_name;
                }
                if (types.includes("postal_code")) {
                    postalCode = component.long_name;
                }
            });

            latitude = place.geometry.location.lat();
            longitude = place.geometry.location.lng();

            // Populate inputs with external address data
            document.getElementById("street-input").value = street;
            document.getElementById("city-input").value = city;
            document.getElementById("postal_code-input").value = postalCode;
            document.getElementById("latitude-input").value = latitude;
            document.getElementById("longitude-input").value = longitude;
        });
    };

    function loadGoogleMapsAPI() {
        if (!window.google || !window.google.maps) {
            const script = document.createElement("script");
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places";
            script.async = true;
            script.defer = true;
            script.onload = function() {
                initializeAutocomplete();
            };
            document.head.appendChild(script);
        } else {
            initializeAutocomplete();
        }
    }

    appointmentTypeDropdown.addEventListener("change", toggleSections);
    branchSelect.addEventListener("change", populateInternalAddress);

    toggleSections();
    loadGoogleMapsAPI();
});
</script>

 
<!-- Start Date and End date same value  -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById("start_date");
    const endDateInput = document.getElementById("end_date");

    function setEndDate() {
        if (!startDateInput.value) return; // If no start date, do nothing
        endDateInput.value = startDateInput.value; // Set end date to match start date
    }

    // Event listener to update end date when start date changes
    startDateInput.addEventListener("input", setEndDate);

    // Set default value on page load (if start date is already set)
    setEndDate();
});
</script>

<!-- Start Date and End date same value : End -->


<!-- Calcuation of total Time:  -->
 
 

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById("start_date");
    const startTimeInput = document.getElementById("start_time");
    const endTimeInput = document.getElementById("end_time");
    const totalTimeInput = document.getElementById("total_time");
    const endDateInput = document.getElementById("end_date");
    const dateTypeInput = $("#date_type"); // Select2 uses jQuery selector

    // Function to set default working hours when selecting "Whole Day"
    function setWholeDayTime() {
        if (dateTypeInput.val() === "day") {
            startTimeInput.value = "08:00";
            endTimeInput.value = "16:00";
            totalTimeInput.value = 8; // 8 hours total
        }
    }

    // Function to set total_time to 8 hours when start_date is selected
    function setDefaultTotalTime() {
        if (startDateInput.value) {
            totalTimeInput.value = 8; // Default 8 hours
            endDateInput.value = startDateInput.value; // Set end_date same as start_date
        }
    }

    // Function to calculate time difference in hours
    function calculateTotalTime() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!startTime || !endTime) return;

        // Convert time to Date objects for calculation
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        // Ensure end time is after start time
        if (end < start) {
            showAlert("Fehler", "Endzeit muss nach der Startzeit liegen.", "error");
            endTimeInput.value = ""; // Reset end time
            return;
        }

        // Calculate difference in hours
        const diffInMs = end - start;
        const diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours

        totalTimeInput.value = diffInHours.toFixed(2); // Display in hours

        // Validate if time is within working hours (06:00 - 19:00)
        const startHour = start.getHours();
        const endHour = end.getHours();

        if (startHour < 6 || startHour >= 19 || endHour < 6 || endHour >= 19) {
            showAlert(
                "Achtung!",
                "Ihre gewählte Zeit liegt außerhalb der Arbeitszeit (06:00 - 19:00 Uhr).",
                "warning"
            );
        }
    }

    // Function to show SweetAlert2 alerts
    function showAlert(title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: "OK"
        });
    }

    // Event Listeners
    startDateInput.addEventListener("change", setDefaultTotalTime);
    startTimeInput.addEventListener("change", calculateTotalTime);
    endTimeInput.addEventListener("change", calculateTotalTime);

 

    // Initialize values on page load
    setDefaultTotalTime();
});
</script>

<!-- Contact List Drop Down API  -->
<script>
$(document).ready(function () {
    // Initialize Select2
    $('.contact_list').select2({
        placeholder: "Wählen", // Optional Placeholder
        allowClear: true,
        minimumInputLength: 0, // ✅ Allow default full list without typing
        ajax: {
            url: "{{ route('get.contact.list') }}",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '' // Pass search term if available, otherwise load all
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.main_id, // Contact ID
                            text: item.name + " " + item.lastname + " - " + item.type, // Display name in dropdown
                            type: item.type, // Contact type
                            phone: item.phone || "",
                            email: item.email || "",
                            street: item.street || "",
                            postcode: item.postcode || "",
                            city: item.city || "",
                            longitude: item.longitude || "",
                            latitude: item.latitude || "",
                            full_address: (item.street && item.city && item.postcode) ?
                                          item.street + ", " + item.postcode + " " + item.city : ""
                        };
                    })
                };
            },
            cache: true
        }
    });

    // ✅ On select, update all related input fields
    $('.contact_list').on('select2:select', function (e) {
        var selectedData = e.params.data;

        $('#contact_type').val(selectedData.type); // Set contact type
        $('.phone').val(selectedData.phone); // Set phone number
        $('.email').val(selectedData.email); // Set email address
        $('#full_address').val(selectedData.full_address); // Set full address
        $('#street-input').val(selectedData.street); // Set street
        $('#city-input').val(selectedData.city); // Set city
        $('#postal_code-input').val(selectedData.postcode); // Set postal code
        $('#latitude-input').val(selectedData.latitude); // Set latitude
        $('#longitude-input').val(selectedData.longitude); // Set longitude
    });

    // ✅ Clear fields when dropdown is cleared
    $('.contact_list').on('select2:clear', function () {
        $('#contact_type, .phone, .email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input').val('');
    });

    // ✅ Load full list when Select2 opens
    $('.contact_list').on('select2:open', function () {
        $(".select2-search__field").attr("placeholder", "Tippen Sie, um zu suchen..."); // Set search placeholder
    });
});
</script>





@endpush