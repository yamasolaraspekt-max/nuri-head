@extends('admin.layouts.app')
@section('title')
Projekt-management
@endsection
@section('style') 
       <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css')}}">
       <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/file-uploaders/dropzone.css')}}">
        <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/extensions/dropzone.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<meta name="csrf-token" content="{{ csrf_token() }}">


 <style>
    .line-through {
        text-decoration: line-through !important;
    }


    .bd {
       border-bottom: 1px solid #e7e0e0 !important;
       
    }
    .select2-selection {
       border: 2px !important;
        width: 100% !important;
        background: #efeded !important;
        height: 40px !important;
        font-size: 20px;
        align-content: center;
        font-weight: bolder;
    }
    
    #documentViewerContainer {
        position: relative;
        overflow: auto;
        text-align: center;
    }

    #documentViewer, #imageViewer {
        transition: transform 0.3s ease;
        display: block;
        margin: 0 auto;
    }

 </style>
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">PROJEKT-MANAGEMENT</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li> 
                                     <li class="breadcrumb-item"><a href="{{ url('task_todo/'.auth()->user()->name) }}">Aufgaben</a>
                                    </li> 
                                    <li class="breadcrumb-item active">Projekt
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
                <div class="row">
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header">Informationen zur Aufgabe
                                <a type="button" href="{{ url('personal/task/'.auth()->user()->name) }}" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light">Zurück</a>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap">
                                                <tbody>
                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Kunden-nummer :</span></td>
                                                        <td>{{ $data->customer_no ?? 'Not Defined' }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Kunde :</span></td>
                                                        <td>{{ $data->name }} {{$data->lastname}}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Verfasser :</span></td>
                                                        <td>
                                                            <div class="d-flex" style="align-items: center;">
                                                                <div class="avatar mr-1">
                                                                    <img src="{{ asset('images/employee/'.$data->emp_image) }}" alt="Avatar" height="32" width="32">
                                                                </div>
                                                                <span class="block text-[14px] font-medium">{{ $data->emp_name }} {{ $data->emp_lastname }}</span>
                                                            </div> 
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgaben-Fortschritt :</span></td>
                                                        <td>
                                                            <div class="progress progress-bar-primary progress-lg">
                                                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="{{ $data->progress }}" aria-valuemin="0" aria-valuemax="100" style="width:{{ $data->progress }}%"></div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgabenstatus :</span></td>
                                                          @php
                                                            $map = [
                                                                'new'        => 'Neue',
                                                                'start'      => 'Starten',
                                                                'on_going'   => 'Im Prozess',
                                                                'on_review'  => 'Kurz vor Abschluss',
                                                                'completed'  => 'Vollendet',
                                                                'pause'      => 'Pause',
                                                                'cancel'      => 'Abbrechen',
                                                            ];

                                                            $status = $map[$data->project_status] ?? 'Status unbekannt'; 
                                                        @endphp

                                                        <td>
                                                            <span class="font-medium text-secondary current-status">{{ $status}}</span>
                                                            <i class="feather icon-edit project_status" style="cursor: pointer;"></i>

                                                            <form id="project_status_form" style="display: none;">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                                <select name="project_status" class="form-control project_status_select">
                                                                    <option value="new" {{ $data->project_status == 'new' ? 'selected' : '' }}>Neue</option>
                                                                    <option value="start" {{ $data->project_status == 'start' ? 'selected' : '' }}>Starten</option>
                                                                    <option value="on_going" {{ $data->project_status == 'on_going' ? 'selected' : '' }}>Im Prozess</option>
                                                                    <option value="on_review" {{ $data->project_status == 'on_review' ? 'selected' : '' }}>Kurz vor Abschluss</option>
                                                                    <option value="completed" {{ $data->project_status == 'completed' ? 'selected' : '' }}>Vollendet</option>
                                                                    <option value="pause" {{ $data->project_status == 'pause' ? 'selected' : '' }}>Pause</option>
                                                                    <option value="cancel" {{ $data->project_status == 'cancel' ? 'selected' : '' }}>Abbrechen</option>
                                                                </select>
                                                            </form>
                                                        </td> 
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgabenpriorität :</span></td>
                                                        <td>
                                                            <span class="badge bg-danger/10 text-danger"><i class="ri-circle-fill text-[8px] me-1"></i> {{ $data->priority }}</span>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Startdatum :</span></td>
                                                        <td>{{ date('d, M Y', strtotime($data->project_start)) }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Enddatum :</span></td>
                                                        <td>{{ date('d, M Y', strtotime($data->end_date)) }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgabendauer:</span></td>
                                                        <td>
                                                            @php
                                                                $hours = floor($data->total_time);
                                                                $minutes = round(($data->total_time - $hours) * 60);
                                                            @endphp
                                                            {{ sprintf('%02d:%02d', $hours, $minutes) }} Uhr
                                                        </td>
                                                    </tr>
 

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Verantwortlichen :</span></td>
                                                        <td class="p-1">
                                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                    @foreach ($project_employees as $emp) 
                                                                        @if($emp->project_id == $data->id)
                                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $emp->name }} {{ $emp->lastname }}" class="avatar pull-up">
                                                                                <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$emp->image) }}" alt="Avatar" height="30" width="30">
                                                                            </li> 
                                                                        @endif
                                                                    @endforeach 
                                                            </ul>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Zuletzt aktualisiert am :</span></td>
                                                        <td>
                                                            <span class="text-primarytint1color font-medium">{{ date('d, M Y', strtotime($data->updated_at)) }}</span>
                                                            <span class="text-primarytint1color font-medium">{{ date('H:i', strtotime($data->updated_at)) }}</span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>

                         <div class="card">
                            <div class="card-header">
                                <h4>Anhangslisten</h4>
                            </div>
                                 
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap" id="attachmentTable">
                                                <thead>
                                                    <tr>
                                                        <th>Image Name</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Rows dynamically loaded via JavaScript -->
                                                </tbody>
                                            </table>  
                                        </div>   
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="col-xl-8 col-md-6 col-sm-12">
                        <section>
                            @foreach ($phases as $phase)
                                <div class="accordion" id="accordionExample{{ $loop->index }}">
                                    <div class="card">
                                        <div class="card-header" id="heading{{ $loop->index }}" style="padding-bottom:21px">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link collapse_card" type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                                                    <h4 style="font-weight: bold;" class="primary">
                                                        <i class="icon-toggle feather icon-plus"></i> {{ $phase->phase_name }}
                                                    </h4>
                                                </button>
                                            </h5>
                                        </div>

                                        <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#accordionExample{{ $loop->index }}">
                                            <div class="card-body"> 
                                                    <div class="accordion" id="mainTask" data-toggle-hover="true">
                                                            @foreach($tasks as $task)
                                                                    @if($task->phase_id == $phase->id)
                                                                        @php
                                                                            // Find the corresponding "main" task in $to_does
                                                                            $foundTodo = $to_does->firstWhere(function($do) use ($task, $phase) {
                                                                                return $do->phase_id == $phase->id && $do->activities_id == $task->id && $do->type == 'main';
                                                                            });
                                                                        @endphp
                                                                        <div class="collapse-border-item collapse-header card collapse-bordered">
                                                                            <div class="card-header" id="heading200" data-toggle="collapse" role="button" data-target="#phase_collaps{{$loop->index}}" aria-expanded="false" aria-controls="collapse200">
                                                                                <span class="lead collapse-title">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table">
                                                                                            <tr>
                                                                                                <td>
                                                                                                    <fieldset>
                                                                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                            <input type="checkbox" 
                                                                                                                value="false" 
                                                                                                                name="{{ $foundTodo && $foundTodo->done == true ? 'unDoneTask' : 'doneTask' }}" 
                                                                                                                id="{{ $foundTodo && $foundTodo->done == true ? 'unDoneTask' : 'doneTask' }}"
                                                                                                                data-customer="{{ $customer_data }}"
                                                                                                                data-product="{{ $product_data }}"
                                                                                                                data-alternative="{{ $alternative_data }}"
                                                                                                                data-phase="{{ $phase->id }}"
                                                                                                                data-task="{{ $task->id }}"
                                                                                                                data-project-id="{{ $data->id }}"
                                                                                                                {{ $foundTodo && $foundTodo->done == true ? 'checked' : '' }}>
                                                                                                            <span class="vs-checkbox">
                                                                                                                <span class="vs-checkbox--check">
                                                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                                                </span>
                                                                                                            </span>
                                                                                                        </div>
                                                                                                    </fieldset>  
                                                                                                </td>
                                                                                                <td>
                                                                                                <h4  class="{{ $foundTodo && $foundTodo->done == true ? 'line-through' : '' }}" >{{ $task->title }}</h4>
                                                                                                    <p>{{$task->description}}</p>
                                                                                                </td>

                                                                                                @if($foundTodo && $foundTodo->done == true)
                                                                                                    <td> {{ \Carbon\Carbon::parse($foundTodo->done_date)->isoFormat('DD.MM.YY')}} <br> <small>{{ \Carbon\Carbon::parse($foundTodo->more_time)->isoFormat('HH:MM')}} </small></td>
                                                                                                    <td>
                                                                                                    
                                                                                                        <div class="avatar mr-1">
                                                                                                            <img src="{{ asset('images/employee/'.$foundTodo->cimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->cname}} {{ $foundTodo->clastname}}"> 
                                                                                                        </div> 
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        @if($foundTodo->responsible_person != Null)
                                                                                                        <div class="avatar mr-1">
                                                                                                            <img src="{{ asset('images/employee/'.$foundTodo->rimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->rname}} {{ $foundTodo->rlastname}} "> 
                                                                                                        </div> 
                                                                                                        @endif
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <div class="avatar mr-1">
                                                                                                            @if($foundTodo->outside_service != Null)
                                                                                                            <img src="{{ asset('images/employee/'.$foundTodo->osimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->osname}} {{ $foundTodo->oslastname}} "> 
                                                                                                            @elseif($foundTodo->outside_company != Null)
                                                                                                            <img src="{{ asset('images/gender/users.png')}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundTodo->company_name}} - {{ $foundTodo->admin_name}} ">  
                                                                                                            @endif
                                                                                                        </div> 
                                                                                                    </td> 
                                                                                                @else
                                                                                                    <td></td>
                                                                                                    <td></td>
                                                                                                    <td></td>
                                                                                                    <td></td>
                                                                                                @endif
                                                                                            </tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </span>
                                                                            </div>

                                                                            <div id="phase_collaps{{$loop->index}}" class="collapse" aria-labelledby="heading200" data-parent="#mainTask">
                                                                                <div class="card-body">
                                                                                    <div class="table-responsive">
                                                                                        <table class="table   mb-0">
                                                                                            <thead>
                                                                                                <tr>
                                                                                                    <th></th>
                                                                                                    <th scope="col">Erlidigt</th>
                                                                                                    <th scope="col">Bezeichnung</th> 
                                                                                                    <th scope="col">Datum</th>
                                                                                                    <th>Verfasser</th>
                                                                                                    <th scope="col">Verantwortlich</th>
                                                                                                    <th scope="col">Ausführende</th> 
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody> 
                                                                                                @foreach ($activities as $sub_task) 
                                                                                                        @if($sub_task->task_id == $task->id) 
                                                                                                            @php
                                                                                                                // Find the corresponding "sub" task in $to_does
                                                                                                                $foundSubTodo = $to_does->firstWhere(function($do) use ($sub_task, $phase) {
                                                                                                                    return $do->phase_id == $phase->id 
                                                                                                                        && $do->activities_id == $sub_task->task_id // Match the activity (task) ID
                                                                                                                        && $do->sub_task_id == $sub_task->id        // Match the sub-task ID
                                                                                                                        && $do->type == 'sub';                      // Ensure it's a sub-task
                                                                                                                });

                                                                                                                // Check if the sub-task is found 
                                                                                                            @endphp
                                                                                                            <tr>
                                                                                                                <td><i class="feather icon-corner-down-right"></i></td>
                                                                                                                <td  >
                                                                                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                        <input type="checkbox" class="{{ $foundSubTodo && $foundSubTodo->done == true ? 'undoneSub' : 'doneSubTask' }}" 
                                                                                                                            name="{{ $foundSubTodo && $foundSubTodo->done == true ? 'undoneSub' : 'doneSubTask' }}"
                                                                                                                            value="false"
                                                                                                                            data-customer="{{ $customer_data }}"
                                                                                                                            data-product="{{ $product_data }}"
                                                                                                                            data-alternative="{{ $alternative_data }}"
                                                                                                                            data-phase="{{ $phase->id }}"
                                                                                                                            data-task="{{ $task->id }}"
                                                                                                                            data-sub-task="{{ $sub_task->id }}"
                                                                                                                            data-project-id="{{ $data->id }}" 
                                                                                                                            {{ $foundSubTodo && $foundSubTodo->done == true ? 'checked' : '' }}>
                                                                                                                        <span class="vs-checkbox">
                                                                                                                            <span class="vs-checkbox--check">
                                                                                                                                <i class="vs-icon feather icon-check"></i>
                                                                                                                            </span>
                                                                                                                        </span> 
                                                                                                                    </div>  
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <h5 class="{{ $foundSubTodo && $foundSubTodo->done == true ? 'line-through' : '' }}">
                                                                                                                    {{ $sub_task->task_title }} 
                                                                                                                    </h5>
                                                                                                                    <p>
                                                                                                                        {{ $sub_task->description }}
                                                                                                                    </p>
                                                                                                                    
                                                                                                                </td> 
                                                                                                                @if($foundSubTodo) 
                                                                                                                    <td> 
                                                                                                                            <small>
                                                                                                                                {{ $foundSubTodo->done_date ? \Carbon\Carbon::parse($foundSubTodo->done_date)->isoFormat('DD.MM.YY') : 'Not Defined' }} <br>
                                                                                                                                <small>{{ $foundSubTodo->done_date ? \Carbon\Carbon::parse($foundSubTodo->done_date)->diffForHumans() : 'Not Defined' }}</small>
                                                                                                                            </small> 
                                                                                                                    </td>  
                                                                                                        
                                                                                                                        <td style="display: flex; flex-wrap: nowrap; align-content: center;  align-items: center;  "> 
                                                                                                                        
                                                                                                                        
                                                                                                                            <div class="avatar mr-1">
                                                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->cimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->cname }} {{ $foundSubTodo->clastname }}" id="contact_person_under"> 
                                                                                                                            </div>  
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <div class="avatar mr-1">
                                                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->rimage)}}" alt="" height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->rname }} {{ $foundSubTodo->rlastname }}" id="responsible_under"> 
                                                                                                                            </div> 
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <div class="avatar mr-1">
                                                                                                                                @if($foundSubTodo->outside_service != Null)
                                                                                                                                <img src="{{ asset('images/employee/'.$foundSubTodo->osimage)}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->osname}} {{ $foundSubTodo->oslastname}} "> 
                                                                                                                                @elseif($foundSubTodo->outside_company != Null)
                                                                                                                                <img src="{{ asset('images/gender/users.png')}}" alt=" " height="32" width="32" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ $foundSubTodo->company_name}} - {{ $foundSubTodo->admin_name}} ">  
                                                                                                                                @endif
                                                                                                                            </div> 
                                                                                                                        </td> 

                                                                                                                    @else
                                                                                                                        <td></td>
                                                                                                                        <td></td>
                                                                                                                        <td></td>
                                                                                                                        <td></td> 
                                                                                                                    @endif
                                                                                                            
                                                                                                            </tr>
                                                                                                        @endif
                                                                                                    @endforeach 
                                                                                                <!-- Additional rows here -->
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>  
                                                                    @endif
                                                            @endforeach
                                                             @php
                                                                    $check_status = DB::table('task_to_dos')
                                                                        ->where('customer_id', $data->customer_id)
                                                                        ->where('alternative', $data->alternative_id)
                                                                        ->where('product_id', $data->product_id)
                                                                        ->where('done', '=', 'true') 
                                                                        ->exists(); // Simplify to directly check if any record exists
                                                                @endphp

                                                                @if($check_status)
                                                        
                                                                <div class="row">
                                                                    <!-- Planzeit -->
                                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                                        <div class="card">
                                                                            <div class="card-header d-flex align-items-start pb-0">
                                                                                <div>
                                                                                    @php
                                                                                        $hours = floor($data->total_time); // Whole hours
                                                                                        $minutes = round(($data->total_time - $hours) * 60); // Convert fractional hours to minutes
                                                                                    @endphp
                                                                                    <h2 class="text-bold-700 mb-0">{{ sprintf('%02d:%02d', $hours, $minutes) }} Uhr</h2>
                                                                                    <p>Planzeit</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Ist Zeit -->
                                                                    @php
                                                                                                            
                                                                        $total_current_time = DB::table('personal_task_keys')
                                                                            ->where('personal_task_id', $data->id)
                                                                            ->pluck('more_time');

                                                                        // Convert each time to minutes and sum them
                                                                        $total_minutes = $total_current_time->reduce(function ($carry, $time) {
                                                                            $parts = explode(':', $time); // Ensure $time is split into parts
                                                                            $hours = (int) ($parts[0] ?? 0); // Convert hours to integer
                                                                            $minutes = (int) ($parts[1] ?? 0); // Convert minutes to integer
                                                                            $seconds = (int) ($parts[2] ?? 0); // Convert seconds to integer
                                                                            $total_time_in_minutes = ($hours * 60) + $minutes + ($seconds / 60); // Convert to total minutes
                                                                            return $carry + $total_time_in_minutes; // Sum up total minutes
                                                                        }, 0);


                                                                        // Convert total minutes to hours and minutes
                                                                        $total_hours = floor($total_minutes / 60);
                                                                        $remaining_minutes = round($total_minutes % 60);

                                                                        // Store total_minutes for reuse
                                                                        $total_hour_in_minutes = $total_minutes;
                                                                    @endphp

                                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                                        <div class="card">
                                                                            <div class="card-header d-flex align-items-start pb-0">
                                                                                <div>
                                                                                    <h2 class="text-bold-700 mb-0">{{ sprintf('%02d:%02d', $total_hours, $remaining_minutes) }} Uhr</h2>
                                                                                    <p>Ist Zeit</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Abweichung -->
                                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                                        <div class="card">
                                                                            <div class="card-header d-flex align-items-start pb-0">
                                                                                <div>
                                                                                    @php
                                                                                        // Convert $data->total_time to minutes
                                                                                        $planned_minutes = $data->total_time * 60;

                                                                                        // Calculate the deviation in minutes
                                                                                        $difference_in_minutes = $total_hour_in_minutes - $planned_minutes;

                                                                                        // Determine the sign of the deviation (positive or negative)
                                                                                        $sign = $difference_in_minutes < 0 ? '-' : '+';

                                                                                        // Convert absolute difference to hours and minutes
                                                                                        $diff_hours = floor(abs($difference_in_minutes) / 60);
                                                                                        $diff_minutes = abs($difference_in_minutes) % 60;
                                                                                    @endphp

                                                                                    <h2 class="text-bold-700 mb-0">
                                                                                        @if($sign== '-') 
                                                                                        <i class="feather icon-thumbs-up primary"></i>
                                                                                        @elseif($sign == '+')
                                                                                    <i class="feather icon-thumbs-down danger"></i> 
                                                                                        @else
                                                                                        <i class="feather icon-thumbs-up primary" ></i>
                                                                                        @endif
                                                                                        {{ $sign . sprintf('%02d:%02d', $diff_hours, $diff_minutes) }} Uhr</h2>
                                                                                    <p>Abweichung in Stunde</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-lg-3 col-sm-6 col-12">
                                                                        <div class="card">
                                                                            <div class="card-header d-flex align-items-start pb-0">
                                                                                <div>
                                                                                @php
                                                                                    // Convert $data->total_time to minutes
                                                                                    $planned_minutes = $data->total_time * 60;

                                                                                    // Calculate the deviation in minutes
                                                                                    $difference_in_minutes = $total_hour_in_minutes - $planned_minutes;

                                                                                    // Calculate percentage difference
                                                                                    $percentage = ($planned_minutes != 0) ? ($difference_in_minutes / $planned_minutes) * 100 : 0;

                                                                                    // Determine the sign of the deviation
                                                                                    $sign = $percentage < 0 ? '-' : '+';
                                                                                @endphp

                                                                                <h2 class="text-bold-700 mb-0">
                                                                                    @if($sign== '-') 
                                                                                        <i class="feather icon-thumbs-up primary"></i>
                                                                                        @elseif($sign == '+')
                                                                                    <i class="feather icon-thumbs-down danger"></i> 
                                                                                        @else
                                                                                        <i class="feather icon-thumbs-up primary" ></i>
                                                                                        @endif
                                                                                    {{ $sign . round(abs($percentage), 2) }} % 
                                                                                </h2>
                                                                                <p>Abweichung in %</p>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                                @endif
                                                        <hr>
                                                        <div class="row">
                                                            
                                                            <div class="col-sm-12">
                                                                <div class="card overflow-hidden"> 
                                                                    <div class="card-content">
                                                                        <div class="card-body"> 
                                                                            <ul class="nav nav-tabs" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home{{$phase->id}}" aria-controls="home" role="tab" aria-selected="true">Aufgabenaktivität</a>
                                                                                </li>
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile{{$phase->id}}" aria-controls="profile" role="tab" aria-selected="false">Kommentare</a>
                                                                                </li>
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents{{$phase->id}}" aria-controls="documents" role="tab" aria-selected="false">Dokumente</a>
                                                                                </li>
                                                                             
                                                                            </ul>
                                                                            <div class="tab-content">
                                                                                <div class="tab-pane active" id="home{{$phase->id}}" aria-labelledby="home-tab" role="tabpanel">
                                                                                      <div class="card notification-card" 
                                                                                            data-project-id="{{ $data->id }}" 
                                                                                            data-phase-id="{{ $phase->id }}" 
                                                                                            id="notification-card-{{ $data->id }}-{{ $phase->id }}">
                                                                                            <div class="card-header">
                                                                                                <h4 class="card-title">Notifications for Project: {{ $phase->phase_name }}</h4>
                                                                                            </div>
                                                                                            <div class="card-content">
                                                                                                <div class="card-body">
                                                                                                    <ul class="notifications-list activity-timeline timeline-left list-unstyled" 
                                                                                                        id="notifications-list-{{ $data->id }}-{{ $phase->id }}">
                                                                                                        <!-- Notifications will be dynamically added here -->
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="tab-pane " id="profile{{$phase->id}}" aria-labelledby="profile-tab" role="tabpanel">
                                                                                        <div class="row">
                                                                                        <div class="col-12">
                                                                                            <div class="card">
                                                                                                <div class="card-body">
                                                                                                    <div class="d-flex justify-content-start align-items-center mb-1">
                                                                                                        <div class="avatar mr-1">
                                                                                                            <img src="{{ asset('images/employee/'.$data->emp_image) }}" alt="avtar img holder" height="45" width="45">
                                                                                                        </div>
                                                                                                        <div class="user-page-info">
                                                                                                            <p class="mb-0">{{ $data->emp_name }} {{ $data->emp_lastname }}</p>
                                                                                                            <span class="font-small-2">{{ $data->created_at }}</span>
                                                                                                        </div> 
                                                                                                    </div>
                                                                                                    
                                                                                                    
                                                                                                    <div class="d-flex justify-content-start align-items-center mb-1"> 
                                                                                                        <div class="ml-2">
                                                                                                            @php
                                                                                                                $commentsCount = $comments_list->count();
                                                                                                                $displayLimit = 4;  
                                                                                                            @endphp

                                                                                                            <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                                                                                @foreach($comments_list->unique(fn($comment) => $comment->name . $comment->lastname)->take($displayLimit) as $comment)
                                                                                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" 
                                                                                                                        data-original-title="{{ $comment->name }} {{ $comment->lastname }}" 
                                                                                                                        class="avatar pull-up">
                                                                                                                        <img class="media-object rounded-circle" 
                                                                                                                            src="{{ asset('images/employee/'.$comment->image) }}" 
                                                                                                                            alt="Avatar" height="30" width="30">
                                                                                                                    </li>
                                                                                                                @endforeach

                                                                                                                @if($commentsCount > $displayLimit)
                                                                                                                    <li class="d-inline-block pl-50">
                                                                                                                        <span>+{{ $commentsCount - $displayLimit }} more</span>
                                                                                                                    </li>
                                                                                                                @endif
                                                                                                            </ul> 
                                                                                                        </div>
                                                                                                        <p class="ml-auto d-flex align-items-center">
                                                                                                            <i class="feather icon-message-square font-medium-2 mr-50"></i>{{ $comments_list->count() }}
                                                                                                        </p>
                                                                                                    </div>
                                                                                                <!-- Comment Section -->
                                                                                                    <div id="comment_section_{{ $data->id }}_{{ $phase->id }}"></div>
                                                                                                    
                                                                                                        <form id="comment_form">
                                                                                                            @csrf
                                                                                                            <input type="hidden" name="project_id" value="{{ $data->id }}">
                                                                                                            <input type="hidden" name="phase_id" value="{{ $phase->id }}">
                                                                                                            <fieldset class="form-label-group mb-50">
                                                                                                                <textarea class="form-control" name="comment" rows="3" placeholder="Add your comment here"></textarea>
                                                                                                                <label for="label-textarea">Kommentar</label>
                                                                                                            </fieldset>
                                                                                                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light comment-save">
                                                                                                                Kommentar hinzufügen
                                                                                                            </button>
                                                                                                        </form>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="tab-pane " id="documents{{$phase->id}}" aria-labelledby="documents-tab" role="tabpanel">
                                                                                    <div class="card" style="height: 579.688px;">
                                                                                       <div class="card-content">
                                                                                            <div class="card-body">
                                                                                                <h4 class="card-title">Dokumentlisten</h4>
                                                                                                <p class="card-text">Diese Dokumente gehören zu dieser Phase</p>
                                                                                            </div>
                                                                                              <h4 class="card-title">Project {{ $data->id }}, Phase {{ $phase->id }}</h4>

                                                                                           <ul class="list-group list-group-flush document_list"
                                                                                                id="documentList-{{ $data->id }}-{{ $phase->id }}" 
                                                                                                data-project="{{ $data->id }}" 
                                                                                                data-phase="{{ $phase->id }}" 
                                                                                                data-customer="{{ $data->customer_id }}">
                                                                                                <!-- Files for this specific project and phase will be appended here -->
                                                                                            </ul>

                                                                                            <div class="card-body">
                                                                                              
                                                                                                <form action="{{ route('project.task.attachment.store') }}" 
                                                                                                    class="dropzone dropzone-area"
                                                                                                    id="documentDropzone-{{ $data->id }}-{{ $phase->id }}" 
                                                                                                    enctype="multipart/form-data">
                                                                                                    @csrf
                                                                                                    <div class="dz-message">Click or drag files here to upload</div>
                                                                                                    <input type="hidden" name="project_id" value="{{ $data->id }}">
                                                                                                    <input type="hidden" name="phase_id" value="{{ $phase->id }}">
                                                                                                    <input type="hidden" name="customer_id" value="{{ $data->customer_id }}">
                                                                                                </form>

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
                                    </div>
                                </div>
                            @endforeach 
                        </section>
                    </div> 
                </div>

                       <!-- Modal for viewing document -->
                                                                                
                    <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewDocumentModalLabel">View Document</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe id="documentViewer" src="" width="100%" height="500px" frameborder="0" style="display: none;"></iframe>
                                    <img id="imageViewer" src="" alt="Image" style="max-width: 100%; max-height: 500px; display: none;">
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <input type="hidden" name="alternative" value="">
                                    <input type="hidden" name="phase_id" value="">
                                    <input type="hidden" name="activities_id" value="">
                                    <input type="hidden" name="sub_task_id" value="">
                                    <input type="hidden" name="project_id" value="">
                                    <input type="hidden" name="type" value="main">
                                    <input type="hidden" name="last" value="false">
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
                                        <select name="responsible_person" class="form-control select-me" style="width:100%;">
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
                                                        <input type="radio" class="custom-control-input internal" checked name="outside_types" id="internal" checked="">
                                                        <label class="custom-control-label" for="internal">Intern</label>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block mr-2">
                                                <fieldset>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input external" name="outside_types" id="external">
                                                        <label class="custom-control-label" for="external">Extern</label>
                                                    </div>
                                                </fieldset>
                                            </li> 
                                        </ul>
                                    </div>
                                    </div>
                                    <div class="form-group outside_company">
                                        <label for="outside_company">Ausführende <code>Ausgelagert</code></label>
                                        <select name="outside_company" class="form-control select-me " style="width:100%;"> 
                                            <option></option>
                                            @foreach($outside as $out)
                                                <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group outside_service ">
                                        <label for="outside_service">Ausführende</label>
                                        <select name="outside_service" class="form-control select-me" style="width:100%;">
                                            <option></option> 
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Aufgabenstatus</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <select name="done_status" id="" class="form-control mb-2 done_status_select">
                                                        <option value="complete">vollständig erledigt </option>
                                                        <option value="part">teilweise erledigt</option>
                                                        <option value="imposible">nicht erledigt</option>
                                                        <option value="unable">kann nicht erledigt werden</option>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Fortschritt</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                      <input type="range" class="form-control work_progress_status" name="work_progress">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Aufgabendauer(Zeit)</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                   <input type="time" name="more_time" class="form-control" required >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Beschreibung</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left"> 
                                                    <textarea name="reason" id="reason" cols="30" rows="10" class="form-control"></textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                    <input type="hidden" name="alternative" value="">
                                    <input type="hidden" name="phase_id" value="">
                                    <input type="hidden" name="activities_id" value="">
                                    <input type="hidden" name="sub_task_id" value="">
                                    <input type="hidden" name="project_id" value="" />
                                    <input type="hidden" name="type" value="sub">
                                    <input type="hidden" name="contact_person" value="{{$current_user->id}}">
                                    <input type="hidden" name="last" value="false">

 
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
                                        <select name="outside_company" class="form-control select2" style="width:100%;">
                                            @foreach($outside as $out)
                                                <option value="{{ $out->id }}">{{ $out->company_name }} - {{ number_format($out->price, 2) }} € </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group outside_service">
                                        <label for="outside_service">Ausführende</label>
                                        <select name="outside_service" class="form-control select2 " style="width:100%;">
                                            @foreach($employees as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->name }} {{ $contact->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Aufgabenstatus</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                    <select name="done_status" id="" class="form-control mb-2 done_status_select">
                                                        <option value="complete">vollständig erledigt </option>
                                                        <option value="part">teilweise erledigt</option>
                                                        <option value="imposible">nicht erledigt</option>
                                                        <option value="unable">kann nicht erledigt werden</option>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Fortschritt</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                      <input type="range" class="form-control work_progress_status" name="work_progress">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Aufgabendauer(Zeit)</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left">
                                                   <input type="time" name="more_time" class="form-control" required >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                              
                                    <div class="col-12"> 
                                        <div class="form-group row">
                                            <div class="col-md-4">
                                                <span>Beschreibung</span>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="position-relative has-icon-left"> 
                                                    <textarea name="reason" id="reason" cols="30" rows="10" class="form-control"></textarea>
                                                    <div class="form-control-position">
                                                        <i class="feather icon-file"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                    <button type="button" class="btn btn-primary" id="save-sub-task-btn">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </div> 
 
        <!-- Modal -->
                <div class="modal fade" id="documentCustomer" tabindex="-1" role="dialog" aria-labelledby="documentCustomerLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="documentCustomerLabel">Document Viewer</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe id="pdfFrame" class="embed-responsive-item" src="" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>


     <!-- attachment listModal -->
        <div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" id="filePreviewContent">
                        <!-- Content dynamically loaded -->
                    </div>
                </div>
            </div>
        </div>
@endsection


        
    @push('scripts')
     
    <script src="{{ asset('js/select2.min.js') }}"></script>
   <script src="{{ asset('app-assets/vendors/js/extensions/dropzone.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/extensions/dropzone.js') }}"></script> 
   

        <script>
            $(document).ready(function () {
                $('div[id^="accordionExample"]').on('hide.bs.collapse show.bs.collapse', function (e) {
                    var icon = $(e.target).prev('.card-header').find('.icon-toggle');
                    if (e.type === 'show') {
                        icon.removeClass('icon-plus').addClass('icon-minus');
                    } else {
                        icon.removeClass('icon-minus').addClass('icon-plus');
                    }
                });
            });
        </script>


        <script>
            document.querySelector('.progress-item').classList.add('active');

        </script>
 
        <!-- Include this script at the bottom of your HTML or in a separate JS file -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const progressItems = document.querySelectorAll('.progress-item');

            progressItems.forEach((item, index) => {
                item.addEventListener('click', () => {
                    if (item.classList.contains('active')) {
                        const isConfirmed = confirm('This progress step and all previous steps are already active. Do you want to deactivate the following steps?');
                        if (isConfirmed) {
                            deactivateFollowingProgress(index);
                        }
                    } else {
                        const isConfirmed = confirm('Do you want to activate this and all previous progress steps?');
                        if (isConfirmed) {
                            activateProgress(index);
                        }
                    }
                });
            });

            function activateProgress(index) {
                for (let i = 0; i <= index; i++) {
                    progressItems[i].classList.add('active');
                }
            }

            function deactivateFollowingProgress(index) {
                for (let i = index + 1; i < progressItems.length; i++) {
                    progressItems[i].classList.remove('active');
                }
            }
        });
        </script>


