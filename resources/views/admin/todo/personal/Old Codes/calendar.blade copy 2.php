@extends('admin.layouts.app')
@section('title')
Mein Kalendar
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
 
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

.fc-v-event {
    background-color: white !important;
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
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: normal !important;
}



/* Ensure the event container has enough height to be scrollable */
.fc-timeGridWeek-view .fc-timegrid-event {
    max-height: 600px;
    /* Set a maximum height for events to allow scrolling */
    overflow-y: auto;
    /* Enable scrolling for events */
}

.fc-day-today {
    background: #f1f1f1 !important;
}

.fc-toolbar-title {
    color: #626262;
}

.fc-timeGridWeek-view,
.fc-timeGridDay-view,
.fc-listWeek-view {
    background: white !important;
}

.fc-timeGridWeek-view {
    background: white !important;
}

.fc-timegrid-slot-minor {
    display: none !important;
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

.fc-popover {
    position: absolute !important;
}

.fc-license-message {
    display: none !important;
}

.emp_active {
    border: 3px solid #8fc73e;
}

.task-bg {
    background: #D6EAF9 !important;
}

.appointmetn-bg {
    background: #E5F0D5;
}

.fc-daygrid {
    background: white;
}

.fc-button-active {
    background: #74b2d4 !important;
}

.task-event {
    background-color: #D6EAF9 !important;
}

.appointment-event {
    background-color: #E5F0D5;
}

.calendar {
    height: 100% !important;
    /* Set a specific height for the calendar */
    overflow-y: auto;
    /* Allow vertical scrolling */
}


@media (max-width: 768px) {
    .fc-daygrid-day {
        min-height: 100px !important;
        /* Adjust height as needed */
    }

    .fc-daygrid-day-frame {
        height: 100%;
        /* Ensure it stretches */
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Center events inside */
    }

    .fc-daygrid-day-events {
        flex-grow: 1;
        /* Ensures it takes up available space */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fc-daygrid-event {
        font-size: 14px !important;
        /* Increase font size */
        padding: 8px !important;
        /* More padding */
        min-width: 80%;
        /* Ensures events don't shrink */
        text-align: center;
        display: inline-block;
    }
}

.fc-timeGridWeek-view .mobile_title {
    transform: rotate(90Deg) !important;
    color: gray;
}

.fc-timeGridWeek-view .mobile_view {

    display: flex;
    align-items: center;
    flex-direction: column;

}

.fc-timeGridWeek-view,
.fc-timeGridDay-view,
.fc-listWeek-view {
    height: auto !important;
    overflow-y: auto;
    /* Enable scrolling for time grid and list views */
}


.fc-popover .fc-timegrid-event {
    display: flex !important;
    position: relative !important;
    min-height: 20px !important;
    /* Ensures small events remain visible */
    width: auto !important;
    white-space: normal;
    font-size: 12px;
    padding: 4px;
}

.fc-popover .fc-timegrid-slot {
    height: 50px;
    /* Adjust slot height */
}
</style>

<style>
.new_task {
    display: none;
    /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* Center the div */
    background: #e7e6e6;
    z-index: 10;
    width: 30% !important;
    /* Default width */
    max-width: 3-% !important;
    max-height: 85vh;
    /* Ensures it doesn't go beyond 80% of viewport height */
    overflow-y: auto;
    /* Enables scrolling inside */

}



/* Ensure modal content area scrolls separately */
.new_task .modal-body {
    max-height: 85vh;
    /* Limit body height */
    overflow-y: auto;
    /* Enable scrolling */
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
    background: #e7e6e6 !important;
    z-index: 10;
    padding: 10px;
    border-top: 1px solid #ddd;
}

/* Responsive styles for mobile */
@media (max-width: 768px) {
    .new_task {
        width: 90% !important;
        /* 90% width on mobile */
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
    background:#2d3e50;
}

/* Style for close button */
.custom-confirm-btn {
    background-color: rgb(194, 58, 28) !important;
    /* Green */
    color: white !important;
    font-weight: bold;
    border-radius: 5px;
}

/* Style for view details button */
.custom-cancel-btn {
    background-color: #74b2d4 !important;
    /* Blue */
    color: white !important;
    font-weight: bold;
    border-radius: 5px;
}

/* Customize text and icon colors inside SweetAlert */
.swal2-html-container .custom-event a {
    font-size: 14px;
    color: #2c3e50 !important;
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

    #mini_calendar {
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

.fc-event-main-frame {
    display: none !important;
}

.fc-event-main {
    display: none !important;
}

.select2-selection__choice {
    border: 0 !important;
}

.line {
    width: 90%;
    border-bottom: 2px solid #b8b8b8;
    margin-top: 6px;
    margin-bottom: 6px;
}

.fc-timegrid-slots table tr {
    height: 34px !important;
}

.fc-timegrid-slots {
    overflow-y: auto;
    /* Enable vertical scrolling in time grid slots */
    max-height: 100%;
    /* Allow time grid to expand within the container */
}

/* Modal Design  */

.swal2-title,
.swal2-html-container {
    text-align: left !important;
}

.swal2-close {
    color: white !important;
}

.swal2-close:hover {
    color: red !important;
}

.edited-event {
    animation: blink-effect 1s ease-in-out 3;
    border: 3px solid red !important;
    /* Highlight with red border */
}

@keyframes blink-effect {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.2;
    }
}
</style>

<style>
#mini_calendar .fc-daygrid-day-events {
    display: none !important;
}

#mini_calendar .fc-dayGridMonth-view {
    background: #f1f1f1;
}

#mini_calendar .fc-daygrid-day-bottom {
    display: none !important;
}

#mini_calendar .fc-day-selected .fc-daygrid-day-frame::after {
    content: "";
    /* Required for pseudo-elements */
    position: absolute;
    top: 50%;
    left: 50%;
    width: 30px;
    /* Adjust size of the circle */
    height: 30px;
    background: #d4d4e4 !important;
    /* Blue background */
    border-radius: 50%;
    transform: translate(-50%, -50%);
    /* Center the circle */
    z-index: -1;
    /* Ensure it's behind the text */
}

#mini_calendar .fc-day-selected .fc-daygrid-day-frame {

    background: #d4d4e4 !important;
    /* Blue background */
    border-radius: 50%;
}

#mini_calendar .fc-day {
    padding: 0 !important;
    justify-items: center;
}

#mini_calendar .fc-toolbar-title {
    font-size: 19px;
}

#slider_section {
    overflow: hidden;
    height: 100%;
}

.employee_lists {
    overflow-y: auto;
    max-height: calc(100dvh - 300px);
    /* 100dvh is more accurate on mobile */
    padding-right: 4px;
    /* prevent content hiding behind scrollbar */
}

.employee_lists::-webkit-scrollbar {
    width: 6px;
}

.employee_lists::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 3px;
}


#slider_section::-webkit-scrollbar {
    width: 6px;
}

#slider_section::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
}

.fc-more-link {
    width: 45px;
    background: #f1f1f1;
}

.fc-more-link .fc-timegrid-more-link-inner {
    font-size: 22px;
    justify-self: anchor-center;
}


.mobile_view_event {
    font-family: 'Arial', sans-serif;
    font-size: 11px;
    line-height: 1.3;
    word-wrap: break-word;
    overflow-wrap: break-word;
}


.fc-timegrid-event {
    background-color: inherit !important;
    color: inherit !important;
}

.public-holiday-cell {
    background-color: #d3d3d3 !important;
}


.fc .public-holiday-cell {
    background-color: #f8f9fa !important;
    /* Light gray like Sunday */
}

#slider_section {
    transition: all 0.2s ease;
}


/* all Day style  */
.fc .fc-all-day-event {
    background-color: #e3f2fd !important;
    border-left: 4px solid #2196f3 !important;
    font-size: 12px;
    font-weight: bold;
    padding: 4px;
}
.custom-all-day {
    background-color: #ffedcc !important;
    border-left: 4px solid #ff9800 !important;
    color: #333 !important;
    font-weight: bold;
    padding: 4px 6px;
}

.custom-all-day .custom-event-header {
    display:none !important;
}

.custom-all-day .custom-event-header .custom-event-title > p {
    margin:0px !important;
    padding:0 !important;
}


