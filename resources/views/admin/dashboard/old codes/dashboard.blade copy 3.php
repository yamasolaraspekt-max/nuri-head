@extends('admin.layouts.app')

@section('title') Employee Dashboard @endsection
@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="{{ asset('css/icon.min.css')}}" rel="stylesheet" type="text/css" />
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
 <script src="https://unpkg.com/feather-icons"></script>

 <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

  
<style> 
 
        #deadline_area, .end_time_area, .repeated_area, .reminder_area ,.add_calendar_area{
            display: none;
        }
        
        .black {
            color: #555555 !important;
        }
        
        .cards {
            background-color: white !important; 
            padding: 20px;
            border: 0;
            text-align: center; 
        }

        .cards h3 {
            margin: 0;
            font-size: 16px;
            color: #a1a1a1;
            font-weight: bold;
        }
        .active-title {
             margin: 0;
            font-size: 16px;
            color: #8fc73e !important;
            font-weight: bold;
        }

        .cards img {
          width: 67px;
            padding: 0px;
            margin: 0px;
        }

        .cards hr {
           margin-top: -1px;
            margin-bottom: 19px;
            border: none;
            background-color: #d5d5d5 !important;
            height:1px;
        }

        .cards ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }

        .cards ul li {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
        .card-cover{
            padding:5px;
        }
        
 
      
        /* Weather Style: end  */

        /* Calendar Data:  */
 

        .icon {
            width: 24px !important;
            height: 24px !important;
        }

       #todo_card {
            height: 556px; /* Fixed height for the card */
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Prevent scrolling on the card itself */
        }

        #todo_card .card-title {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #fff;
            padding: 10px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
            width: 86%;
            justify-self: center;
        }

        #todo_card .card-content {
            flex: 1; /* Allow the content area to grow */
            overflow-y: auto; /* Make the list scrollable */
            padding: 10px;
        }

        #todo_card .list-group {
            margin: 0; /* Remove default margin */
            padding: 0; /* Remove default padding */
            list-style: none; /* Remove bullets */
        }

        #todo_card .list-group-item {
            border-bottom: 1px solid #ddd; /* Optional: Add a bottom border for separation */
            padding: 12px; /* Adjust padding for better readability */
        }

        #todo_card .list-group-item:last-child {
            border-bottom: none; /* Remove border from the last item */
        }

        .complete { 
            text-decoration: line-through 2px #f02828; 
        }
    
   /* Make SweetAlert wider */
        .swal-wide {
            width: 800px !important;
            max-width: 90% !important;
        }

        /* Ensure table fits and adjusts in the modal */
        .swal-wide .table {
            width: 100%;
            table-layout: auto;
        }

        .swal-wide .table th,
        .swal-wide .table td {
            white-space: nowrap;
            text-align: left;
            padding: 8px;
        }
        

       .dragging {
            opacity: 0.8;
            transform: rotate(-3deg);
            transition: transform 0.2s;
        }

        .gu-mirror {
            position: fixed !important;
            margin: 0 !important;
            z-index: 9999 !important;
            opacity: 0.8 !important;
            transform: rotate(-3deg);
        }

        .note-settings {

           position: absolute !important;
            z-index: 1;
            bottom: 9px;
            font-size: 20px !important;
            right: 9px;

        }
        .note-settings:hover {
            color:#8fc73e !important;
        }

        .list-group-item:hover {
            background:white !important;
        }
 
 
        .link-image:hover, .link-image:hover {
            filter: invert(64%) sepia(16%) saturate(2445%) hue-rotate(169deg) brightness(94%) contrast(85%);
        }

        .link-image-active {
           filter: invert(52%) sepia(62%) saturate(509%) hue-rotate(61deg) brightness(98%) contrast(90%);

        }

        .event-line {
            border-top: 1px solid #d5d5d5;
            width: 79%;
            justify-self: center;
            align-self: anchor-center;
        }
       

        .no-reminder-icon-top {
        position: absolute !important;
            z-index: 1;
            bottom: 7px;
            right: 40px;

        }

         .no-repeat-icon-top {
        position: absolute !important;
            z-index: 1;
            bottom: 7px;
            right: 70px;

        }
       

        .line {
            width: 97%;
            border-bottom: 2px solid #d5d5d5;
            margin-left: 33px;
        }

        .note-saperator {
         height: 88px;
            border-right: 2px solid #d5d5d5;
            position: absolute;
            right: 20px;
        }

        #note_table tr td {
            padding:0;
        }

        .select2-dropdown {
            width:200px !important;
        }

        .select2-container--default .select2-selection--single{
            border:0 !important;
            background-color:transparent !important;
            text-align: right !important;
            

        }
        .select2-container--classic .select2-selection--single:focus, .select2-container--default .select2-selection--single:focus{
            box-shadow: 0 0 !important;
        }
        
        .select2-selection__arrow {
            display:none;
        }
        .users-list li img:hover{
            box-shadow: 0 0 !important;
        }
</style>


<style>
 


        .icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
        }
        .icon-box:before {
            content: '' !important;
        }

       
</style>


<style>
@keyframes pulse-red {
    0%   { transform: scale(1);     color: #dc2626; }
    50%  { transform: scale(1.2);   color: #dc2626; }
    100% { transform: scale(1);     color: #dc2626; }
}
</style>



<style>
    .calendar-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
    }

    .fc-calendar {
        width: 300px;
        border: 1px solid #f1f1f1; 
    }

    .fc .fc-toolbar {
        display: none !important;
    }

    .fc .fc-day-today {
        background: #f0f8ff !important;
    }

    .fc .fc-day-sun {
        color: red;
    }

    .navigation-buttons {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 10px;
    }

    .navigation-buttons button {
        padding: 6px 20px;
    }

    .fc-event-title {
            font-size: 0 !important;
        }
        .fc-event-main {
            display: flex;
            align-items: center;
        }

        .fc .fc-event.fc-event-background {
                opacity: 0.3;
                border-radius: 6px;
                pointer-events: none;
            }


</style>

<style>
    

    .fc .fc-toolbar {
        display: none !important;
    }

    .fc .fc-day-today {
        background: #f0f8ff !important;
    }

    .fc .fc-day-sun {
        color: red;
    }

    #eventDetails .card {
        max-width: 600px;
        margin: 0 auto;
    }

    .border-left {
        border-left: 5px solid;
    }

    .fc-daygrid-day.fc-day-has-event {
        background-color:rgb(231, 213, 122) !important;
    }

    .fc .fc-event.fc-event-background {
        opacity: 0.35 !important;
        pointer-events: none;
        border-radius: 4px;
    }


    .fc-daygrid-day.haveEvent {
            position: relative;
        }

 
        .fc-daygrid-day-events {
            display: none !important;
        }


        .fc-daygrid-day-frame {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* .fc-daygrid-day-number {
                display: inline-block;
                width: 28px;
                height: 28px;
                line-height: 28px;
                text-align: center;
                border-radius: 50%;
                font-weight: 500;
            } */


            .fc-daygrid-day.haveEvent .fc-daygrid-day-number {
                    background-color: #8fc73e;
                    color: white;
                    border-radius: 50%;
                    width: 24px;
                    height: 24px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto;
                }

        .fc-daygrid-day-number {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #eventDetailsCard {
                max-width: 100%;
                margin-top: 0.5rem;
            }

            @media (max-width: 768px) {
                #eventDetailsCard .text-sm {
                    font-size: 0.875rem;
                }
                #eventDetailsCard .text-xs {
                    font-size: 0.75rem;
                }
            }

            .fc-daygrid-day.selected-day {
                outline: 2px solid #8fc73e;
            }

</style>


<style>
    .todo-collaps .toggle-header {
        background: #8fc73e;
        color: white;
        padding: 10px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .todo-collaps .toggle-content {
        display: none;
        border: 1px solid #ccc;
        border-top: none;
        padding: 15px;
        background-color: #fafafa;
    }

    .todo-collaps.open .toggle-content {
        display: block;
    }

    .todo-collaps i {
        margin-right: 8px;
    }

    .todo-collaps .arrow {
        transition: transform 0.3s ease;
    }

    .todo-collaps.open .arrow {
        transform: rotate(90deg);
    }
</style>

<style>
    .priority-btn {
        padding: 6px 12px;
        background: #e0e0e0;
        border: 1px solid #ccc;
        border-radius: 4px;
        cursor: pointer;
    }

    .priority-dropdown .dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
    }

    .priority-dropdown .dropdown-item:hover {
        background-color: #f0f0f0;
    }
</style>


