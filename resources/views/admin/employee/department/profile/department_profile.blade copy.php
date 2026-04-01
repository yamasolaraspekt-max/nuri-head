@extends('admin.layouts.app')

@section('title') ABTEILUNG @endsection
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
            font-size: 27px;
            color: #a1a1a1;
            font-weight: bold;
        }
        .active-title {
             margin: 0;
            font-size: 16px;
            color: #8fc73e !important;
            font-weight: bold;
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

       .fc-next-button   { 
            padding: 5px !important;!;
            margin: 0 20px 0 13px !important;

        }
         .fc-prev-button  { 
            padding: 5px !important;!;
            margin: 20px 0 0 13px !important;

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
       .fc-day-today .fc-daygrid-day-number {
            border: 2px solid #989d90 !important;
            border-radius: 25% !important;
            padding: 6px !important;
            position: absolute !important;
            font-size: 17px !important;
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
 

 

        .link-image:hover, .link-image:hover {
            filter: invert(64%) sepia(16%) saturate(2445%) hue-rotate(169deg) brightness(94%) contrast(85%);
        }

        .link-image-active {
           filter: invert(52%) sepia(62%) saturate(509%) hue-rotate(61deg) brightness(98%) contrast(90%);

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

        .more_link {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }

        .edit_link {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>


    <style> 
 
        #deadline_area, .end_time_area, .repeated_area, .reminder_area ,.add_calendar_area{
            display: none;
        }
        
        .black {
            color: #555555 !important;
        }
        
        .cards {
            background-color: white; 
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
         /* events  */
          .card-event {
            display: flex;
            align-items: center;
            padding: 10px 20px; 
            
        }

       

        .time {
            text-align: right;
            color: #757575;
            margin-right:5px;
        }

        .time .date {
            text-align: right;
            color: #757575;
        }

       

        .separator {
            width: 2px;
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
            margin-bottom: 0;
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

   

        #calendar {
            position: absolute;
            top: 0px;
            left: 0;
            right: 0;
            bottom: 30%; /* adjust the bottom margin as needed */
            overflow: hidden;
        }

        .events-list {
            display: flex;
            justify-content: center;
            flex-direction: column;
            position: absolute;
            top: 40%; /* adjust the top margin as needed */
            left: 0;
            right: 0;
            bottom: 0;
            overflow-y: auto;
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
        .cards {
            max-height: 400px; /* Adjust height based on your layout */
            overflow-y: auto;
        }

        .badge {
            font-size: 12px;
            padding: 6px 8px;
        }

        #department_employee td  {
                vertical-align: middle;
                text-align: left;
        }

         #department_employee thead tr th  {
                vertical-align: middle;
                text-align: left;
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
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ABTEILUNG</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/department_view') }}">Abteilung</a>
                                </li>
                                      <li class="breadcrumb-item active">{{ $department->department_name }} Profile
                                </li> 
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        <div class="content-body"> 
            <div class="row">
                <div class="col-xl-8 col-md-6 col-sm-12">
                    <div class="row match-height" id="sortable-cards">
                        
                     <div class="col-xl-4 col-md-6 col-sm-12 card-cover" data-card-id="my_tasks">
                            <div class="cards"  style="height:322px !important">
                                <h3 class="mb-4">ABTEILUNGSDATEN: {{ strtoupper($department->department_name) }}</h3>  
                                    @if($department->department_head)
                                    <div class="avatar mr-1 avatar-xl">
                                        <img src="{{ asset('images/employee/'.$department->emp_image) }}" alt="avtar img holder">
                                    </div>
                                        <p>{{ $department->emp_name }} {{$department->emp_lastname}}</p> 
                                        <p>Abteilungsleiter</p> 
                                    @else
                                    Abteilungsleiter ist nicht definiert
                                    @endif 
                            </div> 
                        </div>  

                        <input type="hidden" id="department_id" value="{{$department->id}}">

                        <div class="col-xl-8 col-md-8 col-sm-12 card-cover" data-card-id="my_tasks">
                            <div class="cards"  style="height:322px !important">
                                <h3 class="mb-4">Kooperation Abteilungen</h3>  
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Abteilung</th>
                                                    <th>Abteilungsleiter</th> 
                                                    <th>Kosten</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($treeDepartments as $department)
                                                    @include('admin.employee.department.profile.components.department_row', ['department' => $department, 'level' => 0])
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                          
                                     
                            </div> 
                        </div>  
                       
                        
                        
                         <div class="col-xl-12 col-md-12 col-sm-12 card-cover" data-card-id="my_tasks">
                            <div class="cards"  style="height:322px !important">
                                <h3 class="">MITARBEITER DER ABTEILUNG</h3>  
                                  <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                        <table class="table" id="department_employee">
                                            <thead>
                                                <tr>  
                                                    <th>Name</th>
                                                    <th>Position</th> 
                                                    <th>Arbeitskapazität</th> 
                                                    <th>Büroanteil</th> 
                                                    <th>Montageanteil</th> 
                                                    <th>Stundenanteil</th> 
                                                    <th>Lohnanteil</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    // Initialize total variables
                                                    $total_used_percent = 0;
                                                    $total_office_percent = 0;
                                                    $total_montage_percent = 0;
                                                    $total_hours_sum = 0;
                                                    $total_salary_sum = 0;
                                                @endphp

                                                @foreach($employees as $employee)
                                                    @php
                                                        // Fetch positions related to this employee
                                                        $employeePositions = $positions->where('employee_id', $employee->id);

                                                        // Fetch employee salary
                                                        $salary = DB::table('salaries')
                                                                    ->where('emp_id', $employee->id)
                                                                    ->value('total_monthly_salary') ?? 0;

                                                        // Define total working hours per month (40 hours per week * 4 weeks)
                                                        $total_monthly_hours = 40 * 4; // 160 hours per month

                                                        // Ensure at least 1 row per employee
                                                        $rowspan = max(1, $employeePositions->count()); 
                                                    @endphp

                                                    <tr>
                                                        <!-- Employee Info -->
                                                        <td rowspan="{{ $rowspan }}" class="">
                                                            <img data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom"
                                                                data-original-title="{{ $employee->name }} {{ $employee->lastname }}"  
                                                                class="media-object rounded-circle" 
                                                                src="{{ asset('images/employee/' . $employee->image) }}" 
                                                                alt="Avatar" height="25" width="25">
                                                             {{ $employee->name }} {{ $employee->lastname }}
                                                        </td>

                                                        <!-- First Position -->
                                                        @if($employeePositions->isNotEmpty())
                                                            @php 
                                                                $firstPosition = $employeePositions->shift(); 

                                                                // Calculate working hours & salary share
                                                                $position_hours = ($firstPosition->percent / 100) * $total_monthly_hours;
                                                                $total_salary = ($firstPosition->percent / 100) * $salary;

                                                                // Accumulate totals for all employees
                                                                $total_used_percent += $firstPosition->percent;
                                                                $total_office_percent += $firstPosition->office_percent;
                                                                $total_montage_percent += $firstPosition->montage_percent;
                                                                $total_hours_sum += $position_hours;
                                                                $total_salary_sum += $total_salary;
                                                            @endphp
                                                            <td><span class="badge bg-primary">{{ $firstPosition->position }} {{$firstPosition->department_name}}</span></td>
                                                            <td>{{ number_format($firstPosition->percent, 0) }}% </td>
                                                            <td>{{ number_format($firstPosition->office_percent, 0) }}% </td>
                                                            <td>{{ number_format($firstPosition->montage_percent, 0) }}% </td>
                                                            <td>{{ number_format($position_hours, 2) }}</td>
                                                            <td>{{ number_format($total_salary, 2, ',', '.') }} €</td>
                                                        @else
                                                            <td colspan="6"><span class="badge bg-danger">No positions assigned</span></td>
                                                        @endif
                                                    </tr>

                                                    <!-- Additional Positions -->
                                                    @foreach($employeePositions as $position)
                                                        @php
                                                            $position_hours = ($position->percent / 100) * $total_monthly_hours;
                                                            $total_salary = ($position->percent / 100) * $salary;

                                                            // Accumulate totals for all employees
                                                            $total_used_percent += $position->percent;
                                                            $total_office_percent += $position->office_percent;
                                                            $total_montage_percent += $position->montage_percent;
                                                            $total_hours_sum += $position_hours;
                                                            $total_salary_sum += $total_salary;
                                                        @endphp
                                                        <tr>
                                                            <td><span class="badge bg-primary">{{ $position->position }}</span></td>
                                                            <td>{{ number_format($position->percent, 0) }}% </td>
                                                            <td>{{ number_format($position->office_percent, 0) }}% </td>
                                                            <td>{{ number_format($position->montage_percent, 0) }}% </td>
                                                            <td>{{ number_format($position_hours, 2) }}</td>
                                                            <td>{{ number_format($total_salary, 2, ',', '.') }} €</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach

                                                <!-- Summary Row -->
                                                <tr style="border-top: 4px solid #8fc73e;"> 
                                                    <td colspan="2"><strong>Gesamt</strong></td>
                                                    <td>{{ number_format($total_used_percent, 0) }}%</td>
                                                    <td>{{ number_format($total_office_percent, 0) }}%</td>
                                                    <td>{{ number_format($total_montage_percent, 0) }}%</td>
                                                    <td>{{ number_format($total_hours_sum, 2, ',', '.') }}</td>
                                                    <td>{{ number_format($total_salary_sum, 2, ',', '.') }} €</td> 
                                                </tr> 
                                            </tbody>
                                        </table>
                                    </div>



                            </div> 
                        </div>  
                        
                    </div>   
                </div> 
                <div class="col-xl-4 col-md-6 col-sm-12"> 
                    <div class="calendar-list"  >
                        <input type="hidden" id="emp_id" value="{{$department->id}}">
                    <!-- Calendar -->
                    <div id='calendar'></div> 
                    <!-- Events List -->
                    <div class="events-list"> 
                        <div class="card-event">
                        <hr class="event-hr">   
                            <div class="time">
                                <div class="date"></div>
                                <div class="hour"></div>
                            </div>
                            <div class="separator"></div>
                            <div class="details">
                                <div class="title"></div>
                                <div class="description"></div>
                            </div> 
                        </div>
                    </div>
                </div>  
            </div>
        </div> 
    </div>
</div>
<!-- END: Content-->
 

@endsection

@section('script')

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://unpkg.com/feather-icons"></script>
  
<script>
$(document).ready(function () {
    const calendarEl = document.getElementById('calendar');
    const departmentInput = document.getElementById('department_id');

    if (!calendarEl || !departmentInput) {
        console.error("Missing #calendar or #department_id element!");
        return;
    }

    const departmentId = departmentInput.value.trim();
    const today = new Date().toISOString().split('T')[0];

    const formatDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('de-DE', { day: '2-digit', month: 'short' });
    };

    const formatTime = (timeStr) => {
        if (!timeStr) return '';
        const [hour, minute] = timeStr.split(':');
        return `${hour}:${minute} Uhr`;
    };

    function fetchCalendarData(callback) {
        $.ajax({
            url: `/get_department_calendar/${departmentId}`,
            method: 'GET',
            success: function (response) {
                if (!response || !Array.isArray(response.data)) {
                    console.error('Invalid calendar response:', response);
                    Swal.fire('Fehler', 'Die Aufgaben konnten nicht geladen werden.', 'error');
                    return callback([]);
                }
                callback(response.data);
            },
            error: function (xhr) {
                console.error('Error fetching calendar data:', xhr.responseText);
                Swal.fire('Fehler', 'Die Aufgaben konnten nicht geladen werden.', 'error');
                callback([]);
            }
        });
    }

    function loadEventsForDate(date) {
        fetchCalendarData(function (allEvents) {
            const filtered = allEvents.filter(item =>
                date >= item.start_date && date <= (item.end_date || item.start_date)
            );

            $('.events-list').empty();

            filtered.forEach(event => {
                const employeesHtml = (event.employees || []).map(emp => `
                    <li class="avatar" data-toggle="tooltip" data-popup="tooltip-custom" 
                        data-placement="bottom" title="${emp.name} ${emp.lastname}">
                        <img class="media-object rounded-circle" 
                             src="/images/employee/${emp.image}" 
                             alt="Avatar" style="height:30px; width:30px">
                    </li>
                `).join('');

                const eventHtml = `
                    <div class="event-line"></div>
                    <div class="card-event mb-1">
                        <div class="time">
                            <div class="date">${formatDate(event.start_date)}</div>
                            <div class="hour">${formatTime(event.start_time)}</div>
                        </div>
                        <div class="separator" style="background-color: ${event.taskColor} !important;"></div>
                        <div class="details">
                            <div class="title">
                                <a href="${event.type === 'task' ? '/personal_task_details/' : '/appointment_details/'}${event.id}">
                                    ${event.title}
                                </a>
                            </div>
                            <div class="description">
                                <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                    ${employeesHtml}
                                </ul>
                            </div>
                        </div>
                    </div>
                `;

                $('.events-list').append(eventHtml);
            });
        });
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        selectMirror: false,
        height: 'auto',
        locale: 'de',
        firstDay: 1,
        buttonText: {
            today: 'Heute'
        },
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        dateClick: function (info) {
            document.querySelectorAll('.fc-day-selected').forEach(day =>
                day.classList.remove('fc-day-selected')
            );
            info.dayEl.classList.add('fc-day-selected');
            loadEventsForDate(info.dateStr);
        },
        dayHeaderContent: args => args.text.charAt(0),
        events: function (fetchInfo, successCallback, failureCallback) {
            fetchCalendarData(function (events) {
                const formatted = events.map(item => ({
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
                    }
                }));
                successCallback(formatted);
            });
        },
        eventDidMount: function (info) {
            if (info.event.backgroundColor) {
                const dateStr = info.event.startStr.split('T')[0];
                document.querySelectorAll(`.fc-day[data-date='${dateStr}'] .fc-daygrid-day-number`).forEach(el => {
                    el.style.setProperty('--event-bg-color', info.event.backgroundColor);
                    el.setAttribute('data-event', 'true');
                });
            }
        },
        eventClick: function (info) {
            loadEventsForDate(info.event.startStr.split('T')[0]);
        },
        dayCellDidMount: function (info) {
            const todayStr = new Date().toISOString().split('T')[0];
            if (info.date.toISOString().split('T')[0] === todayStr) {
                info.el.style.borderBottom = '3px solid #007bff';
            }
        }
    });

    calendar.render();
    loadEventsForDate(today);
});
</script>

 
@endsection