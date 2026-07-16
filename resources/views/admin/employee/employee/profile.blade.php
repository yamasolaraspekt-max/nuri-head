@extends('admin.layouts.app')
@section('title') PROFILE @endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/users.css') }}">
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
        .active-title {
             margin: 0;
            font-size: 16px;
            color: #8fc73e !important;
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
            color: #1f2937;
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

     .calendar-list {
            position: relative;
            height: 500px; /* adjust the height as needed */
            overflow: hidden;
        }

        #calendar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 30%; /* adjust the bottom margin as needed */
            overflow: hidden;
        }

        .events-list {
            position: absolute;
            top: 62%; /* adjust the top margin as needed */
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
                            <h2 class="content-header-title float-left mb-0">KUNDE INFORMATION</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                                    </li>
                                     <li class="breadcrumb-item"><a href="{{ url('/emp') }}">MITARBEITER</a>
                                </li> 
                                    <li class="breadcrumb-item"><a href="#">Details</a>
                                    </li> 
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div id="user-profile">
                    <div class="row">
                        <div class="col-12">
                            <div class="profile-header mb-2">
                                
                                <div class="d-flex justify-content-end align-items-center profile-header-nav">
                                    <nav class="navbar navbar-expand-sm w-100 pr-0">
                                        <button class="navbar-toggler pr-0" type="button" data-toggle="collapse" data-target="navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                            <span class="navbar-toggler-icon"><i class="feather icon-align-justify"></i></span>
                                        </button>
                                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                            <ul class="navbar-nav justify-content-around w-75 ml-sm-auto">
                                                <li class="nav-item px-sm-0">
                                                    <a href="#" class="nav-link font-small-3">Task Management</a>
                                                </li>
                                                <li class="nav-item px-sm-0">
                                                    <a href="#" class="nav-link font-small-3">Projects</a>
                                                </li>
                                                <li class="nav-item px-sm-0">
                                                    <a href="{{ url('salary_sheet/'.request()->id) }}" class="nav-link font-small-3">Salary Management</a>
                                                </li>
                                                <li class="nav-item px-sm-0">
                                                    <a href="#" class="nav-link font-small-3">Events</a>
                                                </li>

                                                <li class="nav-item px-sm-0">
                                                    <a href="#" class="nav-link font-small-3">Warnings</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section id="profile-info">
                        <div class="row">
                            <div class="col-lg-3 col-12">
                                <div class="card" style="height: autopx;">
                                   
                                    <div class="card-content">
                                        <div class="card-body text-center mx-auto">
                                            <div class="picture">
                                                @if($data->image)
                                                <img class="img-fluid" src="{{ asset('images/employee/'.$data->image)}}" alt="{{ $data->name }}" style="    width: 400px;  height: 400px;  border: 10px solid #8fc73e;  border-radius: 50%;">
                                                @else
                                                        @if($data->gender=="Male")
                                                    
                                                        <img class="img-fluid" src="{{ asset('images/gender/male.png')}}" alt="{{ $data->name }}">
                                                        @else
                                                        <img class="img-fluid" src="{{ asset('images/gender/female.png')}}" alt="{{ $data->name }}">
                                                        @endif
                                                @endif
                                            </div> 
                                        </div>
                                    </div>
                                    @php
                                        $initials = strtoupper(substr($data->name, 0, 1) . substr($data->lastname, 0, 1));
                                    @endphp
                                    <div class="card-header mx-auto pb-0">
                                        <div class="row m-0">
                                            <div class="col-sm-12 text-center">
                                                <h2>{{ $data->name }} {{ $data->midname }} {{ $data->lastname }}</h2>
                                             <p>@ {{ $initials }}</p>


                                            </div>
                                            <div class="col-sm-12 text-center">
                                                <p class=""></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="card">
                                    <div class="card-header">
                                        <h4>PROFILE</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $data->bio }}</p>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Name/Familienname:</h6>
                                            <p>{{ $data->name }} {{ $data->midname }} {{ $data->lastname }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Geschlecht:</h6>
                                            <p>{{ $data->gender }} </p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Familienstand:</h6>
                                            <p>{{ $data->marital_status }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Staatsangehörigkeit:</h6>
                                            <p>{{ $data->nationality }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Land:</h6>
                                            <p>{{ $data->country }}</p>
                                        </div>

                                        <div class="mt-1">
                                            <h6 class="mb-0">Sprachen:</h6>
                                            <p>@foreach ($language as $lang)
                                                    {{ $lang->language }},
                                            @endforeach</p>
                                        </div>

                                        <div class="mt-1">
                                            <h6 class="mb-0">E-Mail</h6>
                                            <p><i class="feather icon-mail"></i> {{ $data->email }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Kontakt</h6>
                                            <p><i class="fa fa-mobile"></i> {{ $data->phone }}</p>
                                            <p><i class="fa fa-home"></i> {{ $data->home_phone }}</p>
                                            <p><i class="feather icon-phone"></i> {{ $data->work_phone }}</p>
                                        </div>
                                        
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h4>JOBDETAILS</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        <div class="mt-1">
                                            <h6 class="mb-0">Vertragstyp</h6>
                                            <p>{{ $data->contract_type }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Vertragsdatum:</h6>
                                            <p>{{ $data->contract_date }} </p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Arbeitsstunde:</h6>
                                            <p>{{ $data->working_hour }} / {{ $data->working_type }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Urlaub / verbleibende Tage:</h6>
                                            <p>{{ $data->leave }} / {{ $data->remaining_day }}</p>
                                        </div>

                                        <div class="mt-1">
                                            <h6 class="mb-0">Krankheitsurlaub / Verbleibend :</h6>
                                            <p>{{ $data->sick_leave }} / {{ $data->sick_leave_remaining }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Abteilung:</h6>
                                            <p>@foreach ($department as $dept)
                                                {{ $dept->department_name }},
                                            @endforeach</p>
                                        </div>

                                        <div class="mt-1">
                                            <h6 class="mb-0">E-Mail</h6>
                                            <p><i class="feather icon-mail"></i> {{ $data->email }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Kontakt</h6>
                                            <p><i class="fa fa-mobile"></i> {{ $data->phone }}</p>
                                            <p><i class="fa fa-home"></i> {{ $data->home_phone }}</p>
                                            <p><i class="feather icon-phone"></i> {{ $data->work_phone }}</p>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Suggested Pages</h4>
                                    </div>
                                    <div class="card-body suggested-block">
                                        <div class="d-flex justify-content-start align-items-center mb-1">
                                            <div class="avatar mr-50">
                                                <img src="../../../app-assets/images/profile/pages/page-09.jpg" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <p>Rockose</p>
                                                <span class="font-small-2">Company</span>
                                            </div>
                                            <div class="ml-auto"><i class="feather icon-star"></i></div>
                                        </div>
                                        
                                    </div>
                                </div>
                               
                            </div>

                            {{-- Qualification Section --}}
                            <div class="col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mitarbeiterqualifikationen </h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <ul class="activity-timeline timeline-left list-unstyled">
                                                @foreach ($qualifications as $qual)
                                                <li>
                                                    <div class="timeline-icon bg-primary">
                                                        <i class="fa fa-graduation-cap font-medium-2"></i>
                                                    </div>
                                                    <div class="timeline-info">
                                                        <p class="font-weight-bold">{{ $qual->degree }} - {{ $qual->major }}</p>
                                                        <span><i class="fa fa-university   "></i> {{ $qual->institution }}</span>
                                                    </div>
                                                    <small class="">{{\Carbon\Carbon::parse($qual->year)->isoFormat('YYYY')}}</small>
                                                </li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                               
                        {{-- Qualification Section --}}

                            {{-- Further Education Section --}}
                    
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mitarbeiterweiterbildung und Schulungen </h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <ul class="activity-timeline timeline-left list-unstyled">
                                                @foreach ($feducation as $edu)
                                                <li>
                                                    <div class="timeline-icon bg-primary">
                                                        <i class="fa fa-book font-medium-2"></i>
                                                    </div>
                                                    <div class="timeline-info">
                                                        <p class="font-weight-bold">{{ $edu->course }} - {{ $edu->major }}</p>
                                                        <span><i class="fa fa-university   "></i> Institution: {{ $edu->institution }}</span> &nbsp;
                                                        <span><i class="fa fa-sign-language "></i> Fähigkeiten: {{ $edu->skill }}</span>
                                                    </div>
                                                    <div class="timeline-info">
                                                        <span>{{ $edu->description }}</span>
                                                    </div>
                                                    <small class="">{{\Carbon\Carbon::parse($edu->year)->isoFormat('YYYY')}}</small>
                                                    <p></p>
                                                </li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                               
                                    {{-- Further Education Section --}}

                            {{-- Skill Section --}}
                    
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mitarbeiterweiterbildung und Schulungen </h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                               
                                                    <table class="table" id="">
                                                        <thead>
                                                            <tr>
                                                                <th>Gewerk</th>
                                                                <th>Beratung</th>
                                                                <th>Planung</th>
                                                                <th>Kalkulation</th>
                                                                <th>Montage</th>
                                                                <th>Projektierung</th>
                                                                <th>Bauleitung</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($skills as $skil)
                                                            <tr>
                                                            
                                                                <td>{{ $skil->article_group }}</td>
                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <=  $skil->advice; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <=  $skil->plan; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <= $skil->calculation; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div> 
                                                                </td>

                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <= $skil->montage; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <= $skil->project_planing; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                    @for ($i = 1; $i <= $skil->site_management; $i++)
                                                                    <i class="fa fa-star" style="color:gold"></i>
                                                                    @endfor
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>

                                                </div>
                                        </div>
                                    </div>
                                </div>
                               
                                    {{-- Skill Section --}}

                                     {{-- Other Section --}}
                    
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mitarbeiterweiterbildung und Schulungen </h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                               
                                                <table class="table" id="">
                                                    <thead>
                                                        <tr>
                                                            <th>Mitarbeitername</th>
                                                            <th>Fähigkeiten</th>
                                                            <th>Kompetenz</th>
                                                            <th>Erfahrung</th>
                                                        </tr>
                                                    </thead>
                                                <tbody>
                                                        @foreach ($otherskill as $oskill)
                                                        <tr>
                                                        
                                                            <td>{{ $data->name }} {{ $data->lastname }}</td>
                                                            <td>{{ $oskill->skills }}</td>
                                                            <td>
                                                                <div class="fonticon-wrap">
                                                                @for ($i = 1; $i <=  $oskill->proficiency; $i++)
                                                                <i class="fa fa-star" style="color:gold"></i>
                                                                @endfor
                                                                </div>
                                                            </td>
                                                            <td>{{ $oskill->year_experience }}</td>
                                                        
                                                           
                                                                </tr>
                                                        @endforeach
                                                    </tbody>
                                                    </table>

                                                </div>
                                        </div>
                                    </div>
                                </div>
                               
                                    {{-- Other Skill Section --}}
                                
                            </div>
                    

                        

                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Urlaub und Feiertage</h4>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($leaves as $lev)
                                            
                                     
                                        <div class="twitter-feed">
                                            <div class="d-flex justify-content-start align-items-center mb-1">
                                                <div class="avatar mr-50">
                                                    <img src="{{ asset('images/employee/'.$lev->image) }}" alt="avtar img holder" height="35" width="35">
                                                </div>
                                                <div class="user-page-info">
                                                    <p class="text-bold-600 mb-0">{{ $lev->leave_type }}</p>
                                                    <div class="badge badge-pill badge-light-primary mr-1 mb-1"><small>Genehmigt: {{ $lev->paid }}</small></div>
                                                    <div class="badge badge-pill badge-light-primary mr-1 mb-1"><small>Bezahlt: {{ $lev->paid }}</small></div>
                                               
                                                </div>
                                            </div>

                                            <p class="mb-0"><strong>Grund:</strong> {{ $lev->reason }}</p>
                                            <p class="mb-0"><strong>Beschreibung:</strong> {{ $lev->description }}</p>
                                            <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th>Von</th>
                                                    <th>Bis</th>
                                                </tr>
                                                <tr>
                                                    <td>{{ $lev->start_date }}</td>
                                                    <td>{{ $lev->end_date }}</td>
                                                </tr>
                                            </table>
                                            </div>
                                            
                                        </div>
                                        <hr>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>ADRESS-DETAILS</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($addresses as $add)
                                        <div class="mt-1">
                                            <h6 class="mb-0">{{ $add->address_name }}</h6>
                                            <p>{{ $add->street }} {{ $add->apartment }}</p>
                                            <p>{{ $add->postal }} {{ $add->city }}</p>
                                        </div>
                                        
                                        @endforeach
                                   
                                        
                                    </div>

                                    <div class="card-header">
                                        <h4>NOTFALL-KONTAKTDATEN</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($emergency as $add)
                                        <div class="mt-1">
                                            <h6 class="mb-0">{{ $add->relation }}</h6>
                                            <p>{{ $add->street }} {{ $add->apartment }}</p>
                                            <p>{{ $add->postal }} {{ $add->city }}</p>
                                        </div>
                                        <div class="mt-1">
                                            <h6 class="mb-0">Kontakt</h6>
                                            <p><i class="fa fa-mobile"></i> {{ $add->phone }}</p>
                                            <p><i class="fa fa-home"></i> {{ $add->home_phone }}</p>
                                        </div>
                                        
                                        @endforeach
                                   
                                        
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between">
                                        <h4>Gegenstände übergeben</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                       @foreach ($handover as $hand)
                                        <div class="d-flex justify-content-start align-items-center mb-2">
                                            <div class="avatar mr-50">
                                                <img src="{{ asset('images/asset/'.$hand->image) }}" alt="avtar img holder" height="35" width="35">
                                            </div>
                                            <div class="user-page-info">
                                                <h6 class="mb-0">{{ $hand->item }}</h6>
                                                <span class="font-small-2">Serial#: <code>{{ $hand->serial_no }}</code></span>
                                                <span class="font-small-2">Article#: <code>{{ $hand->article_no }}</code></span>
                                    
                                                <span class="font-small-2">Menge#: <code>{{ $hand->quantity }}</code></span>
                                                <span class="font-small-2">Datum: <code>{{ $hand->handover_date }}</code></span>
                                            </div>
                                            <a type="button" class="btn btn-primary btn-icon ml-auto"  href="{{ url('/handover_details?search='.$hand->serial_no) }}"><i class="feather icon-edit-2  "></i></a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Polls</h4>
                                    </div>
                                    <div class="card-body">
                                        <h6>Who is the best actor in Marvel Cinematic Universe?</h6>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">RDJ</span>
                                                </div>
                                                <div class="text-right">58%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="58" aria-valuemin="58" aria-valuemax="100" style="width:58%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Tonia Seabold" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Carissa Dolle" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Kelle Herrick" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Len Bregantini" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="John Doe" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Tonia Seabold" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-12.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Dirk Fornili" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Chris Hemswort</span>
                                                </div>
                                                <div class="text-right">16%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="16" aria-valuemin="16" aria-valuemax="100" style="width:16%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Liliana Pecor" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Kasandra NaleVanko" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Mark Ruffalo</span>
                                                </div>
                                                <div class="text-right">8%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="8" aria-valuemin="8" aria-valuemax="100" style="width:8%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Lorelei Lacsamana" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-4.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="polls-info mt-1">
                                            <div class="d-flex justify-content-between">
                                                <div class="vs-radio-con vs-radio-primary">
                                                    <input type="radio" name="vueradio" value="false">
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">Chris Evans</span>
                                                </div>
                                                <div class="text-right">16%</div>
                                            </div>
                                            <div class="progress progress-bar-primary my-50">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="16" aria-valuemin="16" aria-valuemax="100" style="width:16%"></div>
                                            </div>
                                            <ul class="list-unstyled users-list d-flex">
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="JeanieBulgrin" class="avatar pull-up ml-0">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Graig Muckey" class="avatar pull-up">
                                                    <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-3.jpg" alt="Avatar" height="30" width="30">
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" class="btn btn-primary block-element mb-1">Load More</button>
                            </div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->
        
    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>
    @endsection

@section('script')

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('app-assets/js/scripts/pages/user-profile.js') }}"></script>
    <!-- END: Page JS-->
@endsection