@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">

            </div>
        </div>
        <div class="content-body">
            <div class="max-w-8xl mx-auto px-4 py-8">
                <!-- KPI Cards Row -->
                @php
                $user = DB::table('employees')
                ->select('name', 'lastname', 'image')
                ->where('id', auth()->user()->name) // Double-check this!
                ->first();

                $full_name = $user ? $user->name . ' ' . $user->lastname : 'Benutzer';
                $image_path = $user ? asset('images/employee/' . $user->image) : asset('images/default-user.png');
                @endphp

             <!-- Top Row: Welcome + Charts -->
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 mb-6 text-center">
                    <!-- Welcome Card -->
                    <div class="bg-white  py-6 px-4 flex flex-col items-center justify-center col-span-2">
                        <img src="{{ $image_path }}" alt="Profilbild" class="w-16 h-16 rounded-full mb-2 shadow">
                        <h2 class="text-xl font-semibold text-gray-800">Willkommen, {{ $full_name }}!</h2>
                        <p class="text-gray-500 text-sm">Schön, dass Sie wieder da sind.</p>
                    </div>

                    <!-- Chart Cards -->
                    <div class="bg-white  py-6 px-4 flex flex-col items-center justify-center">
                        <canvas id="progressDonut" width="100" height="100"></canvas>
                        <p class="text-xs mt-1 text-green-600 font-semibold">Gesamt-Fortschritt</p>
                    </div>
                    <div class="bg-white  py-6 px-4 flex flex-col items-center justify-center">
                        <canvas id="chart_personal_task" width="100" height="100"></canvas>
                        <p class="text-xs mt-1">Aufgaben</p>
                    </div>
                    <div class="bg-white  py-6 px-4 flex flex-col items-center justify-center">
                        <canvas id="chart_appointment" width="100" height="100"></canvas>
                        <p class="text-xs mt-1">Termine</p>
                    </div>
                    
                    <a href="{{ url('employee_daily_plan') }}"
                        class="bg-white py-6 px-4 flex flex-col items-center justify-center group"
                        style="background-color: #74b2d4 !important;">
                            
                            <img src="{{ asset('images/icons/report.png') }}"
                                alt="Report Logo"
                                class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110 group-hover:rotate-1">
                            
                            <p class="text-xs mt-1 text-white">TAGESBERICHT</p>
                        </a> 
                </div>

                <!-- Goal List Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="col-span-3 bg-white  p-4">
                        <p class="text-primary text-sm font-semibold mb-1">Verbleibende Aufgabe</p>
                        <p class="text-xs text-gray-600 mb-3" id="todayPercent">...</p>
                        <div id="goalList" class="space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300" style="max-height: 300px;"></div>
                    </div>  
                    <div class="bg-white  py-0 px-0 flex flex-col items-center justify-center"> 
                           @include('admin.dashboard.employee.partials.notes') 
                    </div>
                </div>

           

        

                <!-- Task, Appointment, Project, Offer, Note Tabs -->
                <div class="mt-10">
                    <div id="tab-content">
                        <div class="flex flex-col items-center justify-center mb-1">
                            <!-- Tabs -->
                            <ul class="flex flex-wrap justify-center text-sm font-medium text-center space-x-2 mb-2" id="tabs">

                                @if (DB::table('user_rolls')
                                    ->where('user_id', auth()->user()->name)
                                    ->where('item_id', 'Programmer')
                                    ->exists())
                                    <li>
                                        <button class="tab-button px-4 py-2" data-tab="admin">
                                            ADMINISTRATOR
                                        </button>
                                    </li>
                                @endif

                                <li>
                                    <button class="tab-button active border-b-2 border-blue-500 text-blue-600 px-4 py-2"
                                        data-tab="all">
                                        ALLGEMEIN
                                    </button>
                                </li>

                                <li>
                                    <button class="tab-button px-4 py-2" data-tab="tasks">
                                        AUFGABEN
                                    </button>
                                </li>

                                <li>
                                    <button class="tab-button px-4 py-2" data-tab="appointments">
                                        TERMIN
                                    </button>
                                </li>

                                <li>
                                    <button class="tab-button px-4 py-2" data-tab="calendar">
                                        KALENDER
                                    </button>
                                </li>
                            </ul>


                            <!-- Search Bar -->
                            <div class="form-row align-items-center mb-3">

                                <!-- 🔍 Search Input -->
                                <div class="col-auto">
                                    <label class="sr-only" for="searchBar">Suche</label>
                                    <input type="text" class="form-control mb-2" id="searchBar" placeholder="🔍 Suche...">
                                </div>

                                <!-- 📅 Date Picker -->
                                <div class="col-auto">
                                    <label class="sr-only" for="searchDate">Datum</label>
                                    <input type="date" class="form-control mb-2" id="searchDate"
                                        value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                </div>

                                <!-- 📊 Zeitraum -->
                                <div class="col-auto">
                                    <label class="sr-only" for="searchOrder">Zeitraum</label>
                                    <select class="form-control mb-2" id="searchOrder" name="range">
                                        <option value="all">Alle anzeigen</option>
                                        <option value="today">Heute</option>
                                        <option value="week">Diese Woche</option>
                                        <option value="month">Dieser Monat</option>
                                    </select>
                                </div>
 

                                </div>

                        </div> 
                    </div>

                    <div id="tab-content"> 

                        <div class="tab-panel hidden " id="tab-admin">
                            @include('admin.dashboard.employee.partials.admin')
                        </div>
                        <div class="tab-panel " id="tab-all">
                            @include('admin.dashboard.employee.partials.all')
                        </div>
                        <div class="tab-panel hidden" id="tab-tasks"></div>
                        <div class="tab-panel hidden" id="tab-appointments"></div>
                        <div class="tab-panel hidden" id="tab-projects"></div>
                        <div class="tab-panel hidden" id="tab-offers"></div>
                    
                        <div class="tab-panel hidden" id="tab-calendar">
                            @include('admin.dashboard.employee.partials.calendar')
                        </div>

                    </div>
                </div>

                <!-- Event Modal -->
                <div id="eventModal"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center"
                    onclick="outsideModalClick(event)">
                    <div id="eventModalContent"
                        class="bg-white rounded-xl shadow-xl w-full max-w-md mx-auto p-6 relative">
                        <button onclick="closeEventModal()"
                            class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                        <div class="flex items-center gap-1 mb-4">
                            <img id="modalAvatar" src="" alt="Avatar"
                                class="w-12 h-12 rounded-full object-cover border" />

                            <div>
                                <h2 id="modalTitle" class="text-lg font-bold text-gray-800"></h2>
                                <p id="modalTime" class="text-sm text-gray-500"></p>
                            </div>
                            <div id="modalPeople" class="flex flex-wrap gap-1 mt-3"></div>


                        </div>
                        <div>
                            <p id="modalDescription" class="text-gray-700 text-sm"></p>
                        </div>
                    </div>
                </div> 
                <div class="modal fade text-left" id="newNote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel110" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h5 class="modal-title" id="myModalLabel110">Todo</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <form method="POST"  id="save_note_form">
                                @csrf
                                <input type="hidden" name="check_availability"  id="check_availability" value="false" >
                                <div class="modal-body">  
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-12">
                                                        <input type="hidden" name="color" id="color" value="#8fc73e">
                                                        <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
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
                                                <div class="col-12">
                                                    <div class="form-group"> 
                                                        <input type="text" id="note_title" class="form-control" name="title" placeholder="Titel">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group"> 
                                                        <textarea name="note" id="" cols="30" rows="5" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-12">
                                                    <div class="font-medium-2">
                                                        Kategorie <i class="feather icon-plus add_category" style="cursor: pointer;"></i>
                                                    </div>
                                                    
                                                    <fieldset class="form-group">
                                                    <select class="form-control category_id" id="category_id" name="category_id">
                                                        <!-- Options will be dynamically loaded here -->
                                                    </select>

                                                    </fieldset>
                                                </div>

                                                <hr>
                                                
                                                <div class="col-md-12">
                                                    <div class="todo-collaps" id="todoDetailsCollapse">
                                                        <div class="toggle-header" onclick="toggleTodoCollapse('todoDetailsCollapse')">
                                                            <span><i class="feather icon-settings"></i> Details</span>
                                                            <span class="arrow">▶</span>
                                                        </div>
                                                        <div class="toggle-content">
                                                            <table class="table mb-0" style="width:100%">
                                                                <!-- Deadline -->
                                                                <tr>
                                                                    <td><i class="feather icon-calendar"></i> Datum</td>
                                                                    <td class="text-right">
                                                                        <input type="checkbox" id="toggle_deadline" onchange="toggleSection('deadline_section')">
                                                                    </td>
                                                                </tr>
                                                                <tr id="deadline_section" class="todo-toggle-section">
                                                                    <td colspan="2"><input type="date" class="form-control" name="deadline"></td>
                                                                </tr>

                                                                <!-- End Time -->
                                                                <tr>
                                                                    <td><i class="feather icon-clock"></i> Uhrzeit</td>
                                                                    <td class="text-right">
                                                                        <input type="checkbox" id="toggle_end_time" onchange="toggleSection('end_time_section')">
                                                                    </td>
                                                                </tr>
                                                                <tr id="end_time_section" class="todo-toggle-section">
                                                                    <td colspan="2"><input type="time" class="form-control" name="end_time"></td>
                                                                </tr>

                                                                <!-- Add to Calendar -->
                                                                <tr>
                                                                    <td><i class="feather icon-plus"></i> Zum Kalender hinzufügen</td>
                                                                    <td class="text-right">
                                                                        <input type="checkbox" id="toggle_calendar" onchange="toggleSection('calendar_section')">
                                                                    </td>
                                                                </tr>
                                                                <tr id="calendar_section" class="todo-toggle-section">
                                                                    <td colspan="2"><input type="date" class="form-control" name="add_calendar_date"></td>
                                                                </tr>

                                                                <!-- Repeat -->
                                                                <tr>
                                                                    <td><i class="feather icon-refresh-cw"></i> Wiederholt</td>
                                                                    <td class="text-right">
                                                                        <input type="checkbox" id="toggle_repeat" onchange="toggleSection('repeat_section')">
                                                                    </td>
                                                                </tr>
                                                                <tr id="repeat_section" class="todo-toggle-section">
                                                                    <td colspan="2">
                                                                        <select class="form-control" name="repeat">
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
                                                                </tr>

                                                                <!-- Reminder -->
                                                                <tr>
                                                                    <td><i class="fa fa-clock-o"></i> Erinnerung</td>
                                                                    <td class="text-right">
                                                                        <input type="checkbox" id="toggle_reminder" onchange="toggleSection('reminder_section')">
                                                                    </td>
                                                                </tr>
                                                                <tr id="reminder_section" class="todo-toggle-section">
                                                                    <td colspan="2">
                                                                        <label>Datum:</label>
                                                                        <input type="date" class="form-control mb-1" name="reminder_date">
                                                                        <label>Zeit:</label>
                                                                        <input type="time" class="form-control" name="reminder_time">
                                                                    </td>
                                                                </tr>

                                                                <!-- Priority -->
                                                                <tr>
                                                                    <td><i class="feather icon-flag"></i> Priorität</td>
                                                                    <td class="text-right">
                                                                        <input type="hidden" name="priority" id="priority" value="normal">
                                                                        <div style="position: relative; display: inline-block;">
                                                                            <button id="priorityDropdownBtn" class="priority-btn" onclick="togglePriorityDropdown()" type="button">
                                                                                ⚫ Keiner ▼
                                                                            </button>
                                                                            <div id="priorityDropdown" class="priority-dropdown" style="display:none; position: absolute; right: 0; z-index: 100; background: white; border: 1px solid #ccc; border-radius: 4px; min-width: 120px;">
                                                                                <div class="dropdown-item" onclick="setPriorityDropdown('normal', '⚫ Keiner')">⚫ Keiner</div>
                                                                                <div class="dropdown-item" onclick="setPriorityDropdown('medium', '🟡 Medium')">🟡 Medium</div>
                                                                                <div class="dropdown-item" onclick="setPriorityDropdown('high', '🔴 Hoch')">🔴 Hoch</div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
 
                                            </div>
                                        </div>
                                
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                    <button type="button"  class="btn btn-success" id="save_note_button">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> 

                <div class="modal fade text-left" id="updateCategoryModal" tabindex="-1" role="dialog" aria-labelledby="updateCategoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h5 class="modal-title" id="updateCategoryModalLabel">Notizkategorie ändern</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateCategoryForm">
                                        <div class="col-md-12 col-12">
                                            <div class="font-medium-2">
                                                Kategorie 
                                            </div>
                                            
                                            <fieldset class="form-group">
                                            <select class="form-control category_date" id="update_category_id" name="category_id">
                                                <!-- Options will be dynamically loaded here -->
                                            </select> 
                                            </fieldset>
                                        </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                <button type="button" id="update_category" class="btn btn-success">Speichern</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade text-left" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h5 class="modal-title" id="categoryModalLabel">Bearbeiten</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="categoryForm">
                                    <div class="form-group">
                                        <label for="category_name">Kategoriename</label>
                                        <input type="text" id="category_name" class="form-control"   >
                                    </div>
                                    <div class="form-group">
                                        <label for="type">Typ</label>
                                        <input type="text" id="type" class="form-control"  value="Normal" >
                                    </div>
                                    <div class="form-group">
                                        <label for="color">Farbe</label>
                                        <input type="color" id="color" class="form-control" style="height: 40px;" >
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                <button type="button" id="saveCategory" class="btn btn-success">Speichern</button>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="modal fade text-left" id="updateSettingModal" tabindex="-1" role="dialog" aria-labelledby="updateSettingModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary white">
                                <h5 class="modal-title" id="updateSettingModalLabel">Bearbeiten</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateSettingForm">
                                    <input type="hidden" name="check_emp"  id="check_emp" value="false" >
                                    <table class="table">
                            
                                        <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="deadline_area"> 
                                            <td>Datum</td>
                                            <td style="text-align:right"  > 
                                                    <input type='date' class="form-control pickatime" name="deadline"/> 
                                                </div>
                                            </td>
                                        </tr> 
                                        <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" class="end_time_area"> 
                                            <td>Zeit</td>
                                            <td style="text-align:right"> 
                                                    <input type='time' class="form-control pickatime" name="end_time"/> 
                                                </div>
                                            </td>
                                        </tr>

                                    <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                            <td><i class="feather icon-plus"></i>Zum Kalender hinzufügen</td>
                                            <td style="text-align:right">
                                                <div class="checkbox">
                                                    <input type='date' class="form-control pickatime" name="add_calendar_date"/> 
                                                </div>  
                                            </td>
                                        </tr>

                                        

                                        <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                            <td><i class="feather icon-refresh-cw"></i> Wiederholt</td>
                                            <td style="text-align:right">
                                                <select name="repeat" class="form-control" id="wiederholung">
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
                                        </tr>
                                        

                                        
                                        <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                            <td><i class="fa fa-clock-o"></i> Erinnerung</td>
                                            <td style="text-align:right">
                                                <label for="reminder_area" style="float:left;">Datum:</label>
                                                <input type="date" name="reminder_date" class="form-control">
                                                <label for="reminder_area"  style="float:left;" class="mt-1">Zeit:</label>
                                                <input type="time" name="reminder_time" class="form-control">
                                            </td>
                                        </tr>
                                    

                                        <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                            <td><i class="feather icon-flag"></i>Priorität</td>
                                            <td style="text-align:right">
                                                <input type="hidden" name="priority" value="normal">
                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1 "> 
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                        
                                                    <i class="fa fa-battery-empty"></i></button>
                                                    <div class="dropdown-menu " x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -233px, 0px);">
                                                        <span class="dropdown-item" data-value="normal">
                                                        <i class="fa fa-battery-empty"></i> Keiner
                                                        </span> 
                                                        <span class="dropdown-item" data-value="medium">
                                                        <i class="fa fa-battery-half"></i> Medium
                                                        </span>

                                                        <span class="dropdown-item" data-value="high">
                                                        <i class="fa fa-battery-full"></i> Hoch
                                                        </span>
                                                        
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>  
                                            
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                <button type="button" id="save_note_settings" class="btn btn-success">Speichern</button>
                            </div>
                        </div>
                    </div>
                </div> 


            </div>


        </div>
    </div>
