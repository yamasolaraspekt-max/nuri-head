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
<style>
    /* Custom Modal CSS */
    .custom-modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none;
        justify-content: center; align-items: center; opacity: 0;
        transition: opacity 0.3s ease;
    }
    .custom-modal-overlay.open { display: flex; opacity: 1; }
    
    .custom-modal-container {
        background: #fff; width: 90%; max-width: 1000px; max-height: 90vh;
        border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        display: flex; flex-direction: column; overflow: hidden;
        transform: translateY(20px); transition: transform 0.3s ease;
    }
    .custom-modal-overlay.open .custom-modal-container { transform: translateY(0); }

    .custom-modal-header {
        background: #74b2d4; color: white; padding: 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .custom-modal-title { font-size: 1.25rem; font-weight: 600; margin: 0; }
    .custom-modal-close { background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }

    .custom-modal-body { padding: 20px; overflow-y: auto; background: #f8f9fa; }
    
    /* Content Styling */
    .customer-card { background: white; border-radius: 8px; padding: 15px; margin-bottom: 15px; border-left: 5px solid #74b2d4; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .house-item { margin-top: 10px; padding: 10px; background: #f1f5f9; border-radius: 6px; }
    .product-badge { display: inline-block; padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; background: #e2e8f0; color: #475569; margin-top: 5px; }
    .status-badge { font-weight: bold; color: #74b2d4; }
</style>

<div class="grid grid-cols-1 sm:grid-cols-6 gap-4 mb-6 text-center">
    
    <div class="bg-white py-6 px-4 flex flex-col items-center justify-center col-span-2 rounded shadow-sm">
        <img src="{{ $image_path }}" alt="Profilbild" class="w-16 h-16 rounded-full mb-2 shadow">
        <h2 class="text-xl font-semibold text-gray-800">Willkommen, {{ $full_name }}!</h2>
        <p class="text-gray-500 text-sm">Schön, dass Sie wieder da sind.</p>
    </div> 
    
    <a href="{{ url('employee_daily_plan') }}"
        class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm transition hover:shadow-md"
        style="background-color: #74b2d4 !important;">
            <img src="{{ asset('images/icons/report.png') }}"
                alt="Report Logo"
                class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110 group-hover:rotate-1">
            <p class="text-xs mt-1 text-white font-bold">TAGESBERICHT</p>
    </a> 

    <div onclick="openMyModal('customers')"
         class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm cursor-pointer hover:bg-gray-50 transition">
        <img src="{{ asset('images/icons/customer.png') }}" onerror="this.src='https://img.icons8.com/color/96/group.png'" 
             alt="Kunden" class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110">
        <p class="text-xs mt-1 text-gray-600 font-bold uppercase">Meine Kunden</p>
    </div>

    <div onclick="openMyModal('projects')"
         class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm cursor-pointer hover:bg-gray-50 transition">
        <img src="{{ asset('images/icons/project.png') }}" onerror="this.src='https://img.icons8.com/color/96/project.png'" 
             alt="Projekte" class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110">
        <p class="text-xs mt-1 text-gray-600 font-bold uppercase">Meine Projekte</p>
    </div>

</div>

<div id="dynamicDataModal" class="custom-modal-overlay">
    <div class="custom-modal-container">
        <div class="custom-modal-header">
            <h3 class="custom-modal-title" id="modalTitle">Laden...</h3>
            <button class="custom-modal-close" onclick="closeMyModal()">&times;</button>
        </div>
        <div class="custom-modal-body" id="modalContent">
            <div class="flex justify-center p-10">
                <svg class="animate-spin h-10 w-10 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<script>
    function openMyModal(type) {
        const modal = document.getElementById('dynamicDataModal');
        const content = document.getElementById('modalContent');
        const title = document.getElementById('modalTitle');

        // Reset
        content.innerHTML = '<div class="text-center p-5">Laden...</div>';
        title.innerText = (type === 'customers') ? 'Meine Kunden' : 'Meine Projekte';
        
        // Show Modal
        modal.classList.add('open');

        // Fetch Data
        fetch(`{{ route('employee.my_data') }}?type=${type}`)
            .then(response => response.json())
            .then(data => {
                if(data.html) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<p class="text-center p-4 text-red-500">Keine Daten gefunden.</p>';
                }
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = '<p class="text-center p-4 text-red-500">Fehler beim Laden.</p>';
            });
    }

    function closeMyModal() {
        document.getElementById('dynamicDataModal').classList.remove('open');
    }

    // Close on outside click
    document.getElementById('dynamicDataModal').addEventListener('click', function(e) {
        if (e.target === this) closeMyModal();
    });
</script>

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
                        <div class="bg-white py-6 px-4 flex flex-col items-center justify-center col-span-2 rounded shadow-sm">
                            <img src="{{ $image_path }}" alt="Profilbild" class="w-16 h-16 rounded-full mb-2 shadow">
                            <h2 class="text-xl font-semibold text-gray-800">Willkommen, {{ $full_name }}!</h2>
                            <p class="text-gray-500 text-sm">Schön, dass Sie wieder da sind.</p>
                        </div> 
                        
                        <a href="{{ url('employee_daily_plan') }}"
                            class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm transition hover:shadow-md"
                            style="background-color: #74b2d4 !important;">
                                <img src="{{ asset('images/icons/report.png') }}"
                                    alt="Report Logo"
                                    class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110 group-hover:rotate-1">
                                <p class="text-xs mt-1 text-white font-bold">TAGESBERICHT</p>
                        </a> 

                        <div onclick="openMyModal('customers')"
                            class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm cursor-pointer hover:bg-gray-50 transition">
                            <img src="{{ asset('images/icons/customer.png') }}" onerror="this.src='https://img.icons8.com/color/96/group.png'" 
                                alt="Kunden" class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110">
                            <p class="text-xs mt-1 text-gray-600 font-bold uppercase">Meine Kunden</p>
                        </div>

                        <div onclick="openMyModal('projects')"
                            class="bg-white py-6 px-4 flex flex-col items-center justify-center group rounded shadow-sm cursor-pointer hover:bg-gray-50 transition">
                            <img src="{{ asset('images/icons/project.png') }}" onerror="this.src='https://img.icons8.com/color/96/project.png'" 
                                alt="Projekte" class="w-16 h-16 mb-2 transition-transform duration-300 transform group-hover:scale-110">
                            <p class="text-xs mt-1 text-gray-600 font-bold uppercase">Meine Projekte</p>
                        </div>

                    </div>

                    <div id="dynamicDataModal" class="custom-modal-overlay">
                        <div class="custom-modal-container">
                            <div class="custom-modal-header">
                                <h3 class="custom-modal-title" id="modalTitle">Laden...</h3>
                                <button class="custom-modal-close" onclick="closeMyModal()">&times;</button>
                            </div>
                            <div class="custom-modal-body" id="modalContent">
                                <div class="flex justify-center p-10">
                                    <svg class="animate-spin h-10 w-10 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Goal List Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="col-span-3 bg-white p-4" id="dueTodayCard">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-primary text-sm font-semibold leading-tight">
                                    Verbleibende Aufgaben
                                </p>
                                <p class="text-[11px] text-gray-500">
                                    Heute fällige Aufgaben, Termine, Anfragen & Tickets
                                </p>
                            </div>

                            <!-- Progress Badge -->
                            <span id="todayPercentBadge"
                                class="inline-flex items-center rounded-full border px-3 py-1 text-[11px] font-semibold
                                        bg-emerald-50 border-emerald-200 text-emerald-700">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                                <span id="todayPercentText">0% erledigt</span>
                            </span>
                        </div>

                        <!-- Filter + Sort -->
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <div id="goalFilterBar" class="flex flex-wrap gap-1 text-[11px]">
                                {{-- Chips – counts werden per JS gefüllt --}}
                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="all">
                                    Alle <span class="ml-1 text-[10px] opacity-70" id="count-all">(0)</span>
                                </button>

                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="lead">
                                    Lead <span class="ml-1 text-[10px] opacity-70" id="count-lead">(0)</span>
                                </button>

                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="anfrage">
                                    Anfrage <span class="ml-1 text-[10px] opacity-70" id="count-anfrage">(0)</span>
                                </button>

                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="aufgabe">
                                    Aufgabe <span class="ml-1 text-[10px] opacity-70" id="count-aufgabe">(0)</span>
                                </button>

                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="appointment">
                                    Termin <span class="ml-1 text-[10px] opacity-70" id="count-appointment">(0)</span>
                                </button>

                                <button type="button" class="goal-filter-chip px-2 py-1 rounded-full border text-gray-600 bg-gray-50"
                                        data-filter="rest">
                                    Rest <span class="ml-1 text-[10px] opacity-70" id="count-rest">(0)</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-1 text-[11px] text-gray-600">
                                <span>Sortierung:</span>
                                <select id="goalSortSelect"
                                        class="border rounded px-2 py-1 text-[11px] focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    <option value="due_asc">Fälligkeit ↑</option>
                                    <option value="due_desc">Fälligkeit ↓</option>
                                    <option value="prio_desc">Priorität hoch → niedrig</option>
                                    <option value="prio_asc">Priorität niedrig → hoch</option>
                                </select>
                            </div>
                        </div>

                        <div id="goalList"
                            class="space-y-2 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300"
                            style="max-height: 300px;"></div>
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
    let allItems      = [];
    let counters      = {};
    let currentFilter = 'all';
    let currentSort   = 'due_asc';

    function showToast(message = 'Als erledigt markiert!') {
        const toast = document.getElementById('toastSuccess');
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    // --- Badge helper -------------------------------------------------

    function updateTodayBadge(percent) {
        const badge  = document.getElementById('todayPercentBadge');
        const textEl = document.getElementById('todayPercentText');
        if (!badge || !textEl) return;

        const p = Number(percent) || 0;
        textEl.textContent = `${p}% erledigt`;

        // reset palette
        badge.classList.remove(
            'bg-emerald-50','border-emerald-200','text-emerald-700',
            'bg-amber-50','border-amber-200','text-amber-700',
            'bg-rose-50','border-rose-200','text-rose-700'
        );

        let bg, border, text;
        if (p >= 80) {
            bg = 'bg-emerald-50'; border = 'border-emerald-200'; text = 'text-emerald-700';
        } else if (p >= 40) {
            bg = 'bg-amber-50'; border = 'border-amber-200'; text = 'text-amber-700';
        } else {
            bg = 'bg-rose-50'; border = 'border-rose-200'; text = 'text-rose-700';
        }
        badge.classList.add(bg, border, text);
    }

    // --- Priority helper (for sorting) -------------------------------

    function mapPriority(prio) {
        if (!prio) return 1;
        const p = String(prio).toLowerCase();
        if (p.includes('very high') || p.includes('sehr')) return 3;
        if (p.includes('high') || p.includes('dring'))    return 2;
        if (p.includes('low') || p.includes('niedrig'))   return 0.5;
        // normal / mittel / default
        return 1;
    }

    // --- Date helpers -------------------------------------------------

    function isOverdue(dueDate) {
        if (!dueDate) return false;
        const due = new Date(dueDate);
        const now = new Date();
        const diff = (now - due) / (1000 * 60 * 60 * 24); // days
        return diff > 2;
    }

    function toDateOrNull(val) {
        if (!val) return null;
        const d = new Date(val);
        return isNaN(d.getTime()) ? null : d;
    }

    // --- Counters + filter chips --------------------------------------

    function updateCountersView(c) {
        counters = c || {};

        const safeGet = (path, def = 0) => {
            try {
                return path.split('.').reduce((acc, k) => acc && acc[k], counters) ?? def;
            } catch {
                return def;
            }
        };

        const map = {
            'all':        safeGet('total.count', allItems.length),
            'lead':       safeGet('lead.count', 0),
            'anfrage':    safeGet('anfrage.count', 0),
            'aufgabe':    safeGet('aufgabe.count', 0),
            'appointment':safeGet('appointment.count', 0),
            'rest':       safeGet('rest.count', 0),
        };

        Object.entries(map).forEach(([key, val]) => {
            const el = document.getElementById(`count-${key}`);
            if (el) el.textContent = `(${val})`;
        });
    }

    function setActiveFilterChip() {
        document.querySelectorAll('.goal-filter-chip').forEach(btn => {
            const f = btn.getAttribute('data-filter');
            if (f === currentFilter) {
                btn.classList.remove('bg-gray-50','text-gray-600','border-gray-200');
                btn.classList.add('bg-primary','text-white','border-primary');
            } else {
                btn.classList.remove('bg-primary','text-white','border-primary');
                btn.classList.add('bg-gray-50','text-gray-600','border-gray-200');
            }
        });
    }

    // --- Filter + Sort logic -----------------------------------------

        function getFilteredItems() {
            if (!Array.isArray(allItems)) return [];

            switch (currentFilter) {
                case 'aufgabe':
                    return allItems.filter(i => i.type === 'personal_task');
                case 'anfrage':
                    return allItems.filter(i => i.type === 'inquiry');
                case 'appointment':
                    return allItems.filter(i => i.type === 'appointment');
                case 'lead':
                    return allItems.filter(i => i.type === 'lead');
                case 'rest':
                    return allItems.filter(i =>
                        !['lead', 'personal_task', 'inquiry', 'appointment'].includes(i.type)
                    );
                case 'all':
                default:
                    return [...allItems];
            }
        }

    function sortItems(items) {
        const copy = [...items];

        copy.sort((a, b) => {
            const da = toDateOrNull(a.due_date);
            const db = toDateOrNull(b.due_date);
            const pa = mapPriority(a.priority);
            const pb = mapPriority(b.priority);

            switch (currentSort) {
                case 'due_desc':
                    if (da && db) return db - da;
                    if (da && !db) return -1;
                    if (!da && db) return 1;
                    return 0;

                case 'prio_desc':
                    return pb - pa;

                case 'prio_asc':
                    return pa - pb;

                case 'due_asc':
                default:
                    if (da && db) return da - db;
                    if (da && !db) return -1;
                    if (!da && db) return 1;
                    return 0;
            }
        });

        return copy;
    }

    function getItemLink(item) {
        switch (item.type) {
            case 'appointment':
                return `/appointment_details/${item.id}`;
            case 'personal_task':
                return `/personal_task_details/${item.id}`;
            case 'problem':
                return `/problem/profile/${item.id}`;
            case 'ticket_task':
                return `/ticket_task_details/${item.id}`;
            case 'inquiry':
                return `/inquiry_show/${item.id}`;
            case 'lead':
                // TODO: an deine echte Lead-Route anpassen
                return `/lead/product/${item.id}`;
            default:
                return '#';
        }
    }


    // --- Render list --------------------------------------------------

    function renderGoalList() {
        const goalList = document.querySelector('#goalList');
        if (!goalList) return;

        const filtered = getFilteredItems();
        const sorted   = sortItems(filtered);

        goalList.innerHTML = '';

        if (!sorted.length) {
            goalList.innerHTML = `
                <div class="text-[11px] text-gray-400 italic px-1">
                    Keine Einträge für diesen Filter.
                </div>
            `;
            feather.replace();
            return;
        }

        sorted.forEach(item => {
            const badgeColor =
                item.type === 'personal_task' ? '#dc2626' :
                item.type === 'appointment'   ? '#c0d8ea' :
                item.type === 'ticket_task'   ? '#164194' :
                item.type === 'inquiry'       ? '#f97316' : // orange
                item.type === 'lead'          ? '#6366f1' : // indigo for leads
                                                '#93c21c';

            const icon =
                item.type === 'personal_task' ? 'alert-circle' :
                item.type === 'appointment'   ? 'calendar' :
                item.type === 'ticket_task'   ? 'tool' :
                item.type === 'inquiry'       ? 'help-circle' :
                item.type === 'lead'          ? 'user' :
                                                'activity';


            const isOverdueAppointment = item.type === 'appointment' && isOverdue(item.due_date);

            const iconStyle = isOverdueAppointment
                ? 'animation: pulse-red 1s infinite; color: #dc2626 !important;'
                : `color:${badgeColor}`;

            const dueBlock = (item.type === 'appointment' && item.due_date)
                ? `
                    <div class="text-[10px] text-gray-500">
                        Fällig: ${new Date(item.due_date).toLocaleDateString('de-DE')}
                    </div>
                    <div class="text-[10px] text-yellow-500">
                        <i class="feather icon-info"></i> Dieser Aufgaben-erforderliche Bericht
                    </div>
                  `
                : '';

            goalList.innerHTML += `
                <div class="goal-item border-l-4 bg-white p-1 flex items-center justify-between"
                     style="border-left:6px solid ${badgeColor}; border-bottom: 1px solid #d5d1d1;"
                     data-id="${item.id ?? ''}" data-type="${item.type}">
                    <div class="flex items-center gap-1">
                        <i data-feather="${icon}" class="w-4 h-4" style="${iconStyle}"></i>
                        <input type="checkbox" class="mark-done-checkbox">
                        <div>
                            <span class="text-xs font-medium">
                                ${item.title ?? ''}${item.description ? ': ' + item.description : ''}
                            </span>
                            ${dueBlock}
                        </div>
                    </div>
                    <a href="${getItemLink(item)}"
                       class="text-xs px-2 py-0.5 rounded-full inline-block"
                       style="background:${badgeColor}; color:white;">
                        ${(item.label || '').replace('_', ' ')}
                    </a>
                </div>`;
        });

        feather.replace();
    }

    // --- Load data from backend --------------------------------------

    function loadDueToday() {
        fetch('/my/due-today')
            .then(res => res.json())
            .then(data => {
                updateTodayBadge(data.percent ?? 0);

                allItems = Array.isArray(data.items) ? data.items : [];
                updateCountersView(data.counters || {});

                setActiveFilterChip();
                renderGoalList();
            })
            .catch(err => {
                console.error('Error loading /my/due-today', err);
            });
    }

    // Initial load + auto-refresh
    loadDueToday();
    setInterval(loadDueToday, 10 * 60 * 1000);

    // --- Filter & sort UI events -------------------------------------

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.goal-filter-chip');
        if (!btn) return;

        const filter = btn.getAttribute('data-filter') || 'all';
        currentFilter = filter;
        setActiveFilterChip();
        renderGoalList();
    });

    document.addEventListener('change', function (e) {
        if (e.target.id === 'goalSortSelect') {
            currentSort = e.target.value || 'due_asc';
            renderGoalList();
        }
    });

    // --- POST helper + mark-done logic -------------------------------

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

    async function postJSON(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(payload)
        });

        const ct = res.headers.get('content-type') || '';

        if (ct.includes('application/json')) {
            const data = await res.json();
            if (!res.ok || data.success === false) {
                const msg = data.message || `HTTP ${res.status}`;
                throw new Error(msg);
            }
            return data;
        }

        const text = await res.text();
        let hint = `Unexpected response (Content-Type: ${ct || 'unknown'})`;
        if (res.status === 419) hint = 'CSRF token mismatch (419).';
        if (res.status === 401) hint = 'Unauthenticated (401).';
        if (res.status === 403) hint = 'Forbidden (403).';
        throw new Error(`${hint} First bytes: ${text.slice(0, 120)}`);
    }

    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('mark-done-checkbox')) return;

        const container = e.target.closest('.goal-item');
        if (!container) return;
        const id   = container.getAttribute('data-id');
        const type = container.getAttribute('data-type');

        if (id && type === 'appointment') {
            const checkbox = e.target;

            Swal.fire({
                title: 'Bericht schreiben',
                html: `<div id="quill-editor" style="height: 200px;"></div>`,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                cancelButtonText: 'Abbrechen',
                focusConfirm: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    const quill = new Quill('#quill-editor', { theme: 'snow' });
                    Swal.__quillInstance = quill;
                },
                preConfirm: async () => {
                    try {
                        const quill = Swal.__quillInstance;
                        const reportContent = quill?.root?.innerHTML || '';

                        if (!reportContent || reportContent === '<p><br></p>') {
                            Swal.showValidationMessage('Bitte einen Bericht schreiben.');
                            return false;
                        }

                        const data = await postJSON('/my/save-appointment-report', {
                            id,
                            report: reportContent
                        });

                        if (!data.success) {
                            throw new Error(data.message ?? 'Fehler beim Speichern.');
                        }
                    } catch (err) {
                        Swal.showValidationMessage(err.message || String(err));
                        return false;
                    }
                }
            }).then(result => {
                if (!result.isConfirmed) {
                    checkbox.checked = false;
                } else {
                    showToast();
                    loadDueToday();
                }
            });

        } else {
            postJSON('/my/mark-done', { id, type })
                .then(data => {
                    if (data.success) {
                        container.classList.add('opacity-50');
                        const checkbox = container.querySelector('.mark-done-checkbox');
                        if (checkbox) {
                            checkbox.disabled = true;
                            checkbox.checked = true;
                        }
                        const title = container.querySelector('span.text-xs.font-medium');
                        if (title) {
                            title.innerHTML += ' <i class="ri-check-line text-green-600 text-sm"></i>';
                        }
                        showToast();
                        loadDueToday();
                    } else {
                        throw new Error(data.message ?? 'Fehler beim Speichern.');
                    }
                })
                .catch(err => {
                    alert(err.message || String(err));
                    e.target.checked = false;
                });
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
<script>
    function openMyModal(type) {
        const modal = document.getElementById('dynamicDataModal');
        const content = document.getElementById('modalContent');
        const title = document.getElementById('modalTitle');

        // Reset
        content.innerHTML = '<div class="text-center p-5">Laden...</div>';
        title.innerText = (type === 'customers') ? 'Meine Kunden' : 'Meine Projekte';
        
        // Show Modal
        modal.classList.add('open');

        // Fetch Data
        fetch(`{{ route('employee.my_data') }}?type=${type}`)
            .then(response => response.json())
            .then(data => {
                if(data.html) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<p class="text-center p-4 text-red-500">Keine Daten gefunden.</p>';
                }
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = '<p class="text-center p-4 text-red-500">Fehler beim Laden.</p>';
            });
    }

    function closeMyModal() {
        document.getElementById('dynamicDataModal').classList.remove('open');
    }

    // Close on outside click
    document.getElementById('dynamicDataModal').addEventListener('click', function(e) {
        if (e.target === this) closeMyModal();
    });
</script>
@endsection 