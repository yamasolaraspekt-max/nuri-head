@extends('admin.layouts.app')
@section('title')
Mein Kalendar
@endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
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

.week-row {
    min-width: 390px;
}
.week-label {
    width: 100%;
}
.week-days > div {
    min-width: 15px; /* w-14 */
}
.relative .dropdown-menu {
    z-index: 9999;
}

</style>






@endsection

@section('content')

<!-- End::app-content -->

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper"> 
        <div class="content-body">  
                <div class="w-full min-h-screen p-1 sm:p-6 flex flex-col mt-3">
                    <div class="flex justify-between items-center mb-0"> 
                        <div class="flex items-center gap-2">
                            <button id="goToToday" class="text-sm bg-gray-600 text-white px-1 py-1   hover:bg-gray-700">Heute</button>
                            <button id="openFilterModal" class="text-sm bg-gray-600 text-white px-1 py-1   hover:bg-gray-700"><i class="feather icon-filter"></i></button>
                            <button type="button" onclick="openAppointmentModal()" class="text-sm bg-gray-600 text-white px-1 py-1 hover:bg-gray-700">
                                <i class="feather icon-plus"></i>
                            </button>
                            <button id="clearFilters" class="text-sm bg-red-600 text-white px-1 py-1   hover:bg-gray-700">
                               <i class="feather icon-corner-down-left"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-0 mt-1">
                        <select id="monthSelect" class="border px-2 py-1 rounded text-sm">
                            <!-- Filled dynamically -->
                        </select>

                        <select id="weekSelect" class="border px-2 py-1 rounded text-sm">
                            <option value="">Woche wählen…</option>
                        </select>
                    </div>

                    <!-- Infinite Scrollable Calendar -->
                     <div id="calendarMonth" class="text-center text-lg font-semibold mb-1"></div>

                    <div id="calendarWrapper" class="overflow-x-auto mb-0 border-b border-gray-200">
                        <div id="scrollableCalendar" class="flex space-x-2 w-max px-2 py-2"></div>
                    </div>

                    <!-- Heute -->
                    <div id="todayAppointments" class="p-1">
                        
                    </div>


                    <!-- Detail Sidebar -->
                    <div id="sidebar" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out z-50">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h4 class="text-lg font-semibold">Details</h4>
                           <button id="closeSidebarBtn" class="text-gray-500 hover:text-red-600">✕</button>

                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <h5 class="font-medium text-gray-700">Mitarbeiter</h5>
                                <p id="detailEmployee" class="text-sm text-gray-600"></p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-700">Zeit</h5>
                                <p id="detailTime" class="text-sm text-gray-600"></p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-700">Projekt Nr.</h5>
                                <p id="detailCode" class="text-sm text-gray-600"></p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-700">Status</h5>
                                <p id="detailStatus" class="text-sm text-indigo-600 font-semibold"></p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-700">Beschreibung</h5>
                                <p id="detailDescription" class="text-sm text-gray-600"></p>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-700">Bilder</h5>
                                <div class="flex space-x-2 overflow-x-auto">
                                    <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                                    <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                                    <img src="https://via.placeholder.com/80" class="w-20 h-16 rounded object-cover" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Modal -->
                    <div id="filterModal" class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center hidden">
                        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
                            <h2 class="text-lg font-semibold mb-2"><i class="feather icon-search"></i> Filter Termine</h2>

                            <!-- Employee -->
                            <div class="mb-1">
                                <label for="filterEmployee" class="block text-sm font-medium text-gray-700">Mitarbeiter</label>
                                <select id="filterEmployee" class="form-select select2 w-full" multiple style="width:100%">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date -->
                            <div class="mb-1">
                                <label for="filterDate" class="block text-sm font-medium text-gray-700">Datum</label>
                                <input type="date" id="filterDate" class="form-input w-full border-gray-300 " />
                            </div>

                            <!-- Keyword -->
                            <div class="mb-1">
                                <label for="filterKeyword" class="block text-sm font-medium text-gray-700">Suchbegriff</label>
                                <input type="text" id="filterKeyword" class="form-input w-full border-gray-300 " placeholder="z. B. Projektname, Adresse..." />
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end gap-2 mt-4">
                                <button id="applyFilters" class="bg-gray-400 text-white px-4 py-2  hover:bg-gray-700">Anwenden</button>
                                <button id="closeFilterModal" class="bg-gray-400 text-white px-4 py-2  hover:bg-gray-500">Abbrechen</button>
                            </div>
 
                        </div>
                    </div>
 
                     <!-- Add Appointment Modal -->
                 
                        <div id="createAppointmentModal" class="fixed inset-0 z-50 bg-black bg-opacity-40 flex items-center justify-center hidden">
                            <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative overflow-y-auto max-h-[95vh]">
                                <h2 class="text-lg font-semibold mb-1"><i class="feather icon-calendar"></i> Neuen Termin erstellen</h2>

                                <form id="mobileAppointmentForm">
                                    @csrf
                                    <input type="hidden" name="contact_mode" id="contact_mode" value="new">

                                    <!-- Title -->
                                    <div class="mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Titel</label>
                                        <input type="text" name="name" class="form-input w-full border-gray-300 " placeholder="Termin Titel" required>
                                    </div>

                                    <!-- Phone / Email -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-1">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Telefon</label>
                                            <input type="text" name="phone" id="contactPhone" class="form-input w-full border-gray-300 " placeholder="Telefon">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">E-Mail</label>
                                            <input type="email" name="email" id="contactEmail" class="form-input w-full border-gray-300 " placeholder="E-Mail">
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                                        <input type="text" id="autocomplete" placeholder="Adresse suchen" class="form-input w-full border-gray-300 ">
                                        <input type="hidden" name="street">
                                        <input type="hidden" name="postcode">
                                        <input type="hidden" name="city">
                                        <input type="hidden" name="latitude">
                                        <input type="hidden" name="longitude">
                                    </div>

                                    <!-- Contact -->
                                    <div class="mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Kontakt</label>
                                        <select id="contactSelect" name="selected_contact" class="form-select select2 w-full border-gray-300 " style="width:100%">
                                            <!-- AJAX -->
                                        </select>
                                    </div>

                                    <!-- Product -->
                                    <div class="mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Produkt</label>
                                        <select id="productSelect" name="product_selection" class="form-select w-full border-gray-300 " disabled>
                                            <!-- AJAX -->
                                        </select>
                                    </div>

                                    <!-- Employees -->
                                    <div class="mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Mitarbeiter</label>
                                            <select name="employees[]" id="employeeSelect" class="form-select select2 w-full border-gray-300" multiple style="width:100%">
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" data-image="{{ asset('images/employee/' . $employee->image) }}">
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                    </div>

                                    <!-- Start / End Date + Time -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-1">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Start Datum</label>
                                            <input type="date" name="start_date" class="form-input w-full border-gray-300 ">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Start Uhrzeit</label>
                                            <input type="time" name="start_time" class="form-input w-full border-gray-300 ">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Ende Datum</label>
                                            <input type="date" name="end_date" class="form-input w-full border-gray-300 ">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Ende Uhrzeit</label>
                                            <input type="time" name="end_time" class="form-input w-full border-gray-300 ">
                                        </div>
                                    </div>

                                    <!-- Priority & Color -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-1">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Priorität</label>
                                            <select name="priority" class="form-select w-full border-gray-300 ">
                                                <option value="normal">Normal</option>
                                                <option value="high">Hoch</option>
                                                <option value="very high">Sehr Hoch</option>
                                            </select>
                                        </div>
                                       
                                    </div>

                                    <!-- Buttons -->
                                    <div class="flex justify-end gap-2 mt-4">
                                        <button type="submit" class="bg-primary text-white px-2 py-2  hover:bg-gray-700" id="saveAppointment">Speichern</button>
                                        <button type="button" onclick="closeAppointmentModal()" class="bg-gray-400 text-white px-2 py-2  hover:bg-gray-500">Abbrechen</button>
                                    </div>

                                        <button onclick="closeAppointmentModal()" class="absolute top-2 right-2 mr-2 text-gray-500 hover:text-red-600 text-xl">✕</button>
                                </form>
                            </div>
                        </div>


        </div>
    </div>
