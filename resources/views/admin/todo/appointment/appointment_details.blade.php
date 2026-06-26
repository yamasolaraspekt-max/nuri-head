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
<style>
    .report-card {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        background: #ffffff;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
        margin-bottom: 0.75rem;
    }

    .report-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .report-card-header img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.5rem;
    }

    .report-card-meta {
        font-size: 11px;
        color: #6b7280;
    }

    .report-card-body {
        font-size: 13px;
        color: #111827;
    }

    .report-card-footer {
        margin-top: 0.5rem;
        border-top: 1px dashed #e5e7eb;
        padding-top: 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;
        justify-content: space-between;
    }

    .report-comments {
        margin-top: 0.4rem;
        padding-left: 2.5rem;
        border-left: 2px solid #e5e7eb;
    }

    .report-comment-item {
        font-size: 11px;
        margin-bottom: 0.25rem;
        color: #374151;
    }

    .report-comment-input {
        font-size: 11px;
        margin-top: 0.25rem;
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
                    <h2 class="content-header-title float-left mb-0">TERMINDETAILS</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/appointments') }}">Terminliste</a></li>
                            <li class="breadcrumb-item active"><a>details</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">   

                <div class="row">
                    <div class="col-xl-4 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header"> 
                                
                                    @php
                                        $previousUrl = url()->previous(); // Get the previous URL
                                        $calendarUrl = url('/tasks/calendar/personal'); // Define your calendar URL
                                        $personalTaskUrl = url('/appointments'); // Define personal task URL

                                        // Decide where to go
                                        $redirectUrl = str_contains($previousUrl, 'calendar') ? $calendarUrl : $personalTaskUrl;
                                    @endphp

                                    <a type="button" href="{{ $redirectUrl }}" class="btn btn-outline-primary mb-1 waves-effect waves-light">
                                        Zurück
                                    </a>
 

                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap">
                                                <tbody> 

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Titel :</span></td>
                                                        <td>{{ $data->name }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Ort :</span></td>
                                                        <td>
                                                            <i class="feather icon-map"></i> <small>
                                                                <a class="map_modal" data-latitude="{{ $data->latitude }}" data-longitude="{{ $data->longitude }}">
                                                                    {{ $data->full_address }}  
                                                                </a>
                                                            </small>
                                                        </td>
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
                                                        <td><span class="font-medium">Betrieb :</span></td>
                                                        <td>
                                                             {{ $data->branch }}
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

                                                            $status = $map[$data->status] ?? 'Status unbekannt'; 


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
                                                                    <option value="new" {{ $data->status == 'new' ? 'selected' : '' }}>Neue</option>
                                                                    <option value="start" {{ $data->status == 'start' ? 'selected' : '' }}>Starten</option>
                                                                    <option value="on_going" {{ $data->status == 'on_going' ? 'selected' : '' }}>Im Prozess</option>
                                                                    <option value="on_review" {{ $data->status == 'on_review' ? 'selected' : '' }}>Kurz vor Abschluss</option>
                                                                    <option value="completed" {{ $data->status == 'completed' ? 'selected' : '' }}>Vollendet</option>
                                                                    <option value="pause" {{ $data->status == 'pause' ? 'selected' : '' }}>Pause</option>
                                                                    <option value="cancel" {{ $data->status == 'cancel' ? 'selected' : '' }}>Abbrechen</option>
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
                                                        <td><span class="font-medium">Startzeit:</span></td>
                                                        <td>{{ \Carbon\Carbon::parse($data->start_date . ' ' . $data->start_time)->locale('de')->isoFormat('DD. MMM YYYY - HH:mm') }}</td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Endzeit:</span></td>
                                                        <td>{{ \Carbon\Carbon::parse($data->end_date . ' ' . $data->end_time)->locale('de')->isoFormat('DD. MMM YYYY - HH:mm') }}</td>
                                                    </tr>


 

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Intern Teilnehmer	 :</span></td>
                                                        <td class="p-1">
                                                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                                 @foreach ($group_emp->unique('employee_id') as $emp)
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
                                                                    @endforeach


                                                            </ul>
                                                        </td>
                                                    </tr>

                                                    <tr class="border-b !border-defaultborder dark:!border-defaultborder/10">
                                                        <td><span class="font-medium">Zuletzt aktualisiert am :</span></td>
                                                        <td>
                                                            <span class="text-primarytint1color font-medium">
                                                                {{ \Carbon\Carbon::parse($data->updated_at)->locale('de')->isoFormat('DD. MMM YYYY') }}
                                                            </span>
                                                            <span class="text-primarytint1color font-medium">
                                                                {{ \Carbon\Carbon::parse($data->updated_at)->locale('de')->isoFormat('HH:mm') }}
                                                            </span>
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
                                                    <form   action="{{ url('appointment.upload.files') }}"  method="post"  class="dropzone" id="file-upload" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                  
                                                        <input type="hidden" name="appointment_id" value="{{$data->id}}"> 
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
                        <section id="nav-justified">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card overflow-hidden">
                                        <div class="card-header">
                                            <h4 class="card-title">{{ $data->name }} </h4>
                                             <a type="button" href="{{ url('appointment/'.$data->id.'/edit') }}" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light"><i class="feather icon-edit"></i></a>

                                        </div>
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <p>{{ $data->note ?? 'Keine Beschreibung verfügbar' }}</p>
                                                 <i class="feather icon-map"></i> <small>
                                                        <a class="map_modal" data-latitude="{{ $data->latitude }}" data-longitude="{{ $data->longitude }}">
                                                             {{ $data->full_address }} 
                                                        </a>
                                                    </small>
                                                  
                                                 

                                                <!-- Tab panes -->
                                                <div class="tab-content pt-1">
                                                    <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified">
                                                       <div class="card match-height">
                                                            <div class="card-content"> 
                                                                @if($data->customer_id)
                                                                <div class="card-body">  
                                                                    <!-- Task key rows  -->
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="card"> 
                                                                                    <div class="card-body">
                                                                                        <div class="table-responsive">
                                                                                            <table class="table">
                                                                                                <thead>
                                                                                                    <tr>
                                                                                                        <th>Kunde</th>
                                                                                                        <th>Kontakt</th>
                                                                                                        <th>Adress</th>  
                                                                                                    </tr>
                                                                                                </thead>

                                                                                                <tbody> 
                                                                                                         @php
                                                                                                            $customer_id = $data->customer_id ?? null;
                                                                                                            $customer = DB::table('new_leads')
                                                                                                                            ->where('id', $customer_id)
                                                                                                                            ->select('id', 'name', 'lastname', 'street', 'postcode', 'city', 'latitude', 'longitude', 'phone', 'email')
                                                                                                                            ->first();
                                                                                                            @endphp     

                                                                                                            @if ($customer)
                                                                                                                <td>
                                                                                                                    <a href="{{ url('new_lead_profile/'.$customer->id) }}" class="m-0 p-0"> {{ $customer->name }} {{ $customer->lastname }}</a> 
                                                                                                                </td>
                                                                                                                    <td>
                                                                                                                        <p><i class="feather icon-phone"></i> {{$customer->phone}}</p>
                                                                                                                        <p><i class="feather icon-mail"></i> {{$customer->email}}</p>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <i class="feather icon-map"></i> <small>
                                                                                                                            <a class="map_modal" data-latitude="{{ $customer->latitude }}" data-longitude="{{ $customer->longitude }}">
                                                                                                                                {{ $customer->street }} {{ $customer->postcode }}, {{ $customer->city }}
                                                                                                                            </a>
                                                                                                                        </small>
                                                                                                                    </td>
                                                                                                            @else
                                                                                                                <td colspan="3">Kein Kunde vorhanden</td> 
                                                                                                            @endif 
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
                                                                @endif

                                                                <hr> 
                                                                <div class="card-body"> 
                                                                    <ul class="nav nav-tabs" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="false">Aktivität</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link " id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="true">Kommentare</a>
                                                                        </li> 

                                                                           <li class="nav-item">
                                                                                <a class="nav-link position-relative" id="report-tab" data-toggle="tab" href="#report" aria-controls="report" role="tab" aria-selected="true">
                                                                                    Report
                                                                                    <span id="report-status-badge" class="badge badge-pill ml-1" style="font-size: 10px;"></span>
                                                                                </a> 
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
                                                                                                <input type="hidden" name="appointment_id" value="{{ $data->id }}">
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


                                                                            <div class="tab-pane" id="report" aria-labelledby="report-tab" role="tabpanel">
                                                                                <div class="card-body p-1">
                                                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                        <h4 class="font-weight-bold mb-0">
                                                                                            Report
                                                                                            <span id="report-status-badge" class="badge badge-pill ml-1" style="display:none;"></span>
                                                                                        </h4>
                                                                                        <button id="toggle-report-btn" class="btn btn-sm btn-warning">
                                                                                            Report aktivieren
                                                                                        </button>
                                                                                    </div>

                                                                                    {{-- Editor --}}
                                                                                    <div id="report-editor-wrapper" style="display:none;">
                                                                                        <label class="font-weight-bold small text-muted mb-50">Neuer Bericht</label>
                                                                                        <div id="quill-editor" style="height: 150px;"></div>
                                                                                        <div class="mt-1 d-flex justify-content-between">
                                                                                            <button id="cancel-report-edit-btn" class="btn btn-sm btn-secondary">
                                                                                                Abbrechen
                                                                                            </button>
                                                                                            <button id="save-report-btn" class="btn btn-sm btn-success">
                                                                                                Bericht speichern
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- List of reports --}}
                                                                                    <div id="report-list" class="mt-2"></div>
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
                        </section>
                    </div>
                    
                </div>
                 
   

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
            const appointment_id = {{ $data->id }}; // Assuming $data.id is defined
            fetchFiles(appointment_id);
        });


        
            Dropzone.autoDiscover = false;

       const myDropzone = new Dropzone("#file-upload", {
            url: "{{ route('appointment.upload.files') }}", // Use Laravel's route helper for the correct URL
            method: "POST",
            paramName: "file",
            maxFilesize: 2, // Max file size in MB
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx",
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
        function fetchFiles(appointment_id) {
    $.ajax({
        url: `{{ route('appointment.get.files', ':appointment_id') }}`.replace(':appointment_id', appointment_id), // Correct route URL
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
                                <p id="image_name_${file.id}" ondblclick="makeEditable(${file.id}, ${file.appointment_id}, '${file.image_name}')">
                                    ${file.image_name}
                                </p>
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light" onclick="deleteFile(${file.id}, ${appointment_id})">
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
        function makeEditable(fileId, appointment_id, currentName) {
            const element = $(`#image_name_${fileId}`);
            const input = `
                <input type="text" id="edit_image_name_${fileId}" 
                    value="${currentName}" 
                    class="form-control"
                    onblur="updateFileName(${fileId}, ${appointment_id})"
                    onkeydown="checkForEnter(event, ${fileId}, ${appointment_id})">
            `;
            element.html(input);
            $(`#edit_image_name_${fileId}`).focus();
        }

        // Check for Enter key during editing
        function checkForEnter(event, fileId, appointment_id) {
            if (event.key === "Enter") {
                updateFileName(fileId, appointment_id);
            }
        }

        // Update file name
        function updateFileName(fileId, appointment_id) {
            const newFileName = $(`#edit_image_name_${fileId}`).val();

            $.ajax({
                url: "{{ route('appointment.update.files.name') }}",
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: fileId,
                    appointment_id: appointment_id,
                    image_name: newFileName
                },
                success: function (response) {
                    toastr.success('File name updated successfully!');
                    fetchFiles(appointment_id); // Refresh table
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
                fileContent = `<img src="/images/appointment/document/${filePath}" alt="${fileName}" class="img-fluid">`;
            } else {
                fileContent = `<iframe src="/images/appointment/document/${filePath}" width="100%" height="500px"></iframe>`;
            }

            $('#filePreviewContent').html(fileContent);
            $('#fileModal').modal('show');
        }

        // Delete file
        function deleteFile(fileId, appointment_id) {
            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    url: `appointment/attachment/delete/${fileId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        toastr.error('File deleted successfully!');
                        fetchFiles(appointment_id); // Reload files after deletion
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
            url: '{{ route("appointment.comment.store") }}',
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
            url: '{{ route("appointment.comment.reply") }}',
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
    let taskId = $('input[name="appointment_id"]').val();

    $.ajax({
        url: `/appointment_comment/${taskId}`,
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
                            <input type="hidden" name="appointment_id" value="${comment.appointment_id}">
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
            url: `/notifications/appointment/${taskId}`, // Use GET and include task_id in the URL
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

 


<script>
    $('.map_modal').on('click', function () {
        const destinationLat = parseFloat($(this).attr('data-latitude'));
        const destinationLng = parseFloat($(this).attr('data-longitude'));
        const destinationAddress = $(this).text();

        // Show SweetAlert2 waiting message
        Swal.fire({
            title: 'Warten...',
            text: 'Die Karte wird geladen...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const currentLat = position.coords.latitude;
                const currentLng = position.coords.longitude;

                // Create a temporary container for the map
                const mapContainer = document.createElement('div');
                mapContainer.id = 'map-container';
                mapContainer.style.width = '100%';
                mapContainer.style.height = '400px';

                // Initialize Google Maps
                const map = new google.maps.Map(mapContainer, {
                    zoom: 7,
                    center: { lat: currentLat, lng: currentLng }
                });

                // Display the map and direction
                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);

                // Calculate and display the route
                directionsService.route({
                    origin: { lat: currentLat, lng: currentLng },
                    destination: { lat: destinationLat, lng: destinationLng },
                    travelMode: 'DRIVING'
                }, (result, status) => {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                        const duration = result.routes[0].legs[0].duration.text;

                        // Show SweetAlert2 with the map and travel details
                        Swal.fire({
                            title: 'Routeninformationen',
                            html: `
                                <div id="map-container-wrapper" style="width: 100%; height: 400px; margin-bottom: 10px;"></div>
                                <p>Ziel: <strong>${destinationAddress}</strong></p>
                                <p>Geschätzte Fahrzeit: <strong>${duration}</strong></p>
                                <p>Möchten Sie die Route in Google Maps öffnen?</p>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Wegbeschreibung anzeigen',
                            cancelButtonText: 'Abbrechen',
                            didRender: () => {
                                // Append the map to SweetAlert2's HTML container
                                document.getElementById('map-container-wrapper').appendChild(mapContainer);
                            },
                            icon: 'info',
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.open(`https://www.google.com/maps/dir/?api=1&origin=${currentLat},${currentLng}&destination=${destinationLat},${destinationLng}`, '_blank');
                            }
                        });
                    } else {
                        Swal.fire('Fehler', 'Konnte die Route nicht abrufen. Bitte versuchen Sie es später erneut.', 'error');
                    }
                });
            }, error => {
                Swal.fire('Geolocation Fehler', 'Konnte den aktuellen Standort nicht abrufen.', 'error');
            });
        } else {
            Swal.fire('Geolocation nicht verfügbar', 'Ihr Browser unterstützt keine Standortdienste.', 'error');
        }
    });
</script>



<script>
    function initializeAutocomplete() {
        const input = document.getElementById('location-input');
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['address'],  // Suggest only addresses
            componentRestrictions: { country: 'DE' } // Restrict to Germany
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            const addressComponents = place.address_components;

            // Initialize variables to extract components
            let streetNumber = "";
            let route = "";
            let postalCode = "";
            let city = "";

            // Extract components
            addressComponents.forEach(component => {
                const types = component.types;
                
                if (types.includes("street_number")) {
                    streetNumber = component.long_name;
                }
                if (types.includes("route")) {
                    route = component.long_name;
                }
                if (types.includes("postal_code")) {
                    postalCode = component.long_name;
                }
                if (types.includes("locality") || types.includes("sublocality")) {
                    city = component.long_name;
                }
            });

            // Populate fields
            document.getElementById('location-input').value = `${route} ${streetNumber}`;  // Street name and number
            document.getElementById('postal_code-input').value = postalCode;  // Postcode
            document.getElementById('city-input').value = city;  // City
            document.getElementById('latitude-input').value = place.geometry.location.lat();  // Latitude
            document.getElementById('longitude-input').value = place.geometry.location.lng();  // Longitude

            // Optional: Debugging log
            console.log("Street:", route, streetNumber);
            console.log("City:", city);
            console.log("Postal Code:", postalCode);
            console.log("Latitude:", place.geometry.location.lat());
            console.log("Longitude:", place.geometry.location.lng());
        });
    }

    // Dynamically load Google Maps script
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initializeAutocomplete";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    loadGoogleMapsAPI();  // Load the API dynamically
</script>
 