</div>

<div id="toastSuccess" class="hidden fixed bottom-5 right-5 bg-primary text-white text-sm rounded px-4 py-2 shadow-xl z-50">
  Als erledigt markiert!
</div>


@endsection

@section('script')

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
<!-- Alpine Core -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Collapse Plugin (must come after Alpine) -->
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>

 
<script>
document.addEventListener("DOMContentLoaded", () => {
     feather.replace();

    const tabs = document.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');

    // ✅ Auto-activate default tab on page load
    const defaultTab = document.querySelector('.tab-button.active')?.dataset.tab || 'all';
    activateTab(defaultTab);

    // ✅ Set up click handler
    tabs.forEach(button => {
        button.addEventListener('click', () => {
            const selectedTab = button.dataset.tab;
            activateTab(selectedTab);
        });
    });

    function activateTab(tabName) {
        tabs.forEach(btn => {
            btn.classList.remove('border-b-2', 'border-blue-500', 'text-blue-600', 'active');
            if (btn.dataset.tab === tabName) {
                btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-600', 'active');
            }
        });

        panels.forEach(panel => panel.classList.add('hidden'));
        const activePanel = document.getElementById(`tab-${tabName}`);
        if (activePanel) activePanel.classList.remove('hidden');
    }
});
</script>


<script>
    const currentUserName = @json(auth()->user()->name);
</script>


