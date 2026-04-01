@extends('admin.layouts.app')
@section('title')
PERSONAL AUFGABEN
@endsection

@section('style')
 
<style>
    .avatars {
           white-space: nowrap;
    background-color: #c3c3c3;
    border-radius: 9%;
    position: relative;
    cursor: pointer;
    color: #fff;
    display: inline-flex;
    font-size: .75rem;
    text-align: center;
    vertical-align: middle;
    margin: 5px;
    width: 74px;
    height: 49px;
    } 

    .line-through {
        text-decoration: line-through !important;
    }



.progress {

    height: 23px !important;
    border: 1px solid gray !important;
    border-radius: 6px !important;

}

.progress-bar {
    width: 60%;
    height: 23px;
    border-radius: 0 !important;
    background-color: #e50056 !important;
}
@keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

.feather.icon-bell.warning.out-date {
    animation: blink 1s infinite; /* Blink animation with 1s duration, infinite loop */
}

.nav.nav-tabs .nav-item .nav-link.active {
    border: none;
    position: relative;
    color: #efffd8 !important;
    transition: all .2s ease;
    background-color: #8fc73e !important;
}

</style>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
 

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- FullCalendar CSS --> 

<style>
  #calendar {
    max-width: 1100px;
    margin: 0 auto;
    padding: 20px;
}

.fc-event-custom {
    background: #f4f5f7;
    padding: 15px;
    border-radius: 8px;
    color: #333;
}

.custom-event-header {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 5px;
}

.custom-event-title {
    font-size: 16px;
    font-weight: bold;
    color: #007bff;
}

.custom-event-product-status .avatar {
    margin-right: 5px;
}

.date {
    font-size: 12px;
    color: #555;
}

