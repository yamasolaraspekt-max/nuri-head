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
    max-height: 500px;
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
</style>






@endsection

@section('content')

<!-- End::app-content -->

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
                        <li class="breadcrumb-item"><a
                                href="{{ url('/personal/task/'.auth()->user()->name) }}">Allgemeine Aufgaben</a></li>
                        <li class="breadcrumb-item active">Meine Kalender</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-body">

        <div class="text-right mb-2">
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="feather icon-settings"></i> Optionen
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#calendarSettingsModal">
                    Einstellungen
                    </a>
                </div>
            </div>

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
                                        </div>
                                        <input type="hidden" name="contact_mode" id="contact_mode" value="new">


                                        <div class="col-md-12 col-12 contact-name-block">
                                            <label for="task_title">Kunde/Kontakt *</label>
                                            <input type="text" id="name" class="form-control name" name="name">
                                        </div>

                                        <div class="col-md-12 contact-select-block d-none">
                                            <label for="task_title">Kunde/Kontakt *</label>
                                            <select name="customer_id" id="customer_id" class="contact_list" style="width:100%"></select>
                                            <input type="hidden" name="contact_type" id="contact_type" value="">
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

                                        <div class="col-md-4">
                                            <div class="row">
                                                <!-- Öffentlich Switch -->
                                                <div class="col-md-6">
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
                                                <div class="col-md-6">
                                                    <label for="switchContact">Kontakt</label>
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





                                        <div class="col-md-12 col-12">
                                            <label for="task_title">Teilnehmer *</label>
                                            <select name="employee[]" id="employee" class="employee" multiple
                                                style="width:100%">
                                                @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}"
                                                    data-image="{{asset('images/employee/'.$emp->image) }}">
                                                    {{ $emp->name }}</option>
                                                @endforeach
                                            </select>
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

                                            <textarea name="description" class="form-control" rows="1"></textarea>
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

                                        <div class="col-md-6 col-12 ">
                                            <label for="month">Nachfasstermin</label>
                                            <input type="date" name="reminder_date" class="form-control"
                                                id="reminder_date">
                                        </div>

                                        <div class="col-md-6 col-12 ">
                                            <label for="month"></label>
                                            <input type="time" name="reminder_time" class="form-control"
                                                id="reminder_time">
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
                    <label for="favoriteEmployees">🌟 Favoriten Mitarbeiter</label>
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
                    <label>🛑 Ausgeblendete Ansichten</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="year"> Jahr</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="month"> Monat</label><br>
                    <label><input type="checkbox" name="hidden_views[]" value="week"> Woche</label><br>
                </div>

                <!-- Calendar Color -->
                <div class="form-group">
                    <label for="calendarColorPicker">🎨 Kalenderfarbe</label>
                    <select id="calendarColorPicker" class="form-control">
                        <option value="default">Standard</option>
                        <option value="black">Schwarz</option>
                        <option value="red">Rot</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">💾 Speichern</button>
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


    window.addEventListener("resize", function() {
        if (window.innerWidth < 768 && calendar.view.type !== "listWeek") {
            calendar.changeView("listWeek");
        } else if (window.innerWidth >= 768 && calendar.view.type !== "timeGridWeek") {
            calendar.changeView("timeGridWeek");
        }
    });

    // Function to get selected employee IDs and their appointment selection
    function autoSelectFavoriteEmployees() {
        if (!favoriteEmployeeIds.length) return;

        // ✅ Desktop: check hidden checkboxes
        document.querySelectorAll('.employee_check').forEach(checkbox => {
            if (favoriteEmployeeIds.includes(checkbox.dataset.id)) {
                checkbox.checked = true;
            }
        });

        // ✅ Also set appointment checkboxes visible for these employees
        favoriteEmployeeIds.forEach(empId => {
            const appointWrapper = document.getElementById(`appointmentWrapper${empId}`);
            if (appointWrapper) appointWrapper.style.display = "block";
        });

        // ✅ Mobile: set Select2 dropdown
        if (window.innerWidth <= 768) {
            $('#mobileEmployeeSelect').val(favoriteEmployeeIds).trigger('change');
        }

        // ✅ Hidden field sync
        $('#employee').val(favoriteEmployeeIds).trigger('change');
    }

 

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
            allDaySlot: false,
          
            nowIndicator: true, // Show current time indicator
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
                left: "prev,next today toggleSlider verfgBtn",
                center: "title",
                right: "year,dayGridMonth,timeGridWeek,timeGridDay,listWeek",
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
                if (info.event.extendedProps.type === "holiday" || info.event.extendedProps.type === "sick") return;
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
                    badge.innerText = '🎌';
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
                const clickedDate = info.dateStr.split("T")[0]; // Extract the date (YYYY-MM-DD)
                const clickedTime = info.dateStr.includes("T") ?
                    info.dateStr.split("T")[1].slice(0, 5) // Extract HH:MM
                    :
                    "00:00"; // Default to midnight if no time is provided

                // Set the values in the form
                if (startDateInput) startDateInput.value = clickedDate;
                if (endDateInput) endDateInput.value = clickedDate;
                if (startTimeInput) startTimeInput.value = clickedTime;

                // Show the new task div
                if (newTaskDiv) {
                    newTaskDiv.style.display = "block";
                }
            },

            eventDidMount: function(info) {

                // searching the event after update
                let urlParams = new URLSearchParams(window.location.search);
                let taskId = urlParams.get("task_id"); // Get task ID from URL

                if (taskId && info.event.id.split("-")[0] === taskId) {
                    let eventElement = info.el;

                    // Add a class for temporary highlighting
                    eventElement.classList.add("edited-event");

                    // Smooth scroll to the event
                    eventElement.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });

                    // Blink effect for 3 seconds
                    setTimeout(() => {
                        eventElement.classList.remove("edited-event");
                    }, 3000);
                }
                // searching the event after update End


                if (info.event.extendedProps.type === "public_holiday") {
                    info.el.style.pointerEvents = "none";
                    info.el.style.backgroundColor = "#999999"; // gray
                    info.el.style.border = "none";
                    info.el.style.color = "#fff";
                    info.el.style.padding = "3px 6px";
                    info.el.style.fontSize = "11px";
                    info.el.style.borderRadius = "4px";
                    info.el.innerHTML = `<b>${info.event.title}</b>`;
                    return;
                }

                const {
                    employees,
                    taskColor,
                    priority,
                    appointment,
                    public,
                    type,
                    start_time,
                    end_time
                } = info.event.extendedProps;

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

                function hexToRGBA(hex, alpha = 1) {
                    hex = hex.replace(/^#/, '');

                    // Expand shorthand HEX (#abc → #aabbcc)
                    if (hex.length === 3) {
                        hex = hex.split('').map(char => char + char).join('');
                    }

                    // Convert to RGB values
                    let r = parseInt(hex.substring(0, 2), 16);
                    let g = parseInt(hex.substring(2, 4), 16);
                    let b = parseInt(hex.substring(4, 6), 16);

                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                }


                // Get truncated event title
                let truncatedTitle = truncateText(info.event.title, 20);

                // Mobile View: Show only title with background color
                if (window.innerWidth <= 500 && calendar.view.type === "timeGridWeek") {
                    const backgroundColor = info.event.backgroundColor ||
                    "#006400"; // fallback to green
                    const textColor = "#ffffff";
                    const title = truncateText(info.event.title || "Event", 20);
                    const startTimeFormatted = formatTime(info.event.extendedProps.start_time);
                    const endTimeFormatted = formatTime(info.event.extendedProps.end_time);

                    info.el.setAttribute("style", `
                            background-color: ${backgroundColor} !important;
                            color: ${textColor} !important;
                            border: none !important;
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

                    info.el.innerHTML = `
                            <div><strong>${title}</strong></div>
                            <div style="font-size: 10px;">${startTimeFormatted} - ${endTimeFormatted}</div>
                        `;
                } else {
                    info.el.classList.add("fc-daygrid-dot-event", "fc-event");

                    // ⛔ Fix: Remove default time + title
                    info.el.innerHTML = "";

                    info.el.setAttribute("style", `
                                white-space: normal !important;
                                border: 0px !important;
                                border-left: 5px solid ${info.event.backgroundColor} !important;
                                background-color: ${hexToRGBA(info.event.backgroundColor, 0.4)} !important;
                            `);

                    // Now inject your custom HTML...
                    const employeeNames = (employees || []).map(emp =>
                        `${emp.name} ${emp.lastname}`).join(", ");
                    info.el.innerHTML = `
                                <div class="custom-event">
                                    <div class="custom-event-header" style="display: flex; align-items: center;" id="calendar_icons">
                                        <i class="fa ${public !== "1" ? "fa-lock warning mr-1" : "fa-unlock mr-1"}"></i>
                                        <i class="fa ${
                                            priority === "very high" ? "fa-fire warning mr-1" 
                                            : priority === "high" ? "fa-bell important mr-1" 
                                            : ""
                                        }"></i>
                                        <p class="p-0 m-0" id="calendar_times" style="font-size:10px; color: ${type === 'task' ? '#74b2d4' : '#4c4c4c'};">
                                            ${formatTime(start_time)} - ${formatTime(end_time)}
                                        </p>
                                    </div>
                                    <div class="custom-event-title m-0">
                                        <p style="font-size:10px;margin-bottom: 0; color: ${type === 'task' ? '#74b2d4' : '#4c4c4c'}; font-weight: bold;">${truncatedTitle}</p>
                                        <p style="font-size:8px; color: ${type === 'task' ? '#74b2d4' : '#4c4c4c'};">${employeeNames}</p>
                                    </div>
                                </div>`;
                }

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


        calendar.render(); // Render after setting everything up
    }


    function showEventDetailsModal(event) {
        const { extendedProps, id, title, end } = event;
        const {
            employees,
            taskColor,
            priority,
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
        if (eventType === "holiday" || eventType === "sick" || eventType === "public_holiday") {
            Swal.fire({
                icon: "warning",
                title: "Änderung nicht erlaubt",
                text: "Urlaub und Krankenstand können nicht geändert werden.",
                confirmButtonColor: "#d92127",
            });
            info.revert(); // Undo move
            return;
        }

        Swal.fire({
            title: "Geben Sie einen Grund für die Änderung an",
            html: `
                        <textarea id="change_reason" class="swal2-textarea" placeholder="Geben Sie einen Grund für die Änderung an"></textarea>
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
                let taskId = info.event.id.split("-")[0];
                let empPersonalId = info.event.extendedProps.emp_personal_id;
                const startDateTime = new Date(info.event.start);
                const endDateTime = info.event.end ? new Date(info.event.end) : startDateTime;

                // Extract date and time
                const startDate = startDateTime.toISOString().split("T")[0];
                const startTime = startDateTime.toTimeString().split(" ")[0].substring(0, 5);
                const endDate = endDateTime.toISOString().split("T")[0];
                const endTime = endDateTime.toTimeString().split(" ")[0].substring(0, 5);

                fetch("{{ route('personal.task.change.appointment') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
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
                    .then(response => {
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text);
                            });
                        }
                        return response.json();
                    })
                    .then((result) => {
                        if (result.success) {
                            Swal.fire("Success!", "Veranstaltung erfolgreich aktualisiert.",
                                "success").then(() => {
                                loadCalendarTasks();
                            });
                        } else {
                            Swal.fire("Error!", result.message || "Failed to update event.",
                                "error");
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
        let employeeData = getSelectedEmployeeData(); // ✅ Always includes the current user

        $.ajax({
            url: "/get_personal_task_calendar",
            type: "GET",
            data: {
                employee_data: JSON.stringify(employeeData)
            },
            success: function(response) {
                console.log("✅ AJAX Response:", response);
                console.log('Backend data example:', response.data[0]);

                if (!response || !response.data || !Array.isArray(response.data)) {
                    console.error("❌ Invalid response format:", response);
                    if (typeof callback === "function") callback([]);
                    return;
                }

                const tasks = response.data.map(task => ({
                    id: task.id + "-" + task.start_date + "-" + task.start_time,
                    title: task.title || "-",
                    start: task.start_time ? `${task.start_date}T${task.start_time}` :
                        task.start_date || "-",
                    end: task.end_date ? (task.end_time ?
                            `${task.end_date}T${task.end_time}` : task.end_date) : task
                        .start_date || "-",
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
                        full_address: task.full_address || ([task.street, task.postcode,
                            task.city
                        ].filter(Boolean).join(' ')) || "-",
                        appointment_type: task.appointment_type || "-",
                        description: task.description || "-",
                        customer_id: task.customer_id ?? null,
                        contact_id: task.contact_id ?? null,

                    },
                }));


                extractPublicHolidayDates(tasks);
                initializeCalendar(tasks);
                initializeMiniCalendar(tasks);

                if (typeof callback === "function") {
                    callback(tasks);
                }
            },
            error: function(xhr) {
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
            let eventType = deleteButton.getAttribute(
            'data-event-type'); // Get event type (appointment or task)
            let baseUrl = window.location.origin; // Get base URL (http://127.0.0.1:8000)

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

    // Handle save operation
    $('.save-task').on('click', function(e) {
        e.preventDefault();

        syncCheckboxWithDropdown();

        let form = $('#task-store-form');
        let formData = form.serialize();

        let title = $('#name').val();
        let employee = $('#employee').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        let errors = [];

        if (!title) errors.push('Der Titel darf nicht leer sein.');
        if (!employee || employee.length === 0) errors.push(
            'Bitte weisen Sie mindestens einen Mitarbeiter zu.');
        if (!startDate) errors.push('Das Startdatum darf nicht leer sein.');
        if (!endDate) errors.push('Das Enddatum darf nicht leer sein.');
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
        }

        // ✅ Check for public holidays within the range
        if (startDate && endDate) {
            let current = new Date(startDate);
            let last = new Date(endDate);
            while (current <= last) {
                let dateStr = current.toISOString().split('T')[0];
                if (publicHolidayDates.includes(dateStr)) {
                    errors.push(`Datum ${dateStr} ist ein Feiertag.`);
                }
                current.setDate(current.getDate() + 1);
            }
        }

        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Fehlerhafte Eingabe',
                html: `<ul style="text-align: left;">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
            });
            return;
        }


        let appointmentId = $('#appointment_id').val();
        let method = appointmentId ? 'PUT' : 'POST';
        let actionUrl = appointmentId ?
            `/main-appointments/${appointmentId}` :
            `{{ route('main.appointments.store') }}`;

        $.ajax({
            url: actionUrl,
            type: method,
            data: formData,
            beforeSend: function() {
                $('.save-task').prop('disabled', true).text('speichern...');
            },
            success: function(response) {
                $('.save-task').prop('disabled', false).text('speichern');
                $('.new_task_card').hide();
                form.trigger('reset');
                $('#appointment_id').val('');

                // ✅ Reset dynamic or special fields manually
                $('#customer_id').val(null).trigger('change');        // Select2
                    // Multi-select
                $('#name').val('');
                $('#name_display').val('');
                $('#contact_type').val('');
                $('#contact_mode').val('new');                        // Default to "new"
                $('#newContact').prop('checked', true).trigger('change'); // Trigger UI toggle

                // Optional: reset address & contact fields
                $('#phone, #email, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input, #full_address').val('');


                Swal.fire({
                    icon: 'success',
                    title: 'Erfolg',
                    text: appointmentId ? 'Termin erfolgreich aktualisiert!' :
                        'Termin erfolgreich gespeichert!',
                });

                // Reload calendar
                let currentView = calendar.view.type;
                let currentDate = calendar.getDate();
                loadCalendarTasks(() => {
                    calendar.changeView(currentView);
                    calendar.gotoDate(currentDate);
                });
            },
            error: function(xhr) {
                $('.save-task').prop('disabled', false).text('speichern');

                let serverErrors = xhr.responseJSON?.errors || {};
                let errorMessages = Object.values(serverErrors).flat().map(msg =>
                    `<li>${msg}</li>`).join('');

                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    html: `<ul>${errorMessages || 'Unbekannter Fehler aufgetreten.'}</ul>`,
                });
            }
        });
    });

    // Binding the edit modal

    $(document).on('click', '.edit-event', function(e) {
        e.preventDefault();
        Swal.close();

        const eventId = $(this).data('event-id');

        $.get(`/main-appointments/${eventId}/fetch`, function(data) {
            // Basic fields
            $('#appointment_id').val(data.id);
            $('#name').val(data.name);
            $('#color').val(data.color);
            $('#colorIcon').css('color', data.color);
            $('#start_date').val(data.start_date);
            $('#end_date').val(data.end_date);
            $('#start_time').val(data.start_time ?? '');
            $('#end_time').val(data.end_time ?? '');
            $('#total_time').val(data.total_time);
            $('#execution_type').val(data.execution_type);
            $('#appointment_type').val(data.appointment_type);
            $('#priority').val(data.priority);
            $('#date_type').val(data.date_type);

            // Address & Location
            $('#full_address').val(data.full_address);
            $('#street-input').val(data.street);
            $('#city-input').val(data.city);
            $('#postal_code-input').val(data.postcode);
            $('#latitude-input').val(data.latitude);
            $('#longitude-input').val(data.longitude);

            // Contact
            $('#phone').val(data.phone);
            $('#email').val(data.email);
            $('#link').val(data.link);
            $('#description').val(data.note);
            $('#branch_id').val(data.branch_id).trigger('change');
            $('#branch_address_id').val(data.branch_address_id).trigger('change');

            $('#reminder_date').val(data.reminder_date);
            $('#reminder_time').val(data.reminder_time);
            $('#from_day').val(data.from_day);
            $('#to_day').val(data.to_day);
            $('#from_month').val(data.from_month);
            $('#to_month').val(data.to_month);
            $('#contact_type').val(data.contact_type);
            $('#contact_id').val(data.contact_id).trigger('change');

            // Pre Type
            if (data.pre_type) {
                $('#pre_type').val(data.pre_type).trigger('change');
            }

            // Source
            if (data.source) {
                if (!$(`#source option[value="${data.source}"]`).length) {
                    $('#source').append(new Option(data.source, data.source, true, true));
                }
                $('#source').val(data.source).trigger('change');
            } else {
                $('#source').val('').trigger('change');
            }

            // Switches
            $('#switchPublic').prop('checked', data.public === '1');
            $('#switchContact').prop('checked', data.is_contact === '1').trigger('change');

            // Employees
            if (Array.isArray(data.employee_ids)) {
                $('#employee').val(data.employee_ids).trigger('change');
            }

            // Contact Mode Logic
            if (data.contact_mode === 'select') {
                $('#selectContact').prop('checked', true).trigger('change');
                $('#customer_id').val(data.customer_id).trigger('change');
                $('#contact_type').val(data.contact_type);

                $('.contact-name-block').addClass('d-none');
                $('.contact-select-block').removeClass('d-none');
            } else {
                $('#newContact').prop('checked', true).trigger('change');
                $('#name').val(data.name);

                $('.contact-name-block').removeClass('d-none');
                $('.contact-select-block').addClass('d-none');
            }

            $('#execution_type').trigger('change');
            if (typeof togglePreTypeAndSource === 'function') {
                togglePreTypeAndSource();
            }

            $('.new_task_card').show();
            $('.title').text('TERMIN BEARBEITEN');
        });
    });


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
    loadCalendarTasks((tasks = []) => {
        const today = new Date().toISOString().split("T")[0];
        filterMainCalendarByDate(today);
    });



    function fetchData(searchQuery = '', filterType = 'employee') {
        const isMobile = window.innerWidth <= 768;
        const employeeList = document.getElementById('search_emp_result');
        employeeList.innerHTML = ''; // Reset list

        const apiUrl = `/getEmployees?search=${searchQuery}&filter=${filterType}`;
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(result => {
                const data = result.data || [];
                if (data.length === 0) {
                    employeeList.innerHTML = '<p>No results found.</p>';
                    return;
                }

                const favoriteIds = (window.favoriteEmployeeIds || []).map(String);

                if (isMobile) {
                    const select = document.createElement('select');
                    select.id = 'mobileEmployeeSelect';
                    select.className = 'form-control employee';
                    select.setAttribute('multiple', 'multiple');

                    data.forEach(emp => {
                        const option = document.createElement('option');
                        option.value = emp.id;
                        option.text = `${emp.name} ${emp.lastname}`;
                        option.setAttribute('data-image', `/images/employee/${emp.image}`);
                        select.appendChild(option);
                    });

                    employeeList.appendChild(select);

                    $('#mobileEmployeeSelect').select2({
                        templateResult: formatEmployee,
                        templateSelection: formatEmployee,
                        placeholder: "Mitarbeiter auswählen",
                        width: '100%',
                        escapeMarkup: m => m,
                        dropdownParent: $('#search_emp_result')
                    });

                    // ✅ Select favorites only (if any)
                    if (favoriteIds.length) {
                        $('#mobileEmployeeSelect').val(favoriteIds).trigger('change');
                        loadCalendarTasks(favoriteIds); // trigger load on init
                    }

                    $('#mobileEmployeeSelect').off('change').on('change', function () {
                        const selected = $(this).val() || [];
                        loadCalendarTasks(selected);
                    });

                } else {
                    data.forEach(emp => {
                        const isFavorite = favoriteIds.includes(String(emp.id));
                        const borderColor = emp.color || 'red';
                        
                        const listItem = document.createElement('div');
                        listItem.classList.add('list-item');

                        listItem.innerHTML = `
                            <div class="d-flex align-items-center m-0">
                                <input type="checkbox" class="employee_check" data-id="${emp.id}" id="check${emp.id}" style="display:none" ${isFavorite ? 'checked' : ''}>

                                <div class="avatar mr-1">
                                    <img src="/images/employee/${emp.image}" alt="avatar" width="38" height="38"
                                        data-id="${emp.id}" class="employee_checkbox ${isFavorite ? 'emp_active' : ''}" id="employeeCheck${emp.id}"
                                        style="border-color: ${isFavorite ? borderColor : 'transparent'}; border-radius: 50%; padding: 2px;">
                                </div>

                                <span>
                                    <span style="font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                        ${emp.name} ${emp.lastname}
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

                        // Image toggle
                        image.addEventListener('click', function () {
                            this.classList.toggle('emp_active');
                            checkbox.checked = this.classList.contains('emp_active');
                            this.style.borderColor = checkbox.checked ? borderColor : 'transparent';
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        });

                        checkbox.addEventListener('change', function () {
                            toggleAppointmentCheckbox(emp.id);
                            loadCalendarTasks();
                        });

                        employeeList.appendChild(listItem);
                    });


                    // ✅ Sync all checked boxes from favorites
                    document.querySelectorAll('.employee_check').forEach(checkbox => {
                        checkbox.checked = favoriteIds.includes(checkbox.dataset.id);
                    });

                    // ✅ Sync visually + to dropdown
                    syncCheckboxWithDropdown();
                    $('#employee').val(favoriteIds).trigger('change');

                    if (favoriteIds.length) {
                        loadCalendarTasks(favoriteIds); // only call if list exists
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                employeeList.innerHTML = '<p>Failed to fetch data. Please try again later.</p>';
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
            const filterType = this.value;
            toggleSearchInput(filterType); // Show the correct input
            fetchData('', filterType); // Fetch data with the selected filter type
        });
    });

    // ✅ Add event listeners for the search inputs
    document.querySelector('.employee_search_input input').addEventListener('input', function() {
        const searchQuery = this.value;
        fetchData(searchQuery, 'employee');
    });


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

            $('#employee').val(selectedIds).trigger('change');
        }

        // 🔄 Dropdown ➜ Checkbox sync
        $('#employee').on('change', function() {
            const selectedIds = $(this).val() || [];

            document.querySelectorAll('.employee_check').forEach((checkbox) => {
                checkbox.checked = selectedIds.includes(checkbox.dataset.id);
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
                    favoriteEmployeeIds = calendar_settings.favorite_employees || [];
                    loadSettingsIntoModal(calendar_settings);
                    applySettingsToCalendar(calendar_settings);
                    applyCalendarSettings(response); // ✅ Pass the whole response

                    // 🔁 THEN load employees
                    fetchData(); // ✅ Call fetchData with favorites loaded
                });
        }

        function autoSelectFavoriteEmployees() {
            const favoriteIds = (window.favoriteEmployeeIds || []).map(String);

            // ✅ Desktop: Toggle avatar & checkbox
            document.querySelectorAll('.employee_check').forEach(cb => {
                const id = cb.dataset.id;
                const avatar = document.querySelector(`#employeeCheck${id}`);

                if (favoriteIds.includes(id)) {
                    cb.checked = true;
                    if (avatar) {
                        avatar.classList.add('emp_active');
                        avatar.style.borderColor = avatar.dataset.color || 'red';
                    }
                } else {
                    cb.checked = false;
                    if (avatar) {
                        avatar.classList.remove('emp_active');
                        avatar.style.borderColor = 'transparent';
                    }
                }
            });

            // ✅ Select2 (mobile + dropdown)
            $('#mobileEmployeeSelect').val(favoriteIds).trigger('change');
            $('#employee').val(favoriteIds).trigger('change');

            // ✅ Load events after auto-selection
            loadCalendarTasks();
        }


        function applyCalendarSettings(settings) {
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
            setTimeout(autoSelectFavoriteEmployees, 300);
        }



        $('#calendarSettingsForm').on('submit', function (e) {
            e.preventDefault();
            const settings = {
                favorite_employees: $('#favoriteEmployees').val() || [],
                hidden_views: $('input[name="hidden_views[]"]:checked').map((_, el) => el.value).get(),
                calendar_color: $('#calendarColorPicker').val()
            };

            fetch('/calendar-settings/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ calendar_settings: settings })
            }).then(res => res.json()).then(response => {
                if (response.status === 'success') {
                    $('#calendarSettingsModal').modal('hide');
                    Swal.fire('Gespeichert!', 'Einstellungen wurden gespeichert.', 'success');
                    applySettingsToCalendar(settings); // optional live apply
                }
            });
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
        let employeeData = [];
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            const selected = $('#mobileEmployeeSelect').val() || [];
            selected.forEach(employeeId => {
                employeeData.push({
                    employee_id: employeeId,
                    tasks_only: 0,
                    appointments_only: 1
                });
            });
        } else {
            document.querySelectorAll(".employee_check").forEach((checkbox) => {
                if (checkbox.checked) {
                    const employeeId = checkbox.getAttribute("data-id");
                    const appointmentCheckbox = document.querySelector(
                        `#employeeAppointment${employeeId}`);
                    employeeData.push({
                        employee_id: employeeId,
                        tasks_only: appointmentCheckbox && appointmentCheckbox.checked ? 1 : 0,
                        appointments_only: appointmentCheckbox && appointmentCheckbox.checked ? 0 : 1,
                    });
                }
            });
        }

        if (employeeData.length === 0) {
            console.log("⚠ No employee selected, using current user...");
            employeeData.push({
                employee_id: authUserName,
                tasks_only: 0,
                appointments_only: 1
            });
        }

        return employeeData;
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

<!-- Start Date and End date same value : End -->


<!-- Calcuation of total Time:  -->



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


<!-- Getting the contact list in drop down: start  -->
<script>
$(document).ready(function() {
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
            data: function(params) {
                return {
                    search: params.term || '' // Pass search term if available, otherwise load all
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.main_id, // Contact ID
                            text: item.name + " " + item.lastname + " - " + item
                            .type, // Display name in dropdown
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
            $('#name').val('');
            $('#name_display').val('');
            $('#customer_id').val(null).trigger('change');
        } else {
            $('.contact-name-block').addClass('d-none');
            $('.contact-select-block').removeClass('d-none');
        }
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





});
</script>



<!-- Getting the contact list in drop down: end  -->

<!-- Showing the contact_type select  -->




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



@endsection