<script>
    const searchBar = document.getElementById('searchBar');
    const searchDate = document.getElementById('searchDate');
    const searchOrder = document.getElementById('searchOrder'); 
    const noteList = $('#personal-note-list');

    const saveNoteButton = $('#save_note_button'); // Save note button
    const saveNoteModal = $('#newNote'); // Save note modal
    const saveNoteForm = $('#save_note_form'); // Save note form
    const updateCategoryModal = $('#updateCategoryModal'); // Reference to the modal
    const categorySelect = $('#update_category_id'); // Select dropdown inside the modal
    
   


    // Ensure a default date is set
    if (!searchDate.value) {
        searchDate.value = new Date().toISOString().split('T')[0];
    }

    // Track current tab state
    let activeTab = 'all';

    // Event listeners for filters
    searchBar.addEventListener('input', debounce(applyFilter, 300));
    searchDate.addEventListener('change', applyFilter);
    searchOrder.addEventListener('change', applyFilter);

    // Tab switching
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', function () {
            const tab = this.dataset.tab;
            activeTab = tab;
            switchTab(tab);
        });
    });

    function switchTab(tab) {
        document.querySelectorAll('.tab-button').forEach(t => t.classList.remove('border-b-2', 'border-blue-600'));
        document.querySelector(`[data-tab="${tab}"]`).classList.add('border-b-2', 'border-blue-600');

        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        const panel = document.getElementById(`tab-${tab}`);
        if (panel) {
            panel.classList.remove('hidden');
            loadTabContent(tab); // Load data with filters applied
        }
    }

    function getFilterUrl(tab) {
        const keyword = searchBar.value;
        const selectedDate = searchDate.value;
        const range = searchOrder.value || 'all'; // fallback if not selected

        const params = new URLSearchParams({
            tab,
            search: keyword,
            date: selectedDate,
            range
        });
        if (tab === 'notes') {
            requestAnimationFrame(() => {
                const noteList = document.getElementById('personal-note-list');
                if (noteList) {
                    loadNotes();
                }
            });
        }


        return `/dashboard/load-tab?${params.toString()}`;
    }


    function applyFilter() {
        loadTabContent(activeTab);
    }

    function loadTabContent(tab) {
        const panel = document.getElementById(`tab-${tab}`);
        if (!panel) return;

        panel.innerHTML = `<div class="p-4 text-center text-gray-500">🔄 Wird geladen...</div>`;

        fetch(getFilterUrl(tab))
            .then(res => res.text())
            .then(html => {
                panel.innerHTML = html;
                feather.replace();

                const noteList = document.getElementById('personal-note-list');
                if (noteList) {
                    new Sortable(noteList, {
                        handle: '.drag-handle',
                        animation: 150,
                        onEnd: function (evt) {
                            // optional reorder logic
                        }
                    });
                }

                if (tab === 'notes') {
                    requestAnimationFrame(() => {
                        if (document.getElementById('personal-note-list')) {
                            loadNotes();
                        }
                    });
                }

                // ✅ If calendar tab, render calendar after content is injected
                if (tab === 'calendar') {
                    setTimeout(() => {
                        renderQuarterCalendars(currentDate);

                        document.getElementById('prevQuarter')?.addEventListener('click', () => {
                            currentDate.setMonth(currentDate.getMonth() - 3);
                            renderQuarterCalendars(currentDate);
                        });

                        document.getElementById('nextQuarter')?.addEventListener('click', () => {
                            currentDate.setMonth(currentDate.getMonth() + 3);
                            renderQuarterCalendars(currentDate);
                        });
                    }, 50); // wait for DOM to be ready
                }

            })
            .catch(err => {
                panel.innerHTML = `<div class="text-red-500 p-4 text-sm">❌ Fehler beim Laden.</div>`;
                console.error('Error loading content:', err);
            });
    }


 
    // Calendar 
    const today = new Date(); // today is used throughout
        let currentDate = new Date(today.getFullYear(), today.getMonth(), 1);
        let calendarInitialized = false;
        let allEvents = [];

        function getISOWeekNumber(date) {
            const temp = new Date(date.getTime());
            temp.setHours(0, 0, 0, 0);
            temp.setDate(temp.getDate() + 3 - (temp.getDay() + 6) % 7);
            const week1 = new Date(temp.getFullYear(), 0, 4);
            return 1 + Math.round(((temp - week1) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
        }

        function loadEvents(start, end) {
            return fetch(`/get_personal_task_calendar_mini?start_date=${start}&end_date=${end}`)
                .then(res => res.json())
                .then(res => {
                    console.log("📥 RAW Events From API:", res.data);
                    return res.data.map(ev => {
                        const endDate = ev.due_date || ev.end_date || ev.start_date;
                        const endTime = ev.end_time || '23:59:59';
                        return {
                            id: ev.id,
                            title: ev.title,
                            start: `${ev.start_date}T${ev.start_time ?? '00:00:00'}`,
                            end: `${endDate}T${endTime}`,
                            color: ev.taskColor || '#3b82f6',
                            type: ev.type,
                            extendedProps: ev
                        };
                    });
                });
        }

        function renderEventCard(events) {
            console.log("🧩 Rendering event card:", events);
            const container = document.getElementById('eventDetailsCard');
            container.innerHTML = '';

            if (!events.length) {
                container.innerHTML = `
                    <div class="text-center text-sm text-gray-500 p-3 border rounded bg-gray-50">
                        ❌ Keine Termine an diesem Tag.
                    </div>`;
                return;
            }

            events.forEach(event => {
                let type = event.type || event.extendedProps?.type || '';
                let icon = '', typeLabel = '', detailUrl = '';

                switch (type) {
                    case 'task':
                        icon = '📝'; typeLabel = 'Aufgabe'; detailUrl = `/personal_task_details/${event.id}`; break;
                    case 'appointment':
                        icon = '📅'; typeLabel = 'Termin'; detailUrl = `/appointment_details/${event.id}`; break;
                    case 'holiday':
                        icon = '🌴'; typeLabel = 'Urlaub'; break;
                    case 'sick':
                        icon = '🤒'; typeLabel = 'Krank'; break;
                    case 'public_holiday':
                        icon = '🏛️'; typeLabel = 'Feiertag'; break;
                    default:
                        icon = '📌'; typeLabel = 'Event';
                }

                const html = `
                    <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-3 mb-2 flex justify-between items-center">
                        <div>
                            <div class="text-sm font-semibold text-gray-800 flex items-center gap-1">
                                <span class="text-lg">${icon}</span> ${event.title}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                ${typeLabel} • ${event.extendedProps?.due_date || event.end.split('T')[0]}
                                ${event.extendedProps?.end_time && event.extendedProps.end_time !== '23:59:59' ? ` • ${event.extendedProps.end_time}` : ''}
                            </div>
                        </div>
                        ${detailUrl ? `<a href="${detailUrl}" class="text-sm text-blue-600 hover:underline whitespace-nowrap">Details →</a>` : ''}
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            });
        }


        function renderQuarterCalendars(baseDate = new Date(today.getFullYear(), today.getMonth(), 1)) {
    const wrapper = document.getElementById('calendarWrapper');
    const eventCard = document.getElementById('eventDetailsCard');

    if (!wrapper || wrapper.offsetParent === null) return;
    wrapper.innerHTML = '';
    eventCard.innerHTML = '';

    const startRange = new Date(baseDate.getFullYear(), baseDate.getMonth() - 1, 1);
    const endRange = new Date(baseDate.getFullYear(), baseDate.getMonth() + 2, 0);
    const startStr = startRange.toISOString().split('T')[0];
    const endStr = endRange.toISOString().split('T')[0];

    loadEvents(startStr, endStr).then(events => {
        allEvents = events;

        for (let offset = -1; offset <= 1; offset++) {
            const monthDate = new Date(baseDate.getFullYear(), baseDate.getMonth() + offset, 1);
            const calYear = monthDate.getFullYear();
            const calMonth = monthDate.getMonth();

            const calendarBox = document.createElement('div');
            calendarBox.classList.add('fc-calendar');
            calendarBox.style.padding = '10px';

            const header = document.createElement('div');
            header.className = 'text-center fw-bold mb-2';
            header.textContent = `${monthDate.toLocaleString('de-DE', { month: 'long' })} ${calYear}`;
            calendarBox.appendChild(header);

            const calendarEl = document.createElement('div');
            calendarBox.appendChild(calendarEl);
            wrapper.appendChild(calendarBox);

            const filteredEvents = events.filter(e => {
                const evStart = new Date(e.start);
                const evEnd = new Date(e.end || e.start);
                const monthStart = new Date(calYear, calMonth, 1);
                const monthEnd = new Date(calYear, calMonth + 1, 0);
                return evEnd >= monthStart && evStart <= monthEnd;
            });

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: monthDate.toISOString().split('T')[0],
                headerToolbar: false,
                height: 'auto',
                firstDay: 1,
                locale: 'de', 

                fixedWeekCount: false,
                showNonCurrentDates: false,
                events: filteredEvents,

                dayCellDidMount(info) {
                    const cellDate = info.date.toISOString().split('T')[0];

                    const hasEvent = allEvents.some(ev => {
                        const due = ev.extendedProps?.due_date || (ev.end?.split('T')[0]);
                        return due === cellDate;
                    });

                    if (hasEvent) info.el.classList.add('haveEvent');

                    const todayStr = new Date().toISOString().split('T')[0];
                    if (cellDate === todayStr) {
                        info.el.classList.add('selected-day');
                    }
                },

                dateClick(info) {
                    document.querySelectorAll('.fc-daygrid-day').forEach(el => el.classList.remove('selected-day'));
                    info.dayEl.classList.add('selected-day');

                    const clickedDate = info.dateStr;
                    const matched = allEvents.filter(e => {
                        const due = e.extendedProps?.due_date || (e.end?.split('T')[0]);
                        return due === clickedDate;
                    });

                    renderEventCard(matched);
                },

                eventDidMount(info) {
                    info.el.innerHTML = '';
                    const dot = document.createElement('div');
                    dot.style.width = '6px';
                    dot.style.height = '6px';
                    dot.style.borderRadius = '50%';
                    dot.style.backgroundColor = info.event.backgroundColor || '#3b82f6';
                    dot.style.margin = '0 auto';
                    info.el.appendChild(dot);
                }
            });

            calendar.render();

            if (offset === 0) {
                setTimeout(() => {
                    calendarBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }

        // ✅ Auto show today's events
        const todayStr = new Date().toISOString().split('T')[0];
        const todayEvents = allEvents.filter(e => {
            const due = e.extendedProps?.due_date || (e.end?.split('T')[0]);
            return due === todayStr;
        });
        renderEventCard(todayEvents);
    });
}


 
    // Function to load notes
    function loadNotes() {
        $.ajax({
            url: "{{ route('notes') }}",
            method: "GET",
            success: function(response) {
                const noteList = $('#personal-note-list');
                    if (!noteList.length) return;
                    noteList.empty();


                response.notes.forEach(note => {
                    noteList.append(`
                        <li class="list-group-item" data-id="${note.id}" style="border:0;cursor:pointer; border-left: 7px solid ${note.color};    margin-left: 33px; padding:0; margin-top:26px; margin-bottom:42px;" > 
                            <div class="media" style="margin-bottom:2px; cursor:pointer; padding-left:10px;  "> 
                                <div class="top" style="position: absolute;right: 20px;bottom: -9px;">
                                  <i class="feather icon-settings note-settings float-right" data-id="${note.id}" style=" color: #d5d5d5; font-size:19px !important;"></i>  
                                    ${note.reminder_date || note.reminder_time ? `
                                        <small class="no-reminder-icon-top"
                                                data-id="${note.id}"
                                                data-toggle="tooltip" 
                                                title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                            <i class="feather icon-bell primary" style=" font-size:19px !important;"  >
                                            </i>  
                                        </small>` 
                                        : ''} 

                                         ${note.repeat ? `
                                            <small class="no-repeat-icon-top" data-toggle="tooltip" data-id="${note.id}" id="no_repeat"
                                                    title="Wiederholung: ${note.repeat}">
                                                <i class="fa fa-refresh secondary" style=" font-size:19px !important;" >
                                                </i>  
                                            </small>` 
                                            : ''}
                                </div>
                                <div class="media-body">
                                    <div style="position: relative;">
                                        <div class="note-details" style="display: flex;align-items: center;justify-content: flex-start;">
                                            <fieldset>
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                       <input type="checkbox" class="done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}  >
                                                        <span class="vs-checkbox vs-checkbox-sm">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span> 
                                                    </div>
                                                </fieldset>
                                          
                                            <span class="badge badge-warning editing-badge" style="position: absolute; top: -20px; left: 0; display: none;">Editing...</span>
                                            <h5 class="mt-0 title-field ${note.is_done == 1 ? 'complete' : ''}" data-id="${note.id}" data-field="title" style="font-size: 14px; color:#555555;margin-left:1px;">${note.title}</h5>
                                        </div>
                                    </div>
                                    <div style="position: relative;">
                                        <span class="badge badge-warning editing-badge" style="position: absolute; top: -20px; left: 0; display: none;">Editing...</span>
                                        <p class="note-field" data-id="${note.id}" data-field="note" style="font-size: 13px; 15px; color:#555555; margin-left:22px;">${note.note}</p> 
                                    </div>
                                    <div class="mt-2">
                                         <table class="table table-borderless" id="note_table">
                                            <tr>
                                                <td>
                                                        <p class="mr-1 change-date"
                                                        data-id="${note.id}"
                                                        ><small><i class="feather icon-calendar ${note.add_calendar_date ? 'primary' : ''}"></i> ${note.deadline || 'Kein Fälligkeitsdatum'}</small></p>
                                                    
                                                </td>
                                                <td>
                                                        <p class="mr-1 change-time"
                                                        data-id="${note.id}"
                                                        ><small><i class="feather icon-clock"></i> ${note.end_time || 'Keine Endzeit'}</small></p> 
                                                </td>
                                                <td>
                                                    <p class="mr-1">
                                                        ${note.reminder_date || note.reminder_time ? `
                                                            <small class="no-reminder-icon"
                                                                    data-id="${note.id}"
                                                                    data-toggle="tooltip" 
                                                                    title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                                                <i class="feather icon-bell primary"  >
                                                                </i> Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}
                                                            </small>` 
                                                            : ''}
                                                    </p> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                            <p class="mr-1 updateCategoryModal" 
                                                            data-category-id="${note.category_id}"
                                                            data-id="${note.id}"
                                                            ><small><i class="feather icon-slack" >
                                                            </i> ${note.category_name || 'Standard'}</small>
                                                        </p> 
                                                </td>
                                                <td>
                                                        <p>
                                                        ${note.repeat ? `
                                                            <small class="no-repeat-icon-top" data-toggle="tooltip" data-id="${note.id}" id="no_repeat"
                                                                    title="Wiederholung: ${note.repeat}">
                                                                <i class="fa fa-refresh warning"  >
                                                                </i> Wiederholung: ${note.repeat}
                                                            </small>` 
                                                            : ''}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                </div>
                                <div class="note-saperator"></div>
                                <div class="media-footer" style="position: absolute;display: flex;right: -13px;top: -11px;flex-direction: column;"> 

                                     <button type="button" class="btn btn-icon btn-icon rounded-circle    drag-handle" data-id="${note.id}" style="    color: #d5d5d5;">
                                        <i class="feather icon-move" ></i>
                                    </button>
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle     note-color" data-id="${note.id}" style="    color: #d5d5d5;">
                                        <i class="feather icon-aperture" ></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle    delete_note" data-id="${note.id}" style="    color: #d5d5d5;">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </div>
                            </div>
                         
                        </li>
                           <div class="line"></div>
                    `);
                });
            },
            error: function() {
                Swal.fire('Fehler',
                    'Notizen konnten nicht geladen werden. Bitte versuche es erneut.', 'error');
            }
        });
    }


    $(document).on('click', '.updateCategoryModal', function() {
        const noteId = $(this).data('id');
        const categoryId = $(this).data('category-id');

        // Fetch categories and pre-select the current one
        $.ajax({
            url: `{{ url('/fetch_note_category') }}/${noteId}/${categoryId}`,
            method: "GET",
            success: function(response) {
                // Clear and populate the category dropdown
                categorySelect.empty();
                response.forEach(category => {
                    const isSelected = category.id === categoryId ? 'selected' : '';
                    categorySelect.append(
                        `<option value="${category.id}" ${isSelected}>${category.category_name}</option>`
                    );
                });

                // Store noteId in modal for reference
                updateCategoryModal.data('note-id', noteId);
                updateCategoryModal.modal('show'); // Show the modal
            },
            error: function() {
                Swal.fire('Fehler', 'Kategorien konnten nicht geladen werden.', 'error');
            }
        });
    });

    // Handle category update
    $('#update_category').on('click', function() {
        const noteId = updateCategoryModal.data('note-id');
        const selectedCategoryId = categorySelect.val();

        if (!selectedCategoryId) {
            Swal.fire('Fehler', 'Bitte wählen Sie eine Kategorie aus.', 'error');
            return;
        }

        $.ajax({
            url: `{{ url('/fetch_note_category') }}/${noteId}/${selectedCategoryId}`,
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(response) {
                Swal.fire('Erfolgreich', 'Die Kategorie wurde aktualisiert.', 'success');
                updateCategoryModal.modal('hide'); // Hide the modal
                loadNotes(); // Reload the notes
            },
            error: function() {
                Swal.fire('Fehler', 'Die Kategorie konnte nicht aktualisiert werden.',
                    'error');
            }
        });
    });

    $(document).on('click', '.no-repeat-icon-top', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Möchten Sie die Wiederholung für diese Notiz entfernen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, entfernen!',
            cancelButtonText: 'Abbrechen',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/notes_no_repeat') }}/" + noteId, // Fixed
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        Swal.fire('Erfolgreich!',
                            'Die Wiederholung wurde entfernt.', 'success');
                        loadNotes(); // Reload notes
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Wiederholung konnte nicht entfernt werden.',
                            'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.no-reminder-icon-top', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Möchten Sie die Erinnerungsoption für diese Notiz deaktivieren?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, entfernen!',
            cancelButtonText: 'Abbrechen',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/notes_no_reminder') }}/" + noteId, // Fixed
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        Swal.fire('Erfolgreich!',
                            'Die Erinnerungsoption wurde entfernt.', 'success');
                        loadNotes(); // Reload notes
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Erinnerungsoption konnte nicht entfernt werden.',
                            'error');
                    }
                });
            }
        });
    });


    // Function to toggle "done" status
    $(document).on('change', '.done-checkbox', function() {
        const noteId = $(this).data('id');
        const isDone = $(this).is(':checked') ? 1 : 0;

        console.log(`Updating note ${noteId} to is_done: ${isDone}`); // Debugging

        $.ajax({
            url: `{{ url('/notes_done') }}/${noteId}`, // Update the done route
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}",
                is_done: isDone,
            },
            success: function(response) {
                console.log('Note updated successfully:', response);

                Swal.fire({
                    icon: 'success',
                    title: 'Status aktualisiert',
                    text: `Die Aufgabe wurde ${isDone ? 'als erledigt' : 'als unerledigt'} markiert.`,
                });

                // Reload notes after successful update
                loadNotes();
            },
            error: function(xhr, status, error) {
                console.error('Error updating note:', {
                    xhr,
                    status,
                    error
                });
                Swal.fire('Fehler',
                    'Der Status konnte nicht aktualisiert werden. Bitte versuche es erneut.',
                    'error');
            }
        });
    });


    // Double-click functionality for title and note
    $(document).on('dblclick', '.title-field, .note-field', function() {
        const $element = $(this);
        const id = $element.data('id');
        const field = $element.data('field'); // Get the field name (title or note) as a string
        const originalValue = $element.text();


        // Add a badge indicating editing
        const badge = $element.siblings('.editing-badge');
        badge.show();

        // Replace with an input for editing
        const input = $(`<input type="text" class="form-control" value="${originalValue}">`);
        $element.replaceWith(input);
        input.focus();

        // Handle saving on Enter or blur
        input.on('blur keydown', function(e) {
            if (e.type === 'blur' || e.key === 'Enter') {
                const newValue = input.val().trim();

                // If the value hasn't changed, just revert the input
                if (newValue === originalValue || newValue === '') {
                    input.replaceWith($element);
                    badge.hide();
                    return;
                }

                // Send AJAX request to update the note
                $.ajax({
                    url: field === 'title' ?
                        `{{ url('/notes_update_name') }}/${id}` :
                        `{{ url('/notes_update_note') }}/${id}`,
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        [field]: newValue // Correctly set the field name dynamically
                    },
                    success: function() {
                        $element.text(newValue);
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Erfolgreich', 'Die Notiz wurde aktualisiert.',
                            'success');
                    },
                    error: function() {
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Fehler',
                            'Die Notiz konnte nicht aktualisiert werden.',
                            'error');
                    }
                });
            }
        });
    });


    $('#save_note_form').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent default form submission
        }
    });

    // Save new note
    saveNoteButton.on('click', function() {
        const formData = saveNoteForm.serialize();

        // Debug: Log the value of check_availability
        console.log('check_availability value:', $('#check_availability').val());

        $.ajax({
            url: "{{ route('notes.store') }}",
            method: "POST",
            data: formData,
            success: function(response) {
                Swal.fire('Erfolgreich', 'Die Notiz wurde gespeichert.', 'success');
                saveNoteModal.modal('hide');
                saveNoteForm[0].reset(); // Reset the form
                $('#check_availability').val('false'); // Reset check_availability to false
                loadNotes(); // Reload notes
            },
            error: function(xhr) {
                if (xhr.status === 409 && xhr.responseJSON.availability) {
                    // Build the conflict table
                    const tableHtml = `
                            <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                                <p>Es gibt bestehende Aufgaben im angegebenen Zeitraum:</p>
                                <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Startdatum</th>
                                            <th>Enddatum</th>
                                            <th>Mitarbeiter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${xhr.responseJSON.availability.map(task => `
                                            <tr>
                                                <td>${task.task_title}</td>
                                                <td>${task.start_date}</td>
                                                <td>${task.end_date}</td>
                                                <td>${task.name || ''} ${task.lastname || ''}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <p>Möchten Sie trotzdem fortfahren?</p>
                            </div>
                        `;

                    // Display SweetAlert with conflicts
                    Swal.fire({
                        title: 'Konflikte erkannt!',
                        html: tableHtml,
                        icon: 'warning',
                        customClass: {
                            popup: 'swal-wide', // Apply custom width class
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Trotzdem speichern',
                        cancelButtonText: 'Abbrechen',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Update the hidden input value
                            $('#check_availability').val('true');
                            console.log('Value after Save Anyway:', $(
                                '#check_availability').val()); // Debugging

                            // Resubmit the form with check_availability = true
                            saveNoteButton.trigger('click');
                        }
                    });
                } else {
                    // Handle validation or other errors
                    const errorMessage = Object.values(xhr.responseJSON.errors || {}).join(
                        '<br>') || 'Die Notiz konnte nicht gespeichert werden.';
                    Swal.fire('Fehler', errorMessage, 'error');
                }
            }
        });
    });


    function updateNoteDate(noteId, newDeadline) {
        $.ajax({
            url: `/note_change_date/${noteId}`, // Verwenden Sie die richtige URL
            type: 'PUT',
            data: {
                deadline: newDeadline,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF-Token
            },
            success: function(response) {
                // Aktualisiere das Fälligkeitsdatum im DOM
                $(`.change-date[data-id="${noteId}"]`).html(`
                        <small>
                            <i class="feather icon-calendar primary"></i> ${response.deadline || 'Kein Fälligkeitsdatum'}
                        </small>
                    `);
                toastr.success('Fälligkeitsdatum erfolgreich aktualisiert.', 'Erfolg');
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Aktualisieren des Fälligkeitsdatums:', error);
                toastr.error(
                    'Fehler beim Aktualisieren des Fälligkeitsdatums. Bitte versuchen Sie es erneut.',
                    'Fehler');
            }
        });
    }

    
    function updateNoteTime(noteId, newTime) {
        $.ajax({
            url: `/note_change_time/${noteId}`, // Verwenden Sie die richtige URL
            type: 'PUT',
            data: {
                end_time: newTime,
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF-Token
            },
            success: function(response) {
                // Aktualisiere die Endzeit im DOM
                $(`.change-time[data-id="${noteId}"]`).html(`
                        <small>
                            <i class="feather icon-clock"></i> ${response.end_time || 'Keine Endzeit'}
                        </small>
                    `);
                toastr.success('Endzeit erfolgreich aktualisiert.', 'Erfolg');
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Aktualisieren der Endzeit:', error);
                toastr.error(
                    'Fehler beim Aktualisieren der Endzeit. Bitte versuchen Sie es erneut.',
                    'Fehler');
            }
        });
    }
 
    // Handle note-color click
    $(document).on('click', '.note-color', function() {
        const noteId = $(this).data('id'); // Get the note ID
        const currentColor = $(this).find('i').css('color'); // Get the current color from the icon

        // Define color options
        const colors = [
            '#8fc73e', '#ff0000', '#0000ff', '#ffff00', '#ff00ff',
            '#00ffff', '#000000', '#ffffff', '#808080', '#ffa500',
            '#800080', '#8b4513', '#4682b4', '#5f9ea0', '#d2691e',
            '#2e8b57', '#dc143c', '#7fffd4', '#9932cc', '#ff6347'
        ];

        // Generate color options HTML
        let colorOptions = colors.map(color => `
                        <div style="display: inline-block; margin: 5px;">
                            <button class="color-btn" data-color="${color}" style="background-color: ${color}; border: none; width: 30px; height: 30px; border-radius: 50%;"></button>
                        </div>
                    `).join('');

        // Show SweetAlert modal
        Swal.fire({
            title: 'Wählen Sie eine Farbe',
            html: `
                            <div style="display: flex; flex-wrap: wrap; justify-content: center;">
                                ${colorOptions}
                            </div>
                            <p style="margin-top: 10px; text-align: center;">Aktuelle Farbe: <span style="color: ${currentColor}; font-weight: bold;">${currentColor}</span></p>
                        `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            showConfirmButton: false,
            didOpen: () => {
                // Handle color selection
                $('.color-btn').on('click', function() {
                    const selectedColor = $(this).data(
                    'color'); // Get the selected color

                    // Send the selected color to the server
                    $.ajax({
                        url: `{{ url('/note_change_color') }}/${noteId}`,
                        method: 'PUT',
                        data: {
                            _token: "{{ csrf_token() }}",
                            color: selectedColor
                        },
                        success: function(response) {
                            Swal.fire('Erfolgreich',
                                'Die Farbe wurde aktualisiert.',
                                'success');
                            loadNotes
                        (); // Reload notes to reflect the color change
                        },
                        error: function() {
                            Swal.fire('Fehler',
                                'Die Farbe konnte nicht aktualisiert werden.',
                                'error');
                        }
                    });
                });
            }
        });
    });
 
    // Handle note-color click
    $(document).on('click', '.change-date', function() {
        const noteId = $(this).data('id'); // Get the note ID  

        // Show SweetAlert modal
        Swal.fire({
            title: 'Wählen Sie ein neues Datum',
            html: `
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <label for="new-deadline" style="margin-bottom: 10px;">Neues Datum auswählen:</label>
                                <input type="date" id="new-deadline" class="form-control">
                            </div> 
                        `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            confirmButtonText: 'Speichern',
            preConfirm: () => {
                const selectedDate = document.getElementById('new-deadline').value;
                if (!selectedDate) {
                    Swal.showValidationMessage('Bitte wählen Sie ein gültiges Datum aus.');
                }
                return selectedDate;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedDate = result.value;

                // Send the selected date to the server
                $.ajax({
                    url: `{{ url('/note_change_date') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        deadline: selectedDate
                    },
                    success: function(response) {
                        Swal.fire('Erfolgreich', 'Das Datum wurde geändert.',
                            'success');
                        loadNotes(); // Reload notes to reflect the date change
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Das Datum konnte nicht geändert werden.', 'error');
                    }
                });
            }
        });
    });

    // Handling the time 
    $(document).on('click', '.change-time', function() {
        const noteId = $(this).data('id'); // Get the note ID  

        // Show SweetAlert modal
        Swal.fire({
            title: 'Wählen Sie eine neue Zeit',
            html: `
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <label for="new-end-time" style="margin-bottom: 10px;">Neue Uhrzeit auswählen:</label>
                                <input type="time" id="new-end-time" class="form-control">
                            </div> 
                        `,
            showCancelButton: true,
            cancelButtonText: 'Abbrechen',
            confirmButtonText: 'Speichern',
            preConfirm: () => {
                const selectedTime = document.getElementById('new-end-time').value;
                if (!selectedTime) {
                    Swal.showValidationMessage(
                    'Bitte wählen Sie eine gültige Uhrzeit aus.');
                }
                return selectedTime;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedTime = result.value;

                // Send the selected time to the server
                $.ajax({
                    url: `{{ url('/note_change_time') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                        end_time: selectedTime
                    },
                    success: function(response) {
                        Swal.fire('Erfolgreich', 'Die Uhrzeit wurde geändert.',
                            'success');
                        loadNotes(); // Reload notes to reflect the time change
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Uhrzeit konnte nicht geändert werden.', 'error'
                            );
                    }
                });
            }
        });
    });
 
    // Delete note functionality
    $(document).on('click', '.delete_note', function() {
        const noteId = $(this).data('id');

        Swal.fire({
            title: 'Bist du sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/notes_delete') }}/${noteId}`,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        Swal.fire('Gelöscht!', 'Die Notiz wurde gelöscht.',
                            'success');
                        loadNotes(); // Reload notes
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Notiz konnte nicht gelöscht werden.', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click', '.trash_box', function() {
        // Fetch trashed notes
        $.ajax({
            url: "{{ route('notes.trash') }}",
            method: "GET",
            success: function(response) {
                // Build the table with trashed notes
                let tableHtml = `
                        <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                            <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Titel</th>
                                        <th>Kategorie</th>
                                        <th>Erstellt am</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.notes.map(note => `
                                        <tr data-id="${note.id}">
                                            <td>${note.title}</td>
                                            <td>${note.category_name}</td>
                                            <td>${new Date(note.created_at).toLocaleDateString()}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm permanent-delete" data-id="${note.id}">Dauerhaft löschen</button>
                                                <button class="btn btn-success btn-sm recover-note" data-id="${note.id}">Wiederherstellen</button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;

                // Show SweetAlert dialog
                Swal.fire({
                    title: 'Papierkorb',
                    html: tableHtml,
                    showCancelButton: true,
                    cancelButtonText: 'Schließen',
                    showConfirmButton: false,
                    width: '800px',
                });
            },
            error: function() {
                Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
            }
        });
    });

    // Handle permanent delete
    $(document).on('click', '.permanent-delete', function() {
        const noteId = $(this).data('id');
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen',
        }).then((result) => {
            if (result.isConfirmed) {
                // Send request to delete note permanently
                $.ajax({
                    url: `{{ url('/notes_permanent_delete') }}/${noteId}`,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        Swal.fire('Erfolgreich!',
                            'Die Notiz wurde dauerhaft gelöscht.', 'success');
                        $(`tr[data-id="${noteId}"]`)
                    .remove(); // Remove the note row from the table
                        loadNotes(); // Reload notes to reflect changes
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Notiz konnte nicht gelöscht werden.', 'error');
                    }
                });
            }
        });
    });

    // Handle recover note
    $(document).on('click', '.recover-note', function() {
        const noteId = $(this).data('id');
        Swal.fire({
            title: 'Bist du sicher?',
            text: 'Möchten Sie diese Notiz wiederherstellen?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, wiederherstellen!',
            cancelButtonText: 'Abbrechen',
        }).then((result) => {
            if (result.isConfirmed) {
                // Send request to recover the note
                $.ajax({
                    url: `{{ url('/notes_recover') }}/${noteId}`,
                    method: 'PUT',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        Swal.fire('Erfolgreich!',
                            'Die Notiz wurde wiederhergestellt.', 'success');
                        $(`tr[data-id="${noteId}"]`)
                    .remove(); // Remove the note row from the table
                        loadNotes(); // Reload notes to reflect changes
                    },
                    error: function() {
                        Swal.fire('Fehler',
                            'Die Notiz konnte nicht wiederhergestellt werden.',
                            'error');
                    }
                });
            }
        });
    });


    $(document).on('click', '.note-settings', function() {
        const noteId = $(this).data('id'); // Get the note ID from the clicked button

        // Fetch the note data
        $.ajax({
            url: `{{ url('/notes') }}/${noteId}`, // Make sure noteId is being passed correctly
            method: "GET",
            success: function(response) {
                const note = response.note;

                // Populate the modal fields
                $('#updateSettingModal input[name="deadline"]').val(note.deadline);
                $('#updateSettingModal input[name="end_time"]').val(note.end_time);
                $('#updateSettingModal input[name="add_calendar_date"]').val(note
                    .add_calendar_date);
                $('#updateSettingModal select[name="repeat"]').val(note.repeat);
                $('#updateSettingModal input[name="reminder_date"]').val(note
                .reminder_date);
                $('#updateSettingModal input[name="reminder_time"]').val(note
                .reminder_time);
                $('#updateSettingModal input[name="priority"]').val(note.priority);

                // Open the modal
                $('#updateSettingModal').modal('show');
            },
            error: function(xhr) {
                console.error(xhr.responseText); // Log the actual error for debugging
                Swal.fire('Fehler', 'Die Notizdaten konnten nicht geladen werden.',
                'error');
            }
        });

    });

    $('#save_note_settings').on('click', function() {
        const formData = $('#updateSettingForm').serialize(); // Serialize the modal form data
        const noteId = $('.note-settings').data('id'); // Get the note ID

        $.ajax({
            url: `{{ url('/notes_update_settings') }}/${noteId}`, // Replace with your update endpoint
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}", // Add CSRF token
                deadline: $('#updateSettingForm input[name="deadline"]').val(),
                end_time: $('#updateSettingForm input[name="end_time"]').val(),
                add_calendar_date: $('#updateSettingForm input[name="add_calendar_date"]')
                .val(),
                repeat: $('#updateSettingForm select[name="repeat"]').val(),
                reminder_date: $('#updateSettingForm input[name="reminder_date"]').val(),
                reminder_time: $('#updateSettingForm input[name="reminder_time"]').val(),
                priority: $('#updateSettingForm input[name="priority"]').val(),
                check_emp: $('#updateSettingForm input[name="check_emp"]').val(),
            },
            success: function(response) {
                Swal.fire('Erfolgreich', response.message, 'success');
                $('#updateSettingModal').modal('hide'); // Close the modal
                loadNotes(); // Reload notes
            },
            error: function(xhr) {
                if (xhr.status === 409 && xhr.responseJSON.availability) {
                    // Handle conflicts with a SweetAlert table
                    const conflicts = xhr.responseJSON.availability;
                    let conflictTable = `
                            <div style="max-height: 500px; overflow-y: auto; padding: 10px;">
                                <p>Es gibt bestehende Aufgaben im angegebenen Zeitraum:</p>
                                <table class="table table-bordered" style="width: 100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>Titel</th>
                                            <th>Startdatum</th>
                                            <th>Enddatum</th>
                                            <th>Mitarbeiter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${conflicts.map(conflict => `
                                            <tr>
                                                <td>${conflict.task_title}</td>
                                                <td>${conflict.start_date}</td>
                                                <td>${conflict.end_date}</td>
                                                <td>${conflict.name || ''} ${conflict.lastname || ''}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <p>Möchten Sie trotzdem fortfahren?</p>
                            </div>
                        `;

                    // Show the SweetAlert with conflicts
                    Swal.fire({
                        title: 'Konflikte erkannt!',
                        html: conflictTable,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Trotzdem speichern',
                        cancelButtonText: 'Abbrechen',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Set the hidden input to "true" to bypass conflict check
                            $('#check_emp').val('true');

                            // Resubmit the form with "check_emp" set to "true"
                            $.ajax({
                                url: `{{ url('/notes_update_settings') }}/${noteId}`,
                                method: "PUT",
                                data: {
                                    ...formData,
                                    check_emp: 'true',
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    Swal.fire('Erfolgreich', response
                                        .message, 'success');
                                    $('#updateSettingModal').modal(
                                        'hide'); // Close modal
                                    loadNotes(); // Reload notes
                                },
                                error: function() {
                                    Swal.fire('Fehler',
                                        'Die Einstellungen konnten nicht aktualisiert werden.',
                                        'error');
                                }
                            });
                        }
                    });
                } else {
                    const errorMessage = Object.values(xhr.responseJSON.errors || {}).join(
                        '<br>') || 'Die Einstellungen konnten nicht gespeichert werden.';
                    Swal.fire('Fehler', errorMessage, 'error');
                }
            }
        });
    });
 

    // Load notes on page load
    loadNotes();

    $('.filter').select2({
        placeholder: 'Filter',
        allowClear: true,
        templateResult: formatState,
        templateSelection: formatState,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    // Function to format options with icons
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
        var icon = '<i class="feather icon-filter"></i>';

        switch (state.id) {
            case 'date':
                icon = '<i class="feather icon-calendar"></i>';
                break;
            case 'sort':
                icon = '<i class="fa fa-sort"></i>';
                break;
            case 'calendar':
                icon = '<i class="feather icon-calendar"></i>';
                break;
            case 'reminder':
                icon = '<i class="fa fa-bell"></i>';
                break;
            case 'repeat':
                icon = '<i class="feather icon-refresh-ccw"></i>';
                break;
            default:
                icon = '<i class="icon-filter"></i>';
        }

        var markup = icon + ' ' + state.text;
        return markup;
    };

    // Handle change event on the filter select
    $('.filter').on('change', function() {
        var selectedFilter = $(this).val();
        fetchFilteredNotes(selectedFilter);
    });
 
    function fetchFilteredNotes(filter) {
        if (!filter) {
            // Optionally, handle the case when no filter is selected
            $('#personal-note-list').html(
            '<li class="list-group-item">Bitte wählen Sie einen Filter aus.</li>');
            return;
        }

        $.ajax({
            url: '/note_view_filter',
            type: 'GET',
            data: {
                filter: filter
            },
            dataType: 'json',
            success: function(response) {
                if (response.notes) {
                    updateNotesList(response.notes);
                    toastr.success(response.message, 'Erfolg');
                }
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Filtern der Notizen:', error);
                toastr.error('Fehler beim Filtern der Notizen. Bitte versuchen Sie es erneut.',
                    'Fehler');
            }
        });
    }

   
    function updateNotesList(notes) {
        var notesList = $('#personal-note-list');
        notesList.empty(); // Clear existing notes

        if (notes.length === 0) {
            notesList.append('<li class="list-group-item">Keine Notizen gefunden.</li>');
            return;
        }

        notes.forEach(function(note) {
            var noteItem = `
                    <li class="list-group-item" data-id="${note.id}" style="border-left: 10px solid ${note.color}; margin-bottom:7px; cursor:pointer; "> 
                        <div class="media">
                            <fieldset>
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" class="done-checkbox" data-id="${note.id}" ${note.is_done ? 'checked' : ''}>
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span> 
                                </div>
                            </fieldset>
                            <i class="feather icon-settings note-settings float-right" data-id="${note.id}" style="font-size:19px !important;"></i>
                            <div class="media-body">
                                <div style="position: relative;">
                                    <span class="badge badge-warning editing-badge" style="position: absolute; top: -20px; left: 0; display: none;">Editing...</span>
                                    <h5 class="mt-0 title-field" data-id="${note.id}" data-field="title">${note.title}</h5>
                                </div>
                                <div style="position: relative;">
                                    <span class="badge badge-warning editing-badge" style="position: absolute; top: -20px; left: 0; display: none;">Editing...</span>
                                    <p class="note-field" data-id="${note.id}" data-field="note">${note.note}</p>
                                </div>
                                <div class="date d-flex">
                                    <p class="mr-1 change-date"
                                    data-id="${note.id}"
                                    ><small><i class="feather icon-calendar ${note.add_calendar_date ? 'primary' : ''}"></i> ${note.deadline || 'Kein Fälligkeitsdatum'}</small></p>
                                    <p class="mr-1 change-time"
                                        data-id="${note.id}"
                                    ><small><i class="feather icon-clock"></i> ${note.end_time || 'Keine Endzeit'}</small></p>
                                    <p class="mr-1 updateCategoryModal" 
                                        data-category-id="${note.category_id}"
                                        data-id="${note.id}"
                                        ><small><i class="feather icon-slack" >
                                        </i> ${note.category_name || 'Standard'}</small>
                                    </p>
                                    
                                    <p class="mr-1">
                                        ${note.reminder_date || note.reminder_time ? `
                                            <small class="no-reminder-icon-top"
                                                    data-id="${note.id}"
                                                    data-toggle="tooltip" 
                                                    title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                                <i class="feather icon-bell primary"  >
                                                </i>
                                            </small>` 
                                            : ''}
                                    </p>
                                    <p>
                                        ${note.repeat ? `
                                            <small class="no-repeat-icon-top" data-toggle="tooltip" data-id="${note.id}" id="no_repeat"
                                                    title="Wiederholung: ${note.repeat}">
                                                <i class="fa fa-refresh warning"  >
                                                </i>
                                            </small>` 
                                            : ''}
                                    </p>
                                </div>
                            </div>
                            <div class="media-footer" style="position: absolute; display: flex; right: -49px; top: 1px; flex-direction: column;">
                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-danger mr-1 mb-1 waves-effect waves-light delete_note" data-id="${note.id}">
                                    <i class="feather icon-trash"></i>
                                </button>

                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-secondary mr-1 mb-1 waves-effect waves-light note-color" data-id="${note.id}">
                                    <i class="feather icon-aperture" style="color:${note.color}"></i>
                                </button>

                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-secondary mr-1 mb-1 waves-effect waves-light drag-handle" data-id="${note.id}">
                                    <i class="feather icon-move" style="color:${note.color}"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                `;
            notesList.append(noteItem);
        });

        // Re-initialize Feather Icons for newly added elements
        // feather.replace();

        // Re-initialize drag-and-drop for the updated list
        Sortable.create(document.getElementById('personal-note-list'), {
            handle: '.drag-handle', // Drag handle selector
            animation: 150, // Animation speed in ms
            onEnd: function( /**Event*/ evt) {
                // Get the new order of IDs
                var order = [];
                $('#personal-note-list li').each(function() {
                    order.push($(this).data('id'));
                });

                // Send the new order to the server via AJAX
                updateOrder(order);
            },
        });
    }
 
    function formatDate(dateStr) {
        var date = new Date(dateStr);
        var day = String(date.getDate()).padStart(2, '0');
        var month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
        var year = date.getFullYear();
        return day + '.' + month + '.' + year;
    }

 
    function updateOrder(order) {
        // Fetch CSRF token from meta tag
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/notes/update-order',
            type: 'POST',
            data: JSON.stringify({
                order: order
            }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.message) {
                    toastr.success(response.message, 'Erfolg');
                }
            },
            error: function(xhr, status, error) {
                console.error('Fehler beim Aktualisieren der Reihenfolge:', error);
                toastr.error(
                    'Fehler beim Aktualisieren der Reihenfolge. Bitte versuchen Sie es erneut.',
                    'Fehler');
            }
        });
    }

    const sortable = Sortable.create(document.getElementById('personal-note-list'), {
        handle: '.drag-handle', // Drag handle selector
        animation: 150, // Animation speed in ms
        onEnd: function( /**Event*/ evt) {
            // Get the new order of IDs
            const order = [];
            const listItems = document.querySelectorAll('#personal-note-list li');
            listItems.forEach(function(item) {
                order.push(item.getAttribute('data-id'));
            });

            // Send the new order to the server via AJAX
            updateOrder(order);
        },
    });

 
    function updateOrder(order) {
        // Fetch CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/notes/update-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken, // Laravel CSRF token
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    order: order
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.message) {
                    // Display success message using Toastr
                    toastr.success(data.message, 'Sortieren');
                }
            })
            .catch(error => {
                console.error('Error updating order:', error);
                // Display error message using Toastr
                toastr.error('Failed to update order. Please try again.', 'Error');
            });
    }
    // Utility: Debounce input search
    function debounce(func, delay) {
        let timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, arguments), delay);
        };
    }

    // Initial load
    window.addEventListener('DOMContentLoaded', () => {
        const firstTab = document.querySelector('.tab-button');
        if (firstTab) {
            activeTab = firstTab.dataset.tab;
            firstTab.click();
        }
    });