<script>

    document.addEventListener('DOMContentLoaded', function () {
    // Select all buttons that will trigger the modal
    const documentButtons = document.querySelectorAll('.documentModalButton');

    documentButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Get the URL from the data-url attribute
            const documentUrl = this.getAttribute('data-url');
            
            // Get the iframe inside the modal
            const iframe = document.getElementById('documentIframe');
            
            // Set the iframe's src to the document URL
            iframe.setAttribute('src', documentUrl);
            
            // Now the modal will automatically open because of the data-toggle and data-target attributes
        });
    });
});

</script>


<script>

    document.addEventListener('DOMContentLoaded', function () {
    // Select all buttons that will trigger the modal
    const documentButtons = document.querySelectorAll('.documentCustomerButton');

    documentButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Get the URL from the data-url attribute
            const documentUrl = this.getAttribute('pdf-url');
            
            // Get the iframe inside the modal
            const iframe = document.getElementById('pdfFrame');
            
            // Set the iframe's src to the document URL
            iframe.setAttribute('src', documentUrl);
            
            // Now the modal will automatically open because of the data-toggle and data-target attributes
        });
    });
});

</script>
<!-- Task Completion Modal : start -->
  <script>
    $(document).ready(function () {
        let currentTaskCheckbox;

        function showIncompleteSubTaskAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Sub-Tasks',
                text: 'Please complete all sub-tasks before marking the main task as done.',
                confirmButtonText: 'OK'
            });
        }

        $('input[type="checkbox"][name="doneTask"]').on('change', function () {
            if (this.checked) {
                const phaseId = $(this).data('phase');
                const taskId = $(this).data('task');
                const incompleteSubTasks = $(`input[type="checkbox"].doneSubTask[data-phase="${phaseId}"][data-task="${taskId}"]:not(:checked)`);

                if (incompleteSubTasks.length > 0) {
                    $(this).prop('checked', false);
                    showIncompleteSubTaskAlert();
                } else {
                    currentTaskCheckbox = this;
                    openDoneModal(this);
                }
            }
        });

        $('input[type="checkbox"][name="doneSubTask"]').on('change', function () {
            if (this.checked) {
                const phaseId = $(this).data('phase');
                const taskId = $(this).data('task');
                const remainingSubTasks = $(`input[type="checkbox"].doneSubTask[data-phase="${phaseId}"][data-task="${taskId}"]:not(:checked)`);

                currentTaskCheckbox = this;
                $('#doneSubTaskModal input[name="last"]').val(remainingSubTasks.length === 0 ? 'true' : 'false');
                openSubTaskModal(this, saveSubTask);
            }
        });

        function openDoneModal(taskCheckbox) {
            currentTaskCheckbox = taskCheckbox;

            const customer = $(taskCheckbox).data('customer');
            const product = $(taskCheckbox).data('product');
            const phase = $(taskCheckbox).data('phase');
            const task = $(taskCheckbox).data('task');
            const projectId = $(taskCheckbox).data('project-id');

            console.log("Main Task Modal Data:", { customer, product, phase, task, projectId });

            $('#doneModal input[name="customer_id"]').val(customer);
            $('#doneModal input[name="product_id"]').val(product);
            $('#doneModal input[name="phase_id"]').val(phase);
            $('#doneModal input[name="activities_id"]').val(task);
            $('#doneModal input[name="project_id"]').val(projectId);

            $('#doneModal').modal('show');

            $('#save-task-btn').off('click').on('click', function () {
                saveMainTask(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Task Saved',
                        text: 'The task has been saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                });
            });
        }

        function openSubTaskModal(subTaskCheckbox, callback) {
            const customer = $(subTaskCheckbox).data('customer');
            const product = $(subTaskCheckbox).data('product');
            const phase = $(subTaskCheckbox).data('phase');
            const task = $(subTaskCheckbox).data('task');
            const subTask = $(subTaskCheckbox).data('sub-task');
            const projectId = $(subTaskCheckbox).data('project-id');
            const alternative = $(subTaskCheckbox).data('alternative');

            console.log("Sub-Task Modal Data:", { customer, product, phase, task, projectId });

            $('#doneSubTaskModal input[name="customer_id"]').val(customer);
            $('#doneSubTaskModal input[name="product_id"]').val(product);
            $('#doneSubTaskModal input[name="phase_id"]').val(phase);
            $('#doneSubTaskModal input[name="activities_id"]').val(task);
            $('#doneSubTaskModal input[name="sub_task_id"]').val(subTask);
            $('#doneSubTaskModal input[name="project_id"]').val(projectId);
            $('#doneSubTaskModal input[name="alternative"]').val(alternative);

            $('#doneSubTaskModal').modal('show');

            $('#save-sub-task-btn').off('click').on('click', function () {
                callback(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sub-Task Saved',
                        text: 'The sub-task has been saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                });
            });
        }

        function saveMainTask(callback) {
            const formData = new FormData();
            $('#doneModal').find('input, select, textarea').each(function () {
                const name = $(this).attr('name');
                const value = $(this).val() || null;
                formData.append(name, value);
            });

            formData.append('_token', '{{ csrf_token() }}');

            console.log("FormData for Main Task:", Object.fromEntries(formData.entries()));

            $.ajax({
                url: "{{ route('task.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log("Server Response:", response);
                    if (callback) callback();
                },
                error: function (xhr) {
                    console.error("Error Response:", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the task.'
                    });
                }
            });
        }

        function saveSubTask(callback) {
            const formData = new FormData();
            $('#doneSubTaskModal').find('input, select, textarea').each(function () {
                const name = $(this).attr('name');
                const value = $(this).val() || null;
                formData.append(name, value);
            });

            formData.append('_token', '{{ csrf_token() }}');

            console.log("FormData for Sub-Task:", Object.fromEntries(formData.entries()));

            $.ajax({
                url: "{{ route('task.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log("Server Response:", response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Sub-Task Saved',
                        text: 'The sub-task has been saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (callback) callback();
                    });
                },
                error: function (xhr) {
                    console.error("Error Response:", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the sub-task.'
                    });
                }
            });
        }
    });
