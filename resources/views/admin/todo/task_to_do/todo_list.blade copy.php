@extends('admin.layouts.app')
@section('title')
Aufgaben-Checkliste
@endsection

@push('style')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />

    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" /> 
    <meta name="csrf-token" content="{{ csrf_token() }}">  
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/plugins/calendars/fullcalendar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/calendars/fullcalendar.min.css')}}">

    <link rel="stylesheet" type="text/css" href="{{ asset('css/calendar.css')}}"> 


    <style>
        
    .icon-info:hover { 
    color: #8fc73e; 
      }
    .star:hover {
        color: #8fc73e; 
    }

    .delete:hover {
    color: #8fc73e; 
    }

</style>
<style>
 .fc .fc-event {
        background: #e7e7e7;
    border: 1px solid #5E50EE;
    padding: 1px 8px;
    border-radius: -13rem;
    border: none;
    padding-right: 1rem;
    margin-top: 4px;
    border-left: 3px solid #5E50EE;
 }

 
</style>
 
@endpush
@section('content')  
    <!-- BEGIN: Content-->
   <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">AUFGABENVERWALTUNG</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">  
                <div class="content-area-wrapper m-0"> 
                    <div class="col-xl-3 col-md-3 col-sm-12 ">  
                            <div class="todo-app-menu">
                                <div class="form-group text-center add-task">
                                    <button type="button" class="btn btn-primary btn-block my-1" data-toggle="modal" data-target="#addTaskModal">Aufgaben erstellen</button>
                                </div>
                                <div class="sidebar-menu-list"> 
                                    <hr>
                                    <h5 class="mt-2 mb-1 pt-25">Filters</h5>
                                    <div class="list-group list-group-filters font-medium-1">
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-star mr-50"></i> Kundenaufgabe</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-users mr-50"></i> Mitarbeiteraufgabe</a>
                                        <a href="#" class="list-group-item list-group-item-action border-0"><i class="font-medium-5 feather icon-briefcase mr-50"></i> Firma Aufgaben</a>
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
                        <!-- Modal -->
                        <!-- calendar Modal starts-->
                            <div class="modal fade text-left" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModal" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document" style="max-width:1500px">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="add_appointment">Neue Aufgabe</h4> 
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">  
                                            <div class="row"> 
                                                <div class="col-xl-6 col-md-6 col-12">
                                                    <fieldset class="form-group">
                                                        <label for="basicInput">Aufgabentyp</label>
                                                        <select name="task_type" id="task_type" class="form-control" style="width:100% !important;">
                                                            <option select>Wählen Sie eine Option</option>  
                                                            <option value="customer">Kunde</option>  
                                                            <option value="employee">Mitarbeiter</option>  
                                                            <option value="personal">Persönlich</option>  
                                                            <option value="company">Firma</option>  
                                                        </select> 
                                                    </fieldset>
                                                </div> 
                                                    <form id="customerFormTask" class="col-12" style="display:none">  
                                                        <div class="customer_task row">
                                                            <div class="col-xl-3 col-md-3 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Kunde</label>
                                                                    <input type="hidden" name="postcode" id="postcode"> 
                                                                    <select name="customer_id" id="customer_id" class="form-control select2" style="width:100% !important;">
                                                                        <option value=""></option> 
                                                                    </select> 
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-3 col-md-3 col-12 d-flex ">
                                                                <fieldset class="form-group">
                                                                    <label for="helpInputTop">Gewerke</label>
                                                                    <select name="product_id" id="product_id" class="form-control select22" style="width:100% !important;">
                                                                        <option value=""></option>
                                                                        <!-- Options will be loaded here via AJAX -->
                                                                    </select> 
                                                                </fieldset>
                                                                <fieldset class="mt-2 ml-2">
                                                                    <i class="feather icon-info warning" style="font-size: 20px;" 
                                                                    data-toggle="tooltip" data-placement="top" 
                                                                    data-container="body" 
                                                                    data-original-title="Bitte stellen Sie sicher, dass Sie dieses Produktprojekt bereits gestartet haben, bevor Sie dessen Aufgabe erstellen." 
                                                                    ></i>
                                                                </fieldset>
                                                                
                                                        </button>
                                                                <input type="hidden" name="selectProduct" id="selectProduct">
                                                            </div>   
                                                            <div class="tasks col-xl-12 d-flex"> 
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped table-bordered" id="tasksTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Phase/Process</th>
                                                                                <th>Hauptaufgabe</th>
                                                                                <th>Teilaufgabe</th>
                                                                                <th>Verantwortlich</th> 
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
                                                                                    <select name="active[0][activity_id]" id="activity_id" class="form-control select22 activity_id" style="width:100% !important;"  > 
                                                                                        <!-- Options will be loaded here via AJAX -->
                                                                                    </select>  
                                                                                </td> 
                                                                                <td> 
                                                                                    <select name="active[0][sub_task_id][]" id="sub_task_id" class="form-control select22 sub_task_id" style="width:100% !important;" multiple> 
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
                                                            <div class="col-xl-2 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Startdatum</label>
                                                                    <input type="date" class="form-control"  name="start_date">
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-xl-2 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Enddatum</label>
                                                                    <input type="date" class="form-control"  name="end_date">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-2 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Berichtsabgabetermin</label>
                                                                    <select name="report_date" id="" class="form-control">
                                                                        <option value="3">3 Tage</option>
                                                                        <option value="5">5 Tage</option>
                                                                        <option value="7">7 Tage</option>
                                                                    </select>
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
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absage</button>
                                                    </form>

                                                    <form id="employeeFormTask" class="col-12"  style="display:none;">  
                                                        <div class="customer_task row">
                                                            <div class="col-xl-3 col-md-3 col-12">
                                                            @php
                                                                $emp_image = DB::table('employees')
                                                                            ->select('name', 'lastname', 'image')
                                                                            ->where('id', auth()->user()->name)  
                                                                            ->first();
                                                            @endphp    

                                                            @if($emp_image)
                                                                <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                    Verfesser:
                                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" 
                                                                        data-original-title="{{ $emp_image->name }} {{ $emp_image->lastname }}" 
                                                                        class="avatar pull-up">
                                                                        <img class="media-object rounded-circle" 
                                                                            src="{{ asset('images/employee/' . $emp_image->image) }}" 
                                                                            alt="Avatar" height="70" width="70"> 
                                                                    </li> 
                                                                </ul>
                                                            @else
                                                                <p>Employee data not found.</p>
                                                            @endif 
                                                            </div> 
                                                            <div class="tasks col-xl-12 d-flex"> 
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped table-bordered" id="EmployeetasksTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Verantwortlich/th>
                                                                                <th>Aufgaben</th> 
                                                                                <th>Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody> 
                                                                            <tr>
                                                                                <td>
                                                                                    <select name="taskTo[0][task_to][]" id="task_to" class="form-control employee_id" style="width:100% !important;" multiple>
                                                                                        @foreach ($employee as $emp)
                                                                                        <option value="{{$emp->id}}" data-image="{{ asset('images/employee/'.$emp->image)}}">
                                                                                            {{$emp->name}} {{ $emp->lastname }}
                                                                                        </option>
                                                                                        @endforeach
                                                                                    </select>   
                                                                                </td>
                                                                                <td> 
                                                                                    <input type="text" class="form-control" name="taskTo[0][task_description]">
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
                                                            <div class="col-xl-2 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Startdatum</label>
                                                                    <input type="date" class="form-control"  name="start_date">
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-xl-2 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Enddatum</label>
                                                                    <input type="date" class="form-control"  name="end_date">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-2 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Berichtsabgabetermin</label>
                                                                    <input type="date" class="form-control"  name="report_date">
                                                                </fieldset>
                                                            </div>
                                                        
                                                            <div class="col-xl-2 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Startzeit</label>
                                                                    <input type="time" class="form-control"  name="start_time">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-2 col-md-6 col-12">
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
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                    </form>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        <!-- calendar Modal ends--> 
                    </div>
                    <div class="col-xl-9 col-md-9 col-sm-12 "> 
                        <div class="content-header row">
                        </div>
                        <div class="content-body">
                            <div class="app-content-overlay"></div>
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Aufgabenverwaltung</h4>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body">  
                                                <!-- Nav tabs -->
                                                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab" aria-controls="home-fill" aria-selected="true"><span id="tab_task_title">NEUE</span></a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab-fill" data-toggle="tab" href="#profile-fill" role="tab" aria-controls="profile-fill" aria-selected="false">Terminkalender</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="messages-tab-fill" data-toggle="tab" href="#messages-fill" role="tab" aria-controls="messages-fill" aria-selected="false">Ausstehende Aufgaben</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="settings-tab-fill" data-toggle="tab" href="#settings-fill" role="tab" aria-controls="settings-fill" aria-selected="false">Einstellungen</a>
                                                    </li>
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill"> 
                                                        <div class="todo-app-area" id="customer_task_management">
                                                            <div class="todo-app-list-wrapper">
                                                                <div class="todo-app-list">
                                                                    <div class="app-fixed-search">
                                                                        <div class="sidebar-toggle d-block d-lg-none"><i class="feather icon-menu"></i></div>
                                                                        <fieldset class="form-group position-relative has-icon-left m-0">
                                                                            <input type="text" class="form-control" id="todo-search" placeholder="Search..">
                                                                            <div class="form-control-position">
                                                                                <i class="feather icon-search"></i>
                                                                            </div>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="todo-task-list list-group mt-2">   
                                                                        <div class="divider divider-primary">
                                                                            <div class="divider-text">
                                                                                <h2> Heutige Aufgabe  
                                                                                    <div class="chip chip-primary mr-1">
                                                                                        <div class="chip-body">
                                                                                            <div class="avatar">
                                                                                                <i class="feather icon-calendar"></i>
                                                                                            </div>
                                                                                            <span class="chip-text">{{ \Carbon\Carbon::parse(now())->isoFormat('DD.MM.YYY') }}</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </h2>
                                                                            </div>
                                                                        </div>

                                                                        <section id="accordion">
                                                                            <div class="row">
                                                                                <div class="col-sm-12">
                                                                                    <div id="accordionWrapa1" role="tablist" aria-multiselectable="true">
                                                                                        <div class="card collapse-icon accordion-icon-rotate"> 
                                                                                            <div class="card-content">
                                                                                                <div class="card-body">   
                                                                                                     <div class="accordion-default collapse-bordered">
                                                                                                        @foreach ($appointments as $customer)
                                                                                                            <div class="card collapse-header">
                                                                                                                <!-- Customer Header -->
                                                                                                                <div id="heading{{ $customer['customer_id'] }}" class="card-header collapse-header collapsed" data-toggle="collapse" role="button" data-target="#accordion{{ $customer['customer_id'] }}" aria-expanded="false" aria-controls="accordion{{ $customer['customer_id'] }}">
                                                                                                                    <div class="title" style="display: flex; flex-direction: column;">
                                                                                                                        <h4 class="lead collapse-title primary bold">
                                                                                                                            <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                                                                                                                                <button type="button" class="btn dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                                                    <i class="feather icon-menu dropdown-icon"></i>
                                                                                                                                </button>
                                                                                                                                <div class="dropdown-menu">
                                                                                                                                    <span class="dropdown-item"><i class="feather icon-edit"></i> Bearbeiten</span>
                                                                                                                                    <span class="dropdown-item"><i class="feather icon-trash danger"></i> Löschen</span>
                                                                                                                                    <span class="employeemodal dropdown-item"><i class="feather icon-users"></i> Mitarbeiter zur Aufgabe hinzufügen</span>
                                                                                                                                    <span class="dropdown-item"><i class="feather icon-corner-down-right"></i> Antrag auf Verschiebung</span>
                                                                                                                                    <span class="dropdown-item"><i class="feather icon-log-in"></i> Aufgabe delegieren</span>
                                                                                                                                    <span class="dropdown-item"><i class="feather icon-user"></i> Ändern des Aufgabenverantwortlichen</span>
                                                                                                                                    <span class="direction dropdown-item" data-lat="{{ $customer['lat'] ?? 000000 }}" data-lon="{{ $customer['lon'] ?? 000000 }}"><i class="feather icon-map-pin"></i> Route</span>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            {{ $customer['customerName'] }} {{ $customer['customerLastname'] }}
                                                                                                                        </h4>
                                                                                                                        <small>
                                                                                                                            <i class="feather icon-map-pin"></i>
                                                                                                                            {{ $customer['street'] ?? 'Unknown Street' }} {{ $customer['postcode'] ?? 'N/A' }},
                                                                                                                            {{ $customer['city'] ?? 'Unknown City' }}
                                                                                                                        </small>
                                                                                                                        <div class="date d-flex">
                                                                                                                            <div class="chip-wrapper">
                                                                                                                                <div class="chip mb-0 mt-1">
                                                                                                                                    <div class="chip-body">
                                                                                                                                        <span class="chip-text" data-value="Frontend">
                                                                                                                                            <span class="bullet bullet-primary bullet-xs"></span>
                                                                                                                                            {{ $customer['product'] }}
                                                                                                                                        </span>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <!-- Customer Phases -->
                                                                                                                <div id="accordion{{ $customer['customer_id'] }}" role="tabpanel" data-parent="#accordionWrapa1" aria-labelledby="heading{{ $customer['customer_id'] }}" class="collapse">
                                                                                                                    <div class="card-content">
                                                                                                                        <div class="card-body">
                                                                                                                            @foreach ($customer['phases'] as $phase)
                                                                                                                                <h5>{{ $phase['phase_name'] }}:</h5> 
                                                                                                                                <hr>

                                                                                                                                <!-- Appointments -->
                                                                                                                                <ul style="list-style: none;">
                                                                                                                                    @foreach ($phase['appointments'] as $appointment)
                                                                                                                                        <li> 
                                                                                                                                            <fieldset>
                                                                                                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                                                    <input type="checkbox" value="false" name="doneTask" id="doneTask"
                                                                                                                                                        data-customer="{{ $customer['customer_id'] }}"
                                                                                                                                                        data-product="{{ $appointment['product_id'] }}"
                                                                                                                                                        data-address="{{ $customer['address_no'] }}"
                                                                                                                                                        data-phase="{{ $phase['phase_id'] }}"
                                                                                                                                                        data-task="{{ $appointment['task']['id'] }}"
                                                                                                                                                        data-photo="{{ $appointment['task']['photo'] ?? 'not_needed' }}"
                                                                                                                                                        {{ $to_does->contains(function ($do) use ($appointment, $phase, $customer) {
                                                                                                                                                            return $do->customer_id == $customer['customer_id'] &&
                                                                                                                                                                $do->phase_id == $phase['phase_id'] &&
                                                                                                                                                                $do->product_id == $appointment['product_id'] &&
                                                                                                                                                                $do->activities_id == $appointment['task']['id'] &&
                                                                                                                                                                $do->type == 'main' &&
                                                                                                                                                                $do->done == true;
                                                                                                                                                        }) ? 'checked disabled' : '' }}>
                                                                                                                                                    <span class="vs-checkbox">
                                                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                                                                        </span>
                                                                                                                                                    </span>
                                                                                                                                                    <h5>Hauptaufgabe: {{ $appointment['task']['title'] }}</h5>
                                                                                                                                                    
                                                                                                                                                </div>
                                                                                                                                            </fieldset>
                                                                                                                                            <fieldset>  
                                                                                                                                                 <ul style="    list-style: none;
                                                                                                                                                                border: 2px solid #8fc73e;
                                                                                                                                                                border-radius: 16px;
                                                                                                                                                                padding: 9px;
                                                                                                                                                                margin: 14px 11px 5px 96px;"> 
                                                                                                                                                    <li class="ml-2 mt-1" style="    border: 2px dotted #95c94a;  padding: 10px; border-radius: 20px;"><strong> <i class="feather icon-file primary"></i> Beschreibung:</strong>{{ strip_tags($appointment['task']['description'] ) }}</li>
                                                                                                                                                    <li class="ml-2 mt-1" style="    border: 2px dotted #95c94a;  padding: 10px; border-radius: 20px;"><strong> <i class="feather icon-file primary"></i> Notiz:</strong> {{ strip_tags($appointment['title'] ) }}: {{ strip_tags($appointment['description']) }}</li>
                                                                                                                                                    <div class="d-flex mt-1 mb-1">
                                                                                                                                                        <li class="ml-2 mt-1"><strong> <i class="feather icon-calendar primary"></i> Termindatum:</strong> {{ \Carbon\Carbon::parse($appointment['start_date'])->isoFormat('DD.MM.YYYY') }} - {{ \Carbon\Carbon::parse($appointment['end_date'])->isoFormat('DD.MM.YYYY') }}</li>
                                                                                                                                                        <li class="ml-2 mt-1"><strong> <i class="feather icon-clock primary"></i> Besuchzeiten:</strong> {{ \Carbon\Carbon::parse($appointment['start_time'])->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment['end_time'])->format('H:i') }}</li>
                                                                                                                                                    </div>
                                                                                                                                                    <li class="ml-2 mt-1"><strong> <i class="feather icon-file primary"></i> Berichtsabgabetermin:</strong> {{ \Carbon\Carbon::parse($appointment['report_date'])->isoFormat('DD.MM.YYYY') }} 
                                                                                                                                                    <div class="chip mb-0 ml-2"> 
                                                                                                                                                        <div class="chip-body warning">
                                                                                                                                                            <span class="chip-text" data-value="Frontend">
                                                                                                                                                                <i class="feather icon-clock primary"></i>
                                                                                                                                                                Verbleibende Tage: {{ \Carbon\Carbon::parse($appointment['report_date'])->diffInDays(\Carbon\Carbon::parse($appointment['end_date'])) }} 
                                                                                                                                                            </span> 
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    </li>
                                                                                                                                                    <li class="ml-2 mt-1"><strong> <i class="feather icon-image primary"></i> Foto benötigt:</strong> {{ $appointment['task']['photo'] == 'needed' ? 'Ja' : 'Nein' }}  
                                                                                                                                                        @if($appointment['task']['photo']=="needed")  
                                                                                                                                                            <div class="chip mb-0 ml-2">
                                                                                                                                                                <a href=""
                                                                                                                                                                    class="photoDone"
                                                                                                                                                                      data-customer="{{ $customer['customer_id'] }}"
                                                                                                                                                                    data-product="{{ $appointment['product_id'] }}"
                                                                                                                                                                    data-address="{{ $customer['address_no'] }}"
                                                                                                                                                                    data-phase="{{ $phase['phase_id'] }}"
                                                                                                                                                                    data-task="{{ $appointment['task']['id'] }}"
                                                                                                                                                                    data-photo="{{ $appointment['task']['photo'] ?? 'not_needed'}}"  >
                                                                                                                                                                    <div class="chip-body">
                                                                                                                                                                        <span class="chip-text" data-value="Frontend"><i class="feather icon-image primary"></i> Foto hochladen</span>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div> 
                                                                                                                                                        @endif
                                                                                                                                                    </li>

                                                                                                                                                </ul>
                                                                                                                                            </fieldset>

                                                                                                                                            <!-- Sub-Tasks -->
                                                                                                                                            <ul style="list-style: none;">
                                                                                                                                                @foreach ($appointment['task']['sub_tasks'] as $subTask)
                                                                                                                                                    <li>
                                                                                                                                                        <fieldset>
                                                                                                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                                                                <input type="checkbox" value="false" name="doneSubTask" id="doneSubTask"
                                                                                                                                                                    data-customer="{{ $customer['customer_id'] }}"
                                                                                                                                                                    data-product="{{ $appointment['product_id'] }}"
                                                                                                                                                                    data-address="{{ $customer['address_no'] }}"
                                                                                                                                                                    data-phase="{{ $phase['phase_id'] }}"
                                                                                                                                                                    data-task="{{ $appointment['task']['id'] }}"
                                                                                                                                                                    data-sub-task="{{ $subTask['sub_task_id'] }}"
                                                                                                                                                                    data-photo="{{ $subTask['photo'] ?? 'not_needed' }}"
                                                                                                                                                                    {{ $to_does->contains(function ($do) use ($subTask, $appointment, $customer, $phase) {
                                                                                                                                                                        return $do->customer_id == $customer['customer_id'] &&
                                                                                                                                                                            $do->phase_id == $phase['phase_id'] &&
                                                                                                                                                                            $do->product_id == $appointment['product_id'] &&
                                                                                                                                                                            $do->activities_id == $appointment['task']['id'] &&
                                                                                                                                                                            $do->sub_task_id == $subTask['sub_task_id'] &&
                                                                                                                                                                            $do->type == 'sub' &&
                                                                                                                                                                            $do->done == true;
                                                                                                                                                                    }) ? 'checked disabled' : '' }}>
                                                                                                                                                                <span class="vs-checkbox">
                                                                                                                                                                    <span class="vs-checkbox--check">
                                                                                                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                                                                                                    </span>
                                                                                                                                                                </span>
                                                                                                                                                                <h6>Teilaufgabe: {{ $subTask['task_title'] }}</h6>
                                                                                                                                                                
                                                                                                                                                            </div>
                                                                                                                                                        </fieldset>
                                                                                                                                                         <fieldset>  
                                                                                                                                                            <ul style="list-style: none;">
                                                                                                                                                                <li class="ml-2 mt-1" style="    border: 2px dotted #95c94a;  padding: 10px; border-radius: 20px;"><strong> <i class="feather icon-file primary"></i> Beschreibung:</strong> {{ strip_tags($subTask['description'] ) }}</li> 
                                                                                                                                                                 
                                                                                                                                                                <li class="ml-2 mt-1"><strong> <i class="feather icon-clock primary"></i> Dauer:</strong> {{ $subTask['duration'] }} {{ $subTask['duration_type'] }} </li> 
                                                                                                                                                                  
                                                                                                                                                                <li class="ml-2 mt-1"><strong> <i class="feather icon-image primary"></i> Foto benötigt:</strong> {{ $subTask['photo']  == 'needed' ? 'Ja' : 'Nein' }}  
                                                                                                                                                                    @if($subTask['photo']=="needed")  
                                                                                                                                                                        <div class="chip mb-0 ml-2">
                                                                                                                                                                            <a href=""
                                                                                                                                                                                class="photoDone"
                                                                                                                                                                                 data-customer="{{ $customer['customer_id'] }}"
                                                                                                                                                                                data-product="{{ $appointment['product_id'] }}"
                                                                                                                                                                                data-address="{{ $customer['address_no'] }}"
                                                                                                                                                                                data-phase="{{ $phase['phase_id'] }}"
                                                                                                                                                                                data-task="{{ $appointment['task']['id'] }}"
                                                                                                                                                                                data-sub-task="{{ $subTask['sub_task_id'] }}"
                                                                                                                                                                                data-photo="{{ $subTask['photo'] ?? 'not_needed'}}"  >
                                                                                                                                                                                <div class="chip-body">
                                                                                                                                                                                    <span class="chip-text" data-value="Frontend"><i class="feather icon-image primary"></i> Foto hochladen</span>
                                                                                                                                                                                </div>
                                                                                                                                                                            </a>
                                                                                                                                                                        </div> 
                                                                                                                                                                    @endif
                                                                                                                                                                </li>

                                                                                                                                                            </ul>
                                                                                                                                                        </fieldset>
                                                                                                                                                    </li>
                                                                                                                                                @endforeach
                                                                                                                                            </ul>
                                                                                                                                        </li>
                                                                                                                                    @endforeach
                                                                                                                                </ul>
                                                                                                                            @endforeach
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        @endforeach
                                                                                                    </div>

                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </section>

                                                                            
                                                                                


                                                                            <!-- Task Completion Modal -->
                                                                        <div class="modal fade" id="doneModal" tabindex="-1" role="dialog" aria-labelledby="doneModalTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="doneModalTitle">Aufgabenerledigungsmodal</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <input type="hidden" name="customer_id" value="">
                                                                                        <input type="hidden" name="product_id" value="">
                                                                                        <input type="hidden" name="address_no" value="">
                                                                                        <input type="hidden" name="phase_id" value="">
                                                                                        <input type="hidden" name="activities_id" value="">
                                                                                        <input type="hidden" name="sub_task_id" value="">
                                                                                        <input type="hidden" name="type" value="main">
                                                                                        <input type="hidden" name="photo" value="">
                                                                                        <input type="hidden" name="contact_person" value="{{$current_user->id}}">
                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Datum</span>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <div class="position-relative has-icon-left">
                                                                                                        <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
                                                                                                        <div class="form-control-position">
                                                                                                            <i class="feather icon-calendar"></i>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Verfasser</span>
                                                                                                </div>
                                                                                                <div class="col-md-8">
                                                                                                    <div class="position-relative has-icon-left">
                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                            <div class="avatar mr-1">
                                                                                                                <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                                                                            </div>
                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                {{ $current_user->name }} {{ $current_user->lastname }}
                                                                                                            </label> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="form-group">
                                                                                            <label for="responsible_person">Verantwortlicher</label>
                                                                                            <select name="responsible_person" class="form-control select2" style="width:100%;">
                                                                                                <option></option>
                                                                                                @foreach($employees as $contact)
                                                                                                    <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <label for="responsible_person">Der Out-Source-Typ</label>

                                                                                            <div class="card-body"> 
                                                                                            <ul class="list-unstyled mb-0">
                                                                                                <li class="d-inline-block mr-2">
                                                                                                    <fieldset>
                                                                                                        <div class="custom-control custom-radio">
                                                                                                            <input type="radio" class="custom-control-input internal" checked name="outside_type" id="internal" checked="">
                                                                                                            <label class="custom-control-label" for="internal">Intern</label>
                                                                                                        </div>
                                                                                                    </fieldset>
                                                                                                </li>
                                                                                                <li class="d-inline-block mr-2">
                                                                                                    <fieldset>
                                                                                                        <div class="custom-control custom-radio">
                                                                                                            <input type="radio" class="custom-control-input external" name="outside_type" id="external">
                                                                                                            <label class="custom-control-label" for="external">Extern</label>
                                                                                                        </div>
                                                                                                    </fieldset>
                                                                                                </li> 
                                                                                            </ul>
                                                                                        </div>
                                                                                        </div>
                                                                                        <div class="form-group outside_company">
                                                                                            <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                                                                            <select name="outside_company" class="form-control select2 " style="width:100%;"> 
                                                                                                <option></option>
                                                                                                @foreach($outside as $out)
                                                                                                    <option value="{{ $out->id }}">{{ $out->company_name }} </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>

                                                                                        <div class="form-group outside_service ">
                                                                                            <label for="outside_service">Ausführende</label>
                                                                                            <select name="outside_service" class="form-control select2" style="width:100%;">
                                                                                                <option></option> 
                                                                                                @foreach($employees as $contact)
                                                                                                    <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>

                                                                                        <div class="form-group photo_section">
                                                                                            <label for="photo_section">Foto</label>
                                                                                                <form action="{{ route('customer.upload') }}" method="POST" class="dropzone" id="file-dropzone" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                                                    @csrf
                                                                                                    <input type="hidden" name="customer_id" value=" ">
                                                                                                    <input type="hidden" name="address_no" value=" ">
                                                                                                    <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                                                    <input type="hidden" name="stage_id" id="stage_id" value="Montage"> 
                                                                                                    <input type="hidden" name="phase_id" value="">
                                                                                                    <input type="hidden" name="activities_id" value="">
                                                                                                    <input type="hidden" name="sub_task_id" value="">
                                                                                                </form>
                                                                                        </div>
                                                                                            
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                        <button type="button" class="btn btn-primary" id="save-task-btn">Speichern</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Sub Task Modal -->
                                                                        <div class="modal fade" id="doneSubTaskModal" tabindex="-1" role="dialog" aria-labelledby="doneSubTaskModalTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="doneSubTaskModalTitle">Unteraufgabe</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <input type="hidden" name="customer_id" value="">
                                                                                        <input type="hidden" name="product_id" value="">
                                                                                        <input type="hidden" name="address_no" value="">
                                                                                        <input type="hidden" name="phase_id" value="">
                                                                                        <input type="hidden" name="activities_id" value="">
                                                                                        <input type="hidden" name="sub_task_id" value="">
                                                                                        <input type="hidden" name="type" value="sub">
                                                                                        <input type="hidden" name="photo" value="">
                                                                                        <input type="hidden" name="contact_person" value="{{$current_user->id}}"> 
                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Datum</span>
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <div class="position-relative has-icon-left">
                                                                                                        <input type="date" class="form-control" name="done_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" placeholder="Datum" data-np-intersection-state="visible">
                                                                                                        <div class="form-control-position">
                                                                                                            <i class="feather icon-calendar"></i>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                    
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Verfasser</span>
                                                                                                </div>
                                                                                                <div class="col-md-8"> 
                                                                                                    <div class="position-relative has-icon-left">
                                                                                                        <div class="photo" style="display: flex; align-items: center;">
                                                                                                            <div class="avatar mr-1">
                                                                                                                <img src="{{ asset('images/employee/'.$current_user->image) }}" alt="{{ $current_user->name }}" height="32" width="32">
                                                                                                            </div>
                                                                                                            <label for="avatar" class="mt-0" style="font-size:14px">
                                                                                                                {{ $current_user->name }} {{ $current_user->lastname }}
                                                                                                            </label>
                                                                                                            <input type="hidden" name="contact_person" value="13" class="form-control">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="form-group">
                                                                                            <label for="responsible_person">Verantwortlicher</label>
                                                                                            <select name="responsible_person" class="form-control select2" style="width:100%;">
                                                                                                @foreach($employees as $contact)
                                                                                                    <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="form-group">
                                                                                            <label for="responsible_person">Der Out-Source-Typ</label>

                                                                                            <div class="card-body"> 
                                                                                            <ul class="list-unstyled mb-0">
                                                                                                <li class="d-inline-block mr-2">
                                                                                                    <fieldset>
                                                                                                        <div class="custom-control custom-radio">
                                                                                                            <input type="radio" class="custom-control-input subinternal" name="outside_types" id="subinternal" checked="">
                                                                                                            <label class="custom-control-label" for="subinternal">Intern</label>
                                                                                                        </div>
                                                                                                    </fieldset>
                                                                                                </li>
                                                                                                <li class="d-inline-block mr-2">
                                                                                                    <fieldset>
                                                                                                        <div class="custom-control custom-radio">
                                                                                                            <input type="radio" class="custom-control-input subexternal" name="outside_types" id="subexternal">
                                                                                                            <label class="custom-control-label" for="subexternal">Extern</label>
                                                                                                        </div>
                                                                                                    </fieldset>
                                                                                                </li> 
                                                                                            </ul>
                                                                                        </div>
                                                                                        </div>
                                                                                        <div class="form-group outside_company">
                                                                                            <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                                                                            <select name="outside_company" class="form-control " style="width:100%;">
                                                                                                @foreach($outside as $out)
                                                                                                    <option value="{{ $out->id }}">{{ $out->company_name }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>

                                                                                        <div class="form-group outside_service">
                                                                                            <label for="outside_service">Ausführende</label>
                                                                                            <select name="outside_service" class="form-control " style="width:100%;">
                                                                                                @foreach($employees as $contact)
                                                                                                    <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                            
                                                                                        <div class="form-group photo_section_sub">
                                                                                            <label for="photo_section_sub">Foto</label>
                                                                                                <form action="{{ route('customer.upload') }}" method="POST" class="dropzone" id="file-dropzone" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                                                    @csrf
                                                                                                    <input type="hidden" name="customer_id" value=" ">
                                                                                                    <input type="hidden" name="address_no" value=" ">
                                                                                                    <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                                                    <input type="hidden" name="stage_id" id="stage_id" value="Montage"> 
                                                                                                </form>
                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                        <button type="button" class="btn btn-primary" id="save-sub-task-btn">Speichern</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                        <!-- photo Modal  --> 
                                                                        <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="photoModalTitle">Foto hinzufügen</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <input type="hidden" name="product_id" value="">
                                                                                        <input type="hidden" name="address_no" value="">
                                                                                        <input type="hidden" name="phase_id" value="">
                                                                                        <input type="hidden" name="activities_id" value="">
                                                                                        <input type="hidden" name="sub_task_id" value="">
                                                                                        <input type="hidden" name="type" value="main">
                                                                                        <input type="hidden" name="photo" value="">
                                                                                        <input type="hidden" name="contact_person" value="{{$current_user->id}}">
                                                                                        <div class="form-group photo_section">
                                                                                            <label for="photo_section">Foto</label>
                                                                                            <form action="{{ route('customer.upload') }}" method="POST" class="dropzone" id="file-dropzone" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                                                @csrf
                                                                                                <input type="hidden" name="customer_id" value="">
                                                                                                <input type="hidden" name="address_no" value="">
                                                                                                <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                                                <input type="hidden" name="stage_id" id="stage_id" value="Montage">
                                                                                                <input type="hidden" name="phase_id" value="">
                                                                                                <input type="hidden" name="activities_id" value="">
                                                                                                <input type="hidden" name="sub_task_id" value=""> 
                                                                                            </form>
                                                                                        </div>

                                                                                        <div class="photo" id="photo_image">
                                                                                            <div class="row mt-2">
                                                                                                <!-- Images will be dynamically loaded here by AJAX -->
                                                                                            </div>
                                                                                        </div>

                                                                                    
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                            <!-- Employee Task Modal: Start  -->
                                                                        <div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalTitle" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable  modal-lg " role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="employeeModalTitle">Mitarbeiter einladen</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <input type="hidden" name="appointment_id" value="">
                                                                                        <input type="hidden" name="phase_id" value="">
                                                                                        <input type="hidden" name="activity_id" value=""> 
                                                                                        <table class="table table-hover-animation mb-0">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th colspan="2">ENGAGIERTE MITARBEITER</th>   
                                                                                                    
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                                <tr>
                                                                                                    
                                                                                                </tr> 
                                                                                            </tbody>
                                                                                        </table>
                                                                                        <form id="addEmployeeForm">
                                                                                            @csrf
                                                                                        <div class="col-md-12 col-12">
                                                                                            <div class="text-bold-600 font-medium-2">
                                                                                                MITARBEITER HINZUFÜGEN
                                                                                            </div>
                                                                                            
                                                                                            @php
                                                                                                use Carbon\Carbon;
                                                                                            @endphp

                                                                                            <fieldset class="form-group">
                                                                                                <select class="form-control select2" id="basicSelect" name="employee[]" multiple style="width:100%;">
                                                                                                    @foreach ($employees as $emp)
                                                                                                        @php
                                                                                                            // Find the leave record for this employee, if it exists
                                                                                                            $leave = $leaves->firstWhere('emp_id', $emp->id);
                                                                                                            $leaveExists = !is_null($leave);
                                                                                                            $endDate = $leaveExists ? Carbon::parse($leave->end_date)->format('Y-m-d') : null;

                                                                                                            // Find the representative if the employee is on leave
                                                                                                            $representer = $representers->firstWhere('employee_id', $emp->id);
                                                                                                            $representativeInfo = $representer ? "{$representer->name} - {$representer->lastname}: {$representer->department_name} - {$representer->position}" : '';
                                                                                                        @endphp
                                                                                                        <option value="{{ $emp->id }}" 
                                                                                                            data-icon="{{ $leaveExists ? 'fa fa-minus-circle danger' : '' }}"
                                                                                                            data-name="{{ $emp->name }} {{ $emp->lastname }}"
                                                                                                            data-leave-end="{{ $endDate ? 'Till ' . $endDate : '' }}"
                                                                                                            data-representative="{{ $representativeInfo }}">
                                                                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </fieldset>



                                                                                        </div>
                                                                                        <div class="col-md-12 col-12">
                                                                                                <button type="button" class="btn btn-primary" id="add_employee_task" > i hinzufügen</button>  
                                                                                        </div>

                                                                                        </form>

                                                                                    
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                            <!-- Employee Task Modal: end  --> 
                                                                            <!-- Upcomming Tasks: Start --> 
                                                                        <div class="default-collapse collapse-bordered">
                                                                            <div class="card collapse-header">
                                                                                <div id="headingCollapsefuture" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#collapsefuture" aria-expanded="false" aria-controls="collapsefuture">
                                                                                    <span class="lead collapse-title col-12">
                                                                                        <div class="divider divider-primary">
                                                                                            <div class="divider-text"><h2>Zukünftige Aufgabe</h2></div>
                                                                                        </div>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="collapsefuture" role="tabpanel" aria-labelledby="headingCollapsefuture" class="collapse" style="">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <ul class="todo-task-list-wrapper media-list" style="list-style:none;">
                                                                                                <li class="todo-item" data-toggle="modal" data-target="#editTaskModal">
                                                                                                    <div class="todo-title-wrapper d-flex justify-content-between mb-50">
                                                                                                        <div class="todo-title-area d-flex align-items-center">
                                                                                                            <div class="title-wrapper d-flex">
                                                                                                                <div class="vs-checkbox-con">
                                                                                                                    <input type="checkbox">
                                                                                                                    <span class="vs-checkbox vs-checkbox-sm">
                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                                        </span>
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                                <h6 class="todo-title mt-50 mx-50">Fix Responsiveness 💻</h6>
                                                                                                            </div>
                                                                                                            <div class="chip-wrapper">
                                                                                                                <div class="chip mb-0">
                                                                                                                    <div class="chip-body">
                                                                                                                        <span class="chip-text" data-value="Frontend"><span class="bullet bullet-primary bullet-xs"></span> Frontend</span>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="float-right todo-item-action d-flex">
                                                                                                            <a class='todo-item-info mr-1'><i class="feather icon-info" style=" font-size: 20px;"></i></a>
                                                                                                            <a class='todo-item-favorite mr-1'><i class="feather icon-star star" style=" font-size: 20px;"></i></a>
                                                                                                            <a class='todo-item-delete'><i class="feather icon-trash delete" style=" font-size: 20px;"></i></a>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <p class="todo-desc truncate mb-0">Jelly topping toffee bear claw. Sesame snaps lollipop macaroon croissant cheesecake pastry cupcake.</p>
                                                                                                </li>
                                                                                                    
                                                                                            </ul>

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                            <!-- Upcomming Tasks: end --> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
    
                                                    </div>
                                                    <div class="tab-pane" id="profile-fill" role="tabpanel" aria-labelledby="profile-tab-fill"> 
                                                            <section>
                                                                <div class="col-12">
                                                                    <div class="card">
                                                                        <div class="card-header"></div>
                                                                        <div class="card-body">
                                                                            <section id="basic-examples">
                                                                                <div class="row">
                                                                                    <div class="col-12">
                                                                                        <div class="card">
                                                                                            <div class="card-content">
                                                                                                <div class="card-body">
                                                                                                    <div class="cal-category-bullets d-none">
                                                                                                        <div class="bullets-group-1 mt-2">
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
                                                                                                    <div id='fc-default'></div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- calendar Modal starts-->
                                                                                <div class="modal fade text-left modal-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal" aria-modal="true">
                                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h4 class="modal-title text-text-bold-600" id="cal-modal">Add Event</h4>
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">×</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <form action="#">
                                                                                                <div class="modal-body">
                                                                                                    <div class="d-flex justify-content-between align-items-center add-category">
                                                                                                        <div class="chip-wrapper"></div>
                                                                                                        <div class="label-icon pt-1 pb-2 dropdown calendar-dropdown">
                                                                                                            <i class="feather icon-tag dropdown-toggle" id="cal-event-category" data-toggle="dropdown"></i>
                                                                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="cal-event-category">
                                                                                                                <span class="dropdown-item business" data-color="success">
                                                                                                                    <span class="bullet bullet-success bullet-sm mr-25"></span>
                                                                                                                    Business
                                                                                                                </span>
                                                                                                                <span class="dropdown-item work" data-color="warning">
                                                                                                                    <span class="bullet bullet-warning bullet-sm mr-25"></span>
                                                                                                                    Work
                                                                                                                </span>
                                                                                                                <span class="dropdown-item personal" data-color="danger">
                                                                                                                    <span class="bullet bullet-danger bullet-sm mr-25"></span>
                                                                                                                    Personal
                                                                                                                </span>
                                                                                                                <span class="dropdown-item others" data-color="primary">
                                                                                                                    <span class="bullet bullet-primary bullet-sm mr-25"></span>
                                                                                                                    Others
                                                                                                                </span>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <fieldset class="form-label-group">
                                                                                                        <input type="text" class="form-control" id="cal-event-title" placeholder="Event Title">
                                                                                                        <label for="cal-event-title">Event Title</label>
                                                                                                    </fieldset>
                                                                                                    <fieldset class="form-label-group">
                                                                                                        <input type="text" class="form-control pickadate" id="cal-start-date" placeholder="Start Date">
                                                                                                        <label for="cal-start-date">Start Date</label>
                                                                                                    </fieldset>
                                                                                                    <fieldset class="form-label-group">
                                                                                                        <input type="text" class="form-control pickadate" id="cal-end-date" placeholder="End Date">
                                                                                                        <label for="cal-end-date">End Date</label>
                                                                                                    </fieldset>
                                                                                                    <fieldset class="form-label-group">
                                                                                                        <textarea class="form-control" id="cal-description" rows="5" placeholder="Description"></textarea>
                                                                                                        <label for="cal-description">Description</label>
                                                                                                    </fieldset>
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" class="btn btn-primary cal-add-event waves-effect waves-light" disabled>
                                                                                                        Add Event</button>
                                                                                                    <button type="button" class="btn btn-primary d-none cal-submit-event waves-effect waves-light" disabled>submit</button>
                                                                                                    <button type="button" class="btn btn-flat-danger cancel-event waves-effect waves-light" data-dismiss="modal">Cancel</button>
                                                                                                    <button type="button" class="btn btn-flat-danger remove-event d-none waves-effect waves-light" data-dismiss="modal">Remove</button>
                                                                                                </div>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- calendar Modal ends-->
                                                                            </section>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </section>

                                                            
                                                    </div>
                                                    <div class="tab-pane" id="messages-fill" role="tabpanel" aria-labelledby="messages-tab-fill">
                                                        <p>
                                                            Biscuit powder jelly beans. Lollipop candy canes croissant icing chocolate cake. Cake fruitcake powder
                                                            pudding pastry.
                                                        </p>

                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Name</th>
                                                                        <th>Email</th>
                                                                        <th>User ID</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">1</th>
                                                                        <td>Leanne Graham</td>
                                                                        <td>sincere@april.biz</td>
                                                                        <td>@mdo</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">2</th>
                                                                        <td>Ervin Howell</td>
                                                                        <td>shanna@melissa.tv</td>
                                                                        <td>@fat</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">3</th>
                                                                        <td>Clementine Bauch</td>
                                                                        <td>nathan@yesenia.net</td>
                                                                        <td>@twitter</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="settings-fill" role="tabpanel" aria-labelledby="settings-tab-fill">
                                                        <p>
                                                            Tootsie roll oat cake I love bear claw I love caramels caramels halvah chocolate bar. Cotton candy
                                                            gummi
                                                            bears pudding pie apple pie cookie. Cheesecake jujubes lemon drops danish dessert I love caramels
                                                            powder.
                                                        </p>
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
    <!-- END: Content-->
 
@endsection

@push('scripts')

<script src="{{ asset('js/dropzone.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('fc-default');

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today reload',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        customButtons: {
            reload: {
                text: 'Reload',
                click: function() {
                    reloadCalendar(calendar);
                },
            },
        },
        selectable: true,
        editable: true,
        droppable: true,

        eventContent: function(arg) {
            // Custom rendering logic...
        },

        eventClick: function(info) {
            handleEditModal(info.event);
        },

        dateClick: function(info) {
            $('#start_date').val(info.dateStr);
            $('#end_date').val(info.dateStr);
            $('#add_appointment').modal('show');
        },
    });

    // Fetch appointments and render events
    fetchAppointments(calendar);

    // Render the calendar
    calendar.render();

    // Reload calendar function
    function reloadCalendar(calendar) {
        console.log('Reloading calendar...');
        calendar.destroy();
        calendar.render();
    }

    // Fetch appointments from the server
    function fetchAppointments(calendar) {
        $.ajax({
            url: "{{ route('appointments.employee.task', auth()->user()->name) }}",
            type: "GET",
            success: function(appointments) {
                if (Array.isArray(appointments)) {
                    appointments.forEach(function(appointment) {
                        calendar.addEvent({
                            id: appointment.id,
                            title: appointment.title,
                            start: appointment.start_date + 'T' + (appointment.start_time || '00:00:00'),
                            end: appointment.end_date + 'T' + (appointment.end_time || '00:00:00'),
                            extendedProps: {
                                customerName: appointment.customerName,
                                customerLastname: appointment.customerLastname,
                                priority: appointment.priority,
                                employees: appointment.employees,
                            },
                        });
                    });
                }
            },
            error: function(response) {
                console.error('Error fetching appointments:', response);
            },
        });
    }
});