</script>

  
  
<!-- Deadline Script Toggle: start  -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const deadlineButton = document.getElementById('deadline');
    const deadlineArea = document.getElementById('deadline_area');
    const endTimeButton = document.getElementById('end_time');
    const endTimeArea = document.querySelector('.end_time_area');
    const repeatedButton = document.getElementById('repeated');
    const repeatedArea = document.querySelector('.repeated_area');
    const reminderButton = document.getElementById('reminder_check');
    const reminderArea = document.querySelector('.reminder_area');
    const addCalendarButton = document.getElementById('add_calendar');
    const addCalendarArea = document.getElementById('add_calendar_area');

    // Toggle deadline area
    deadlineButton.addEventListener('change', function() {
        if (this.checked) {
            deadlineArea.style.display = 'table-row';
        } else {
            deadlineArea.style.display = 'none';
        }
    });

    // Toggle end time area
    endTimeButton.addEventListener('change', function() {
        if (this.checked) {
            endTimeArea.style.display = 'table-row';
        } else {
            endTimeArea.style.display = 'none';
        }
    });

    // Toggle repeated area
    repeatedButton.addEventListener('change', function() {
        if (this.checked) {
            repeatedArea.style.display = 'table-row';
        } else {
            repeatedArea.style.display = 'none';
        }
    });

    // Toggle reminder area
    reminderButton.addEventListener('change', function() {
        if (this.checked) {
            reminderArea.style.display = 'table-row';
        } else {
            reminderArea.style.display = 'none';
        }
    });

    // Toggle add calendar area
    addCalendarButton.addEventListener('change', function() {
        if (this.checked) {
            addCalendarArea.style.display = 'table-row';
        } else {
            addCalendarArea.style.display = 'none';
        }
    });

    // Initially hide all areas
    deadlineArea.style.display = 'none';
    endTimeArea.style.display = 'none';
    repeatedArea.style.display = 'none';
    reminderArea.style.display = 'none';
    addCalendarArea.style.display = 'none';
});
</script>

 
<!-- Note Category Operations: start -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newNoteModal = $('#newNote'); // New Note modal
    const categoryModal = $('#categoryModal'); // Category modal
    const categorySelect = $('#category_id'); // Category dropdown
    const checkAvailabilityInput = $('#check_availability'); // The hidden input

    // Function to load categories into the select dropdown
    function loadCategories() {
        $.ajax({
            url: "{{ route('note.category.get') }}",
            method: "GET",
            success: function(data) {
                // Clear existing options and add the default option
                categorySelect.empty();
                categorySelect.append('<option value="">Wählen Sie eine Kategorie</option>');
                // Populate with categories
                data.forEach(category => {
                    categorySelect.append(
                        `<option value="${category.id}">${category.category_name}</option>`
                        );
                });
            },
            error: function() {
                Swal.fire('Fehler',
                    'Kategorien konnten nicht geladen werden. Bitte versuchen Sie es erneut.',
                    'error');
            }
        });
    }

    // Handle the add_category button click
    $(document).on('click', '.add_category', function() {
        // Hide the newNote modal
        newNoteModal.modal('hide');
        // Show the category modal
        categoryModal.modal('show');
    });

    // Handle the close event of the category modal
    categoryModal.on('hidden.bs.modal', function() {
        // Show the newNote modal
        newNoteModal.modal('show');
    });

    // Save category
    $('#saveCategory').on('click', function() {
        console.log('Save Category button clicked!'); // Debugging point

        const categoryName = $('#category_name').val();
        const type = $('#type').val();
        const color = $('#color').val();

        console.log('Form Values:', {
            categoryName,
            type,
            color
        }); // Debugging point

        // Validate fields
        if (!categoryName || !type || !color) {
            console.warn('Validation failed:', {
                categoryName,
                type,
                color
            }); // Debugging point
            Swal.fire('Fehler', 'Alle Felder sind erforderlich.', 'error');
            return;
        }

        // Send AJAX request
        $.ajax({
            url: "{{ route('note.category.auto.save') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                category_name: categoryName,
                type: type,
                color: color,
            },
            success: function(response) {
                console.log('Category Save Response:', response); // Debugging point
                if (response.status === 'success') {
                    Swal.fire('Erfolg', response.message, 'success');
                    categoryModal.modal('hide'); // Close modal
                    loadCategories(); // Reload categories
                } else {
                    Swal.fire('Fehler', 'Etwas ist schief gelaufen.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error saving category:', xhr.responseJSON ||
                error); // Debugging point
                Swal.fire('Fehler',
                    'Kategorie konnte nicht gespeichert werden. Bitte versuchen Sie es erneut.',
                    'error');
            }
        });
    });


    // Initial load of categories when the page is loaded
    loadCategories();
});
</script>
<!-- Note Category Operations: end -->



