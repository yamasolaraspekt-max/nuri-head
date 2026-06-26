@extends('admin.layouts.app')

@section('title') TERMIN-KALENDER @endsection
@section('style')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/calendar.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
    .fc-year-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        padding: 10px;
    }
    .fc-month-box {
        text-align: center;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .fc-month-grid {
        border: 1px solid #ddd;
        padding: 5px;
    }
    .custom-dropdown select option {
        display: flex;
        align-items: center;
    }

    .custom-dropdown select option i {
        margin-right: 8px; /* Space between the icon and the text */
        color: inherit; /* Inherit color from the option */
    }

    /* Optional: Adjusting icon size and padding for a better look */
    .custom-dropdown select option i {
        font-size: 1.2em; /* Adjust icon size */
    }

    #priorityIcon {
        font-size: 18px; /* Adjust icon size */
        color: grey; /* Default color */
        transition: color 0.3s;
    }

    #priorityIcon.checked {
        color: #8fc73e; /* Color when checked */
    }
    .img-circle {
    border-radius: 50%;
    object-fit: cover;
}




</style>



@endsection

@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">TERMIN-KALENDAR</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body"> 
                <!-- Full calendar start -->
                <section id="basic-examples">
                    <div class="row">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="cal-category-bullets">
                                            <div class="bullets-group-1 mt-2" style="display: flex;  margin-bottom: 2rem;">
                                                <div class="category-business mr-1">
                                                    <span class="bullet bullet-success bullet-sm mr-25"></span>
                                                    Business
                                                </div>
                                                <div class="category-work mr-1">
                                                    <span class="bullet bullet-warning bullet-sm mr-25"></span>
                                                    Work
                                                </div>
                                                <div class="category-personal mr-1">
                                                    <span class="bullet bullet-danger bullet-sm mr-25"></span>
                                                    Personal
                                                </div>
                                                <div class="category-others">
                                                    <span class="bullet bullet-primary bullet-sm mr-25"></span>
                                                    Others
                                                </div> 
                                            </div>
                                        </div>
                                        <div id='calendar'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form id="appointmentForm"> 
                    <!-- calendar Modal starts-->
                        <div class="modal fade text-left" id="add_appointment" tabindex="-1" role="dialog" aria-labelledby="add_appointment" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="add_appointment">Neuen Termin</h4> 
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row" style="    display: flex; justify-content: flex-end;"> 
                                        <div class="col-xl-2 col-md-2 col-12 mb-1" style="display: flex;  justify-content: space-around; align-items: baseline;">
                                                <fieldset class="form-group"> 
                                                    <div class="custom-dropdown">
                                                        <select name="calendar_color" id="colorSelect" class="form-control select2 colorSelect"  >
                                                            <option value="#FF5733" data-color="#FF5733"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FFBD33" data-color="#FFBD33"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#75FF33" data-color="#75FF33"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#33FF57" data-color="#33FF57"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#33FFBD" data-color="#33FFBD"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#33C7FF" data-color="#33C7FF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#335BFF" data-color="#335BFF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#7533FF" data-color="#7533FF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#BD33FF" data-color="#BD33FF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FF33C7" data-color="#FF33C7"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FF3333" data-color="#FF3333"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FF7F33" data-color="#FF7F33"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FFD133" data-color="#FFD133"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#A3FF33" data-color="#A3FF33"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#33FF99" data-color="#33FF99"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#33FFD1" data-color="#33FFD1"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#3385FF" data-color="#3385FF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#7F33FF" data-color="#7F33FF"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FF33A3" data-color="#FF33A3"> <i class="feather icon-aperture"></i> </option>
                                                            <option value="#FF3399" data-color="#FF3399"> <i class="feather icon-aperture"></i> </option>
                                                        </select>
                                                    </div>
                                                </fieldset>
                                                <fieldset>
                                                <i class="feather icon-flag" id="priorityIcon" style="cursor: pointer;"></i>
                                                <input type="checkbox" name="priority" id="priorityCheckbox" style="display: none;">
                                                </fieldset>
                                            </div> 

                                        </div>

                                        <div class="row">
                                          <div class="col-xl-6 col-md-6 col-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Kunde</label>
                                                    <select name="customer_id" id="customer_id" class="form-control select2" style="width:100% !important;">
                                                        <option value=""></option> <!-- Default empty option -->
                                                    </select>
                                                    <label for="quicklead" class="primary" id="quicklead">QUICK LEAD</label>
                                                </fieldset>
                                            </div>
                                            <div class="col-xl-6 col-md-6 col-12">
                                                <fieldset class="form-group">
                                                    <label for="helpInputTop">Gwerke</label>
                                                    <select name="product_id" id="product_id" class="form-control select22" style="width:100% !important;">
                                                        <option value=""></option>
                                                        <!-- Options will be loaded here via AJAX -->
                                                    </select>
                                                </fieldset>
                                                <input type="hidden" name="selectProduct" id="selectProduct">
                                            </div> 
  
                                            <div class="tasks col-xl-12 d-flex"> 
                                               <div class="table-responsive">
                                                    <table class="table table-striped table-bordered" id="tasksTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Phase/Process</th>
                                                                <th>Aufgaben</th>
                                                                <th>Verantwortlich</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody> 
                                                            <tr>
                                                                <td>
                                                                    <select name="active[0][phase_id]" id="phase_id" class="form-control select22 phase_id" style="width:100% !important;"> 
                                                                        <!-- Options will be loaded here via AJAX -->
                                                                    </select>
                                                                </td>
                                                                <td> 
                                                                    <select name="active[0][activity_id][]" id="activity_id" class="form-control select22 activity_id" style="width:100% !important;" multiple> 
                                                                        <!-- Options will be loaded here via AJAX -->
                                                                    </select>  
                                                                </td>
                                                                <td> 
                                                                    <select name="active[0][employee_id][]" id="employee" class="form-control employee_id" style="width:100% !important;" multiple>
                                                                        @foreach ($employee as $emp)
                                                                        <option value="{{$emp->id}}" data-image="{{ asset('images/employee/'.$emp->image)}}">
                                                                            {{$emp->name}} {{ $emp->lastname }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>   
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-flat-danger mr-1 mb-1 waves-effect waves-light add-task" id="add_task">
                                                                        <i class="feather icon-plus"></i>
                                                                    </button> 
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table> 
                                                </div>

                                            </div> 
                                            <div class="col-xl-12 col-md-12 col-12 ">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Titel</label>
                                                    <input type="text" class="form-control"  name="title">
                                                </fieldset>
                                            </div>
                                            <div class="col-xl-3 col-md-6 col-12 ">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Startdatum</label>
                                                    <input type="date" class="form-control"  name="start_date">
                                                </fieldset>
                                            </div>

                                            <div class="col-xl-3 col-md-6 col-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Enddatum</label>
                                                    <input type="date" class="form-control"  name="end_date">
                                                </fieldset>
                                            </div>
                                          
                                            <div class="col-xl-3 col-md-6 col-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Startzeit</label>
                                                    <input type="time" class="form-control"  name="start_time">
                                                </fieldset>
                                            </div>

                                            <div class="col-xl-3 col-md-6 col-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Endzeit</label>
                                                    <input type="time" class="form-control"  name="end_time">
                                                </fieldset>
                                            </div>

                                            <div class="col-xl-12 col-md-12 col-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Notiz</label>
                                                    <textarea name="description" id="" class="form-control" rows="4"></textarea>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absage</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- calendar Modal ends-->


                    <form id="edit_appointment_form">
                        @csrf
                    <div class="modal fade" id="edit_appointment" tabindex="-1" role="dialog" aria-labelledby="edit_appointment">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="edit_appointment">Bearbeiten Termin</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row" style="display: flex; justify-content: flex-end;">
                                        <input type="hidden" id="customer_id_edit" name="customer_id">
                                         <input type="hidden" id="product_id_edit" name="product_id">
                                        <!-- Color selection -->
                                        <div class="col-xl-2 col-md-2 col-12 mb-1">
                                            <fieldset class="form-group">
                                               <select name="calendar_color" id="colorSelect" class="form-control select2 colorSelect"  >
                                                    <option value="#FF5733" data-color="#FF5733"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FFBD33" data-color="#FFBD33"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#75FF33" data-color="#75FF33"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#33FF57" data-color="#33FF57"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#33FFBD" data-color="#33FFBD"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#33C7FF" data-color="#33C7FF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#335BFF" data-color="#335BFF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#7533FF" data-color="#7533FF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#BD33FF" data-color="#BD33FF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FF33C7" data-color="#FF33C7"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FF3333" data-color="#FF3333"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FF7F33" data-color="#FF7F33"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FFD133" data-color="#FFD133"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#A3FF33" data-color="#A3FF33"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#33FF99" data-color="#33FF99"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#33FFD1" data-color="#33FFD1"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#3385FF" data-color="#3385FF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#7F33FF" data-color="#7F33FF"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FF33A3" data-color="#FF33A3"> <i class="feather icon-aperture"></i> </option>
                                                    <option value="#FF3399" data-color="#FF3399"> <i class="feather icon-aperture"></i> </option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Title, dates, and times -->
                                        <div class="col-xl-12 col-md-12 col-12">
                                            <fieldset class="form-group">
                                                <label for="title">Titel</label>
                                                <input type="text" class="form-control" name="title" id="title">
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12">
                                            <fieldset class="form-group">
                                                <label for="start_date">Startdatum</label>
                                                <input type="date" class="form-control" name="start_date" id="start_date">
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12">
                                            <fieldset class="form-group">
                                                <label for="end_date">Enddatum</label>
                                                <input type="date" class="form-control" name="end_date" id="end_date">
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12">
                                            <fieldset class="form-group">
                                                <label for="start_time">Startzeit</label>
                                                <input type="time" class="form-control" name="start_time" id="start_time">
                                            </fieldset>
                                        </div>
                                        <div class="col-xl-3 col-md-6 col-12">
                                            <fieldset class="form-group">
                                                <label for="end_time">Endzeit</label>
                                                <input type="time" class="form-control" name="end_time" id="end_time">
                                            </fieldset>
                                        </div>

                                        <!-- Description -->
                                        <div class="col-xl-12 col-md-12 col-12">
                                            <fieldset class="form-group">
                                                <label for="description">Notiz</label>
                                                <textarea name="description" class="form-control" rows="4" id="description"></textarea>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absage</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                    <!-- calendar Modal ends-->
                </section>
                <!-- // Full calendar end -->
 
        </div>
    </div> 
</div> 
@endsection

@section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            editable: true,
            droppable: true,
            eventResizableFromStart: true,
            eventDurationEditable: true,

            // Handle event resizing
            eventResize: function(info) {
                if (info.event) {
                    updateEvent(info.event);
                } else {
                    console.error("Event not found for resizing.");
                }
            },

            // Handle event dragging
            eventDrop: function(info) {
                if (info.event) {
                    updateEvent(info.event);
                } else {
                    console.error("Event not found for dragging.");
                }
            },

            // Click to add new event
            dateClick: function(info) {
                $('#start_date').val(info.dateStr);
                $('#end_date').val(info.dateStr);
                $('#add_appointment').modal('show');
            },

            
            // Double-click to edit event
            eventClick: function(info) {
                if (!info.event) {
                    console.error("Event not found.");
                    return;
                }
                handleEditModal(info.event);
            },

            // Custom HTML content for event
           // Custom HTML content for event with dynamic border color
           eventContent: function(arg) {
                let employeesHtml = '';

                // Check if employees are provided and build HTML for them
                if (arg.event.extendedProps.employees && arg.event.extendedProps.employees.length > 0) {
                    employeesHtml = arg.event.extendedProps.employees.map(employee => {
                        return `
                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="${employee.name} ${employee.lastname}" class="avatar pull-up">
                                <img class="media-object rounded-circle" src="/images/employee/${employee.image}" alt="${employee.name}" height="30" width="30">
                            </li>
                        `;
                    }).join('');
                } else {
                    employeesHtml = '<li></li>';
                }

                // Set the border color dynamically or default to '#7315d1'
                var borderColor = arg.event.borderColor ? arg.event.borderColor : '#7315d1';

                // Check if the event has priority and set the badge accordingly
                const priorityBadge = arg.event.extendedProps.priority ? 
                    `<div class="badge badge-pill badge-light-warning mb-1" id="importantTask"> 
                        <i class="fa fa-bell important" id="bellIcon"></i> 
                    </div>` 
                    :  ``;

                return {
                    html: `
                   <div class="fc-daygrid-event-harness " style="border-left: 15px solid ${borderColor}; padding: 10px; border-radius: 5px;">
                    <div class="fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-future" data-event-id="${arg.event.id}">
                        <div class="custom-event">
                            <div class="row"> 
                                <div class="col-xl-12">
                                    <div class="custom-event-header">
                                     <a href="{{ url('personal_task_details/'.${id}) }}"> 
                                        <div class="custom-event-status">
                                          
                                                <span class="custom-event-status-text" style="white-space: normal; word-wrap: break-word;"> ${truncateText(arg.event.title || '', 4)}
                                                </span> 
                                        </div> 
                                        </a>
                                    </div> 
                                </div> 
                              
                            </div>
                   
                            <div class="custom-event-product">

                                <a href="{{ url('customer_details') }}?search=${arg.event.extendedProps.customerName}" class="float-left mr-1">
                                    <small><span style="color:gray;">${arg.event.extendedProps.customerName} ${arg.event.extendedProps.customerLastname}</span></small>
                                </a>
                             
                                
                                <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                    ${employeesHtml}
                                </ul>
                            </div>
                            
                            <div class="custom-event-footer"> 
                                    
                                <div class="date d-flex">
                                    <div class="custom-event-time mr-1">
                                        <small><i class="feather icon-calendar"></i> ${arg.event.startStr.split('T')[0]}</small>
                                         <lable class="ml-1"  for="route" data-latitude="${arg.event.extendedProps.latitude}" data-longitude="${arg.event.extendedProps.longitude}" onclick="showDirections(this)" ><i class="feather icon-map"></i> Route</lable> 

                                    </div>
                                    <div class="custom-event-time">
                                        <small>${priorityBadge}</small><br> 
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dropdown menu for the event -->
                            <div class="custom-dropdown-menu" style="display:none;">
                                <ul>
                                    <li class="dropdown-item" data-action="edit">Edit</li>
                                    <li class="dropdown-item" data-action="delete">Delete</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
 
                    `
                };
            }
 
        });

        // Fetch existing appointments from the server
       $.ajax({
            url: "{{ route('appointments.get') }}",
            type: "GET",
            success: function(appointments) {
                if (Array.isArray(appointments)) {
                    appointments.forEach(function(appointment) {
                        // Use the color from the database or default to #7315d1 if not available
                        var eventColor = appointment.color ? appointment.color : '#8fc73e';
                        console.log('The color: ', eventColor);

                        var startTime = appointment.start_time ? appointment.start_time.substring(0, 5) : '';
                        var endTime = appointment.end_time ? appointment.end_time.substring(0, 5) : '';

                        var eventStart = appointment.start_date + (startTime ? 'T' + startTime + ':00' : '');
                        var eventEnd = appointment.end_date + (endTime ? 'T' + endTime + ':00' : '');

                        // Add the event to the calendar with the correct border color using eventContent
                        calendar.addEvent({
                            id: appointment.id,
                            title: appointment.title,
                            start: eventStart,
                            end: eventEnd || eventStart,
                            description: appointment.description,
                            backgroundColor: '#ffffff',  // Keeping background white
                            borderColor: eventColor,     // Use the dynamically set color
                            extendedProps: {
                                customer_id: appointment.customer_id,
                                customerName: appointment.customerName,  // Include customer name
                                customerLastname: appointment.customerLastname,  // Include customer lastname
                                latitude: appointment.lat,  // Latitude of Customer of the google Map  
                                longitude: appointment.lon,   // Longitude of Customer of the google Map  
                                product_id: appointment.product_id,
                                priority: appointment.priority,
                                phases: appointment.phases,
                                activities: appointment.activities,
                                employees: appointment.employees

                            },
                            // Handle custom event rendering with correct border color
                            eventContent: function(arg) {
                                return {
                                    html: `
                                        <div class="fc-daygrid-event-harness" style="border-left: 4px solid ${eventColor};">
                                            <div class="fc-daygrid-event">
                                                <div class="custom-event">
                                                    <div class="custom-event-header d-flex">
                                                        <span class="custom-event-status">${arg.event.title}</span>
                                                    </div>
                                                    <div class="custom-event-time">
                                                        ${arg.event.startStr.split('T')[1] || 'All Day'} - ${arg.event.endStr ? arg.event.endStr.split('T')[1] : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `
                                };
                            }
                        });
                    });
                } else {
                    console.error('Expected an array but got:', appointments);
                }
            },
            error: function(response) {
                console.log('Error fetching appointments:', response);
            }
        });


        // Handle form submission for adding new appointments
        $('#appointmentForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serializeArray();
                var selectedEmployees = [];
                $('input[name="employees[]"]:checked').each(function() {
                    selectedEmployees.push($(this).val());
                });
                formData.push({ name: 'employees', value: selectedEmployees });

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('appointments.store') }}",
                    data: formData,
                    success: function(response) {
                        $('#add_appointment').modal('hide');
                        calendar.addEvent({
                            id: response.id,
                            title: response.title,
                            start: response.start_date + 'T00:00:00',
                            end: response.end_date + 'T23:59:59',
                            backgroundColor: response.color ? response.color : '#3788d8',
                            borderColor: response.color ? response.color : '#3788d8',
                            description: response.description,
                            extendedProps: {
                                employees: response.employees
                            }
                        });
                        $('#appointmentForm')[0].reset();
                        toastr.success('Appointment created successfully!');
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            var overlappingEvent = response.responseJSON.overlapping_event;

                            // Check if there is an overlapping event in the error message
                            if (overlappingEvent) {
                                var errorMsg = `
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Datum</th>
                                                <th>Startzeit</th>
                                                <th>Endzeit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>${overlappingEvent.title}</td>
                                                <td>${overlappingEvent.start_date}</td>
                                                <td>${overlappingEvent.start_time}</td>
                                                <td>${overlappingEvent.end_time}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                `;

                                Swal.fire({
                                    title: 'Überlappender Fehler!',
                                    html: `
                                        <p>Wir haben bereits einen Termin in dieser ausgewählten Zeit:</p>
                                        ${errorMsg}
                                    `,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                // If it's a different validation error, show the regular errors
                                $.each(errors, function(key, value) {
                                    toastr.error(value); // Display the validation errors to the user
                                });
                            }
                        } else {
                            toastr.error('An error occurred while creating the appointment.');
                        }
                        console.log('Error:', response);
                    }
                });
            }); 

        // Update event after dragging, resizing, or editing
        function updateEvent(event) {
            var eventData = {
                id: event.id,
                title: event.title,
                description: event.extendedProps.description || null,
                priority: event.extendedProps.priority || null, 
                color: event.backgroundColor || null,
                start_date: event.start ? event.start.toISOString().slice(0, 10) : null,
                end_date: event.end ? event.end.toISOString().slice(0, 10) : event.start.toISOString().slice(0, 10),
                start_time: event.start ? event.start.toISOString().slice(11, 16) : '',  // Properly handle start time
                end_time: event.end ? event.end.toISOString().slice(11, 16) : '',  // Properly handle end time
                customer_id: event.extendedProps.customer_id || null,
                product_id: event.extendedProps.product_id || null,
                phases: event.extendedProps.phases || [],
                activities: event.extendedProps.activities || [],
                employees: event.extendedProps.employees || []
            };

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "PUT",
                url: `/appointments_update/${event.id}`,
                data: eventData,
                success: function(response) {
                    toastr.success('Event updated successfully.');
                },
                error: function(response) {
                    if (response.status === 422) {
                        const errors = response.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value);
                        });
                    } else {
                        toastr.error('Error updating event.');
                    }
                }
            });
        }
 
      // Handle edit modal for event click

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function handleEditModal(event) {
            let appointmentId = event.id;
            let customerId = event.extendedProps.customer_id;
            let productId = event.extendedProps.product_id; 
            let startDate = event.startStr.split('T')[0];
            let endDate = event.end ? event.endStr.split('T')[0] : startDate;

            // Extract time components and ensure fallback values if undefined
            let startTime = event.startStr.split('T')[1] ? event.startStr.split('T')[1].slice(0, 5) : '';
            let endTime = event.endStr ? (event.endStr.split('T')[1] ? event.endStr.split('T')[1].slice(0, 5) : '') : '';

            // Log these values to debug
            console.log("Start Date:", startDate);
            console.log("End Date:", endDate);
            console.log("Start Time:", startTime);
            console.log("End Time:", endTime);
            console.log("Event:", event);


            // Show the modal
            $('#edit_appointment').modal('show');

            // Fetch customer and product data
            $.ajax({
                url: `/customer_edit/${customerId}/${productId}`,
                type: 'GET',
                success: function(response) {
                    if (response) {
                        // Populate customer details in the form
                        $('#customer_id_edit').val(response.id);
                        $('#customer_name').val(response.name + ' ' + response.lastname);

                        // Fetch and load products based on the customer
                        loadProductsForCustomer(response.id, productId);
                    }
                },
                error: function(response) {
                    console.error('Error fetching customer data:', response);
                }
            });
 
            // Populate other fields in the form
            $('#title').val(event.title);
            $('#description').val(event.extendedProps.description);
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);  // Properly handle multi-day event
            $('#start_time').val(startTime);
            $('#end_time').val(endTime);
            $('#colorSelect').val(event.backgroundColor);


            // Populate phases, activities, and employees
            populatePhasesActivitiesEmployees(event.extendedProps.phases, event.extendedProps.activities, event.extendedProps.employees);

            // Save appointment ID in a hidden input field for form submission
            $('#edit_appointment_form').data('appointment-id', appointmentId);  // Store event ID for form submission
        }

        // Handle form submission for editing an appointment
        $('#edit_appointment_form').on('submit', function(e) {
            e.preventDefault();  // Prevent the default form submission

            let appointmentId = $(this).data('appointment-id');  // Get the event ID stored in the form
            let formData = {
                customer_id: $('#customer_id_edit').val(),
                product_id: $('#product_id_edit').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                calendar_color: $('#colorSelect').val(),
                _method: 'PUT',  // Simulate the PUT request for Laravel
            };

            console.log(formData);  // Log the form data to check if customer_id and product_id are populated

            // Make the AJAX request to update the appointment
            $.ajax({
                url: `/appointments_mini_update/${appointmentId}`,  // Endpoint from the route
                type: 'POST',  // Use POST, and include _method: 'PUT'
                data: formData,
                success: function(response) {
                    $('#edit_appointment').modal('hide');  // Close the modal
                    toastr.success('Event updated successfully.');

                    var event = calendar.getEventById(appointmentId);
                    event.setProp('title', response.title);
                    event.setExtendedProp('description', response.description);
                    event.setStart(response.start_date + 'T' + response.start_time);
                    event.setEnd(response.end_date + 'T' + response.end_time);
                    event.setProp('backgroundColor', response.calendar_color);
                    event.setProp('borderColor', response.calendar_color);
                },
               error: function(xhr) {
                    console.error(xhr.responseJSON);  // Log the error response to check validation messages

                    if (xhr.responseJSON && xhr.responseJSON.overlapping_event) {
                        let event = xhr.responseJSON.overlapping_event;

                        // Construct a message in a table format
                        let errorMsg = `
                            <table class="table">
                                <tr>
                                    <th>Title</th>
                                    <td>${event.title}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>${event.start_date}</td>
                                </tr>
                                <tr>
                                    <th>Start Time</th>
                                    <td>${event.start_time}</td>
                                </tr>
                                <tr>
                                    <th>End Time</th>
                                    <td>${event.end_time}</td>
                                </tr>
                            </table>
                        `;

                        // Display SweetAlert with the table
                        Swal.fire({
                            title: 'Überlappender Fehler!',
                            html: `
                                <p>Wir haben bereits einen Termin in dieser ausgewählten Zeit:</p>
                                ${errorMsg}
                            `,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        // Handle other validation errors
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = 'Error updating appointment:\n';
                        for (let field in errors) {
                            errorMsg += `${field}: ${errors[field].join(', ')}\n`;
                        }
                        toastr.error(errorMsg);
                    }
                }
            });
        });
 
        // Fetch products based on customer and load into product field
        function loadProductsForCustomer(customerId, selectedProductId) {
            $.ajax({
                url: `/edit_product/${customerId}/${selectedProductId}`,
                type: 'GET',
                success: function(response) {
                    if (response) {
                        // Populate product field with the product related to this customer
                        $('#product_id_edit').val(response.id);
                        $('#product_name').val(response.article_group);
                    }
                },
                error: function(response) {
                    console.error('Error fetching products for customer:', response);
                }
            });
        }

        // Populate phases, activities, and employees in the modal
        function populatePhasesActivitiesEmployees(phases, activities, employees) {
            $('#tasksTableEdit tbody').empty();

            // Iterate over each phase and populate related activities and employees
            phases.forEach((phase, index) => {
                let phaseId = phase.id;
                let phaseName = phase.phase_name;

                let activitiesHtml = '';
                let employeesHtml = '';

                activities.forEach(activity => {
                    if (activity.phase_id === phaseId) {
                        activitiesHtml += `<option value="${activity.id}" selected>${activity.title}</option>`;
                    }
                });

                employees.forEach(employee => {
                    employeesHtml += `<option value="${employee.id}" selected data-image="/images/employee/${employee.image}">
                                        ${employee.name} ${employee.lastname}
                                    </option>`;
                });

                $('#tasksTableEdit tbody').append(`
                    <tr>
                        <td>
                            <select name="edit[${index}][phase_id]" class="form-control select22 phase_id" style="width:100% !important;">
                                <option value="${phaseId}" selected>${phaseName}</option>
                            </select>
                        </td>
                        <td>
                            <select name="edit[${index}][activity_id][]" class="form-control select22 activity_id" style="width:100% !important;" multiple>
                                ${activitiesHtml}
                            </select>
                        </td>
                        <td>
                            <select name="edit[${index}][employee_id][]" class="form-control employee_id" style="width:100% !important;" multiple>
                                ${employeesHtml}
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-flat-danger mr-1 mb-1 waves-effect waves-light remove-task">
                                <i class="feather icon-minus"></i>
                            </button>
                        </td>
                    </tr>
                `);

                initializeSelect2($('#tasksTableEdit tbody tr:last').find('.select22'));
                initializeSelect2WithImages($('#tasksTableEdit tbody tr:last').find('.employee_id'));
            });
        }

        // Initialize Select2 for employees with images
        function initializeSelect2WithImages(selectElement) {
            selectElement.select2({
                templateResult: formatEmployeeOption,
                templateSelection: formatEmployeeOption,
                allowClear: true
            });
        }

        // Initialize Select2 for normal dropdowns
        function initializeSelect2(selectElement) {
            selectElement.select2({
                allowClear: true
            });
        }

        // Function to format the employee options with images in Select2
        function formatEmployeeOption(opt) {
            if (!opt.id) return opt.text;
            var optimage = $(opt.element).data('image');
            if (!optimage) return opt.text;
            return $(`<span><img src="${optimage}" class="img-circle" style="width: 20px; height: 20px; margin-right: 5px;" /> ${opt.text}</span>`);
        }

        // Truncate text to a specific number of words
        function truncateText(text, wordLimit) {
            const words = text.split(' ');
            if (words.length > wordLimit) {
                return words.slice(0, wordLimit).join(' ') + '...';
            }
            return text;
        } 
        // Render the calendar
        calendar.render();
    });