</script>
 
 
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState(state) {
            if (!state.id) {
                return state.text;
            }

            // Extract data attributes from the option element
            let $element = $(state.element);
            let iconClass = $element.data('icon') || '';
            let employeeName = $element.data('name') || '';
            let leaveEndDate = $element.data('leave-end') || '';
            let representativeInfo = $element.data('representative') || '';

            // Create HTML layout for each option with multi-line structure
            var $state = $(
                `<div style="display: flex; flex-direction: column;">
                    <span style="font-weight: bold;">
                        <i class="${iconClass}" style="margin-right: 8px;"></i>${employeeName}
                    </span>
                    <span style="font-size: 12px; color: gray;">${leaveEndDate ? 'Urlaub bis: ' + leaveEndDate : ''}</span>
                    <span style="font-size: 12px; color: gray;">${representativeInfo ? 'Representative: ' + representativeInfo : ''}</span>
                </div>`
            );
            return $state;
        }
    });
</script>

 
<!-- Load the customer:  -->
  
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#customer_id').select2({
            placeholder: "Select a customer",
            allowClear: true
        });

        // Load customers from the server
        function loadCustomers() {
            $.ajax({
                url: "{{ route('appointment.customer.load') }}", // The route for getting customers
                type: "GET",
                dataType: "json",
                beforeSend: function() {
                    $('#customer_id').prop('disabled', true).html('<option>Loading...</option>');
                },
                success: function(response) {
                    if (!response || response.length === 0) {
                        alert('No customers found.');
                        $('#customer_id').empty().append('<option value=""></option>').trigger('change');
                        return;
                    }

                    // Build and append options
                    let options = '<option value=""></option>'; // Placeholder
                    response.forEach(customer => {
                        options += `<option value="${customer.id}" data-postcode="${customer.postcode}">
                                        ${customer.title}.${customer.name} ${customer.lastname} - ${customer.postcode} - ${customer.city}
                                    </option>`;
                    });
                    $('#customer_id').html(options).trigger('change');
                },
                error: function(xhr) {
                    console.error('Error loading customer data:', xhr);
                    alert('Failed to load customers. Please try again later.');
                },
                complete: function() {
                    $('#customer_id').prop('disabled', false); // Re-enable dropdown
                }
            });
        }

        // Set the postcode when a customer is selected
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const postcode = selectedOption.data('postcode') || ''; // Get postcode or empty
            $('#postcode').val(postcode); // Set the postcode
        });

        // Trigger the customer load on page load
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
    productDropdown.append('<option value="">Bitte wählen Sie ein Produkt</option>');

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
        $('.select22').select2({
            placeholder: "Select an option",
            allowClear: true
        });

        $('#phase_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var phaseId = selectedOption.data('phaseId'); 
            var productId = selectedOption.data('productId'); 
            var customerId = $('#customer_id').val(); 

            var activityDropdown = $('.activity_id');
            activityDropdown.empty();
            activityDropdown.append('<option value=""></option>');

            if (phaseId && productId && customerId) {
                $.ajax({
                    url: '/activityLoad/' + phaseId + '/' + productId + '/' + customerId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log("Received data:", data);

                        if (data.activities.length > 0) {
                            $.each(data.activities, function(key, value) {
                                activityDropdown.append('<option data-phase=' + phaseId + ' value="' + value.id + '">' + value.title + ' (Task#: ' + value.sub_task_count + ') </option>');
                            });
                        } else {
                            // Trigger SweetAlert when no activities are returned
                            Swal.fire({
                                title: "Keine Aktivitäten verfügbar",
                                text: "Das Projekt dieses Kunden wurde noch nicht gestartet. Bitte bestätigen Sie dies mit dem Projektmanagement, bevor Sie eine Aufgabe erstellen.",
                                icon: "warning",
                                confirmButtonText: "Kundenprofil besuchen"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '/customer_product_create/' + customerId + '/' + data.postcode + '/' + data.address_no;
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while fetching activities.');
                        console.error('Error fetching activities:', xhr);
                    }
                });
            } else {
                console.error('One of the required IDs (phase_id, product_id, or customer_id) is missing.');
            }
        });
    });