.fc-event.recurring-leave {
  background: repeating-linear-gradient(45deg, #6c757d, #6c757d 5px, #9ca3af 5px, #9ca3af 10px);
  color: #fff !important;
  border: 1px solid #6c757d !important;
  border-radius: 6px;
  font-size: 11px;
  padding: 4px;
}
.fc-event.recurring-leave::before {
  content: "🔁 ";
  margin-right: 3px;
}


.picker-avatar {
  width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border:1px solid #dee2e6;
}
.picker-card {
  border:1px solid #e9ecef; border-radius:.5rem; padding:.5rem;
}
.picker-chip {
  display:flex; align-items:center; gap:.5rem; padding:.35rem .5rem; border:1px solid #e9ecef; border-radius:9999px; cursor:pointer;
}
.picker-chip.active { border-color:#8fc73e; background:#f6fff1; }
.picker-list-item { padding:.35rem .5rem; cursor:pointer; border-bottom:1px dashed #eee; }
.picker-list-item:hover { background:#f8f9fa; }
.fc-ticket-link:hover { opacity: .8; }

</style> 

@endsection 
@section('content') 

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12" style=" display: flex;">
                <h2 class="content-header-title float-left mb-0">KALENDER</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashbaord</a></li> 
                        <li class="breadcrumb-item active">Kalender</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-body">

        <div class="text-right mb-2">
             <button class="btn"    data-toggle="modal" data-target="#calendarSettingsModal">
               <i class="feather icon-settings"></i> Einstellungen
            </button>

            <!-- button here -->
        </div>

            <div class="row">
                <!-- Sidebar (Filters, Search, Employees) -->
                <div class="col-md-2 col-12" id="slider_section" style="overflow-y: auto;">
                    <div class="cards">
                        <div id="mini_calendar"></div>
                    </div>
                    <div class="cards mt-1">
                        <div class="card-body">
                            <div class="col-12 p-0">
                                <!-- Search Inputs -->
                                <div class="col-12 employee_search_input">
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="searchEmployee" id="employee_get"
                                            placeholder="Vorname, nachname,...">
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

            <div class="cards new_task_card new_task" style="display:none">
                <div class="card-header"
                    style="  border: 0;  background: transparent;  padding: 0;     justify-items: anchor-center;">
                    <h3 class="title mt-1 ml-2"
                        style="    color: #8fc73e !important; font-weight: bold;  justify-items: left;"> TERMIN
                        ERSTELLEN</h3>
                    <div class="line" style="    border-bottom: 2px solid #8fc73e; width:90% !important"></div>
                </div>
                <div class="card-body p-0">
                    <form id="task-store-form">
                        @csrf
                        <input type="hidden" name="id" id="appointment_id">

                        <div class="modal-body pt-0 pb-0">
                            <div class="cards p-1">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label>Typ</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input contact-type-toggle" type="radio" name="contact_mode" id="newContact" value="new" checked>
                                                <label class="form-check-label" for="newContact">Neu</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input contact-type-toggle" type="radio" name="contact_mode" id="selectContact" value="select">
                                                <label class="form-check-label" for="selectContact">Kontakt</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input contact-type-toggle" type="radio" name="contact_mode" id="ticketMode" value="ticket">
                                                <label class="form-check-label" for="ticketMode">Ticket</label>
                                            </div>
                                        </div>
                                        <input type="hidden" name="contact_mode" id="contact_mode" value="new">
                                       <input type="hidden" id="problem_id" name="problem_id">
                                        <input type="hidden" id="problem_task_id" name="problem_task_id">
                                        <input type="hidden" id="ticket_mode" name="ticket_mode" value="new"> <!-- new | select | ticket -->
                                        <input type="hidden" id="ticket_auto_create" name="ticket_auto_create" value="0">


                                        <div class="col-md-12 ticket-block d-none">
                                            <div class="row">
                                                <div class="col-md-12">
                                                <label>Kunde *</label>
                                                <select id="ticket_customer_id" class="form-control" style="width:100%"></select>
                                                </div>

                                                <div class="col-md-12 mt-1">
                                                <label>Problem (Ticket)</label>
                                                <select id="ticket_problem_id" class="form-control" style="width:100%"></select>
                                                </div>

                                                <div class="col-md-12 mt-1">
                                                <label>Ticket Task</label>
                                                <select id="ticket_task_id" class="form-control" style="width:100%">
                                                    <!-- Options will include existing tasks + a “create new from appointment title” option -->
                                                </select>
                                                </div>

                                                <!-- optional: reuse your product selector if you want to expose “Object/Produkt” in ticket mode too -->
                                                <div class="col-md-12 mt-1">
                                                <label>Leistung/Service (optional)</label>
                                                <select id="ticket_service_id" class="form-control" style="width:100%"></select>
                                                </div>
                                            </div>
                                            </div>

                                        <div class="col-md-12 col-12 contact-name-block">
                                            <label for="task_title">Kunde/Kontakt *</label>
                                            <input type="text" id="name" class="form-control name" name="name">
                                        </div>

                                        <div class="col-md-12 contact-select-block d-none">
                                            <label for="task_title">Kunde/Kontakt *</label>
                                            <select name="customer_id" id="customer_id" class="contact_list" style="width:100%"></select>
                                            <input type="hidden" name="contact_type" id="contact_type" value="">
                                        </div>

                                        <div class="col-md-12 product-select-block d-none">
                                            <label for="productSelect">Object/Produkt</label>
                                            <select id="productSelect" name="productSelect[]" class="form-control" multiple style="width:100%"></select>
                                            <input type="hidden" name="products" id="product">
                                        </div>



                                        <div class="col-md-10 col-10">
                                            <label for="task_title">Art des Termins</label>
                                            <input type="text" class="form-control"
                                                value="{{ old('appointment_type') }}" id="appointment_type"
                                                name="appointment_type">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="hidden" name="color" id="color" value="#8fc73e">
                                            <div class="btn-group dropup dropdown-icon-wrapper mt-1 "
                                                id="color_drop_down">
                                                <button type="button" class="btn btn-icon    waves-effect waves-light"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
                                                        <i class="fa fa-square" style="color: #5f9ea0;"></i>
                                                        Kadettenblau
                                                    </span>
                                                    <span class="dropdown-item" data-value="#d2691e">
                                                        <i class="fa fa-square" style="color: #d2691e;"></i>
                                                        Schokoladenbraun
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
                                                        <i class="fa fa-square" style="color: #9932cc;"></i> Dunkles
                                                        Lila
                                                    </span>
                                                    <span class="dropdown-item" data-value="#ff6347">
                                                        <i class="fa fa-square" style="color: #ff6347;"></i> Tomate
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Startdatum *</label>
                                            <input type="date" id="start_date" class="form-control" name="start_date"
                                                value="">

                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="start_date">Enddatum *</label>
                                            <input type="date" id="end_date" class="form-control" name="end_date"
                                                value="">

                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label for="start_time">Startzeit *</label>
                                            <input type="time" id="start_time" class="form-control" name="start_time"
                                                value="">
                                        </div>

                                        <div class="col-md-5 col-12 ">
                                            <label for="end_time">Endzeit </label>
                                            <input type="time" id="end_time" class="form-control" name="end_time">
                                        </div>
                                        <div class="col-md-5 col-12 ">
                                            <label for="total_time"> Dauer </label>
                                            <input type="number" id="total_time" class="form-control" name="total_time">
                                        </div>

                                        <div class="col-md-6">
                                            <div class="row">
                                                <!-- Öffentlich Switch -->
                                                <div class="col-md-4">
                                                    <label for="switchPublic">Öffentlich</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="switchPublic" name="public" checked>
                                                        <label class="custom-control-label" for="switchPublic">
                                                            <span class="switch-icon-left"><i
                                                                    class="feather icon-unlock"></i></span>
                                                            <span class="switch-icon-right"><i
                                                                    class="feather icon-lock"></i></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Kontakt Switch -->
                                                <div class="col-md-4">
                                                    <label for="switchContact">Anfrage</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="switchContact" name="is_contact">
                                                        <label class="custom-control-label" for="switchContact">
                                                            <span class="switch-icon-left"><i
                                                                    class="feather icon-user"></i></span>
                                                            <span class="switch-icon-right"><i
                                                                    class="feather icon-user-x"></i></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <label for="switchReport">Report</label>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="switchReport" name="is_report">
                                                        <label class="custom-control-label" for="switchReport">
                                                            <span class="switch-icon-left"><i
                                                                    class="feather icon-file-text"></i></span>
                                                            <span class="switch-icon-right"><i
                                                                    class="feather icon-file"></i></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- pre_type Dropdown (Shown only if is_contact is ON) -->
                                            <div class="form-group mt-2" id="preTypeBox" style="display: none;">
                                                <label for="pre_type">Typ</label>
                                                <select name="pre_type" id="pre_type" class="form-control select2">
                                                    <option value="">Auswählen</option>
                                                    <option value="Lead">Lead</option>
                                                    <option value="Lieferant">Lieferant</option>
                                                    <option value="Hersteller">Hersteller</option>
                                                    <option value="Kooperationspartner">Kooperationspartner</option>
                                                    <option value="Architekt">Architekt</option>
                                                    <option value="Nachunternehmer">Nachunternehmer</option>
                                                    <option value="Bank">Bank</option>
                                                    <option value="Versicherung">Versicherung</option>
                                                    <option value="Bewerber">Bewerber</option>
                                                    <option value="Sonstige">Sonstige</option>
                                                </select>
                                            </div>

                                            <div class="form-group mt-2" id="sourceBox" style="display: none;">
                                                <label for="pre_type">Quelle</label>
                                                <select name="source" id="source" class="form-control"
                                                    style="width: 100%">
                                                    <option></option>
                                                    <option value="Telefonisch">Telefonisch</option>
                                                    <option value="Persönlich">Persönlich</option>
                                                    <option value="Mail">Mail</option>
                                                    <option value="Nachbar">Nachbar</option>
                                                    <option value="Empfehlung">Empfehlung</option>
                                                    <option value="Solarrechner">Solarrechner</option>
                                                    <option value="Herstellerlead">Herstellerlead</option>
                                                    <option value="Event">Event</option>
                                                    <option value="Messe">Messe</option>
                                                    <option value="Hausmesse">Hausmesse</option>
                                                    <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
 
                                        <div class="col-md-12 col-12 mt-1">
                                            <label for="task_title">Teilnehmer *</label>
                                            <button type="button" id="btnClearEmployees" class="btn  ">
                                                <i class="feather icon-x-circle"></i> Auswahl leeren
                                            </button>
                                               
                                            <select name="employee[]" id="employee" class="employee" multiple
                                                style="width:100%">
                                                @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}"
                                                    data-image="{{asset('images/employee/'.$emp->image) }}">
                                                    {{ $emp->name }}</option>
                                                @endforeach
                                            </select>

                                            <div class="d-flex align-items-center mt-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="openPickerBtn">
                                                    <i class="feather icon-users"></i> Auswahl öffnen
                                                </button>
                                                <small class="text-muted ml-1">Wähle Mitarbeiter oder füge ganze Teams hinzu</small>
                                            </div> 
                                        </div>
 
                                        <div class="col-md-6" style="display:none;" id="link_section">
                                            <span>Link </span>
                                            <input type="text" class="form-control" value="{{ old('link') }}" id="link"
                                                name="link">
                                        </div>

                                        <div class="col-md-6" id="intern" style="display: none;">
                                            <label for="task_title">Adress </label>
                                            <select name="branch_address_id" class="form-control">
                                                <option></option>
                                                @foreach ($branch_addresses as $address)
                                                <option value="{{ $address->id }}" data-street="{{ $address->street }}"
                                                    data-latitude="{{ $address->latitude }}"
                                                    data-longitude="{{ $address->longitude }}"
                                                    data-city="{{ $address->city }}"
                                                    data-postcode="{{ $address->postcode }}">
                                                    {{ $address->branch_initial }} - {{ $address->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6" id="extern">
                                            <label for="task_title">Adress </label>
                                            <input id="full_address" type="text" class="form-control form-element"
                                                placeholder="Adresse eingeben" name="full_address" value="">

                                            <input type="hidden" id="street-input" name="street" value="">
                                            <input type="hidden" id="city-input" name="city" value="">
                                            <input type="hidden" id="latitude-input" name="latitude" value="">
                                            <input type="hidden" id="longitude-input" name="longitude" value="">
                                            <input type="hidden" id="postal_code-input" name="postcode" value="">
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

                                        <div class="col-md-6">
                                            <label for="task_title">Telefon</label>
                                            <input type="text" class="form-control phone" value="{{ old('phone') }}"
                                                name="phone" id="phone">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="task_title">Email <small>Optional</small></label>
                                            <input type="email" class="form-control email" value="{{ old('email') }}"
                                                name="email" id="email">
                                        </div> 

                                        <div class="col-md-12 col-12 mb-1">
                                            <label for="task_title">Beschreibung</label>

                                            <textarea name="description" class="form-control" id="description" rows="1"></textarea>
                                        </div> 

                                        <div class="col-md-4">
                                            <label for="task_title">Betrieb</label>
                                            <select name="branch_id" id="branch_id" class="selectables"
                                                style="width:100%">
                                                <option></option>
                                                @foreach($branches as $br)
                                                <option value="{{ $br->id}}">{{$br->branch}} </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12 line"></div>

                                        <div class="col-md-4 col-12 ">
                                            <label for="month">Nachfasstermin</label>
                                            <input type="date" name="reminder_date" class="form-control"
                                                id="reminder_date">
                                        </div>

                                        <div class="col-md-4 col-12 ">
                                            <label>Nächster Schritt</label>
                                                <select name="next_step" class="form-control select2" id="next_step" style="width:100%">
                                                    <option value="">Bitte wählen</option>
                                                    <option value="Rückruf erledigen">Rückruf erledigen</option>
                                                    <option value="Problem klären">Problem klären</option>
                                                    <option value="E-Mail senden">E-Mail senden</option>
                                                    <option value="Angebot nachfassen">Angebot nachfassen</option>
                                                    <option value="Projektbesprechung vorbereiten">Projektbesprechung vorbereiten</option>
                                                    <option value="Kein weiterer Schritt">Kein weiterer Schritt</option>
                                                </select>
                                        </div>

                                        <div class="col-md-4 col-12 ">
                                            <label>Verantwortlicher</label>
                                                <select name="report_responsible[]" class="form-control select2" id="report_responsible" style="width:100%">
                                                    <option value="">Bitte wählen</option>
                                                    @foreach ($allEmployees as $employee)
                                                        <option value="{{$employee->id}}"> {{$employee->name}} {{$employee->lastname}}</option>
                                                    @endforeach
                                                </select>
                                        </div>

                                        <div class="col-md-6 col-12 ">
                                            <label for="priority">Priorität</label>
                                            <select name="priority" class="form-control" id="priority">
                                                <option value="normal" data-icon="fa fa-battery-empty">Keiner</option>
                                                <option value="medium" data-icon="fa fa-battery-half">Medium</option>
                                                <option value="high" data-icon="fa fa-battery-full">Hoch</option>
                                                <option value="very high" data-icon="fa fa-fire warning">Sehr Wichtig
                                                </option>

                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12  ">
                                            <label for="date_type">Wiederholung</label>
                                            <select name="date_type" id="date_type" class="form-control"
                                                style="width:100%">
                                                <option>Wählen</option>
                                                <option value="day">Ganzer Tag</option>
                                                <option value="week">7 Tage (Eine Woche)</option>
                                                <option value="daily">Täglich</option>
                                                <option value="weekly">Wochen</option>
                                                <option value="monthly">Monatlich</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12" id="week_dropdown_container" style="display:none;">
                                            <label for="week_select">Wähle Woche(n)</label>
                                            <select id="week_select" name="week_select[]" class="form-control"
                                                style="width: 100%;">
                                                <!-- Dynamic options will be added here -->
                                            </select>
                                        </div>

                                        <div class="col-md-6 col-12 from_day ">
                                            <label for="end_time">Von</label>
                                            <select name="from_day" id="from_day" class="form-control"
                                                style="width:100%">
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
                                            <select name="from_month" id="from_month" class="form-control"
                                                style="width:100%">
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
                                            <select name="to_month" id="to_month" class="form-control"
                                                style="width:100%">
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

                        <div class="modal-footer" style="border:0;">
                            <button type="button"
                                class="btn btn-danger mr-1 waves-effect waves-light btn-sm close_task_window"
                                data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                            <button type="button" class="btn btn-primary save-task btn-sm"><i
                                    class="feather icon-save"></i> speichern</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div> 
<!-- Team/Employee Picker Modal -->
<div class="modal fade" id="pickerModal" tabindex="-1" role="dialog" aria-labelledby="pickerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="pickerModalLabel">Teilnehmer auswählen</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0">
        <ul class="nav nav-tabs px-2 pt-2" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="tab-employees" data-toggle="tab" href="#pane-employees" role="tab">Mitarbeiter</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="tab-teams" data-toggle="tab" href="#pane-teams" role="tab">Teams</a>
          </li>
        </ul>

        <div class="tab-content p-2">
          <!-- Tab 1: Employees -->
          <div class="tab-pane fade show active" id="pane-employees" role="tabpanel" aria-labelledby="tab-employees">
            <div class="form-group mb-2">
              <input type="text" class="form-control" id="pickerEmployeeSearch" placeholder="Mitarbeiter suchen…">
            </div>
            <div id="pickerEmployeeGrid" class="d-flex flex-wrap" style="gap:10px;"></div>
          </div>

          <!-- Tab 2: Teams -->
          <div class="tab-pane fade" id="pane-teams" role="tabpanel" aria-labelledby="tab-teams">
            <div class="row no-gutters">
              <div class="col-md-4 border-right">
                <div class="form-group px-2">
                  <input type="text" class="form-control" id="pickerTeamSearch" placeholder="Team suchen…">
                </div>
                <div id="pickerTeamList" style="max-height: 60vh; overflow:auto;"></div>
              </div>
              <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center px-2">
                  <h6 class="m-0"><span id="pickerTeamTitle">Team</span></h6>
                  <div>
                    <button class="btn btn-sm btn-light" id="pickerSelectAllTeam">Alle markieren</button>
                    <button class="btn btn-sm btn-light" id="pickerClearTeam">Leeren</button>
                    <button class="btn btn-sm btn-success" id="pickerApplyTeam"><i class="feather icon-check"></i> Übernehmen</button>
                  </div>
                </div>
                <div id="pickerTeamMembers" class="p-2"></div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /modal-body -->

      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" id="pickerApplyAll"><i class="feather icon-save"></i> Auswahl übernehmen</button>
      </div>
    </div>
  </div>
</div> 
<!-- Calendar Settings Modal -->
<div class="modal fade" id="calendarSettingsModal" tabindex="-1" role="dialog" aria-labelledby="calendarSettingsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="calendarSettingsForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="calendarSettingsLabel">Einstellungen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Favorite Employees -->
                <div class="form-group">
                    <label for="favoriteEmployees">Favoriten Mitarbeiter</label>
                    <select id="favoriteEmployees" class="form-control employee" multiple style="width:100%;">
                        @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" data-image="/images/employee/{{ $emp->image }}">
                            {{ $emp->name }} {{ $emp->lastname }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Hidden Views -->
                <div class="form-group">
                    <label>Ausgeblendete Ansichten</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="year"> Jahr</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="month"> Monat</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="week"> Woche</label><br>
                </div>

                <!-- Calendar Color -->
                <div class="form-group">
                    <label for="calendarColorPicker">Kalenderfarbe</label>
                    <select id="calendarColorPicker" class="form-control">
                        <option value="default">Standard</option>
                        <option value="black">Schwarz</option>
                        <option value="red">Rot</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Speichern</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
            </div>
        </form>
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
<script>
$(document).ready(function() {
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
<script>
    const settings = {
        favorite_employee_ids: @json($favorite_employee_ids)
    };
    window.favoriteEmployeeIds = settings.favorite_employee_ids || [];

    (function normalizeFavoriteIds(){
    const fromBlade = (settings.favorite_employee_ids || settings.favorite_employees || []);
    window.favoriteEmployeeIds = fromBlade.map(String);
    })();
</script>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        // Define the mobile/tablet breakpoint
        const maxWidthForMobile = 1024;

        // Check screen width and redirect if it's mobile or tablet
        if (window.innerWidth < maxWidthForMobile) {
            window.location.href = "{{ route('get.employee.calendar.mobile') }}";
        }
    });
</script>

<!-- Filter by Employee  -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const calendarEl = document.querySelector(".calendar");
    const taskViewModal = document.querySelector("#task_view");
    const newTaskDiv = document.querySelector(".new_task");
    const startDateInput = document.querySelector("#start_date");
    const endDateInput = document.querySelector("#end_date");
    const startTimeInput = document.querySelector("#start_time");
    const miniCalendarEl = document.getElementById("mini_calendar");
    let publicHolidayDates = [];
     let calendar;
    let rowIndex = 1; // Initialize the row index
    let miniCalendar;
    let favoriteEmployeeIds = [];
 
    // use the global bag
    window.selectedEmployeeIds = new Set((window.favoriteEmployeeIds || []).map(String));
    let didAutoselectFavorites = (window.selectedEmployeeIds && window.selectedEmployeeIds.size > 0);


    function collectSelectedFromDOM() {
    const idsFromChecks = Array.from(document.querySelectorAll('.employee_check:checked'))
        .map(cb => String(cb.dataset.id));
    const idsFromSelect2 = ($('#employee').val() || []).map(String);
    // union with whatever we remembered before
    return new Set([...idsFromChecks, ...idsFromSelect2, ...Array.from(window.selectedEmployeeIds || [])]);

    }

    function applySelectionToRowElements(empId, imageEl, checkboxEl, borderColor) {
    const isSelected = window.selectedEmployeeIds.has(String(empId));

    checkboxEl.checked = isSelected;
    if (imageEl) {
        imageEl.classList.toggle('emp_active', isSelected);
        imageEl.style.borderColor = isSelected ? (borderColor || 'red') : 'transparent';
    }
    }



    window.addEventListener("resize", function() {
        if (!calendar) return; // 👈 avoid errors during init
        if (window.innerWidth < 768 && calendar.view.type !== "listWeek") {
            calendar.changeView("listWeek");
        } else if (window.innerWidth >= 768 && calendar.view.type !== "timeGridWeek") {
            calendar.changeView("timeGridWeek");
        }
    });
   
 
    let currentSearch = '';
    function initializeCalendar(events = []) {
        if (calendar) {
            calendar.getEventSources().forEach(source => source.remove());
            calendar.addEventSource(events);
            calendar.refetchEvents();
            return;

        }

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "timeGridWeek",
            locale: "de",
            firstDay: 1,
            weekNumbers: true, // Enable week numbers
            weekNumberCalculation: "ISO", // Use ISO week numbers
            weekNumberFormat: {
                week: "numeric"
            }, // Show the week number in numeric format
            allDaySlot: true,
            allDayText: "Ganztägig", 
            // titleFormat: { weekday: 'long' }, // Shows only "Mo", "Di", "Mi" (short weekday names)
            // titleFormat: { month: 'long' }, //  Shows only "Februar", "März", etc.
            //eventMinHeight: 100, // Ensure event is always readable
            // dayHeaderFormat: { weekday: 'long' }, // Shows only "Mo", "Di", "Mi", etc.
            dayHeaderFormat: {
                weekday: 'short',
                day: 'numeric'
            }, // Shows "Mi 18", "Do 19", etc. 
            eventOverlap: false, // Prevent event overlap
            eventMaxStack: 5,
            eventOrder: ['start', 'priority', 'title'],
            eventDisplay: 'block',
            slotEventOverlap: true, // allow side-by-side stacking
            eventOverlap: true, 

            slotMinTime: "05:00:00", // ⏰ Start at 7 AM
            slotMaxTime: "23:59:59", // ⏰ End at 8 PM
            slotDuration: "00:30:00", // ⏳ Each slot = 30 min (increase to 60 min for bigger cells)
            slotLabelInterval: "01:00:00", // 🏷 Label every 1 hour
             //slotEventOverlap: false, Prevent overlap
            nowIndicator: true, // Show current time indicator
            eventMinHeight: 20, // Minimum event height
            displayEventTime: true, // Show time in event
            eventOverlap: true, // Prevent event overlap
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }, // 24-hour format
            height: "auto",
            expandRows: true, // Ensures rows expand dynamically

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
                left: "prev,next today toggleSlider verfgBtn searchBox", // add searchBox here
                center: "title",
                right: "year,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
                },

            eventClassNames: function(arg) {
                    if (arg.event.allDay) {
                        return ['custom-all-day']; // 👈 class for styling all-day events
                    }
                    return [];
                },

            customButtons: {
                toggleSlider: {
                    text: "⇔",
                    click: function () {
                        const $slider = $('#slider_section');
                        const $calendarSection = $('.calender_section');

                        const isHidden = $slider.hasClass('d-none');

                        if (isHidden) {
                            $slider.removeClass('d-none');
                            $calendarSection.removeClass('col-md-12').addClass('col-md-9');

                            setTimeout(() => {
                                if (typeof miniCalendar !== 'undefined') {
                                    miniCalendar.render();
                                }
                            }, 10);
                        } else {
                            $slider.addClass('d-none');
                            $calendarSection.removeClass('col-md-9').addClass('col-md-12');
                        }

                        calendar.updateSize();
                    }
                },
                verfgBtn: {
                    text: "Verfügbarkeit",
                    click: function () {
                        window.location.href = "/employee-availability";
                    }
                },
                 searchBox: { // NEW placeholder; will be replaced with a <select>
                                text: 'Suche',
                                click: function(){ /* no-op */ }
                            }
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
                    duration: {
                        months: 12
                    },
                    buttonText: "Jahr",
                },
            },


            editable: true,
            eventResizableFromStart: true,
            events: events,


            eventClick: function(info) {
            const t = info.event.extendedProps.type;
            if (["holiday", "sick", "recurring_leave"].includes(t)) return;
            showEventDetailsModal(info.event);
            },
 
            dayCellDidMount: function(info) {
                const dateStr = info.date.toISOString().split('T')[0];

                if (publicHolidayDates.includes(dateStr)) {
                    // Match Sunday gray
                    info.el.style.backgroundColor = '#f8f9fa'; // Light gray used for Sundays
                    info.el.classList.add('public-holiday-cell');

                    // Optional icon/flag
                    const badge = document.createElement('div');
                    badge.innerText = '🇩🇪';
                    badge.style.position = 'absolute';
                    badge.style.top = '2px';
                    badge.style.right = '2px';
                    info.el.appendChild(badge);
                }
            },

            moreLinkClick: function(info) {
                const clickedDate = info.date;
                const eventsOnThatDay = calendar.getEvents().filter(event => {
                    const eventDate = event.start;
                    return eventDate.toDateString() === clickedDate.toDateString();
                });

                showCustomEventModal(eventsOnThatDay, clickedDate);
                return false;
            },


            dateClick: function(info) {
                const clickedDate = info.dateStr.split("T")[0]; // YYYY-MM-DD
                const clickedTime = info.dateStr.includes("T")
                    ? info.dateStr.split("T")[1].slice(0, 5)
                    : "00:00";

                // Get input fields
                const startDateInput = document.getElementById("start_date");
                const endDateInput = document.getElementById("end_date");
                const startTimeInput = document.getElementById("start_time");
                const reminderInput = document.getElementById("reminder_date");

                // Set start, end, time
                if (startDateInput) startDateInput.value = clickedDate;
                if (endDateInput) endDateInput.value = clickedDate;
                if (startTimeInput) startTimeInput.value = clickedTime;

                // ✅ Set reminder_date = start_date + 5 days
                // if (reminderInput) {
                //     const reminderDate = new Date(clickedDate);
                //     reminderDate.setDate(reminderDate.getDate() + 5);
                //     const isoReminder = reminderDate.toISOString().split("T")[0];
                //     reminderInput.value = isoReminder;

                //     console.log("📅 reminder_date gesetzt auf:", isoReminder);
                // }

                // Show the task form div
                if (typeof newTaskDiv !== "undefined" && newTaskDiv) {
                    newTaskDiv.style.display = "block";
                }
            },


            eventDidMount: function(info) {
                const event = info.event;
                const el = info.el;
                const { type, employees, priority, public, start_time, end_time } = event.extendedProps;

                // ✅ Highlight event after update (from ?task_id in URL)
                const urlParams = new URLSearchParams(window.location.search);
                const taskId = urlParams.get("task_id");
                if (taskId && event.id.split("-")[0] === taskId) {
                    el.classList.add("edited-event");
                    el.scrollIntoView({ behavior: "smooth", block: "center" });
                    setTimeout(() => el.classList.remove("edited-event"), 3000);
                }

                // 🎨 Special styling for recurring leaves
                if (type === "recurring_leave") {
                    el.style.pointerEvents = "none";
                    el.style.background = "repeating-linear-gradient(45deg, #8fc73e, #8ec73e95 5px, #8ec73e4b 5px, #8ec73e1d 10px)";
                    el.style.color = "#fff";
                    el.style.border = "1px solid #6c757d";
                    el.style.padding = "4px";
                    el.style.fontSize = "11px";
                    el.style.borderRadius = "6px";

                    el.innerHTML = `
                        <div style="display:flex; align-items:center; gap:4px;">
                            <span><i class="feather icon-user"></i></span>
                            <b>${event.title}</b>
                        </div>
                        <div style="font-size:10px; opacity:0.9;">
                            Wiederkehrender Urlaub
                        </div>
                    `;
                    return;
                }

                // ✅ Immutable events (public holiday, holiday, sick)
                if (["public_holiday", "holiday", "sick"].includes(type)) {
                    const employeeNames = (employees || []).map(emp => `${emp.name} ${emp.lastname}`).join(", ") || "";

                    el.style.pointerEvents = "none";
                    el.style.backgroundColor = "#999999";
                    el.style.border = "none";
                    el.style.color = "#fff";
                    el.style.padding = "3px 6px";
                    el.style.fontSize = "11px";
                    el.style.borderRadius = "4px";

                    el.innerHTML = `
                        <div style="font-size:11px; font-weight:bold;">${event.title}</div>
                        ${employeeNames ? `<div style="font-size:10px; opacity:0.8;">${employeeNames}</div>` : ""}
                    `;
                    return;
                }


                // 🔧 Helpers
                function formatTime(time) {
                    return (!time || time === "null" || time === "undefined")
                        ? "N/A"
                        : time.split(":").slice(0, 2).join(":");
                }
                function truncateText(text, maxLength) {
                    return text?.length > maxLength ? text.substring(0, maxLength) + "…" : text;
                }
                function hexToRGBA(hex, alpha = 1) {
                    hex = hex.replace(/^#/, "");
                    if (hex.length === 3) hex = hex.split("").map(c => c + c).join("");
                    const r = parseInt(hex.substring(0, 2), 16);
                    const g = parseInt(hex.substring(2, 4), 16);
                    const b = parseInt(hex.substring(4, 6), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                }

                // ✅ Event title truncation
                const truncatedTitle = truncateText(event.title, 20);
                const employeeNames = (employees || []).map(emp => `${emp.name} ${emp.lastname}`).join(", ");

                // ✅ Mobile compact style
                if (window.innerWidth <= 500 && calendar.view.type === "timeGridWeek") {
                    const backgroundColor = event.backgroundColor || "#006400";
                    const textColor = "#fff";
                    el.setAttribute("style", `
                        background-color: ${backgroundColor} !important;
                        color: ${textColor} !important;
                        border-left: 4px solid ${backgroundColor} !important;
                        border-radius: 6px !important;
                        padding: 5px !important;
                        white-space: nowrap !important;
                        overflow: hidden !important;
                        text-overflow: ellipsis !important;
                        font-size: 11px !important;
                        text-align: left !important;
                        max-width: 100px !important;
                    `);
                    el.innerHTML = `
                        <div><strong>${truncatedTitle}</strong></div>
                        <div style="font-size: 10px;">${formatTime(start_time)} - ${formatTime(end_time)}</div>
                    `;
                    return;
                }

                // ✅ Desktop / normal view
                el.classList.add("fc-daygrid-dot-event", "fc-event");
                el.innerHTML = "";
                el.setAttribute("style", `
                    white-space: normal !important;
                    border: 0px !important;
                    border-left: 5px solid ${event.backgroundColor} !important;
                    background-color: ${hexToRGBA(event.backgroundColor, 0.4)} !important;
                `);

                const { has_ticket, ticket_problem_id, ticket_task_id } = event.extendedProps || {};
                const ticketUrl = has_ticket ? makeTicketUrl(ticket_problem_id, ticket_task_id) : null;
                if (ticketUrl) {
                const a = el.querySelector('.fc-ticket-link');
                if (a) a.addEventListener('click', (ev) => ev.stopPropagation());
                }

                const ticketBtn = ticketUrl
                ? `<a href="${ticketUrl}" class="fc-ticket-link" title="Ticket öffnen"
                        target="_blank" rel="noopener"
                        style="display:inline-flex;align-items:center;margin-left:6px;text-decoration:none;color:#444">
                        ${TICKET_SVG}
                    </a>`
                : '';


                el.innerHTML = `
                    <div class="custom-event">
                        <div class="custom-event-header d-flex align-items-center" id="calendar_icons">
                            <i class="fa ${public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                            <i class="fa ${
                                priority === "very high" ? "fa-fire warning mr-1" 
                                : priority === "high" ? "fa-bell important mr-1" 
                                : ""
                            }"></i>
                            <p class="p-0 m-0" id="calendar_times" 
                            style="font-size:10px; color:${type === "task" ? "#74b2d4" : "#4c4c4c"};">
                            ${formatTime(start_time)} - ${formatTime(end_time)}
                            </p>
                        </div>
                        <div class="custom-event-title m-0">
                            <p style="font-size:10px;margin-bottom:0; color:${type === "task" ? "#74b2d4" : "#4c4c4c"}; font-weight:bold;">
                                ${truncatedTitle}
                            </p>
                            <p style="font-size:8px; color:${type === "task" ? "#74b2d4" : "#4c4c4c"};">
                                ${employeeNames}
                            </p>
                        </div>
                    </div>`;
            },


            eventDrop: handleEventUpdate,
            eventResize: handleEventUpdate,
        });


        let urlParams = new URLSearchParams(window.location.search);
        let taskId = urlParams.get("task_id");

        if (taskId) {
            let taskEvent = events.find(e => e.id.split("-")[0] === taskId);
            if (taskEvent) {
                calendar.gotoDate(taskEvent.start); // Move to the task's date
            }
        }


        let currentSearch = ''; // keep current search query

         function mountCalendarSearch() {
            // 1) If our Select2 already exists anywhere, just remove FC's fresh button and bail.
            if (document.getElementById('calendarSearch')) {
                const strayBtn = document.querySelector('.fc-searchBox-button');
                if (strayBtn) strayBtn.remove();
                return;
            }

            // 2) Find the custom-button placeholder FC rendered for us.
            const btn = document.querySelector('.fc-searchBox-button');
            if (!btn) return; // toolbar not ready yet

            // 3) Swap the button for our Select2.
            const wrapper = document.createElement('div');
            wrapper.style.display = 'inline-block';
            wrapper.style.minWidth = '280px';
            wrapper.innerHTML = `<select id="calendarSearch" style="width:280px"></select>`;
            btn.replaceWith(wrapper);

            // 4) Init Select2 once.
            const $sel = $('#calendarSearch');
            if ($sel.data('bound')) return; // hard guard, just in case

            $sel
                .data('bound', true)
                .select2({
                placeholder: 'Suchen… (Termin, Aufgabe, Mitarbeiter, Stadt)',
                minimumInputLength: 1,
                allowClear: true,
                width: 'style',
                ajax: {
                    url: "{{ route('calendar.search.suggest') }}",
                    dataType: 'json',
                    delay: 200,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                    results: (data.results || []).map(item => ({
                        id: item.id,
                        text: item.label,
                        type: item.type,
                        date: item.date,
                        image: item.image || null
                    }))
                    })
                },
                templateResult: function(item) {
                    if (!item.id) return item.text;
                    const pillMap = { appointment: 'Termin', task: 'Aufgabe', employee: 'Mitarbeiter', city: 'Ort' };
                    const pill = pillMap[item.type] || item.type;
                    const d = item.date ? ` <small style="opacity:.7">(${item.date})</small>` : '';
                    return $(
                    `<div><strong>${item.text}</strong> <span class="badge badge-light">${pill}</span>${d}</div>`
                    );
                },
                templateSelection: item => item.text || item.id
                })
                // on pick → set search, reload calendar, jump to date if provided
                .on('select2:select', function(e) {
                const sel = e.params.data;
                currentSearch = sel.text || '';

                reloadCalendarWithSearch(() => {
                    if (sel.date) calendar.gotoDate(sel.date);

                    // If they picked an employee: auto-select that employee filter
                    if (sel.type === 'employee') {
                    const id = String(sel.id);
                    const cb = document.querySelector(`.employee_check[data-id="${id}"]`);

                    if (cb) {
                        if (!cb.checked) {
                        cb.checked = true;
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        // employee chip not rendered → update global selection + Select2s
                        selectedEmployeeIds.add(id);
                        ensureOptionInEmployeeSelect(id, sel.text, sel.image);

                        // desktop Select2
                        $('#employee').val(Array.from(selectedEmployeeIds)).trigger('change');

                        // mobile Select2 (if present)
                        const $mobile = $('#mobileEmployeeSelect');
                        if ($mobile.length) {
                        const vals = ($mobile.val() || []).map(String);
                        if (!vals.includes(id)) {
                            $mobile.val([...vals, id]).trigger('change.select2');
                        }
                        }

                        loadCalendarTasks();
                    }
                    }
                });
                })
                // clear → remove filter and reload
                .on('select2:clear', function() {
                currentSearch = '';
                reloadCalendarWithSearch();
                });
            }

            // Helper: make sure #employee has an option for this id (so Select2 can display it)
            function ensureOptionInEmployeeSelect(id, label, imageUrl) {
            const $sel = $('#employee');
            if ($sel.find(`option[value="${id}"]`).length === 0) {
                const opt = new Option(label || `ID ${id}`, id, true, true);
                if (imageUrl) $(opt).attr('data-image', imageUrl);
                $sel.append(opt);
            }
            }


        // make sure search mounts after render and after view changes
        calendar.render();
        mountCalendarSearch();
        calendar.on('datesSet', mountCalendarSearch);

    }

    function reloadCalendarWithSearch(done){
        // remember current view & date for smooth UX
        const view = calendar.view.type;
        const date = calendar.getDate();
        loadCalendarTasks(() => {
            calendar.changeView(view);
            calendar.gotoDate(date);
            if (typeof done === 'function') done();
        });
        }



        function setHiddenTicketFields({problemId=null, problemTaskId=null, mode='new', autoCreate=0} = {}) {
        $('#problem_id').val(problemId || '');
        $('#problem_task_id').val(problemTaskId || '');
        $('#ticket_mode').val(mode || 'new');
        $('#ticket_auto_create').val(String(autoCreate || 0));
        }

        function currentContactMode() {
        // reuse your existing contact toggles if present
        const explicit = $('#contact_mode').val(); // 'new' | 'select' | maybe 'ticket'
        // if you added the radio with id="ticketMode"
        if ($('#ticketMode').is(':checked')) return 'ticket';
        return explicit || 'new';
        }

        // --- ticket select2 endpoints (adjust to your routes) ---
        const TICKET_ROUTES = {
        customers: "{{ route('ticket.customer.search') }}",   // ?q=
        problemsByCustomer: (id) => `/tickets/${id}/problems`, // returns [{id,text}]
        tasksByProblem: (pid) => `/problems/${pid}/tasks`,     // returns [{id,text}]
        };

        function initTicketSelects() {
        // customer search
        const $cust = $('#ticket_customer_id');
        if ($cust.length && !$cust.data('select2')) {
            $cust.select2({
            placeholder: 'Kunde wählen…',
            allowClear: true,
            ajax: {
                url: TICKET_ROUTES.customers, dataType: 'json', delay: 200,
                data: params => ({ q: params.term || '' }),
                processResults: data => ({ results: (data.results || data || []).map(x => ({id:x.id, text:x.text || `${x.name} ${x.lastname}`})) })
            }
            }).on('select2:select', function(e){
            const id = e.params.data.id;
            // reset downstream
            $('#ticket_problem_id').val(null).trigger('change');
            $('#ticket_task_id').val(null).trigger('change');
            loadProblemsForCustomer(id);
            }).on('select2:clear', function(){
            $('#ticket_problem_id').val(null).trigger('change');
            $('#ticket_task_id').val(null).trigger('change');
            });
        }

        // problems
        const $prob = $('#ticket_problem_id');
        if ($prob.length && !$prob.data('select2')) {
            $prob.select2({ placeholder: 'Ticket/Problem wählen…', allowClear: true });
            $prob.on('change', function(){
            const pid = $(this).val();
            $('#ticket_task_id').val(null).trigger('change');
            if (pid) loadTasksForProblem(pid);
            });
        }

        // tasks
        const $task = $('#ticket_task_id');
        if ($task.length && !$task.data('select2')) {
            $task.select2({ placeholder: 'Ticket-Task wählen…', allowClear: true });
        }

        // auto-create checkbox (optional UI)
        $('#ticket_create_task').on('change', function(){
            $('#ticket_auto_create').val(this.checked ? '1' : '0');
        });
        }

        async function loadProblemsForCustomer(customerId, preselectId=null) {
        try {
            const res = await fetch(TICKET_ROUTES.problemsByCustomer(customerId));
            const list = await res.json();
            const $sel = $('#ticket_problem_id');
            $sel.empty();
            (list || []).forEach(p => $sel.append(new Option(p.text || p.title || `#${p.id}`, p.id)));
            if (preselectId) { $sel.val(String(preselectId)).trigger('change'); }
        } catch (e) { console.error('loadProblemsForCustomer', e); }
        }

        async function loadTasksForProblem(problemId, preselectId=null) {
        try {
            const res = await fetch(TICKET_ROUTES.tasksByProblem(problemId));
            const list = await res.json();
            const $sel = $('#ticket_task_id');
            $sel.empty();
            (list || []).forEach(t => $sel.append(new Option(t.text || t.title || `#${t.id}`, t.id)));
            if (preselectId) { $sel.val(String(preselectId)).trigger('change'); }
        } catch (e) { console.error('loadTasksForProblem', e); }
        }



    function showEventDetailsModal(event) {
        const { extendedProps, id, title, end } = event;
        const {
            employees,
            taskColor,
            priority,
            report,
            appointment,
            public: isPublic,
            type,
            start_time,
            end_time,
            description,
            customer_id,
            contact_id,
            contact_type,
            city,
            street,
            postcode,
            phone,
            email,
            full_address,
            appointment_type
        } = extendedProps;

        const ticketUrl = extendedProps.has_ticket
          ? makeTicketUrl(extendedProps.ticket_problem_id, extendedProps.ticket_task_id)
           : null;
        const svg = (typeof TICKET_SVG !== 'undefined') ? TICKET_SVG : '🎫';
        const ticketAnchor = ticketUrl
            ? `<a href="${ticketUrl}" target="_blank" rel="noopener" title="Ticket öffnen"
                 style="margin-left:8px;display:inline-flex;align-items:center;color:#fff">${svg}</a>`
          : '';


        if (type === "holiday" || type === "sick") return;

        const cleanId = id ? id.toString().split("-")[0] : "";
        const detailsUrl = type === "appointment" ? `/appointment_details/${cleanId}` : `/personal_task_details/${cleanId}`;
        const hasCustomer = !!customer_id && customer_id !== "Null" && customer_id !== "-";
        const hasContact = !!contact_id && contact_id !== "Null" && contact_id !== "-";
        const displayAddress = full_address && full_address !== "-" && full_address !== "null" ? full_address : "-";

        const formatTime = time =>
            (!time || time === "null" || time === "undefined" || time === "-")
                ? "N/A"
                : time.split(":").slice(0, 2).join(":");

        const formatDate = dateString => {
            if (!dateString || dateString === "-") return "-";
            const date = new Date(dateString);
            return date.toLocaleDateString("de-DE", {
                day: "numeric",
                month: "short",
                year: "numeric"
            });
        };

        const priorityIcon = priority === "very high"
            ? '<i class="fa fa-fire warning mr-1"></i>'
            : priority === "high"
            ? '<i class="fa fa-bell important mr-1"></i>'
            : "";

        const reportIcon = report === "1"
            ? '<i class="feather icon-file-text warning mr-1"></i>'
            : "";

        const typeIcon = type === "appointment"
            ? '<i class="feather icon-calendar"></i>'
            : '<i class="fa fa-tasks"></i>';


        const customerLink = hasCustomer
            ? `/new_lead_profile/${customer_id}`
            : hasContact
            ? `/inquiry_show/${contact_id}`
            : "#";

        const customerIcon = hasCustomer || hasContact
            ? '<i class="feather icon-users white"></i>'
            : '<i class="feather icon-user-x white"></i>';

        const employeeList = (employees || []).map(employee => `
            <li data-toggle="tooltip" title="${employee.name} ${employee.lastname}">
                <img src="/images/employee/${employee.image || "default-avatar.png"}" alt="Avatar"
                    height="30" width="30" class="rounded-circle">
            </li>`).join('');

        const actionMenu = `
            <div class="dropdown" style="position: absolute; top: 112px;">
                <button class="btn btn-sm" type="button" id="eventActionMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-more-vertical"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="eventActionMenu">
                    <a class="dropdown-item duplicate-event" id="copy_event" data-event-id="${cleanId}">
                        <i class="feather icon-copy"></i> Duplizieren
                    </a>
                    <a class="dropdown-item edit-event" data-event-id="${cleanId}">
                        <i class="feather icon-edit"></i> Bearbeiten
                    </a>
                    <a class="dropdown-item text-danger" href="#" id="delete_event" data-event-type="${type}" data-event-id="${cleanId}">
                        <i class="feather icon-trash"></i> Löschen
                    </a>
                </div>
            </div>`;

        const eventDetails = `
            <div class="custom-event">
                <div class="custom-event-header d-flex align-items-center">
                    <i class="fa ${isPublic !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                    ${priorityIcon}
                     ${reportIcon} 
                    ${ticketAnchor}
                    <span class="custom-event-status-text">
                        ${typeIcon}
                        <i class="feather icon-info warning info_popup" data-id="${cleanId}" data-type="${type}"></i>
                        ${type === "appointment" ? `<i class="feather icon-map show_map" data-id="${cleanId}"></i>` : ""}
                        <span class="calendar_menu">${actionMenu}</span>
                        <a href="${customerLink}" target="_blank" style="margin-left:8px;">${customerIcon}</a>
                    </span>
                </div>
                <div class="custom-event-title mt-1">
                    <a href="${detailsUrl}" style="font-size:13px; color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                        ${description || title}
                    </a>
                    ${appointment_type && appointment_type !== "-" ? `
                        <p style="font-size:12px; color:#fff;"><strong>Typ:</strong> ${appointment_type}</p>` : ""}
                    <p style="font-size:13px; color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                        <i class="feather icon-calendar"></i> ${formatDate(end)}
                    </p>
                    <p style="font-size:13px; color:${type === "task" ? "#74b2d4" : "#93c21c"};">
                        <i class="feather icon-clock"></i> ${formatTime(start_time)} - ${formatTime(end_time)}
                    </p>
                </div>
                <div class="mt-2">
                    ${phone && phone !== "-" ? `<p style="font-size:13px;"><i class="feather icon-phone"></i> ${phone}</p>` : ""}
                    ${email && email !== "-" ? `<p style="font-size:13px;"><i class="feather icon-mail"></i> ${email}</p>` : ""}
                    ${displayAddress && displayAddress !== "-" ? `<p style="font-size:13px;"><i class="feather icon-map-pin"></i> ${displayAddress}</p>` : ""}
                </div>
                <ul class="list-unstyled users-list m-0 d-flex align-items-center mt-3">
                    ${employeeList}
                </ul>
                <div id="customerDetails"></div>
            </div>`;

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
                popup: "custom-swal-popup",
                confirmButton: "custom-confirm-btn",
                cancelButton: "custom-cancel-btn"
            },
            didOpen: () => {
                $('[data-toggle="tooltip"]').tooltip();
                document.querySelector(".swal2-popup").style.background = '#2c3e50';

                if (hasCustomer) {
                    $.get(`/get_customer_details/${customer_id}`, function(response) {
                        if (response) {
                            const customerDetails = `
                                <div class="customer-info mt-2 p-2 rounded" style="background: #f4f4f4;">
                                    <h6>Kunde Informationen</h6>
                                    <p><b>Name:</b> ${response.name} ${response.lastname}</p>
                                    <p><b>Telefon:</b> ${response.phone || "N/A"}</p>
                                    <p><b>Email:</b> ${response.email || "N/A"}</p>
                                    <p><b>Adresse:</b> ${response.full_address || "N/A"}</p>
                                </div>`;
                            $('#customerDetails').html(customerDetails);
                        }
                    });
                }
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                window.location.href = detailsUrl;
            }
        });
    }


    // This modal is for more customer modals 
    function showCustomEventModal(events, date) {
        const dateLabel = new Date(date).toLocaleDateString("de-DE", {
            weekday: 'long',
            day: 'numeric',
            month: 'long'
        });

        const eventListHtml = events.map(event => {
            const title = event.title || "-";
            const startTime = event.start
                ? new Date(event.start).toLocaleTimeString("de-DE", { hour: '2-digit', minute: '2-digit' })
                : "-";
            const color = event.backgroundColor || "#ccc";
            const eventId = event.id;

            return `
                <div class="clickable-event" data-event-id="${eventId}" 
                    style="border-left: 5px solid ${color}; padding: 5px 10px; margin-bottom: 5px; cursor: pointer;">
                    <strong>${title}</strong><br>
                    <small>${startTime}</small>
                </div>
            `;
        }).join("");

        Swal.fire({
            title: `Alle Termine am ${dateLabel}`,
            html: eventListHtml,
            showCloseButton: true,
            confirmButtonText: "Schließen",
            width: '600px',
            background: '#f9f9f9',
            didOpen: () => {
                document.querySelectorAll('.clickable-event').forEach(el => {
                    el.addEventListener('click', function () {
                        const eventId = this.getAttribute('data-event-id');
                        let clickedEvent = calendar.getEventById(eventId);

                        // Fallback: match base ID if full ID not found
                        if (!clickedEvent && eventId.includes("-")) {
                            const baseId = eventId.split("-")[0];
                            clickedEvent = calendar.getEvents().find(e => {
                                return e.id && e.id.toString().split("-")[0] === baseId;
                            });
                        }

                        if (clickedEvent) {
                            Swal.close(); // Close the "more" popup first
                            setTimeout(() => {
                                showEventDetailsModal(clickedEvent); // Open detail modal safely
                            }, 100);
                        } else {
                            console.warn("❌ Event not found:", eventId);
                        }
                    });
                });
            }
        });
    } 
   function handleEventUpdate(info) {
        const eventType = info.event.extendedProps.type; 
        // 🚫 Prevent moving or resizing holiday & sick leave events 
        if (["public_holiday", "holiday", "sick"].includes(eventType)) {
            // Just revert the move
            info.revert();
            return;
        }

        Swal.fire({
            title: "Geben Sie einen Grund für die Änderung an",
            html: `<textarea id="change_reason" class="swal2-textarea"
                            placeholder="Geben Sie einen Grund für die Änderung an"></textarea>`,
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
                let taskId = info.event.id.split("-")[0];
                let empPersonalId = info.event.extendedProps.emp_personal_id;
                const startDateTime = new Date(info.event.start);
                const endDateTime = info.event.end ? new Date(info.event.end) : startDateTime;

                const startDate = startDateTime.toISOString().split("T")[0];
                const startTime = startDateTime.toTimeString().slice(0, 5);
                const endDate = endDateTime.toISOString().split("T")[0];
                const endTime = endDateTime.toTimeString().slice(0, 5);

                fetch("{{ route('personal.task.change.appointment') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        emp_personal_id: empPersonalId || null,
                        start_date: startDate,
                        end_date: endDate,
                        start_time: startTime,
                        end_time: endTime,
                        change_reason: result.value,
                        type: eventType,
                    }),
                })
                .then(r => r.json())
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


    // Loading the data from database 
    function loadCalendarTasks(callback) {
        let employeeData = getSelectedEmployeeData();  

        // Helper to format date in YYYY-MM-DDTHH:MM:SS
        function formatDateTime(date) {
            const pad = n => String(n).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}:00`;
        }

        $.ajax({
            url: "/get_personal_task_calendar",
            type: "GET",
            data: {
                employee_data: JSON.stringify(employeeData)
            },
            success: function (response) {
                console.log("✅ AJAX Response:", response);
                console.log('Backend data example:', response.data?.[0]);

                if (!response || !Array.isArray(response.data)) {
                    console.error("❌ Invalid response format:", response);
                    if (typeof callback === "function") callback([]);
                    return;
                }

                const tasks = [];

                response.data.forEach(task => {
                    const startDateTime = new Date(`${task.start_date}T${task.start_time}`);
                    const endDateTime = new Date(`${task.end_date || task.start_date}T${task.end_time}`);

                    for (let d = new Date(startDateTime); d <= endDateTime; d.setDate(d.getDate() + 1)) {
                        const currentDate = new Date(d);
                        const yyyy = currentDate.getFullYear();
                        const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
                        const dd = String(currentDate.getDate()).padStart(2, '0');
                        const dateStr = `${yyyy}-${mm}-${dd}`;

                        // Default working times
                        let startTime = "07:30:00";
                        let endTime = "16:00:00";

                        // First day uses actual start
                        if (dateStr === task.start_date) startTime = task.start_time;
                        // Last day uses actual end
                        if (dateStr === task.end_date || task.end_date === null) endTime = task.end_time;

                        const eventStart = `${dateStr}T${startTime}`;

                        // 🧠 Fix visual height: +1 minute if endTime is exact slot (like 16:00)
                        let endDateObj = new Date(`${dateStr}T${endTime}`);
                        const needsPadding = (
                            (endDateObj.getMinutes() === 0 || endDateObj.getMinutes() === 30) &&
                            endDateObj.getSeconds() === 0 &&
                            endDateObj.getMilliseconds() === 0
                        );

                        if (needsPadding) {
                            endDateObj.setMinutes(endDateObj.getMinutes() + 1);
                        }

                        const eventEnd = formatDateTime(endDateObj);

                        function getTimeInMinutes(timeStr) {
                            const [h, m] = timeStr.split(":").map(Number);
                            return h * 60 + m;
                        }

                        const startMinutes = getTimeInMinutes(startTime);
                        const endMinutes = getTimeInMinutes(endTime);
                        const duration = endMinutes - startMinutes;

                        // const isAllDayEvent = (
                        //     duration >= 480 && // at least 8 hours
                        //     (startMinutes <= 450 || endMinutes >= 960) // start before 07:30 or end after 16:00
                        // ) || (startTime === "00:00:00" && endTime === "23:59:59"); // holiday/sick

                        const isAllDayEvent = (
                            task.type === "public_holiday" ||
                            task.type === "holiday" ||
                            task.type === "sick" ||
                            task.type === "recurring_leave"  
                            );

                        tasks.push({
                            id: `${task.id}-${dateStr}-${startTime}`,
                            title: task.title || "-",
                            start: eventStart,
                            end: eventEnd,
                            color: task.taskColor || "#cccccc",
                            allDay: isAllDayEvent,
                            extendedProps: {
                                employees: task.employees || [],
                                priority: task.priority || "-",
                                public: task.public_view || "-",
                                report: task.is_report || "-",
                                type: task.type || "-",
                                start_time: startTime,
                                end_time: endTime,
                                city: task.city || "-",
                                phone: task.phone || "-",
                                email: task.email || "-",
                                full_address:
                                    task.full_address ||
                                    [task.street, task.postcode, task.city].filter(Boolean).join(" ") ||
                                    "-",
                                appointment_type: task.appointment_type || "-",
                                description: task.description || "-",
                                customer_id: task.customer_id ?? null,
                                contact_id: task.contact_id ?? null,
                                next_step: task.next_step || null,
                                responsible_report: task.responsible_report || [],
                            }
                        });
                    }
                });

                extractPublicHolidayDates(tasks);
                initializeCalendar(tasks);
                initializeMiniCalendar(tasks);

                if (typeof callback === "function") {
                    callback(tasks);
                }
            },
            error: function (xhr) {
                console.error("❌ Failed to fetch tasks:", xhr.responseText);
                if (typeof callback === "function") callback([]);
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
    document.addEventListener('click', function(e) {
        let deleteButton = e.target.closest('#delete_event'); // Detect delete button click
        if (deleteButton) {
            e.preventDefault(); // Prevent default behavior

            let eventId = deleteButton.getAttribute('data-event-id'); // Get event ID

            let eventType = deleteButton.getAttribute('data-event-type'); // Get event type (appointment or task)
            let baseUrl = window.location.origin; // Get base URL (http://127.0.0.1:8000)

              // 🚫 Prevent deleting immutable events
                if (["holiday", "sick", "public_holiday", "recurring_leave"].includes(eventType)) {
                    Swal.fire({
                        icon: "warning",
                        title: "Löschen nicht erlaubt",
                        text: "Dieser Termin kann nicht gelöscht werden.",
                        confirmButtonColor: "#d92127",
                    });
                    return;
                }

            let deleteUrl = eventType === 'appointment' ?
                `${baseUrl}/calendar/appointments/destroy/${eventId}` :
                `${baseUrl}/calendar/personal_task_delete/${eventId}`;

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
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                // ✅ **Reload calendar tasks but keep the same view and range**
                                loadCalendarTasks(() => {
                                    calendar.changeView(
                                    currentView); // Restore previous view
                                    calendar.gotoDate(
                                    currentDate); // Restore previous position
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

    //save function

    initSelect2(); // Initialize select2 for existing rows

    // Initialize Select2 for dynamically added rows
    function initSelect2() {
        $('.employee').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: function(markup) {
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


    function extractPublicHolidayDates(events = []) {
        publicHolidayDates = events
            .filter(item => item.extendedProps?.type === 'public_holiday')
            .map(item => item.start.split('T')[0]);

        console.log("✅ Extracted Public Holidays:", publicHolidayDates); // DEBUG
    }

    let reminderManuallyCleared = false;

    // Handle save operation 
    $('.save-task').on('click', function (e) {
    e.preventDefault();

    syncCheckboxWithDropdown();
    initTicketSelects(); // falls Ticket-Selects erst jetzt sichtbar wurden

    const $form = $('#task-store-form');

    // --- Grunddaten
    const employee      = $('#employee').val();
    const startDate     = $('#start_date').val();
    const endDate       = $('#end_date').val();
    const reminderDate  = $('#reminder_date').val();
    const nextStep      = $('#next_step').val();
    const responsible   = $('#report_responsible').val();

    // --- Ticket-/Kontaktmodus
    const mode             = currentContactMode(); // 'new' | 'select' | 'ticket'
    const ticketCustomerId = $('#ticket_customer_id').val();
    const ticketProblemId  = $('#ticket_problem_id').val();
    const ticketTaskId     = $('#ticket_task_id').val();
    const ticketAutoCreate = $('#ticket_create_task').is(':checked') ? 1 : 0;

    // --- Titel-Fallbacks bestimmen
    const rawName          = ($('#name').val() || '').trim();

    const selContact       = $('#customer_id').select2('data');
    const contactText      = (selContact && selContact[0]?.text ? selContact[0].text.split(' - ')[0] : '').trim();

    const selTicketProb    = $('#ticket_problem_id').select2('data');
    const ticketProblemTxt = (selTicketProb && selTicketProb[0]?.text ? selTicketProb[0].text : '').trim();

    const selTicketTask    = $('#ticket_task_id').select2('data');
    const ticketTaskTxt    = (selTicketTask && selTicketTask[0]?.text ? selTicketTask[0].text : '').trim();

    let effectiveTitle = rawName;
    if (!effectiveTitle) {
        if (mode === 'select') {
        effectiveTitle = contactText;
        } else if (mode === 'ticket') {
        // Reihenfolge: Task > Problem > Kontakt
        effectiveTitle = ticketTaskTxt || ticketProblemTxt || contactText;
        // Optionaler Rettungsring, wenn Auto-Create aktiv ist
        if (!effectiveTitle && ticketAutoCreate) {
            effectiveTitle = `Ticket ${ticketProblemId || ''}`.trim();
        }
        }
    }
    // Letzter Fallback: Terminart oder Adresse
    if (!effectiveTitle) {
        effectiveTitle = ($('#appointment_type').val() || '').trim() ||
                        ($('#full_address').val() || '').trim();
    }
    // Titel zurückschreiben, damit er ans Backend gesendet wird
    $('#name').val(effectiveTitle);

    // --- Hidden Ticket-Felder setzen (damit form.serialize() immer alles enthält)
    setHiddenTicketFields({
        problemId: ticketProblemId || null,
        problemTaskId: ticketTaskId || null,
        mode,
        autoCreate: ticketAutoCreate
    });

    // --- Validierung
    const errors = [];

    if (!effectiveTitle)                       errors.push('Der Titel darf nicht leer sein.');
    if (!employee || employee.length === 0)    errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
    if (!startDate)                            errors.push('Das Startdatum darf nicht leer sein.');
    if (!endDate)                              errors.push('Das Enddatum darf nicht leer sein.');
    if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
        errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
    }

    // Feiertage blocken
    if (startDate && endDate) {
        let cur = new Date(startDate);
        const last = new Date(endDate);
        while (cur <= last) {
        const y = cur.getFullYear();
        const m = String(cur.getMonth()+1).padStart(2,'0');
        const d = String(cur.getDate()).padStart(2,'0');
        const dateStr = `${y}-${m}-${d}`;
        if (publicHolidayDates.includes(dateStr)) {
            errors.push(`Datum ${dateStr} ist ein Feiertag.`);
        }
        cur.setDate(cur.getDate()+1);
        }
    }

    // Nachfasstermin -> Next Step + Verantwortliche Pflicht
    if (reminderDate) {
        if (!nextStep) errors.push('Bitte wählen Sie einen nächsten Schritt.');
        if (!responsible || responsible.length === 0) {
        errors.push('Bitte wählen Sie einen Verantwortlichen.');
        } else {
        const jsonResponsible = JSON.stringify([responsible]);
        if ($('#responsible_json').length === 0) {
            $('<input>', { type:'hidden', id:'responsible_json', name:'responsible_json', value: jsonResponsible }).appendTo($form);
        } else {
            $('#responsible_json').val(jsonResponsible);
        }
        }
    }

    // Ticket-Pflichten
    if (mode === 'ticket') {
        if (!ticketCustomerId) errors.push('Bitte wählen Sie einen Ticket-Kunden.');
        if (!ticketProblemId)  errors.push('Bitte wählen Sie ein Ticket/Problem.');
        if (!ticketTaskId && !ticketAutoCreate) {
        errors.push('Bitte wählen Sie einen Ticket-Task oder aktivieren Sie "Neuen Ticket-Task aus Termin-Titel erstellen".');
        }
    }

    // Fehler ausgeben & abbrechen
    if (errors.length > 0) {
        Swal.fire({
        icon: 'error',
        title: 'Fehlerhafte Eingabe',
        html: `<ul style="text-align:left;">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`
        });
        return;
    }

    // --- Submit
    const appointmentId = $('#appointment_id').val();
    const method   = appointmentId ? 'PUT' : 'POST';
    const actionUrl= appointmentId ? `/main-appointments/${appointmentId}` : `{{ route('main.appointments.store') }}`;

    $.ajax({
        url: actionUrl,
        type: method,
        data: $form.serialize(), // enthält Hidden-Felder & den finalen Titel
        beforeSend: function () {
        $('.save-task').prop('disabled', true).text('speichern...');
        },
        success: function (response) {
        $('.save-task').prop('disabled', false).text('speichern');
        $('.new_task_card').hide();
        $form.trigger('reset');
        $('#appointment_id').val('');
        $('#customer_id').val(null).trigger('change');
        $('#ticket_customer_id, #ticket_problem_id, #ticket_task_id').val(null).trigger('change');
        $('#name, #name_display, #contact_type, #phone, #email, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input, #full_address').val('');
        $('#contact_mode').val('new');
        $('#newContact').prop('checked', true).trigger('change');
        setHiddenTicketFields({}); // clean slate

        Swal.fire({
            icon: 'success',
            title: 'Erfolg',
            text: appointmentId ? 'Termin erfolgreich aktualisiert!' : 'Termin erfolgreich gespeichert!'
        });

        // Ansicht/Datum beibehalten
        const currentView = calendar.view.type;
        const currentDate = calendar.getDate();
        loadCalendarTasks(() => {
            calendar.changeView(currentView);
            calendar.gotoDate(currentDate);
        });
        },
        error: function (xhr) {
        $('.save-task').prop('disabled', false).text('speichern');
        const serverErrors = xhr.responseJSON?.errors || {};
        const errorMessages = Object.values(serverErrors).flat().map(msg => `<li>${msg}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Fehler',
            html: `<ul>${errorMessages || 'Unbekannter Fehler aufgetreten.'}</ul>`
        });
        }
    });
    });

        
    // --- Edit appointment (full rewrite with Ticket prefill) --------------------
    $(document).on('click', '.edit-event', function (e) {
    e.preventDefault();
    Swal.close();

    const eventId = $(this).data('event-id');

    $.get(`/main-appointments/${eventId}/fetch`, async function (data) {
        console.log("📦 Appointment Data Received:", data);

        // ── Show editor
        $('.new_task_card').show();
        $('.title').text('TERMIN BEARBEITEN');

        // ── Base fields
        $('#appointment_id').val(data.id);
        $('#name').val(data.name ?? '');
        $('#note').val(data.note ?? '');
        $('#color').val(data.color ?? '').trigger('change');
        $('#colorIcon').css('color', data.color ?? '#000');

        // ── Types & priorities
        $('#appointment_type').val(data.appointment_type ?? '');
        $('#execution_type').val(data.execution_type ?? '').trigger('change');
        $('#priority').val(data.priority ?? '').trigger('change');
        $('#date_type').val(data.date_type ?? '').trigger('change');
        $('#repeat').val(data.repeat ?? '').trigger('change');

        // ── Dates & times
        $('#start_date').val(data.start_date ?? '');
        $('#end_date').val(data.end_date ?? '');
        $('#start_time').val(data.start_time ?? '');
        $('#end_time').val(data.end_time ?? '');
        $('#total_time').val(data.total_time ?? '');

        // ── Reminder
        $('#reminder_date').val(data.reminder_date ?? '');
        $('#reminder_time').val(data.reminder_time ?? '');

        // ── Next step
        if (data.next_step) {
        if (!$(`#next_step option[value="${data.next_step}"]`).length) {
            $('#next_step').append(new Option(data.next_step, data.next_step, true, true));
        }
        $('#next_step').val(data.next_step).trigger('change');
        } else {
        $('#next_step').val('').trigger('change');
        }

        // ── Responsible (array-safe)
        try {
        const responsible = Array.isArray(data.responsible_report)
            ? data.responsible_report
            : JSON.parse(data.responsible_report || '[]');
        $('#report_responsible').val(responsible).trigger('change');
        } catch {
        $('#report_responsible').val([]).trigger('change');
        }

        // ── Repeat period
        $('#from_day').val(data.from_day ?? '');
        $('#to_day').val(data.to_day ?? '');
        $('#from_month').val(data.from_month ?? '');
        $('#to_month').val(data.to_month ?? '');

        // ── Location
        $('#full_address').val(data.full_address ?? '');
        $('#street-input').val(data.street ?? '');
        $('#city-input').val(data.city ?? '');
        $('#postal_code-input').val(data.postcode ?? '');
        $('#latitude-input').val(data.latitude ?? '');
        $('#longitude-input').val(data.longitude ?? '');

        // ── Contact info
        $('#phone').val(data.phone ?? '');
        $('#email').val(data.email ?? '');
        $('#link').val(data.link ?? '');
        $('#contact_type').val(data.contact_type ?? '');
        $('#description').val(data.description ?? '');

        // ── Public & contact/report switches
        $('#switchPublic').prop('checked', data.public === '1');
        $('#switchContact').prop('checked', data.is_contact === '1').trigger('change');
        $('#switchReport').prop('checked', data.is_report == '1');

        // ── Type & source
        $('#pre_type').val(data.pre_type ?? '').trigger('change');
        if (data.source) {
        if (!$(`#source option[value="${data.source}"]`).length) {
            $('#source').append(new Option(data.source, data.source, true, true));
        }
        $('#source').val(data.source).trigger('change');
        } else {
        $('#source').val('').trigger('change');
        }

        // ── Branch info
        $('#branch_id').val(data.branch_id ?? '').trigger('change');
        $('#branch_address_id').val(data.branch_address_id ?? '').trigger('change');

        // ── Employees
        $('#employee').val(data.employee_ids ?? []).trigger('change');

        // ── Change tracking
        $('#change_date').val(data.change_date ?? '');
        $('#change_reason').val(data.change_reason ?? '');

        // ── Status
        $('#status').val(data.status ?? '').trigger('change');

        // ── Audit
        $('.audit-info').html(`
        <div>Erstellt von: <strong>${data.created_by_name ?? '-'}</strong></div>
        <div>Geändert von: <strong>${data.changed_by_name ?? '-'}</strong></div>
        <div>Erstellt am: ${data.created_at ?? '-'} | Geändert am: ${data.updated_at ?? '-'}</div>
        `);

        // ── Products prefill (Select2)
        if (data.products) {
        try {
            const parsed = typeof data.products === 'string' ? JSON.parse(data.products) : data.products;
            const productIds = [];
            for (const name in parsed) {
            const [altId] = parsed[name];
            productIds.push(`${name}_${altId}`);
            }
            $('#product').val(JSON.stringify(parsed));
            loadCustomerProducts(data.customer_id, productIds);
        } catch (e) {
            console.warn("❌ Produkt JSON Fehler", e);
            loadCustomerProducts(data.customer_id);
        }
        } else {
        loadCustomerProducts(data.customer_id);
        }

        // ── Toggle derived UI (preType/source blocks)
        if (typeof togglePreTypeAndSource === 'function') {
        togglePreTypeAndSource();
        }

        // ──────────────────────────────────────────────────────────────
        // 🔗 Ticket block (prefill radio + Select2s + hidden fields)
        // Requires (in DOM):
        //   radio:    #ticketMode
        //   selects:  #ticket_customer_id, #ticket_problem_id, #ticket_task_id
        //   hidden:   #problem_id, #problem_task_id, #contact_mode
        //   routes:   /tickets/problems/by-customer/{id}, /tickets/tasks/by-problem/{id}
        // ──────────────────────────────────────────────────────────────

        // Helpers (idempotent)
        function ensureSelect2(sel) {
        if (!$(sel).length) return;
        if (!$(sel).data('select2')) $(sel).select2({ width: '100%', placeholder: 'Wählen', allowClear: true });
        }
        function setTicketHidden({ problemId=null, problemTaskId=null, mode=null } = {}) {
        if ($('#problem_id').length)       $('#problem_id').val(problemId || '');
        if ($('#problem_task_id').length)  $('#problem_task_id').val(problemTaskId || '');
        if (mode && $('#contact_mode').length) $('#contact_mode').val(mode);
        }
        function addOrSelect($sel, id, text) {
        if (!id) return;
        const exist = $sel.find(`option[value="${id}"]`).length;
        if (!exist) $sel.append(new Option(text || `#${id}`, id, true, true));
        $sel.val(id).trigger('change');
        }
        async function loadProblemsForCustomer(customerId, preselectProblemId=null) {
        if (!customerId || !$('#ticket_problem_id').length) return;
        const $p = $('#ticket_problem_id'); $p.empty().trigger('change');
        try {
            const res = await $.getJSON(`/tickets/problems/by-customer/${customerId}`);
            (res.data || []).forEach(p => {
            $p.append(new Option(p.label || `Ticket #${p.id}`, p.id));
            });
            if (preselectProblemId) $p.val(preselectProblemId).trigger('change');
        } catch (err) { console.error('loadProblemsForCustomer failed', err); }
        }
        async function loadTasksForProblem(problemId, preselectTaskId=null) {
        if (!problemId || !$('#ticket_task_id').length) return;
        const $t = $('#ticket_task_id'); $t.empty().trigger('change');
        try {
            const res = await $.getJSON(`/tickets/tasks/by-problem/${problemId}`);
            (res.data || []).forEach(t => {
            $t.append(new Option(t.label || `Task #${t.id}`, t.id));
            });
            if (preselectTaskId) $t.val(preselectTaskId).trigger('change');
        } catch (err) { console.error('loadTasksForProblem failed', err); }
        }

        // Init select2s (if present)
        ensureSelect2('#ticket_customer_id');
        ensureSelect2('#ticket_problem_id');
        ensureSelect2('#ticket_task_id');

        const preProblemId   = data.problem_id || null;
        const preTaskId      = data.problem_task_id || null;
        const preTicketCust  = data.ticket_customer_id || data.customer_id || null;
        const preCustLabel   = data.customer_label || data.customer_name || null;

        if (preProblemId || preTaskId) {
        // flip UI to Ticket mode
        if ($('#ticketMode').length) {
            $('#ticketMode').prop('checked', true).trigger('change');
        }
        setTicketHidden({ problemId: preProblemId, problemTaskId: preTaskId, mode: 'ticket' });

        // preselect customer → problems → tasks
        if ($('#ticket_customer_id').length && preTicketCust) {
            const $c = $('#ticket_customer_id');
            addOrSelect($c, preTicketCust, preCustLabel);
            await loadProblemsForCustomer(preTicketCust, preProblemId);
            if (preProblemId) await loadTasksForProblem(preProblemId, preTaskId);
        } else if (preProblemId) {
            await loadTasksForProblem(preProblemId, preTaskId);
        }
        } else {
        // clear hidden fields if not ticket-linked
        setTicketHidden({});
        }
        // ── end ticket block
    });
    });

    // Contact list data 


     // Initialize Select2
     $('.contact_list').select2({
            placeholder: "Wählen",
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: "{{ route('get.contact.list') }}",
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || ''
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.main_id,
                                text: `${item.name} ${item.lastname} - ${item.type}`,
                                type: item.type,
                                phone: item.phone || "",
                                email: item.email || "",
                                street: item.street || "",
                                postcode: item.postcode || "",
                                city: item.city || "",
                                longitude: item.longitude || "",
                                latitude: item.latitude || "",
                                full_address: item.street && item.city && item.postcode
                                    ? `${item.street}, ${item.postcode} ${item.city}`
                                    : ""
                            };
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            const selected = e.params.data;

            $('#contact_type').val(selected.type);

            if (selected.type === 'Kunde') {
                $('.product-select-block').removeClass('d-none');
                loadCustomerProducts(selected.id); // 👈 dynamically fetch objects/products
            } else {
                $('.product-select-block').addClass('d-none');
                $('#productSelect').empty().trigger('change');
                $('#product').val('');
            }
        });


    // ✅ On select, update all related input fields
    $('.contact_list').on('select2:select', function(e) {
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
        $('.contact_list').on('select2:clear', function() {
            $('#contact_type, .phone, .email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input')
                .val('');
        });

        // ✅ Load full list when Select2 opens
        $('.contact_list').on('select2:open', function() {
            $(".select2-search__field").attr("placeholder",
            "Tippen Sie, um zu suchen..."); // Set search placeholder
        });


            // Toggle between new and existing contact
        // Toggle between "Neu" and "Kontakt"
        $('input.contact-type-toggle').on('change', function () {
            const mode = $(this).val();
            $('#contact_mode').val(mode); // ✅ update hidden input
            if (mode === 'new') {
                $('.contact-name-block').removeClass('d-none');
                $('.contact-select-block').addClass('d-none'); 
                $('#customer_id').val(null).trigger('change');
            } else {
                $('.contact-name-block').addClass('d-none');
                $('.contact-select-block').removeClass('d-none');
            }
        });

        // === Ticket mode toggling ===
            $('input.contact-type-toggle').on('change', function () {
            const mode = $(this).val(); // 'new' | 'select' | 'ticket'
            $('#contact_mode').val(mode);

            if (mode === 'new') {
                $('.contact-name-block').removeClass('d-none');
                $('.contact-select-block').addClass('d-none');
                $('.ticket-block').addClass('d-none');
                setHiddenTicketFields({});
            } else if (mode === 'select') {
                $('.contact-name-block').addClass('d-none');
                $('.contact-select-block').removeClass('d-none');
                $('.ticket-block').addClass('d-none');
                setHiddenTicketFields({});
            } else if (mode === 'ticket') {
                $('.contact-name-block').addClass('d-none');
                $('.contact-select-block').addClass('d-none');
                $('.product-select-block').removeClass('d-none'); // optional, if you tie products to tickets
                $('.ticket-block').removeClass('d-none');
                initTicketSelects();
            }
            });


            // === Select2s for ticket mode ===

            // 1) Customer (new_leads)
            $('#ticket_customer_id').select2({
            placeholder: "Kunde wählen",
            allowClear: true,
            ajax: {
                url: "{{ route('ticket.customer.search') }}", // returns id, name, lastname
                dataType: 'json',
                delay: 200,
                data: params => ({ q: params.term || '' }),
                processResults: data => ({
                results: (data.results || []).map(x => ({ id: x.id, text: `${x.name} ${x.lastname}` }))
                })
            }
            }).on('select2:select', function(e){
            const customerId = e.params.data.id;

            // populate Problems for this customer
            $('#ticket_problem_id').val(null).trigger('change');
            $('#ticket_task_id').val(null).trigger('change');

            // 2) Problems by customer
            $('#ticket_problem_id').select2({
                placeholder: "Problem wählen",
                allowClear: true,
                ajax: {
                url: "{{ route('ticket.problems.by.customer') }}",
                dataType: 'json',
                delay: 200,
                data: params => ({ q: params.term || '', customer_id: customerId }),
                processResults: data => ({
                    results: (data.results || []).map(p => ({ id: p.id, text: `#${p.ticket_no} — ${p.status || ''}`.trim() }))
                })
                }
            });
            });

            // 3) Tasks by problem
            $('#ticket_problem_id').on('change', function(){
            const problemId = $(this).val() || null;

            // Reset
            $('#ticket_task_id').val(null).trigger('change');

            if (!problemId) return;

            $('#ticket_task_id').select2({
                placeholder: "Ticket Task wählen",
                allowClear: true,
                ajax: {
                url: "{{ route('ticket.tasks.by.problem') }}",
                dataType: 'json',
                delay: 200,
                data: params => ({ q: params.term || '', problem_id: problemId }),
                processResults: data => {
                    const rows = (data.results || []).map(t => ({ id: t.id, text: t.title || `Task #${t.id}` }));
                    // Add a special “new from appointment title” sentinel
                    rows.unshift({ id: 'NEW_FROM_APPOINTMENT', text: '➕ Neu aus Termin-Titel erstellen' });
                    return { results: rows };
                }
                }
            });
            });

            // 4) Before submit -> copy UI values to hidden fields
            function collectTicketHiddenFields() {
            const mode = $('#contact_mode').val();
            if (mode !== 'ticket') {
                $('#problem_id').val('');
                $('#problem_task_id').val('');
                return;
            }
            $('#problem_id').val($('#ticket_problem_id').val() || '');
            $('#problem_task_id').val($('#ticket_task_id').val() || '');
            }

            // Hook into your existing save button flow (right before serialize)
            $('.save-task').on('click', function(e){
            collectTicketHiddenFields(); // << ensure hidden fields are filled
            });




        $('.contact_list').on('select2:select', function (e) {
        const selectedData = e.params.data;

        if (selectedData && selectedData.text) {
            const cleanName = selectedData.text.split(' - ')[0];

        

            // Hidden field for form submission
            $('#name').val(cleanName);
        }

        // All your other fills
        $('#contact_type').val(selectedData.type);
        $('.phone').val(selectedData.phone);
        $('.email').val(selectedData.email);
        $('#full_address').val(selectedData.full_address);
        $('#street-input').val(selectedData.street);
        $('#city-input').val(selectedData.city);
        $('#postal_code-input').val(selectedData.postcode);
        $('#latitude-input').val(selectedData.latitude);
        $('#longitude-input').val(selectedData.longitude);
    });


    let productMap = {};

    function loadCustomerProducts(customerId) {
        $.ajax({
            url: "{{ route('get.products.by.customer') }}",
            method: 'GET',
            data: { customer_id: customerId },
            success: function (groupedData) {
                const $select = $('#productSelect');
                $select.empty().off('change'); // Clear all before reinit

                productMap = {};

                // ✅ Build productMap and options
                groupedData.forEach(group => {
                    const $optgroup = $('<optgroup>').attr('label', group.text);

                    group.children.forEach(prod => {
                        const uid = `${prod.product_name}_${prod.alternative_id}`;

                        productMap[uid] = prod;

                        const $option = $('<option>')
                            .val(uid)
                            .text(`${prod.product_name} (${prod.city || ''})`);

                        $optgroup.append($option);
                    });

                    $select.append($optgroup);
                });

                // ✅ Initialize Select2
                $select.select2({
                    placeholder: "Produkte wählen",
                    multiple: true,
                    allowClear: true,
                    templateSelection: function (data) {
                        if (!data.id) return '';
                        return data.text || '';
                    }
                });

                // ✅ On change → generate JSON for #product
                $select.on('change', function () {
                    const selectedIds = $(this).val() || [];
                    const result = {};

                    selectedIds.forEach(id => {
                        const info = productMap[id];
                        if (info) {
                            result[info.product_name] = [
                                info.alternative_id,
                                info.product_id,
                                info.customer_id
                            ];
                        }
                    });

                    $('#product').val(JSON.stringify(result));

                    // Optional debug
                    console.log("✅ Final product JSON:", $('#product').val());
                });

                // ✅ Preselect from existing JSON
                const saved = $('#product').val();
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        const ids = [];

                        for (const name in parsed) {
                            const [altId] = parsed[name];
                            ids.push(`${name}_${altId}`);
                        }

                        // SET selected values AFTER Select2 is ready
                        $select.val(ids).trigger('change');
                    } catch (err) {
                        console.warn('⚠️ JSON Parse Error:', err);
                    }
                }
            }
        });
    }

    // end of contact list data 


    $('.close_task_window').on('click', function() {
        $('#task-store-form').trigger('reset');
        $('#appointment_id').val('');
        $('.new_task_card').hide();
        $('.title').text('TERMIN ERSTELLEN');
    });


    // Initial load for default user tasks only


    // Mini Calendar 
    // Function to initialize the mini calendar
    function initializeMiniCalendar(events = []) {
          if (!calendar) return;  

        if (miniCalendar) {
            miniCalendar.destroy();
        }

        let lastClickTime = 0; // Track last click time for detecting double click

        miniCalendar = new FullCalendar.Calendar(miniCalendarEl, {
            initialView: "dayGridMonth",
            locale: "de",
            selectable: true,
            headerToolbar: {
                left: "title",
                center: "",
                right: "prev,next"
            },
            events: events, // ✅ Use existing events dataset
            dateClick: function(info) {
                let currentTime = new Date().getTime();
                let timeSinceLastClick = currentTime - lastClickTime;

                if (timeSinceLastClick < 300) {
                    // ✅ Double click detected (switch to day view)
                    console.log("🖱 Double click detected, switching to day view for:", info
                        .dateStr);
                    calendar.changeView("timeGridDay");
                    filterMainCalendarByDate(info.dateStr);
                } else {
                    // ✅ Single click detected (show all week events)
                    console.log("🖱 Single click detected, showing all week events for:", info
                        .dateStr);
                    calendar.changeView("timeGridWeek");

                    // ✅ Ensure filtering by both week range and selected employees
                    let weekStartDate = getWeekStartDate(info.dateStr);
                    filterMainCalendarByDate(weekStartDate);
                }

                lastClickTime = currentTime;
            }
        });

        miniCalendar.render();
        calendar.render();
            setTimeout(() => {
                $(".fc-toggleSlider-button").html("<i class='feather icon-sidebar'></i>");
               
            }, 0);
    }

    function getWeekStartDate(dateStr) {
        let date = new Date(dateStr);
        let dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, etc.

        // Ensure week starts on Monday
        let startDate = new Date(date);
        startDate.setDate(date.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1));

        return startDate.toISOString().split("T")[0]; // Format as YYYY-MM-DD
    }


    function getCurrentCalendarDate() {
        let currentView = calendar.view.type; // Get current view mode (day, week, month)
        let currentDate = calendar.getDate(); // Get the current date being viewed

        console.log("📅 Current Calendar View:", currentView);
        console.log("📆 Current Calendar Date:", currentDate.toISOString().split("T")[0]); // Format YYYY-MM-DD
    }

    function getWeekStartDate(dateStr) {
        let date = new Date(dateStr);
        let firstDayOfWeek = calendar.getOption("firstDay"); // Get week start (0 = Sunday, 1 = Monday)

        let startDate = new Date(date);
        let dayOfWeek = date.getDay();

        // Adjust to first day of the week
        startDate.setDate(date.getDate() - ((dayOfWeek + 7 - firstDayOfWeek) % 7));

        return startDate.toISOString().split("T")[0]; // Return as YYYY-MM-DD
    }

    function makeTicketUrl(problemId, taskId) {
        if (!problemId) return null;
        // Adjust paths if your app uses different routes.
        return taskId
            ? `/problems/${problemId}/tasks/${taskId}`
            : `/problems/${problemId}`;
        }

    const TICKET_SVG = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            width="14" height="14" aria-hidden="true" focusable="false">
        <path fill="currentColor"
                d="M3 7a2 2 0 0 1 2-2h5l1 2h3a2 2 0 1 0 0 4h-3l-1 2H5a2 2 0 0 1-2-2V7zM14 5h5a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2h-5l-2-4 2-4z"/>
        </svg>`;


    // Function to filter main calendar by selected date
    function filterMainCalendarByDate(date) {
        if (!date) return;

        let employeeData = getSelectedEmployeeData();

        console.log("🗓 Filtering Events for:", date);
        console.log("👤 Selected Employees:", employeeData);

        $.ajax({
            url: "/get_personal_task_calendar",
            type: "GET",
            data: {
                employee_data: JSON.stringify(employeeData),
                search: currentSearch || '',
                filter_date: date,
            },
            success: function(response) {
                console.log("✅ AJAX Response:", response);

                if (!response || !response.data || !Array.isArray(response.data)) {
                    console.error("❌ Invalid response format:", response);
                    initializeCalendar([]); // Reset calendar
                    return;
                }

                const filteredEvents = response.data.map(task => ({
                    id: task.id + "-" + task.start_date + "-" + task.start_time,
                    title: task.title || "-",
                    start: task.start_time ? `${task.start_date}T${task.start_time}` : task.start_date || "-",
                    end: task.end_date ? (task.end_time ? `${task.end_date}T${task.end_time}` : task.end_date) : task.start_date || "-",
                    color: task.taskColor || "#cccccc",
                    extendedProps: {
                        employees: task.employees || [],
                        priority: task.priority || "-",
                        public: task.public_view || "-",
                        type: task.type || "-",
                        start_time: task.start_time || "-",
                        end_time: task.end_time || "-",
                        city: task.city || "-",
                        phone: task.phone || "-",
                        email: task.email || "-",
                        full_address: task.full_address || ([task.street, task.postcode, task.city].filter(Boolean).join(' ')) || "-",
                        customer_id: task.customer_id ?? null,
                        contact_id: task.contact_id ?? null,
                        contact_type: task.contact_type ?? null,
                        appointment_type: task.appointment_type ?? null,
                        description: task.description ?? "-",
                        street: task.street ?? "-",
                        postcode: task.postcode ?? "-",
                        has_ticket: !!task.has_ticket,
                        ticket_problem_id: task.ticket_problem_id || null,
                        ticket_task_id: task.ticket_task_id || null
                        
                    },
                }));

                extractPublicHolidayDates(filteredEvents); // 🏖 highlight holidays
                initializeCalendar(filteredEvents);        // ✅ load new view
                calendar.gotoDate(date);                   // 📅 jump to date
            },
            error: function(xhr) {
                console.error("❌ Failed to fetch tasks:", xhr.responseText);
            },
        });
    }


    // Initialize both calendars on page load
        // First boot: favorites → UI → single fetch
        (function firstBoot(){
        // Make sure favorites are in memory as strings
        window.favoriteEmployeeIds = (window.favoriteEmployeeIds || []).map(String);
        window.selectedEmployeeIds = new Set((window.favoriteEmployeeIds || []).map(String));

        // Mirror to UI (avatars + Select2) without triggering double loads
        if (typeof autoSelectFavoriteEmployees === 'function') {
        autoSelectFavoriteEmployees();
        } else {
        loadCalendarTasks();
        }

        })();



    let empFetchController;

    function fetchData(searchQuery = '', filterType = 'employee') {
        const employeeList = document.getElementById('search_emp_result');
        employeeList.innerHTML = '';

        if (empFetchController) empFetchController.abort();
        empFetchController = new AbortController();

        const apiUrl = `/getEmployees?search=${encodeURIComponent(searchQuery)}&filter=${encodeURIComponent(filterType)}`;
        fetch(apiUrl, { signal: empFetchController.signal })
            .then(r => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
            })
            .then(result => {
            const data = result.data || [];
            if (!data.length) {
                employeeList.innerHTML = '<p>No results found.</p>';
                return;
            }

            const seen = new Set();
            const favoriteIds = (window.favoriteEmployeeIds || []).map(String);
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                const select = document.createElement('select');
                select.id = 'mobileEmployeeSelect';
                select.className = 'form-control employee';
                select.setAttribute('multiple', 'multiple');

                data.forEach(emp => {
                if (seen.has(String(emp.id))) return;
                seen.add(String(emp.id));

                const option = document.createElement('option');
                option.value = emp.id;
                option.text = `${emp.name} ${emp.lastname}`;
                option.setAttribute('data-image', `/images/employee/${emp.image}`);
                select.appendChild(option);
                });

                employeeList.appendChild(select);
                // (re)init select2 exactly once
                $('#mobileEmployeeSelect').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                placeholder: "Mitarbeiter auswählen",
                width: '100%',
                escapeMarkup: m => m,
                dropdownParent: $('#search_emp_result')
                }).off('change').on('change', function () {
                const selected = ($(this).val() || []).map(String);
                    window.selectedEmployeeIds = new Set(selected);
                    loadCalendarTasks();

                });

            const preselect = Array.from(window.selectedEmployeeIds);
            if (preselect.length) {
            $('#mobileEmployeeSelect').val(preselect).trigger('change.select2');
            } else if (favoriteIds.length) {
            $('#mobileEmployeeSelect').val(favoriteIds).trigger('change');
            favoriteIds.forEach(id => window.selectedEmployeeIds.add(String(id)));
            }


            } else {
                data.forEach(emp => {
                if (seen.has(String(emp.id))) return;
                seen.add(String(emp.id));

                const isFavorite = favoriteIds.includes(String(emp.id)); 

                const listItem = document.createElement('div');
                listItem.classList.add('list-item');
               // innerhalb von fetchData(...) im Desktop-Zweig (else), direkt vor listItem.innerHTML:
                const isChecked = selectedEmployeeIds.has(String(emp.id));
                const borderColor = emp.color || 'red';

                listItem.innerHTML = `
                <div class="d-flex align-items-center m-0">
                    <input type="checkbox" class="employee_check" data-id="${emp.id}"
                        id="check${emp.id}" style="display:none" ${isChecked ? 'checked' : ''}>
                    <div class="avatar mr-1">
                    <img
                        src="/images/employee/${emp.image}"
                        alt="avatar"
                        width="48" height="48"
                        data-id="${emp.id}"
                        class="employee_checkbox ${isChecked ? 'emp_active' : ''}"
                        id="employeeCheck${emp.id}"
                        style="border-color:${isChecked ? borderColor : 'transparent'}; border-radius:50%; padding:2px;">
                    </div>
                    <span>
                    <span style="font-size:11px; font-weight:bold; text-transform:uppercase;">
                        ${emp.name}
                    </span>
                    <small>
                        <fieldset>
                        <div class="custom-control custom-checkbox" style="display:none" id="appointmentWrapper${emp.id}">
                            <input type="checkbox" class="custom-control-input employeeAppointment"
                                name="employeeAppointment" id="employeeAppointment${emp.id}"
                                data-employee-id="${emp.id}" data-filter-search="appointmentEmployee">
                            <label class="custom-control-label" style="font-size:10px;" for="employeeAppointment${emp.id}">Aufgabe</label>
                        </div>
                        </fieldset>
                    </small>
                    </span>
                </div>
                `;


                const image = listItem.querySelector(`#employeeCheck${emp.id}`);
                const checkbox = listItem.querySelector(`#check${emp.id}`);
                applySelectionToRowElements(emp.id, image, checkbox, borderColor);

                image.addEventListener('click', function () {
                    const id = String(emp.id);
                    const nowSelected = !checkbox.checked;
                    checkbox.checked = nowSelected;
                    this.classList.toggle('emp_active', nowSelected);
                    this.style.borderColor = nowSelected ? borderColor : 'transparent';
                    if (nowSelected) window.selectedEmployeeIds.add(id); else window.selectedEmployeeIds.delete(id);
                    syncCheckboxWithDropdown();
                    loadCalendarTasks();
                });

                checkbox.addEventListener('change', function () {
                    const id = String(emp.id);
                    if (this.checked) window.selectedEmployeeIds.add(id); else window.selectedEmployeeIds.delete(id); 
                    toggleAppointmentCheckbox(emp.id);
                    syncCheckboxWithDropdown();
                    loadCalendarTasks();
                });

                employeeList.appendChild(listItem);
                });

                // sync favorites visually
                        // 👇 Auswahl NICHT überschreiben – UI an selectedEmployeeIds spiegeln
                document.querySelectorAll('.employee_check').forEach(cb => {
                const id = String(cb.dataset.id);
                const checked = selectedEmployeeIds.has(id);
                cb.checked = checked;
                const img = document.querySelector(`#employeeCheck${id}`);
                if (img) {
                    img.classList.toggle('emp_active', checked);
                    img.style.borderColor = checked ? (img.style.borderColor || 'red') : 'transparent';
                }
                });
                syncCheckboxWithDropdown(); // Dropdown ↔️ Checkbox in Einklang halten

            }
            })
            .catch(err => {
            if (err.name === 'AbortError') return; // expected on new keystroke
            console.error('Error fetching data:', err);
            document.getElementById('search_emp_result').innerHTML = '<p>Failed to fetch data. Please try again later.</p>';
            });
        }


        // ✅ Function to toggle the search input fields based on filter type
        function toggleSearchInput(filterType) {
            const employeeSearchInput = document.querySelector('.employee_search_input');

        }

        // ✅ Function to toggle the appointment checkbox visibility
        function toggleAppointmentCheckbox(employeeId) {
            const appointmentWrapper = document.getElementById(`appointmentWrapper${employeeId}`);
            const employeeCheckbox = document.querySelector(`#check${employeeId}`);

            if (appointmentWrapper) {
                // Show the appointment checkbox only if the employee is selected
                appointmentWrapper.style.display = employeeCheckbox.checked ? 'block' : 'none';
            }
        }

        // ✅ Add event listeners for the radio buttons
            document.querySelectorAll('input[name="filter"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                // ✅ preserve before refetch
                selectedEmployeeIds = collectSelectedFromDOM();
                const filterType = this.value;
                toggleSearchInput(filterType);
                fetchData('', filterType);
            });

            if (!didAutoselectFavorites && selectedEmployeeIds.size === 0 && (window.favoriteEmployeeIds||[]).length) {
            window.favoriteEmployeeIds.map(String).forEach(id => selectedEmployeeIds.add(id));
            didAutoselectFavorites = true;
            autoSelectFavoriteEmployees(); 
            syncCheckboxWithDropdown();
            }

    });


    // ✅ Add event listener for the search input (debounced + bind once)
        const searchEl = document.querySelector('.employee_search_input input');
        if (searchEl && !searchEl.dataset.bound) {
        searchEl.dataset.bound = '1';
        let t;
        searchEl.addEventListener('input', function () {
            selectedEmployeeIds = collectSelectedFromDOM();
            const q = this.value.trim();
            clearTimeout(t);
            t = setTimeout(() => fetchData(q, 'employee'), 200);
        });
        }


    // ✅ Initial setup: Show employee filter by default
    toggleSearchInput('employee'); // Default to employee filter
    fetchData('', 'employee'); // Fetch initial employee data




    // Event delegation to handle dynamically loaded checkboxes
    document.addEventListener("change", function(event) {
        if (event.target.classList.contains("employee_check") || event.target.classList.contains(
                "employeeAppointment")) {
            loadCalendarTasks();
        }
    });


    $(document).on("click", ".duplicate-event", function(e) {
        e.preventDefault();
        const eventId = $(this).data("event-id");

        Swal.fire({
            title: "Duplizieren auf neues Datum",
            input: "date",
            inputLabel: "Wähle ein Datum",
            inputAttributes: {
                min: new Date().toISOString().split("T")[0]
            },
            showCancelButton: true,
            confirmButtonText: "Duplizieren",
            cancelButtonText: "Abbrechen",
            inputValidator: (value) => {
                if (!value) {
                    return "Datum ist erforderlich!";
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedDate = result.value;

                $.ajax({
                    url: "{{ route('appointment.duplicate') }}",
                    method: "POST",
                    data: {
                        appointment_id: eventId,
                        new_date: selectedDate
                    },
                    success: function(response) {
                        Swal.fire("Erfolgreich!", response.message, "success");

                        // ✅ Reload all calendar tasks and jump to new date
                        loadCalendarTasks(() => {
                            calendar.gotoDate(response.data.start_date);
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = Object.values(errors).map(errArr =>
                                errArr.join(', ')).join('<br>');
                            Swal.fire("Validierungsfehler", errorMessages, "error");
                        } else {
                            Swal.fire("Fehler!", "Unbekannter Serverfehler",
                                "error");
                        }
                    }
                });
            }
        });
    });


    // EMployee Selecting from checkbox to form 

    var authUserName = "{{ auth()->user()->name }}"; // Make sure it's the ID, not the name

    // ✅ Initialize Select2
    $('#employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    // Clear all selected employees everywhere
        $(document).on('click', '#btnClearEmployees', function (e) {
        e.preventDefault();

        // 1) Desktop Select2
        $('#employee').val(null).trigger('change');

        // 2) Mobile Select2 (if present)
        if ($('#mobileEmployeeSelect').length) {
            $('#mobileEmployeeSelect').val(null).trigger('change');
        }

        // 3) Desktop checkbox list + avatar rings
        $('.employee_check').prop('checked', false);
        $('.employee_checkbox').removeClass('emp_active').css('borderColor', 'transparent');

        // 4) Hide per-employee appointment toggles
        $('[id^="appointmentWrapper"]').hide();

        // 5) Keep your dropdown/checkbox sync honest
        if (typeof syncCheckboxWithDropdown === 'function') {
            syncCheckboxWithDropdown();
        }

        // 6) Reload calendar (if available)
        if (typeof loadCalendarTasks === 'function') {
            loadCalendarTasks(() => {
            // Optional: your implementation already preserves view/date
            });
        }

        // 7) Optional: clear the calendar search select (if you mounted it)
        if ($('#calendarSearch').length) {
            $('#calendarSearch').val(null).trigger('change');
        }
        });


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

    // ✅ Show new_task and select only current user
    // 📌 Show task form and select current user by default
        document.addEventListener("click", function(event) {
            if (event.target.classList.contains("show_new_task")) {
                selectCurrentUserOnly();
                document.querySelector(".new_task").style.display = "block";
            }
        });

        // ✅ Select only the current user in both dropdown and checkboxes
        function selectCurrentUserOnly() {
            const $employeeDropdown = $('#employee');
            $employeeDropdown.val(null).trigger('change');

            const currentOption = $employeeDropdown.find(`option[value="${authUserName}"]`);
            if (currentOption.length > 0) {
                $employeeDropdown.val([authUserName]).trigger('change');
            }

            // Update checkboxes
            document.querySelectorAll('.employee_check').forEach((checkbox) => {
                checkbox.checked = (checkbox.dataset.id === authUserName);
            });
        }

        // 🔄 Checkbox ➜ Dropdown sync
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('employee_check')) {
                syncCheckboxWithDropdown();
            }
        });

        function syncCheckboxWithDropdown() {
            let selectedIds = [];

            document.querySelectorAll('.employee_check:checked').forEach((checkbox) => {
                const employeeId = checkbox.dataset.id;
                selectedIds.push(employeeId);

                const $option = $(`#employee option[value="${employeeId}"]`);
                if ($option.length === 0) {
                    // Add dynamically if missing
                    const employeeName = checkbox.closest('.list-item')?.querySelector('span')?.innerText?.trim() || 'Unbekannt';
                    const imageUrl = checkbox.closest('.list-item')?.querySelector('img')?.getAttribute('src') || '/images/default-avatar.png';

                    $('#employee').append(new Option(employeeName, employeeId, true, true))
                        .find(`option[value="${employeeId}"]`)
                        .attr('data-image', imageUrl);
                }
            });

            window.selectedEmployeeIds = new Set(selectedIds);
            $('#employee').val(selectedIds).trigger('change');

        }

        // 🔄 Dropdown ➜ Checkbox sync
            $('#employee').on('change', function() {
                const selectedIds = ($(this).val() || []).map(String);
                window.selectedEmployeeIds = new Set(selectedIds);

                // mirror to checkboxes if present
                document.querySelectorAll('.employee_check').forEach((checkbox) => {
                    const id = String(checkbox.dataset.id);
                    checkbox.checked = selectedEmployeeIds.has(id);
                    const img = document.querySelector(`#employeeCheck${id}`);
                    if (img) {
                    img.classList.toggle('emp_active', checkbox.checked);
                    img.style.borderColor = checkbox.checked ? (img.style.borderColor || 'red') : 'transparent';
                    }
                });
            });


     

        function loadSettingsIntoModal(settings) {
            if (settings.favorite_employees) {
                $('#favoriteEmployees').val(settings.favorite_employees.map(String)).trigger('change');
            }

            if (settings.hidden_views) {
                $('input[name="hidden_views[]"]').each(function () {
                    $(this).prop('checked', settings.hidden_views.includes($(this).val()));
                });
            }

            if (settings.calendar_color) {
                $('#calendarColorPicker').val(settings.calendar_color);
            }
        }
    
        function loadUserCalendarSettings() {
        fetch('/calendar-settings')
            .then(res => res.json())
            .then(({ calendar_settings }) => {
            const favs = (calendar_settings.favorite_employee_ids || calendar_settings.favorite_employees || []).map(String);
            window.favoriteEmployeeIds = favs;

            // merge into selection not to wipe user’s current picks
            selectedEmployeeIds = new Set([...selectedEmployeeIds, ...favs]);

            loadSettingsIntoModal(calendar_settings);
            applySettingsToCalendar(calendar_settings);

            // Re-render employee list so favorites show as selected
            fetchData('', 'employee');  // this will call autoSelectFavoriteEmployees afterwards
            });
        }

        function autoSelectFavoriteEmployees() {
            if (didAutoselectFavorites) return; // run only once
            const favoriteIds = (window.favoriteEmployeeIds || []).map(String);

            const persisted = collectSelectedFromDOM();
            favoriteIds.forEach(id => persisted.add(String(id)));
            window.selectedEmployeeIds = new Set([...persisted]);

            // UI mirrors
            document.querySelectorAll('.employee_check').forEach(cb => {
                const id = cb.dataset.id;
                const avatar = document.querySelector(`#employeeCheck${id}`);
                const on = favoriteIds.includes(id);
                cb.checked = on;
                if (avatar) {
                avatar.classList.toggle('emp_active', on);
                avatar.style.borderColor = on ? (avatar.dataset.color || 'red') : 'transparent';
                }
            });

            $('#mobileEmployeeSelect').val(favoriteIds).trigger('change');
            $('#employee').val(favoriteIds).trigger('change');

            didAutoselectFavorites = true;
            loadCalendarTasks();
            }



        function applyCalendarSettings(settings) {

              const favs = (settings.favorite_employee_ids || settings.favorite_employees || []).map(String);
                window.favoriteEmployeeIds = favs;
            // ✅ Check if favorite_employee_ids is present
            if (!settings.favorite_employee_ids) {
                console.warn("⚠️ favorite_employee_ids not found in settings");
                return;
            }

            // ✅ Save to global for fetchData use
            window.favoriteEmployeeIds = settings.favorite_employee_ids.map(String);

            // ✅ Refresh UI
            fetchData(); 

            // ✅ Sync selection after short delay
            setTimeout(() => {
                const currentView = calendar.view.type;
                const currentDate = calendar.getDate();

                autoSelectFavoriteEmployees();

                loadCalendarTasks(() => {
                    calendar.changeView(currentView);
                    calendar.gotoDate(currentDate);
                });
            }, 300);


        }



                $('#calendarSettingsForm').on('submit', async function (e) {
                    e.preventDefault();

                    const $form = $(this);
                    const $submit = $form.find('button[type="submit"]');

                    const settings = {
                        favorite_employees: $('#favoriteEmployees').val() || [],
                        hidden_views: $('input[name="hidden_views[]"]:checked').map((_, el) => el.value).get(),
                        calendar_color: $('#calendarColorPicker').val()
                    };

                    // Optional: visual busy state
                    $submit.prop('disabled', true).text('Speichern…');

                    try {
                        const res = await fetch('/calendar-settings/save', {
                        method: 'POST',
                        credentials: 'same-origin', // ensure cookies (session) go along
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json', // tell Laravel we want JSON
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        body: JSON.stringify({ calendar_settings: settings })
                        });

                        // Try to parse JSON, but fall back to text on HTML responses
                        let payload;
                        const text = await res.text();
                        try { payload = JSON.parse(text); } catch { payload = { raw: text }; }

                        if (!res.ok) {
                        // Common Laravel cases: 419 (CSRF), 422 (validation), 302/HTML
                        const msg =
                            payload?.message ||
                            payload?.error ||
                            (payload?.raw && payload.raw.slice(0, 200)) ||
                            `HTTP ${res.status}`;
                        Swal.fire({ icon: 'error', title: 'Fehler', text: msg });
                        return;
                        }

                        if (payload.status === 'success') {
                        // Live apply if the function exists (don’t crash if it doesn’t)
                        if (typeof applySettingsToCalendar === 'function') {
                            try { applySettingsToCalendar(settings); } catch (e) { console.warn(e); }
                        }

                        // Wait for modal to be fully hidden, then toast, then reload
                        $('#calendarSettingsModal')
                            .one('hidden.bs.modal', function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Gespeichert!',
                                text: 'Einstellungen wurden gespeichert.',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => window.location.reload());
                            })
                            .modal('hide');
                        } else {
                        const msg =
                            payload?.message ||
                            (payload?.errors && Object.values(payload.errors).flat().join('\n')) ||
                            'Einstellungen konnten nicht gespeichert werden.';
                        Swal.fire({ icon: 'error', title: 'Fehler', text: msg });
                        }
                    } catch (err) {
                        console.error('Save failed:', err);
                        Swal.fire({
                        icon: 'error',
                        title: 'Netzwerkfehler',
                        text: 'Bitte erneut versuchen.'
                        });
                    } finally {
                        $submit.prop('disabled', false).text('Speichern');
                    }
                    });



        function applySettingsToCalendar(settings) {
            if (settings.favorite_employees?.length) {
                $('#mobileEmployeeSelect').val(settings.favorite_employees).trigger('change');
            }

            if (settings.hidden_views) {
                settings.hidden_views.forEach(view => {
                    const btn = document.querySelector(`.fc-${view}-button`);
                    if (btn) btn.style.display = 'none';
                });
            }

            if (settings.calendar_color === 'black') {
                document.querySelector('.fc').style.backgroundColor = '#111';
                document.querySelector('.fc').style.color = '#fff';
            } else if (settings.calendar_color === 'red') {
                document.querySelector('.fc').style.backgroundColor = '#ffefef';
            }
        }

        $('#calendarSettingsModal').on('shown.bs.modal', function () {
            loadUserCalendarSettings();
        });


        $('#favoriteEmployees').select2({
            dropdownParent: $('#calendarSettingsModal'),
            width: '100%'
        });


          // 🔹 First define this function
             function getSelectedEmployeeData() {
                const ids = Array.from((window.selectedEmployeeIds || new Set())).map(String);
                const isMobile = window.innerWidth <= 768;

                if (ids.length) {
                    return ids.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
                }

                // Fallback: current UI state (mobile)
                if (isMobile) {
                    const selected = ($('#mobileEmployeeSelect').val() || []).map(String);
                    if (selected.length) {
                    return selected.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
                    }
                } else {
                    // Fallback: current UI state (desktop)
                    const fromChecks = Array.from(document.querySelectorAll('.employee_check:checked')).map(cb => String(cb.dataset.id));
                    if (fromChecks.length) {
                    return fromChecks.map(id => ({ employee_id: id, tasks_only: 0, appointments_only: 1 }));
                    }
                }

                // Last resort: your NAME (as you want)
                return [{
                    employee_id: String(window.authUserName),
                    tasks_only: 0,
                    appointments_only: 1
                }];
                }



    // 🔹 Now call this after it's defined
    loadCalendarTasks();



});
</script> 
<script>
var authUserName = "{{ auth()->user()->name }}";
</script> 
<!-- Serach and Filter by Employee and Task Title, Date :end_date -->
<!-- moving from menu to kalender tab  -->
<script>
$(document).ready(function() {
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
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        let target = $(e.target).attr("href");
        history.replaceState(null, null, target);
    });
});
</script>