@media (max-width: 768px) {
    #calendar {
        width: 100%;
    }
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
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Aufgaben</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/personal/task/'.auth()->user()->name) }}">Aufgabeliste</a></li> 
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">   

                <div class="row">
                    <div class="col-xl-4 col-md-6 col-sm-12 pr-0 pl-0">
                        <div class="card">
                            <div class="card-header"> 
                         
                                <a type="button" href="{{ url('personal/task/'.auth()->user()->name) }}" class="btn btn-outline-primary  mb-1 waves-effect waves-light">Zurück</a>

                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap">
                                                <tbody>
                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">ID:</span></td>
                                                        <td>{{ $data->task_id }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Titel :</span></td>
                                                        <td>{{ $data->task_title }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Verfasser :</span></td>
                                                        <td>
                                                            <div class="d-flex" style="align-items: center;">
                                                                <div class="avatar mr-1">
                                                                    <img src="{{ asset('images/employee/'.$data->cimage) }}" alt="Avatar" height="32" width="32">
                                                                </div>
                                                                <span class="block text-[14px] font-medium">{{ $data->cname }} {{ $data->clastname }}</span>
                                                            </div> 
                                                        </td>
                                                    </tr>

                                                     <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Erstellt am :</span></td>
                                                        <td>{{ date('d, M Y', strtotime($data->created_at)) }}</td>
                                                    </tr>
 
                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Fälligkeitsdatum :</span></td>
                                                        <td data-id="{{ $data->id }}" 
                                                            id="due_date" 
                                                            data-start-date="{{ $data->start_date ?? \Carbon\Carbon::parse($data->created_at)->format('Y.m.d') }}"
                                                            data-total-time="{{ $data->total_time}}" 
                                                            data-due-time="{{ $data->due_time}}" 
                                                            data-total-day="{{ $data->total_day}}">{{ date('d, M Y', strtotime($data->due_date)) }}</td>
                                                    </tr>


                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Fälligkeitszeit :</span></td>
                                                        <td data-id="{{ $data->id }}" 
                                                            id="due_time" 
                                                            data-start-date="{{ $data->start_date ?? \Carbon\Carbon::parse($data->created_at)->format('Y.m.d') }}" 
                                                            data-total-time="{{ $data->total_time}}" 
                                                            data-total-day="{{ $data->total_day}}"
                                                            data-due-date="{{ $data->due_date}}"
                                                            >{{ \Carbon\Carbon::parse($data->due_time)->format('H:i') }}</td>
                                                    </tr>

                                                    

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Gesamtzeit (STD):</span></td>
                                                        <td>
                                                             {{ $data->total_time}}
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Gesamttag:</span></td>
                                                        <td>
                                                             {{ $data->total_day }}
                                                        </td>
                                                    </tr>
 
                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Fortschritt nach Schritt:</span></td>
                                                        <td>
                                                           @php
                                                                    // Fetch the total and completed counts in a single query
                                                                    $taskKeys = DB::table('personal_task_keys')
                                                                        ->selectRaw('
                                                                            SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_count,
                                                                            COUNT(*) as total_count
                                                                        ')
                                                                        ->where('personal_task_id', $data->id)
                                                                        ->first();

                                                                    // Completed and total tasks
                                                                    $completedTasks = $taskKeys->completed_count ?? 0;
                                                                    $totalTasks = $taskKeys->total_count ?? 0;
                                                                @endphp

                                                                @if($totalTasks == 0)
                                                                    <i class="fa fa-circle font-small-3 text-success mr-50"></i> Neu
                                                                @else
                                                                    <div class="progress progress-bar-primary progress-lg" style="height: 28px;">
                                                                        <div 
                                                                            class="progress-bar" 
                                                                            role="progressbar" 
                                                                            aria-valuenow="{{ $completedTasks }}" 
                                                                            aria-valuemin="0" 
                                                                            aria-valuemax="{{ $totalTasks }}" 
                                                                            style="width: {{ ($totalTasks > 0) ? ($completedTasks / $totalTasks) * 100 : 0 }}%; height: 28px;">
                                                                            {{ $completedTasks }}/{{ $totalTasks }}  
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                        </td>
                                                    </tr>

                                                     

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Status :</span></td>
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

                                                            $status = $map[$data->task_status] ?? 'Status unbekannt'; 


                                                        @endphp

                                                            @php
                                                                $accept_date = DB::table('employees_personal_tasks') 
                                                                    ->where('task_id', $data->id)
                                                                    ->where('employee_id', auth()->user()->name)
                                                                    ->select('status')
                                                                    ->first();

                                                                // Ensure $accept_date is not null and extract the status or default to 'send'
                                                                $accept_status = $accept_date ? $accept_date->status : 'send';
                                                            @endphp
                                                        <td>
                                                            <span class="font-medium text-secondary current-status">{{ $status}}</span>

                                                           @if($accept_status === 'accept')
                                                                <i class="feather icon-edit project_status" style="cursor: pointer;"></i>
                                                            @endif

                                                            <form id="project_status_form" style="display: none;">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                                <select name="project_status" class="form-control project_status_select">
                                                                    <option value="new" {{ $data->task_status == 'new' ? 'selected' : '' }}>Offen</option>
                                                                    <option value="start" {{ $data->task_status == 'start' ? 'selected' : '' }}>Starten</option>
                                                                    <option value="on_going" {{ $data->task_status == 'on_going' ? 'selected' : '' }}>Im Prozess</option>
                                                                    <option value="on_review" {{ $data->task_status == 'on_review' ? 'selected' : '' }}>Kurz vor Abschluss</option>
                                                                    <option value="completed" {{ $data->task_status == 'completed' ? 'selected' : '' }}>Vollendet</option>
                                                                    <option value="pause" {{ $data->task_status == 'pause' ? 'selected' : '' }}>Pause</option>
                                                                    <option value="cancel" {{ $data->task_status == 'cancel' ? 'selected' : '' }}>Abbrechen</option>
                                                                </select>
                                                            </form>
                                                        </td> 
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Priorität :</span></td>
                                                        <td>
                                                            <span class="badge bg-danger/10 text-danger"><i class="ri-circle-fill text-[8px] me-1"></i> {{ $data->priority }}</span>
                                                        </td>
                                                    </tr>

                                                   

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Verantwortlichen :</span></td>
                                                        <td class="p-1">
                                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                     @foreach ($group_emp->unique('employee_id') as $emp)
                                                                        <li data-toggle="tooltip" 
                                                                            data-popup="tooltip-custom" 
                                                                            data-placement="bottom" 
                                                                            data-original-title="{{ $emp->name }} {{ $emp->lastname }}" 
                                                                            class="avatar pull-up">
                                                                            <img class="media-object rounded-circle" 
                                                                                src="{{ $emp->image ? asset('images/employee/'.$emp->image) : asset('images/gender/male.png') }}" 
                                                                                alt="{{ $emp->name }}" 
                                                                                height="30" 
                                                                                width="30">
                                                                        </li>
                                                                    @endforeach


                                                            </ul>
                                                        </td>
                                                    </tr>


                                                   <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                    <td><span class="font-medium">Kontroller :</span></td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                            @forelse ($controllers as $emp)
                                                                <li data-toggle="tooltip"
                                                                    data-popup="tooltip-custom"
                                                                    data-placement="bottom"
                                                                    data-original-title="{{ $emp->name }} {{ $emp->lastname }}"
                                                                    class="avatar pull-up">
                                                                    <img class="media-object rounded-circle"
                                                                        src="{{ $emp->image ? asset('images/employee/'.$emp->image) : asset('images/default-avatar.png') }}"
                                                                        alt="{{ $emp->name }}"
                                                                        height="30"
                                                                        width="30">
                                                                </li>
                                                            @empty
                                                                <li><span class="text-muted">Kein Kontroller definiert</span></li>
                                                            @endforelse
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
                                 <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#large">UPLOAD</button> 
                                    <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel17">Upload Files</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form   action="{{ url('upload.files') }}"  method="post"  class="dropzone" id="file-upload" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                  
                                                        <input type="hidden" name="task_id" value="{{$data->id}}"> 
                                                        @csrf
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Done</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                        <table class="table text-nowrap" id="attachmentTable"></table>
                                                                            

                                            <!-- Modal -->
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
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-md-6 col-sm-12 pr-0"> 
                        <section id="nav-justified">
                            <div class="row">
                                <div class="col-sm-12 pr-0">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">Aufgabetitel: {{ $data->task_title }} </h4>
                                             <a type="button" href="{{ url('personal_task/'.$data->id.'/edit') }}" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light"><i class="feather icon-edit"></i></a>

                                        </div>
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <p style="font-weight:bold;">Aufgabenbeschreibung</p>
                                                <p>{{ $data->description ?? 'Keine Beschreibung verfügbar' }}</p>
                                                <hr>
                                                <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Aufgabenschritte</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="profile-tab-justified" data-toggle="tab" href="#profile-just" role="tab" aria-controls="profile-just" aria-selected="true">Verantwortlichen</a>
                                                    </li> 
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                                       <div class="card match-height">
                                                            <div class="card-content"> 
                                                                <div class="card-body">  
                                                                    <!-- Task key rows  -->
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="card">
                                                                                    <div class="card-header  bold"><strong>Hauptaufgabe</strong></div>
                                                                                    <div class="card-body">
                                                                                        <div class="table-responsive">
                                                                                            <table class="table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>#</th>
                                                                                                        <th colspan="2">Aufgabenschritte</th> 
                                                                                                        <th >Zugewiesen</th> 
                                                                                                        <th>Zeit</th> 
                                                                                                        <th>Zeitdiffrence</th>
                                                                                                        <th>Abgeschlossen durch</th> 
                                                                                                        <th>Bemerkung</th>
                                                                                                    </tr>
                                                                                                </thead>

                                                                                                <tbody> 
                                                                                                      @foreach ($key_task->where('personal_task_id', '=', $data->id) as $task)
                                                                                                       <tr>
                                                                                                            <td> 
                                                                                                                @if($accept_status === 'accept')
                                                                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                                                    <input type="checkbox" name="done" class="done" data-id="{{ $task->id }}" data-task-id="{{ $task->personal_task_id }}" {{ $task->is_completed== 1 ? 'checked' : '' }} value="false">
                                                                                                                    <span class="vs-checkbox">
                                                                                                                        <span class="vs-checkbox--check">
                                                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                                                        </span>
                                                                                                                    </span>
                                                                                                                </div>
                                                                                                                @else  
                                                                                                                <i class="fa fa-window-close danger" style="font-size: 20px;"data-toggle="tooltip" data-placement="top" title="" data-original-title="Die Aufgabenanforderung wurde noch nicht akzeptiert" ></i>
                                                                                                                @endif
                                                                                                            </td>
                                                                                                            <td colspan="2">
                                                                                                                    <p class="task-title {{ $task->is_completed== 1 ? 'line-through' : '' }}" id="task-title-{{ $task->id }}">
                                                                                                                         <strong>{{ $task->task }}</strong>
                                                                                                                    </p>
                                                                                                                    <p class="task-title {{ $task->is_completed== 1 ? 'line-through' : '' }}" id="task-title-{{ $task->id }}">
                                                                                                                         {{ $task->key_description }}
                                                                                                                    </p>
                                                                                                            </td>  

                                                                                                            <td>
                                                                                                                @php
                                                                                                                    $employeeIds = json_decode($task->employee_id, true) ?? [];
                                                                                                                    $assignedEmployees = $employees->whereIn('id', $employeeIds);
                                                                                                                @endphp

                                                                                                                @foreach ($assignedEmployees as $emp)
                                                                                                                    <img class="media-object rounded-circle" 
                                                                                                                        src="{{ asset('images/employee/' . $emp->image) }}" 
                                                                                                                        alt="Avatar" 
                                                                                                                        height="30" width="30"
                                                                                                                        data-toggle="tooltip" 
                                                                                                                        title="{{ $emp->name }} {{ $emp->lastname }}">
                                                                                                                @endforeach
                                                                                                            </td>

                                                                                                         <td>
                                                                                                           <p>
                                                                                                            <strong>Planzeit</strong>
                                                                                                             <span class="change_plan_time" 
                                                                                                                data-id="{{ $task->id }}" 
                                                                                                                data-task="{{ $data->id }}" 
                                                                                                                ondblclick="makeEditableTime(this)">
                                                                                                                {{ $task->duration }}
                                                                                                            </span>
                                                                                                           </p>
                                                                                                            <p>
                                                                                                            <strong>Istzeit</strong>
                                                                                                               @if($task->is_completed == 1)
                                                                                                                <p class="submit-time" data-duration="{{ $task->duration }}" data-submit="{{ $task->submit_time }}"> 
                                                                                                                    {{ $task->submit_time }} 
                                                                                                                </p>
                                                                                                                
                                                                                                            @else
                                                                                                                @php
                                                                                                                    $statusMapping = [
                                                                                                                        '2' => 'Teilweise erledigt',
                                                                                                                        '3' => 'nicht erledigt',
                                                                                                                        '4' => 'Kann nicht erledigt werden', 
                                                                                                                    ];
                                                                                                                    $is_complete_status = $statusMapping[$task->done_status] ?? null;
                                                                                                                @endphp

                                                                                                                <p class="m-0">
                                                                                                                    @if($is_complete_status)
                                                                                                                        <div class="badge badge-square badge-danger">
                                                                                                                            <i class="feather icon-flag"></i>
                                                                                                                            <span>{{ $is_complete_status }} {{$task->work_progress}} %</span>
                                                                                                                        </div>  
                                                                                                                    @endif
                                                                                                                </p>
                                                                                                            @endif
                                                                                                           </p>
                                                                                                        </td>
 

                                                                                                          <td> 
                                                                                                                <div class="time-difference">
                                                                                                                        @php  
                                                                                                                         
                                                                                                                            // Convert total_time (duration) from decimal hours to minutes
                                                                                                                            $planTime = is_numeric($task->duration) ? $task->duration * 60 : 0;

                                                                                                                            // Convert submit_time (decimal hours) to minutes
                                                                                                                            $submitTime = is_numeric($task->submit_time) ? $task->submit_time * 60 : null;

                                                                                                                            // Calculate the difference in minutes
                                                                                                                            $difference = isset($submitTime) ? $submitTime - $planTime : null;

                                                                                                                            // Format the output properly in HH:MM format
                                                                                                                            $differenceFormatted = isset($difference) 
                                                                                                                                ? sprintf('%02d:%02d', floor(abs($difference) / 60), abs($difference) % 60) 
                                                                                                                                : 'N/A';

                                                                                                                            // Determine if the task took longer or was completed early
                                                                                                                            $statusText = isset($difference) 
                                                                                                                                ? ($difference >= 0 ? "+$differenceFormatted Std" : "-$differenceFormatted Std") 
                                                                                                                                : 'N/A';
                                                                                                                        @endphp

                                                                                                                        <p>{{ $statusText }}</p>
                                                                                                                         <p class="m-0">
                                                                                                                            <div class="badge badge-square badge-warning">
                                                                                                                                <i class="feather icon-calendar"></i>
                                                                                                                                <span>{{ \Carbon\Carbon::parse($task->done_date)->isoFormat('DD.MM.YYYY') }}</span>
                                                                                                                            </div>   
                                                                                                                        </p>
                                                                                                                    </div>

                                                                                                            </td>

                                                                                                            <td>
                                                                                                                @if($task->done_by)
                                                                                                                    @if($task->is_completed == 0) 
                                                                                                                    <span class="done-by" data-toggle="popover" 
                                                                                                                            data-placement="right" data-container="body" 
                                                                                                                            data-original-title="Grund für Aufgabenabbruch" data-content="{{ $task->reason ?? 'Not Defined' }}" data-trigger="click">
                                                                                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$task->image) }}" alt="Avatar" height="30" width="30" 
                                                                                                                        data-toggle="tooltip" data-popup="tooltip-custom" 
                                                                                                                        data-placement="bottom" data-original-title="{{ $task->name }} {{ $task->lastname}}"
                                                                                                                        class="avatar pull-up">
                                                                                                                    </span>
                                                                                                                    @else 
                                                                                                                        <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$task->image) }}" alt="Avatar" height="30" width="30" 
                                                                                                                        data-toggle="tooltip" data-popup="tooltip-custom" 
                                                                                                                        data-placement="bottom" data-original-title="{{ $task->name }} {{ $task->lastname}}"
                                                                                                                        class="avatar pull-up">
                                                                                                                        
                                                                                                                    @endif
                                                                                                                @endif
                                                                                                            </td> 
 
                                                                                                            <td>
                                                                                                                {{ $task->reason ?? 'Nicht definiert' }}
                                                                                                            </td>
                                                                                                            
                                                                                                        </tr>
                                                                                                        @endforeach     
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div> 
                                                                        
                                                                    </div>
                                                                    <!-- Task key rows  --> 
                                                                </div>

                                                                <hr>
                                                                 @php
                                                                // Fetch the required data
                                                                $key_total = DB::table('personal_task_keys')
                                                                    ->where('personal_task_id', $data->id)
                                                                    ->select('duration', 'submit_time', 'total_time')
                                                                    ->get();

                                                                // Sum values from the collection and ensure they are numeric
                                                                $total_plan = (float) $key_total->sum(fn($item) => (float) $item->duration);
                                                                $total_submit = (float) $key_total->sum(fn($item) => (float) $item->submit_time);
                                                                $difference = $total_plan - $total_submit; 

                                                                // Ensure $total_plan is not zero before calculating the percentage
                                                                $percent = ($total_plan > 0) ? ($total_submit / $total_plan * 100) : 0; 

                                                                // Determine status based on the time difference
                                                                if ($total_submit <= $total_plan) {
                                                                    $status_icon = '<i class="feather icon-thumbs-up text-success"></i>'; // Good (job done before or on time)
                                                                } else {
                                                                    $status_icon = '<i class="feather icon-thumbs-down text-danger"></i>'; // Not good (took longer)
                                                                }
                                                            @endphp


                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <p><i class="feather icon-clock"></i> Gesamt Planzeit</p>
                                                                        <p><strong>{{ $total_plan }}</strong></p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <p><i class="feather icon-clock"></i> Gesamt Istzeit</p>
                                                                        <p><strong>{{ $total_submit }}</strong></p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <p><i class="feather icon-clock"></i>Differenz</p>
                                                                        <p><strong>{{ $difference }}</strong></p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <p><i class="feather icon-clock"></i> Status</p>
                                                                        <p>{!! $status_icon !!}</p> 
                                                                    </div>
                                                                      <div class="col-md-2">
                                                                        <p> Perzent</p>
                                                                        <p>{{$percent}} %</p> 
                                                                    </div>
                                                                </div>
                                                            </div>

                                                                
                                                     
                                                                <hr>
                                                                
                                                                <div class="card-body"> 
                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="false">Aufgabenaktivität</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link " id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="true">Kommentare</a>
                                                                        </li>
                                                                    
                                                                    </ul>
                                                                    <div class="tab-content">
                                                                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                            <div class="card" id="notification-card" data-task-id="{{ $data->id }}">
                                                                                <div class="card-header">
                                                                                    <h4 class="card-title">Benachrichtigung</h4>
                                                                                </div>
                                                                                <div class="card-content">
                                                                                    <div class="card-body">
                                                                                        <ul id="notifications-list" class="activity-timeline timeline-left list-unstyled">
                                                                                            <!-- Notifications will be dynamically added here -->
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="tab-pane " id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                                            
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <div class="card">
                                                                                        <div class="card-body">
                                                                                            <div class="d-flex justify-content-start align-items-center mb-1">
                                                                                                <div class="avatar mr-1">
                                                                                                    <img src="{{ asset('images/employee/'.$data->cimage) }}" alt="avtar img holder" height="45" width="45">
                                                                                                </div>
                                                                                                <div class="user-page-info">
                                                                                                    <p class="mb-0">{{ $data->cname }} {{ $data->clastname }}</p>
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
                                                                                            <div id="comment_section"></div> 
                                                                                            <form id="comment_form">
                                                                                                @csrf
                                                                                                <input type="hidden" name="task_id" value="{{ $data->id }}">
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
                                                                    </div>
                                                                </div>   
                                                            </div>  
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane" id="profile-just" role="tabpanel" aria-labelledby="profile-tab-justified">
                                                         <div class="row" id="table-head">
                                                            <div class="col-12">
                                                                   <button
                                                                        type="button"
                                                                        class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light float-right"
                                                                        data-toggle="modal"
                                                                        data-target="#addEmployee"
                                                                    >
                                                                        Neue Mitarbeiter
                                                                    </button> 

                                                            </div>
                                                            <div class="col-12">
                                                                <div class="card" > 
                                                                    <div class="card-content"> 
                                                                        <div class="table-responsive">
                                                                            <table class="table mb-0">
                                                                                <thead class="thead-dark">
                                                                                    <tr>
                                                                                        <th scope="col">#</th>
                                                                                        <th scope="col">Mitarbeiterliste</th> 
                                                                                        <th scope="col">Notiz</th>
                                                                                        <th scope="col">Aktion</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @if($task_employee) 
                                                                                        @foreach ($task_employee as $row)
                                                                                            <tr> 
                                                                                                <td>{{ $row->id}}</td>

                                                                                                <!-- Employee List -->
                                                                                                <td class="p-1"> 
                                                                                                        <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                                                             
                                                                                                                <li 
                                                                                                                    data-toggle="tooltip" 
                                                                                                                    data-popup="tooltip-custom"
                                                                                                                    data-placement="bottom"
                                                                                                                    data-original-title="{{ $row->name }} {{ $row->lastname }}"
                                                                                                                    class="avatar pull-up"
                                                                                                                >
                                                                                                                    <img 
                                                                                                                        class="media-object rounded-circle"
                                                                                                                        src="{{ $emp->image ? asset('images/employee/'.$row->image) : asset('images/default-avatar.png') }}"
                                                                                                                        alt="Avatar"
                                                                                                                        height="30"
                                                                                                                        width="30"
                                                                                                                    >
                                                                                                                </li>
                                                                                                        
                                                                                                        </ul> 
                                                                                                 </td>
 

                                                                                                <!-- Actions -->

                                                                                                 <td>
                                                                                                    @if($row->note) <!-- Fix: Compare IDs, not names -->
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light"
                                                                                                            data-toggle="modal"
                                                                                                            data-target="#notes{{ $row->id }}"
                                                                                                        >
                                                                                                            <i class="feather icon-file"></i>
                                                                                                        </button> 
                                                                                                    @else
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light"
                                                                                                          
                                                                                                        >
                                                                                                            <i class="feather icon-file"></i>
                                                                                                        </button> 
                                                                                                    @endif
                                                                                                </td>
                                                                                                <td>
                                                                                                    @if($data->assigned_by == auth()->user()->name) <!-- Fix: Compare IDs, not names -->
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light change-btn"
                                                                                                            data-task-id="{{$data->id}}"   
                                                                                                             data-employee-id="{{ $row->employee_id }}" 
                                                                                                             data-old-employee-id="{{ $row->employee_id }}"
                                                                                                            data-toggle="modal" data-target="#addEmployeeModal"
                                                                                                        >
                                                                                                            <i class="feather icon-edit"></i>
                                                                                                        </button>

                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete_appointment"
                                                                                                            data-id="{{ $row->id }}"
                                                                                                        >
                                                                                                            <i class="feather icon-trash"></i>
                                                                                                        </button>
                                                                                                    @else
                                                                                                        <div class="alert alert-danger mb-2" role="alert">
                                                                                                            Sie sind nicht berechtigt, Änderungen vorzunehmen
                                                                                                        </div>
                                                                                                    @endif
                                                                                                </td>
                                                                                            </tr>

                                                                                            <!-- Notes Modal -->
                                                                                            <div class="modal fade text-left" id="notes{{ $row->id }}" tabindex="-1" role="dialog">
                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                    <div class="modal-content">
                                                                                                        <div class="modal-header">
                                                                                                            <h4 class="modal-title">Terminnotiz</h4>
                                                                                                            <button type="button" class="close" data-dismiss="modal">
                                                                                                                <span aria-hidden="true">×</span>
                                                                                                            </button>
                                                                                                        </div>
                                                                                                        <div class="modal-body">
                                                                                                            <p>{{ $row->note }}</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
 
                                                                                        @endforeach
                                                                                    @endif
                                                                                </tbody>
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
                        </section>
                    </div>
                    
                </div> 
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="uncheck" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger white">
                    <h5 class="modal-title" id="myModalLabel120">Deaktivieren Sie die Aufgabe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="uncheck_form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="task_id" value="">
                        <label for="reason">Bitte erläutern Sie den Grund für diese Aktion</label>
                            <textarea name="reason" id="reason" cols="30" rows="10" class="form-control"></textarea> 
                            <fieldset>
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input type="checkbox" checked=""   name="reset">
                                    <span class="vs-checkbox">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                    <span class="">Arbeitszeit zurücksetzen</span>
                                </div>
                            </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-light uncheck-save">Speichern</button>
                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="completecheck" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h5 class="modal-title" id="myModalLabel120">Fortschritt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="complete_form">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="task_id" value="">
                        <label for="reason">Aufgabenstatus</label>
                        <select name="done_status" id="" class="form-control mb-2 done_status_select">
                            <option value="complete">vollständig erledigt </option>
                            <option value="part">teilweise erledigt</option>
                            <option value="imposible">nicht erledigt</option>
                            <option value="unable">kann nicht erledigt werden</option>
                        </select>
                        <p class="work_progress_status m-0 "> 
                        <label for="reason">Fortschritt</label>
                        <input type="range" class="form-control   " name="work_progress">
                        </p>

                        <label for="more_time"><i class="feather icon-info"></i> Aufgabendauer(Stunden)</label>
                        <input type="number" name="submit_time" class="form-control" required >

                        <label for="reason">Beschreibung</label> 
                        <textarea name="reason" id="reason" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary waves-effect waves-light check-save">Speichern</button>
                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h5 class="modal-title">Mitarbeiter/Verantwortliche ändern</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add-employee-modal">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="task_id" id="task_id" value=""> 
                        <input type="hidden" name="old_employee" id="old_employee" value="">

                        <label for="employee_id">Neuen Mitarbeiter zur Aufgabe hinzufügen</label>
                        <select name="employee_id" class="form-control employee" required style="width:100%">
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save-add-emp" class="btn btn-primary">speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <div class="modal fade" id="addEmployee" tabindex="-1" role="dialog" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary white">
                    <h5 class="modal-title">Neuer Mitarbeiter für die Aufgabe</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="EmployeeForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="task_id" id="task_id" value="{{$data->id}}">  

                        <label for="employee_id">Neuen Mitarbeiter zur Aufgabe hinzufügen</label>
                        <select name="employee_id" class="form-control employee" required style="width:100%">
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="add_new_employee" class="btn btn-primary">speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script') 
 
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
  <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

 <script>
    // Ensure functions are loaded
        $(document).ready(function () {
            const task_id = {{ $data->id }}; // Assuming $data.id is defined
            fetchFiles(task_id);
        });


        
            Dropzone.autoDiscover = false;

        const myDropzone = new Dropzone("#file-upload", {
            url: "/upload-files", // Replace with your actual upload endpoint
            method: "POST",
            paramName: "file",
            maxFilesize: 2, // Max file size in MB
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.txt",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    console.log("File uploaded successfully:", response);
                });
                this.on("error", function (file, errorMessage) {
                    console.error("File upload error:", errorMessage);
                });
            },
        });



        // Fetch and display files
        function fetchFiles(task_id) {
            $.ajax({
                url: `/personal_task_attachment/${task_id}`,
                method: 'GET',
                success: function (data) {
                    console.log(data);

                    let tableContent = '';

                    if (data.length === 0) {
                        tableContent = `<tr><td colspan="3">No files uploaded.</td></tr>`;
                    } else {
                        data.forEach(file => {
                            tableContent += `
                                <tr id="row_${file.id}">
                                    <td>
                                        <p><i class="feather icon-file primary" style="font-size: 28px;" onclick="openFileModal('${file.image}', '${file.image_name}', '${file.file_type}')"></i></p>
                                        <span>${file.file_type}</span>
                                    </td>
                                    <td>
                                        <p id="image_name_${file.id}" ondblclick="makeEditable(${file.id}, ${file.task_id}, '${file.image_name}')">
                                            ${file.image_name}
                                        </p>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light" onclick="deleteFile(${file.id}, ${task_id})">
                                            <i class="feather icon-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                        });
                    }

                    $('#attachmentTable').html(tableContent);
                },
                error: function (xhr, status, error) {
                    console.error(`Error fetching files: ${error}`);
                    alert('Failed to load files. Check console for more details.');
                }
            });
        }

        // Make file name editable
        function makeEditable(fileId, taskId, currentName) {
            const element = $(`#image_name_${fileId}`);
            const input = `
                <input type="text" id="edit_image_name_${fileId}" 
                    value="${currentName}" 
                    class="form-control"
                    onblur="updateFileName(${fileId}, ${taskId})"
                    onkeydown="checkForEnter(event, ${fileId}, ${taskId})">
            `;
            element.html(input);
            $(`#edit_image_name_${fileId}`).focus();
        }

        // Check for Enter key during editing
        function checkForEnter(event, fileId, taskId) {
            if (event.key === "Enter") {
                updateFileName(fileId, taskId);
            }
        }

        // Update file name
        function updateFileName(fileId, taskId) {
            const newFileName = $(`#edit_image_name_${fileId}`).val();

            $.ajax({
                url: '/update-file-attachment',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: fileId,
                    task_id: taskId,
                    image_name: newFileName
                },
                success: function (response) {
                    toastr.success('File name updated successfully!');
                    fetchFiles(taskId); // Refresh table
                },
                error: function (xhr, status, error) {
                    console.error(`Error updating file: ${error}`);
                    alert('Failed to update file name. Check console for more details.');
                }
            });
        }

        // Open file preview modal
        function openFileModal(filePath, fileName, fileType) {
            let fileContent;

            if (['jpg', 'jpeg', 'png'].includes(fileType.toLowerCase())) {
                fileContent = `<img src="/images/task/personal/document/${filePath}" alt="${fileName}" class="img-fluid">`;
            } else {
                fileContent = `<iframe src="/images/task/personal/document/${filePath}" width="100%" height="500px"></iframe>`;
            }

            $('#filePreviewContent').html(fileContent);
            $('#fileModal').modal('show');
        }

        // Delete file
        function deleteFile(fileId, task_id) {
            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    url: `/personal_task_attachment/delete/${fileId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        toastr.error('File deleted successfully!');
                        fetchFiles(task_id); // Reload files after deletion
                    },
                    error: function (xhr, status, error) {
                        console.error(`Error deleting file: ${error}`);
                        alert('Failed to delete file. Check console for more details.');
                    }
                });
            }
        }

 </script>
 <script>
$(document).on('change', '.done', function() {
    let taskId = $(this).data('id');
    let personalTaskId = $(this).data('task-id');
    let $checkbox = $(this); // Reference to the checkbox

    // Variable to track if save action was performed
    let saveActionPerformed = false;

    if ($checkbox.is(':checked')) {
        // Populate the complete modal inputs
        $('#complete_form input[name="id"]').val(taskId);
        $('#complete_form input[name="task_id"]').val(personalTaskId);

        // Show the complete modal
        $('#completecheck').modal('show');

        // Revert checkbox state if modal is closed without saving
        $('#completecheck').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            if (!saveActionPerformed) {
                $checkbox.prop('checked', false);
            }
        });

        // Save action for the complete modal
        $('.check-save').off('click').on('click', function () {
            let formData = $('#complete_form').serialize();
            $.ajax({
                url: "{{ route('personal.task.done') }}",
                type: "POST",
                data: formData,
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message); // Show success message
                        $('#completecheck').modal('hide');
                        saveActionPerformed = true; // Mark save action as performed
                        location.reload();
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors; // Get validation errors
                        $.each(errors, function (key, value) {
                            toastr.error(value); // Display each error using Toastr
                        });
                    } else {
                        toastr.error('An unexpected error occurred.'); // Handle other errors
                    }
                    console.error('Error:', xhr);
                }
            });
        });
    } else {
        // Populate the uncheck modal inputs
        $('#uncheck_form input[name="id"]').val(taskId);
        $('#uncheck_form input[name="task_id"]').val(personalTaskId);

        // Show the uncheck modal
        $('#uncheck').modal('show');

        // Revert checkbox state if modal is closed without saving
        $('#uncheck').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            if (!saveActionPerformed) {
                $checkbox.prop('checked', true);
            }
        });

        // Save action for the uncheck modal
        $('.uncheck-save').off('click').on('click', function() {
            let formData = $('#uncheck_form').serialize();
            $.ajax({
                url: "{{ route('personal.task.undo') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#uncheck').modal('hide');
                        saveActionPerformed = true; // Mark save action as performed
                        location.reload();
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                }
            });
        });
    }
});
</script>


<script>
$(document).ready(function () {
    // Initialize elements
    const $doneStatusSelect = $('.done_status_select');
    const $workProgressStatus = $('.work_progress_status');
    const $rangeValueDisplay = $('<span class="range-value"> 0</span>%'); // Element to show range value
    $workProgressStatus.after($rangeValueDisplay); // Add range value display beside the range

    // Hide range initially
    $workProgressStatus.hide();
    $rangeValueDisplay.hide();

    // Show or hide range based on done_status_select
    $doneStatusSelect.on('change', function () {
        if ($(this).val() === 'part') {
            $workProgressStatus.show();
            $rangeValueDisplay.show();
        } else {
            $workProgressStatus.hide();
            $rangeValueDisplay.hide();
        }
    });

    // Update range value display when range is changed
    $workProgressStatus.on('input', function () {
        $rangeValueDisplay.text($(this).val());
    });

    // Set range attributes for 4 steps (0, 25, 50, 75, 100)
    $workProgressStatus.attr({
        min: 0,
        max: 100,
        step: 5,
        value: 0 // Default value
    });
});
</script>




<!-- Comment CRUD  -->
 <script>
     $(document).ready(function () {
    // Load comments on page load
    loadComments();

    // Save comment and reload comments on success
    $(document).on('click', '.comment-save', function () {
        let formData = $('#comment_form').serialize();

        $.ajax({
            url: '{{ route("personal.task.comment.store") }}',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#comment_form')[0].reset(); 
                    loadComments(); 
                } else {
                    alert('Error saving comment');
                }
            },
            error: function () {
                alert('Something went wrong. Please try again.');
            }
        });
    });

    // Toggle reply form visibility
    $(document).on('click', '.reply-comment', function () {
        $(this).closest('.user-page-info').find('.comment_form_reply').toggle();
    });

    // Save reply and reload comments on success
    $(document).on('click', '.comment-reply', function () {
        let formData = $(this).closest('form').serialize();

        $.ajax({
            url: '{{ route("personal.task.comment.reply") }}',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    loadComments();
                } else {
                    alert('Error saving reply');
                }
            },
            error: function () {
                alert('Something went wrong while saving the reply.');
            }
        });
    });
});

// Load comments function
function loadComments() {
    let taskId = $('input[name="task_id"]').val();

    $.ajax({
        url: `/personal_task_comment/${taskId}`,
        type: 'GET',
        success: function (data) {
            if (data.length > 0) {
                let commentsHtml = generateCommentsHtml(data, null);
                $('#comment_section').html(commentsHtml);
            } else {
                $('#comment_section').html('<p>No comments yet. Be the first to comment!</p>');
            }
        },
        error: function () {
            alert('Failed to load comments');
        }
    });
}

// Generate nested comments HTML
function generateCommentsHtml(data, parentId) {
    let commentsHtml = '';

    data.filter(comment => comment.parent_id == parentId).forEach(comment => {
        let avatarPath = `{{ asset('images/employee') }}/${comment.image}`;

        commentsHtml += `
            <div class="comment-wrapper" style="margin-left: ${parentId ? '40px' : '0'};">
                <div class="d-flex justify-content-start align-items-center mb-1">
                    <div class="avatar mr-50">
                        <img src="${avatarPath}" alt="Avatar" height="30" width="30">
                    </div>
                    <div class="user-page-info">
                        <table>
                            <tr>
                                <td>
                                 <h6 class="mb-0">${comment.name} ${comment.lastname}</h6>
                                    <span class="font-small-2">${comment.created_at}</span><br>
                                    <span class="font-small-2">${comment.comment}</span>
                                </td>
                                <td>  
                                    <div class="ml-auto cursor-pointer">
                                        <i class="feather icon-message-square reply-comment"></i>
                                    </div> 
                                </td>
                            </tr>
                        </table>
                   
                        <form class="comment_form_reply" style="display: none;">
                            @csrf
                            <input type="hidden" name="task_id" value="${comment.task_id}">
                            <input type="hidden" name="parent_id" value="${comment.id}">
                            <fieldset class="form-label-group mb-50">
                                <textarea class="form-control" name="comment" rows="2" placeholder="Reply to this comment"></textarea>
                            </fieldset>
                            <button type="button" class="btn btn-sm btn-primary waves-effect waves-light comment-reply">
                                Add Reply
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Recursively load nested replies -->
                <div class="nested-comments">
                    ${generateCommentsHtml(data, comment.id)}
                </div>
            </div>`;
    });

    return commentsHtml;
}


 </script>



<script>
$(document).ready(function () {
    // Show form when icon is clicked
    $(document).on('click', '.project_status', function () {
        $('#project_status_form').toggle();
    });

    // Update project status on change
    $(document).on('change', '.project_status_select', function () {
        let formData = $('#project_status_form').serialize();

        $.ajax({
            url: '{{ route("personal.task.project.status") }}',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    // Reload the page after success
                    location.reload();
                } else {
                    alert('Task update failed');
                }
            },
            error: function () {
                alert('Error occurred while updating task status.');
            }
        });
    });
});

</script>


<!-- Get Notifications  -->
 <script>
    $(document).ready(function () {
        // Get task_id dynamically from the data-task-id attribute
        const taskId = $('#notification-card').data('task-id');

        if (taskId) {
            fetchTaskNotifications(taskId); // Fetch notifications for the given task ID
        } else {
            console.error("Task ID is missing!");
        }
    });

    function fetchTaskNotifications(taskId) {
        $.ajax({
            url: `/notifications/task/${taskId}`, // Use GET and include task_id in the URL
            type: "GET",
            success: function (response) {
                console.log("Notifications received:", response); // Debugging: Log notifications

                // Clear the notification list before appending
                $('#notifications-list').empty();

                if (response.data && response.data.length > 0) {
                    response.data.forEach(notification => {
                        const title = notification.title || "Notification";
                        const message = notification.message || "No details available.";
                        const performedAt = new Date(notification.performed_at).toLocaleString();

                        $('#notifications-list').append(`
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
                    $('#notifications-list').append(`
                        <li>
                            <div class="timeline-info">
                                <p class="font-weight-bold">No Notifications</p>
                            </div>
                        </li>
                    `);
                }
            },
            error: function (error) {
                console.error("Error fetching notifications:", error);
                toastr.error("Failed to load notifications. Please try again.");
            }
        });
    }
</script>


 

<!-- updating the duration time start -->
  
<script>
    function makeEditableTime(span) {
        let originalText = span.innerText; // Format: HH:MM
        let taskId = span.getAttribute('data-task');
        let id = span.getAttribute('data-id');

        // Create input field for hour & minute selection
        let input = document.createElement("input");
        input.type = "number";  // Only allow time selection
        input.value = originalText;
        input.classList.add("form-control");
        input.style.width = "80px";

        // Ensure that seconds are not included in the input field
        input.setAttribute("step", "60"); // Step of 60 seconds (prevents seconds selection)

        // Replace span with input
        span.replaceWith(input);
        input.focus();

        // Handle blur or enter key
        input.addEventListener("blur", function () {
            saveUpdatedTime(input, span, originalText, id, taskId);
        });

        input.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                saveUpdatedTime(input, span, originalText, id, taskId);
            }
        });
    }

     function saveUpdatedTime(input, span, originalText, id, taskId) {
    let newValue = input.value.trim(); // HH:MM format only
    if (newValue === originalText || newValue === "") {
        input.replaceWith(span);
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to update the time?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, update it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request
            fetch("{{ route('tasks.duration.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: JSON.stringify({
                    id: id,
                    task_id: taskId,
                    duration: newValue
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Update UI with new time
                span.innerText = newValue;
                input.replaceWith(span);

                Swal.fire({
                    title: "Updated!",
                    text: "Time has been updated successfully.",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page after success
                    location.reload();
                });

            })
            .catch(error => {
                console.error("Error updating time:", error);
                input.replaceWith(span);

                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong. Try again!",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        } else {
            input.replaceWith(span);
        }
    });
} 
</script>
 

 <!-- updating duration time end  -->


 <!-- Updating the Due time and Due Date  -->
    <script>
        $(document).ready(function () {
    $(document).on("dblclick", "#due_date, #due_time", function () {
        let currentText = $(this).text().trim();
        let dataId = $(this).data("id");
        let inputType = $(this).attr("id") === "due_date" ? "date" : "time";

        let inputField = `<input type="${inputType}" class="edit-input" data-id="${dataId}" value="${formatInputValue(inputType, currentText)}" />`;
        $(this).html(inputField);
        $(this).find("input").focus();
    });

    $(document).on("blur keypress", ".edit-input", function (event) {
        if (event.type === "keypress" && event.which !== 13) return;

        let newValue = $(this).val();
        let field = $(this).parent().attr("id");
        let dataId = $(this).data("id");

        let parent = $(this).parent();
        let startDate = parent.data("start-date");
        let dueDate = field === "due_date" ? newValue : parent.data("due-date");
        let dueTime = field === "due_time" ? newValue : parent.data("due-time");

        if (!dueDate) dueDate = newValue; // Ensure dueDate is set
        if (!dueTime) dueTime = "00:00"; // Default time if missing

        let { totalHours, totalDays } = calculateTotalTime(startDate, dueDate, dueTime);

        if (newValue) {
            $.ajax({
                url: "{{ route('due.date.update') }}",
                method: "POST",
                data: {
                    id: dataId,
                    [field]: newValue,
                    total_time: totalHours,
                    total_day: totalDays,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    toastr.success(response.success, "Success");
                    setTimeout(() => location.reload(), 1000);
                },
                error: function () {
                    toastr.error("Error saving data.", "Error");
                }
            });
        }

        $(this).parent().text(formatDisplayValue(field, newValue));

        parent.data("total-time", totalHours);
        parent.data("total-day", totalDays);
    });

    function calculateTotalTime(startDate, dueDate, dueTime) {
        if (!startDate || !dueDate) return { totalHours: 0, totalDays: 0 };

        let start = parseDate(startDate);
        let end = parseDate(dueDate);

        if (isNaN(start.getTime()) || isNaN(end.getTime())) return { totalHours: 0, totalDays: 0 };

        let totalDays = Math.floor((end - start) / (1000 * 60 * 60 * 24));
        let totalHours = 0;

        if (dueTime) {
            let [dueHour, dueMinute] = dueTime.split(":").map(Number);
            totalHours = totalDays * 24 + dueHour + dueMinute / 60;
        }

        return { totalHours, totalDays };
    }

    function parseDate(dateString) {
        let parts = dateString.split(".");
        if (parts.length === 3) {
            return new Date(`${parts[0]}-${parts[1]}-${parts[2]}`);
        }
        return new Date(dateString);
    }

    function formatInputValue(type, value) {
        if (type === "date") {
            return value ? parseDate(value).toISOString().split("T")[0] : "";
        }
        return value;
    }

    function formatDisplayValue(field, value) {
        if (field === "due_date") {
            let date = parseDate(value);
            return date.toLocaleDateString("de-DE", { day: "2-digit", month: "short", year: "numeric" });
        }
        return value;
    }
});

    </script>