</script>



<script>
   $(document).ready(function() {
    // Initialize select2 for better UX
    $('.select22').select2({
        placeholder: "Select an option",
        allowClear: true
    });

    // Handle the change event on the activity_id select
    $('#activity_id').on('change', function() {
        var activityId = $(this).val(); // Get the selected activity ID

        // Fetch the associated phase ID from the #phase_id dropdown
        var phaseId = $('#phase_id').find(':selected').data('phase-id'); // Access the data-phase-id attribute from the selected option
        var customerId = $('#customer_id').val(); // Get the selected customer ID using .val()
        var productId = $('#product_id').find(':selected').data('product-id'); // Access the data-product-id attribute from the selected option
        console.log('Selected activity_id:', activityId); // Debugging line
        console.log('Selected phase_id:', phaseId); // Debugging line
        console.log('Selected customer_id:', customerId); // Debugging line
        console.log('Selected product_id:', productId); // Debugging line

        var subTaskDropdown = $('#sub_task_id'); // Select the sub-task dropdown

        // Clear the current options in the sub-task dropdown
        subTaskDropdown.empty().append('<option value="">Select Sub-Task</option>');

        if (activityId && phaseId && customerId && productId) {
            // Send an AJAX request to load sub-tasks related to the selected activity ID and phase ID
            $.ajax({
                url: '/subTaskLoad/' + phaseId + '/' + activityId + '/' + customerId + '/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Sub-tasks data received:', data); // Debugging line
                    // Populate the sub-task dropdown with the returned data
                    $.each(data, function(key, value) {
                        subTaskDropdown.append(`<option value="${value.id}">${value.task_title}</option>`);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching sub-tasks:', error); // Debugging line
                }
            });
        } else {
            console.error('Activity ID, Phase ID, Customer ID, or Product ID is missing.');
        }
    });
});

