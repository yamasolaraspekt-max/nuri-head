@extends('admin.layouts.app')
@section('title')
Mein Kalendar 
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />

<!-- <style>
    .fc-h-event {
        border-top: 1px solid #e8eaec !important;
        border-bottom: 1px solid #e8eaec !important;
        border-right: 1px solid #e8eaec !important;
    }
    .fc-event-time{
        color:black !important;
        margin-top: 34px!important;
    }
    .fc-event-title {
        color: #8fc73f !important;
        
    }
</style> -->


 <style>



      .fc-h-event {
        border-top: 1px solid #e8eaec !important;
        border-bottom: 1px solid #e8eaec !important;
        border-right: 1px solid #e8eaec !important;
        
    }
    .fc-button {
            background: #8fc73e !important;
            border: 0 !important;
            margin-right: 3px !important;
    }
    .fc-v-event  {
        background-color:white !important;
    }
        .fc-daygrid-event {
            display: block;
            width: 100%;
            background-color: #f8f9fa;
            border-left: 4px solid #00aaff;
            padding: 10px;
            border-radius: 6px; 
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s ease;
        }

        .fc-day-today  {
            background: #f1f1f1 !important;
        }
        .fc-toolbar-title{
            color:#626262;
        }

        .fc-timeGridWeek-view , .fc-timeGridDay-view , .fc-listWeek-view {
            background:white !important;
        }
        .fc-timeGridWeek-view {
            background:white !important;
        }

        .fc-timegrid-slot-minor {
            display:none !important;
        }

        .fc-daygrid-event:hover {
            background-color: #f2f2f2;
        }

        .custom-event {
            display: flex;
            flex-direction: column;
            gap: 0px;
        }

        .custom-event-status {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #28a745;
            font-weight: 600;
        }

        .custom-event-status i {
            margin-right: 5px;
        }

        .custom-event-title {
            font-size: 1rem;
            font-weight: 700;
            color: #333;
        }

        .custom-event-product {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #007bff;
        }

        .custom-event-product-status {
            font-weight: 600;
        }

        .custom-event-time {
            font-size: 0.8rem;
            color: #666;
        }

        .custom-dropdown-menu {
            display: none;
            position: absolute;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            z-index: 100;
            margin-top: -116px;
            margin-left: 249px;
            padding: 10px;
        }

        .custom-dropdown-menu ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .custom-dropdown-menu ul li {
            padding: 8px 15px;
            cursor: pointer;
        }

        .custom-dropdown-menu ul li:hover {
            background-color: #f0f0f0;
        }

        /* To ensure proper placement next to the icon */
        .event_drop_down {
            cursor: pointer;
            position: relative;
        }
        #bellIcon {
    animation: zoomAndColorChange 1s ease-in-out infinite;
        }

        /* Keyframes for the animation */
        @keyframes zoomAndColorChange {
            0% {
                transform: scale(1);
                color: inherit;
            }
            100% {
                transform: scale(1.2);
                color: red;
            }
        }


         .warning_text {
    animation: zoomAndColorChange 1s ease-in-out infinite;
        }

        /* Keyframes for the animation */
        @keyframes zoomAndColorChange {
            0% {
                transform: scale(1);
                color: inherit;
            }
            100% {
                transform: scale(1.2);
                color: #ff9f43;
            }
        }
    </style>

    <style>
    .fc-daygrid-event {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .custom-event-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .custom-event-product ul {
        margin: 0;
        padding: 0;
        display: flex;
        gap: 5px;
    }

    .custom-event-product ul li img {
        border-radius: 50%;
    }

    @media (max-width: 768px) {
        .fc-header-toolbar {
            flex-direction: column;
        }
        .fc-daygrid-event {
            font-size: 12px;
        }
    }

    .fc-popover{
        position: absolute !important;
    }
    .fc-license-message {
        display:none !important;
    }

    .emp_active {
        border: 3px solid #8fc73e;
    }

    .task-bg {
        background: #D6EAF9 !important;
    }
    .appointmetn-bg{
        background:#E5F0D5 !important;
    }
    .fc-daygrid {
        background:white;
    }
    .fc-button-active{
        background:#74b2d4 !important;
    }

    .task-event {
    background-color: #D6EAF9 !important;
}

.appointment-event {
    background-color: #E5F0D5 !important;
}

.calendar   {
        height: 100vh !important;
}


@media (max-width: 768px) {
    .fc-daygrid-day {
        min-height: 100px !important; /* Adjust height as needed */
    }

    .fc-daygrid-day-frame {
        height: 100%; /* Ensure it stretches */
        display: flex;
        flex-direction: column;
        justify-content: center; /* Center events inside */
    }

    .fc-daygrid-day-events {
        flex-grow: 1; /* Ensures it takes up available space */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fc-daygrid-event {
        font-size: 14px !important; /* Increase font size */
        padding: 8px !important; /* More padding */
        min-width: 80%; /* Ensures events don't shrink */
        text-align: center;
        display: inline-block;
    }
}

.fc-timeGridWeek-view .mobile_title {
    transform:rotate(90Deg) !important;
    color:gray;
}

.fc-timeGridWeek-view  .mobile_view {

    display: flex;
    align-items: center;
    flex-direction: column;

}

 
 .fc-popover .fc-timegrid-event {
    display: flex !important;
    position: relative !important;
    min-height: 20px !important; /* Ensures small events remain visible */
    width: auto !important;
    white-space: normal;
    font-size: 12px;
    padding: 4px;
}

.fc-popover .fc-timegrid-slot {
    height: 50px; /* Adjust slot height */
}
 

</style>

<style>
    .new_task {
        display: none; /* Hidden by default */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); /* Center the div */
        background: white;
        padding: 20px;
        background: transparent; 
        z-index: 10;
        width: 55% !important; /* Default width */
        max-width: 55% !important;
        max-height: 80vh; /* Ensures it doesn't go beyond 80% of viewport height */
        overflow-y: auto; /* Enables scrolling inside */
        border-radius: 8px;
    }

    .new_task .new_task_card {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Ensure modal content area scrolls separately */
    .new_task .modal-body {
        max-height: 60vh; /* Limit body height */
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

/* Event information Design  */
/* Default popup style */
.custom-swal-popup {
    color: white !important;
    border-radius: 10px;
    text-align: left;
}

/* Style for close button */
.custom-confirm-btn {
    background-color:rgb(194, 58, 28) !important; /* Green */
    color: white !important;
    font-weight: bold;
    border-radius: 5px;
}

/* Style for view details button */
.custom-cancel-btn {
    background-color: #74b2d4 !important; /* Blue */
    color: white !important;
    font-weight: bold;
    border-radius: 5px;
}

/* Customize text and icon colors inside SweetAlert */
.swal2-html-container .custom-event a {
    font-size: 14px;
    color: #74b2d4 !important;
}

.swal2-html-container .custom-event p {
    font-size: 12px;
    color: #74b2d4 !important;
}

/* Customize employee images inside the modal */
.custom-event ul.users-list li img {
    border: 2px solid white;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
}

/* Hide sidebar on mobile */
@media (max-width: 1394px) {
    #calendar_icons {
        display: none !important;
    }
    #calendar_times {
        display: none !important;
    }
}

/* Adjust search bar spacing on small screens */
@media (max-width: 576px) {
    .employee_search_input,
    .task_search_input,
    .appointment_search_input {
        margin-bottom: 10px;
    }
}

/* Menu of calender  */
.calendar_menu {
        position: absolute !important;
    bottom: 173px !important;
    left: 86% !important;
}
.calendar_menu button {
        color: white;
    font-size: 18px;
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
                    <h2 class="content-header-title float-left mb-0">KALENDER</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashbaord</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/personal/task/'.auth()->user()->name) }}">Allgemeine Aufgaben</a></li>
                            <li class="breadcrumb-item active">Meine Kalender</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">  
                 <div class="row">
                    <!-- Sidebar (Filters, Search, Employees) -->
                    <div class="col-md-2 col-12 slider   " id="slider_section">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                                    <li class="nav-item d-flex justify-content-between">
                                        <button type="button" class="btn btn-icon btn-outline-primary waves-effect waves-light" id="hide_slider">
                                            <i class="feather icon-minus"></i>
                                        </button> 
                                    </li>
                                </ul> 

                                <div class="divider"><div class="divider-text">Filter nach</div></div>

                                <div class="col-12 p-0">
                                    <ul class="list-unstyled mb-1">
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" name="filter" checked="" value="employee">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span>Mitarbeiter</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" name="filter" value="task">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span>Aufgaben</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" name="filter" value="appointment">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span>Termin</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" name="filter" value="">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span>Montage</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>

                                    <!-- Search Inputs -->
                                    <div class="col-12 employee_search_input">
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="text" class="form-control" name="searchEmployee" id="employee_get" placeholder="Vorname, nachname,...">
                                            <div class="form-control-position"><i class="feather icon-search"></i></div>
                                        </fieldset>
                                    </div>

                                    <div class="col-12 task_search_input" style="display:none;">
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="text" class="form-control" name="searchEmployee" id="employee_get" placeholder="Aufgaben, Datum,..">
                                            <div class="form-control-position"><i class="feather icon-search"></i></div>
                                        </fieldset>
                                    </div>

                                    <div class="col-12 appointment_search_input" style="display:none;">
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="text" class="form-control" id="appointment_search" placeholder="Termin, Datum,..">
                                            <div class="form-control-position"><i class="feather icon-search"></i></div>
                                        </fieldset>
                                    </div>  
                                    <!-- Employee List -->
                                    <div class="employee_lists" id="search_emp_result">
                                        <!-- Dynamic content -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Section -->
                    <div class="col-md-10 col-12 calender_section"> 
                        <div class="calendar"></div>
                    </div>
                </div>
 
                <div class="new_task"  style="display:none">                                                                                          
                    <div class="card new_task_card">
                        <div class="card-header" style="    border-bottom: 1px solid #8fc73e;">
                            <h3 class="title mt-1 ml-2"  style="    color: rgb(70, 70, 70) !important;"> Termin erstellen</h3> 
                        </div>
                        <div class="card-body">
                            <form  id="task-store-form">
                                @csrf
                                <div class="modal-body">
                                    <div class="card p-1">
                                        <div class="form-body">
                                              <div class="row">   
                                                    <div class="col-md-9 col-12">
                                                        <label for="task_title">Titel / Name *</label>
                                                        <input type="text" id="name" class="form-control" name="name">
                                                    </div>

                                                

                                                    <div class="col-md-3  " > 
                                                        <div class="row">
                                                                <div class="col-md-6"> 
                                                                    <input type="hidden" name="color" id="color" value="#8fc73e"> 
                                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mt-1 " id="color_drop_down">
                                                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
                                                                                <i class="fa fa-square" style="color: #000000;"></i> Schwarz
                                                                            </span>
                                                                            <span class="dropdown-item" data-value="#ffffff">
                                                                                <i class="fa fa-square" style="color: #ffffff; border: 1px solid #ccc;"></i> Weiß
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

                                                    <div class="col-md-3 col-12">
                                                        <label for="task_title">Ort des Termin *</label>
                                                        <select name="execution_type" id="execution_type" class="form-control">
                                                                <option selected disabled>Wählen</option>
                                                                <option value="internal">Intern</option>
                                                                <option value="external">Extern</option>
                                                                <option value="online">Online</option>
                                                                <option value="telephone">Telefon</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-3 col-12">
                                                        <label for="task_title">Art des Termin *</label> 
                                                            <input type="text" class="form-control" value="{{ old('appointment_type') }}" id="appointment_type" name="appointment_type" >
                                                    </div>
                                                    <div class="col-md-6"  style="display:none;" id="link_section" >
                                                            <span>Link *</span>
                                                            <input type="text" class="form-control" value="{{ old('link') }}" id="link" name="link" >
                                                    </div> 
                                                
                                                <div class="col-md-6" id="intern" style="display: none;">
                                                        <label for="task_title">Adress *</label>
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
                                                        <label for="task_title">Adress *</label> 
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
                    
                                                
                                                    <div class="col-md-3"  >
                                                            <label for="task_title">Telefon</label>
                                                            <input type="text" class="form-control" value="{{ old('phone') }}" id="telephone-input" name="phone"  >
                                                    </div> 

                                                    <div class="col-md-3"  >
                                                            <label for="task_title">Email <small>Optional</small></label>
                                                            <input type="email" class="form-control" value="{{ old('email') }}"  name="email"  >
                                                    </div> 


                                                    <div class="col-md-3"  >
                                                        <label for="task_title">Kunde</label>
                                                        <select name="customer_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($customers as $cus) 
                                                                <option value="{{ $cus->id}}">{{$cus->name}} {{$cus->lastname}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div> 

                                                    <div class="col-md-3"  >
                                                        <label for="task_title">Betrieb *</label>
                                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($branches as $br)
                                                                <option value="{{ $br->id}}">{{$br->branch}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>   
                    
                                                    
                                                        <div class="col-md-12 time_management_card">
                                                            <div class="row d-flex">
                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="start_date">Startdatum *</label>
                                                                    <input type="hidden" name="same_id" value="same">
                                                                    <input type="date" id="start_date" class="form-control" name="start_date"  value="">
                                                                    <input type="hidden" id="end_date" class="form-control" name="end_date" value="">

                                                                </div> 
                                                                <div class="col-md-3 col-12">
                                                                    <label for="start_time">Startzeit *</label>
                                                                    <input type="time" id="start_time" class="form-control" name="start_time" value="">
                                                                </div>

                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="end_time">Endzeit *</label>
                                                                    <input type="time" id="end_time" class="form-control" name="end_time">
                                                                </div> 
                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="total_time">Termin Dauer </label>
                                                                    <input type="number" id="total_time" class="form-control" name="total_time">
                                                                </div>

                                                                <div class="col-md-4 col-12  ">
                                                                    <label for="date_type">Typ</label>
                                                                    <select name="date_type" id="date_type" class="form-control"   style="width:100%">  
                                                                        <option >Wählen</option>
                                                                    <option value="day" >Ganzer Tag</option>
                                                                        <option value="week" >7 Tage (Eine Woche)</option>
                                                                        <option value="daily" >Täglich</option>
                                                                        <option value="monthly" >Monatlich</option>
                                                                    </select>
                                                                </div> 

                                                                <div class="col-md-4 col-12 from_day ">
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


                                                                <div class="col-md-4 col-12 to_day ">
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

                                                                <div class="col-md-4 col-12 from_month ">
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

                                                                <div class="col-md-4 col-12 to_month ">
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

                                                    <div class="row mt-1">
                                                        <div class="col-md-12 col-12"> 
                                                            <label for="task_title">Teilnehmer *</label> 
                                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                                @foreach ($employees as $emp)
                                                                <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}"  >{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div> 

                                                

                                                        <div class="col-md-12 col-12 mb-1"> 
                                                            <label for="task_title">Beschreibung</label> 

                                                            <textarea name="description" class="form-control" rows="1"></textarea>
                                                        </div>
                                                    </div> 
                                                            
                                                </div>  

                                                 <div class="col-md-12 p-0">
                                                    <div class="table-responsive">
                                                        <table class="table"> 
                                                            <tr>
                                                                <th>Wiederholung</th>
                                                                <th>Erinnerung</th>
                                                                <th>Priorität</th>
                                                            </tr>
                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                <td>
                                                                    <select name="repeat" class="form-control mt-1" id="wiederholung">
                                                                        <option value="">Häufigkeit auswählen</option>
                                                                        <option value="minute">Minütlich</option>
                                                                        <option value="hourly">Stündlich</option>
                                                                        <option value="daily">Täglich</option>
                                                                        <option value="weekly">Wöchentlich</option>
                                                                        <option value="monthly">Monatlich</option>
                                                                        <option value="quarterly">Vierteljährlich</option>
                                                                        <option value="yearly">Jährlich</option>
                                                                    </select>
                                                                </td>  

                                                                <td>
                                                                    <div class="d-flex">
                                                                        <div class="col-md-6">
                                                                            <label for="reminder_area" >Datum:</label>
                                                                            <input type="date" name="reminder_date" class="form-control">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="reminder_area"  >Zeit:</label>
                                                                            <input type="time" name="reminder_time" class="form-control">
                                                                        </div>  
                                                                    </div>
                                                                </td>
                                                                <td> 
                                                                     <select name="priority" class="form-control mt-1" id="priority"> 
                                                                        <option value="normal" data-icon="fa fa-battery-empty">Keiner</option>
                                                                        <option value="medium" data-icon="fa fa-battery-half">Medium</option>
                                                                        <option value="high" data-icon="fa fa-battery-full">Hoch</option>
                                                                        <option value="very high" data-icon="fa fa-fire warning">Sehr Wichtig</option>
                                                                       
                                                                    </select>
                                                                    
                                                                </td>
                                                            </tr> 

                                                        </table>      
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>
                                    </div>

                                <div class="modal-footer"> 
                                    <button type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                                    <button type="button" class="btn btn-primary save-task"><i class="feather icon-save"></i> Speichern und schließen</button>
                                    <button type="button" class="btn btn-primary save-task"><i class="feather icon-save-continue"></i> Speichern und fortfahren</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
       

    @endsection

    @section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="{{asset('app-assets/js/scripts/tooltip/tooltip.js')}}"></script>
 
 
  <!-- hidding th slider div: start  -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let hideButton = document.getElementById("hide_slider");

            if (!hideButton) {
                console.error("Hide button not found!");
                return;
            }

            hideButton.addEventListener("click", function () {
                let slider = document.getElementById("slider_section");
                let calendarSection = document.querySelector(".col-md-10.calender_section");

                if (slider) {
                    // Hide the slider
                    slider.classList.add("d-none");
                    slider.classList.remove("d-md-block");

                    // Expand the calendar section
                    if (calendarSection) {
                        calendarSection.classList.remove("col-md-10");
                        calendarSection.classList.add("col-md-12");
                    }

                    // Check if "Show Slider" button already exists
                    if (!document.getElementById("show_slider")) {
                        let showButton = document.createElement("button");
                        showButton.id = "show_slider";
                        showButton.className = "fc-today-button fc-button fc-button-primary";
                        showButton.innerHTML = "Slider anzeigen";

                        // Insert next to the "Today" button in the FullCalendar header
                        let todayButton = document.querySelector(".fc-today-button");
                        if (todayButton) {
                            todayButton.parentNode.insertBefore(showButton, todayButton.nextSibling);
                        } else {
                            console.warn("Today button not found, appending 'Show Slider' to body");
                            document.body.appendChild(showButton);
                        }

                        // Add click event to show the slider again
                        showButton.addEventListener("click", function () {
                            slider.classList.remove("d-none");
                            slider.classList.add("d-md-block");

                            // Resize the calendar section
                            if (calendarSection) {
                                calendarSection.classList.remove("col-md-12");
                                calendarSection.classList.add("col-md-10");
                            }

                            // Remove "Show Slider" button
                            showButton.remove();
                        });
                    }
                } else {
                    console.error("Slider not found!");
                }
            });
        });
        </script>

  <!-- hidding th slider div: end  --> 

  <script>
    $(document).ready(function(){
        $('.selectables').select2({
            tags: true, 
            placeholder: "Wählen",
            allowClear: true
        });
    });
</script>
 
<script>
    const baseUrl = "{{ asset('images/employee/')}}";
</script>
<!-- Filter by Employee  -->
 <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
 <script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.querySelector(".calendar");
        const taskViewModal = document.querySelector("#task_view");
        const newTaskDiv = document.querySelector(".new_task");
        const startDateInput = document.querySelector("#start_date");
        const endDateInput = document.querySelector("#end_date");
        const startTimeInput = document.querySelector("#start_time");

        // Function to get selected employee IDs and their appointment selection
        function getSelectedEmployeeData() {
            const employeeData = [];

            document.querySelectorAll(".employee_check").forEach((checkbox) => {
                if (checkbox.checked) {
                    const employeeId = checkbox.getAttribute("data-id");
                    const appointmentCheckbox = document.querySelector(`#employeeAppointment${employeeId}`);

                    employeeData.push({
                        employee_id: employeeId,
                        tasks_only: appointmentCheckbox && appointmentCheckbox.checked ? 1 : 0,
                        appointments_only: appointmentCheckbox && appointmentCheckbox.checked ? 0 : 1,
                    });
                }
            });

            return employeeData;
        }
 
        let calendar;

        function initializeCalendar(events = []) {
                if (calendar) {
                    calendar.destroy(); // Ensure we don't create duplicate calendars
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: "timeGridWeek",
                    locale: "de",
                    firstDay: 1,
                    weekNumbers: true,  // Enable week numbers
                    weekNumberCalculation: "ISO", // Use ISO week numbers
                    weekNumberFormat: { week: "numeric" }, // Show the week number in numeric format
                    allDaySlot: false,
                    slotEventOverlap: false, // Prevent overlapping events
                    nowIndicator: true, // Show current time indicator
                    // titleFormat: { weekday: 'long' }, // Shows only "Mo", "Di", "Mi" (short weekday names)
                    // titleFormat: { month: 'long' }, //  Shows only "Februar", "März", etc.
                    //eventMinHeight: 100, // Ensure event is always readable
                    // dayHeaderFormat: { weekday: 'long' }, // Shows only "Mo", "Di", "Mi", etc.
                    dayHeaderFormat: { weekday: 'short', day: 'numeric' }, // Shows "Mi 18", "Do 19", etc.
                    eventMaxStack: true, // Allow auto-adjusting stacks
                    eventOverlap: false, // Prevent event overlap
                    slotMinTime: "00:00:00",
                    slotMaxTime: "24:00:00",
                    // slotDuration: "00:30:00", // Show 30-minute intervals
                    // slotLabelInterval: "00:30:00", // Label every 30 minutes (17:00, 17:30, etc.)
                    slotEventOverlap: false, // Prevent overlap
                    // nowIndicator: true, // Show current time indicator
                    // eventMinHeight: 20, // Minimum event height
                    displayEventTime: true, // Show time in event
                    eventOverlap: true, // Prevent event overlap
                    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false }, // 24-hour format
                    // height: "auto",
                    // expandRows: true, // Ensures rows expand dynamically
                    slotLabelFormat: {
                        hour: "2-digit",
                        minute: "2-digit",
                        omitZeroMinute: false,
                        meridiem: false
                    },
                    slotLabelContent: function(info) {
                        return info.text; // Add 'Uhr' to each time slot
                    },
                    headerToolbar: {
                        left: "prev,next today",
                        center: "title",
                        right: "year,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                    },
                    
                    buttonText: { 
                        today: "Heute",
                        year: "Jahr",
                        month: "Monat",
                        week: "Woche",
                        day: "Tag",
                        list: "Übersicht",
                    },
                    views: {
                        year: {
                            type: "multiMonthYear",
                            duration: { months: 12 },
                            buttonText: "Jahr",
                        },
                    },
    

                    editable: true,
                    eventResizableFromStart: true,
                    events: events,


                eventClick: function (info) {
                    const { title, extendedProps, id } = info.event;
                    const { employees, taskColor, priority, appointment, public, type, start_time, end_time, description, customer_id } = extendedProps;

                    // Function to format time from HH:mm:ss to HH:mm
                    function formatTime(time) {
                        if (!time || time === "null" || time === "undefined") {
                            return "N/A"; // If no valid time is provided
                        }
                        return time.split(":").slice(0, 2).join(":"); // Extracts only HH:mm
                    }

                    const cleanId = id.toString().split("-")[0];

                    let priorityIcon = priority === "very high"
                        ? '<i class="fa fa-fire warning mr-1"></i>'
                        : priority === "high"
                        ? '<i class="fa fa-bell important mr-1"></i>'
                        : "";

                    let typeIcon = type === 'appointment'
                        ? '<i class="feather icon-users"></i>'
                        : '<i class="fa fa-tasks"></i>';

                    let detailsUrl = type === 'appointment'
                        ? `/appointment_details/${cleanId}`
                        : `/personal_task_details/${cleanId}`;

                    let editUrl = type === 'appointment' 
                        ? `/appointment/${cleanId}/edit`
                        : `/personal_task/${cleanId}/edit`;

                    let eventType = type === 'appointment' ? 'appointment' : 'task';

                    let employeeList = employees.map(employee => `
                        <li data-toggle="tooltip" title="${employee.name} ${employee.lastname}">
                            <img src="/images/employee/${employee.image || "default-avatar.png"}" 
                            alt="Avatar" height="30" width="30" class="rounded-circle" style="border: 0; box-shadow: 0 0;">
                        </li>
                    `).join('');

                    const backgroundColor = type === 'appointment' ? '#c0d8ea' : '#c0d8ea';

                    let actionMenu = `
                        <div class="dropdown" style="position: absolute; top: 112px;">
                            <button class="btn btn-sm" type="button" id="eventActionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="eventActionMenu">
                                <a class="dropdown-item" href="#" id="copy_event"><i class="feather icon-copy"></i> Duplizieren [Coming Soon...]</a>
                                <a class="dropdown-item" href="${editUrl}" id="edit_event"><i class="feather icon-edit"></i> Bearbeiten</a>
                                <a class="dropdown-item text-danger" href="#" id="delete_event" data-event-type="${eventType}" data-event-id="${cleanId}">
                                    <i class="feather icon-trash"></i> Löschen
                                </a>
                            </div>
                        </div>`;

                    let eventDetails = `
                        <div class="custom-event">
                            <div class="custom-event-header" style="display: flex; align-items: center;">
                                <i class="fa ${public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                                ${priorityIcon}
                                <span class="custom-event-status-text">
                                    ${typeIcon}
                                    <i class="feather icon-info warning info_popup" data-id="${cleanId}" ${type === 'appointment' ? 'data-type="appointment"' : 'data-type="task"'}></i>
                                    ${type === 'appointment' 
                                        ? `<i class="feather icon-map show_map" data-id="${cleanId}"></i>` 
                                        : ''
                                    }
                                    <span class="calendar_menu">${actionMenu}</span>
                                </span>
                            
                            </div>
                            <div class="custom-event-title m-0">
                                <a href="${detailsUrl}" style="font-size:13px; color: ${type === 'task' ? '#74b2d4' : '#93c21c'};">
                                    ${description}
                                </a>
                                <p class="p-0 m-0" style="font-size:13px; color: ${type === 'task' ? '#74b2d4' : '#93c21c'};">
                                    <i class="feather icon-clock"></i> ${formatTime(start_time)} - ${formatTime(end_time)}
                                </p>
                            </div>
                            <div id="customerDetails"></div> <!-- Placeholder for customer info -->
                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">${employeeList}</ul>
                        </div>`;

                    // Show modal first without customer info
                    Swal.fire({
                        title: title,
                        html: eventDetails,
                        showCloseButton: true,
                        confirmButtonText: "abbrechen",
                        cancelButtonText: "weitere Details anzeigen",
                        showCancelButton: true,
                        confirmButtonColor: "#d92127",
                        cancelButtonColor: "#93c21c",
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-confirm-btn',
                            cancelButton: 'custom-cancel-btn'
                        },
                        didOpen: () => {
                            $('[data-toggle="tooltip"]').tooltip();
                            document.querySelector('.swal2-popup').style.background = backgroundColor;
                            
                            // Fetch customer details only if customer_id exists
                            if (customer_id) {
                                $.ajax({
                                    url: `/get_customer_details/${customer_id}`, // Laravel route to fetch customer
                                    type: 'GET',
                                    success: function (response) {
                                        if (response) {
                                            let customerDetails = `
                                                <div class="customer-info mt-2 p-2 rounded" style="background: #f4f4f4;">
                                                    <h6>Kunde Informationen</h6>
                                                    <p><b>Name:</b> ${response.name} ${response.lastname}</p>
                                                    <p><b>Telefon:</b> ${response.phone || "N/A"}</p>
                                                    <p><b>Email:</b> ${response.email || "N/A"}</p>
                                                    <p><b>Adresse:</b> ${response.full_address || "N/A"}</p>
                                                </div>`;
                                            $('#customerDetails').html(customerDetails);
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error("Error fetching customer details:", error);
                                    }
                                });
                            }
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href = detailsUrl;
                        }
                    });
                },

 
                dateClick: function (info) {
                    const clickedDate = info.dateStr.split("T")[0]; // Extract the date (YYYY-MM-DD)
                    const clickedTime = info.dateStr.includes("T") 
                        ? info.dateStr.split("T")[1].slice(0, 5)  // Extract HH:MM
                        : "00:00";  // Default to midnight if no time is provided

                    // Set the values in the form
                    if (startDateInput) startDateInput.value = clickedDate;
                    if (endDateInput) endDateInput.value = clickedDate;
                    if (startTimeInput) startTimeInput.value = clickedTime;

                    // Show the new task div
                    if (newTaskDiv) {
                        newTaskDiv.style.display = "block";
                    }
                },

                eventDidMount: function (info) {
                    const { employees, taskColor, priority, appointment, public, type, start_time, end_time } = info.event.extendedProps;

                    // Function to format time from HH:mm:ss to HH:mm
                    function formatTime(time) {
                        if (!time || time === "null" || time === "undefined") {
                            return "N/A"; // If no valid time is provided
                        }
                        return time.split(":").slice(0, 2).join(":"); // Extracts only HH:mm
                    }

                    // Function to truncate text to 10 characters
                    function truncateText(text, maxLength) {
                        return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
                    }

                    // Get truncated event title
                    let truncatedTitle = truncateText(info.event.title, 20);

                    // Mobile View: Show only title with background color
                    if (window.innerWidth <= 500) {
                        info.el.style.backgroundColor = info.event.backgroundColor || "#8fc73e"; // Use background color from event
                        info.el.style.color = "#fff"; // Ensure text is visible
                        info.el.style.padding = "5px";
                        info.el.style.borderRadius = "4px";
                        info.el.style.textAlign = "center";
                        info.el.style.fontSize = "12px";
                        info.el.innerHTML = `
                            <div class="mobile_view">
                                <p>${truncatedTitle}</p> 
                            </div>
                        `;
                    } else {
                        info.el.classList.add("fc-daygrid-dot-event", "fc-event");

                        if (info.event.extendedProps.type === 'task') {
                            info.el.classList.add("task-event");  // Add a CSS class
                        } else {
                            info.el.classList.add("appointment-event");
                        }

                        info.el.style.whiteSpace = "normal";
                        info.el.style.borderLeft = `5px solid ${taskColor || "#8fc73e"}`;

                        const cleanId = info.event.id.toString().split("-")[0]; // Extract only the event ID

                        const detailsUrl = type === 'appointment' 
                            ? `/appointment_details/${cleanId}` 
                            : `/personal_task_details/${cleanId}`;

                        info.el.innerHTML = `
                            <div class="custom-event">
                                <div class="custom-event-header" style="display: none; align-items: center;" id="calendar_icons">
                                    <i class="fa ${public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                                    <i class="fa ${
                                        priority === "very high" ? "fa-fire warning mr-1" 
                                        : priority === "high" ? "fa-bell important mr-1" 
                                        : ""
                                    }"></i>
                                    <span class="custom-event-status-text"> 
                                        ${type === 'appointment' 
                                            ? '<i class="feather icon-users"></i>' 
                                            : '<i class="fa fa-tasks"></i>'
                                        }
                                        <i class="feather icon-info warning info_popup" data-id="${cleanId}" 
                                        ${ type === 'appointment' ? 'data-type="appointment"' : 'data-type="task"' }></i>

                                        ${type === 'appointment' 
                                            ? `<i class="feather icon-map show_map" data-id="${cleanId}"></i>` 
                                            : ''
                                        }
                                    </span>
                                     <p class="p-0 m-0" id="calendar_times"  style="font-size:10px; color: ${info.event.extendedProps.type === 'task' ? '#74b2d4' : '#93c21c'};" > ${formatTime(start_time)} - ${formatTime(end_time)}</p>
                                </div>
                                <div class="custom-event-title m-0">
                                    <p href="${detailsUrl}" 
                                    style="font-size:10px; color: ${info.event.extendedProps.type === 'task' ? '#74b2d4' : '#93c21c'};">
                                    ${truncatedTitle}
                                      </p>
                                   
                                </div>
                            </div>`; 
                    }
                },

    
                    eventDrop: handleEventUpdate,
                    eventResize: handleEventUpdate,
                });
  
            calendar.render(); // Render after setting everything up
        }

        function handleEventUpdate(info) {
                Swal.fire({
                    title: "Geben Sie einen Grund für die Änderung an",
                    html: `
                        <textarea id="change_reason" class="swal2-textarea" placeholder="Enter reason for change"></textarea>
                    `,
                    showCancelButton: true,
                    confirmButtonText: "Speichern",
                    cancelButtonText: "Abbrechen",
                    preConfirm: () => {
                        const changeReason = document.getElementById("change_reason").value.trim();
                        if (!changeReason) {
                            Swal.showValidationMessage("Änderungsgrund ist erforderlich.");
                        }
                        return changeReason;
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        let taskId = info.event.id.split("-")[0];  // Extract only the task ID
                        let empPersonalId = info.event.extendedProps.emp_personal_id; 
                        let sameId = info.event.extendedProps.same_id;
                        const startDateTime = new Date(info.event.start);  
                        const endDateTime = info.event.end ? new Date(info.event.end) : startDateTime;  
                        const eventType = info.event.extendedProps.type;

                        // Extract date and time
                        const startDate = startDateTime.toISOString().split("T")[0];  
                        const startTime = startDateTime.toTimeString().split(" ")[0].substring(0, 5); 
                        const endDate = endDateTime.toISOString().split("T")[0];  
                        const endTime = endDateTime.toTimeString().split(" ")[0].substring(0, 5); 

                        fetch("{{ route('personal.task.change.appointment') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            },
                            body: JSON.stringify({
                                task_id: taskId, 
                                emp_personal_id: empPersonalId,
                                same_id: sameId,
                                start_date: startDate,
                                end_date: endDate,
                                start_time: startTime,
                                end_time: endTime,
                                change_reason: result.value,
                                type: eventType,
                            }),
                        })
                        .then(response => response.json())
                        .then((result) => {
                            if (result.success) {
                                Swal.fire("Success!", "Veranstaltung erfolgreich aktualisiert.", "success").then(() => {
                                    loadCalendarTasks();
                                });
                            } else {
                                Swal.fire("Error!", result.message || "Failed to update event.", "error");
                                info.revert();
                            }
                        })
                        .catch((error) => {
                            console.error("Error updating event:", error);
                            Swal.fire("Error!", "Failed to update event.", "error");
                            info.revert();
                        });
                    } else {
                        info.revert();
                    }
                });
            }

        
            function loadCalendarTasks(callback) {
                const employeeData = getSelectedEmployeeData();

                $.ajax({
                    url: "/get_personal_task_calendar",
                    type: "GET",
                    data: { employee_data: JSON.stringify(employeeData) },
                    success: function (response) {
                        const tasks = response.data.map(task => ({
                            id: task.id + "-" + task.start_date + "-" + task.start_time,
                            title: task.title,
                            start: task.start_time ? `${task.start_date}T${task.start_time}` : task.start_date,
                            end: task.end_date ? (task.end_time ? `${task.end_date}T${task.end_time}` : task.end_date) : task.start_date,
                            color: task.taskColor,
                            extendedProps: {
                                employees: task.employees,
                                appointment: task.appointment,
                                priority: task.priority,
                                public: task.public_view,
                                same_id: task.same_id,
                                type: task.type,
                                end_time: task.end_time,
                                start_time: task.start_time,
                                total_time: task.total_time,
                                emp_personal_id: task.emp_personal_id,
                                description: task.description,
                                customer_id: task.customer_id,
                            },
                        }));

                        initializeCalendar(tasks);

                        // ✅ Run callback if provided (to restore view and position)
                        if (typeof callback === "function") {
                            callback();
                        }
                    },
                    error: function (xhr) {
                        console.error("Failed to fetch tasks:", xhr.responseText);
                    },
                });
            }



        function adjustEventHeight(info) {
            let eventElement = info.el;
            let start = new Date(info.event.start);
            let end = new Date(info.event.end);

            if (!end) return; // Skip if no end time

            let duration = (end - start) / (1000 * 60); // Convert milliseconds to minutes
            let slotHeight = 50; // Adjust slot height (default FullCalendar slot height)
            let eventHeight = (duration / 30) * slotHeight; // Scale height based on duration

            eventElement.style.height = eventHeight + "px";
        }

        //delete function from Menu

        
            document.addEventListener('click', function (e) {
                let deleteButton = e.target.closest('#delete_event'); // Detect delete button click
                if (deleteButton) {
                    e.preventDefault(); // Prevent default behavior

                    let eventId = deleteButton.getAttribute('data-event-id'); // Get event ID
                    let eventType = deleteButton.getAttribute('data-event-type'); // Get event type (appointment or task)
                    let baseUrl = window.location.origin; // Get base URL (http://127.0.0.1:8000)

                    let deleteUrl = eventType === 'appointment'
                        ? `${baseUrl}/calendar/appointments/destroy/${eventId}`
                        : `${baseUrl}/calendar/personal_task_delete/${eventId}`;

                    // ✅ **Save current view type and visible date range**
                    let currentView = calendar.view.type; // View mode (day, week, month)
                    let currentDate = calendar.getDate(); // The current date being viewed
                    let visibleStart = calendar.view.currentStart; // The start of the visible range
                    let visibleEnd = calendar.view.currentEnd; // The end of the visible range

                    Swal.fire({
                        title: "Are you sure?",
                        text: "This action will permanently delete the event.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel",
                        confirmButtonColor: "#d92127",
                        cancelButtonColor: "#93c21c"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === "success") {
                                    // ✅ **Reload calendar tasks but keep the same view and range**
                                    loadCalendarTasks(() => {
                                        calendar.changeView(currentView); // Restore previous view
                                        calendar.gotoDate(currentDate); // Restore previous position
                                    });

                                    // ✅ **Show success message**
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "The event has been deleted successfully.",
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Error",
                                        text: "Failed to delete the event.",
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Something went wrong.",
                                });
                            });
                        }
                    });
                }
            });




      
        
        // Initial load for default user tasks only
        loadCalendarTasks();

        // Event delegation to handle dynamically loaded checkboxes
       document.addEventListener("change", function (event) {
            if (event.target.classList.contains("employee_check") || event.target.classList.contains("employeeAppointment")) {
                loadCalendarTasks();
            }
        });
    });