<script>
    document.addEventListener('click', function (event) {
    if (event.target.matches('.change-btn')) {
        const taskId = event.target.getAttribute('data-task-id');
        const oldEmployee = event.target.getAttribute('data-old-employee-id');
        console.log('Task ID from element:', taskId);

        const taskInput = document.getElementById('task_id');
        const oldEmployeeInput = document.getElementById('old_employee');
        if (taskInput) {
            taskInput.value = taskId;
            console.log('Task ID set in input:', taskInput.value);
        } else {
            console.error('Task ID input not found.');
        }

          if (oldEmployeeInput) {
            oldEmployeeInput.value = oldEmployee;
            console.log('Old Employee set in input:', oldEmployeeInput.value);
        } else {
            console.error('Old Employee input not found.');
        }

        $('#addEmployeeModal').modal('show');
    }
});

document.getElementById('save-add-emp').addEventListener('click', function () {
    const form = document.getElementById('add-employee-modal');
    const formData = new FormData(form); // Serialize form data

    // Debug: Log form data before submission
    console.log('Form Data:', Array.from(formData.entries()));

    fetch("{{ route('personal.task.add.employee') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value, // Include CSRF token
        },
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            console.log('Server Response:', data); // Debug: Log server response
            if (data.success) {
                $('#addEmployeeModal').modal('hide'); // Close the modal
                toastr.success('Mitarbeiter erfolgreich hinzugefügt!');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500); // 1.5-second delay to show success message
            } else {
                toastr.error(data.error || 'Fehler beim Hinzufügen des Mitarbeiters.');
            }
        })
        .catch(error => {
            console.error('Fehler beim Senden der Anfrage:', error); // Debug: Log fetch error
            toastr.error('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
        });
});