</script>


<!-- Load SubTask  -->
  

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
            placeholder: "Wählen Sie eine Option",
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

     function loadSubTask(subTaskDropdown, phaseId, activity_id) {
        $.ajax({
            url: '/subTaskLoad/' + phaseId + '/' + activity_id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                activityDropdown.empty();
                $.each(data, function(key, value) {
                    activityDropdown.append('<option value="' + value.id + '">' + value.task_title + '</option>');
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

  

        // Load phases for the new row's phase dropdown
        loadPhases($newRow.find('.phase_id'));

        // Reinitialize Select2 for all dropdowns in the new row
        initializeBasicSelect2($newRow.find('.phase_id')); // Initialize Select2 for phase_id
        initializeBasicSelect2($newRow.find('.activity_id')); // Initialize Select2 for activity_id
        initializeBasicSelect2($newRow.find('.sub_task_id')); // Initialize Select2 for Subtask
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
   
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"></script>

<!-- Map container for SweetAlert modal -->
<div id="map" style="height: 400px; width: 100%; display: none;"></div>

<script>
    function showDirections(element) {
        const customerLat = parseFloat(element.getAttribute('data-lat'));
        const customerLng = parseFloat(element.getAttribute('data-lan'));

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                // Create a unique map container ID
                const mapId = `map-${Date.now()}`;

                // Show SweetAlert with a map container and directions
                Swal.fire({
                    title: 'Wegbeschreibung Route',
                    html: `<div id="${mapId}" style="height: 400px; width: 100%;"></div>
                           <div id="eta" style="margin-top: 10px; font-weight: bold;"></div>`,
                    width: '600px',
                    showCloseButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Google Maps öffnen',
                    cancelButtonText: 'Abbrechen',
                    didOpen: () => {
                        // Initialize the map after the modal is fully rendered
                        initMap(userLat, userLng, customerLat, customerLng, mapId);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Open Google Maps with real-time directions
                        window.open(`https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${customerLat},${customerLng}&travelmode=driving`);
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
        // Initialize the map in the unique container
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
            travelMode: 'DRIVING' // Options: 'WALKING', 'BICYCLING', 'TRANSIT'
        };

        // Make the request to the Directions API
        directionsService.route(request, function (result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);

                // Extract and display ETA
                const etaElement = document.getElementById('eta');
                const duration = result.routes[0].legs[0].duration.text;
                etaElement.innerText = `Geschätzte Ankunftszeit: ${duration}`;
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

    // Attach click event to .todo-item-info elements with class "direction"
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.direction').forEach(function(el) {
            el.addEventListener('click', function(event) {
                event.preventDefault();
                showDirections(this);
            });
        });
    });
</script>


<!-- Show and hide the form of customer, employee and personal task in add new : start  -->
<script>
    document.getElementById('task_type').addEventListener('change', function() {
    // Hide all forms initially
    document.getElementById('customerFormTask').style.display = 'none';
    document.getElementById('employeeFormTask').style.display = 'none';
    // document.getElementById('personalFormTask').style.display = 'none';

    // Show the selected form based on the dropdown value
    if (this.value === 'customer') {
        document.getElementById('customerFormTask').style.display = 'block';
    } else if (this.value === 'employee') {
        document.getElementById('employeeFormTask').style.display = 'block';
    } 
    // else if (this.value === 'personal') {
    //     document.getElementById('personalFormTask').style.display = 'block';
    // }
});

</script>

<!-- Show and hide the form of customer, employee and personal task in add new : end  -->
  
<script>
    $(document).ready(function() {
        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#customerFormTask').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("appointments.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    $('#customerFormTask button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    toastr.success('Task saved successfully!');
                    $('#customerFormTask')[0].reset();
                    $('#addTaskModal').modal('hide');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var response = xhr.responseJSON;

                        if (response.status === 'error' && response.overlapping_event) {
                            var event = response.overlapping_event;

                            Swal.fire({
                                title: "Overlapping Appointment Found",
                                html: `<p><strong>Title:</strong> ${event.title}</p>
                                       <p><strong>Date:</strong> ${event.start_date} to ${event.end_date}</p>
                                       <p><strong>Time:</strong> ${event.start_time} to ${event.end_time}</p>`,
                                icon: "warning",
                                confirmButtonText: "Okay"
                            });
                        } else {
                            var errors = response.errors;
                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    toastr.error(message);
                                });
                            });
                        }
                    } else {
                        toastr.error('Failed to save task due to an unexpected error.');
                    }
                },
                complete: function() {
                    $('#customerFormTask button[type="submit"]').prop('disabled', false);
                }
            });
        });
    });