<!-- Priority Script  -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Priority Dropdown
    const priorityInput = document.querySelector('input[name="priority"]'); // Hidden input field for priority
    const priorityDropdownItems = document.querySelectorAll(
        '.btn-group .dropdown-menu .dropdown-item[data-value][data-priority]'); // Priority dropdown items

    priorityDropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            const selectedPriority = this.getAttribute(
            'data-value'); // Get the data-value for priority
            priorityInput.value =
            selectedPriority; // Update the hidden input value for priority
            console.log(`Priority set to: ${selectedPriority}`); // Debugging log
        });
    });

    // Color Dropdown
    const colorInput = document.getElementById('color'); // Hidden input field for color
    const colorIcon = document.getElementById('colorIcon'); // Icon to change color
    const colorDropdownItems = document.querySelectorAll(
        '.btn-group .dropdown-menu .dropdown-item[data-value][data-color]'); // Color dropdown items

    colorDropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            const selectedColor = this.getAttribute(
            'data-value'); // Get the data-value for color
            colorInput.value = selectedColor; // Update the hidden input value for color
            colorIcon.style.color = selectedColor; // Change icon color
            console.log(`Color selected: ${selectedColor}`); // Debugging log
        });
    });
});
</script>

 

 
<script>
// Initialize Toastr options
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

 
</script>


