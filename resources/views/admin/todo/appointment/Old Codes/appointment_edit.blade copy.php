@extends('admin.layouts.app')
@section('title')
BEARBEITEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('content')
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
                            <li class="breadcrumb-item active"><a href="{{ url('/personal_task_view') }}">Bearbeiten</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">  
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                                <form  id="task-store-form">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="card p-1">
                                            <div class="form-body">
                                                <div class="row">   
                                                    <div class="col-md-3 col-12">
                                                        <label for="task_title">Titel</label>
                                                        <input type="text" id="name" class="form-control" name="name" value="{{$data->name}}">
                                                        <input type="hidden" name="id" value="{{$data->id}}">
                                                    </div>

                                                    <div class="col-md-3 col-12">
                                                        <label for="task_title">Ort des Termin</label>
                                                        <select name="execution_type" id="execution_type" class="form-control">
                                                                <option selected disabled>Wählen</option>
                                                                <option value="internal" @if($data->execution_type == 'internal') selected @endif>Intern</option>
                                                                <option value="external" @if($data->execution_type == 'external') selected @endif>Extern</option>
                                                                <option value="online" @if($data->execution_type == 'online') selected @endif>Online</option>
                                                                <option value="telephone" @if($data->execution_type == 'telephone') selected @endif>Telefon</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-3 col-12">
                                                        <label for="task_title">Art des Termin</label> 
                                                        <input type="text" id="appointment_type" class="form-control" name="appointment_type" value="{{$data->appointment_type}}">

                                                    </div>

                                                    <div class="col-md-3  " > 
                                                        <div class="row">
                                                                <div class="col-md-6"> 
                                                                    <input type="hidden" name="color" id="color" value="#8fc73e"> 
                                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mt-1 " id="color_drop_down">
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

                                                                <div class="col-md-6"> 
                                                                    <label for="task_title">Öffentlich</label>
                                                                    <div class="custom-control custom-switch   ">  
                                                                        <input type="checkbox" class="custom-control-input" id="customSwitch10" name="public" checked>
                                                                        <label class="custom-control-label" for="customSwitch10">
                                                                            <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                                                            <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                                                        </label> 
                                                                    </div>                               
                                                                </div> 
                                                        </div> 
                                                    </div> 
                                 
                                                     <div class="col-md-3"  style="display:none;" id="link_section" >
                                                                <span>Link</span>
                                                                <input type="text" class="form-control" value="{{ old('link', $data->link) }}" id="link" name="link" >
                                                        </div> 
                                                    
                                                    <div class="col-md-3" id="intern" style="display: none;">
                                                            <label for="task_title">Adress</label>
                                                            <select name="branch_address_id" class="form-control" >
                                                                <option ></option>
                                                                @foreach ($branch_addresses as $address)
                                                                    <option value="{{ $address->id }}" 
                                                                        data-street="{{ $address->street }}"
                                                                        data-latitude="{{ $address->latitude }}"
                                                                        data-longitude="{{ $address->longitude }}"
                                                                        data-city="{{ $address->city }}"
                                                                        data-postcode="{{ $address->postcode }}"
                                                                        @if($address->id == $data->branch_address_id) selected @endif
                                                                    >{{ $address->branch_initial }} - {{ $address->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        
                                                        </div>

                                                        <div class="col-md-3" id="extern">
                                                            <label for="task_title">Adress</label> 
                                                            <input id="full_address" type="text" class="form-control form-element"  
                                                                    placeholder="Adresse eingeben" 
                                                                    name="full_address" 
                                                                    value="{{$data->full_address}}"> 

                                                                <input type="hidden" id="street-input" name="street" value="{{$data->street}}">
                                                                <input type="hidden" id="city-input" name="city" value="{{$data->city}}">
                                                                <input type="hidden" id="latitude-input" name="latitude" value="{{$data->latitude}}">
                                                                <input type="hidden" id="longitude-input" name="longitude" value="{{$data->longitude}}">
                                                                <input type="hidden" id="postal_code-input" name="postcode" value="{{$data->postcode}}">
                                                        </div>
                    

                                                    <div class="col-md-3"  >
                                                            <label for="task_title">Telefon</label>
                                                            <input type="text" class="form-control" value="{{ old('telephone') }}" id="telephone-input" name="telephone"  >
                                                    </div> 

                                                        <div class="col-md-3"  >
                                                        <label for="task_title">Kunde</label>
                                                            <select name="customer_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($customers as $cus) 
                                                            <option value="{{ $cus->id}}" @if($data->customer_id == $cus->id) selected @endif >{{$cus->name}} {{$cus->lastname}}</option>
                                                             @endforeach
                                                            </select>
                                                    </div> 

                                                    <div class="col-md-3"  >
                                                        <label for="task_title">Betrieb</label>
                                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($branches as $br)
                                                                <option value="{{ $br->id}}" @if($data->branch_id == $br->id) selected @endif >{{$br->branch}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>   
                                                    
                                                        <div class="col-md-12 time_management_card">
                                                            <div class="row d-flex">
                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="start_date">Startdatum</label>
                                                                    <input type="hidden" name="same_id" value="same">
                                                                    <input type="date" id="start_date" class="form-control" name="start_date"  value="{{$data->start_date}}">
                                                                    <input type="hidden" id="end_date" class="form-control" name="end_date"  value="{{$data->end_date}}"> 
                                                                </div> 
                                                                <div class="col-md-3 col-12">
                                                                    <label for="start_time">Startzeit</label>
                                                                    <input type="time" id="start_time" class="form-control" name="start_time" 
                                                                        value="{{ isset($data->start_time) ? \Carbon\Carbon::parse($data->start_time)->format('H:i') : '' }}">
                                                                </div>

                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="end_time">Endzeit</label>
                                                                    <input type="time" id="end_time" class="form-control" name="end_time" 
                                                                        value="{{ isset($data->end_time) ? \Carbon\Carbon::parse($data->end_time)->format('H:i') : '' }}">
                                                                </div>

                                                                <div class="col-md-3 col-12 ">
                                                                    <label for="total_time">Gesamtzeit</label>
                                                                    <input type="number" id="total_time" class="form-control" name="total_time" value="{{$data->total_time}}">
                                                                </div>

                                                                <div class="col-md-4 col-12  ">
                                                                    <label for="date_type">Typ</label>
                                                                    <select name="date_type" id="date_type" class="form-control"   style="width:100%">  
                                                                        <option >Wählen</option>
                                                                        <option value="day" @if($data->date_type == 'day') selected @endif >Ganzer Tag</option>
                                                                        <option value="week"  @if($data->date_type == 'week') selected @endif  >7 Tage (Eine Woche)</option>
                                                                        <option value="daily"   @if($data->date_type == 'daily') selected @endif >Täglich</option>
                                                                        <option value="monthly"  @if($data->date_type == 'weekly') selected @endif  >Monatlich</option>  
                                                                    </select>
                                                                </div> 

                                                                <div class="col-md-4 col-12 from_day ">
                                                                    <label for="end_time">Von</label>
                                                                    <select name="from_day" id="from_day" class="selectables" style="width:100%">  
                                                                        <option value="monday"  @if($data->from_day == 'monday') selected @endif  >Montag</option> 
                                                                        <option value="tuesday"  @if($data->from_day == 'tuesday') selected @endif >Dienstag</option> 
                                                                        <option value="wednesday"  @if($data->from_day == 'wednesday') selected @endif >Mittwoch</option> 
                                                                        <option value="thursday"  @if($data->from_day == 'thursday') selected @endif >Donnerstag</option> 
                                                                        <option value="friday"  @if($data->from_day == 'friday') selected @endif >Freitag</option> 
                                                                        <option value="saturday"  @if($data->from_day == 'saturday') selected @endif >Samstag</option> 
                                                                        <option value="sunday"  @if($data->from_day == 'sunday') selected @endif >Sonntag</option> 
                                                                    </select>
                                                                </div>


                                                                <div class="col-md-4 col-12 to_day ">
                                                                    <label for="end_time">Zu</label>
                                                                    <select name="to_day" id="to_day" class="selectables" style="width:100%">  
                                                                       <option value="monday"  @if($data->to_day == 'monday') selected @endif  >Montag</option> 
                                                                        <option value="tuesday"  @if($data->to_day == 'tuesday') selected @endif >Dienstag</option> 
                                                                        <option value="wednesday"  @if($data->to_day == 'wednesday') selected @endif >Mittwoch</option> 
                                                                        <option value="thursday"  @if($data->to_day == 'thursday') selected @endif >Donnerstag</option> 
                                                                        <option value="friday"  @if($data->to_day == 'friday') selected @endif >Freitag</option> 
                                                                        <option value="saturday"  @if($data->to_day == 'saturday') selected @endif >Samstag</option> 
                                                                        <option value="sunday"  @if($data->to_day == 'sunday') selected @endif >Sonntag</option> 
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4 col-12 from_month ">
                                                                    <label for="month">Von (Monat)</label>
                                                                    <select name="from_month" id="from_month" class="selectables" style="width:100%">  
                                                                        <option value="january"   @if($data->from_month == 'sunday') selected @endif >Januar</option> 
                                                                        <option value="february"  @if($data->from_month == 'sunday') selected @endif >Februar</option> 
                                                                        <option value="march"  @if($data->from_month == 'sunday') selected @endif >März</option> 
                                                                        <option value="april"  @if($data->from_month == 'sunday') selected @endif >April</option> 
                                                                        <option value="may"  @if($data->from_month == 'sunday') selected @endif >Mai</option> 
                                                                        <option value="june"  @if($data->from_month == 'sunday') selected @endif >Juni</option> 
                                                                        <option value="july"  @if($data->from_month == 'sunday') selected @endif >Juli</option> 
                                                                        <option value="august"  @if($data->from_month == 'sunday') selected @endif >August</option> 
                                                                        <option value="september"  @if($data->from_month == 'sunday') selected @endif >September</option> 
                                                                        <option value="october"  @if($data->from_month == 'sunday') selected @endif >Oktober</option> 
                                                                        <option value="november"  @if($data->from_month == 'sunday') selected @endif >November</option> 
                                                                        <option value="december"  @if($data->from_month == 'sunday') selected @endif >Dezember</option> 
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4 col-12 to_month ">
                                                                    <label for="month">Zu (Monat)</label>
                                                                    <select name="to_month" id="to_month" class="selectables" style="width:100%">  
                                                                      <option value="january"   @if($data->to_month == 'sunday') selected @endif >Januar</option> 
                                                                        <option value="february"  @if($data->to_month == 'sunday') selected @endif >Februar</option> 
                                                                        <option value="march"  @if($data->to_month == 'sunday') selected @endif >März</option> 
                                                                        <option value="april"  @if($data->to_month == 'sunday') selected @endif >April</option> 
                                                                        <option value="may"  @if($data->to_month == 'sunday') selected @endif >Mai</option> 
                                                                        <option value="june"  @if($data->to_month == 'sunday') selected @endif >Juni</option> 
                                                                        <option value="july"  @if($data->to_month == 'sunday') selected @endif >Juli</option> 
                                                                        <option value="august"  @if($data->to_month == 'sunday') selected @endif >August</option> 
                                                                        <option value="september"  @if($data->to_month == 'sunday') selected @endif >September</option> 
                                                                        <option value="october"  @if($data->to_month == 'sunday') selected @endif >Oktober</option> 
                                                                        <option value="november"  @if($data->to_month == 'sunday') selected @endif >November</option> 
                                                                        <option value="december"  @if($data->to_month == 'sunday') selected @endif >Dezember</option> 
                                                                    </select>
                                                                </div> 
                                                            </div> 
                                                        </div> 
                                                    </div> 

                                                    <div class="row mt-1">
                                                        <div class="col-md-12 col-12"> 
                                                            <label for="task_title">Teilnehmer</label> 
                                                                <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                                        @php 
                                                                            // Get the selected employees for the current appointment
                                                                            $selectables = DB::table('main_appointment_employees')
                                                                                ->where('appointment_id', $data->id) 
                                                                                ->pluck('employee_id')  // Only fetch the employee_id to optimize the query
                                                                                ->toArray();  // Convert to array for easy lookup
                                                                        @endphp

                                                                        @foreach ($employees as $emp) 
                                                                            <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}" 
                                                                                @if(in_array($emp->id, $selectables)) selected @endif>
                                                                                {{ $emp->name }} {{ $emp->lastname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                        </div> 

                                                        <div class="col-md-12 col-12 mb-1"> 
                                                            <label for="task_title">Beschreibung</label>  
                                                               <textarea name="description" class="form-control" rows="2">{{$data->note}}</textarea>
                                                        </div>
                                                    </div> 
                                                            
                                                </div>  

                                                    <div class="col-md-12 p-0">
                                                         <div class="table-responsive">
                                                             <table class="table"> 
                                                                    <tr  >
                                                                        <th> Wiederholung </th>
                                                                        <th>Erinnerung</th>                                                                   
                                                                        <th>Priorität</th> 
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
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
                                                                        <td style="text-align:right">
                                                                            <label for="reminder_date" style="float:left;">Datum:</label>
                                                                            <input type="date" name="reminder_date" class="form-control" value="{{$data->reminder_date}}">
                                                                            <label for="reminder_time" style="float:left;" class="mt-1">Zeit:</label>
                                                                            <input type="time" name="reminder_time" class="form-control" value="{{$data->reminder_time}}">
                                                                        </td> 
                                                                        <td >
                                                                            <select name="priority" class="form-control" id="priority">
                                                                                <option value="">Häufigkeit auswählen</option>
                                                                                <option value="normal" @if($data->priority == 'normal') selected @endif>Normal</option> 
                                                                                <option value="medium" @if($data->priority == 'medium') selected @endif>Medium</option>
                                                                                <option value="high" @if($data->priority == 'high') selected @endif>Hoch</option>
                                                                                <option value="very high" @if($data->priority == 'very high') selected @endif>Sehr Wichtig</option> 
                                                                        </td>
                                                                 </tr> 

                                                            </table>  
                                                         </div>
                                                    </div>  
                                                </div>
                                            </div>
                                        </div>

                                    <div class="modal-footer"> 
                                        <button type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                                        <button type="button" class="btn btn-primary save-task"><i class="feather icon-save"></i> speichern</button>
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
            escapeMarkup: function (markup) {
                return markup;
            },
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

    // Handle save operation
    $('.save-task').on('click', function (e) {
        e.preventDefault();

        let form = $('#task-store-form');
        let formData = form.serialize();
        let errors = [];

        // Get form field values
        let title = $('#name').val();
        // let appointmentType = $('#appointment_type').val();
        let executionType = $('#execution_type').val();
        let employee = $('#employee').val();
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        let startTime = $('#start_time').val();
        let endTime = $('#end_time').val();
        let branchId = $('select[name="branch_id"]').val();
        let branchAddressId = $('select[name="branch_address_id"]').val();
        let fullAddress = $('#full_address').val();
        let link = $('#link').val();
        let telephone = $('#telephone-input').val();

        // General validation
        if (!title) errors.push('Der Titel darf nicht leer sein.');
        // if (!appointmentType) errors.push('Bitte wählen Sie eine Terminart aus.');
        if (!executionType) errors.push('Bitte wählen Sie einen Ausführungsort aus.');
        if (!startDate) errors.push('Das Startdatum darf nicht leer sein.');
        if (!endDate) errors.push('Das Enddatum darf nicht leer sein.');
        if (new Date(startDate) > new Date(endDate)) errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
        if (!startTime) errors.push('Bitte geben Sie eine Startzeit ein.');
        if (!endTime) errors.push('Bitte geben Sie eine Endzeit ein.');
        if (startTime && endTime && startTime >= endTime) errors.push('Die Startzeit muss vor der Endzeit liegen.');
        if (!employee || employee.length === 0) errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
        if (!branchId) errors.push('Bitte wählen Sie einen Betrieb aus.');

        // Execution type-specific validation
        if (executionType === 'intern' && !branchAddressId) errors.push('Bitte wählen Sie eine interne Adresse aus.');
        if (executionType === 'extern' && !fullAddress) errors.push('Bitte geben Sie eine externe Adresse ein.');
        if (executionType === 'online' && !link) errors.push('Bitte geben Sie einen Link für das Online-Meeting ein.');
        if (executionType === 'telephone' && !telephone) errors.push('Bitte geben Sie eine Telefonnummer für das Telefonat ein.');

        // Show validation errors
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: `<ul style="text-align: left;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`,
            });
            return;
        }

        // AJAX request to store the task
        let actionUrl = '{{ route('appointment.update') }}';

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $('.save-task').prop('disabled', true).text('speichern...');
            },
            success: function (response) {
                $('.save-task').prop('disabled', false).text('speichern');
                form.trigger('reset');
                $('#create').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Erfolg',
                    text: 'Aufgabe erfolgreich gespeichert!',
                }).then(() => {
                    window.location.href = "/appointments"; // ✅ Redirect to appointments page
                });
            },
            error: function (xhr) {
                $('.save-task').prop('disabled', false).text('speichern');

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
        document.getElementById("full_address").value = "";
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
            script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places";
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

 
 
@endsection