@push('style')
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

@endpush
<section>
    <div class="col-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                         <div class="sidebar">  
                            <div class="todo-app-menu"> 
                                <div class="sidebar-menu-list"> 
                                    <hr>
                                    <h5 class="mt-2 mb-1 pt-25">Filters</h5>
                                    <div class="list-group list-group-filters font-medium-1">
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-star mr-50"></i> TODAY</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-users mr-50"></i> MONTH</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-briefcase mr-50"></i> YEAR</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-user mr-50"></i> Persönliche Aufgabe</a>
                                    </div>
                                    <hr>
                                    <h5 class="mt-2 mb-1 pt-25">Labels</h5>
                                    <div class="list-group list-group-labels font-medium-1">
                                        <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-primary mr-1"></span> Frontend</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-warning mr-1"></span> Backend</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-success mr-1"></span> Doc</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0 d-flex align-items-center"><span class="bullet bullet-danger mr-1"></span> Bug</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div id='calendar'></div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@push('scripts')

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
                                        <div class="custom-event-status">
                                            <span class="custom-event-status-text" style="white-space: normal; word-wrap: break-word;"> ${truncateText(arg.event.title || '', 4)}
                                            </span>
                                        </div> 
                                       
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
 
    
@endpush