</script>   

<script>
    $(document).ready(function () {
        let rowIndex = 1; // Start from 1 because 0 is already used

        // Initialize select2 for existing rows
        initSelect2();
        // Add new employee area on click
 

        // Initialize Select2 for dynamically added rows
        function initSelect2() {
            $('.employee').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }

        // Employee formatting for Select2
        function formatEmployee(employee) {
            if (!employee.id) {
                return employee.text;
            }

            const imageUrl = $(employee.element).data('image');
            const employeeName = employee.text;

            const markup = `
                <div style="display: flex; align-items: center;">
                    <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
                    <span>${employeeName}</span>
                </div>
            `;

            return markup;
        }
 
             

            // handling save operation:

            // Attach the click event to the "speichern" button
                $(document).on('click', '#add_new_employee', function (e) {
                    e.preventDefault();

                    // Get the form and serialize the data
                    let form = $('#EmployeeForm');
                    let formData = form.serialize();

                    // Append CSRF token manually if necessary
                    formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');

                    // AJAX request
                    let actionUrl = '{{ route('personal.task.add.employee.details') }}'; // Update the route if necessary

                    $.ajax({
                        url: actionUrl,
                        type: 'POST',
                        data: formData,
                        beforeSend: function () {
                            $('#add_new_employee').prop('disabled', true).text('speichern...');
                        },
                        success: function (response) {
                            $('#add_new_employee').prop('disabled', false).text('speichern');
                            form.trigger('reset'); // Reset the form
                            $('#create').modal('hide'); // Close the modal
                            Swal.fire({
                                icon: 'success',
                                title: 'Erfolg',
                                text: 'Aufgabe erfolgreich gespeichert!',
                            }).then(() => {
                                location.reload(); // Reload the page
                            });
                        },
                        error: function (xhr) {
                            $('#add_new_employee').prop('disabled', false).text('speichern');

                            let serverErrors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            if (serverErrors) {
                                $.each(serverErrors, function (key, value) {
                                    errorMessages += `<li>${value}</li>`;
                                });

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Serverfehler',
                                    html: `<ul style="text-align: left;">${errorMessages}</ul>`,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Fehler',
                                    text: 'Es ist ein unerwarteter Fehler aufgetreten.',
                                });
                            }
                        },
                    });
                });

 
            // save operation end:


            // delete operation : star
              // Handle delete button click
                    $(document).on('click', '.delete_appointment', function (e) {
                        e.preventDefault();

                        // Get the appointment ID from the button's data attribute
                        const appointmentId = $(this).data('id');
                        
                        // Confirmation popup using SweetAlert
                        Swal.fire({
                            title: 'Sind Sie sicher?',
                            text: 'Möchten Sie diesen Mitarbeiter wirklich löschen?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ja, löschen!',
                            cancelButtonText: 'Abbrechen',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Send AJAX DELETE request
                                $.ajax({
                                    url: '{{ route('personal.task.delete.employee.details') }}', // Update to your delete route
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}', // Include CSRF token
                                        id: appointmentId, // Send the appointment ID
                                    },
                                    beforeSend: function () {
                                        Swal.fire({
                                            title: 'Löschen...',
                                            text: 'Bitte warten Sie.',
                                            didOpen: () => Swal.showLoading(),
                                        });
                                    },
                                    success: function (response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Erfolgreich!',
                                            text: response.message,
                                        }).then(() => {
                                            // Optionally reload the page or remove the deleted row
                                            location.reload();
                                        });
                                    },
                                    error: function (xhr) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Fehler',
                                            text: 'Beim Löschen ist ein Fehler aufgetreten.',
                                        });
                                    },
                                });
                            }
                        });
                    });
            // delete operation : Delete

    });
</script>

@endsection