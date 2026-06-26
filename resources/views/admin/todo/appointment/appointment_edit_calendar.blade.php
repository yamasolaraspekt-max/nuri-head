@extends('admin.layouts.app')
@section('title')
BEARBEITEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

.edit_task {
        display: none; /* Hidden by default */
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); /* Center the div */
        background: #f1f1f1; 
        z-index: 10000;
        width: 30% !important; /* Default width */
        max-width: 3-% !important;
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
                  <div class="cards new_task_card edit_task" style="display:none">
                        <div class="card-header" style="  border: 0;  background: transparent;  padding: 0;     justify-items: anchor-center;">
                            <h3 class="title mt-1 ml-2" style="    color: #8fc73e !important; font-weight: bold;  justify-items: left;"> TERMIN BEARBEITEN</h3>
                               <div class="line"  style="    border-bottom: 2px solid #8fc73e; width:90% !important"></div> 
                           
                        </div>  
                        <div class="card-body p-0"> 
                            <form  id="task-update-form">
                                @csrf
                                <div class="modal-body pt-0 pb-0">
                                    <div class="cards p-1">
                               

                                        <div class="form-body">
                                                <div class="row">   
                                                    <div class="col-md-10 col-10">
                                                        <label for="task_title">Titel / Name *</label>
                                                        <input type="text" id="name" class="form-control" name="name">
                                                    </div> 

                                                    <div class="col-md-2"> 
                                                         <input type="hidden" name="color" id="color" value="#8fc73e"> 
                                                        <div class="btn-group dropup dropdown-icon-wrapper mt-1 " id="color_drop_down">
                                                            <button type="button" class="btn btn-icon    waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                <i class="fa fa-square" id="colorIcon" style="color: #8fc73e;"></i>
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
                                                            <input type="date" id="start_date" class="form-control" name="start_date"  value="">
                                                            <input type="hidden" id="end_date" class="form-control" name="end_date" value="">

                                                        </div> 
                                                        <div class="col-md-5 col-12">
                                                            <label for="start_time">Startzeit *</label>
                                                            <input type="time" id="start_time" class="form-control" name="start_time" value="">
                                                        </div>

                                                        <div class="col-md-2 " > 
                                                            <div class="row"> 

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

                                                        <div class="col-md-12 col-12"> 
                                                            <label for="task_title">Teilnehmer *</label> 
                                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                                @foreach ($employees as $emp)
                                                                <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}"  >{{ $emp->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div> 

                                                        <div class="col-md-6"  >
                                                            <label for="task_title">Kontakt</label>
                                                            <select name="contact_id"   class="contact_list" style="width:100%">
                                                            
                                                            </select>
                                                            <input type="hidden" name="contact_type" id="contact_type" value="">
                                                        </div> 

                                                         <div class="col-md-6"  style="display:none;" id="link_section-edit" >
                                                                <span>Link *</span>
                                                                <input type="text" class="form-control" value="{{ old('link') }}" id="link" name="link" >
                                                        </div> 
                                                    
                                                        <div class="col-md-6" id="intern-edit" style="display: none;">
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
                                                                    >{{ $address->branch_initial }} - {{ $address->name }}</option>
                                                                @endforeach
                                                            </select> 
                                                        </div>

                                                        <div class="col-md-6" id="extern-edit">
                                                            <label for="task_title">Adress *</label> 
                                                            <input id="full_address-edit" type="text" class="form-control form-element full_address"  
                                                                placeholder="Adresse eingeben" 
                                                                name="full_address" 
                                                                value=""> 

                                                            <input type="hidden" id="street-input-edit" name="street" value="">
                                                            <input type="hidden" id="city-input-edit" name="city" value="">
                                                            <input type="hidden" id="latitude-input-edit" name="latitude" value="">
                                                            <input type="hidden" id="longitude-input-edit" name="longitude" value="">
                                                            <input type="hidden" id="postal_code-input-edit" name="postcode" value="">
                                                        </div>

                                                         <div class="col-md-6"  >
                                                            <label for="task_title">Telefon</label>
                                                                <input type="text" class="form-control phone" value="{{ old('phone') }}"   name="phone"  >
                                                        </div> 

                                                        <div class="col-md-6"  >
                                                                <label for="task_title">Email <small>Optional</small></label>
                                                                <input type="email" class="form-control email" value="{{ old('email') }}"  name="email"  >
                                                        </div> 
 
                                                    <div class="col-md-6 col-12">
                                                        <label for="task_title">Zweck</label> 
                                                            <input type="text" class="form-control" value="{{ old('appointment_type') }}" id="appointment_type" name="appointment_type" >
                                                    </div>

                                                      <div class="col-md-6 col-12">
                                                        <label for="task_title">Ort des Termin *</label>
                                                        <select name="execution_type" id="execution_type-edit" class="form-control"> 
                                                                <option value="internal">Intern</option>
                                                                <option value="external" selected>Extern</option>
                                                                <option value="online">Online</option>
                                                                <option value="telephone">Telefon</option>
                                                        </select>
                                                    </div>


                                                    <div class="col-md-12 col-12 mb-1"> 
                                                        <label for="task_title">Beschreibung</label> 

                                                        <textarea name="description" class="form-control" rows="1"></textarea>
                                                    </div>

                                                     <div class="col-md-4 col-12 ">
                                                        <label for="end_time">Endzeit *</label>
                                                        <input type="time" id="end_time" class="form-control" name="end_time">
                                                    </div> 
                                                    <div class="col-md-4 col-12 ">
                                                        <label for="total_time">Termin Dauer </label>
                                                        <input type="number" id="total_time" class="form-control" name="total_time">
                                                    </div>

                                                    
                                                    <div class="col-md-4"  >
                                                        <label for="task_title">Betrieb</label>
                                                        <select name="branch_id" id="" class="selectables" style="width:100%">
                                                            <option></option>
                                                            @foreach($branches as $br)
                                                                <option value="{{ $br->id}}">{{$br->branch}} </option>
                                                            @endforeach
                                                        </select>
                                                    </div>   

                                                    <div class="col-12 line"></div>
                     
                                                         <div class="col-md-6 col-12 ">
                                                            <label for="month">Erinnerung</label>
                                                             <input type="date" name="reminder_date" class="form-control">
                                                        </div> 

                                                        <div class="col-md-6 col-12 ">
                                                            <label for="month">Erinnerung</label>
                                                            <input type="time" name="reminder_time" class="form-control">
                                                        </div> 

                                                        <div class="col-md-6 col-12 ">
                                                            <label for="priority">Priorität</label>
                                                            <select name="priority" class="form-control" id="priority"> 
                                                                <option value="normal" data-icon="fa fa-battery-empty">Keiner</option>
                                                                <option value="medium" data-icon="fa fa-battery-half">Medium</option>
                                                                <option value="high" data-icon="fa fa-battery-full">Hoch</option>
                                                                <option value="very high" data-icon="fa fa-fire warning">Sehr Wichtig</option>
                                                                
                                                            </select>
                                                        </div>  

                                                         <div class="col-md-6 col-12  ">
                                                            <label for="date_type">Wiederholung</label>
                                                            <select name="date_type" id="date_type" class="form-control"   style="width:100%">  
                                                                <option >Wählen</option>
                                                            <option value="day" >Ganzer Tag</option>
                                                                <option value="week" >7 Tage (Eine Woche)</option>
                                                                <option value="daily" >Täglich</option>
                                                                <option value="monthly" >Monatlich</option>
                                                            </select>
                                                        </div> 

                                                        <div class="col-md-6 col-12 from_day ">
                                                            <label for="end_time">Von</label>
                                                            <select name="from_day" id="from_day" class="form-control" style="width:100%">  
                                                                <option value="monday">Montag</option> 
                                                                <option value="tuesday">Dienstag</option> 
                                                                <option value="wednesday">Mittwoch</option> 
                                                                <option value="thursday">Donnerstag</option> 
                                                                <option value="friday">Freitag</option> 
                                                                <option value="saturday">Samstag</option> 
                                                                <option value="sunday">Sonntag</option> 
                                                            </select>
                                                        </div>


                                                        <div class="col-md-6 col-12 to_day ">
                                                            <label for="end_time">Zu</label>
                                                            <select name="to_day" id="to_day" class="form-control" style="width:100%">  
                                                                <option value="monday">Montag</option> 
                                                                <option value="tuesday">Dienstag</option> 
                                                                <option value="wednesday">Mittwoch</option> 
                                                                <option value="thursday">Donnerstag</option> 
                                                                <option value="friday">Freitag</option> 
                                                                <option value="saturday">Samstag</option> 
                                                                <option value="sunday">Sonntag</option> 
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 col-12 from_month ">
                                                            <label for="month">Von (Monat)</label>
                                                            <select name="from_month" id="from_month" class="form-control" style="width:100%">  
                                                                <option value="january">Januar</option> 
                                                                <option value="february">Februar</option> 
                                                                <option value="march">März</option> 
                                                                <option value="april">April</option> 
                                                                <option value="may">Mai</option> 
                                                                <option value="june">Juni</option> 
                                                                <option value="july">Juli</option> 
                                                                <option value="august">August</option> 
                                                                <option value="september">September</option> 
                                                                <option value="october">Oktober</option> 
                                                                <option value="november">November</option> 
                                                                <option value="december">Dezember</option> 
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6 col-12 to_month ">
                                                            <label for="month">Zu (Monat)</label>
                                                            <select name="to_month" id="to_month" class="form-control" style="width:100%">  
                                                                <option value="january">Januar</option> 
                                                                <option value="february">Februar</option> 
                                                                <option value="march">März</option> 
                                                                <option value="april">April</option> 
                                                                <option value="may">Mai</option> 
                                                                <option value="june">Juni</option> 
                                                                <option value="july">Juli</option> 
                                                                <option value="august">August</option> 
                                                                <option value="september">September</option> 
                                                                <option value="october">Oktober</option> 
                                                                <option value="november">November</option> 
                                                                <option value="december">Dezember</option> 
                                                            </select>
                                                        </div> 
                                                </div>   
                                            </div>
                                        </div>
                                    </div>

                                <div class="modal-footer" style="border:0; background:#f1f1f1 !important;"> 
                                  
                                    <button type="button" class="btn btn-danger mr-1 waves-effect waves-light btn-sm close_task_window"><i class="feather icon-x"></i> abbrechen</button>
                                    <button type="button" class="btn btn-primary update-task btn-sm"><i class="feather icon-save"></i> speichern</button> 
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

 
 
@endsection