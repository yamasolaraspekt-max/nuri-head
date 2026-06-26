@extends('admin.layouts.app')
@section('title') Test @endsection
@section('content')
    <style>
        .fc-daygrid-event {
            display: block;
            width: 100%;
            background-color: #f8f9fa;
            border-left: 4px solid #00aaff;
            padding: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
            transition: background-color 0.3s ease;
        }

        .fc-daygrid-event:hover {
            background-color: #e9ecef;
        }
          .fc-daygrid-event {
            white-space: normal !important;
            word-wrap: break-word !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .custom-event {
            display: flex;
            flex-direction: column;
            gap: 5px;
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
    </style>
    <div class="row"></div>
    <div class="container mt-4">
        <div class="row mt-3" style="margin-top:23rem !important">
            <div class="col-md-4">
                <div class="fc-daygrid-event-harness">
                    <a class="fc-daygrid-event fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-future">
                        <div class="custom-event">
                            
                            <div class="custom-event-header d-flex">
                                <div class="custom-event-status"> 
                                    <i class="feather icon-more-vertical menu"></i>
                                    <span class="custom-event-status-text">SESSION START</span> 
                                </div> 
                                <div class="dropdown-menu custom-dropdown-menu">
                                    <ul>
                                        <li class="dropdown-item">Finished</li>
                                        <li class="dropdown-item">More Information</li>
                                        <li class="dropdown-item">Delete</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="custom-event-title">
                                This is the title  
                            </div>
                            <div class="custom-event-product">
                                <span>Yama Nuri</span>
                                <span class="custom-event-product-status" style="text-align: center;">
                                    <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                          </li> 
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                            <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                        </li> 
                                    </ul>
                                </span>
                            </div>
                            
                            <div class="date d-flex">
                                <div class="custom-event-time mr-3">
                                    <i class="feather icon-calendar"></i> 2 Mar, 2024
                                </div>
                                <div class="custom-event-time">
                                    <i class="feather icon-clock"></i> 07:00 - 13:00
                                </div>
                            </div>
                            
                        </div>
                    </a>
                </div>
            </div>

             <div class="col-md-4">
                <div class="fc-daygrid-event-harness">
                    <a class="fc-daygrid-event fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-future">
                        <div class="custom-event">
                            
                            <div class="custom-event-header d-flex">
                                <div class="custom-event-status"> 
                                    <i class="feather icon-more-vertical menu"></i>
                                    <span class="custom-event-status-text">SESSION START</span> 
                                </div> 
                                <div class="dropdown-menu custom-dropdown-menu">
                                    <ul>
                                        <li class="dropdown-item">Finished</li>
                                        <li class="dropdown-item">More Information</li>
                                        <li class="dropdown-item">Delete</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="custom-event-title">
                                This is the title  
                            </div>
                            <div class="custom-event-product">
                                <span>Yama Nuri</span>
                                <span class="custom-event-product-status" style="text-align: center;">
                                    <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                         <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Julee Rossignol" class="avatar pull-up">
                                            <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                        </li>
                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Jeffrey Gerondale" class="avatar pull-up">
                                            <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" height="30" width="30">
                                        </li> 
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                            <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                        </li> 
                                    </ul>
                                </span>
                            </div>
                            
                            <div class="date d-flex">
                                <div class="custom-event-time mr-3">
                                    <i class="feather icon-calendar"></i> 2 Mar, 2024
                                </div>
                                <div class="custom-event-time">
                                    <i class="feather icon-clock"></i> 07:00 - 13:00
                                </div>
                            </div>
                            
                        </div>
                    </a>
                </div>
            </div>

             <div class="col-md-4">
                <div class="fc-daygrid-event-harness">
                    <a class="fc-daygrid-event fc-daygrid-dot-event fc-event fc-event-draggable fc-event-resizable fc-event-start fc-event-end fc-event-future">
                        <div class="custom-event">
                            
                            <div class="custom-event-header d-flex">
                                <div class="custom-event-status"> 
                                    <i class="feather icon-more-vertical menu"></i>
                                    <span class="custom-event-status-text">SESSION START</span> 
                                </div> 
                                <div class="dropdown-menu custom-dropdown-menu">
                                    <ul>
                                        <li class="dropdown-item">Finished</li>
                                        <li class="dropdown-item">More Information</li>
                                        <li class="dropdown-item">Delete</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="custom-event-title">
                                This is the title  
                            </div>
                            <div class="custom-event-product">
                                <span>Yama Nuri
                                        <div class="badge badge-pill badge-light-warning mr-1 mb-1" id="importantTask"> 
                                            <i class="fa fa-bell important"  id="bellIcon"></i> Important Task
                                        </div>
                                </span> 
                            
                                <span class="custom-event-product-status" style="text-align: center;">
                                    <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                          </li> 
                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                            <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                        </li> 
                                    </ul>
                                </span>
                            </div>
                            
                            <div class="date d-flex">
                                <div class="custom-event-time mr-3">
                                    <i class="feather icon-calendar"></i> 2 Mar, 2024
                                </div>
                                <div class="custom-event-time">
                                    <i class="feather icon-clock"></i> 07:00 - 13:00
                                </div>
                            </div>
                            
                        </div>
                    </a>
                </div>
            </div>


        </div>
    </div> 
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle dropdown functionality
        document.querySelectorAll('.menu').forEach(function(menuIcon) {
            menuIcon.addEventListener('click', function(event) {
                // Toggle the corresponding dropdown menu
                let dropdownMenu = this.parentElement.nextElementSibling;
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';

                // Stop the click from propagating to the document click handler
                event.stopPropagation();
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            document.querySelectorAll('.custom-dropdown-menu').forEach(function(dropdownMenu) {
                if (!dropdownMenu.contains(event.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        });

        // Example functions for dropdown items (add actions here)
        document.querySelectorAll('.dropdown-item').forEach(function(item) {
            item.addEventListener('click', function() {
                let action = this.textContent.trim();
                switch (action) {
                    case 'Finished':
                        alert('Event marked as finished.');
                        break;
                    case 'More Information':
                        alert('Showing more information.');
                        break;
                    case 'Delete':
                        alert('Event deleted.');
                        break;
                }
            });
        });
    });
</script>
@endsection