</div>
  
@endsection
@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('app-assets/js/scripts/tooltip/tooltip.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const scrollable = document.getElementById('scrollableCalendar');
    const calendarWrapper = document.getElementById('calendarWrapper');
    const sidebar = document.getElementById('sidebar');
    const today = new Date();
    const currentYear = today.getFullYear();

    // Always work from local midnight
    let baseDate = new Date();
    baseDate.setHours(0, 0, 0, 0);

    let selectedDayEl = null;
    let currentFilters = { employees: [], keyword: '', date: '' };
    const monthSelect = document.getElementById('monthSelect');
    const weekSelect = document.getElementById('weekSelect');

    // ---------- Local date helpers (no UTC conversion) ----------
    function formatDateLocal(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    function parseLocalDate(ymd) {
        const [y, m, d] = ymd.split('-').map(Number);
        return new Date(y, m - 1, d);
    }
    function getISOWeekNumber(date) {
        const temp = new Date(date.getTime());
        temp.setHours(0, 0, 0, 0);
        temp.setDate(temp.getDate() + 3 - ((temp.getDay() + 6) % 7));
        const firstThursday = new Date(temp.getFullYear(), 0, 4);
        const diff = temp - firstThursday + ((firstThursday.getDay() + 6) % 7) * 86400000;
        return 1 + Math.floor(diff / 604800000);
    }
    function getMonday(date) {
        const d = new Date(date.getTime());
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        d.setDate(diff);
        d.setHours(0,0,0,0);
        return d;
    }
    function updateCalendarMonthLabel(date) {
        const monthNames = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
        const label = `${monthNames[date.getMonth()]} ${date.getFullYear()}`;
        const labelEl = document.getElementById('calendarMonth');
        if (labelEl) labelEl.innerText = label;
    }

    // ---------- Day cell ----------
    function addDayBox(date) {
        const weekdays = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'];
        const dayName = weekdays[date.getDay()];
        const dateStr = formatDateLocal(date);
        const isToday = dateStr === formatDateLocal(today);

        const dayBox = document.createElement('div');
        dayBox.className = `flex-shrink-0 w-14 rounded text-center text-sm font-medium cursor-pointer py-1 px-1 border transition hover:bg-primary ${isToday ? 'bg-gray-500 text-white' : 'text-gray-700 bg-gray-100'}`;
        dayBox.innerHTML = `${dayName}<br><span class="block text-base">${date.getDate()}</span>`;
        dayBox.dataset.date = dateStr;
        if (isToday) dayBox.id = 'todayDateBox';

        dayBox.addEventListener('click', () => {
            if (selectedDayEl) {
                selectedDayEl.classList.remove('bg-primary', 'text-white');
                selectedDayEl.classList.add('bg-gray-100', 'text-gray-700');
            }
            dayBox.classList.remove('bg-gray-100', 'text-gray-700');
            dayBox.classList.add('bg-primary', 'text-white');
            selectedDayEl = dayBox;

            currentFilters.date = dateStr;
            loadAppointments();
        });

        return dayBox;
    }

    // ---------- Infinite weekly rows ----------
    let currentRange = { start: -14, end: 14 };

    function renderWeeklyRange(startWeekOffset, endWeekOffset) {
        for (let weekOffset = startWeekOffset; weekOffset <= endWeekOffset; weekOffset++) {
            const monday = getMonday(baseDate);
            monday.setDate(monday.getDate() + weekOffset * 7);
            const weekNumber = getISOWeekNumber(monday);
            const monthName = monday.toLocaleDateString('de-DE', { month: 'long', year: 'numeric' });

            const weekId = `week-${formatDateLocal(monday)}`;
            if (document.getElementById(weekId)) continue;

            const weekRow = document.createElement('div');
            weekRow.className = 'week-row';
            weekRow.id = weekId;

            const weekLabel = document.createElement('div');
            weekLabel.className = 'week-label text-sm text-center text-gray-500 mb-1';
            weekLabel.innerText = `KW ${weekNumber} · ${monthName}`;
            weekRow.appendChild(weekLabel);

            const weekDays = document.createElement('div');
            weekDays.className = 'week-days flex gap-1';

            for (let i = 0; i < 7; i++) {
                const day = new Date(monday.getTime());
                day.setDate(day.getDate() + i);
                const dayBox = addDayBox(day);
                weekDays.appendChild(dayBox);
            }

            weekRow.appendChild(weekDays);
            scrollable.appendChild(weekRow);
        }
    }

    // ---------- Month & week selectors ----------
    function populateMonthDropdown(year) {
        const months = [];
        for (let m = 0; m < 12; m++) {
            const date = new Date(year, m, 1);
            months.push({
                value: `${year}-${String(m + 1).padStart(2, '0')}`,
                label: date.toLocaleDateString('de-DE', { month: 'long', year: 'numeric' })
            });
        }
        monthSelect.innerHTML = months.map(m => `<option value="${m.value}">${m.label}</option>`).join('');
        monthSelect.value = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;
    }

    function populateWeeksOfMonth(year, month) {
        const weeks = [];
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);

        let monday = getMonday(firstDay);
        while (monday <= lastDay) {
            const isoWeek = getISOWeekNumber(monday);
            const label = `KW ${isoWeek} (${monday.toLocaleDateString('de-DE', { day: '2-digit', month: 'short' })})`;
            weeks.push({
                value: formatDateLocal(monday),
                label: label
            });
            monday.setDate(monday.getDate() + 7);
        }

        weekSelect.innerHTML = `<option value="">Woche wählen…</option>` +
            weeks.map(w => `<option value="${w.value}">${w.label}</option>`).join('');
    }

    monthSelect.addEventListener('change', function () {
        const [year, month] = this.value.split('-');
        populateWeeksOfMonth(parseInt(year, 10), parseInt(month, 10));
        updateCalendarMonthLabel(parseLocalDate(`${year}-${month}-01`));
    });

    weekSelect.addEventListener('change', function () {
        if (!this.value) return;
        const weekStart = parseLocalDate(this.value); // Monday, LOCAL
        const dates = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(weekStart.getTime());
            d.setDate(weekStart.getDate() + i);
            dates.push(formatDateLocal(d));
        }

        currentFilters.date = this.value;
        loadAppointmentsForWeek(dates);

        const weekRow = document.getElementById(`week-${this.value}`);
        if (weekRow) {
            weekRow.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'center' });
        } else {
            Swal.fire('Nicht geladen', 'Diese Woche ist noch nicht sichtbar. Bitte scrollen oder laden Sie mehr Daten.', 'warning');
        }
    });

    // ---------- Scrolling to extend range ----------
    renderWeeklyRange(currentRange.start, currentRange.end);

    calendarWrapper.addEventListener('scroll', () => {
        const maxScrollLeft = scrollable.scrollWidth - calendarWrapper.clientWidth;
        const buffer = 100;

        if (calendarWrapper.scrollLeft < buffer) {
            currentRange.start -= 1;
            renderWeeklyRange(currentRange.start, currentRange.start);
        }

        if (calendarWrapper.scrollLeft > maxScrollLeft - buffer) {
            currentRange.end += 1;
            renderWeeklyRange(currentRange.end, currentRange.end);
        }
    });

    // ---------- Today button ----------
    document.getElementById('goToToday').addEventListener('click', () => {
        const todayEl = document.getElementById('todayDateBox');
        if (todayEl) {
            todayEl.scrollIntoView({ behavior: 'smooth', inline: 'center' });
            todayEl.click();
        }
    });

    // ---------- Sidebar ----------
    document.getElementById('closeSidebarBtn')?.addEventListener('click', () => {
        sidebar.classList.add('translate-x-full');
    });

    // ---------- Data loaders ----------
    function loadAppointments() {
        const params = new URLSearchParams();
        if (currentFilters.date) params.append('date', currentFilters.date);
        if (currentFilters.keyword) params.append('keyword', currentFilters.keyword);
        if (currentFilters.employees.length) {
            currentFilters.employees.forEach(e => params.append('employees[]', e));
        }

        fetch(`/get-appointments?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector('#todayAppointments');
                if (!container) return;

                container.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = `<div class="text-gray-500 text-sm mt-2">Keine Termine gefunden.</div>`;
                    return;
                }

                data.forEach(app => {
                    const wrapper = document.createElement('div');
                    wrapper.className = `border rounded-xl mb-1 p-1 bg-white cursor-pointer hover:bg-gray-50 open-sidebar`;

                    wrapper.dataset.employee = app.employee_name || '';
                    wrapper.dataset.time = app.start_time || '';
                    wrapper.dataset.code = app.code || '';
                    wrapper.dataset.status = app.status || '';
                    wrapper.dataset.description = app.description || '';
                    wrapper.classList.add('custom-border-left');
                    wrapper.style.setProperty('border-left', `6px solid ${app.employee_color || '#8fc73e'}`, 'important');

                    const priorityIcon = app.priority === "very high"
                        ? '<i class="fa fa-fire warning mr-1"></i>'
                        : app.priority === "high"
                            ? '<i class="fa fa-bell important mr-1"></i>'
                            : "";

                    const reportIcon = app.is_report === "1"
                        ? '<i class="feather icon-file-text warning mr-1"></i>'
                        : "";

                    const mapIcon = `
                        <i class="feather icon-map-pin text-green-600 ml-1 cursor-pointer open-map"
                            data-lat="${app.latitude || ''}"
                            data-lng="${app.longitude || ''}"
                            data-name="${app.name || 'Ziel'}"
                            title="In Google Maps anzeigen">
                        </i>
                    `;

                    const imageBlock = Array.isArray(app.images) && app.images.length > 0 ? `
                        <div class="overflow-x-auto scrollbar-hide mt-2">
                            <div class="flex gap-2 min-w-max">
                                ${app.images.map((img, i) => `
                                    <a href="${img}" class="glightbox" data-gallery="day-${currentFilters.date}" data-glightbox="title: Bild ${i + 1}">
                                        <img src="${img}" class="h-16 w-24 object-cover rounded" />
                                    </a>
                                `).join('')}
                            </div>
                        </div>` : '';

                    wrapper.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex items-start space-x-2">
                                <div class="relative custom-menu-wrapper">
                                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none custom-menu-button" data-id="${app.id}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="absolute left-0 mt-2 z-50 w-36 bg-white rounded shadow border text-sm custom-menu hidden">
                                        <a href="/appointment_details/${app.id}" class="block px-3 py-2 hover:bg-gray-100 text-gray-700">Details</a>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-blue-100 text-blue-600 custom-edit" data-id="${app.id}">Bearbeiten</button>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-yellow-100 text-yellow-600 custom-duplicate" data-id="${app.id}">Duplizieren</button>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-red-100 text-red-600 custom-delete" data-id="${app.id}">Löschen</button>
                                    </div>
                                </div>

                                <div class="text-sm text-gray-500 mt-0">${priorityIcon} ${reportIcon}</div>
                            </div>

                            <div class="flex items-center gap-2">
                                ${app.employee_image ? `<img src="${app.employee_image}" class="h-6 w-6 rounded-full object-cover" alt="Avatar">` : ''}
                            </div>
                        </div>

                        <div class="text-sm font-medium text-gray-700">${app.name || ''}</div>
                        <div class="text-xs text-indigo-600 font-semibold mb-1">${app.status || ''} · ${app.description || 'Keine Beschreibung'}</div>
                        <div class="text-xs text-indigo-600 font-semibold mb-0"><i class="feather icon-clock"></i> ${app.start_time || ''} - ${app.end_time || ''} ${mapIcon}</div>
                        ${imageBlock}
                    `;

                    container.appendChild(wrapper);
                });

                if (window.glightboxInstance) window.glightboxInstance.destroy();
                window.glightboxInstance = GLightbox({
                    selector: '.glightbox',
                    loop: true,
                    touchNavigation: true,
                    closeButton: true,
                });

                document.querySelectorAll('.open-map').forEach(icon => {
                    icon.addEventListener('click', () => {
                        const lat = icon.getAttribute('data-lat');
                        const lng = icon.getAttribute('data-lng');
                        const name = icon.getAttribute('data-name') || 'Ziel';

                        if (!lat || !lng) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Keine Adresse vorhanden',
                                text: 'Für diesen Termin ist keine gültige Position hinterlegt.',
                            });
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(position => {
                            const currentLat = position.coords.latitude;
                            const currentLng = position.coords.longitude;

                            const mapUrl = `https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${lat},${lng}&travelmode=driving`;
                            const embedUrl = `https://www.google.com/maps/embed/v1/directions?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&origin=${currentLat},${currentLng}&destination=${lat},${lng}&mode=driving`;

                            Swal.fire({
                                title: `${name}`,
                                html: `
                                    <div style="width:100%;height:300px;margin-bottom:10px;">
                                        <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen
                                            src="${embedUrl}">
                                        </iframe>
                                    </div>
                                    <a href="${mapUrl}" target="_blank" class="swal2-confirm swal2-styled" style="background:#3085d6;">
                                        In Google Maps öffnen
                                    </a>
                                `,
                                width: 600,
                                showConfirmButton: false
                            });

                        }, () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Standort nicht verfügbar',
                                text: 'Bitte aktivieren Sie den Standortzugriff im Browser.',
                            });
                        });
                    });
                });
            });
    }

    function loadAppointmentsForWeek(dates) {
        const params = new URLSearchParams();
        dates.forEach(date => params.append('week_dates[]', date));
        if (currentFilters.employees.length) {
            currentFilters.employees.forEach(e => params.append('employees[]', e));
        }
        if (currentFilters.keyword) {
            params.append('keyword', currentFilters.keyword);
        }

        fetch(`/get-appointments?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                const container = document.querySelector('#todayAppointments');
                if (!container) return;
                container.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = `<div class="text-gray-500 text-sm mt-2">Keine Termine gefunden.</div>`;
                    return;
                }

                data.forEach(app => {
                    const wrapper = document.createElement('div');
                    wrapper.className = `border rounded-xl mb-1 p-1 bg-white cursor-pointer hover:bg-gray-50 open-sidebar`;

                    wrapper.dataset.employee = app.employee_name || '';
                    wrapper.dataset.time = app.start_time || '';
                    wrapper.dataset.code = app.code || '';
                    wrapper.dataset.status = app.status || '';
                    wrapper.dataset.description = app.description || '';
                    wrapper.classList.add('custom-border-left');
                    wrapper.style.setProperty('border-left', `6px solid ${app.employee_color || '#8fc73e'}`, 'important');

                    const priorityIcon = app.priority === "very high"
                        ? '<i class="fa fa-fire warning mr-1"></i>'
                        : app.priority === "high"
                            ? '<i class="fa fa-bell important mr-1"></i>'
                            : "";

                    const reportIcon = app.is_report === "1"
                        ? '<i class="feather icon-file-text warning mr-1"></i>'
                        : "";

                    const mapIcon = `
                        <i class="feather icon-map-pin text-green-600 ml-1 cursor-pointer open-map"
                            data-lat="${app.latitude || ''}"
                            data-lng="${app.longitude || ''}"
                            data-name="${app.name || 'Ziel'}"
                            title="In Google Maps anzeigen">
                        </i>
                    `;

                    const imageBlock = Array.isArray(app.images) && app.images.length > 0 ? `
                        <div class="overflow-x-auto scrollbar-hide mt-2">
                            <div class="flex gap-2 min-w-max">
                                ${app.images.map((img, i) => `
                                    <a href="${img}" class="glightbox" data-gallery="week-${currentFilters.date}" data-glightbox="title: Bild ${i + 1}">
                                        <img src="${img}" class="h-16 w-24 object-cover rounded" />
                                    </a>
                                `).join('')}
                            </div>
                        </div>` : '';

                    wrapper.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex items-start space-x-2">
                                <div class="relative custom-menu-wrapper">
                                    <button class="text-gray-500 hover:text-gray-700 focus:outline-none custom-menu-button" data-id="${app.id}">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="absolute left-0 mt-2 z-50 w-36 bg-white rounded shadow border text-sm custom-menu hidden">
                                        <a href="/appointment_details/${app.id}" class="block px-3 py-2 hover:bg-gray-100 text-gray-700">Details</a>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-blue-100 text-blue-600 custom-edit" data-id="${app.id}">Bearbeiten</button>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-yellow-100 text-yellow-600 custom-duplicate" data-id="${app.id}">Duplizieren</button>
                                        <button class="block w-full text-left px-3 py-2 hover:bg-red-100 text-red-600 custom-delete" data-id="${app.id}">Löschen</button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500 mt-0">${priorityIcon} ${reportIcon}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${app.employee_image ? `<img src="${app.employee_image}" class="h-6 w-6 rounded-full object-cover" alt="Avatar">` : ''}
                            </div>
                        </div>

                        <div class="text-sm font-medium text-gray-700">${app.name || ''}</div>
                        <div class="text-xs text-indigo-600 font-semibold mb-1">${app.status || ''} · ${app.description || 'Keine Beschreibung'}</div>
                        <div class="text-xs text-indigo-600 font-semibold mb-0"><i class="feather icon-clock"></i> ${app.start_time || ''} - ${app.end_time || ''} ${mapIcon}</div>
                        ${imageBlock}
                    `;

                    container.appendChild(wrapper);
                });

                if (window.glightboxInstance) window.glightboxInstance.destroy();
                window.glightboxInstance = GLightbox({
                    selector: '.glightbox',
                    loop: true,
                    touchNavigation: true,
                    closeButton: true,
                });

                document.querySelectorAll('.open-map').forEach(icon => {
                    icon.addEventListener('click', () => {
                        const lat = icon.getAttribute('data-lat');
                        const lng = icon.getAttribute('data-lng');
                        const name = icon.getAttribute('data-name') || 'Ziel';

                        if (!lat || !lng) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Keine Adresse vorhanden',
                                text: 'Für diesen Termin ist keine gültige Position hinterlegt.',
                            });
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(position => {
                            const currentLat = position.coords.latitude;
                            const currentLng = position.coords.longitude;
                            const mapUrl = `https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${lat},${lng}&travelmode=driving`;
                            const embedUrl = `https://www.google.com/maps/embed/v1/directions?key=YOUR_GOOGLE_MAPS_API_KEY&origin=${currentLat},${currentLng}&destination=${lat},${lng}&mode=driving`;

                            Swal.fire({
                                title: `${name}`,
                                html: `
                                    <div style="width:100%;height:300px;margin-bottom:10px;">
                                        <iframe width="100%" height="100%" style="border:0;" loading="lazy" allowfullscreen
                                            src="${embedUrl}">
                                        </iframe>
                                    </div>
                                    <a href="${mapUrl}" target="_blank" class="swal2-confirm swal2-styled" style="background:#3085d6;">
                                        In Google Maps öffnen
                                    </a>
                                `,
                                width: 600,
                                showConfirmButton: false
                            });

                        }, () => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Standort nicht verfügbar',
                                text: 'Bitte aktivieren Sie den Standortzugriff im Browser.',
                            });
                        });
                    });
                });
            });
    }

    // ---------- Clear filters ----------
    document.getElementById('clearFilters').addEventListener('click', () => {
        currentFilters = { employees: [], keyword: '', date: '' };
        $('#filterEmployee').val([]).trigger('change');
        document.getElementById('filterKeyword').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterModal').classList.add('hidden');
        loadAppointments();
    });

    // ---------- Menus & actions ----------
    document.body.addEventListener('click', function (e) {
        document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));

        if (e.target.closest('.custom-menu-button')) {
            e.stopPropagation();
            const wrapper = e.target.closest('.custom-menu-wrapper');
            const menu = wrapper.querySelector('.custom-menu');
            if (menu) menu.classList.toggle('hidden');
            return;
        }

        if (e.target.closest('.custom-delete')) {
            const id = e.target.dataset.id;
            Swal.fire({
                title: 'Löschen bestätigen?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/appointments/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(() => {
                        loadAppointments();
                        Swal.fire('Gelöscht!', '', 'success');
                    });
                }
            });
            return;
        }

        if (e.target.closest('.custom-edit')) {
            const id = e.target.dataset.id;
            window.location.href = `/appointments/edit/${id}`;
            return;
        }

        if (e.target.closest('.custom-duplicate')) {
            const id = e.target.dataset.id;
            fetch(`/appointments/duplicate/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                loadAppointments();
                Swal.fire('Dupliziert!', '', 'success');
            });
            return;
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));
        }
    });
    window.addEventListener('scroll', () => {
        document.querySelectorAll('.custom-menu').forEach(m => m.classList.add('hidden'));
    });

    // ---------- Filter modal ----------
    document.getElementById('openFilterModal').addEventListener('click', () => {
        document.getElementById('filterModal').classList.remove('hidden');
    });
    document.getElementById('closeFilterModal').addEventListener('click', () => {
        document.getElementById('filterModal').classList.add('hidden');
    });
    document.getElementById('applyFilters').addEventListener('click', () => {
        currentFilters.employees = $('#filterEmployee').val() || [];
        currentFilters.keyword = document.getElementById('filterKeyword').value.trim();
        currentFilters.date = document.getElementById('filterDate').value || currentFilters.date;
        document.getElementById('filterModal').classList.add('hidden');
        loadAppointments();
    });

    // ---------- Kick-off ----------
    document.getElementById('goToToday').click();
    populateMonthDropdown(currentYear);
    monthSelect.dispatchEvent(new Event('change'));
});
</script>

<script>
           function openAppointmentModal() {
            document.getElementById('createAppointmentModal').classList.remove('hidden');
        }

        function closeAppointmentModal() {
            document.getElementById('createAppointmentModal').classList.add('hidden');
        }
</script>
 
 <script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('createAppointmentModal');
    const form = document.getElementById('mobileAppointmentForm');

 
    // 🧠 Google Places Autocomplete
    window.initMap = function () {
        const input = document.getElementById("autocomplete");
        if (!input) return;

        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            componentRestrictions: { country: 'de' }
        });

        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            const components = {
                street_number: '',
                route: '',
                locality: '',
                postal_code: ''
            };

            place.address_components.forEach(component => {
                const types = component.types;
                if (types.includes('street_number')) components.street_number = component.long_name;
                if (types.includes('route')) components.route = component.long_name;
                if (types.includes('locality')) components.locality = component.long_name;
                if (types.includes('postal_code')) components.postal_code = component.long_name;
            });

            form.querySelector('[name="street"]').value = `${components.route} ${components.street_number}`.trim();
            form.querySelector('[name="postcode"]').value = components.postal_code;
            form.querySelector('[name="city"]').value = components.locality;
            form.querySelector('[name="latitude"]').value = place.geometry.location.lat();
            form.querySelector('[name="longitude"]').value = place.geometry.location.lng();
        });
    };

    // 📤 Submit Form via AJAX
    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("/appointments/store/mobile", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Gespeichert', text: 'Termin erfolgreich erstellt.' });
                form.reset();
                closeAppointmentModal();
                document.getElementById('goToToday')?.click();
            } else {
                Swal.fire({ icon: 'error', title: 'Fehler', text: data.message || 'Speichern fehlgeschlagen.' });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({ icon: 'error', title: 'Serverfehler', text: 'Ein Fehler ist aufgetreten.' });
        });
    });

    // 🔄 Contact Select2
    $('#contactSelect').select2({
        ajax: {
            url: '/get/contact/list',
            dataType: 'json',
            delay: 250,
            data: params => ({ search: params.term }),
            processResults: data => ({
                results: data.map(item => ({
                    id: `${item.type}:${item.main_id}:${item.sub_id}`,
                    text: `${item.name} ${item.lastname} (${item.type})`,
                    phone: item.phone,
                    email: item.email,
                    street: item.street,
                    city: item.city,
                    postcode: item.postcode,
                    lat: item.latitude,
                    lon: item.longitude,
                    isCustomer: item.type === 'Kunde',
                    customerId: item.type === 'Kunde' ? item.main_id : null
                }))
            })
        },
        placeholder: 'Kontakt auswählen',
        allowClear: true
    });

    $('#contactSelect').on('select2:select', function (e) {
        const d = e.params.data;
        document.getElementById('contactPhone').value = d.phone || '';
        document.getElementById('contactEmail').value = d.email || '';
        form.querySelector('[name="street"]').value = d.street || '';
        form.querySelector('[name="postcode"]').value = d.postcode || '';
        form.querySelector('[name="city"]').value = d.city || '';
        form.querySelector('[name="latitude"]').value = d.lat || '';
        form.querySelector('[name="longitude"]').value = d.lon || '';

        if (d.isCustomer) {
            $('#productSelect').prop('disabled', false);
            loadProductsByCustomer(d.customerId);
        } else {
            $('#productSelect').empty().prop('disabled', true);
        }
    });

    function loadProductsByCustomer(customerId) {
        $.get(`/api/products-by-customer?customer_id=${customerId}`, function (data) {
            const options = [];
            data.forEach(group => {
                const optgroup = $('<optgroup>', { label: group.text });
                group.children.forEach(p => {
                    optgroup.append(`<option value="${p.customer_id}_${p.alternative_id}_${p.product_id}">${p.product_name}</option>`);
                });
                $('#productSelect').append(optgroup);
            });
        });
    }
});
</script>



<script>
    $(document).ready(function () {
        $('#filterEmployee').select2({
            placeholder: "Mitarbeiter auswählen"
        });
    });
</script>
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>



<script>
$(document).ready(function () {
    $('#employeeSelect').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: m => m
    });

    function formatEmployee(employee) {
        if (!employee.id) return employee.text;

        const img = $(employee.element).data('image');
        const name = employee.text;

        return `
            <div class="flex items-center">
                <img src="${img}" class="w-6 h-6 rounded-full mr-2" style="object-fit:cover;" />
                <span>${name}</span>
            </div>
        `;
    }
});
</script>

@endsection
