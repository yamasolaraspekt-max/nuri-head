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
        

        /* Weather Style: start  */

        .weather-dashboard {
            font-family: Arial, sans-serif;
            margin: 20px auto;
            text-align: center;
            max-width: 600px;
        }
        .weather-dashboard table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .weather-dashboard th, .weather-dashboard td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .weather-dashboard th {
            background-color: #f4f4f4;
            font-weight: bold;
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

     .calendar-list {
            position: relative;
            height: 432px; /* adjust the height as needed */
            overflow: hidden;
        }

        #calendar {
            position: absolute;
            top: 37px;
            left: 0;
            right: 0;
            bottom: 30%; /* adjust the bottom margin as needed */
            overflow: hidden;
        }

        .events-list {
            display: flex;
            justify-content: center;
            flex-direction: column;
         
        
            right: 0;
            bottom: 0;
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
        .fc-day-sat .fc-daygrid-day-top, .fc-day-sun .fc-daygrid-day-top {
            color: #cecece !important;
        }

        /* Highlight days with events */
        .fc-day.has-event .fc-daygrid-day-top {
            background-color: #e6f7da; 
            color: #3c763d;
            font-weight: bold;
        }

        

        .fc-day-today .fc-daygrid-day-number {
            position: relative; /* To position the pseudo-element relative to it */ 
            padding: 0 6px !important; /* Minimal padding to adjust for the circle */
                border: 1px solid #a2a2a2;
            color:#8fc73e !important;
        }

 


       /* Default - no circle unless marked as event day */
        .fc-daygrid-day-number::before {
            content: '';
            display: none;
        }

        /* Show circle only for days with events and use dynamic background color */
        .fc-daygrid-day-number[data-event='true']::before {
            display: block;
            background-color: var(--event-bg-color, red);
            position: absolute;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            z-index: -1;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .fc-daygrid-day-number {
            position: relative;
            padding: 5px;
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
.fc .fc-toolbar.fc-header-toolbar {
    flex-wrap: wrap;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
}

.fc .fc-toolbar-title {
    font-size: 1.1rem;
    text-align: center;
}

.fc-daygrid-event,
.fc-timegrid-event {
    border: none !important;
}

.fc-event {
    padding: 2px 6px !important;
    font-size: 12px !important;
}

@media (max-width: 640px) {
    .fc .fc-toolbar.fc-header-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .fc .fc-daygrid-day-frame {
        padding: 6px;
    }

    .fc .fc-scroller-harness {
        overflow-x: auto;
    }

    .fc .fc-timegrid-slot-label {
        font-size: 10px;
    }

    .fc .fc-event-title {
        font-size: 11px !important;
    }

    .fc-event {
        padding: 2px 4px !important;
    }
}



         /* Make Calendar Responsive */
         #calendar {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Highlight Selected Date with Blue Circle */
        .fc-daygrid-day.fc-day-selected {
           
             
            
        }
        .fc-daygrid-day{
            padding:0 !important;
        }
        .fc-daygrid-day-frame{
                align-content: center !important;
                padding:1px !important;
        }
 

        /* Remove Events Section from Each Cell */
        .fc-daygrid-day-events {
            display: none !important;
        }

        /* Center and Style the Date Numbers */
        .fc-daygrid-day-top {
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            height: 100%;
        }

       
        .fc-col-header-cell{
            padding:5px !important;
        }
         /* Remove Borders from Calendar */
        .fc-daygrid-day {
            border: none !important;
        }

        /* Show Only First Letter of Day Names */
        .fc-col-header-cell {
            text-transform: uppercase;
            font-weight: bold;
            color: black;
            border:0px !important;
        }
        .fc-col-header-cell-cushion {
            font-size: 1rem;
        }
        
        /* Style for Saturday and Sunday in Red */
        .fc-day-sat .fc-daygrid-day-top,
        .fc-day-sun .fc-daygrid-day-top {
            color: #f88585;
        }

        /* Center and Style the Date Numbers */
        .fc-daygrid-day-top {
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            height: 100%;
            font-size: 1rem;
        }

        /* Highlight Selected Date with Blue Circle */
        .fc-daygrid-day.fc-day-selected .fc-daygrid-day-top {
         
            
            color: white !important; /* White text */
        }

        /* Capitalize only the first word in the title */
        .fc-toolbar-title {
            text-transform: capitalize;
        }
        .fc-scrollgrid-section td {
            background:transparent !important;
        }

        
         
        .fc-scrollgrid-section td {
            border:0 !important;
        }
        /* Capitalize only the first word in the title */
        .fc-toolbar-title {
            text-transform: capitalize;
        }
         .fc-button {
            Background: #d3d0d0 !important;
            border: 0 !important;
            border-radius: 50% !important;
            padding: 9px !important;
         } 
         .fc-toolbar-title {
                font-size: 14px !important;
                color: #585656 !important;
         }
         .fc-scrollgrid{
            border:0px !important;
         }
         .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 0.5em !important;
        }

        .fc-header-toolbar{
                border-bottom: 1px solid #d5d5d5;
                padding-bottom: 10px;
                width: 79%;
                align-self: center;
        }
        .fc-button {
                width: 30px;
                height: 30px;
                color: white;
                background: #8fc73e !important;
        }
        .fc-scrollgrid-section td {
            padding-top:0 !important;
            padding-bottom:0 !important;
        }

        .fc-icon-chevron-left { 
            position: relative !important;
            top: -7px !important;
            color:rgb(255, 255, 255) !important;
            left: -7px !important;
            font-size: 27px !important;

        }   
        
        .fc-icon-chevron-right { 
            position: relative !important;
            top: -7px !important;
            color:rgb(254, 254, 254) !important;
            right: 7px !important;
            font-size: 27px !important;

        } 
        .fc-col-header-cell {
            color:#8fc73e;
        }
         

        .card-event a:hover {
            text-decoration: underline;
            color: #5a9c2f;
        }
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
                    
                    <div class="bg-white  py-6 px-4 flex flex-col items-center justify-center">
                        <canvas id="chart_problem" width="100" height="100"></canvas>
                        <p class="text-xs mt-1">Probleme</p>
                    </div>
                </div>

                <!-- Goal List Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="col-span-3 bg-white  p-4">
                        <p class="text-green-600 text-sm font-semibold mb-1">✅ Fast geschafft!</p>
                        <p class="text-xs text-gray-600 mb-3" id="todayPercent">...</p>
                        <div id="goalList" class="space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300" style="max-height: 150px;"></div>
                    </div>
                </div>

        

                <!-- Task, Appointment, Project, Offer, Note Tabs -->
                <div class="mt-10">
                    <div id="tab-content">
                        <div class="flex flex-col items-center justify-center mb-1">
                            <!-- Tabs -->
                            <ul class="flex flex-wrap justify-center text-sm font-medium text-center space-x-2 mb-2"
                                id="tabs">
                               
                                 
                                @if (DB::table('user_rolls')->select('user_id')->where('user_id', auth()->user()->name)->first())
                                    <li>
                                        <button class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-2"
                                            data-tab="admin">
                                            ADMINISTRATOR
                                        </button>
                                    </li>
                                @endif


                                <li>
                                    <button class="tab-button border-b-2 border-blue-500 text-blue-600 px-4 py-2"
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
                                    <button class="tab-button px-4 py-2" data-tab="notes">
                                        TODO  
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

                        <div class="tab-panel hidden" id="tab-calendar"> 
                         
                        </div> 
                    </div>

                    <div id="tab-content"> 

                        <div class="tab-panel hidden" id="tab-admin">
                            @include('admin.dashboard.employee.partials.admin')
                        </div>
                        <div class="tab-panel" id="tab-all">
                            @include('admin.dashboard.employee.partials.all')
                        </div>
                        <div class="tab-panel hidden" id="tab-tasks"></div>
                        <div class="tab-panel hidden" id="tab-appointments"></div>
                        <div class="tab-panel hidden" id="tab-projects"></div>
                        <div class="tab-panel hidden" id="tab-offers"></div>
                        <div class="tab-panel hidden" id="tab-notes">
                            @include('admin.dashboard.employee.partials.notes') 
                        </div>
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
                                                    <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                                        <div class="card   "> 
                                                            <div class="card-content">
                                                                <div class="card-body p-0"> 
                                                                    <div class="accordion-default collapse-bordered">
                                                                        <div class="card collapse-header">
                                                                            <div id="heading1" class="card-header collapse-header collapsed p-0" data-toggle="collapse" role="button" data-target="#accordion1" aria-expanded="false" aria-controls="accordion1">
                                                                                <span class="lead collapse-title"> 
                                                                                    <div class="text-bold font-medium-2" style=" background: #8fc73e;  border-radius: 6px; padding: 10px; color: white;">
                                                                                                <i class="feather icon-settings"></i> Details
                                                                                    </div>
                                                                                </span>
                                                                            </div>
                                                                            <div id="accordion1" role="tabpanel" data-parent="#accordionWrapa1" aria-labelledby="heading1" class="collapse" style="">
                                                                                <div class="card-content">
                                                                                    <div class="card-body">
                                                                                        <table class="table">
                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                                                <td><i class="feather icon-calendar"></i> Date</td>
                                                                                                <td style="text-align:right">
                                                                                                    <div class="checkbox">
                                                                                                        <div class="custom-control custom-switch mr-2 mb-1"> 
                                                                                                            <input type="checkbox" class="custom-control-input deadline_button" id="deadline" name=>
                                                                                                            <label class="custom-control-label" for="deadline"></label>
                                                                                                        </div>
                                                                                                    </div>  
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="deadline_area"> 
                                                                                                <td style="text-align:right" colspan="2"> 
                                                                                                        <input type='date' class="form-control pickatime" name="deadline"/> 
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                                                <td><i class="feather icon-clock"></i> Time</td>
                                                                                                <td style="text-align:right">
                                                                                                    <div class="checkbox">
                                                                                                        <div class="custom-control custom-switch mr-2 mb-1"> 
                                                                                                            <input type="checkbox" class="custom-control-input end_time_button" id="end_time" name="end_time">
                                                                                                            <label class="custom-control-label" for="end_time"></label>
                                                                                                        </div>
                                                                                                    </div>  
                                                                                                </td>
                                                                                            </tr>

                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" class="end_time_area"> 
                                                                                                <td style="text-align:right" colspan="2"> 
                                                                                                        <input type='time' class="form-control pickatime" name="end_time"/> 
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>

                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                                                <td><i class="feather icon-plus"></i>Zum Kalender hinzufügen</td>
                                                                                                <td style="text-align:right">
                                                                                                    <div class="checkbox">
                                                                                                        <div class="custom-control custom-switch mr-2 mb-1"> 
                                                                                                            <input type="checkbox" class="custom-control-input" id="add_calendar" name="add_calendar">
                                                                                                            <label class="custom-control-label" for="add_calendar"></label>
                                                                                                        </div>
                                                                                                    </div>  
                                                                                                </td>
                                                                                            </tr>

                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="add_calendar_area"> 
                                                                                                <td style="text-align:right" colspan="2"> 
                                                                                                        <input type='date' class="form-control pickatime" name="add_calendar_date"/> 
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>

                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                                                <td><i class="feather icon-refresh-cw"></i> Wiederholt</td>
                                                                                                <td style="text-align:right">
                                                                                                    <div class="checkbox">
                                                                                                        <div class="custom-control custom-switch mr-2 mb-1"> 
                                                                                                            <input type="checkbox" class="custom-control-input" id="repeated" name="repeated">
                                                                                                            <label class="custom-control-label" for="repeated"></label>
                                                                                                        </div>
                                                                                                    </div>  
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" class="repeated_area"> 
                                                                                                <td style="text-align:right" colspan="2">  
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

                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>

                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;">
                                                                                                <td><i class="fa fa-clock-o"></i> Erinnerung</td>
                                                                                                <td style="text-align:right">
                                                                                                    <div class="checkbox">
                                                                                                        <div class="custom-control custom-switch mr-2 mb-1"> 
                                                                                                            <input type="checkbox" class="custom-control-input" id="reminder_check" name="reminder_check">
                                                                                                            <label class="custom-control-label" for="reminder_check"></label>
                                                                                                        </div>
                                                                                                    </div>  
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" class="reminder_area"> 
                                                                                                <td style="text-align:right" colspan="2"> 
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
                                                                                                
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                    </div> 
                                                                </div>
                                                            </div>
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

<div id="toastSuccess" class="hidden fixed bottom-5 right-5 bg-green-600 text-white text-sm rounded px-4 py-2 shadow-xl z-50">
  Als erledigt markiert!
</div>


@endsection

@section('script')

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
 

<script>
    document.addEventListener("DOMContentLoaded", () => {
    // Initialize feather icons
    feather.replace();
 
    // Tab Switching
    const tabs = document.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(button => {
        button.addEventListener('click', () => {
            tabs.forEach(btn => btn.classList.remove('border-b-2', 'border-blue-500',
                'text-blue-600'));
            panels.forEach(panel => panel.classList.add('hidden'));

            button.classList.add('border-b-2', 'border-blue-500', 'text-blue-600');
            document.getElementById(`tab-${button.dataset.tab}`).classList.remove(
                'hidden');
        });
    });

 
});
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
                // Re-init sortable after HTML is injected 
                const noteList = document.getElementById('personal-note-list');

                 if (noteList) {
                    new Sortable(noteList, {
                        handle: '.drag-handle',
                        animation: 150,
                        onEnd: function (evt) {
                            // trigger reorder logic if needed
                        }
                    });
                }

                if (tab === 'calendar') {
                        initCalendar();
                    }

                    if (tab === 'notes') {
                        requestAnimationFrame(() => {
                            if (document.getElementById('personal-note-list')) {
                                loadNotes();
                            }
                        });
                    }


 
            })
            .catch(err => {
                panel.innerHTML = `<div class="text-red-500 p-4 text-sm">❌ Fehler beim Laden.</div>`;
                console.error('Error loading content:', err);
            });
    }


    let currentQuarterStart = new Date(); // Start from current month

    function initQuarterlyCalendar() {
        const container = document.getElementById('multiMonthCalendars');
        if (!container) return console.warn('❌ #multiMonthCalendars not found');
        container.innerHTML = ''; // Clear previous calendars

        for (let i = 0; i < 3; i++) {
            const calendarEl = document.createElement('div');
            calendarEl.style.width = '300px';
            calendarEl.classList.add('mini-calendar');
            container.appendChild(calendarEl);

            const displayMonth = new Date(currentQuarterStart);
            displayMonth.setMonth(currentQuarterStart.getMonth() + i);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: displayMonth.toISOString().split('T')[0],
                locale: 'de',
                firstDay: 1,
                selectable: true,
                headerToolbar: false,
                height: 'auto',

                select(info) {
                    loadMiniCalendarEvents(info.startStr, info.endStr);
                },

                dateClick(info) {
                    document.querySelectorAll('.fc-day-selected')
                        .forEach(el => el.classList.remove('fc-day-selected'));
                    info.dayEl.classList.add('fc-day-selected');
                    loadMiniCalendarEvents(info.dateStr, info.dateStr);
                },

                eventDidMount(info) {
                    const dateStr = info.event.startStr.split('T')[0];
                    const cell = calendarEl.querySelector(`.fc-day[data-date='${dateStr}']`);
                    if (cell) {
                        cell.classList.add('has-event');
                        cell.style.setProperty('--event-highlight-color', info.event.backgroundColor);
                    }
                },

                events: fetchCalendarEvents
            });

            calendar.render();
        }

        // Load events for today's date (once)
        if (currentQuarterStart.getMonth() === new Date().getMonth()) {
            const today = new Date().toISOString().split('T')[0];
            loadMiniCalendarEvents(today, today);
        }
    }

    function fetchCalendarEvents(fetchInfo, successCallback, failureCallback) {
        $.ajax({
            url: '/get_personal_task_calendar_mini',
            method: 'GET',
            success(response) {
                if (!Array.isArray(response.data)) return failureCallback();
                const events = response.data.map(item => ({
                    id: item.id,
                    title: `${item.type}: ${item.title}`,
                    start: item.start_date,
                    end: item.end_date || item.start_date,
                    backgroundColor: item.taskColor,
                    borderColor: item.taskColor,
                    extendedProps: {
                        employees: item.employees,
                        type: item.type,
                        priority: item.priority,
                        status: item.status,
                        public_view: item.public_view
                    }
                }));
                successCallback(events);
            },
            error: failureCallback
        });
    }


    function formatTime(timeString) {
        if (!timeString) return '';
        const [hour, minute] = timeString.split(':');
        return `${hour}:${minute} Uhr`;
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
                                    <div class="accordion-default collapse-bordered">
                                        <div class="cardss collapse-header">
                                            <div id="heading${note.id}" class="card-header collapse-header"  style="padding:0 !important;" data-toggle="collapse" role="button" data-target="#accordion${note.id}" aria-expanded="false" aria-controls="accordion1">
                                                <span class="lead collapse-title">
                                                   <h5 style="font-size: 13px;color: #d5d5d5;"> <i class="fa fa-chevron-down"></i> Details</h5>
                                                </span>
                                            </div>
                                            <div id="accordion${note.id}" role="tabpanel" data-parent="#accordionWrapa1" aria-labelledby="heading${note.id}" class="collapse">
                                                <div class="card-content p-0">
                                                    <div class="card-body p-0">
                                                         <div class="date">
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
                                                </div>
                                            </div>
                                        </div> 
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
        appointment: '#eab308',     // yellow
        ticket_task: '#2563eb',     // blue
        problem: '#10b981'          // green
    };

    typeStats.forEach(stat => {
        const canvas = document.getElementById(`chart_${stat.type}`);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const color = colors[stat.type] ?? '#64748b';

        typeCharts[stat.type] = updateDonutChart(typeCharts[stat.type], ctx, stat.today, color);
    });
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

            data.items.forEach(item => {
                const badgeColor = item.type === 'personal_task' ? 'red' :
                                   item.type === 'appointment' ? 'yellow' :
                                   item.type === 'ticket_task' ? 'blue' : 'green';

                const icon = item.type === 'personal_task' ? 'alert-circle' :
                             item.type === 'appointment' ? 'calendar' :
                             item.type === 'ticket_task' ? 'tool' : 'activity';

                goalList.innerHTML += `
                    <div class="goal-item border-l-4 border-${badgeColor}-500 bg-white rounded p-1 flex items-center justify-between shadow"
                         data-id="${item.id ?? ''}" data-type="${item.type}">
                        <div class="flex items-center gap-1">
                            <i data-feather="${icon}" class="text-${badgeColor}-500 w-4 h-4"></i>
                            <input type="checkbox" class="mark-done-checkbox">
                            <span class="text-xs font-medium">${item.title}</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 bg-${badgeColor}-100 text-${badgeColor}-600 rounded-full">
                            ${item.label.replace('_', ' ')}
                        </span>
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

        if (id && type) {
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


@endsection 