<!-- Information Popup  -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("info_popup")) {
            let infoId = event.target.getAttribute("data-id");
            let infoType = event.target.getAttribute("data-type");

            fetch(`/get/info/${infoId}/${infoType}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
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
document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", function(event) {
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
                    headers: {
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let destination = {
                            lat: parseFloat(data.latitude),
                            lng: parseFloat(data.longitude)
                        };

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                let origin = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };

                                // Once the location is retrieved, show the map
                                showMapWithRoute(origin, destination, data.title);
                            }, function() {
                                Swal.fire("Error", "Could not get your location.", "error");
                            });
                        } else {
                            Swal.fire("Error", "Geolocation is not supported by your browser.",
                                "error");
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

            directionsService.route({
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING,
                },
                function(response, status) {
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
<!-- script for hidding the day and month drop down:  -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    const startDateInput = document.getElementById("start_date");
    const weekSelect = document.getElementById("week_select");
    const weekDropdownContainer = document.getElementById("week_dropdown_container");
    const dateType = document.getElementById("date_type");

    function getWeekNumber(date) {
        const tempDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
        const dayNum = tempDate.getUTCDay() || 7;
        tempDate.setUTCDate(tempDate.getUTCDate() + 4 - dayNum);
        const yearStart = new Date(Date.UTC(tempDate.getUTCFullYear(), 0, 1));
        return Math.ceil((((tempDate - yearStart) / 86400000) + 1) / 7);
    }

    function updateWeekDropdown() {
        const startDate = new Date(startDateInput.value);
        if (isNaN(startDate)) return;

        const currentWeek = getWeekNumber(startDate);
        const totalWeeks = 52;

        // Clear old options
        weekSelect.innerHTML = "";

        for (let i = currentWeek; i <= totalWeeks; i++) {
            const option = document.createElement("option");
            option.value = i;
            option.textContent = `Woche ${i}`;
            weekSelect.appendChild(option);
        }

        // Reinitialize Select2 for weekSelect (in case it was used)
        $('#week_select').select2({
            placeholder: "Wähle Woche(n)",
            allowClear: true
        });

        weekDropdownContainer.style.display = "block";
    }

    function toggleFields() {
        const selectedValue = $("#date_type").val();

        $(".from_day, .to_day, .from_month, .to_month").hide();
        $("#week_dropdown_container").hide();

        if (selectedValue === "daily") {
            $(".from_day, .to_day").show();
        } else if (selectedValue === "monthly") {
            $(".from_month, .to_month").show();
        } else if (selectedValue === "weekly") {
            if (startDateInput.value) {
                updateWeekDropdown();
            }
        }
    }

    // Setup event listeners
    $("#date_type").on("change", toggleFields);
    $("#start_date").on("change", function() {
        if ($("#date_type").val() === "weekly") {
            updateWeekDropdown();
        }
    });

    // Initial setup
    toggleFields();
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
<!-- moving from menu to kalender tab  -->
<script>
$(document).ready(function() {
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
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        let target = $(e.target).attr("href");
        history.replaceState(null, null, target);
    });
});
</script>

<!-- Dupllicate: start  -->
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).on("click", ".duplicate-event", function(e) {
    e.preventDefault();
    const eventId = $(this).data("event-id");

    Swal.fire({
        title: "Duplizieren auf neues Datum",
        input: "date",
        inputLabel: "Wähle ein Datum",
        inputAttributes: {
            min: new Date().toISOString().split("T")[0]
        },
        showCancelButton: true,
        confirmButtonText: "Duplizieren",
        cancelButtonText: "Abbrechen",
        inputValidator: (value) => {
            if (!value) {
                return "Datum ist erforderlich!";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const selectedDate = result.value;

            $.ajax({
                url: "{{ route('appointment.duplicate') }}",
                method: "POST",
                data: {
                    appointment_id: eventId,
                    new_date: selectedDate
                },
                success: function(response) {
                    Swal.fire("Erfolgreich!", response.message, "success").then(() => {
                        loadCalendarTasks(() => {
                            calendar.gotoDate(response.data
                            .start_date); // optional: scroll to new event
                        });
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON);
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = Object.values(errors).map(errArr => errArr.join(
                            ', ')).join('<br>');
                        Swal.fire("Validierungsfehler", errorMessages, "error");
                    } else {
                        Swal.fire("Fehler!", "Unbekannter Serverfehler", "error");
                    }
                }
            });

        }
    });
});
</script>

<!-- Dupllicate: end  -->


<!-- Menu Close and Open Button: start  -->
<script>
$(document).ready(function() {
    // Show the .new_task when the "Erstellen" button is clicked
    $('.create_new_task').on('click', function() {
        $('.new_task').css({
            right: '-100%', // Start offscreen (adjust based on your layout)
            display: 'block', // Ensure it's visible
        }).animate({
            right: '0', // Slide into view
        }, 500); // Animation duration in ms
    });

    // Hide the .new_task when the "abbrechen" button is clicked
    $('.new_task').on('click', '.close_task_window', function() {
        $('.new_task').animate({
            right: '-100%', // Slide out of view
        }, 500, function() {
            $(this).hide(); // Hide after animation completes
        });
    });
});
</script>

<script>
document.addEventListener("keydown", function(event) {
    const newTaskDiv = document.querySelector(".new_task");

    if (event.key === "Escape" && newTaskDiv.style.display === "block") {
        newTaskDiv.style.display = "none"; // Hide the new_task div
    }
});
</script>
<!-- Menu Close and Open Button: end  -->
 
<!-- Priority Script  -->
<script>
$(document).ready(function() {
    // Add click event listener to each dropdown-item
    $('#color_drop_down .dropdown-item').on('click', function() {
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
document.addEventListener("DOMContentLoaded", function() {
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
        } else if (appointmentType === "external") {
            externDiv.style.display = "block";
        } else if (appointmentType === "online") {
            linkDiv.style.display = "block";
        } else if (appointmentType === "telephone") {
            // Do nothing for telephone appointments
        } else {
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
            componentRestrictions: {
                country: 'DE'
            }
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.error("No details available for input: '" + place.name + "'");
                return;
            }

            let street = "",
                city = "",
                postalCode = "",
                latitude = "",
                longitude = "";

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
            script.src =
                "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places";
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
document.addEventListener("DOMContentLoaded", function() {
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
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
document.addEventListener("DOMContentLoaded", function() {
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

 
<script>
$(document).ready(function() {
    $('#source').select2({
        tags: true,
        placeholder: "Quelle auswählen",
        allowClear: true
    });
});
</script>

<script>
function togglePreTypeAndSource() {
    const contactSwitch = document.getElementById('switchContact');
    const preTypeBox = document.getElementById('preTypeBox');
    const sourceBox = document.getElementById('sourceBox');

    const show = contactSwitch.checked;
    preTypeBox.style.display = show ? 'block' : 'none';
    sourceBox.style.display = show ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const contactSwitch = document.getElementById('switchContact');
    contactSwitch.addEventListener('change', togglePreTypeAndSource);
    togglePreTypeAndSource(); // Run on page load
});
</script> 
<script>
$(document).ready(function() {
    $('#next_step').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true,
        tags:true
    });

    $('#report_responsible').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true
    });

     
});
</script> 


<script>
(function(){
  // === Data sources (adjust routes if needed) ===============================
  // Server should return:
  //  GET /picker/employees?search= -> [{id, name, lastname, image}]
  //  GET /picker/teams?search=     -> [{id, name}]
  //  GET /picker/teams/{id}        -> {id, name, members: [{id, name, lastname, image, position}]}

  const ROUTES = {
    employees: "{{ route('picker.employees') }}",   // expects ?search=
    teams: "{{ route('picker.teams') }}",           // expects ?search=
    teamMembers: (id) => "{{ url('/picker/teams') }}/" + id
  };

  // Fallback: if you already have employees in blade, you can inline:
  
  const BOOT_EMPLOYEES = null; // leave null to use AJAX

  // Refs
  const $modal = $('#pickerModal');
  const $openBtn = $('#openPickerBtn');
  const $applyAll = $('#pickerApplyAll');

  const $empSearch = $('#pickerEmployeeSearch');
  const $empGrid   = $('#pickerEmployeeGrid');

  const $teamSearch = $('#pickerTeamSearch');
  const $teamList   = $('#pickerTeamList');
  const $teamMembers= $('#pickerTeamMembers');
  const $teamTitle  = $('#pickerTeamTitle');
  const $teamSelectAll = $('#pickerSelectAllTeam');
  const $teamClear  = $('#pickerClearTeam');
  const $teamApply  = $('#pickerApplyTeam');

  // State
    window.selectedEmployeeIds = new Set( ($('#employee').val() || []).map(String) );
  let currentTeamId = null;
  let currentTeamMembers = []; // {id, name, lastname, image, position}

  // Utils
  const imgUrl = (img) => img ? `/images/employee/${img}` : `/images/employee/default.png`;
  const fullName = (e) => [e.name, e.lastname].filter(Boolean).join(' ');
  const posText = (pos) => pos ? ` — ${pos}` : '';

  function toggleId(set, id) {
    id = String(id);
    if (set.has(id)) set.delete(id); else set.add(id);
  }

  // === Employees Tab ========================================================

  function renderEmployeeGrid(list) {
    $empGrid.empty();
    if (!list || !list.length) {
      $empGrid.html('<div class="text-muted p-2">Keine Ergebnisse.</div>');
      return;
    }
    list.forEach(e => {
      const id = String(e.id);
      const active = window.selectedEmployeeIds.has(id) ? 'active' : ''; 
      const $chip = $(`
        <div class="picker-chip ${active}" data-id="${id}" title="${fullName(e)}">
          <img src="${imgUrl(e.image)}" class="picker-avatar" alt="">
          <span style="font-size:12px; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            ${fullName(e)}
          </span>
        </div>
      `);
      $chip.on('click', () => {
        toggleId(window.selectedEmployeeIds, id); 
        $chip.toggleClass('active', selectedEmployeeIds.has(id));
      });
      $empGrid.append($chip);
    });
  }

  async function loadEmployees(search='') {
    if (BOOT_EMPLOYEES && !search) return renderEmployeeGrid(BOOT_EMPLOYEES);
    const url = new URL(ROUTES.employees, window.location.origin);
    if (search) url.searchParams.set('search', search);
    const res = await fetch(url.toString());
    const json = await res.json();
    renderEmployeeGrid(json.data || []);
  }

  // === Teams Tab ============================================================

  function renderTeamList(list) {
    $teamList.empty();
    if (!list || !list.length) {
      $teamList.html('<div class="text-muted p-2">Keine Teams gefunden.</div>');
      return;
    }
    list.forEach(t => {
      const $item = $(`<div class="picker-list-item" data-id="${t.id}">${t.name}</div>`);
      $item.on('click', () => selectTeam(t.id, t.name));
      $teamList.append($item);
    });
  }

  function renderTeamMembers(members) {
    $teamMembers.empty();
    if (!members || !members.length) {
      $teamMembers.html('<div class="text-muted p-2">Keine Mitglieder.</div>');
      return;
    }
    const $wrap = $('<div class="d-flex flex-wrap" style="gap:8px;"></div>');
    members.forEach(m => {
      const id = String(m.id);
    const active = window.selectedEmployeeIds.has(id) ? 'active' : '';
      const $chip = $(`
        <div class="picker-chip ${active}" data-id="${id}" title="${fullName(m)}${posText(m.position)}">
          <img src="${imgUrl(m.image)}" class="picker-avatar" alt="">
          <div style="display:flex;flex-direction:column;line-height:1;">
            <span style="font-size:12px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${fullName(m)}</span>
            <small class="text-muted" style="font-size:10px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.position || ''}</small>
          </div>
        </div>
      `);
      $chip.on('click', () => {
        toggleId(window.selectedEmployeeIds, id); 
        $chip.toggleClass('active', selectedEmployeeIds.has(id));
      });
      $wrap.append($chip);
    });
    $teamMembers.append($wrap);
  }

  async function loadTeams(search='') {
    const url = new URL(ROUTES.teams, window.location.origin);
    if (search) url.searchParams.set('search', search);
    const res = await fetch(url.toString());
    const json = await res.json();
    renderTeamList(json.data || []);
  }

  async function selectTeam(id, name='Team') {
    currentTeamId = id;
    $teamTitle.text(name);
    const res = await fetch(ROUTES.teamMembers(id));
    const json = await res.json();
    currentTeamMembers = (json.members || []).map(m => ({
      id: m.id,
      name: m.name,
      lastname: m.lastname,
      image: m.image,
      position: m.position || (m.pivot && m.pivot.position) || null
    }));
    renderTeamMembers(currentTeamMembers);
  }

  // === Apply to #employee and allow edit afterwards =========================

  function ensureOptionInSelect2(id, text, image) {
    const $sel = $('#employee');
    const exists = $sel.find(`option[value="${id}"]`).length > 0;
    if (!exists) {
      const opt = new Option(text, id, true, true);
      $(opt).attr('data-image', imgUrl(image));
      $sel.append(opt);
    }
  }

  function applySelectionToEmployeeSelect() {
    const ids = Array.from(window.selectedEmployeeIds); 
    // Ensure options exist
    // If you have an endpoint to resolve names by IDs, use it; otherwise we trust Select2 existing options
    ids.forEach(id => {
      // If option missing, create a generic label; your formatEmployee renderer shows avatar anyway
      if ($('#employee').find(`option[value="${id}"]`).length === 0) {
        ensureOptionInSelect2(id, `ID ${id}`, null);
      }
    });
    $('#employee').val(ids).trigger('change');
  }

  // === Wire up ==============================================================
  $openBtn.on('click', async () => {
    // Sync current selection from Select2 to chips
    selectedEmployeeIds = new Set( ($('#employee').val() || []).map(String) );

    // Default load Employees tab + Teams list
    await Promise.all([loadEmployees(''), loadTeams('')]);
    // If you want a default team selected, call selectTeam(firstId)
    $modal.modal('show');
  });

  // Search fields
  let empTimer = null;
  $empSearch.on('input', (e) => {
    clearTimeout(empTimer);
    empTimer = setTimeout(()=> loadEmployees(e.target.value.trim()), 250);
  });

  let teamTimer = null;
  $teamSearch.on('input', (e) => {
    clearTimeout(teamTimer);
    teamTimer = setTimeout(()=> loadTeams(e.target.value.trim()), 250);
  });

  // Team actions
  $teamSelectAll.on('click', () => {
    currentTeamMembers.forEach(m => selectedEmployeeIds.add(String(m.id)));
    renderTeamMembers(currentTeamMembers);
  });
  $teamClear.on('click', () => {
    currentTeamMembers.forEach(m => selectedEmployeeIds.delete(String(m.id)));
    renderTeamMembers(currentTeamMembers);
  });
  $teamApply.on('click', () => {
    // Ensure team members exist as options with their names + avatars
    currentTeamMembers.forEach(m => {
      ensureOptionInSelect2(String(m.id), fullName(m), m.image);
      selectedEmployeeIds.add(String(m.id));
    });
    applySelectionToEmployeeSelect();
    // Keep modal open so user can switch teams; or close if you prefer:
    // $modal.modal('hide');
  });

  // Apply all (from both tabs)
  $applyAll.on('click', () => {
    applySelectionToEmployeeSelect();
    $modal.modal('hide');
  });

})();
</script>
{{-- FullCalendar CSS/JS (v5) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const calEl = document.getElementById('inquiry-mini-calendar');
  let calendar = new FullCalendar.Calendar(calEl, {
    initialView: 'timeGridWeek',
    locale: 'de',
    firstDay: 1,
    slotMinTime: '07:00:00',
    slotMaxTime: '21:00:00',
    allDaySlot: false,
    height: 420,
    headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
    initialDate: new Date(),
    events: [] // we load programmatically
  });
  calendar.render();

  // --- helpers ---
  function gatherSelection() {
    const internal = new Set();
    const external = new Set();
    const dates = [];

    $('#inquiryProductTable tbody tr').each(function(){
      const idx = $(this).data('index');
      const inVal = $(`.employee-select[data-index="${idx}"]`).val();
      const outVal = $(`.field-employee-select[data-index="${idx}"]`).val();
      const dtVal = $(`.termin-input[data-index="${idx}"]`).val(); // datetime-local

      if (inVal && !isNaN(inVal)) internal.add(parseInt(inVal,10));
      if (outVal && !isNaN(outVal)) external.add(parseInt(outVal,10));
      if (dtVal) {
        const d = dtVal.split('T')[0];
        if (d) dates.push(d);
      }
    });

    let anchorDate = (dates.length ? dates.sort()[0] : new Date().toISOString().slice(0,10));
    return {
      internal_ids: Array.from(internal),
      external_ids: Array.from(external),
      date: anchorDate
    };
  }

  // --- debounced refresher with stale-response guard ---
  let lastAnchor = null;
  let requestSeq = 0;   // increment per request
  let pendingSeq = 0;   // last request we care about

  const debounce = (fn, ms) => {
    let t; 
    return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), ms); };
  };

  const refreshCalendar = debounce(function(){
    const sel = gatherSelection();

    // Move calendar to correct week only if anchor changed
    if (sel.date !== lastAnchor) {
      lastAnchor = sel.date;
      calendar.gotoDate(sel.date);
    }

    calendar.removeAllEvents();

    // Nothing selected? show empty week (no fetch)
    if (!sel.internal_ids.length && !sel.external_ids.length) return;

    // Build URL with params
    const params = new URLSearchParams();
    sel.internal_ids.forEach(id => params.append('internal_ids[]', id));
    sel.external_ids.forEach(id => params.append('external_ids[]', id));
    params.append('date', sel.date);

    // Mark this request as the newest we care about
    const mySeq = ++requestSeq;
    pendingSeq = mySeq;

    $.getJSON('{{ route("inquiries.calendar.availability") }}?' + params.toString())
      .done(function(resp){
        // Ignore stale responses
        if (mySeq !== pendingSeq) return;

        (resp.events || []).forEach(ev => calendar.addEvent(ev));
        if (resp.weekStart) calendar.gotoDate(resp.weekStart);
      })
      .fail(function(xhr){
        // Ignore aborts / network hiccups
        if (xhr && xhr.statusText === 'abort') return;
        console.error('Calendar fetch error', xhr?.status, xhr?.responseText || xhr);
        if (window.toastr) toastr.error('Kalender konnte nicht geladen werden.');
      });
  }, 250);

  // --- bindings ---
  $(document).on('change', '.employee-select, .field-employee-select, .termin-input', refreshCalendar);
  $(document).on('click', '#addRow', () => setTimeout(refreshCalendar, 200));

  // first paint
  setTimeout(refreshCalendar, 300);
});
</script>

@endsection
