@extends('admin.layouts.app')
@section('title')
BEARBEITEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('content')
    <div class="app-content"> 
        <div class="content-wrapper"> 
            <div class="content-body">  
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                        @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger mt-1">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success mt-1">
                                    {{ session('success') }}
                                </div>
                            @endif


                             <form method="post" action="{{ route('personal.task.update')}}" id="save-form">
                                @csrf
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <div class="new_task"  >                                                                                         
                                    <div class="card new_task_card">
                                        <div class="card-header" style="    border-bottom: 1px solid #8fc73e;">
                                            <h3 class="title mt-1 ml-2">Aufgaben Bearbeiten</h3>
                                        </div>
                                        <div class="card-body p-0">
                                           
                                                <div class="modal-body p-0">
                                                    <div class="card p-1">
                                                        <div class="form-body">
                                                            <div class="row">  

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
                                                                                                            <i class="feather icon-settings"></i> Einstellungen
                                                                                                </div>
                                                                                            </span>
                                                                                        </div>
                                                                                        <div id="accordion1" role="tabpanel" data-parent="#accordionWrapa1" aria-labelledby="heading1" class="collapse" style="">
                                                                                            <div class="card-content">
                                                                                                <div class="card-body">
                                                                                                    <table class="table"> 
                                                                                                      <tr style="background: #f7f7f7a8; border-bottom: 6px solid white;">
                                                                                                        <td>
                                                                                                            <i class="feather icon-refresh-cw"></i> Wiederholung
                                                                                                        </td>
                                                                                                        <td style="text-align:right">
                                                                                                            <div class="checkbox">
                                                                                                                <div class="custom-control custom-switch mr-2 mb-1">
                                                                                                                    <input type="checkbox" class="custom-control-input" id="repeated" name="repeated" @if($data->repeat) checked @endif>
                                                                                                                    <label class="custom-control-label" for="repeated"></label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr style="background: #f7f7f7a8; border-bottom: 6px solid white;" class="repeated_area">
                                                                                                        <td style="text-align:right" colspan="2">
                                                                                                            <select name="repeat" class="form-control" id="wiederholung">
                                                                                                                <option value="">Häufigkeit auswählen</option>
                                                                                                                <option value="minute" @if($data->repeat == 'minute') selected @endif>Minütlich</option>
                                                                                                                <option value="hourly" @if($data->repeat == 'hourly') selected @endif>Stündlich</option>
                                                                                                                <option value="daily" @if($data->repeat == 'daily') selected @endif>Täglich</option>
                                                                                                                <option value="weekly" @if($data->repeat == 'weekly') selected @endif>Wöchentlich</option>
                                                                                                                <option value="monthly" @if($data->repeat == 'monthly') selected @endif>Monatlich</option>
                                                                                                                <option value="quarterly" @if($data->repeat == 'quarterly') selected @endif>Vierteljährlich</option>
                                                                                                                <option value="yearly" @if($data->repeat == 'yearly') selected @endif>Jährlich</option>
                                                                                                            </select>
                                                                                                        </td>
                                                                                                    </tr>


                                                                                                    <tr style="background: #f7f7f7a8; border-bottom: 6px solid white;">
                                                                                                        <td>
                                                                                                            <i class="fa fa-clock-o"></i> Erinnerung
                                                                                                        </td>
                                                                                                        <td style="text-align:right">
                                                                                                            <div class="checkbox">
                                                                                                                <div class="custom-control custom-switch mr-2 mb-1">
                                                                                                                    <input type="checkbox" class="custom-control-input" id="reminder_check" name="reminder_check"  @if($data->reminder_date) checked @endif>
                                                                                                                    <label class="custom-control-label" for="reminder_check"></label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                    <tr style="background: #f7f7f7a8; border-bottom: 6px solid white;" class="reminder_area">
                                                                                                        <td style="text-align:right" colspan="2">
                                                                                                            <label for="reminder_date" style="float:left;">Datum:</label>
                                                                                                            <input type="date" name="reminder_date" class="form-control" value="{{$data->reminder_date}}">
                                                                                                            <label for="reminder_time" style="float:left;" class="mt-1">Zeit:</label>
                                                                                                            <input type="time" name="reminder_time" class="form-control" value="{{$data->reminder_time}}">
                                                                                                        </td>
                                                                                                    </tr>


                                                                                                        <tr style="background: #f7f7f7a8; border-bottom: 6px solid white;">
                                                                                                            <td><i class="feather icon-flag"></i> Priorität</td>
                                                                                                            <td style="text-align:right">
                                                                                                                <input type="hidden" name="priority" id="priority_value" value="{{ $data->priority }}">
                                                                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1" id="priority_select">
                                                                                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                                                                        <i id="priority_icon" class="
                                                                                                                            {{ $data->priority === 'medium' ? 'fa fa-battery-half' : ($data->priority === 'high' ? 'fa fa-battery-full' : ($data->priority === 'very high' ? 'fa fa-fire warning' : 'fa fa-battery-empty')) }}">
                                                                                                                        </i>
                                                                                                                    </button>
                                                                                                                    <div class="dropdown-menu" x-placement="top-start">
                                                                                                                        <span class="dropdown-item" data-value="normal">
                                                                                                                            <i class="fa fa-battery-empty"></i> Keiner
                                                                                                                        </span>
                                                                                                                        <span class="dropdown-item" data-value="medium">
                                                                                                                            <i class="fa fa-battery-half"></i> Medium
                                                                                                                        </span>
                                                                                                                        <span class="dropdown-item" data-value="high">
                                                                                                                            <i class="fa fa-battery-full"></i> Hoch
                                                                                                                        </span>
                                                                                                                        <span class="dropdown-item" data-value="very high">
                                                                                                                            <i class="fa fa-fire warning"></i> Sehr Wichtig
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
                                                                <div class="col-md-8 col-12">
                                                                    <label for="task_title">Aufgabentitel</label>
                                                                    <input type="text" id="task_title" class="form-control" name="task_title" value="{{$data->task_title}}">
                                                                </div>

                                                                <div class="col-md-4 ">
                                                                  <div class="d-flex">
                                                                    <input type="hidden" name="color" id="color" value="{{$data->color}}">
                                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1 mt-2" id="color_drop_down">
                                                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
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
                                                                                <i class="fa fa-square" style="color: #1f2937;"></i> Schwarz
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
                                                                    <div class="custom-control custom-switch mr-2 mb-1">
                                                                        <p class="mb-0">Öffentlich</p>
                                                                        
                                                                        <!-- Hidden input to always send a value -->
                                                                        <input type="hidden" name="public" value="off">

                                                                        <input type="checkbox" class="custom-control-input" id="customSwitch10" name="public" value="on" 
                                                                            {{ isset($data) && $data->public == 'on' ? 'checked' : '' }}>
                                                                        
                                                                        <label class="custom-control-label" for="customSwitch10">
                                                                            <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                                                            <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                                                        </label>
                                                                    </div> 
                                                                    <div class="custom-control custom-switch mr-2 mb-1">
                                                                        <p class="mb-0">Kunde</p>
                                                                        <input type="checkbox" class="custom-control-input" id="customerSwitch" name="is_customer" value="{{ $data->is_customer }}" @if($data->is_customer == 1) checked @endif >
                                                                        <label class="custom-control-label" for="customerSwitch">
                                                                            <span class="switch-icon-left"><i class="feather icon-user-check"></i></span>
                                                                            <span class="switch-icon-right"><i class="feather icon-user-x"></i></span>
                                                                        </label>
                                                                    </div> 
                                                                  </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div id="customerSelectContainer" style="display: none;">
                                                                        <label for="customerLeadProductSelect">Wähle Kunde & Objekt</label>
                                                                        <select class="form-control" id="customerLeadProductSelect" name="customer_id" style="width: 100%;">
                                                                            @if(!empty($data->customer_id))
                                                                                @php
                                                                                    $customer = DB::table('new_leads')->where('id', $data->customer_id)->first();
                                                                                @endphp
                                                                                @if($customer)
                                                                                    <option value="{{ $customer->id }}" selected>{{ $customer->name }} {{ $customer->lastname }}</option>
                                                                                @endif
                                                                            @endif
                                                                        </select>

                                                                        <input type="hidden" name="alternative_id" id="select_alternative_id" value="{{ $data->alternative_id ?? '' }}">
                                                                        <input type="hidden" name="product_id" id="select_product_id" value="{{ $data->product_id ?? '' }}">
                                                                    </div>
                                                                </div>


                                                                <div class="col-md-12 col-12">
                                                                    <label for="description">Beschreibung</label>
                                                                    <textarea name="description" class="form-control" rows="5">{{$data->description}}</textarea>
                                                                </div>

                                                                <div class="col-md-12  time_management">
                                                                    <div class="row d-flex"  > 
                                                                        <div class="col-md-3 col-12">
                                                                            <label for="end_date">Fälligkeitsdatum</label>
                                                                            <input type="date"
                                                                                id="due_date"
                                                                                class="form-control"
                                                                                name="due_date"
                                                                                value="{{ old('due_date', \Carbon\Carbon::parse($data->due_date)->format('Y-m-d')) }}">

                                                                            <input type="hidden" id="start_date" class="form-control" name="start_date" value="{{ $data->start_date ?? \Carbon\Carbon::parse($data->created_at)->format('Y.m.d') }}">
                                                                        </div> 

                                                                            <div class="col-md-3 col-12">
                                                                            <label for="end_date">Fälligkeitsuhrzeit</label>
                                                                            <input type="time" id="due_time" class="form-control" name="due_time"  value="{{$data->due_time}}">  
                                                                        </div>  

                                                                            <div class="col-md-3 col-12">
                                                                            <label for="end_time">Gesamt Tage</label>
                                                                            <input type="integer" id="total_day" class="form-control" name="total_day" value="{{$data->total_day}}">
                                                                        </div> 
                                                                        <div class="col-md-3 col-12">
                                                                            <label for="end_time">Gesamtstunden</label>
                                                                            <input type="integer" id="total_time" class="form-control" name="total_time" value="{{$data->total_time}}">
                                                                        </div> 
                                                                    </div> 

                                                                    <div class="row mt-1" id="task_employee_section">
                                                                        <div class="col-md-12 col-12">
                                                                            <label for="employee">Zugewiesen an</label>
                                                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                                                @php
                                                                                    $selectedEmployees = DB::table('employees_personal_tasks')
                                                                                        ->where('task_id', $data->id)
                                                                                        ->pluck('employee_id')
                                                                                        ->toArray();
                                                                                @endphp

                                                                                @foreach ($employees as $emp)
                                                                                    <option value="{{ $emp->id }}"
                                                                                            data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                                                            @if(in_array($emp->id, $selectedEmployees)) selected @endif
                                                                                            data-checked="false">
                                                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row mt-1" id="controller_section">
                                                                        <div class="col-md-12 col-12">
                                                                            <label for="controller">Kontroller</label>
                                                                            @php
                                                                                $selectedControllers = is_array($data->controller_id)
                                                                                    ? $data->controller_id
                                                                                    : json_decode($data->controller_id, true);
                                                                            @endphp
                                                                            <select name="controller[]" id="controller" class="employee" multiple style="width:100%">
                                                                                @foreach ($employees as $emp)
                                                                                    <option value="{{ $emp->id }}"
                                                                                            data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                                                            data-checked="false"
                                                                                            @if(in_array($emp->id, $selectedControllers ?? [])) selected @endif>
                                                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>


                                                                </div>   
                                                                <div class="col-12">
                                                                    <div class="divider divider-left">
                                                                        <div class="divider-text">Uhrzeit- und Datumseinstellungen</div>
                                                                    </div> 
                                                                </div>  
                                                                
                                                              
                                                            <!-- Key Task Table -->
                                                            <div class="col-md-12 col-12">
                                                                <div class="card collapse-header mb-0" data-toggle="collapse" data-target="#subtaskCollapse" aria-expanded="false" role="button">
                                                                    <div class="card-header bg-primary text-white p-2 mt-2">
                                                                        <strong><i class="feather icon-layers"></i> Aufgabenschritte anzeigen</strong>
                                                                    </div>
                                                                </div>
                                                                <div id="subtaskCollapse" class="collapse">
                                                                    <div class="card-body p-1 border border-top-0">
                                                                        <div class="table-responsive">
                                                                            <table class="table" id="key_task">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Arbeitsschritte</th>
                                                                                        <th>Dauer <br><small><code id="key_total_time">23 Stunden</code></small></th>
                                                                                        <th>Zugewiesen</th>
                                                                                        <th>Beschreibung</th>
                                                                                        <th>Aktion</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @forelse ($key_task as $key)
                                                                                            @php 
                                                                                                $assignedRaw = json_decode($key->employee_id ?? '[]', true);

                                                                                                // Force it to be an array in all cases
                                                                                                $assigned = is_array($assignedRaw) ? $assignedRaw : (array) $assignedRaw;
                                                                                            @endphp
                                                                                        <tr data-id="{{ $key->id }}">
                                                                                            <td>
                                                                                                <div class="d-flex">
                                                                                                    <label class="mt-1 mr-1">{{ $loop->index + 1 }}</label>
                                                                                                    <input type="text" name="key[{{ $key->id }}][task]" value="{{ $key->task }}" class="form-control">
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="number" name="key[{{ $key->id }}][duration]" value="{{ $key->duration }}" class="form-control task-duration">
                                                                                            </td>
                                                                                            <td>
                                                                                                 <select name="key[{{ $key->id }}][employee_id][]" class="employee-select" multiple style="width:100%">
                                                                                                        @foreach ($employees as $employee)
                                                                                                            <option value="{{ $employee->id }}"
                                                                                                                {{ in_array($employee->id, $assigned) ? 'selected' : '' }}
                                                                                                                data-image="{{ asset('images/employee/'.$employee->image) }}">
                                                                                                                {{ $employee->name }} {{ $employee->lastname }}
                                                                                                            </option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <textarea name="key[{{ $key->id }}][key_description]" class="form-control">{{ $key->key_description }}</textarea>
                                                                                            </td>
                                                                                            <td>
                                                                                                <button type="button" class="btn btn-icon btn-danger remove-task" data-id="{{ $key->id }}">
                                                                                                    <i class="fa fa-trash"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @empty
                                                                                        <tr data-id="0">
                                                                                            <td><input type="text" name="key[0][task]" class="form-control" placeholder="Aufgabe eingeben"></td>
                                                                                            <td><input type="number" name="key[0][duration]" class="form-control"></td>
                                                                                            <td>
                                                                                                <select name="key[0][employee_id][]" class="employee-select" multiple style="width:100%">
                                                                                                    @foreach ($employees as $employee)
                                                                                                        <option value="{{ $employee->id }}" data-image="{{ asset('images/employee/'.$employee->image) }}">
                                                                                                            {{ $employee->name }} {{ $employee->lastname }}
                                                                                                        </option>
                                                                                                    @endforeach
                                                                                                </select>
                                                                                            </td>
                                                                                            <td><textarea name="key[0][key_description]" class="form-control"></textarea></td>
                                                                                            <td>
                                                                                                <button type="button" class="btn btn-icon btn-danger remove-task-list">
                                                                                                    <i class="fa fa-trash"></i>
                                                                                                </button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforelse
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                        <button type="button" class="btn btn-icon btn-success add-task mt-2">
                                                                            <i class="feather icon-plus"></i> Aufgabe hinzufügen
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            

                                                            </div>
                                                        </div>
                                                    </div>

                                                <div class="modal-footer"> 
                                                    <a type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" href="{{ url('personal/task/'.auth()->user()->name)}}"><i class="feather icon-x"></i> abbrechen</a>
                                                    <button type="submit" class="btn btn-primary save-task"><i class="feather icon-save"></i> speichern</button>
                                                </div> 
                                        </div>
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>

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
<script>
$(document).ready(function () {
    const collapse = $('#subtaskCollapse');
    const employeeSection = $('#task_employee_section');
    const hasTaskKeys = {{ $key_task->count() > 0 ? 'true' : 'false' }};

    // Initial state on load
    if (hasTaskKeys) {
        employeeSection.hide();
    }

    // On collapse open
    collapse.on('show.bs.collapse', function () {
        employeeSection.hide();
    });

    // On collapse close
    collapse.on('hide.bs.collapse', function () {
        if (!hasTaskKeys) {
            employeeSection.show();
        }
    });
});
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
 


 <!-- task step and manuals script: start -->
 <script>
    $(document).ready(function () {
        let keyTaskIndex = {{ $key_task->count() ?: 0 }};

        const employeeOptions = @json(
            collect($employees)->map(function ($e) {
                return [
                    'id' => $e->id,
                    'name' => $e->name . ' ' . $e->lastname,
                    'image' => asset('images/employee/' . $e->image)
                ];
            })
        );

        // Add task row
        $(document).on("click", ".add-task", function () {
            keyTaskIndex++;
            const rowCount = $("#key_task tbody tr").length + 1;
            const uniqueId = `new_${Date.now()}`;

            let optionsHtml = '';
            employeeOptions.forEach(emp => {
                optionsHtml += `<option value="${emp.id}" data-image="${emp.image}">${emp.name}</option>`;
            });

            const newRow = `
                <tr data-id="${uniqueId}" class="new-task-row">
                    <td class="d-flex">
                        <label class="mt-1 mr-1">${rowCount}</label>
                        <input type="text" name="key[${keyTaskIndex}][task]" class="form-control" placeholder="Aufgabe eingeben">
                    </td>
                    <td><input type="number" name="key[${keyTaskIndex}][duration]" class="form-control task-duration"></td>
                    <td>
                        <select name="key[${keyTaskIndex}][employee_id][]" class="employee-select" multiple style="width:100%">
                            ${optionsHtml}
                        </select>
                    </td>
                    <td><textarea name="key[${keyTaskIndex}][key_description]" class="form-control"></textarea></td>
                    <td>
                        <button type="button" class="btn btn-icon btn-danger remove-task-list" data-id="${uniqueId}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $("#key_task tbody").append(newRow);
            $(`tr[data-id="${uniqueId}"] .employee-select`).select2({
                templateResult: formatOption,
                templateSelection: formatOption,
                escapeMarkup: m => m
            });
        });

        // Remove from DB
        $(document).on("click", ".remove-task", function () {
            const row = $(this).closest("tr");
            const taskId = row.data("id");

            Swal.fire({
                title: 'Sind Sie sicher?',
                text: "Diese Aufgabe wird dauerhaft gelöscht.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen',
            }).then((result) => {
                if (result.isConfirmed && taskId) {
                    $.get(`/personal_task_key_delete/${taskId}`, function (response) {
                        if (response.success) {
                            row.remove();
                            toastr.success("Task erfolgreich gelöscht!");
                        } else {
                            toastr.error("Fehler beim Löschen.");
                        }
                    }).fail(() => {
                        toastr.error("Serverfehler. Bitte erneut versuchen.");
                    });
                }
            });
        });

        // Remove local unsaved row
        $(document).on("click", ".remove-task-list", function () {
            const row = $(this).closest("tr");

            Swal.fire({
                title: 'Entfernen?',
                text: "Diese Aufgabe ist noch nicht gespeichert.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, entfernen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                    toastr.info("Aufgabe entfernt.");
                }
            });
        });

        // Select2 option format
        function formatOption(option) {
            if (!option.id) return option.text;

            const img = $(option.element).data('image');
            if (!img) return option.text;

            return `<div class="d-flex align-items-center gap-2">
                        <img src="${img}" style="width:24px;height:24px;object-fit:cover;border-radius:50%;">
                        <span>${option.text}</span>
                    </div>`;
        }

        // Init existing selects
        $('.employee-select').select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            escapeMarkup: m => m
        });
    });
    </script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        let startDateInput = document.getElementById("start_date");
        let dueDateInput = document.getElementById("due_date");
        let dueTimeInput = document.getElementById("due_time");
        let totalDayInput = document.getElementById("total_day");
        let totalTimeInput = document.getElementById("total_time");

        function updateTotalDuration() {
            let totalTaskHours = 0;

            $('.task-duration').each(function () {
                let timeValue = parseFloat($(this).val()) || 0;
                totalTaskHours += timeValue;
            });

            let totalTimeAllowed = parseFloat($('#total_time').val()) || 0;
            let remainingHours = totalTimeAllowed - totalTaskHours;

            let remainingText = remainingHours >= 0
                ? `${remainingHours} Std`
                : `Überschreitung um ${Math.abs(remainingHours)} Std!`;

            $('#key_total_time').text(remainingText);

            if (totalTaskHours > totalTimeAllowed) {
                Swal.fire({
                    icon: "error",
                    title: "⚠ Zeitüberschreitung!",
                    text: `Die gesamte Dauer der Aufgaben beträgt ${totalTaskHours} Stunden, überschreitet jedoch die geplanten ${totalTimeAllowed} Stunden.`,
                });
            }
        }

        function calculateTotalDaysAndHours() {
            let startDate = new Date(startDateInput.value);
            let dueDate = new Date(dueDateInput.value);

            if (!startDateInput.value || !dueDateInput.value || isNaN(startDate) || isNaN(dueDate)) {
                totalDayInput.value = "";
                totalTimeInput.value = "";
                return;
            }

            let workHoursPerDay = 24; // Assuming full 24-hour working days
            let totalDays = 0;
            let totalWorkingHours = 0;
            
            let tempDate = new Date(startDate);
            
            // Count only weekdays (Mon-Fri)
            while (tempDate <= dueDate) {
                let day = tempDate.getDay(); // 0 = Sunday, 6 = Saturday
                if (day !== 0 && day !== 6) { // Exclude weekends
                    totalDays++;
                    totalWorkingHours += workHoursPerDay;
                }
                tempDate.setDate(tempDate.getDate() + 1);
            }

            // Adjust totalWorkingHours if due_time is set
            if (dueTimeInput.value) {
                let [dueHour, dueMinute] = dueTimeInput.value.split(":").map(Number);
                let remainingHoursForDueDay = dueHour + (dueMinute > 0 ? 1 : 0);

                let lastDay = new Date(dueDate);
                let lastDayOfWeek = lastDay.getDay();

                // If the due date falls on a weekend, skip to the next Monday
                while (lastDayOfWeek === 0 || lastDayOfWeek === 6) {
                    lastDay.setDate(lastDay.getDate() + 1);
                    lastDayOfWeek = lastDay.getDay();
                }

                totalWorkingHours -= workHoursPerDay; // Remove the last full 24 hours
                totalWorkingHours += remainingHoursForDueDay; // Add the due time hours
            }

            totalDayInput.value = totalDays;
            totalTimeInput.value = totalWorkingHours;

            updateTotalDuration();
        }


        dueDateInput.addEventListener("change", calculateTotalDaysAndHours);
        dueTimeInput.addEventListener("change", calculateTotalDaysAndHours);
        startDateInput.addEventListener("change", calculateTotalDaysAndHours);

        
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
 
    <script>
$(document).ready(function () {
    const $select = $('#customerLeadProductSelect');
    const $switch = $('#customerSwitch');
    const $container = $('#customerSelectContainer');

    // Enable Select2 with AJAX
    $select.select2({
        placeholder: 'Kunde suchen...',
        ajax: {
            url: '{{ route("lead.product.list.ajax") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            html: item.html,
                            alternative_id: item.alternative_id,
                            product_id: item.product_id
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            return $(data.html);
        },
        templateSelection: function (data) {
            // Populate hidden fields on selection
            if (data.alternative_id) {
                $('#select_alternative_id').val(data.alternative_id);
            }
            if (data.product_id) {
                $('#select_product_id').val(data.product_id);
            }
            return data.text;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Toggle visibility and sync switch value
    function toggleCustomerFields(show) {
        if (show) {
            $container.slideDown();
            $switch.val(1);
        } else {
            $container.slideUp();
            $select.val(null).trigger('change');
            $('#select_alternative_id').val('');
            $('#select_product_id').val('');
            $switch.val(0);
        }
    }

    $switch.on('change', function () {
        toggleCustomerFields($(this).is(':checked'));
    });

    // On page load (edit mode)
    @if(!empty($data->is_customer) && $data->is_customer == 1)
        toggleCustomerFields(true);
    @else
        toggleCustomerFields(false);
    @endif
});
</script>

 
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs =[
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Aufgabeliste',
                url: "{{ url('admin/todo/personal')}}", 
            },
            {
                label: 'Bearbeiten',
                url: "{{ url()->current()}}",
                clickable: false
            }

        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush