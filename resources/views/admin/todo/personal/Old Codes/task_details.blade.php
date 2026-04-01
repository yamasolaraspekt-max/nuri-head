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
</style>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css' rel='stylesheet' />
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- FullCalendar CSS -->
 
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/noui-slider.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-noui.css')}}"> 

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
                    <h2 class="content-header-title float-left mb-0">Aufgabenliste</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                        </ol>
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
                                                        <td><span class="font-medium">Aufgaben-ID :</span></td>
                                                        <td>{{ $data->task_id }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgabentitel :</span></td>
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

                                                            $status = $map[$data->task_status] ?? 'Status unbekannt'; 
                                                        @endphp

                                                        <td>
                                                            <span class="font-medium text-secondary current-status">{{ $status}}</span>
                                                            <i class="feather icon-edit project_status" style="cursor: pointer;"></i>

                                                            <form id="project_status_form" style="display: none;">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                                <select name="project_status" class="form-control project_status_select">
                                                                    <option value="new" {{ $data->task_status == 'new' ? 'selected' : '' }}>Neue</option>
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
                                                        <td><span class="font-medium">Aufgabenpriorität :</span></td>
                                                        <td>
                                                            <span class="badge bg-danger/10 text-danger"><i class="ri-circle-fill text-[8px] me-1"></i> {{ $data->priority }}</span>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Startdatum :</span></td>
                                                        <td>{{ date('d, M Y', strtotime($data->created_at)) }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Enddatum :</span></td>
                                                        <td> </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Aufgabendauer:</span></td>
                                                        <td>
                                                             
                                                        </td>
                                                    </tr>
 

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Verantwortlichen :</span></td>
                                                        <td class="p-1">
                                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                    @foreach ($task_employee as $emp) 
                                                                        <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="{{ $emp->name }} {{ $emp->lastname }}" class="avatar pull-up">
                                                                            <img class="media-object rounded-circle" src="{{ asset('images/employee/'.$emp->image) }}" alt="Avatar" height="30" width="30">
                                                                        </li> 
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
                                                    <form action="{{ route('upload.files') }}" method="post" enctype="multipart/form-data" class="dropzone" id="file-upload">
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
                    <div class="col-xl-8 col-md-6 col-sm-12">
                        <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <h4 class="card-title">Task Details</h4>
                                            </div>
                                        <div class="card-body">
                                            <p>Aufgabentitel: <strong>{{ $data->task_title }}</strong> 
                                                <span class="badge ti-btn-soft-primary1 text-[10px] font-medium">#erstellt am {{ date('d M, Y', strtotime($data->created_at)) }}</span>
                                            </p>
                                            <p>Aufgabenbeschreibung:</p>
                                            <p>{{ $data->description ?? 'Keine Beschreibung verfügbar' }}</p>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="card">
                                                            <div class="card-header  bold"><strong>Hauptaufgabe</strong></div>
                                                            <div class="card-body">
                                                                <ul class="task-details-key-tasks mb-0 ps-8" style="list-style: none;">
                                                                    @foreach ($key_task->where('personal_task_id', '=', $data->id) as $task)
                                                                        <li>
                                                                            <fieldset class="d-flex mb-1">
                                                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                    <input type="checkbox" name="done" class="done" data-id="{{ $task->id }}" data-task-id="{{ $task->personal_task_id }}" {{ $task->is_completed== 1 ? 'checked' : '' }} value="false">
                                                                                    <span class="vs-checkbox">
                                                                                        <span class="vs-checkbox--check">
                                                                                            <i class="vs-icon feather icon-check"></i>
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                                <div class="data">
                                                                                    <span class="task-title {{ $task->is_completed== 1 ? 'line-through' : '' }}" id="task-title-{{ $task->id }}">{{ $task->task }}</span>
                                                                                    @if($task->is_completed == 1)
                                                                                    <p class="m-0">
                                                                                        <div class="badge badge-square badge-primary">
                                                                                            <i class="feather icon-calendar"></i>
                                                                                            <span>{{ \Carbon\Carbon::parse($task->done_date)->isoFormat('DD.MM.YYYY') }}</span>
                                                                                        </div>  
                                                                                        <div class="badge badge-square badge-primary">
                                                                                            <i class="feather icon-calendar"></i>
                                                                                            @php
                                                                                                $hours = floor($task->total_time / 60); // Calculate hours
                                                                                                $minutes = $task->total_time % 60; // Calculate remaining minutes
                                                                                            @endphp
                                                                                            <span>{{ sprintf('%02d:%02d', $hours, $minutes) }}</span>

                                                                                        </div> 
                                                                                    </p>
                                                                                    @elseif($task->is_completed != 1)
                                                                                            @php
                                                                                            // Map statuses to labels
                                                                                            $statusMapping = [
                                                                                                '2' => 'Teil',
                                                                                                '3' => 'Unmöglich',
                                                                                                '4' => 'Unfähig', 
                                                                                            ];

                                                                                            // Check if done_status exists in the mapping
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
                                                                                </div>
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
                                                                            </fieldset>
                                                                        </li>
                                                                    @endforeach 
                                                            </button>
                                                            
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">Handbuch und Hilfe</div>
                                                        <div class="card-body">
                                                            <div class="accordion" id="accordionExample0" data-toggle-hover="true">
                                                                @foreach ($sub_task as $index => $task)
                                                                    <div class="collapse-border-item collapse-header card collapse-bordered">
                                                                        <div class="card-header p-1" id="heading{{ $index }}" data-toggle="collapse" role="button" data-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                                            <p class="lead primary m-0" style="font-size: 13px; font-weight: bold;">
                                                                                <i class="feather icon-link"></i> {{ $task->sub_task_title }}
                                                                            </p>
                                                                        </div>
                                                                        <div id="collapse{{ $index }}" class="collapse" aria-labelledby="heading{{ $index }}" data-parent="#accordionExample0">
                                                                            <div class="card-body">
                                                                            {{$task->description}}
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
                                    <hr>
                                    @php
                                            $check_status = DB::table('personal_task_keys')
                                                ->where('personal_task_id', $data->id)
                                                ->whereNotNull('is_completed')
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



                                </div>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-12">
                                <div class="card overflow-hidden"> 
                                    <div class="card-content">
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
                                                            <h4 class="card-title">Notifications</h4>
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
                        </div>
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
                    <h5 class="modal-title" id="myModalLabel120">Aufgabenstatus</h5>
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

                        <label for="reason">Fortschritt</label>
                        <input type="range" class="form-control work_progress_status" name="work_progress">

                        <label for="more_time">Aufgabendauer(Zeit)</label>
                        <input type="time" name="more_time" class="form-control" required >

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
@endsection


@section('script') 
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
 
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js'></script>
  <script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>

 <script>
  // Ensure functions are loaded
$(document).ready(function () {
    const task_id = {{ $data->id }};  // Assuming $data->id is defined
    fetchFiles(task_id);
});

function fetchFiles(task_id) {
    $.ajax({
        url: `/personal_task_attachment/${task_id}`,
        method: 'GET',
        success: function(data) {
            console.log(data); 

            let tableContent = '';

            if (data.length === 0) {
                tableContent = `<tr><td colspan="3">No files uploaded.</td></tr>`;
            } else {
                data.forEach(file => {
                    tableContent += `
                        <tr id="row_${file.id}"> 
                            <td>   
                                <div class="avatar mr-1">
                                    <span class="avatar-icon feather icon-file" 
                                          onclick="openFileModal('${file.image}', '${file.image_name}', '${file.file_type}')"></span>
                                </div>  
                            </td>
                            <td>
                                <p id="image_name_${file.id}" ondblclick="makeEditable(${file.id}, ${file.task_id}, '${file.image_name}')">
                                    ${file.image_name}
                                </p>
                                <span>${(file.file_type)}</span>  
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light"
                                        onclick="deleteFile(${file.id}, ${task_id})">
                                    <i class="feather icon-trash"></i>
                                </button>  
                            </td>
                        </tr>`;
                });
            }

            $('#attachmentTable').html(tableContent);
        },
        error: function(xhr, status, error) {
            console.error(`Error fetching files: ${error}`);
            alert('Failed to load files. Check console for more details.');
        }
    });
}

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

function checkForEnter(event, fileId, taskId) {
    if (event.key === "Enter") {
        updateFileName(fileId, taskId);
    }
}

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
        success: function(response) {
            toastr.success('File name updated successfully!');
            fetchFiles(taskId); // Refresh table
        },
        error: function(xhr, status, error) {
            console.error(`Error updating file: ${error}`);
            alert('Failed to update file name. Check console for more details.');
        }
    });
}

 </script>

 <script>
    function openFileModal(filePath, fileName, fileType) {
    let fileContent;

    if (['jpg', 'jpeg', 'png'].includes(fileType.toLowerCase())) {
        fileContent = `<img src="/${filePath}" alt="${fileName}" class="img-fluid">`;
    } else {
        fileContent = `<iframe src="/${filePath}" width="100%" height="500px"></iframe>`;
    }

    $('#filePreviewContent').html(fileContent);
    $('#fileModal').modal('show');
}

function deleteFile(fileId, task_id) {
    if (confirm('Are you sure you want to delete this file?')) {
        $.ajax({
            url: `/personal_task_attachment/delete/${fileId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                toastr.error('File deleted successfully!');
                fetchFiles(task_id);  // Reload files after deletion
            },
            error: function(xhr, status, error) {
                console.error(`Error deleting file: ${error}`);
                alert('Failed to delete file. Check console for more details.');
            }
        });
    }
}

 </script>

 <script>
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
        success: function(response) {
            toastr.success('File name updated successfully!');
            fetchFiles(taskId); // Refresh table
        },
        error: function(xhr, status, error) {
            console.error(`Error updating file: ${error}`);
            alert('Failed to update file name. Check console for more details.');
        }
    });
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
    const $rangeValueDisplay = $('<span class="range-value"> 0</span>%</br>'); // Element to show range value
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


@endsection