</script>
 
<!-- Load the customer:  -->

<script>
    $(document).ready(function() {
        // Initialize select2 on the customer select element
        $('#customer_id').select2({
            placeholder: "Select a customer",
            allowClear: true
        });

        // Fetch the customers via AJAX
        function loadCustomers() {
            $.ajax({
                url: "{{ route('appointment.customer.load') }}", // The route for getting customers
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Clear any existing options
                    $('#customer_id').empty().append('<option value=""></option>');

                    // Loop through the response data and append options to the select dropdown
                    $.each(response, function(index, customer) {
                        $('#customer_id').append(
                            `<option value="${customer.id}">
                                ${customer.title}.${customer.name} ${customer.lastname} - ${customer.city}
                             </option>`
                        );
                    });

                    // Refresh the select2 dropdown to reflect the new data
                    $('#customer_id').trigger('change');
                },
                error: function() {
                    alert('Error loading customer data.');
                }
            });
        }

        // Load the customers when the page loads
        loadCustomers();
    });
</script>
<script>
   $(document).ready(function() {
    // Initialize select2 for better UX
    $('.select22').select2({
        placeholder: "Select an option",
        allowClear: true
    });
 
     // Existing code to load products based on customer selection
    $('#customer_id').on('change', function() {
    var customerId = $(this).val(); // Get the selected customer's ID
    var productDropdown = $('#product_id'); // Product dropdown
  

    // Clear the current options in the product dropdown
    productDropdown.empty();
    productDropdown.append('<option value="">Please select a product</option>');

    if (customerId && customerId !== "") {
            // Send an AJAX request to fetch products
            $.ajax({
                url: '/productLoad/' + customerId, // Laravel route
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Products data received:', data); // Debugging line
                    // Populate the product dropdown with the returned data
                    $.each(data, function(key, value) {
                        productDropdown.append('<option value="' + value.id + '" data-product-id="'+value.id+'">' + value.article_group + '</option>');
                    });

                    
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', xhr.responseText, error);
                }
            });
        } else {
            console.error('No valid customer_id selected.');
        }
    });


    // Handle the change event on the product_id select
   $('#product_id').on('change', function() {
        var productId = $(this).val(); // Get the selected product's ID
        var selectProduct = $('#selectProduct'); // The hidden input for the Product ID
        console.log('Selected product_id:', productId); // Debugging line

        var phaseDropdown = $('#phase_id'); // Phase dropdown

        // Clear the current options in the phase dropdown
        phaseDropdown.empty();
        phaseDropdown.append('<option value=""></option>');

        if (productId && productId !== "") {
            // Send an AJAX request to load phases
            $.ajax({
                url: '/phaseLoad/' + productId, // Laravel route
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Phases data received:', data); // Debugging line
                    // Check if data is not empty
                    if (data.length > 0) {
                        // Populate the phase dropdown with the returned data
                    $.each(data, function(key, value) {
                            phaseDropdown.append('<option value="' + value.id + '" data-phase-id="'+value.id+'" data-product-id="' + value.product_id + '">' + value.phase_name + '</option>');
                        });

                        // Set the product ID in the hidden input
                        selectProduct.val(productId); // Set the selected product ID into the hidden input
                    } else {
                        console.log('No phases found for this product.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching phases:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            });
        } else {
            console.error('No valid product_id selected.');
        }
    });

    // Handle when a phase is selected
    $('#phase_id').on('change', function() {
        var phaseProductId = $(this).find(':selected').data('product-id'); // Get the product ID from the selected phase
        $('#selectProduct').val(phaseProductId); // Update the hidden input with the product ID
        console.log('Updated hidden input with product ID:', phaseProductId); // Debugging line
    });
    
    }); 
    </script>
 
<!-- Load Activity  -->
 <script>
    $(document).ready(function() {
    // Initialize select2 for better UX
    $('.select22').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Handle the change event on the phase_id select
    $('#phase_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var phaseId = selectedOption.data('phaseId'); // Access the data-phase-id attribute
        var productId = selectedOption.data('productId'); // Access the data-product-id attribute
        console.log('Selected phase_id:', phaseId); // Debugging line
        console.log('Selected product_id:', productId); // Debugging line
        var activityDropdown = $('.activity_id');

        // Clear the current options in the activity dropdown
        activityDropdown.empty();
        activityDropdown.append('<option value=""></option>');

        if (phaseId && productId) {
            // Send an AJAX request to load activities
            $.ajax({
                url: '/activityLoad/' + phaseId + '/' + productId, // Laravel route
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Populate the activity dropdown with the returned data
                    $.each(data, function(key, value) {
                        activityDropdown.append('<option value="' + value.id + '">' + value.title + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching activities:', error);
                }
            });
        }
    });
});

 </script>

 <script>
   document.addEventListener('DOMContentLoaded', function () {
    // Toggle dropdown functionality
    document.querySelectorAll('.menu-btn').forEach(function (menuBtn) {
        menuBtn.addEventListener('click', function (event) {
            // Toggle the corresponding dropdown menu
            let dropdownMenu = this.closest('.custom-event-header').nextElementSibling.nextElementSibling;
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';

            // Stop the click from propagating to the document click handler
            event.stopPropagation();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function () {
        document.querySelectorAll('.custom-dropdown-menu').forEach(function (dropdownMenu) {
            dropdownMenu.style.display = 'none';
        });
    });

    // Handle dropdown actions (Edit and Delete)
    document.querySelectorAll('.dropdown-item').forEach(function (item) {
        item.addEventListener('click', function () {
            let action = this.getAttribute('data-action');
            let eventId = this.closest('.fc-event').getAttribute('data-event-id');
            
            switch (action) {
                case 'edit':
                    // Redirect to the edit route
                    window.location.href = `/appointment_edit/${eventId}`;
                    break;
                case 'delete':
                    if (confirm('Are you sure you want to delete this event?')) {
                        let event = calendar.getEventById(eventId);
                        if (event) {
                            event.remove(); // Remove from calendar
                        }
                        $.ajax({
                            url: `/appointments/${eventId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function () {
                                alert('Event deleted successfully.');
                            },
                            error: function (xhr) {
                                alert('Failed to delete event.');
                            }
                        });
                    }
                    break;
            }
        });
    });
});

</script>

<!-- This script is for cloning the rows:  -->
 <script>
    $(document).ready(function() {
    var rowIndex = 1; // Start index at 1 because the first row is already there.

    // Function to initialize Select2 with a basic setup
    function initializeBasicSelect2(selectElement) {
        selectElement.select2({
            placeholder: "Select an option",
            allowClear: true
        });
    }

    // Function to initialize Select2 with image template for employee dropdown
    function initializeSelect2WithImages(selectElement) {
        selectElement.select2({
            templateResult: formatState,
            templateSelection: formatState,
            placeholder: "Select an option",
            allowClear: true
        });
    }

    // Function to format the employee dropdown options (for showing images)
    function formatState(opt) {
        if (!opt.id) {
            return opt.text;
        }
        var optimage = $(opt.element).data('image'); // Assuming the image is stored in the 'data-image' attribute
        if (!optimage) {
            return opt.text;
        }
        var $opt = $(
            '<span><img src="' + optimage + '" class="img-flag" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 5px;" /> ' + opt.text + '</span>'
        );
        return $opt;
    }

    // Function to load phases via AJAX
    function loadPhases(phaseDropdown) {
        var productId = $('#selectProduct').val();
        $.ajax({
            url: '/phaseLoad/' + productId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                phaseDropdown.empty();
                phaseDropdown.append('<option value="">Select Phase</option>');
                $.each(data, function(key, value) {
                    phaseDropdown.append('<option value="' + value.id + '" data-phase-id="'+ value.id +'" data-product-id="' + value.product_id + '">' + value.phase_name + '</option>');
                });
                initializeBasicSelect2(phaseDropdown); // Initialize Select2 for phase_id after loading options
            },
            error: function(xhr, status, error) {
                console.error('Error loading phases:', error);
            }
        });
    }

    // Function to load activities via AJAX based on phase_id and product_id
    function loadActivities(activityDropdown, phaseId, productId) {
        $.ajax({
            url: '/activityLoad/' + phaseId + '/' + productId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                activityDropdown.empty();
                $.each(data, function(key, value) {
                    activityDropdown.append('<option value="' + value.id + '">' + value.title + '</option>');
                });
                initializeBasicSelect2(activityDropdown); // Initialize Select2 for activity_id after loading options
            },
            error: function(xhr, status, error) {
                console.error('Error loading activities:', error);
            }
        });
    }

    // Attach event handlers for dynamic rows
    function bindPhaseChangeEvent(row) {
        row.find('.phase_id').on('change', function() {
            var selectedPhase = $(this).val();
            var productId = $(this).find('option:selected').data('product-id');
            var activityDropdown = row.find('.activity_id');
            if (selectedPhase && productId) {
                loadActivities(activityDropdown, selectedPhase, productId);
            }
        });
    }

    // Function to create a new row with unique names
    function addNewRow() {
         var newRow = `
        <tr>
            <td>
                <select name="active[` + rowIndex + `][phase_id]" class="form-control select22 phase_id" style="width:100% !important;">
                    <option value="">Select Phase</option>
                    <!-- Phases will be loaded here via AJAX -->
                </select>
            </td>
            <td>
                <select name="active[` + rowIndex + `][activity_id][]" class="form-control select22 activity_id" style="width:100% !important;" multiple>
                    <option value="">Select Activity</option>
                    <!-- Activities will be loaded here via AJAX -->
                </select>
            </td>
            <td>
                <select name="active[` + rowIndex + `][employee_id][]" class="form-control employee_id" style="width:100% !important;" multiple>
                    <!-- Employee options will be loaded here -->
                    @foreach ($employee as $emp)
                    <option value="{{$emp->id}}" data-image="{{ asset('images/employee/'.$emp->image)}}">
                        {{$emp->name}} {{ $emp->lastname }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-flat-danger mr-1 mb-1 waves-effect waves-light remove-task">
                    <i class="feather icon-minus"></i>
                </button>
            </td>
        </tr>
        `;

        // Append the new row to the table
        $('#tasksTable tbody').append(newRow);

        // Select the new row just added
        var $newRow = $('#tasksTable tbody tr:last');

        // Load phases for the new row's phase dropdown
        loadPhases($newRow.find('.phase_id'));

        // Reinitialize Select2 for all dropdowns in the new row
        initializeBasicSelect2($newRow.find('.phase_id')); // Initialize Select2 for phase_id
        initializeBasicSelect2($newRow.find('.activity_id')); // Initialize Select2 for activity_id
        initializeSelect2WithImages($newRow.find('.employee_id')); // Initialize Select2 for employee_id with image template

        // Bind change event for phase dropdown in the new row
        bindPhaseChangeEvent($newRow);

        rowIndex++; // Increment the index for the next row
    }

    // Initialize the first row with phases and employee select2
    loadPhases($('.phase_id'));
    initializeBasicSelect2($('.select22')); // Initialize all .select22 elements in the first row
    initializeSelect2WithImages($('.employee_id')); // Initialize employee dropdown in the first row

    // Add new row when clicking the "plus" button
    $(document).on('click', '.add-task', function() {
        if ($(this).find('i').hasClass('icon-plus')) {
            // Append a new row when the plus button is clicked
            addNewRow();
        } else {
            // Remove the row if the minus button is clicked
            $(this).closest('tr').remove();
        }
    });
});

 </script>

<script>
    $(document).ready(function() {
    function formatEmployee(option) {
        if (!option.id) {
            return option.text;
        }

        var imgSrc = $(option.element).data('image');
        if (imgSrc) {
            var $option = $(
                '<span><img src="' + imgSrc + '" class="img-circle" style="width: 30px; height: 30px; margin-right: 10px;" />' + option.text + '</span>'
            );
            return $option;
        } else {
            return option.text;
        }
    }

    // Initialize select2 with custom template
    $('.employee_id').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        placeholder: "Select an employee",
        allowClear: true,
        multiple: true
    });
});

</script>

 
<!-- Prority checkbox  -->
 <script>
    document.addEventListener("DOMContentLoaded", function() {
    var icon = document.getElementById('priorityIcon');
    var checkbox = document.getElementById('priorityCheckbox');

    icon.addEventListener('click', function() {
        // Toggle checkbox state
        checkbox.checked = !checkbox.checked;

        // Toggle icon's class to change color
        if (checkbox.checked) {
            icon.classList.add('checked');
        } else {
            icon.classList.remove('checked');
        }
    });
});

 </script>


<script>
  $(document).ready(function() {
    // Initialize select2
    $('.colorSelect').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        placeholder: "Farbe auswählen",
        allowClear: true
    });

    // Function to format options with colored icons
    function formatOption(state) {
        if (!state.id) {
            return state.text;
        }

        var color = $(state.element).data('color'); // Get color from the option's data-color attribute
        var $state = $(
            '<span><i class="feather icon-aperture" style="color:' + color + ';"></i> ' + state.text + '</span>'
        );

        return $state;
    }
});

</script>
 
<!-- SweetAlert and Google Maps API -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"></script>

<!-- Map container that will be shown in the SweetAlert modal -->
<div id="map" style="height: 400px; width: 100%; display: none;"></div>

<script>
    function showDirections(element) {
        const customerLat = parseFloat(element.getAttribute('data-latitude'));
        const customerLng = parseFloat(element.getAttribute('data-longitude'));

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                // Dynamically generate a unique map container id
                const mapId = `map-${Date.now()}`;

                // Show SweetAlert with the unique map container
                Swal.fire({
                    title: 'Wegbeschreibung Route',
                    html: `<div id="${mapId}" style="height: 400px; width: 100%;"></div>`,
                    width: '600px',
                    showCloseButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Absagen',
                    didOpen: () => {
                        // Ensure the map is initialized after the modal content is fully rendered
                        initMap(userLat, userLng, customerLat, customerLng, mapId);
                    }
                });
            }, function () {
                Swal.fire({
                    title: 'Error',
                    text: 'Unable to retrieve your location.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Geolocation is not supported by this browser.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    function initMap(userLat, userLng, customerLat, customerLng, mapId) {
        // Initialize the map in the dynamically created map container
        const map = new google.maps.Map(document.getElementById(mapId), {
            zoom: 7,
            center: { lat: userLat, lng: userLng }
        });

        // Set up the directions service and renderer
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();

        directionsRenderer.setMap(map);

        // Create the directions request object
        const request = {
            origin: { lat: userLat, lng: userLng },
            destination: { lat: customerLat, lng: customerLng },
            travelMode: 'DRIVING' // Can also use 'WALKING', 'BICYCLING', 'TRANSIT'
        };

        // Make the request to the Directions API
        directionsService.route(request, function (result, status) {
            if (status == 'OK') {
                directionsRenderer.setDirections(result);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Unable to retrieve directions.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>


    <!-- Quick lead popup --> 
<script>
    document.querySelector('#quicklead').addEventListener('click', function() {
        $('#add_appointment').modal('hide'); // Close the underlying modal

        Swal.fire({
            title: 'Quick Lead Form',
            html: `
                <div>
                    <form id="quickLeadForm">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <select class="form-control" id="title" name="title">
                                <option value="Frau">Frau</option>
                                <option value="Herr">Herr</option>
                                <option value="Dr.">Dr.</option>
                                <option value="Pro.">Pro.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="firma">Firma</label>
                            <input type="text" class="form-control" id="firma" name="firma">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Vorname</label>
                            <input type="text" class="form-control" id="lastname" name="lastname">
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="street">Straße / Nr.</label>
                            <input type="text" id="location-input" class="form-control" name="street">
                        </div>
                        <div class="form-group">
                            <label for="postcode">PLZ / Ort</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="postcode" name="postcode">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <div class="form-group">
                            <label for="telephone">Festnet</label>
                            <input type="text" class="form-control" id="telephone" name="telephone">
                        </div>
                        <div class="form-group">
                            <label for="phone">Mobile</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="product">Product</label>
                            <select id="product" name="product" class="form-control">
                                <option value="product1">Product 1</option>
                                <option value="product2">Product 2</option>
                            </select>
                        </div>
                    </form>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            focusConfirm: false, // Ensure it doesn't focus on the confirm button
            didOpen: () => {
                // Initialize Google Places Autocomplete after the modal is fully opened
                initGoogleAutocomplete(); 
            }
        }).then((result) => {
            // Reopen the underlying modal after SweetAlert is closed
            if (result.isConfirmed || result.isDismissed) {
                $('#add_appointment').modal('show');
            }
        });
    });

    // Initialize Google Places API Autocomplete
    function initGoogleAutocomplete() {
        var input = document.getElementById('location-input');
        if (!input) {
            console.error('Location input field not found.');
            return;
        }
        
        var autocomplete = new google.maps.places.Autocomplete(input);

        autocomplete.setFields(['address_component', 'geometry']);

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                console.error('No details available for input: ' + input.value);
                return;
            }

            // Get address components and update the corresponding fields
            var addressComponents = place.address_components;
            var postalCode = '';
            var city = '';

            addressComponents.forEach(function(component) {
                var componentType = component.types[0];
                if (componentType === 'postal_code') {
                    postalCode = component.long_name;
                } else if (componentType === 'locality') {
                    city = component.long_name;
                }
            });

            // Fill in the fields with the selected place's details
            document.getElementById('postcode').value = postalCode;
            document.getElementById('city').value = city;

            // Get latitude and longitude from the place geometry
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
</script>

@endsection