</script>


<!-- Task Completion Modal : End -->

<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
</script>
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
        $('input[name="outside_types"]').change(function () {
            toggleDropdowns();
        });

        // Attach event listener to the radio buttons in the subtask modal to toggle the dropdowns on change
        $('input[name="outside_types"]').change(function () {
            toggleDropdown();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Check if the URL contains the #project-management hash
    if (window.location.hash === "#project-management") {
        // Activate the PROJEKTMANAGEMENT tab
        var projectManagementTab = document.querySelector("#messages-tab-justified");
        if (projectManagementTab) {
            new bootstrap.Tab(projectManagementTab).show();
        }
    }
});

</script>


<!-- Fetch Notifications  -->
<script>
  $(document).ready(function () {
    // Loop through all notification cards
    $('.notification-card').each(function () {
        const projectId = $(this).data('project-id'); // Get project ID from data attribute
        const phaseId = $(this).data('phase-id'); // Get phase ID from data attribute
        const listId = `#notifications-list-${projectId}-${phaseId}`; // Generate list ID dynamically

        if (projectId && phaseId) {
            fetchTaskNotifications(projectId, phaseId, listId); // Fetch notifications for each card
        } else {
            console.error("Project ID or Phase ID is missing for card:", this);
        }
    });
});

    function fetchTaskNotifications(projectId, phaseId, listId) {
        $.ajax({
            url: `/get/project/notification/${projectId}/${phaseId}`, // Include projectId and phaseId in the URL
            type: "GET",
            success: function (response) {
                console.log(`Notifications for Project ${projectId}, Phase ${phaseId}:`, response);

                // Clear the specific notification list
                $(listId).empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(notification => {
                        const title = notification.title || "Notification";
                        const message = notification.message || "No details available.";
                        const performedAt = new Date(notification.performed_at).toLocaleString();

                        $(listId).append(`
                            <li>
                                <div class="timeline-icon bg-primary">
                                    <i class="feather icon-bell font-medium-2"></i>
                                </div>
                                <div class="timeline-info">
                                    <p class="font-weight-bold">${title}</p>
                                    <span>${message}</span>
                                </div>
                                <small>${performedAt}</small>
                            </li>
                        `);
                    });
                } else {
                    $(listId).append(`
                        <li>
                            <div class="timeline-info">
                                <p class="font-weight-bold">No Notifications</p>
                            </div>
                        </li>
                    `);
                }
            },
            error: function (error) {
                console.error(`Error fetching notifications for Project ${projectId}, Phase ${phaseId}:`, error);
                toastr.error("Failed to load notifications. Please try again.");
            }
        });
    }