<script>
let progressChart = null;
const typeCharts = {};

function showToast(message = 'Als erledigt markiert!') {
    const toast = document.getElementById('toastSuccess');
    toast.textContent = message;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 2500);
}

function updateDonutChart(chartInstance, ctx, percent, color = '#16a34a') {
    const remaining = 100 - percent;

    if (chartInstance) {
        chartInstance.data.datasets[0].data = [percent, remaining];
        chartInstance.update();
    } else {
        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Erreicht', 'Offen'],
                datasets: [{
                    data: [percent, remaining],
                    backgroundColor: [color, '#e5e7eb'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.label}: ${ctx.raw}%`
                        }
                    }
                }
            }
        });
    }
}

function renderTypeCharts(typeStats) {
    const colors = {
        personal_task: '#dc2626',   // red
        appointment: '#74b2d4',     // yellow
        ticket_task: '#164194',     // blue
        problem: '#93c21c'          // green
    };

    typeStats.forEach(stat => {
        const canvas = document.getElementById(`chart_${stat.type}`);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const color = colors[stat.type] ?? '#64748b';

        typeCharts[stat.type] = updateDonutChart(typeCharts[stat.type], ctx, stat.today, color);
    });
}

function isOverdue(dueDate) {
    if (!dueDate) return false;
    const due = new Date(dueDate);
    const now = new Date();
    const diff = (now - due) / (1000 * 60 * 60 * 24); // days
    return diff > 2;
}


function loadDueToday() {
    fetch('/my/due-today')
        .then(res => res.json())
        .then(data => {
            // Update % text
            document.querySelector('#todayPercent').innerText = `${data.percent}% erreicht`;

            // Main chart
            const mainCtx = document.getElementById('progressDonut').getContext('2d');
            progressChart = updateDonutChart(progressChart, mainCtx, data.percent);

            // Per-type charts
            renderTypeCharts(data.type_stats);

            // Render goal list
            const goalList = document.querySelector('#goalList');
            goalList.innerHTML = '';

            function getItemLink(item) {
                switch (item.type) {
                    case 'appointment':
                        return `/appointment_details/${item.id}`;
                    case 'personal_task':
                        return `/personal_task_details/${item.id}`;
                    case 'problem':
                        return `/problem/profile/${item.id}`;
                    case 'ticket_task':
                        return `/ticket_task_details/${item.id}`; // if applicable
                    default:
                        return '#';
                }
            }

            data.items.forEach(item => {
                    const badgeColor = item.type === 'personal_task' ? '#dc2626' :
                                    item.type === 'appointment' ? '#c0d8ea' :
                                    item.type === 'ticket_task' ? '#164194' : '#93c21c';

                    const icon = item.type === 'personal_task' ? 'alert-circle' :
                                item.type === 'appointment' ? 'calendar' :
                                item.type === 'ticket_task' ? 'tool' : 'activity';

                    const isOverdueAppointment = item.type === 'appointment' && isOverdue(item.due_date);

                    const iconAnimation = isOverdueAppointment
                        ? 'animation: pulse-red 1s infinite; color: #dc2626 !important;'
                        : `color:${badgeColor}`;

                    goalList.innerHTML += `
                        <div class="goal-item border-l-4 bg-white p-1 flex items-center justify-between"
                            style="border-left:6px solid ${badgeColor}; border-bottom: 1px solid #d5d1d1;"
                            data-id="${item.id ?? ''}" data-type="${item.type}">
                            <div class="flex items-center gap-1">
                                <i data-feather="${icon}" class="w-4 h-4" style="${iconAnimation}"></i>
                                <input type="checkbox" class="mark-done-checkbox">
                                <div>
                                    <span class="text-xs font-medium">${item.title}${item.description ? ': ' + item.description : ''}</span>
                                    ${item.type === 'appointment' && item.due_date ? `
                                        <div class="text-[10px] text-gray-500">
                                            Fällig: ${new Date(item.due_date).toLocaleDateString('de-DE')}
                                            
                                        </div>
                                        <div class="text-[10px] text-yellow-500">
                                          <i class="feather icon-info"></i> Dieser Aufgaben-erforderliche Bericht
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                           <a href="${getItemLink(item)}"
                                class="text-xs px-2 py-0.5 rounded-full inline-block"
                                style="background:${badgeColor}; color:white;">
                                    ${item.label.replace('_', ' ')}
                            </a>

                        </div>`;
                });


            feather.replace();
        });
}