</script>
 
<!-- deleting event function  -->
 <script>
    document.addEventListener('click', function (e) {
    let deleteButton = e.target.closest('#delete_event'); // Detect delete button click
    if (deleteButton) {
        e.preventDefault(); // Prevent default behavior

        let eventId = deleteButton.getAttribute('data-event-id'); // Get event ID
        let eventType = deleteButton.getAttribute('data-event-type'); // Get event type (appointment or task)
        let baseUrl = window.location.origin; // Get base URL (http://127.0.0.1:8000)

        // **Decide the correct delete URL based on the event type**
        let deleteUrl = eventType === 'appointment' 
            ? `${baseUrl}/calendar/appointments/destroy/${eventId}`  
            : `${baseUrl}/calendar/personal_task_delete/${eventId}`;

        Swal.fire({
            title: "Are you sure?",
            text: "This action will permanently delete the event.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d92127",
            cancelButtonColor: "#93c21c"
        }).then((result) => {
            if (result.isConfirmed) {
                // **Perform AJAX request instead of redirecting**
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // **Remove event from FullCalendar**
                        let calendar = $('#calendar').fullCalendar ? $('#calendar') : document.getElementById('calendar');
                        if (calendar) {
                            let fullCalendar = $(calendar).fullCalendar('getCalendar');
                            let eventToRemove = fullCalendar.getEventById(eventId);
                            if (eventToRemove) {
                                eventToRemove.remove(); // Remove event from calendar
                            }
                        }

                        // **Show success message**
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: "The event has been deleted successfully.",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to delete the event.",
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Something went wrong.",
                    });
                });
            }
        });
    }
});


 </script>
 


 <!-- Serach and Filter by Employee and Task Title, Date :start -->
   <script> 
    // Function to fetch data based on search query and filter type
       function fetchData(searchQuery = '', filterType = 'employee') {
            const apiUrl = `/getEmployees?search=${searchQuery}&filter=${filterType}`;
            fetch(apiUrl)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then((result) => {
                    const employeeList = document.getElementById('search_emp_result');
                    employeeList.innerHTML = ''; // Clear the list

                    const data = result.data || [];
                    if (data.length > 0) {
                        data.forEach((item) => {
                            const listItem = document.createElement('div');
                            listItem.classList.add('list-item');

                            if (filterType === 'employee') {
                                listItem.innerHTML = `
                                    <div class="d-flex align-items-center m-0"> 
                                        <input type="checkbox" class="employee_check" data-id="${item.id}" id="check${item.id}" style="display: none;"> 

                                        <div class="avatar mr-1">
                                            <img src="/images/employee/${item.image}" alt="avatar img holder" width="25" height="25" 
                                            data-id="${item.id}" class="employee_checkbox" id="employeeCheck${item.id}">
                                        </div>

                                        <span>
                                            <span style="font-size:10px;">${item.name} </span>
                                            <small>
                                                <fieldset>
                                                    <div class="custom-control custom-checkbox" style="display: none;" id="appointmentWrapper${item.id}">
                                                        <input type="checkbox" class="custom-control-input employeeAppointment" 
                                                        name="employeeAppointment" id="employeeAppointment${item.id}" 
                                                        data-employee-id="${item.id}" data-filter-search="appointmentEmployee">
                                                        
                                                     <label class="custom-control-label" style="font-size:10px;" for="employeeAppointment${item.id}">Aufgabe</label>
                                                    </div>
                                                </fieldset>
                                            </small>
                                        </span>
                                    </div>
                                `;

                                // Attach event listener to image for toggling emp_active and checkbox selection
                                const employeeImage = listItem.querySelector(`#employeeCheck${item.id}`);
                                const employeeCheckbox = listItem.querySelector(`#check${item.id}`);

                                employeeImage.addEventListener('click', function () {
                                    this.classList.toggle('emp_active');
                                    employeeCheckbox.checked = this.classList.contains('emp_active');

                                    // Manually trigger the change event to ensure filtering works
                                    employeeCheckbox.dispatchEvent(new Event('change', { bubbles: true }));
                                });

                                // Handle checkbox change to update calendar when selection changes
                                employeeCheckbox.addEventListener('change', function () {
                                    toggleAppointmentCheckbox(item.id);
                                    loadCalendarTasks();
                                });

                                employeeList.appendChild(listItem);
                            }
                        });
                    } else {
                        employeeList.innerHTML = '<p>No results found.</p>';
                    }
                })
                .catch((error) => {
                    console.error('Error fetching data:', error);
                    const employeeList = document.getElementById('search_emp_result');
                    employeeList.innerHTML = '<p>Failed to fetch data. Please try again later.</p>';
                });
        }

        // Function to toggle appointment checkbox visibility
        function toggleAppointmentCheckbox(employeeId) {
            const appointmentWrapper = document.getElementById(`appointmentWrapper${employeeId}`);
            const employeeCheckbox = document.querySelector(`#check${employeeId}`);
            if (appointmentWrapper) {
                appointmentWrapper.style.display = employeeCheckbox.checked ? 'block' : 'none';
            }
        }



    // Show the correct input field based on the selected filter
    function toggleSearchInput(filterType) {
        const employeeSearchInput = document.querySelector('.employee_search_input');
        const taskSearchInput = document.querySelector('.task_search_input');
        const appointmentSearchInput = document.querySelector('.appointment_search_input');

        if (filterType === 'employee') {
            employeeSearchInput.style.display = 'block';
            taskSearchInput.style.display = 'none';
            appointmentSearchInput.style.display = 'none';
        } else if (filterType === 'task') {
            employeeSearchInput.style.display = 'none';
            appointmentSearchInput.style.display = 'none';
            taskSearchInput.style.display = 'block';
        }

        else if (filterType === 'appointment') {
            employeeSearchInput.style.display = 'none';
            taskSearchInput.style.display = 'none';
            appointmentSearchInput.style.display = 'block';

        }
    }

    // Add event listeners for the radio buttons
    document.querySelectorAll('input[name="filter"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            const filterType = this.value;
            toggleSearchInput(filterType); // Show the correct input
            fetchData('', filterType); // Fetch data with the selected filter type
        });
    });

    // Add event listeners for the search inputs
    document.querySelector('.employee_search_input input').addEventListener('input', function () {
        const searchQuery = this.value;
        fetchData(searchQuery, 'employee');
    });

    document.querySelector('.task_search_input input').addEventListener('input', function () {
        const searchQuery = this.value;
        fetchData(searchQuery, 'task');
    });

        document.querySelector('.appointment_search_input input').addEventListener('input', function () {
        const searchQuery = this.value;
        fetchData(searchQuery, 'appointment');
    });
    // Initial setup
    document.addEventListener('DOMContentLoaded', function () {
        toggleSearchInput('employee'); // Default to employee filter
        fetchData('', 'employee'); // Fetch initial employee data
    });