</script>



<!-- SHow the main and sub model : start  --> 
 <script>
    $(document).ready(function () {
        var currentTaskCheckbox; // Store reference to the current task checkbox (either sub-task or main task)

        // Function to show SweetAlert for incomplete sub-tasks
        function showIncompleteSubTaskAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Sub-Tasks',
                text: 'Please complete all sub-tasks before marking the main task as done.',
                confirmButtonText: 'OK'
            });
        }

        // Handle the main task checkbox
        $('input[type="checkbox"][name="doneTask"]').on('change', function () {
            if (this.checked) {
                var phaseId = $(this).data('phase');
                var taskId = $(this).data('task');

                // Check if there are incomplete sub-tasks related to the main task
                var incompleteSubTasks = $('input[type="checkbox"].doneSubTask[data-phase="' + phaseId + '"][data-task="' + taskId + '"]:not(:checked)');

                // If there are incomplete sub-tasks, show the SweetAlert and uncheck the checkbox
                if (incompleteSubTasks.length > 0) {
                    $(this).prop('checked', false); // Uncheck the main task checkbox
                    showIncompleteSubTaskAlert();  // Show alert
                } else {
                    // No incomplete sub-tasks, show the main task modal
                    currentTaskCheckbox = this;

                    var customer = $(this).data('customer');
                    var product = $(this).data('product');
                    var address = $(this).data('address');
                    var phase = $(this).data('phase');
                    var task = $(this).data('task');
                    var subTask = $(this).data('sub-task');
                    var photo = $(this).data('photo');

                    // Populate the modal with task-related data
                    $('#doneModal input[name="customer_id"]').val(customer);
                    $('#doneModal input[name="product_id"]').val(product);
                    $('#doneModal input[name="address_no"]').val(address);
                    $('#doneModal input[name="phase_id"]').val(phase);
                    $('#doneModal input[name="activities_id"]').val(task);
                    $('#doneModal input[name="sub_task_id"]').val(subTask);
                    $('#doneModal input[name="photo"]').val(photo);

                    // Show or hide the photo section based on `data-photo`
                    if (photo === 'needed') {
                        $('.photo_section').show();
                    } else {
                        $('.photo_section').hide();
                    }

                    // Open the main task modal
                    $('#doneModal').modal('show');
                }
            }
        });

        // Handle sub-task checkbox click event and show the sub-task modal
        $('input[type="checkbox"][name="doneSubTask"]').on('change', function () {
            if (this.checked) {
                currentTaskCheckbox = this;

                var customer = $(this).data('customer');
                var product = $(this).data('product');
                var address = $(this).data('address');
                var phase = $(this).data('phase');
                var task = $(this).data('task');
                var subTask = $(this).data('sub-task');
                var photo = $(this).data('photo');

                // Populate the modal with sub-task related data
                $('#doneSubTaskModal input[name="customer_id"]').val(customer);
                $('#doneSubTaskModal input[name="product_id"]').val(product);
                $('#doneSubTaskModal input[name="address_no"]').val(address);
                $('#doneSubTaskModal input[name="phase_id"]').val(phase);
                $('#doneSubTaskModal input[name="activities_id"]').val(task);
                $('#doneSubTaskModal input[name="sub_task_id"]').val(subTask);
                $('#doneSubTaskModal input[name="photo"]').val(photo);

                // Show or hide the sub-task photo section based on `data-photo`
                if (photo === 'needed') {
                    $('.photo_section_sub').show();
                } else {
                    $('.photo_section_sub').hide();
                }

                // Open the sub-task modal
                $('#doneSubTaskModal').modal('show');
            }
        });

        // Uncheck the checkbox when the modal is closed without saving
        $('#doneModal, #doneSubTaskModal').on('hidden.bs.modal', function () {
            if (currentTaskCheckbox) {
                $(currentTaskCheckbox).prop('checked', false); // Uncheck the checkbox
                currentTaskCheckbox = null; // Reset the reference
            }
        });

        // Save main task
        $('#save-task-btn').on('click', function() {
            let formData = new FormData();

            // Append necessary fields manually
            formData.append('customer_id', $('#doneModal input[name="customer_id"]').val());
            formData.append('product_id', $('#doneModal input[name="product_id"]').val());
            formData.append('address_no', $('#doneModal input[name="address_no"]').val());
            formData.append('phase_id', $('#doneModal input[name="phase_id"]').val());
            formData.append('activities_id', $('#doneModal input[name="activities_id"]').val());
            formData.append('sub_task_id', $('#doneModal input[name="sub_task_id"]').val());
            formData.append('type', 'main');
            formData.append('contact_person', $('#doneModal input[name="contact_person"]').val());
            formData.append('responsible_person', $('#doneModal select[name="responsible_person"]').val());

            let outsideType = $('input[name="outside_type"]:checked').attr('id');
            formData.append('outside_type', outsideType);
            formData.append('outside_service', $('#doneModal select[name="outside_service"]').val());
            formData.append('outside_company', $('#doneModal select[name="outside_company"]').val());

            formData.append('done_date', $('#doneModal input[name="done_date"]').val());
            formData.append('calendar', $('#doneModal input[name="calendar"]').is(':checked') ? 1 : 0);

            // Append document if uploaded
        
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('task.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#doneModal').modal('hide');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });

        // Save sub-task
        $('#save-sub-task-btn').on('click', function() {
            let formData = new FormData();

            formData.append('customer_id', $('#doneSubTaskModal input[name="customer_id"]').val());
            formData.append('product_id', $('#doneSubTaskModal input[name="product_id"]').val());
            formData.append('address_no', $('#doneSubTaskModal input[name="address_no"]').val());
            formData.append('phase_id', $('#doneSubTaskModal input[name="phase_id"]').val());
            formData.append('activities_id', $('#doneSubTaskModal input[name="activities_id"]').val());
            formData.append('sub_task_id', $('#doneSubTaskModal input[name="sub_task_id"]').val());
            formData.append('type', 'sub');
            formData.append('contact_person', $('#doneSubTaskModal input[name="contact_person"]').val());
            formData.append('responsible_person', $('#doneSubTaskModal select[name="responsible_person"]').val());

            let outsideType = $('input[name="outside_types"]:checked').attr('id');
            formData.append('outside_type', outsideType);
            formData.append('outside_service', $('#doneSubTaskModal select[name="outside_service"]').val());
            formData.append('outside_company', $('#doneSubTaskModal select[name="outside_company"]').val());

            formData.append('done_date', $('#doneSubTaskModal input[name="done_date"]').val());
            formData.append('calendar', $('#doneSubTaskModal input[name="calendar"]').is(':checked') ? 1 : 0);

            
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('task.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    toastr.success(response.message);
                    $('#doneSubTaskModal').modal('hide');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        $.each(xhr.responseJSON.errors, function(key, value) {
                            toastr.error(value[0]);
                        });
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });
    });