</script>




<!-- Comment CRUD  -->
 
 <script>
    $(document).ready(function () {
        // Set up CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Load comments on page load
        $('[id^="comment_section_"]').each(function () {
            let ids = $(this).attr('id').split('_');
            let projectId = ids[2];
            let phaseId = ids[3];
            loadComments(projectId, phaseId);
        });

        // Save new comment
        $(document).on('click', '.comment-save', function () {
            let form = $(this).closest('form');
            let projectId = form.find('input[name="project_id"]').val();
            let phaseId = form.find('input[name="phase_id"]').val();
            let formData = form.serialize();

            $.ajax({
                url: '{{ route("project.task.comment.store") }}',
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        form[0].reset(); // Clear the form
                        loadComments(projectId, phaseId); // Reload comments
                    } else {
                        alert('Error saving comment');
                    }
                },
                error: function (xhr) {
                    alert('Something went wrong. Please try again.');
                    console.error('Save error:', xhr.responseText);
                }
            });
        });

        // Show textarea for reply when the reply button is clicked
        $(document).on('click', '.reply-comment', function () {
            let parentComment = $(this).closest('.comment-wrapper');
            if (parentComment.find('.reply-textarea').length === 0) {
                let projectId = parentComment.data('project-id');
                let phaseId = parentComment.data('phase-id');
                let parentId = parentComment.data('comment-id');

                parentComment.append(`
                    <div class="reply-wrapper mt-2">
                        <textarea class="form-control reply-textarea" rows="2" placeholder="Write a reply and press Enter"></textarea>
                        <input type="hidden" class="reply-project-id" value="${projectId}">
                        <input type="hidden" class="reply-phase-id" value="${phaseId}">
                        <input type="hidden" class="reply-parent-id" value="${parentId}">
                    </div>
                `);
            } else {
                parentComment.find('.reply-wrapper').toggle();
            }
        });

        // Save reply when Enter is pressed in the reply textarea
        $(document).on('keypress', '.reply-textarea', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                let textarea = $(this);
                let commentText = textarea.val();
                let projectId = textarea.siblings('.reply-project-id').val();
                let phaseId = textarea.siblings('.reply-phase-id').val();
                let parentId = textarea.siblings('.reply-parent-id').val();

                if (commentText.trim() === '') {
                    alert('Reply cannot be empty!');
                    return;
                }

                $.ajax({
                    url: '{{ route("project.task.comment.reply") }}',
                    type: 'POST',
                    data: {
                        project_id: projectId,
                        phase_id: phaseId,
                        parent_id: parentId,
                        comment: commentText
                    },
                    success: function (response) {
                        if (response.success) {
                            loadComments(projectId, phaseId); // Reload comments
                        } else {
                            alert('Error saving reply');
                        }
                    },
                    error: function (xhr) {
                        alert('Something went wrong while saving the reply.');
                        console.error('Reply save error:', xhr.responseText);
                    }
                });
            }
        });
    });

    // Load comments function
    function loadComments(projectId, phaseId) {
        $.ajax({
            url: `/project_phase_comment/${projectId}/${phaseId}`,
            type: 'GET',
            success: function (data) {
                let commentSectionId = `#comment_section_${projectId}_${phaseId}`;
                if (data.length > 0) {
                    let commentsHtml = generateCommentsHtml(data, null, projectId, phaseId);
                    $(commentSectionId).html(commentsHtml);
                } else {
                    $(commentSectionId).html('<p>No comments yet. Be the first to comment!</p>');
                }
            },
            error: function (xhr) {
                alert('Failed to load comments');
                console.error('Failed to load comments:', xhr.responseText);
            }
        });
    }

    // Generate nested comments HTML recursively
    function generateCommentsHtml(data, parentId, projectId, phaseId) {
        let commentsHtml = '';
        const imagePath = "{{ asset('images/employee') }}";

        data.filter(comment => comment.parent_id == parentId).forEach(comment => {
            let avatarPath = `${imagePath}/${comment.image || 'default-avatar.png'}`;
            commentsHtml += `
                <div class="comment-wrapper" data-comment-id="${comment.id}" data-project-id="${projectId}" data-phase-id="${phaseId}" style="margin-left: ${parentId ? '40px' : '0'};">
                    <div class="d-flex justify-content-start align-items-center mb-1">
                        <div class="avatar mr-50">
                            <img src="${avatarPath}" alt="Avatar" height="30" width="30">
                        </div>
                        <div class="user-page-info">
                            <h6 class="mb-0">${comment.name || 'Anonymous'} ${comment.lastname || ''}</h6>
                            <span class="font-small-2">${comment.created_at}</span>
                            <p class="font-small-2">${comment.comment}</p>
                            <button type="button" class="btn btn-sm btn-link reply-comment">Reply</button>
                        </div>
                    </div>
                    <div class="nested-comments">
                        ${generateCommentsHtml(data, comment.id, projectId, phaseId)}
                    </div>
                </div>`;
        });

        return commentsHtml;
    }