</script>


 <!-- Serach and Filter by Employee and Task Title, Date :end_date --> 
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

<!-- Information Popup  -->
 <script>
  document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("info_popup")) {
            let infoId = event.target.getAttribute("data-id");
            let infoType = event.target.getAttribute("data-type");

            fetch(`/get/info/${infoId}/${infoType}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let detailsTable = `
                        <table style="width:100%; border-collapse: collapse;">
                            <tr><th style="text-align:left; padding:5px;">Titel</th><td>${data.title}</td></tr>
                            <tr><th style="text-align:left; padding:5px;">Beschreibung</th><td>${data.description}</td></tr>
                            ${data.execution_type ? `<tr><th style="text-align:left; padding:5px;">Ausführungstyp</th><td>${data.execution_type}</td></tr>` : ""}
                            <tr><th style="text-align:left; padding:5px;">Startdatum</th><td>${data.start_date}</td></tr>
                            <tr><th style="text-align:left; padding:5px;">Enddatum</th><td>${data.end_date}</td></tr>
                            <tr><th style="text-align:left; padding:5px;">Startzeit</th><td>${data.start_time}</td></tr>
                            <tr><th style="text-align:left; padding:5px;">Endzeit</th><td>${data.end_time}</td></tr>
                        </table>
                    `;

                    Swal.fire({
                        title: "Beschreibung",
                        html: detailsTable,
                        icon: "info",
                        confirmButtonText: "OK",
                        customClass: {
                            popup: 'swal-wide' // Optional: CSS class to widen the modal
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            })
            .catch(error => {
                console.error("Error fetching event info:", error);
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        }
    });
});


 </script>
<!-- Information Popup: end  -->

 <!-- show map:  -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.addEventListener("click", function (event) {
            if (event.target.classList.contains("show_map")) {
                let appointmentId = event.target.getAttribute("data-id");

                // Show loading dialog
                Swal.fire({
                    title: "Fetching Location...",
                    text: "Please wait while we load the map...",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/get/map/${appointmentId}`, {
                    method: "GET",
                    headers: { "Content-Type": "application/json" }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let destination = { lat: parseFloat(data.latitude), lng: parseFloat(data.longitude) };

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                let origin = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };

                                // Once the location is retrieved, show the map
                                showMapWithRoute(origin, destination, data.title);
                            }, function () {
                                Swal.fire("Error", "Could not get your location.", "error");
                            });
                        } else {
                            Swal.fire("Error", "Geolocation is not supported by your browser.", "error");
                        }
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(error => {
                    console.error("Error fetching map data:", error);
                    Swal.fire("Error", "Something went wrong. Please try again.", "error");
                });
            }
        });
    });

    // Function to show the map with route and open Google Maps button
    function showMapWithRoute(origin, destination, locationTitle) {
        let googleMapsAPIKey = "AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo"; // Replace with your Google API Key
        let mapContainer = document.createElement("div");
        mapContainer.id = "map";
        mapContainer.style = "width: 100%; height: 400px; margin-top: 10px;";

        // Replace loading message with actual map
        Swal.fire({
            title: `Termin: ${locationTitle}`,
            html: `<div id="map" style="width: 100%; height: 400px;"></div>
                <p><strong>Distance:</strong> <span id="distance"></span></p>
                <p><strong>Estimated Time:</strong> <span id="duration"></span></p>
                <a href="https://www.google.com/maps/dir/?api=1&origin=${origin.lat},${origin.lng}&destination=${destination.lat},${destination.lng}&travelmode=driving" 
                    target="_blank" class="swal2-confirm swal2-styled">Open in Google Maps</a>`,
            icon: "info",
            didOpen: () => {
                let map = new google.maps.Map(document.getElementById("map"), {
                    center: origin,
                    zoom: 10,
                });

                let directionsService = new google.maps.DirectionsService();
                let directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                directionsService.route(
                    {
                        origin: origin,
                        destination: destination,
                        travelMode: google.maps.TravelMode.DRIVING,
                    },
                    function (response, status) {
                        if (status === "OK") {
                            directionsRenderer.setDirections(response);
                            let route = response.routes[0].legs[0];

                            document.getElementById("distance").textContent = route.distance.text;
                            document.getElementById("duration").textContent = route.duration.text;
                        } else {
                            console.error("Directions request failed due to " + status);
                            Swal.fire("Error", "Could not get directions.", "error");
                        }
                    }
                );
            },
            width: 600,
            showCancelButton: false,
            showConfirmButton: false,
        });
    }

  </script>

<!-- show map end  -->
 
 

<!-- APPOINTMENT CRUD SCRIPT  -->


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

<script>
    document.addEventListener("keydown", function (event) {
    const newTaskDiv = document.querySelector(".new_task");

    if (event.key === "Escape" && newTaskDiv.style.display === "block") {
        newTaskDiv.style.display = "none"; // Hide the new_task div
    }
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

        // if (!appointmentType) {
        //     errors.push('Bitte wählen Sie einen Termin-Typ aus.');
        // }

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

          
    });


   </script>

    <!-- Priority Script end  -->


 

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
            script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places";
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

<!-- Start Date and End date same value : start -->
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

 

    @endsection