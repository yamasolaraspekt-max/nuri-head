@extends('admin.layouts.app')
@section('title')
BEARBEITEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

.edit_task { 
    
 
        
        background: #f1f1f1; 
        z-index: 10000;
        width: 50% !important; /* Default width */
        max-width: 50% !important;
        max-height: 85vh; /* Ensures it doesn't go beyond 80% of viewport height */
        overflow-y: auto; /* Enables scrolling inside */
        
    }

 

    /* Ensure modal content area scrolls separately */
    .edit_task .modal-body {
        max-height: 85vh; /* Limit body height */
        overflow-y: auto; /* Enable scrolling */
        padding: 15px;
    }

    /* Sticky Header & Close Button */
    .edit_task .modal-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .edit_task .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        z-index: 10;
        padding: 10px;
        border-top: 1px solid #ddd;
    }

    /* Responsive styles for mobile */
    @media (max-width: 768px) {
        .edit_task {
            width: 90% !important; /* 90% width on mobile */
            max-width: 90% !important;
        }
    }


.edit_task_close {
   position: absolute;
    z-index: 4;
    left: -135px;
    top: 16%;
}

    </style>
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">TERMIN BEARBEITEN</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/appointments') }}">Termin</a></li>
                            <li class="breadcrumb-item active"><a>{{ $data->name }}</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">  
                  <div class="cards new_task_card edit_task" >   
                        <div class="card-body p-0"> 
                           <form id="task-store-form" method="POST" action="{{ route('appointment.update') }}">
                                @csrf
                                <div class="modal-body pt-0 pb-0">
                                    <div class="cards p-1">
                               
                                        <input type="hidden" name="id" value="{{$data->id}}">
                                        <input type="hidden" name="calendar" value="" id="calendarInput">
                                        <div class="form-body">
                                                <div class="row">   
                                                    <div class="col-md-10 col-10">
                                                        <label for="task_title">Titel / Name *</label>
                                                        <input type="text" id="name" class="form-control" name="name" value="{{ $data->name  }}">
                                                    </div> 

                                                    <div class="col-md-2"> 
                                                         <input type="hidden" name="color" id="color" value="{{$data->color}}"> 
                                                        <div class="btn-group dropup dropdown-icon-wrapper mt-1 " id="color_drop_down">
                                                            <button type="button" class="btn btn-icon    waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                <i class="fa fa-square" id="colorIcon" style="color: {{$data->color}};"></i>
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

                                                        <div class="col-md-5 col-12 ">
                                                            <label for="start_date">Startdatum *</label>
                                                            <input type="hidden" name="same_id" value="same">
                                                            <input type="date" id="start_date" class="form-control" name="start_date"  value="{{$data->start_date}}">
                                                            <input type="hidden" id="end_date" class="form-control" name="end_date"  value="{{$data->end_date}}">

                                                        </div> 

                                                          <div class="col-md-5 col-12 ">
                                                            <label for="start_date">Enddatum *</label>  
                                                            <input type="date" id="end_date" class="form-control" name="end_date"  value="{{$data->end_date}}">

                                                        </div> 
                                                        <div class="col-md-5 col-12">
                                                            <label for="start_time">Startzeit *</label>
                                                            <input type="time" id="start_time" class="form-control" name="start_time"  value="{{ \Carbon\Carbon::parse($data->start_time)->format('H:i') }}">
                                                        </div>

                                                        <div class="col-md-5 col-12 ">
                                                                <label for="end_time">Endzeit *</label>
                                                            <input type="time" id="end_time" class="form-control" name="end_time" 
                                                             value="{{ \Carbon\Carbon::parse($data->end_time)->format('H:i') }}"> 
                                                        </div> 
                                                        <div class="col-md-5 col-12 ">
                                                            <label for="total_time">Termin Dauer </label>
                                                            <input type="number" id="total_time" class="form-control" name="total_time" value="{{$data->total_time}}">
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="row">
                                                                <!-- Öffentlich Switch -->
                                                                <div class="col-md-6">
                                                                    <label for="customSwitchPublic">Öffentlich</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" class="custom-control-input" id="customSwitchPublic" name="public"
                                                                            @if($data->public == 1) checked @endif>
                                                                        <label class="custom-control-label" for="customSwitchPublic">
                                                                            <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                                                            <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                <!-- Kontakt Switch -->
                                                                <div class="col-md-6">
                                                                    <label for="customSwitchContact">Kontakt</label>
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox" class="custom-control-input" id="customSwitchContact" name="is_contact"
                                                                            @if($data->is_contact == '1') checked @endif>
                                                                        <label class="custom-control-label" for="customSwitchContact">
                                                                            <span class="switch-icon-left"><i class="feather icon-user"></i></span>
                                                                            <span class="switch-icon-right"><i class="feather icon-user-x"></i></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- pre_type Dropdown (conditionally visible) -->
                                                            <div class="form-group mt-2" id="preTypeBox" style="display: none;">
                                                                <label for="pre_type">Typ</label>
                                                                <select name="pre_type" class="form-control select2">
                                                                    <option value="">Auswählen</option>
                                                                    @php $selectedPreType = $data->pre_type; @endphp
                                                                    @foreach(['Lead','Lieferant','Hersteller','Kooperationspartner','Architekt','Nachunternehmer','Bank','Versicherung','Bewerber','Sonstige'] as $type)
                                                                        <option value="{{ $type }}" @if($selectedPreType === $type) selected @endif>{{ $type }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
 

                                                        <div class="col-md-12 col-12"> 
                                                            <label for="task_title">Teilnehmer *</label> 
                                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                                @php
                                                                    // Fetch selected employee IDs into an array for optimized lookup
                                                                    $selectedIds = DB::table('main_appointment_employees')
                                                                                    ->where('appointment_id', $data->id)
                                                                                    ->pluck('employee_id')
                                                                                    ->toArray();
                                                                @endphp

                                                                @foreach ($employees as $emp)
                                                                    <option value="{{ $emp->id }}" 
                                                                            data-image="{{ asset('images/employee/'.$emp->image) }}" 
                                                                            @if(in_array($emp->id, $selectedIds)) selected @endif>
                                                                        {{ $emp->name }}
                                                                    </option>
                                                                @endforeach

                                                            </select>
                                                        </div> 

                                                        <div class="col-md-6"  >
                                                            <label for="task_title">Kontakt</label> 
                                                                    @php
                                                                        $preselectedContact = null;

                                                                        if (!empty($data->contact_id) && !empty($data->contact_type)) {
                                                                            $preselectedContact = DB::table('new_leads')
                                                                                ->where('id', $data->contact_id)
                                                                                ->select(
                                                                                    'id as main_id', 'name', 'lastname', 'phone', 'email',
                                                                                    'street', 'city', 'postcode', 'longitude', 'latitude',
                                                                                    DB::raw('"customer" as type'),
                                                                                    DB::raw("CONCAT(street, ', ', postcode, ' ', city) as full_address")
                                                                                )
                                                                                ->first();

                                                                            if (!$preselectedContact) {
                                                                                $preselectedContact = DB::table('brands')
                                                                                    ->join('brand_departments', 'brand_departments.id', '=', 'brands.id')
                                                                                    ->where('brands.id', $data->contact_id)
                                                                                    ->select(
                                                                                        'brands.id as main_id', 'brand_departments.name',
                                                                                        'brand_departments.phone', 'brand_departments.email',
                                                                                        'brands.name as lastname', 'brands.type',
                                                                                        DB::raw('NULL as street'), DB::raw('NULL as city'),
                                                                                        DB::raw('NULL as postcode'), DB::raw('NULL as longitude'),
                                                                                        DB::raw('NULL as latitude'),
                                                                                        DB::raw("CONCAT(brand_departments.name, ' - ', brands.name) as full_address")
                                                                                    )
                                                                                    ->first();
                                                                            }

                                                                            if (!$preselectedContact) {
                                                                                $preselectedContact = DB::table('distributors')
                                                                                    ->join('distributor_departments', 'distributor_departments.id', '=', 'distributors.id')
                                                                                    ->where('distributors.id', $data->contact_id)
                                                                                    ->select(
                                                                                        'distributors.id as main_id', 'distributor_departments.name',
                                                                                        'distributor_departments.phone', 'distributor_departments.email',
                                                                                        'distributors.name as lastname',
                                                                                        DB::raw('"distributor" as type'),
                                                                                        DB::raw('NULL as street'), DB::raw('NULL as city'),
                                                                                        DB::raw('NULL as postcode'), DB::raw('NULL as longitude'),
                                                                                        DB::raw('NULL as latitude'),
                                                                                        DB::raw("CONCAT(distributor_departments.name, ' - ', distributors.name) as full_address")
                                                                                    )
                                                                                    ->first();
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    <select name="contact_id" class="contact_list form-control" style="width:100%">
                                                                        @if($preselectedContact)
                                                                            <option value="{{ $preselectedContact->main_id }}" selected>
                                                                                {{ $preselectedContact->name }} {{ $preselectedContact->lastname }} - {{ $preselectedContact->type }}
                                                                            </option>
                                                                        @else
                                                                            <option value="">Bitte Kontakt auswählen</option>
                                                                        @endif
                                                                    </select>

                                                                    <input type="hidden" name="contact_type" id="contact_type" value="{{ $data->contact_type ?? '' }}">

                                                        </div> 

                                                         <div class="col-md-6"  style="display:none;" id="link_section" >
                                                                <span>Link *</span>
                                                                <input type="text" class="form-control" value="{{ $data->link }}" id="link" name="link" >
                                                        </div> 
                                                    
                                                        <div class="col-md-6" id="intern" style="display: none;">
                                                            <label for="task_title">Adress *</label>
                                                            <select name="branch_address_id" class="form-control" >
                                                                <option ></option>
                                                                @foreach ($branch_addresses as $address)
                                                                    <option value="{{ $address->id }}" 
                                                                        data-street="{{ $address->street }}"
                                                                        data-latitude="{{ $address->latitude }}"
                                                                        data-longitude="{{ $address->longitude }}"
                                                                        data-city="{{ $address->city }}"
                                                                        data-postcode="{{ $address->postcode }}"
                                                                        @if($data->branch_address_id == $address->id) selected @endif
                                                                    >{{ $address->branch_initial }} - {{ $address->name }}</option>
                                                                @endforeach
                                                            </select> 
                                                        </div>

                                                        <div class="col-md-6" id="extern">
                                                            <label for="task_title">Adress *</label> 
                                                            <input id="full_address" type="text" class="form-control form-element full_address"  
                                                                placeholder="Adresse eingeben" 
                                                                name="full_address" 
                                                                value="{{$data->full_address}}"> 

                                                            <input type="hidden" id="street-input" name="street" value="{{$data->street}}">
                                                            <input type="hidden" id="city-input" name="city" value="{{$data->city}}">
                                                            <input type="hidden" id="latitude-input" name="latitude" value="{{$data->latitude}}">
                                                            <input type="hidden" id="longitude-input" name="longitude" value="{{$data->longitude}}">
                                                            <input type="hidden" id="postal_code-input" name="postcode" value="{{$data->postcode}}">
                                                        </div>

                                                         <div class="col-md-6"  >
                                                            <label for="task_title">Telefon</label>
                                                                <input type="text" class="form-control phone" value="{{ $data->phone }}"   name="phone"  >
                                                        </div> 

                                                        <div class="col-md-6"  >
                                                                <label for="task_title">Email <small>Optional</small></label>
                                                                <input type="email" class="form-control email" value="{{ $data->email }}"  name="email"  >
                                                        </div> 
 
                                                    <div class="col-md-6 col-12">
                                                        <label for="task_title">Zweck</label> 
                                                            <input type="text" class="form-control" value="{{ old('appointment_type', $data->appointment_type) }}" id="appointment_type" name="appointment_type" >
                                                    </div>

                                                      <div class="col-md-6 col-12">
                                                        <label for="task_title">Ort des Termin *</label>
                                                        <select name="execution_type" id="execution_type" class="form-control"> 
                                                                <option value="internal" @if($data->execution_type == 'internal') selected @endif>Intern</option>
                                                                <option value="external" @if($data->execution_type == 'external') selected @endif>Extern</option>
                                                                <option value="online" @if($data->execution_type == 'online') selected @endif>Online</option>
                                                                <option value="telephone" @if($data->execution_type == 'telephone') selected @endif>Telefon</option>
                                                        </select>
                                                    </div> 
                                                    <div class="col-md-12 col-12 mb-1"> 
                                                        <label for="task_title">Beschreibung</label> 

                                                        <textarea name="description" class="form-control" rows="1">
                                                            {{$data->note}}
                                                        </textarea>
                                                    </div>

                                                

                                                    
                                                    <div class="col-md-4"  >
                                                        <label for="task_title">Betrieb</label>
                                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($branches as $br)
                                                                <option value="{{ $br->id}}" @if($br->id == $data->branch_id) selected @endif>{{$br->branch}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>   

                                                    <div class="col-12 line"></div>
                     
                                                         <div class="col-md-6 col-12 ">
                                                            <label for="month">Erinnerung</label>
                                                             <input type="date" name="reminder_date" class="form-control" value="{{$data->reminder_date}}">
                                                        </div> 

                                                        <div class="col-md-6 col-12 ">
                                                            <label for="month">Erinnerung</label>
                                                            <input type="time" name="reminder_time" class="form-control"  value="{{$data->reminder_time}}">
                                                        </div> 

                                                        <div class="col-md-6 col-12 ">
                                                            <label for="priority">Priorität</label>
                                                            <select name="priority" class="form-control" id="priority"> 
                                                                <option value="normal" data-icon="fa fa-battery-empty" @if($data->priority == 'normal') selected @endif>Keiner</option>
                                                                <option value="medium" data-icon="fa fa-battery-half" @if($data->priority == 'medium') selected @endif>Medium</option>
                                                                <option value="high" data-icon="fa fa-battery-full" @if($data->priority == 'high') selected @endif>Hoch</option>
                                                                <option value="very high" data-icon="fa fa-fire warning" @if($data->priority == 'very high') selected @endif>Sehr Wichtig</option>
                                                                
                                                            </select>
                                                        </div>  

                                                         <div class="col-md-6 col-12  ">
                                                            <label for="date_type">Wiederholung</label>
                                                            <select name="date_type" id="date_type" class="form-control"   style="width:100%">  
                                                                <option >Wählen</option>
                                                                <option value="day" @if($data->date_type == 'day') selected @endif >Ganzer Tag</option>
                                                                <option value="week" @if($data->date_type == 'week') selected @endif>7 Tage (Eine Woche)</option>
                                                                <option value="daily" @if($data->date_type == 'daily') selected @endif>Täglich</option>
                                                                <option value="monthly"@if($data->date_type == 'monthly') selected @endif >Monatlich</option>
                                                            </select>
                                                        </div> 

                                                        <div class="col-md-6 col-12 from_day ">
                                                            <label for="end_time">Von</label>
                                                            <select name="from_day" id="from_day" class="form-control" style="width:100%">  
                                                                <option value="monday"  @if($data->from_day == 'monday') selected @endif >Montag</option> 
                                                                <option value="tuesday" @if($data->from_day == 'tuesday') selected @endif >Dienstag</option> 
                                                                <option value="wednesday" @if($data->from_day == 'wednesday') selected @endif >Mittwoch</option> 
                                                                <option value="thursday" @if($data->from_day == 'thursday') selected @endif >Donnerstag</option> 
                                                                <option value="friday" @if($data->from_day == 'friday') selected @endif >Freitag</option> 
                                                                <option value="saturday" @if($data->from_day == 'saturday') selected @endif >Samstag</option> 
                                                                <option value="sunday" @if($data->from_day == 'sunday') selected @endif >Sonntag</option> 
                                                            </select>
                                                        </div>


                                                        <div class="col-md-6 col-12 to_day ">
                                                            <label for="end_time">Zu</label>
                                                            <select name="to_day" id="to_day" class="form-control" style="width:100%">  
                                                                 <option value="monday"  @if($data->to_day == 'monday') selected @endif >Montag</option> 
                                                                <option value="tuesday" @if($data->to_day == 'tuesday') selected @endif >Dienstag</option> 
                                                                <option value="wednesday" @if($data->to_day == 'wednesday') selected @endif >Mittwoch</option> 
                                                                <option value="thursday" @if($data->to_day == 'thursday') selected @endif >Donnerstag</option> 
                                                                <option value="friday" @if($data->to_day == 'friday') selected @endif >Freitag</option> 
                                                                <option value="saturday" @if($data->to_day == 'saturday') selected @endif >Samstag</option> 
                                                                <option value="sunday" @if($data->to_day == 'sunday') selected @endif >Sonntag</option> 
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 col-12 from_month ">
                                                            <label for="month">Von (Monat)</label>
                                                            <select name="from_month" id="from_month" class="form-control" style="width:100%">  
                                                                <option value="january" @if($data->from_month == 'january') selected @endif>Januar</option> 
                                                                <option value="february" @if($data->from_month == 'february') selected @endif>Februar</option> 
                                                                <option value="march" @if($data->from_month == 'march') selected @endif>März</option> 
                                                                <option value="april" @if($data->from_month == 'april') selected @endif>April</option> 
                                                                <option value="may" @if($data->from_month == 'may') selected @endif>Mai</option> 
                                                                <option value="june" @if($data->from_month == 'june') selected @endif>Juni</option> 
                                                                <option value="july" @if($data->from_month == 'july') selected @endif>Juli</option> 
                                                                <option value="august" @if($data->from_month == 'august') selected @endif>August</option> 
                                                                <option value="september" @if($data->from_month == 'september') selected @endif>September</option> 
                                                                <option value="october" @if($data->from_month == 'october') selected @endif>Oktober</option> 
                                                                <option value="november" @if($data->from_month == 'november') selected @endif>November</option> 
                                                                <option value="december" @if($data->from_month == 'december') selected @endif>Dezember</option> 
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 col-12 to_month ">
                                                            <label for="month">Zu (Monat)</label>
                                                            <select name="to_month" id="to_month" class="form-control" style="width:100%">  
                                                                <option value="january" @if($data->to_month == 'january') selected @endif>Januar</option> 
                                                                <option value="february" @if($data->to_month == 'february') selected @endif>Februar</option> 
                                                                <option value="march" @if($data->to_month == 'march') selected @endif>März</option> 
                                                                <option value="april" @if($data->to_month == 'april') selected @endif>April</option> 
                                                                <option value="may" @if($data->to_month == 'may') selected @endif>Mai</option> 
                                                                <option value="june" @if($data->to_month == 'june') selected @endif>Juni</option> 
                                                                <option value="july" @if($data->to_month == 'july') selected @endif>Juli</option> 
                                                                <option value="august" @if($data->to_month == 'august') selected @endif>August</option> 
                                                                <option value="september" @if($data->to_month == 'september') selected @endif>September</option> 
                                                                <option value="october" @if($data->to_month == 'october') selected @endif>Oktober</option> 
                                                                <option value="november" @if($data->to_month == 'november') selected @endif>November</option> 
                                                                <option value="december" @if($data->to_month == 'december') selected @endif>Dezember</option> 
                                                            </select>
                                                        </div> 
                                                </div>   
                                            </div>
                                        </div>
                                    </div>

                                <div class="modal-footer" style="border:0; background:#f1f1f1 !important;"> 

                                    <button type="button" 
                                            class="btn btn-danger mr-1 waves-effect waves-light btn-sm close_task_window" 
                                            id="cancelButton">
                                        <i class="feather icon-x"></i> abbrechen
                                    </button>                                    
                                    <button type="button" class="btn btn-primary save-task btn-sm"><i class="feather icon-save"></i> speichern</button> 
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
 

<script src="{{ asset('js/select2.min.js') }}"></script>


<!-- script for hidding the day and month drop down:  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateType = document.getElementById("date_type");
        const fromDay = document.querySelector(".from_day");
        const toDay = document.querySelector(".to_day");
        const fromMonth = document.querySelector(".from_month");
        const toMonth = document.querySelector(".to_month");

        function toggleFields() {
            const selectedValue = dateType.value;

            // Hide all fields by default
            fromDay.style.display = "none";
            toDay.style.display = "none";
            fromMonth.style.display = "none";
            toMonth.style.display = "none";

            // Show fields based on selection
            if (selectedValue === "daily") {
                fromDay.style.display = "block";
                toDay.style.display = "block";
            } else if (selectedValue === "monthly") {
                fromMonth.style.display = "block";
                toMonth.style.display = "block";
            }
        }

        // Run function on change and on load
        dateType.addEventListener("change", toggleFields);
        toggleFields();
    });
</script>
<script>
    $(document).ready(function(){
        $('.selectables').select2({
            tags: true, 
            placeholder: "Wählen",
            allowClear: true
        });
    });
</script>

<script>
    $(document).ready(function() {
    $('.employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
});

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

</script> 

 
</script> 
<!-- Color: start  -->
 <script>
    $(document).ready(function() {
    $('#color-select').select2({
        templateResult: formatColor,
        templateSelection: formatColor,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    function formatColor(color) {
        if (!color.id) {
            return color.text;
        }

        var colorValue = $(color.element).data('color');
        var colorName = color.text;

        var markup = `
            <div style="display: flex; align-items: center;">
                <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                <span>${colorName}</span>
            </div>
        `;

        return markup;
    }
});

 </script> 

 <!-- task step and manuals script: end -->

<!-- Dynamic Handling for Repeat Field -->
    <script>
        $(document).ready(function () {
        // Toggle the visibility of the repeat area based on the checkbox
        $('#repeated').on('change', function () {
            if ($(this).is(':checked')) {
                $('.repeated_area').slideDown();
            } else {
                $('.repeated_area').slideUp();
                $('#wiederholung').val(''); // Clear the dropdown if unchecked
            }
        });

        // Show the repeat area if already checked on page load
        if ($('#repeated').is(':checked')) {
            $('.repeated_area').show();
        } else {
            $('.repeated_area').hide();
        }

        // Handle priority dropdown
        $('#priority_select .dropdown-item').on('click', function () {
            const priorityValue = $(this).data('value');
            const priorityIcon = $(this).find('i').attr('class');
            $('input[name="priority"]').val(priorityValue);

            $('#priority_select button i').attr('class', priorityIcon); // Update icon in the dropdown button
        });

        // Toggle the visibility of the reminder area based on the checkbox
        $('#reminder_check').on('change', function () {
            if ($(this).is(':checked')) {
                $('.reminder_area').slideDown();
            } else {
                $('.reminder_area').slideUp();
                $('input[name="reminder_date"]').val('');
                $('input[name="reminder_time"]').val('');
            }
        });

        // Show the reminder area if already checked on page load
        if ($('#reminder_check').is(':checked')) {
            $('.reminder_area').show();
        } else {
            $('.reminder_area').hide();
        }
    });

 </script>



<!-- Color: start  -->
 <script>
    $(document).ready(function() {
    $('#color-select').select2({
        templateResult: formatColor,
        templateSelection: formatColor,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    function formatColor(color) {
        if (!color.id) {
            return color.text;
        }

        var colorValue = $(color.element).data('color');
        var colorName = color.text;

        var markup = `
            <div style="display: flex; align-items: center;">
                <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                <span>${colorName}</span>
            </div>
        `;

        return markup;
    }
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
 <!-- save operation start -->
   <script>
$(document).ready(function () {
    initSelect2();

    function initSelect2() {
        $('.employee').select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployee,
            escapeMarkup: markup => markup,
        });
    }

    function formatEmployee(employee) {
        if (!employee.id) return employee.text;
        const imageUrl = $(employee.element).data('image');
        return `
            <div style="display: flex; align-items: center;">
                <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
                <span>${employee.text}</span>
            </div>
        `;
    }

    $('.save-task').on('click', function (e) {
        e.preventDefault();

        const form = document.getElementById('task-store-form');
        const formData = new FormData(form);

        // Handle checkboxes manually
        formData.set('is_contact', $('#customSwitchContact').is(':checked') ? 'on' : 'off');
        formData.set('public', $('#customSwitchPublic').is(':checked') ? 'on' : 'off');

        // Add validation if needed (same as before)

        // Submit using AJAX
        $.ajax({
            url: '{{ route("appointment.update") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
                $('.save-task').prop('disabled', true).text('speichern...');
            },
            success: function (response) {
                $('.save-task').prop('disabled', false).text('speichern');

                Swal.fire({
                    icon: 'success',
                    title: 'Erfolg',
                    text: 'Aufgabe erfolgreich gespeichert!',
                }).then(() => {
                    const taskId = response.task_id;
                    const calendarParam = $('#calendarInput').val();
                    const redirectUrl = calendarParam === "true"
                        ? `/tasks/calendar/personal?view=week&task_id=${taskId}`
                        : `/appointments?task_id=${taskId}&_=${new Date().getTime()}`;
                    window.location.href = redirectUrl;
                });
            },
            error: function (xhr) {
                $('.save-task').prop('disabled', false).text('speichern');
                const errors = xhr.responseJSON.errors || {};
                let html = '<ul>';
                for (const key in errors) {
                    html += `<li>${errors[key]}</li>`;
                }
                html += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    html: html,
                });
            }
        });
    });
});
</script>

<!-- save operation end  -->

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

             
              // Attach change event to employee select: start 
                 const checkedEmployees = new Set(); // Track checked employees 
                // Function to handle employee selection
                $(document).on("change", ".employee", function (e) {
                    const currentSelect = $(this);
                    const selectedEmployees = currentSelect.val(); // Get all selected employee IDs
                    const newlySelected = selectedEmployees.filter(id => !checkedEmployees.has(id)); // Find newly selected employees

                    // Process each newly selected employee
                    newlySelected.forEach(employeeId => {
                        const startDate = $('#start_date').val();
                        const endDate = $('#end_date').val();

                        // Ensure start_date and end_date are selected
                        if (!startDate || !endDate) {
                            Swal.fire({
                                icon: "warning",
                                title: "Fehlende Daten",
                                text: "Bitte wählen Sie sowohl das Startdatum als auch das Enddatum aus, bevor Sie einen Mitarbeiter zuweisen.",
                                showCancelButton: true,
                                showDenyButton: true,
                                confirmButtonText: "Nicht prüfen",
                                denyButtonText: "Abbrechen",
                                cancelButtonText: "Schließen",
                            }).then((result) => {
                                if (result.isDenied) {
                                    // Remove only the current employee if canceled
                                    currentSelect
                                        .val(selectedEmployees.filter(id => id !== employeeId))
                                        .trigger("change");
                                }
                                // If "Nicht prüfen" is pressed, do nothing (keep the selection)
                            });
                            return;
                        }

                        // Call backend to check conflicts for the newly selected employee
                        checkForConflicts(employeeId, startDate, endDate, (hasConflict, conflictData) => {
                            if (hasConflict) {
                                // Show conflict details
                                displayConflictAlert(conflictData, () => {
                                    // If "Abbrechen" is clicked, remove only the conflicting employee
                                    currentSelect
                                        .val(selectedEmployees.filter(id => id !== employeeId))
                                        .trigger("change");
                                }, () => {
                                    // If "Trotzdem auswählen" is clicked, add the employee to the checked set
                                    checkedEmployees.add(employeeId);
                                });
                            } else {
                                // If no conflict, add the employee to the checked set
                                checkedEmployees.add(employeeId);
                            }
                        });
                    });

                    // Remove unchecked employees from the Set
                    checkedEmployees.forEach((id) => {
                        if (!selectedEmployees.includes(id)) {
                            checkedEmployees.delete(id);
                        }
                    });
                });

                // Function to check for conflicts
                function checkForConflicts(employeeId, startDate, endDate, callback) {
                    $.get(`/search_duplicate_task/${employeeId}/${startDate}/${endDate}`, function (data) {
                        if (data.length > 0) {
                            callback(true, data); // Conflicts found
                        } else {
                            callback(false, []); // No conflicts
                        }
                    }).fail(function () {
                        Swal.fire({
                            icon: "error",
                            title: "Fehler",
                            text: "Beim Überprüfen der Mitarbeiterverfügbarkeit ist ein Fehler aufgetreten.",
                        });
                    });
                }

                // Function to display conflict alert
                function displayConflictAlert(conflictData, onCancel, onContinue) {
                    let conflictTable = `
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Typ</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Benutzer</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Start</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Ende</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    conflictData.forEach((task) => {
                        conflictTable += `
                            <tr>
                                 <td style="border: 1px solid #ddd; padding: 8px;">${task.type}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${task.name} ${task.lastname}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${task.start_date}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${task.end_date}</td>
                            </tr>
                        `;
                    });

                    conflictTable += `
                            </tbody>
                        </table>
                    `;

                    Swal.fire({
                        icon: "error",
                        title: "Konflikte erkannt",
                        html: conflictTable,
                        showCancelButton: true,
                        confirmButtonText: "Trotzdem auswählen",
                        cancelButtonText: "Abbrechen",
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            onCancel();
                        } else {
                            onContinue();
                        }
                    });
                }

              // Attach change event to employee select: end 

            // save operation end: 
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const appointmentTypeDropdown = document.getElementById("execution_type");
    const internDiv = document.getElementById("intern");
    const externDiv = document.getElementById("extern");
    const linkDiv = document.getElementById("link_section");
    const branchSelect = document.querySelector("[name='branch_address_id']");
    const externInput = document.getElementById("full_address");

    function toggleSections() {
        const appointmentType = appointmentTypeDropdown.value;

        internDiv.style.display = "none";
        externDiv.style.display = "none";
        linkDiv.style.display = "none";

        resetHiddenInputs();

        if (appointmentType === "internal") {
            internDiv.style.display = "block";
            branchSelect.value = "";
        } 
        else if (appointmentType === "external") {
            externDiv.style.display = "block";
        } 
        else if (appointmentType === "online") {
            linkDiv.style.display = "block";
        }
        else if (appointmentType === "telephone") {
            // Do nothing for telephone appointments
        }
        else {
            externDiv.style.display = "block"; // Default to external
        }
    }

    function populateInternalAddress() {
        const selectedOption = branchSelect.options[branchSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            resetHiddenInputs();
            return;
        }

        document.getElementById("full_address").value = selectedOption.innerText;
        document.getElementById("street-input").value = selectedOption.getAttribute("data-street") || "";
        document.getElementById("city-input").value = selectedOption.getAttribute("data-city") || "";
        document.getElementById("postal_code-input").value = selectedOption.getAttribute("data-postcode") || "";
        document.getElementById("latitude-input").value = selectedOption.getAttribute("data-latitude") || "";
        document.getElementById("longitude-input").value = selectedOption.getAttribute("data-longitude") || "";
    }

    function resetHiddenInputs() {
        // document.getElementById("full_address").value = "";
        document.getElementById("street-input").value = "";
        document.getElementById("city-input").value = "";
        document.getElementById("postal_code-input").value = "";
        document.getElementById("latitude-input").value = "";
        document.getElementById("longitude-input").value = "";
    }

    // Ensure initializeAutocomplete is globally accessible
    window.initializeAutocomplete = function() {
        if (!externInput) return;

        const autocomplete = new google.maps.places.Autocomplete(externInput, {
            types: ['geocode'],
            componentRestrictions: { country: 'DE' }
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.error("No details available for input: '" + place.name + "'");
                return;
            }

            let street = "", city = "", postalCode = "", latitude = "", longitude = "";

            place.address_components.forEach(component => {
                const types = component.types;

                if (types.includes("route")) {
                    street = component.long_name;
                }
                if (types.includes("locality") || types.includes("sublocality")) {
                    city = component.long_name;
                }
                if (types.includes("postal_code")) {
                    postalCode = component.long_name;
                }
            });

            latitude = place.geometry.location.lat();
            longitude = place.geometry.location.lng();

            // Populate inputs with external address data
            document.getElementById("street-input").value = street;
            document.getElementById("city-input").value = city;
            document.getElementById("postal_code-input").value = postalCode;
            document.getElementById("latitude-input").value = latitude;
            document.getElementById("longitude-input").value = longitude;
        });
    };

    function loadGoogleMapsAPI() {
        if (!window.google || !window.google.maps) {
            const script = document.createElement("script");
            script.src = "https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places";
            script.async = true;
            script.defer = true;
            script.onload = function() {
                initializeAutocomplete();
            };
            document.head.appendChild(script);
        } else {
            initializeAutocomplete();
        }
    }

    appointmentTypeDropdown.addEventListener("change", toggleSections);
    branchSelect.addEventListener("change", populateInternalAddress);

    toggleSections();
    loadGoogleMapsAPI();
});
</script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const startDateInput = document.getElementById("start_date");
    const startTimeInput = document.getElementById("start_time");
    const endTimeInput = document.getElementById("end_time");
    const totalTimeInput = document.getElementById("total_time");
    const endDateInput = document.getElementById("end_date");
    const dateTypeInput = $("#date_type"); // Select2 uses jQuery selector

    // Function to set default working hours when selecting "Whole Day"
    function setWholeDayTime() {
        if (dateTypeInput.val() === "day") {
            startTimeInput.value = "08:00";
            endTimeInput.value = "16:00";
            totalTimeInput.value = 8; // 8 hours total
        }
    }

    // Function to set total_time to 8 hours when start_date is selected
    function setDefaultTotalTime() {
        if (startDateInput.value) {
            totalTimeInput.value = 8; // Default 8 hours
            endDateInput.value = startDateInput.value; // Set end_date same as start_date
        }
    }

    // Function to calculate time difference in hours
    function calculateTotalTime() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!startTime || !endTime) return;

        // Convert time to Date objects for calculation
        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        // Ensure end time is after start time
        if (end < start) {
            showAlert("Fehler", "Endzeit muss nach der Startzeit liegen.", "error");
            endTimeInput.value = ""; // Reset end time
            return;
        }

        // Calculate difference in hours
        const diffInMs = end - start;
        const diffInHours = diffInMs / (1000 * 60 * 60); // Convert milliseconds to hours

        totalTimeInput.value = diffInHours.toFixed(2); // Display in hours

        // Validate if time is within working hours (06:00 - 19:00)
        const startHour = start.getHours();
        const endHour = end.getHours();

        if (startHour < 6 || startHour >= 19 || endHour < 6 || endHour >= 19) {
            showAlert(
                "Achtung!",
                "Ihre gewählte Zeit liegt außerhalb der Arbeitszeit (06:00 - 19:00 Uhr).",
                "warning"
            );
        }
    }

    // Function to show SweetAlert2 alerts
    function showAlert(title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            confirmButtonText: "OK"
        });
    }

    // Event Listeners
    startDateInput.addEventListener("change", setDefaultTotalTime);
    startTimeInput.addEventListener("change", calculateTotalTime);
    endTimeInput.addEventListener("change", calculateTotalTime);

    // Select2 event listener for `date_type`
    dateTypeInput.on("change.select2", function () {
        setWholeDayTime();
        calculateTotalTime(); // Ensure total time updates when the type changes
    });

    // Initialize Select2
    $(document).ready(function () {
        $("#date_type").select2(); // Ensure Select2 is initialized
    });

    // Initialize values on page load
    setDefaultTotalTime();
});
</script>

 

<!-- Getting the contact list in drop down: start  -->
  <script>
    $(document).ready(function () {
    $('.contact_list').select2({
        placeholder: "Wählen",
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: "{{ route('get.contact.list') }}",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || ''
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.main_id,
                            text: item.name + " " + item.lastname + " - " + item.type,
                            type: item.type,
                            phone: item.phone || "",
                            email: item.email || "",
                            street: item.street || "",
                            postcode: item.postcode || "",
                            city: item.city || "",
                            longitude: item.longitude || "",
                            latitude: item.latitude || "",
                            full_address: (item.street && item.city && item.postcode) 
                                ? item.street + ", " + item.postcode + " " + item.city 
                                : item.name + " " + item.lastname
                        };
                    })
                };
            },
            cache: true
        }
    });

    // ✅ Auto-fill fields on select
    $('.contact_list').on('select2:select', function (e) {
        var selectedData = e.params.data;

        $('#contact_type').val(selectedData.type);
        $('.phone').val(selectedData.phone);
        $('.email').val(selectedData.email);
        $('#full_address').val(selectedData.full_address); // 👈 FIXED HERE
        $('#street-input').val(selectedData.street);
        $('#city-input').val(selectedData.city);
        $('#postal_code-input').val(selectedData.postcode);
        $('#latitude-input').val(selectedData.latitude);
        $('#longitude-input').val(selectedData.longitude);
    });

    // ✅ Clear fields when dropdown is cleared
    $('.contact_list').on('select2:clear', function () {
        $('#contact_type, .phone, .email, #full_address, #street-input, #city-input, #postal_code-input, #latitude-input, #longitude-input').val('');
    });
});

  </script>




<!-- Getting the contact list in drop down: end  -->

<!-- Calendar Input URL:start  -->
 
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the referrer URL (previous page)
        let referrer = document.referrer;
        
        // Check if the referrer URL ends with "tasks/calendar/personal"
        if (referrer.includes("/tasks/calendar/personal")) {
            document.getElementById("calendarInput").value = "true";
        } else {
            document.getElementById("calendarInput").value = "false";
        }
    });
</script>
<!-- Calendar Input URL:end  -->

<script>
    document.getElementById('cancelButton').addEventListener('click', function () {
        const referrer = document.referrer;

        if (referrer.includes('tasks/calendar/personal')) {
            window.location.href = '/tasks/calendar/personal';
        } else if (referrer.includes('appointments')) {
            window.location.href = '/appointments';
        } else {
            // Default fallback
            window.history.back();
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const contactSwitch = document.getElementById('customSwitchContact');
    const preTypeBox = document.getElementById('preTypeBox');

    function togglePreType() {
        preTypeBox.style.display = contactSwitch.checked ? 'block' : 'none';
    }

    contactSwitch.addEventListener('change', togglePreType);
    togglePreType(); // run on page load
});
</script>



@endsection