<script>
    let quill;
    let currentEditingReportId = null;

    document.addEventListener('DOMContentLoaded', function () {
        quill = new Quill('#quill-editor', {
            theme: 'snow'
        });

        const appointmentId = @json($data->id);
        let isReportActive = {{ $data->is_report === '1' ? 'true' : 'false' }};

        if (isReportActive) {
            $('#report-editor-wrapper').show();
            $('#toggle-report-btn').text('Report deaktivieren');
        }

        loadReports();
        updateReportStatusBadge();

        // Toggle report flag on appointment
        $('#toggle-report-btn').on('click', function () {
            const newStatus = isReportActive ? '0' : '1';

            $.ajax({
                url: '/appointments/toggle-report/' + appointmentId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_report: newStatus
                },
                success: function () {
                    isReportActive = !isReportActive;
                    $('#toggle-report-btn').text(isReportActive ? 'Report deaktivieren' : 'Report aktivieren');
                    $('#report-editor-wrapper').toggle(isReportActive);

                    if (!isReportActive) {
                        $('#report-list').empty();
                    }

                    updateReportStatusBadge();
                }
            });
        });

        // Save (create or update) report
        $('#save-report-btn').on('click', function () {
            const content = quill.root.innerHTML;
            const plain = quill.getText().trim();

            if (!plain) {
                return Swal.fire('Fehler', 'Bericht darf nicht leer sein.', 'warning');
            }

            const routeBase = '/appointments/' + appointmentId + '/reports';
            const url   = currentEditingReportId ? routeBase + '/' + currentEditingReportId : routeBase;
            const type  = currentEditingReportId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: type,
                data: {
                    _token: '{{ csrf_token() }}',
                    report: content
                },
                success: function () {
                    Swal.fire('Gespeichert', 'Bericht wurde gespeichert.', 'success');
                    quill.setContents([]);
                    currentEditingReportId = null;
                    $('#cancel-report-edit-btn').text('Abbrechen');
                    loadReports();
                    updateReportStatusBadge();
                }
            });
        });

        // Cancel edit
        $('#cancel-report-edit-btn').on('click', function () {
            currentEditingReportId = null;
            quill.setContents([]);
            $(this).text('Abbrechen');
        });

        function formatDate(isoDate) {
            if (!isoDate) return '';
            const d = new Date(isoDate);
            return ('0' + d.getDate()).slice(-2) + '.' + ('0' + (d.getMonth() + 1)).slice(-2) + '.' + d.getFullYear();
        }

        function renderReports(reports) {
            const $list = $('#report-list');
            $list.empty();

            if (!reports.length) {
                $list.html('<p class="text-muted small mb-0">Noch kein Bericht vorhanden.</p>');
                return;
            }

            reports.forEach(function (item) {
                const authorName = item.author
                    ? (item.author.name + ' ' + (item.author.lastname || ''))
                    : 'Unbekannt';

                const authorImage = item.author
                    ? '/images/employee/' + item.author.image
                    : '/images/employee/default.png';

                let commentsHtml = '';
                if (item.comments && item.comments.length) {
                    commentsHtml += '<div class="report-comments">';
                    item.comments.forEach(function (c) {
                        commentsHtml += `
                            <div class="report-comment-item">
                                <span class="text-muted">
                                    <i class="feather icon-message-square"></i>
                                    ${$('<div>').text(c.text).html()}
                                    <span class="ml-25">${c.created_at ? '(' + c.created_at + ')' : ''}</span>
                                </span>
                            </div>
                        `;
                    });
                    commentsHtml += '</div>';
                }

                const cardHtml = `
                    <div class="report-card" data-report-id="${item.id}">
                        <div class="report-card-header">
                            <img src="${authorImage}" alt="Avatar">
                            <div>
                                <div class="font-weight-bold" style="font-size: 13px;">${authorName}</div>
                                <div class="report-card-meta">
                                    <i class="feather icon-calendar"></i>
                                    ${formatDate(item.report_date)}
                                </div>
                            </div>
                        </div>
                        <div class="report-card-body">
                            ${item.report}
                        </div>
                        <div class="report-card-footer">
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-success report-like-btn">
                                    <i class="feather icon-thumbs-up"></i>
                                    <span class="like-count">${item.likes}</span>
                                </button>
                                <button class="btn btn-outline-danger report-dislike-btn">
                                    <i class="feather icon-thumbs-down"></i>
                                    <span class="dislike-count">${item.dislikes}</span>
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary report-edit-btn">
                                    <i class="feather icon-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger report-delete-btn">
                                    <i class="feather icon-trash"></i>
                                </button>
                                <button class="btn btn-outline-secondary report-comment-toggle-btn">
                                    <i class="feather icon-message-circle"></i> Kommentar
                                </button>
                            </div>
                        </div>

                        <div class="report-comment-input" style="display:none;">
                            <div class="input-group input-group-sm mt-25">
                                <input type="text" class="form-control report-comment-text" placeholder="Kommentar hinzufügen...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary report-comment-save-btn" type="button">
                                        <i class="feather icon-send"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        ${commentsHtml}
                    </div>
                `;

                $list.append(cardHtml);
            });
        }

        function loadReports() {
            $.get('/appointments/' + appointmentId + '/reports', function (data) {
                renderReports(data.reports || []);
            });
        }

        function updateReportStatusBadge() {
            const badge = $('#report-status-badge');
            badge.removeClass().addClass('badge badge-pill ml-1').hide();

            $.get('/appointments/' + appointmentId + '/reports', function (data) {
                const hasReports = (data.reports || []).length > 0;

                if (isReportActive && !hasReports) {
                    const startDate = new Date(@json($data->start_date));
                    const today = new Date();
                    const diffDays = Math.floor((today - startDate) / (1000 * 60 * 60 * 24));

                    if (diffDays > 0) {
                        badge
                            .addClass(diffDays > 3 ? 'badge-danger' : 'badge-warning')
                            .attr('title', `Bericht ausstehend seit ${diffDays} Tag(en)`)
                            .text(`${diffDays} Tage`)
                            .tooltip('dispose').tooltip()
                            .show();
                    }
                } else if (hasReports) {
                    badge
                        .removeClass()
                        .addClass('badge badge-success ml-1')
                        .attr('title', 'Bericht vorhanden')
                        .html('<i class="feather icon-check-circle"></i>')
                        .tooltip('dispose').tooltip()
                        .show();
                }
            });
        }

        // Delegated events for dynamic report cards
        $(document).on('click', '.report-edit-btn', function () {
            const $card = $(this).closest('.report-card');
            const id = $card.data('report-id');
            const bodyHtml = $card.find('.report-card-body').html();

            currentEditingReportId = id;
            quill.root.innerHTML = bodyHtml;
            $('#report-editor-wrapper').slideDown();
            $('#cancel-report-edit-btn').text('Bearbeitung abbrechen');

            $('html, body').animate({
                scrollTop: $('#report-editor-wrapper').offset().top - 100
            }, 300);
        });

        $(document).on('click', '.report-delete-btn', function () {
            const $card = $(this).closest('.report-card');
            const id = $card.data('report-id');

            Swal.fire({
                title: 'Bericht löschen?',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/appointments/' + appointmentId + '/reports/' + id,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () {
                            quill.setContents([]);
                            currentEditingReportId = null;
                            loadReports();
                            updateReportStatusBadge();
                            Swal.fire('Gelöscht', 'Bericht wurde gelöscht.', 'success');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.report-like-btn', function () {
            const $card = $(this).closest('.report-card');
            const id = $card.data('report-id');
            const $btn = $(this);

            $.post('/appointment-reports/' + id + '/react', {
                _token: '{{ csrf_token() }}',
                type: 'like'
            }, function (data) {
                $btn.find('.like-count').text(data.likes);
                $card.find('.report-dislike-btn .dislike-count').text(data.dislikes);
            });
        });

        $(document).on('click', '.report-dislike-btn', function () {
            const $card = $(this).closest('.report-card');
            const id = $card.data('report-id');
            const $btn = $(this);

            $.post('/appointment-reports/' + id + '/react', {
                _token: '{{ csrf_token() }}',
                type: 'dislike'
            }, function (data) {
                $card.find('.report-like-btn .like-count').text(data.likes);
                $btn.find('.dislike-count').text(data.dislikes);
            });
        });

        $(document).on('click', '.report-comment-toggle-btn', function () {
            const $card = $(this).closest('.report-card');
            $card.find('.report-comment-input').slideToggle(150);
        });

        $(document).on('click', '.report-comment-save-btn', function () {
            const $card = $(this).closest('.report-card');
            const id = $card.data('report-id');
            const $input = $card.find('.report-comment-text');
            const text = $input.val().trim();

            if (!text) {
                return;
            }

            $.post('/appointment-reports/' + id + '/comments', {
                _token: '{{ csrf_token() }}',
                comment: text
            }, function () {
                $input.val('');
                loadReports();
            });
        });

        // init tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
</script>

@endsection