// Initial load
loadDueToday();

// Auto-refresh every 10 minutes
setInterval(loadDueToday, 10 * 60 * 1000);

// Mark-as-done event
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('mark-done-checkbox')) {
        const container = e.target.closest('.goal-item');
        const id = container.getAttribute('data-id');
        const type = container.getAttribute('data-type');

       if (id && type === 'appointment') {
            const checkbox = e.target;

            Swal.fire({
                title: 'Bericht schreiben',
                html: `<div id="quill-editor" style="height: 200px;"></div>`,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                focusConfirm: false,
                allowOutsideClick: false,     // ⛔ Don't allow clicking outside to close
                allowEscapeKey: false,        // ⛔ Don't allow ESC key to close
                didOpen: () => {
                    const quill = new Quill('#quill-editor', {
                        theme: 'snow'
                    });

                    Swal.__quillInstance = quill;
                },
                preConfirm: () => {
                    const quill = Swal.__quillInstance;
                    const reportContent = quill.root.innerHTML;

                    if (!reportContent || reportContent === '<p><br></p>') {
                        Swal.showValidationMessage('Bitte einen Bericht schreiben.');
                        return false;
                    }

                    return fetch('/my/save-appointment-report', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            id,
                            report: reportContent
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message ?? 'Fehler beim Speichern.');
                        }
                    })
                    .catch(err => {
                        Swal.showValidationMessage(err.message);
                        return false;
                    });
                }
            }).then(result => {
                if (!result.isConfirmed) {
                    checkbox.checked = false;
                } else {
                    showToast();
                    loadDueToday();
                }
            });
        }



        else { 

            fetch('/my/mark-done', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ id, type })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    container.classList.add('opacity-50');
                    const checkbox = container.querySelector('.mark-done-checkbox');
                    checkbox.disabled = true;
                    checkbox.checked = true;

                    const title = container.querySelector('span.text-xs.font-medium');
                    if (title) title.innerHTML += ' <i class="ri-check-line text-green-600 text-sm"></i>';

                    showToast();
                    loadDueToday(); // refresh charts and list
                } else {
                    alert(data.message ?? 'Fehler beim Speichern.');
                    e.target.checked = false;
                }
            });
        }
    }
});
</script>
 
<script>
    function toggleTodoCollapse(id) {
        const el = document.getElementById(id);
        el.classList.toggle('open');
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.style.display = section.style.display === 'none' || section.style.display === '' ? 'table-row' : 'none';
    }

    function setPriority(value, btn) {
        document.getElementById('priority').value = value;
        const buttons = btn.parentElement.querySelectorAll('.priority-btn');
        buttons.forEach(b => b.style.outline = 'none');
        btn.style.outline = '2px solid black';
    }
</script>

<script>
    function togglePriorityDropdown() {
        const dropdown = document.getElementById('priorityDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function setPriorityDropdown(value, label) {
        document.getElementById('priority').value = value;
        document.getElementById('priorityDropdownBtn').innerText = label + ' ▼';
        document.getElementById('priorityDropdown').style.display = 'none';
    }

    // Optional: Close dropdown on click outside
    document.addEventListener('click', function (e) {
        const dropdown = document.getElementById('priorityDropdown');
        const btn = document.getElementById('priorityDropdownBtn');
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

@endsection 