</script>

<!-- Task Completion Modal : End -->

 
<script>
    $(document).ready(function () {
        // Initially hide both dropdowns
        $('.outside_company').hide();
        $('.outside_service').hide();

        // Function to toggle dropdowns for the main modal based on radio button selection
        function toggleDropdowns() {
            if ($('.internal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.external').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Function to toggle dropdowns for the subtask modal based on radio button selection
        function toggleDropdown() {
            if ($('.subinternal').is(':checked')) {
                $('.outside_service').show();  // Show internal service dropdown
                $('.outside_company').hide();  // Hide external company dropdown
            } else if ($('.subexternal').is(':checked')) {
                $('.outside_company').show();  // Show external company dropdown
                $('.outside_service').hide();  // Hide internal service dropdown
            }
        }

        // Call the toggle function for the main modal when the page loads
        toggleDropdowns();

        // Call the toggle function for the subtask modal when the page loads
        toggleDropdown();

        // Attach event listener to the radio buttons in the main modal to toggle the dropdowns on change
        $('input[name="outside_type"]').change(function () {
            toggleDropdowns();
        });

        // Attach event listener to the radio buttons in the subtask modal to toggle the dropdowns on change
        $('input[name="outside_types"]').change(function () {
            toggleDropdown();
        });
    });
</script>

<!-- SHow the main and sub model : end  -->
 


<!-- PHoto Modal : start  --> 
  <script>
    $(document).ready(function() {
        $('.photoDone').on('click', function(event) {
            event.preventDefault();

            // Retrieve data attributes from the clicked link
            const customerId = $(this).data('customer');
            const productId = $(this).data('product');
            const addressNo = $(this).data('address');
            const phaseId = $(this).data('phase');
            const taskId = $(this).data('task');
            const subTaskId = $(this).data('sub-task');
            const photo = $(this).data('photo');

            // Populate hidden fields
            $('#photoModal input[name="customer_id"]').val(customerId);
            $('#photoModal input[name="product_id"]').val(productId);
            $('#photoModal input[name="address_no"]').val(addressNo);
            $('#photoModal input[name="phase_id"]').val(phaseId);
            $('#photoModal input[name="activities_id"]').val(taskId);
            $('#photoModal input[name="sub_task_id"]').val(subTaskId);
            $('#photoModal input[name="photo"]').val(photo);

            // Toggle the photo section based on `photo` value
            if (photo === 'needed') {
                $('.photo_section').show();
            } else {
                $('.photo_section').hide();
            }

            // Build the URL based on whether `sub_task_id` is present
            let url = `/task_todo_image/${customerId}/${phaseId}/${taskId}/${productId}`;
            if (subTaskId) {
                url += `/${subTaskId}`;
            }

            // AJAX request to fetch images
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // Clear the existing images
                    $('#photo_image .row').empty();

                    // Populate new images
                    if (response.length > 0) {
                        response.forEach(image => {
                            const imageHtml = `
                                <div class="col-md-2">
                                    <div class="card-content">
                                        <img class="card-img-top img-fluid open-modal" src="/images/customers/${image.image}" alt="${image.image_name}">
                                        <div class="card-body p-0">
                                            <h6 class="card-title edit_image_name mt-1" data-id="${image.id}">${image.image_name}</h6>
                                            <input type="text" data-id="${image.id}" name="image_name" value="${image.image_name}" class="form-control" style="display:none;">
                                        </div>
                                        <div class="card-footer p-0 mt-1"> 
                                            <button type="button" class="delete-photo btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="${image.id}">
                                                <i class="feather icon-trash"></i> Löschen
                                            </button> 
                                        </div>
                                    </div>
                                </div>`;
                            $('#photo_image .row').append(imageHtml);
                        });
                    } else {
                        $('#photo_image .row').append('<p>No images found.</p>');
                    }
                },
                error: function() {
                    alert('Error fetching images.');
                }
            });

            // Show the modal
            $('#photoModal').modal('show');
        });

        // Delete image
        $(document).on('click', '.btn-flat-danger', function() {
            const imageId = $(this).data('id');
            const imageCard = $(this).closest('.col-md-2'); // Adjusted selector to match the updated structure

            Swal.fire({
                title: 'Bist du sicher?',
                text: "Sie können dies nicht rückgängig machen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ja, löschen!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete image
                    $.ajax({
                        url: `/customer_image_destroy/${imageId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                imageCard.remove(); // Remove image card from the DOM
                            } else {
                                toastr.error("Error deleting image");
                            }
                        },
                        error: function(xhr) {
                            console.error("Error deleting image:", xhr);
                            alert('Error deleting image');
                        }
                    });
                }
            });
        });
    });

  </script>
<!-- PHoto Modal : End  -->

<!-- Script for showing the employees involved in the appoitment : start  -->

 

<!-- JavaScript/jQuery Script --> 
<script> 
$(document).ready(function() {
    // Open modal and load available employees when .employeemodal is clicked
    $('.employeemodal').on('click', function() {
        const appointmentId = $(this).data('appointment_id');
        const phaseId = $(this).data('phase_id');
        const activityId = $(this).data('activity-id');

        $('#employeeModal input[name="appointment_id"]').val(appointmentId);
        $('#employeeModal input[name="phase_id"]').val(phaseId);
        $('#employeeModal input[name="activity_id"]').val(activityId);

        // AJAX call to retrieve employees data
        $.ajax({
            url: `/task_todo_get_employees/${appointmentId}/${phaseId}/${activityId}`,
            method: 'GET',
            success: function(response) {
                $('#employeeModal .modal-body tbody').empty();

                response.forEach(employee => {
                    const employeeRow = `
                        <tr>
                            <td class="p-1">
                                <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="${employee.name} ${employee.lastname}" class="avatar pull-up">
                                        <img class="media-object rounded-circle" src="/images/employee/${employee.image}" alt="${employee.name}" height="30" width="30">
                                    </li>
                                    ${employee.name} ${employee.lastname}
                                </ul>
                            </td>
                        </tr>`;
                    $('#employeeModal .modal-body tbody').append(employeeRow);
                });

                $('#employeeModal').modal('show');
            },
            error: function() {
                alert('Error retrieving employee data. Please try again.');
            }
        });
    });

    // Add employee(s) on button click
    $('#add_employee_task').on('click', function() {
        const appointmentId = $('#employeeModal input[name="appointment_id"]').val();
        const phaseId = $('#employeeModal input[name="phase_id"]').val();
        const activityId = $('#employeeModal input[name="activity_id"]').val();
        const employeeIds = $('#addEmployeeForm select[name="employee[]"]').val();

        // Log to confirm activity_id is being passed
        console.log({ appointmentId, phaseId, activityId, employeeIds });

        $.ajax({
            url: '/add_employee_to_task',
            method: 'POST',
            data: {
                appointment_id: appointmentId,
                phase_id: phaseId,
                activity_id: activityId,
                employee: employeeIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response);
                $('.employeemodal[data-appointment_id="' + appointmentId + '"]').trigger('click');
            },
            error: function() {
                alert('Error adding employee(s). Please try again.');
            }
        });
    });

});

</script>

<!-- Script for showing the employees involved in the appoitment : end  -->

@endpush