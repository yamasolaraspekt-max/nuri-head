@extends('admin.layouts.app')
@section('title') Contract Type @stop
@section('style')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />  
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">  
<meta name="csrf-token" content="{{ csrf_token() }}">

<style> 
 
        #deadline_area, .end_time_area, .repeated_area, .reminder_area ,.add_calendar_area{
            display: none;
        }
 
        
        .cards {
            background-color: white; 
            padding: 20px;
            border: 1px solid #ddd; 
            text-align: center; 
        }

        .cards h3 {
            margin: 0;
            font-size: 16px;
            color: #a1a1a1;
            font-weight: bold;
        }

        .cards img {
          width: 89px;
            padding: 0px;
            margin: 0px;
        }

        .cards hr {
           margin-top: -1px;
        margin-bottom: 19px;
            border: none;
            background-color: #a1a1a1 !important
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
            background-color: #91c353 !important; /* Blue background */
            
            color: white !important; /* White text */
        }

        /* Capitalize only the first word in the title */
        .fc-toolbar-title {
            text-transform: capitalize;
        }
        .fc-scrollgrid-section td {
            background:transparent !important;
        }

           /* Highlight Today's Date */
        .fc-daygrid-day.fc-today .fc-daygrid-day-top {
            background-color: #ffcc00 !important; /* Yellow background for today */
            border-radius: 50%; /* Circle highlight */
            color: black !important; /* Black text */
            font-weight: bold;
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

         /* events  */
          .card-event {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }

        .time {
            margin-right: 15px;
            text-align: right;
            color: #757575;
        }

        .time .date {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .time .hour {
            font-size: 18px;
            font-weight: bold;
        }

        .separator {
            width: 4px;
            height: 50px;
            background-color: #ffcc80;
            margin-right: 15px;
        }

        .details {
            flex: 1;
                text-align: left;
        }

        .details .title {
            font-size: 0.9rem;
            font-weight: bold;
            color: #757575;
            margin-bottom: 5px;
        }

        .details .description {
            font-size: 16px;
            color: #333;
        }

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
            position: sticky; /* Stick the title to the top */
            top: 0;
            z-index: 10; /* Ensure it stays above scrollable content */
            background-color: #fff; /* Match background color of the card */
            padding: 10px;
            border-bottom: 1px solid #ddd; /* Optional: Add a subtle bottom border */
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
            padding: 15px; /* Adjust padding for better readability */
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

        #note_table tr td {
            padding:0 !important;
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
                    <h2 class="content-header-title float-left mb-0">PERSÖNLICHE TO-DOS</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        </ol>
                    </div>
                </div>
            </div>
                          
            <div class="content-body">
                <div class="row mt-6" style="margin-top:100px;">
                    <div class="container d-flex"> 
                        <div class="col-9">
                            <form id="search_form" action="javascript:void(0);">
                                    <fieldset>
                                        <div class="input-group">
                                            <input type="text" name="search" id="search_input" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                            <div class="input-group-append" id="button-addon2">
                                                <button class="btn btn-primary waves-effect waves-light" type="submit" id="search_button">Go</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>

                        </div>
                        <div class="col-3 d-flex">
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light"  data-toggle="modal" data-target="#newNote"  ><i class="feather icon-plus"></i></button>
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light category_search"  data-toggle="modal" data-target="#warning"     ><i class="feather icon-slack"  data-toggle="tooltip" data-placement="top"  data-original-title="Suche nach Kategorie"></i></button>  
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light trash_box" data-toggle="tooltip" data-placement="top"  data-original-title="Papierkorb"   ><i class="feather icon-trash"></i></button> 
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
                                    <form method="post"  id="save_note_form">
                                        @csrf
                                        <input type="hidden" name="check_availability"  id="check_availability" value="false" >
                                        <div class="modal-body">  
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                                <input type="hidden" name="color" id="color" value="#8fc73e">
                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1" id="color_drop_down">
                                                                    <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split waves-effect waves-light"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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

                        <div class="modal fade text-left" id="warning" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning white">
                                        <h5 class="modal-title" id="myModalLabel140">Suche nach Kategorie</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form id="search_category">
                                        @csrf
                                    <div class="modal-body">
                                         <label for="">Kategorie wählen</label>
                                         <select name="category_id" id="" class="form-control select-category" style="width:100%">
                                            @foreach ($category as $cat)
                                                <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                            @endforeach
                                         </select>
                                    </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Abbrechen</button> 
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                       <div class="card"> 
                            {{-- Tabs + Filter --}}
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <ul class="nav nav-pills" id="noteStatusTabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#" data-status="open">
                                            Offen
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-status="done">
                                            Erledigt
                                        </a>
                                    </li>
                                </ul>

                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="noteFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="noteFilterDropdown">
                                        <a class="dropdown-item note-filter" href="#" data-filter="date">Datum (neueste zuerst)</a>
                                        <a class="dropdown-item note-filter" href="#" data-filter="sort">Manuelle Reihenfolge</a>
                                        <a class="dropdown-item note-filter" href="#" data-filter="calendar">Nur mit Kalender</a>
                                        <a class="dropdown-item note-filter" href="#" data-filter="reminder">Nur mit Erinnerung</a>
                                        <a class="dropdown-item note-filter" href="#" data-filter="repeat">Nur wiederholende</a>
                                    </div>
                                </div>
                            </div>
 
                            <div class="table-responsive">
                                <table class="table" id="note_table"> 
                                    <thead>
                                        <tr> 
                                            <th>#</th>
                                            <th>Erledigt</th>
                                            <th>Titel</th>
                                            <th>Beschreibung</th>
                                            <th>Frist</th>
                                            <th>Abgeschlossen am</th>
                                            <th>Status</th>
                                            <th>Endzeit</th>
                                            <th>Kategorie</th>
                                            <th>Erinnerung</th>
                                            <th>Wiederholen</th>
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Rows will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </div>
                       </div> 
                    </div>
                </div> 
                    <!-- Modals :start -->
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
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg " role="document">
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
                                            
                                           <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="title"> 
                                                <td>Titel</td>
                                                <td style="text-align:right"  > 
                                                        <input type='input' class="form-control pickatime" name="task_title" /> 
                                                    </div>
                                                </td>
                                            </tr> 

                                             <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="title"> 
                                                <td>Beschreibung</td>
                                                <td style="text-align:right"  > 
                                                        <textarea name="note" id="note" class="form-control"></textarea>
                                                    </div>
                                                </td>
                                            </tr>

                                             <tr style="background: #f7f7f7a8;border-bottom: 6px solid white;" id="title"> 
                                                <td>Kategorie</td>
                                                <td style="text-align:right"  > 
                                                        <select class="form-control category_id" id="category_id" name="category_id">
                                                                @foreach ($category as $cat)
                                                                    <option value="{{$cat->id}}">{{$cat->category_name}}</option>
                                                                @endforeach
                                                            </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                              
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
                    <!-- Modals :end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
     <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
     <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://unpkg.com/feather-icons"></script>
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif 
        
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
    

</script>

<script>
    $('.select-category').select2();
</script>




<!-- Deadline Script Toggle: start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
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
        deadlineButton.addEventListener('change', function () {
            if (this.checked) {
                deadlineArea.style.display = 'table-row';
            } else {
                deadlineArea.style.display = 'none';
            }
        });

        // Toggle end time area
        endTimeButton.addEventListener('change', function () {
            if (this.checked) {
                endTimeArea.style.display = 'table-row';
            } else {
                endTimeArea.style.display = 'none';
            }
        });

        // Toggle repeated area
        repeatedButton.addEventListener('change', function () {
            if (this.checked) {
                repeatedArea.style.display = 'table-row';
            } else {
                repeatedArea.style.display = 'none';
            }
        });

        // Toggle reminder area
        reminderButton.addEventListener('change', function () {
            if (this.checked) {
                reminderArea.style.display = 'table-row';
            } else {
                reminderArea.style.display = 'none';
            }
        });

        // Toggle add calendar area
        addCalendarButton.addEventListener('change', function () {
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


<!-- Deadline Script Toggle: end  -->


<!-- Note Category Operations: start -->
 <script>
    document.addEventListener('DOMContentLoaded', function () {
    const newNoteModal = $('#newNote'); // New Note modal
    const categoryModal = $('#categoryModal'); // Category modal
    const categorySelect = $('#category_id'); // Category dropdown
    const checkAvailabilityInput = $('#check_availability'); // The hidden input

    // Function to load categories into the select dropdown
    function loadCategories() {
        $.ajax({
            url: "{{ route('note.category.get') }}",
            method: "GET",
            success: function (data) {
                // Clear existing options and add the default option
                categorySelect.empty();
                categorySelect.append('<option value="">Wählen Sie eine Kategorie</option>');
                // Populate with categories
                data.forEach(category => {
                    categorySelect.append(`<option value="${category.id}">${category.category_name}</option>`);
                });
            },
            error: function () {
                Swal.fire('Fehler', 'Kategorien konnten nicht geladen werden. Bitte versuchen Sie es erneut.', 'error');
            }
        });
    }

    // Handle the add_category button click
    $(document).on('click', '.add_category', function () {
        // Hide the newNote modal
        newNoteModal.modal('hide');
        // Show the category modal
        categoryModal.modal('show');
    });

    // Handle the close event of the category modal
    categoryModal.on('hidden.bs.modal', function () {
        // Show the newNote modal
        newNoteModal.modal('show');
    });

    // Save category
    $('#saveCategory').on('click', function () {
        console.log('Save Category button clicked!'); // Debugging point

        const categoryName = $('#category_name').val();
        const type = $('#type').val();
        const color = $('#color').val();

        console.log('Form Values:', { categoryName, type, color }); // Debugging point

        // Validate fields
        if (!categoryName || !type || !color) {
            console.warn('Validation failed:', { categoryName, type, color }); // Debugging point
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
            success: function (response) {
                console.log('Category Save Response:', response); // Debugging point
                if (response.status === 'success') {
                    Swal.fire('Erfolg', response.message, 'success');
                    categoryModal.modal('hide'); // Close modal
                    loadCategories(); // Reload categories
                } else {
                    Swal.fire('Fehler', 'Etwas ist schief gelaufen.', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error saving category:', xhr.responseJSON || error); // Debugging point
                Swal.fire('Fehler', 'Kategorie konnte nicht gespeichert werden. Bitte versuchen Sie es erneut.', 'error');
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
    document.addEventListener('DOMContentLoaded', function () {
    // Priority Dropdown
    const priorityInput = document.querySelector('input[name="priority"]'); // Hidden input field for priority
    const priorityDropdownItems = document.querySelectorAll('.btn-group .dropdown-menu .dropdown-item[data-value][data-priority]'); // Priority dropdown items

    priorityDropdownItems.forEach(item => {
        item.addEventListener('click', function () {
            const selectedPriority = this.getAttribute('data-value'); // Get the data-value for priority
            priorityInput.value = selectedPriority; // Update the hidden input value for priority
            console.log(`Priority set to: ${selectedPriority}`); // Debugging log
        });
    });

    // Color Dropdown
    const colorInput = document.getElementById('color'); // Hidden input field for color
    const colorIcon = document.getElementById('colorIcon'); // Icon to change color
    const colorDropdownItems = document.querySelectorAll('.btn-group .dropdown-menu .dropdown-item[data-value][data-color]'); // Color dropdown items

    colorDropdownItems.forEach(item => {
        item.addEventListener('click', function () {
            const selectedColor = this.getAttribute('data-value'); // Get the data-value for color
            colorInput.value = selectedColor; // Update the hidden input value for color
            colorIcon.style.color = selectedColor; // Change icon color
            console.log(`Color selected: ${selectedColor}`); // Debugging log
        });
    });
});

   </script>

    <!-- Priority Script end  -->

<!-- Note operation:start  -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const noteList = $('#personal-note-list'); // Note list container
        const saveNoteButton = $('#save_note_button'); // Save note button
        const saveNoteModal = $('#newNote'); // Save note modal
        const saveNoteForm = $('#save_note_form'); // Save note form
         const updateCategoryModal = $('#updateCategoryModal'); // Reference to the modal
        const categorySelect = $('#update_category_id'); // Select dropdown inside the modal
        
        const repeatTranslations = {
            "": "Häufigkeit auswählen",
            "minute": "Minütlich",
            "hourly": "Stündlich",
            "daily": "Täglich",
            "weekly": "Wöchentlich",
            "monthly": "Monatlich",
            "quarterly": "Vierteljährlich",
            "yearly": "Jährlich"
        };

        let currentStatus = 'open';      // 'open' or 'done'
        let currentFilter = null; 
        // Function to load notes
        function loadNotes(options = {}) {
            // merge options into globals
            if (options.status) {
                currentStatus = options.status;
            }
            if (typeof options.filter !== 'undefined') {
                currentFilter = options.filter;
            }

            const status = currentStatus;
            const filter = currentFilter;

            const noteTableBody = $('#note_table tbody');
            noteTableBody.empty();

            // decide which endpoint to call
            let url;
            let data = { status: status };

            if (filter) {
                url = "{{ route('notes.filter') }}";     // uses filter() controller
                data.filter = filter;
            } else {
                url = "{{ route('notes.lists') }}";      // uses index() controller
            }

            $.ajax({
                url: url,
                method: "GET",
                data: data,
                success: function (response) {
                    const repeatTranslations = {
                        "": "Häufigkeit auswählen",
                        "minute": "Minütlich",
                        "hourly": "Stündlich",
                        "daily": "Täglich",
                        "weekly": "Wöchentlich",
                        "monthly": "Monatlich",
                        "quarterly": "Vierteljährlich",
                        "yearly": "Jährlich"
                    };

                    noteTableBody.empty();

                    response.notes.forEach((note, index) => {
                        noteTableBody.append(`
                            <tr data-id="${note.id}" style="cursor: grab; border-bottom: 10px solid #f8f8f8; border-left: 10px solid ${note.color}; background:white;">
                                <td style="text-align: center;">${index + 1}</td>
                                <td>
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
                                </td>
                                <td class="title-field ${note.is_done == 1 ? 'complete' : ''}" data-id="${note.id}" data-field="title">${note.title}</td>
                                <td class="note-field" data-id="${note.id}" data-field="note">${note.note}</td>
                                <td class="change-date" data-id="${note.id}">
                                    ${note.deadline ? 
                                        new Date(note.deadline).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                        'Kein Fälligkeitsdatum'}
                                </td>
                                <td>
                                    ${note.done_date ? 
                                        new Date(note.done_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                        'Nicht erledigt'}
                                </td>
                                <td>
                                    ${note.done_date ? 
                                        (note.deadline && new Date(note.done_date) > new Date(note.deadline) ? 
                                            '<div class="badge badge-danger">Verspätet</div>' :
                                            (note.deadline && new Date(note.done_date) < new Date(note.deadline) ? 
                                                '<div class="badge badge-success">Vorzeitig</div>' : 
                                                '<div class="badge badge-primary">Pünktlich</div>')
                                        ) : 
                                        '-'}
                                </td>
                                <td class="change-time" data-id="${note.id}">${note.end_time || 'Keine Endzeit'}</td>
                                <td class="updateCategoryModal" data-id="${note.id}">${note.category_name || 'Standard'}</td>
                                <td>
                                    ${note.reminder_date || note.reminder_time ? `
                                        <small class="no-reminder-icon" data-id="${note.id}" data-toggle="tooltip" title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                            <i class="feather icon-bell primary"></i> 
                                            ${note.reminder_date ? 
                                                new Date(note.reminder_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                                ''} 
                                            ${note.reminder_time || ''}
                                        </small>` : '-'}
                                </td>
                                <td>
                                    ${note.repeat ? `
                                        <small class="no-repeat-icon" data-id="${note.id}">
                                            <i class="feather icon-refresh-ccw primary"></i> ${repeatTranslations[note.repeat] || note.repeat} 
                                        </small>` : '-'}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete_note" data-id="${note.id}">
                                        <i class="feather icon-trash"></i>  
                                    </button>
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light note-color" data-id="${note.id}">
                                        <i class="feather icon-aperture" ></i> 
                                    </button> 
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light note-settings" data-id="${note.id}">
                                        <i class="feather icon-edit"></i> 
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    // Reinitialize drag & drop
                    initializeSortable();
                },
                error: function () {
                    Swal.fire('Fehler', 'Notizen konnten nicht geladen werden. Bitte versuche es erneut.', 'error');
                }
            });
        }

        // Status Tabs: Offen / Erledigt
        $(document).on('click', '#noteStatusTabs .nav-link', function (e) {
            e.preventDefault();

            $('#noteStatusTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            const status = $(this).data('status'); // 'open' or 'done'
            loadNotes({ status: status });         // keep last filter, just change status
        });

        // Filter dropdown (date, sort, calendar, reminder, repeat)
        $(document).on('click', '.note-filter', function (e) {
            e.preventDefault();
            const filter = $(this).data('filter'); // 'date', 'sort', 'calendar', 'reminder', 'repeat'
            loadNotes({ filter: filter });         // apply filter for current tab
        });


 
        $(document).on('click', '.updateCategoryModal', function () {
            const noteId = $(this).data('id');
            const categoryId = $(this).data('category-id');

            // Fetch categories and pre-select the current one
            $.ajax({
                url: `{{ url('/fetch_note_category') }}/${noteId}/${categoryId}`,
                method: "GET",
                success: function (response) {
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
                error: function () {
                    Swal.fire('Fehler', 'Kategorien konnten nicht geladen werden.', 'error');
                }
            });
        });

        // Handle category update
        $('#update_category').on('click', function () {
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
                success: function (response) {
                    Swal.fire('Erfolgreich', 'Die Kategorie wurde aktualisiert.', 'success');
                    updateCategoryModal.modal('hide'); // Hide the modal
                    loadNotes(); // Reload the notes
                },
                error: function () {
                    Swal.fire('Fehler', 'Die Kategorie konnte nicht aktualisiert werden.', 'error');
                }
            });
        });
      
        $(document).on('click', '.no-repeat-icon', function () {
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
                        success: function () {
                            Swal.fire('Erfolgreich!', 'Die Wiederholung wurde entfernt.', 'success');
                            loadNotes(); // Reload notes
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Die Wiederholung konnte nicht entfernt werden.', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.no-reminder-icon', function () {
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
                    success: function () {
                        Swal.fire('Erfolgreich!', 'Die Erinnerungsoption wurde entfernt.', 'success');
                        loadNotes(); // Reload notes
                    },
                    error: function () {
                        Swal.fire('Fehler', 'Die Erinnerungsoption konnte nicht entfernt werden.', 'error');
                    }
                });
            }
        });
    });


    // Function to toggle "done" status
     $(document).on('change', '.done-checkbox', function () {
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
            success: function (response) {
                console.log('Note updated successfully:', response);

                // Toggle the "complete" class on the <td> element with .title-field
                const titleField = $(`.title-field[data-id="${noteId}"]`);
                if (isDone) {
                    titleField.addClass('complete');
                } else {
                    titleField.removeClass('complete');
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Status aktualisiert',
                    text: `Die Aufgabe wurde ${isDone ? 'als erledigt' : 'als unerledigt'} markiert.`,
                });

                // Reload notes after successful update
                loadNotes();
            },
            error: function (xhr, status, error) {
                console.error('Error updating note:', { xhr, status, error });
                Swal.fire('Fehler', 'Der Status konnte nicht aktualisiert werden. Bitte versuche es erneut.', 'error');
            }
        });
    });



    // Double-click functionality for title and note
    $(document).on('dblclick', '.title-field, .note-field', function () {
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
        input.on('blur keydown', function (e) {
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
                    url: field === 'title' 
                        ? `{{ url('/notes_update_name') }}/${id}` 
                        : `{{ url('/notes_update_note') }}/${id}`,
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        [field]: newValue // Correctly set the field name dynamically
                    },
                    success: function () {
                        $element.text(newValue);
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Erfolgreich', 'Die Notiz wurde aktualisiert.', 'success');
                    },
                    error: function () {
                        input.replaceWith($element);
                        badge.hide();
                        Swal.fire('Fehler', 'Die Notiz konnte nicht aktualisiert werden.', 'error');
                    }
                });
            }
        });
    });

        // Save new note
      saveNoteButton.on('click', function () {
            const formData = saveNoteForm.serialize();

            // Debug: Log the value of check_availability
            console.log('check_availability value:', $('#check_availability').val());

            $.ajax({
                url: "{{ route('notes.store') }}",
                method: "POST",
                data: formData,
                success: function (response) {
                    Swal.fire('Erfolgreich', 'Die Notiz wurde gespeichert.', 'success');
                    saveNoteModal.modal('hide');
                    saveNoteForm[0].reset(); // Reset the form
                    $('#check_availability').val('false'); // Reset check_availability to false
                    loadNotes(); // Reload notes
                },
                error: function (xhr) {
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
                                console.log('Value after Save Anyway:', $('#check_availability').val()); // Debugging

                                // Resubmit the form with check_availability = true
                                saveNoteButton.trigger('click');
                            }
                        });
                    } else {
                        // Handle validation or other errors
                        const errorMessage = Object.values(xhr.responseJSON.errors || {}).join('<br>') || 'Die Notiz konnte nicht gespeichert werden.';
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
                    toastr.error('Fehler beim Aktualisieren des Fälligkeitsdatums. Bitte versuchen Sie es erneut.', 'Fehler');
                }
            });
        }

        /**
         * Sendet eine AJAX-Anfrage, um die Endzeit einer Notiz zu aktualisieren.
         * @param {number} noteId - Die ID der Notiz.
         * @param {string} newTime - Die neue Endzeit im Format HH:MM.
         */
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
                    toastr.error('Fehler beim Aktualisieren der Endzeit. Bitte versuchen Sie es erneut.', 'Fehler');
                }
            });
        }
     
            
         


             // Handle note-color click
                 $(document).on('click', '.note-color', function () {
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
                            $('.color-btn').on('click', function () {
                                const selectedColor = $(this).data('color'); // Get the selected color

                                // Send the selected color to the server
                                $.ajax({
                                    url: `{{ url('/note_change_color') }}/${noteId}`,
                                    method: 'PUT',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        color: selectedColor
                                    },
                                    success: function (response) {
                                        Swal.fire('Erfolgreich', 'Die Farbe wurde aktualisiert.', 'success');
                                        loadNotes(); // Reload notes to reflect the color change
                                    },
                                    error: function () {
                                        Swal.fire('Fehler', 'Die Farbe konnte nicht aktualisiert werden.', 'error');
                                    }
                                });
                            });
                        }
                    });
                });



                  // Handle note-color click
                $(document).on('click', '.change-date', function () {
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
                                success: function (response) {
                                    Swal.fire('Erfolgreich', 'Das Datum wurde geändert.', 'success');
                                    loadNotes(); // Reload notes to reflect the date change
                                },
                                error: function () {
                                    Swal.fire('Fehler', 'Das Datum konnte nicht geändert werden.', 'error');
                                }
                            });
                        }
                    });
                });

                // Handling the time 
                $(document).on('click', '.change-time', function () {
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
                                Swal.showValidationMessage('Bitte wählen Sie eine gültige Uhrzeit aus.');
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
                                success: function (response) {
                                    Swal.fire('Erfolgreich', 'Die Uhrzeit wurde geändert.', 'success');
                                    loadNotes(); // Reload notes to reflect the time change
                                },
                                error: function () {
                                    Swal.fire('Fehler', 'Die Uhrzeit konnte nicht geändert werden.', 'error');
                                }
                            });
                        }
                    });
                });



        // Delete note functionality
        $(document).on('click', '.delete_note', function () {
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
                        success: function () {
                            Swal.fire('Gelöscht!', 'Die Notiz wurde gelöscht.', 'success');
                            loadNotes(); // Reload notes
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Die Notiz konnte nicht gelöscht werden.', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.trash_box', function () {
            // Fetch trashed notes
            $.ajax({
                url: "{{ route('notes.trash') }}",
                method: "GET",
                success: function (response) {
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
                error: function () {
                    Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
                }
            });
        });

        // Handle permanent delete
        $(document).on('click', '.permanent-delete', function () {
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
                        success: function () {
                            Swal.fire('Erfolgreich!', 'Die Notiz wurde dauerhaft gelöscht.', 'success');
                            $(`tr[data-id="${noteId}"]`).remove(); // Remove the note row from the table
                            loadNotes(); // Reload notes to reflect changes
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Die Notiz konnte nicht gelöscht werden.', 'error');
                        }
                    });
                }
            });
        });

        // Handle recover note
        $(document).on('click', '.recover-note', function () {
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
                        success: function () {
                            Swal.fire('Erfolgreich!', 'Die Notiz wurde wiederhergestellt.', 'success');
                            $(`tr[data-id="${noteId}"]`).remove(); // Remove the note row from the table
                            loadNotes(); // Reload notes to reflect changes
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Die Notiz konnte nicht wiederhergestellt werden.', 'error');
                        }
                    });
                }
            });
        });


       $(document).on('click', '.note-settings', function () {
            const noteId = $(this).data('id'); // Get the note ID from the clicked button

            // Fetch the note data
            $.ajax({
                url: `{{ url('/notes') }}/${noteId}`, // Ensure noteId is passed correctly
                method: "GET",
                success: function (response) {
                    const note = response.note;

                    // Populate the modal fields
                    $('#updateSettingModal input[name="task_title"]').val(note.title); // Current title
                    $('#updateSettingModal textarea[name="note"]').val(note.note); // Current note description
                    $('#updateSettingModal input[name="deadline"]').val(note.deadline);
                    $('#updateSettingModal input[name="end_time"]').val(note.end_time);
                    $('#updateSettingModal input[name="add_calendar_date"]').val(note.add_calendar_date);
                    $('#updateSettingModal select[name="repeat"]').val(note.repeat);
                    $('#updateSettingModal input[name="reminder_date"]').val(note.reminder_date);
                    $('#updateSettingModal input[name="reminder_time"]').val(note.reminder_time);
                    $('#updateSettingModal input[name="priority"]').val(note.priority);

                    // Open the modal
                    $('#updateSettingModal').modal('show');
                },
                error: function (xhr) {
                    console.error(xhr.responseText); // Log the actual error for debugging
                    Swal.fire('Fehler', 'Die Notizdaten konnten nicht geladen werden.', 'error');
                }
            });
        });

       $('#save_note_settings').on('click', function () {
        const formData = $('#updateSettingForm').serialize(); // Serialize the modal form data
        const noteId = $('.note-settings').data('id'); // Get the note ID

        $.ajax({
            url: `{{ url('/notes_update_settings') }}/${noteId}`, // Replace with your update endpoint
            method: "PUT",
            data: {
                _token: "{{ csrf_token() }}", // Add CSRF token
                deadline: $('#updateSettingForm input[name="deadline"]').val(),
                end_time: $('#updateSettingForm input[name="end_time"]').val(),
                add_calendar_date: $('#updateSettingForm input[name="add_calendar_date"]').val(),
                repeat: $('#updateSettingForm select[name="repeat"]').val(),
                reminder_date: $('#updateSettingForm input[name="reminder_date"]').val(),
                reminder_time: $('#updateSettingForm input[name="reminder_time"]').val(),
                priority: $('#updateSettingForm input[name="priority"]').val(),
                check_emp: $('#updateSettingForm input[name="check_emp"]').val(),
                task_title: $('#updateSettingForm input[name="task_title"]').val(), // Title field
                note: $('#updateSettingForm textarea[name="note"]').val(), // Note/description field
                category_id: $('#updateSettingForm select[name="category_id"]').val() // Category ID
            },
            success: function (response) {
                Swal.fire('Erfolgreich', response.message, 'success');
                $('#updateSettingModal').modal('hide'); // Close the modal
                loadNotes(); // Reload notes
            },
            error: function (xhr) {
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
                                success: function (response) {
                                    Swal.fire('Erfolgreich', response.message, 'success');
                                    $('#updateSettingModal').modal('hide'); // Close modal
                                    loadNotes(); // Reload notes
                                },
                                error: function () {
                                    Swal.fire('Fehler', 'Die Einstellungen konnten nicht aktualisiert werden.', 'error');
                                }
                            });
                        }
                    });
                } else {
                    const errorMessage = Object.values(xhr.responseJSON.errors || {}).join('<br>') || 'Die Einstellungen konnten nicht gespeichert werden.';
                    Swal.fire('Fehler', errorMessage, 'error');
                }
            }
        });
    });

         // Function to perform category-based search
                function searchByCategory(categoryId) {
                    $.ajax({
                        url: "{{ route('notes.search.category') }}", // Route for category search
                        method: "GET",
                        data: { category_id: categoryId },
                        success: function (response) {
                            const noteTableBody = $('#note_table tbody'); // Reference to the table body
                            noteTableBody.empty(); // Clear existing rows

                            response.notes.forEach((note, index) => {
                                noteTableBody.append(`
                                    <tr data-id="${note.id}" style="cursor: grab; border-bottom: 10px solid #f8f8f8; border-left: 10px solid ${note.color}; background:white;">
                                        <td style="text-align: center;">${index + 1}</td>
                                        <td>
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
                                        </td>
                                        <td class="title-field ${note.is_done == 1 ? 'complete' : ''}" data-id="${note.id}" data-field="title">${note.title}</td>
                                        <td class="note-field" data-id="${note.id}" data-field="note">${note.note}</td>
                                        <td class="change-date" data-id="${note.id}">
                                            ${note.deadline ? 
                                                new Date(note.deadline).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                                'Kein Fälligkeitsdatum'}
                                        </td>
                                        <td>
                                            ${note.done_date ? 
                                                new Date(note.done_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                                'Nicht erledigt'}
                                        </td>
                                        <td>
                                            ${note.done_date ? 
                                                (note.deadline && new Date(note.done_date) > new Date(note.deadline) ? 
                                                    '<div class="badge badge-danger">Verspätet</div>' :
                                                    (note.deadline && new Date(note.done_date) < new Date(note.deadline) ? 
                                                        '<div class="badge badge-success">Vorzeitig</div>' : 
                                                        '<div class="badge badge-primary">Pünktlich</div>')
                                                ) : 
                                                '-'}
                                        </td>
                                        <td class="change-time" data-id="${note.id}">${note.end_time || 'Keine Endzeit'}</td>
                                        <td>${note.category_name || 'Standard'}</td>
                                        <td>
                                            ${note.reminder_date || note.reminder_time ? `
                                                <small class="no-reminder-icon" data-id="${note.id}" data-toggle="tooltip" title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                                    <i class="feather icon-bell primary"></i> 
                                                    ${note.reminder_date ? 
                                                        new Date(note.reminder_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                                        ''} 
                                                    ${note.reminder_time || ''}
                                                </small>` : '-'}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete_note" data-id="${note.id}">
                                                <i class="feather icon-trash"></i>  
                                            </button>
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light note-settings" data-id="${note.id}">
                                                <i class="feather icon-edit"></i> 
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            });

                            Swal.fire('Erfolgreich!', response.message, 'success');
                        },
                        error: function () {
                            Swal.fire('Fehler', 'Notizen konnten nicht geladen werden. Bitte versuche es erneut.', 'error');
                        }
                    });
                }

                // Event listener for category selection
                $(document).on('change', '.select-category', function () {
                    const selectedCategoryId = $(this).val(); // Get the selected category ID
                    if (selectedCategoryId) {
                        searchByCategory(selectedCategoryId); // Call the search function
                    }
                });

                // Optional: Add button to trigger search manually
                $(document).on('click', '#search_by_category', function () {
                    const selectedCategoryId = $('.select-category').val(); // Get the selected category ID
                    if (selectedCategoryId) {
                        searchByCategory(selectedCategoryId); // Call the search function
                    } else {
                        Swal.fire('Fehler', 'Bitte wählen Sie eine Kategorie aus.', 'error');
                    }
                });


        // Handle Search Function 
            function searchNotes(query) {
                $.ajax({
                    url: "{{ route('notes.search') }}",
                    method: "GET",
                    data: { 
                        search: query,
                        status: currentStatus   // << important
                    },
                    success: function (response) {
                        const noteTableBody = $('#note_table tbody');
                        noteTableBody.empty();

                        const repeatTranslations = {
                            "": "Häufigkeit auswählen",
                            "minute": "Minütlich",
                            "hourly": "Stündlich",
                            "daily": "Täglich",
                            "weekly": "Wöchentlich",
                            "monthly": "Monatlich",
                            "quarterly": "Vierteljährlich",
                            "yearly": "Jährlich"
                        };

                        response.notes.forEach((note, index) => {
                            noteTableBody.append(`
                                <tr data-id="${note.id}" style="cursor: grab; border-bottom: 10px solid #f8f8f8; border-left: 10px solid ${note.color}; background:white;">
                                    <td style="text-align: center;">${index + 1}</td>
                                    <td>
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
                                    </td>
                                    <td class="title-field ${note.is_done == 1 ? 'complete' : ''}" data-id="${note.id}" data-field="title">${note.title}</td>
                                    <td class="note-field" data-id="${note.id}" data-field="note">${note.note}</td>
                                    <td class="change-date" data-id="${note.id}">
                                        ${note.deadline ? 
                                            new Date(note.deadline).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                            'Kein Fälligkeitsdatum'}
                                    </td>
                                    <td>
                                        ${note.done_date ? 
                                            new Date(note.done_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                            'Nicht erledigt'}
                                    </td>
                                    <td>
                                        ${note.done_date ? 
                                            (note.deadline && new Date(note.done_date) > new Date(note.deadline) ? 
                                                '<div class="badge badge-danger">Verspätet</div>' :
                                                (note.deadline && new Date(note.done_date) < new Date(note.deadline) ? 
                                                    '<div class="badge badge-success">Vorzeitig</div>' : 
                                                    '<div class="badge badge-primary">Pünktlich</div>')
                                            ) : 
                                            '-'}
                                    </td>
                                    <td class="change-time" data-id="${note.id}">${note.end_time || 'Keine Endzeit'}</td>
                                    <td class="updateCategoryModal" data-id="${note.id}">${note.category_name || 'Standard'}</td>
                                    <td>
                                        ${note.reminder_date || note.reminder_time ? `
                                            <small class="no-reminder-icon" data-id="${note.id}" data-toggle="tooltip" title="Erinnerung: ${note.reminder_date || ''} ${note.reminder_time || ''}">
                                                <i class="feather icon-bell primary"></i> 
                                                ${note.reminder_date ? 
                                                    new Date(note.reminder_date).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 
                                                    ''} 
                                                ${note.reminder_time || ''}
                                            </small>` : '-'}
                                    </td>
                                    <td>
                                        ${note.repeat ? `
                                            <small class="no-repeat-icon" data-id="${note.id}">
                                                <i class="feather icon-refresh-ccw primary"></i> ${repeatTranslations[note.repeat] || note.repeat} 
                                            </small>` : '-'}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete_note" data-id="${note.id}">
                                            <i class="feather icon-trash"></i>  
                                        </button>
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light note-color" data-id="${note.id}">
                                            <i class="feather icon-aperture" ></i> 
                                        </button> 
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light note-settings" data-id="${note.id}">
                                            <i class="feather icon-edit"></i> 
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });

                        initializeSortable();
                    },
                    error: function () {
                        Swal.fire('Fehler', 'Notizen konnten nicht durchsucht werden. Bitte versuche es erneut.', 'error');
                    }
                });
            }

  
        
    // Event listener for the search form
    $(document).on('submit', '#search_form', function (e) {
        e.preventDefault(); // Prevent the default form submission
        const query = $('#search_input').val().trim(); // Get the search input value
        searchNotes(query); // Call the searchNotes function
    });

    // Optionally: Clear search and reload all notes
    $(document).on('click', '#search_button', function () {
        const query = $('#search_input').val().trim();
        if (query === '') {
            loadNotes(); // Reload all notes if search is empty
        }
    });

        // Load notes on page load
        loadNotes();
    });
</script>



<script>
    // Function to initialize sortable rows
    function initializeSortable() {
        Sortable.create(document.querySelector('#note_table tbody'), {
            animation: 150, // Animation speed in ms
            onEnd: function (evt) {
                // Get the new order of IDs
                const order = [];
                const rows = document.querySelectorAll('#note_table tbody tr');
                rows.forEach(row => {
                    order.push(row.getAttribute('data-id'));
                });

                // Send the new order to the server via AJAX
                updateOrder(order);
            },
        });
    }

    /**
     * Sends the new order to the server via AJAX.
     * @param {Array} order - Array of note IDs in the new order.
     */
    function updateOrder(order) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/notes/update-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken, // Laravel CSRF token
            },
            body: JSON.stringify({ order: order }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                toastr.success(data.message, 'Reihenfolge aktualisiert');
            }
        })
        .catch(error => {
            console.error('Error updating order:', error);
            toastr.error('Die Reihenfolge konnte nicht aktualisiert werden.', 'Fehler');
        });
    }

    // Ensure the DOM is fully loaded and load notes
    document.addEventListener('DOMContentLoaded', function () {
        loadNotes();
    });
</script>
 


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

          // Add click event listener to each dropdown-item
            $('#priority_select .dropdown-item').on('click', function () {
                // Get the selected priority value from the data-value attribute
                const selectedPriority = $(this).data('value');

                // Get the selected icon's HTML
                const selectedIcon = $(this).html();

                // Update the hidden input value
                $('input[name="priority"]').val(selectedPriority);

                // Update the button's icon
                $('#priority_select button').html(selectedIcon);
            });
        
    });


   </script>

    <!-- Priority Script end  -->

 
@endsection