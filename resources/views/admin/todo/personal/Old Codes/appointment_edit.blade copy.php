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
                            <form  id="task-form">
                                @csrf
                                <div class="modal-body p-0">
                                    <div class="card p-1">
                                        <div class="form-body">
                                            <div class="row">  
                                            <input type="hidden" name="id" value="{{$data->id}}">
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
                                                <div class="col-md-4 col-12">
                                                    <label for="task_title">Aufgabentitel</label>
                                                    <input type="text" id="name" class="form-control" name="name" value="{{$data->name}}">
                                                </div>


                                                <div class="col-md-4 col-12">
                                                    <label for="task_title">Art des Termin</label>
                                                    <select name="appointment_type" id="appointment_type" class="form-control">
                                                            <option selected disabled>Wählen</option>
                                                            <option value="internal" @if($data->appointment_type == 'internal') selected @endif>Intern</option>
                                                            <option value="external" @if($data->appointment_type == 'external') selected @endif>Extern</option>
                                                            <option value="online" @if($data->appointment_type == 'online') selected @endif>Online</option>
                                                            <option value="telephone" @if($data->appointment_type == 'telephone') selected @endif>Telefon</option>
                                                    </select>
                                                </div>

                                                
                                                <div class="col-3">
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
                                                </div>

                                                <div class="col-md-12 col-12 mb-1">
                                                    <label for="description">Beschreibung</label>
                                                    <textarea name="description" class="form-control" rows="2">{{$data->note}}</textarea>
                                                </div>
 
                                                 <div class="col-md-3"  style="display:none;" id="link_section" >
                                                        <span>Link zum Online-Meeting</span>
                                                        <input type="text" class="form-control" value="{{ old('link') }}" id="link" name="link" >
                                                </div> 
                                            
                                            <div class="col-md-3">
                                                    <span>Address</span>
                                                    <select name="branch_address_id" id="intern" class="form-control" style="display: none;">
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
                                                    <input id="full_address" type="text" class="form-control form-element"  id="extern"
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
                                                        <span>Telefon</span>
                                                        <input type="text" class="form-control" value="{{$data->phone}}" id="telephone-input" name="phone"  >
                                                </div> 

                                                    <div class="col-md-3"  >
                                                        <span>Kunde</span>
                                                        <select name="customer_id" id="" class="selectables" style="width:100%">
                                                        <option selected disabled>Keine</option>
                                                        @foreach($customers as $cus) 
                                                            <option value="{{ $cus->id}}" @if($data->customer_id == $cus->id) selected @endif >{{$cus->name}} {{$cus->lastname}}</option>
                                                        @endforeach
                                                        </select>
                                                </div> 

                                                    <div class="col-md-3"  >
                                                        <span>Betrib</span>
                                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                                         <option selected disabled>Keine</option>

                                                        @foreach($branches as $br)
                                                            <option value="{{ $br->id}}" @if($data->branch_id == $br->id) selected @endif >{{$br->branch}} </option>
                                                        @endforeach
                                                        </select>
                                                </div> 
                                                <div class="col-12">
                                                    <div class="divider divider-left">
                                                        <div class="divider-text">Uhrzeit- und Datumseinstellungen</div>
                                                    </div> 
                                                </div>  
                                                
                                                    <div class="col-md-12 time_management_card">
                                                        <div class="row d-flex">
                                                            <div class="col-md-3 col-12">
                                                                <label for="start_date">Startdatum</label>
                                                                <input type="hidden" name="same_id" value="same">
                                                                <input type="date" id="start_date" class="form-control" name="start_date"  value="{{$data->start_date}}">
                                                            </div>

                                                            <div class="col-md-3 col-12">
                                                                <label for="end_date">Enddatum</label>
                                                                <input type="date" id="end_date" class="form-control" name="end_date"  value="{{$data->end_date}}">
                                                            </div> 
                                                            <div class="col-md-3 col-12">
                                                                <label for="start_time">Startzeit</label>
                                                                <input type="time" id="start_time" class="form-control" name="start_time"  value="{{$data->start_time}}">
                                                            </div>

                                                            <div class="col-md-3 col-12">
                                                                <label for="end_time">Endzeit</label>
                                                                <input type="time" id="end_time" class="form-control" name="end_time" value="{{$data->end_time}}">
                                                            </div> 
                                                        </div> 

                                                        <div class="row mt-1">
                                                            <div class="col-md-12 col-12">
                                                                <label for="employee">Teilnehmer</label>
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
                                                        </div>
                                                    </div> 

                                                    
                                            </div>
                                        </div>
                                    </div>

                                <div class="modal-footer"> 
                                    <a type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" href="{{ url('appointments') }}"><i class="feather icon-x"></i> abbrechen</a>
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
<script>
    $(document).ready(function(){
        $('.selectables').select2({

        });
    })
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

    <script>
    $(document).ready(function () {
        // Initialize select2 for existing rows
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

        // Handle save operation
        $('.save-task').on('click', function (e) {
            e.preventDefault();

            let form = $('#task-form');

            // Ensure the hidden inputs for address are properly populated before serialization
            if ($('#appointment_type').val() === 'internal') {
                const selectedOption = $('#intern').find(':selected');
                if (selectedOption.length > 0) {
                    $('#street-input').val(selectedOption.data('street') || '');
                    $('#city-input').val(selectedOption.data('city') || '');
                    $('#postal_code-input').val(selectedOption.data('postcode') || '');
                    $('#latitude-input').val(selectedOption.data('latitude') || '');
                    $('#longitude-input').val(selectedOption.data('longitude') || '');
                }
            }

            let formData = form.serialize();  // Serialize after updating the hidden inputs

            // Basic validations
            let startDate = $('#start_date').val();
            let endDate = $('#end_date').val();
            let employee = $('#employee').val(); 
            let errors = [];

            // Validate start_date and end_date
            if (!startDate) {
                errors.push('Das Startdatum darf nicht leer sein.');
            }
            
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                errors.push('Das Startdatum darf nicht größer als das Enddatum sein.');
            }

            // Validate employee selection
            if (!employee || employee.length === 0) {
                errors.push('Bitte weisen Sie mindestens einen Mitarbeiter zu.');
            }

            // Show errors using SweetAlert if any
            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validierungsfehler',
                    html: `<ul style="text-align: left;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`,
                });
                return;
            }

            // AJAX request
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
                        window.location.href = "{{ route('main.appointment') }}"; // Redirect to view
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
    window.onload = function() {
        loadGoogleMapsAPI();
        handleAppointmentType(); // Check and display relevant section on page load

        document.getElementById('appointment_type').addEventListener('change', function () {
            handleAppointmentType(); // Re-check and display the correct section on change
        });
    }

    function handleAppointmentType() {
        const appointmentType = document.getElementById('appointment_type').value;
        const internDropdown = document.getElementById('intern');
        const externalInput = document.getElementById('full_address');
        const linkSection = document.getElementById('link_section');

        // Hide all sections by default
        internDropdown.style.display = 'none';
        externalInput.style.display = 'none';
        linkSection.style.display = 'none';
        resetHiddenInputs();  // Clear previous values

        if (appointmentType === 'internal') {
            internDropdown.style.display = 'block';

            // Populate hidden inputs with the selected option on page load (if any)
            updateHiddenInputsFromSelect(internDropdown);

            // Listen for dropdown changes and update hidden inputs
            internDropdown.addEventListener('change', function () {
                updateHiddenInputsFromSelect(this);
            });

        } else if (appointmentType === 'external') {
            externalInput.style.display = 'block';

        } else if (appointmentType === 'online') {
            linkSection.style.display = 'block';
        }
    }

    function updateHiddenInputsFromSelect(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        if (selectedOption) {
            document.getElementById('street-input').value = selectedOption.getAttribute('data-street') || '';
            document.getElementById('city-input').value = selectedOption.getAttribute('data-city') || '';
            document.getElementById('postal_code-input').value = selectedOption.getAttribute('data-postcode') || '';
            document.getElementById('latitude-input').value = selectedOption.getAttribute('data-latitude') || '';
            document.getElementById('longitude-input').value = selectedOption.getAttribute('data-longitude') || '';
        }
    }

    function resetHiddenInputs() {
        document.getElementById('street-input').value = '';
        document.getElementById('city-input').value = '';
        document.getElementById('postal_code-input').value = '';
        document.getElementById('latitude-input').value = '';
        document.getElementById('longitude-input').value = '';
    }

    function initializeAutocomplete() {
        const input = document.getElementById('full_address');
        const autocomplete = new google.maps.places.Autocomplete(input, {
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

                if (types.includes('route')) {
                    street = component.long_name;
                }
                if (types.includes('locality') || types.includes('sublocality')) {
                    city = component.long_name;
                }
                if (types.includes('postal_code')) {
                    postalCode = component.long_name;
                }
            });

            latitude = place.geometry.location.lat();
            longitude = place.geometry.location.lng();

            // Populate inputs with external address data
            document.getElementById('full_address').value = place.formatted_address;
            document.getElementById('street-input').value = street;
            document.getElementById('city-input').value = city;
            document.getElementById('postal_code-input').value = postalCode;
            document.getElementById('latitude-input').value = latitude;
            document.getElementById('longitude-input').value = longitude;
        });
    }

    // Dynamically load Google Maps API
    function loadGoogleMapsAPI() {
        const script = document.createElement('script');
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initializeAutocomplete";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
</script>



 
 
@endsection