</script>

<!-- Comment Crud End  --> 
  

<!-- Attachment file  -->
 <script>
    Dropzone.autoDiscover = false;

    $(document).ready(function () {
        console.log("Initializing Dropzones...");

        // Loop through each Dropzone form and initialize it
        $(".dropzone").each(function () {
            const $form = $(this); // Current Dropzone form
            const dropzoneId = $form.attr("id"); // Unique Dropzone ID
            const project_id = $form.find('input[name="project_id"]').val();
            const phase_id = $form.find('input[name="phase_id"]').val();
            const customer_id = $form.find('input[name="customer_id"]').val();

            console.log(`Initializing Dropzone for project: ${project_id}, phase: ${phase_id}`);

            // Initialize Dropzone for this specific form
            new Dropzone(`#${dropzoneId}`, {
                url: "{{ route('project.task.attachment.store') }}", // Laravel route
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}" // CSRF token for Laravel
                },
                paramName: "file", // Name of the input field for the file
                maxFilesize: 2, // Max file size in MB
                acceptedFiles: ".docx,.xlsx,.jpg,.png,.pdf", // Allowed file types
                addRemoveLinks: true, // Show remove links
                dictDefaultMessage: "Click or drag files here to upload", // Message in Dropzone area
                params: {
                    project_id: project_id,
                    phase_id: phase_id,
                    customer_id: customer_id
                },
                success: function (file, response) {
                    console.log("File uploaded successfully:", response);
                    Swal.fire("Success!", "File uploaded successfully!", "success");
                    fetchFiles(); // Refresh file list after upload
                },
                error: function (file, errorMessage) {
                    console.error("Error uploading file:", errorMessage);
                    Swal.fire("Error!", "File upload failed. Please try again.", "error");
                }
            });
        });

        console.log("All Dropzones initialized!");

        // Fetch files from the server
        function fetchFiles() {
            $(".document_list").each(function () {
                const $list = $(this); // Current document list element
                const project_id = $list.data("project"); // Get project ID for this list
                const phase_id = $list.data("phase"); // Get phase ID for this list

                console.log(`Fetching files for project: ${project_id}, phase: ${phase_id}`);

                $.ajax({
                    url: `/project_get_attachments/${project_id}/${phase_id}`, // Laravel endpoint
                    method: "GET",
                    success: function (files) {
                        console.log(`Fetched files for project ${project_id}, phase ${phase_id}:`, files);

                        $list.empty(); // Clear the current list before appending files
                        files.forEach(file => appendFileToList($list, file)); // Append fetched files
                    },
                    error: function (xhr) {
                        console.error(`Error fetching files for project ${project_id}, phase ${phase_id}:`, xhr.responseText);
                        Swal.fire("Error!", "Failed to fetch files. Please try again.", "error");
                    }
                });
            });
        }

        // Append files to the specific document list
        function appendFileToList($list, file) {
            console.log("Appending file:", file);

            const badgeClass = file.file_type === "pdf" ? "bg-primary" : "bg-secondary";
            const fileItem = `
                <li class="list-group-item">
                    <span class="badge badge-pill ${badgeClass} float-right">${file.file_type.toUpperCase()}</span>
                    <span class="editable-image-name" data-id="${file.id}" style="cursor: pointer;">${file.image_name}</span>
                    <a href="#" class="view-document" 
                    data-path="/storage/${file.image}" 
                    data-type="${file.file_type}" 
                    data-bs-toggle="modal" 
                    data-bs-target="#viewDocumentModal"  style="margin-left: 10px;">View</a>
                    <button class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light delete-document" data-id="${file.id}" style="margin-left: 10px;"><i class="feather icon-trash"></i></button>
                </li>`;
            $list.append(fileItem); // Append to the specific list
        }

        // Handle file viewing in the modal
       // Handle file viewing in the modal
        $(document).on("click", ".view-document", function (e) {
            e.preventDefault();

            const filePath = $(this).data("path");
            const fileType = $(this).data("type").toLowerCase();

            console.log("Viewing file:", filePath, "Type:", fileType);

            // Reset the modal viewers
            $("#documentViewer").hide().attr("src", "");
            $("#imageViewer").hide().attr("src", "");

            // Handle file type display
            if (fileType === "pdf") {
                $("#documentViewer").show().attr("src", filePath); // Show PDF in iframe
            } else if (["jpg", "jpeg", "png", "gif"].includes(fileType)) {
                $("#imageViewer").show().attr("src", filePath); // Show image
            } else {
                Swal.fire("Error!", "Unsupported file type.", "error");
                return;
            }

            // Show the modal
            $("#viewDocumentModal").modal("show");
        });


        // Handle double-click on image_name
        $(document).on("dblclick", ".editable-image-name", function () {
            const $element = $(this);
            const currentName = $element.text();
            const attachmentId = $element.data("id");

            const inputField = `<input type="text" class="form-control rename-input" value="${currentName}" data-id="${attachmentId}" style="width: auto;">`;
            $element.html(inputField);
            $element.find("input").focus();
        });

        // Handle pressing Enter to save the new name
        $(document).on("keypress", ".rename-input", function (e) {
            if (e.key === "Enter") {
                const $input = $(this);
                const newName = $input.val();
                const attachmentId = $input.data("id");

                if (!newName.trim()) {
                    Swal.fire("Error!", "Image name cannot be empty.", "error");
                    return;
                }

                $.ajax({
                    url: "{{ route('project.task.attachment.rename') }}",
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: {
                        id: attachmentId,
                        image_name: newName
                    },
                    success: function (response) {
                        console.log(response.message);
                        Swal.fire("Success!", response.message, "success");
                        $input.parent().text(newName);
                    },
                    error: function (xhr) {
                        console.error("Error renaming file:", xhr.responseText);
                        Swal.fire("Error!", "Failed to rename the file. Please try again.", "error");
                        $input.parent().text($input.attr("value"));
                    }
                });
            }
        });

        // Handle deleting a file
        $(document).on("click", ".delete-document", function (e) {
            e.preventDefault();
            const attachmentId = $(this).data("id");

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/project_task_attachment_delete/${attachmentId}`,
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            console.log(response.message);
                            Swal.fire("Deleted!", response.message, "success");
                            $(`.delete-document[data-id="${attachmentId}"]`).closest("li").remove();
                        },
                        error: function (xhr) {
                            console.error("Error deleting file:", xhr.responseText);
                            Swal.fire("Error!", "Failed to delete the file. Please try again.", "error");
                        }
                    });
                }
            });
        });

        fetchFiles(); // Fetch files on page load
    });
</script>


<!-- Attachment file  -->

<!-- attachment List: start -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const attachmentTable = document.getElementById('attachmentTable').querySelector('tbody');
        const projectId = {{ $data->id }}; // Dynamically assigned project ID
        const fetchAttachmentsUrl = `{{ route('project.phase.attachments.all', ['project_id' => $data->id]) }}`;
        const deleteAttachmentUrl = `{{ route('project.task.attachment.delete', ['id' => ':id']) }}`; // Use :id as placeholder
        const renameAttachmentUrl = `{{ route('project.task.attachment.rename') }}`;

        // Fetch and populate attachments
        function loadAttachments() {
            fetch(fetchAttachmentsUrl)
                .then(response => response.json())
                .then(data => {
                    attachmentTable.innerHTML = '';
                    data.forEach(attachment => {
                            const row = `
                                <tr>
                                    <td contenteditable="false" class="editable-name" data-id="${attachment.id}">
                                        ${attachment.image_name}
                                        <br>
                                        <span class="badge badge-info">${attachment.file_type}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-btn" data-url="${attachment.url}" data-type="${attachment.file_type}">View</button>
                                        <a href="${attachment.url}" class="btn btn-sm btn-success download-btn">Download</a>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="${attachment.id}">Delete</button>
                                    </td>
                                </tr>
                            `;
                            attachmentTable.insertAdjacentHTML('beforeend', row);
                        });


                    // Add event listeners for view, delete, and editable fields
                    document.querySelectorAll('.view-btn').forEach(button => {
                        button.addEventListener('click', viewFile);
                    });
                    document.querySelectorAll('.delete-btn').forEach(button => {
                        button.addEventListener('click', deleteAttachment);
                    });
                    document.querySelectorAll('.editable-name').forEach(cell => {
                        cell.addEventListener('dblclick', makeEditable);
                    });
                })
                .catch(error => {
                    Swal.fire('Error', 'Failed to load attachments!', 'error');
                    console.error(error);
                });
        }

        // View file in modal
       function viewFile(event) {
            const url = event.target.getAttribute('data-url');
            const type = event.target.getAttribute('data-type');
            const filePreviewContent = document.getElementById('filePreviewContent');

            if (!url) {
                Swal.fire('Error', 'File URL is missing or invalid.', 'error');
                return;
            }

            if (type === 'pdf') {
                filePreviewContent.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="500px" />`;
            } else {
                filePreviewContent.innerHTML = `<img src="${url}" class="img-fluid" alt="File Preview" />`;
            }

            $('#fileModal').modal('show');
        }



        // Delete attachment
        function deleteAttachment(event) {
            const attachmentId = event.target.getAttribute('data-id');
            const deleteUrl = deleteAttachmentUrl.replace(':id', attachmentId);

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', 'Your file has been deleted.', 'success');
                                loadAttachments();
                            } else {
                                Swal.fire('Error', 'Failed to delete the file.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'An error occurred while deleting.', 'error');
                            console.error(error);
                        });
                }
            });
        }

        // Make image_name editable
        function makeEditable(event) {
            const cell = event.target;
            const attachmentId = cell.getAttribute('data-id');
            cell.contentEditable = "true";
            cell.focus();

            cell.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const newName = cell.textContent.trim();
                    if (newName) {
                        renameAttachment(attachmentId, newName);
                    }
                    cell.contentEditable = "false";
                }
            });

            cell.addEventListener('blur', function () {
                cell.contentEditable = "false";
            });
        }

        // Rename attachment
        function renameAttachment(id, newName) {
            fetch(renameAttachmentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id,
                    image_name: newName
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Renamed!', 'The file has been renamed successfully.', 'success');
                    } else {
                        Swal.fire('Error', 'Failed to rename the file.', 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'An error occurred while renaming.', 'error');
                    console.error(error);
                });
        }

        // Initial load
        loadAttachments();
    });
</script>

 <!-- attachment list: end -->

    @endpush