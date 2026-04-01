
@extends('admin.layouts.app')

@section('title') 
Mitarbeiter
@endsection

@section('style') 
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

    <style>
    .color-strip {
        border: none !important;
        height: 13px !important;
        background-color: var(--primary-color);
    }

        .color-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .color-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #ccc;
            background-color: #ffffff;
            display: inline-block;
        }

        .color-select {
            width: 260px;
            padding: 8px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .color-strip {
            width: 100%;
            height: 50px;
            margin-top: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
        }
</style>

@endsection
@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">KUNDE INFORMATION</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a>
                                    </li>
                                     <li class="breadcrumb-item"><a href="{{ url('/emp') }}">MITARBEITER</a>
                                </li> 
                                    <li class="breadcrumb-item"><a href="#">Details</a>
                                    </li> 
                                </ol>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="content-body">  
                <div class="row">
                    <!-- left menu section -->
                    <div class="col-md-2 mb-2 mb-0 md-0 p-0">
                        <div class="col-md-12 mb-2 mb-md-0 p-0">
                            <div class="card" style="height: 329.656px;">
                                <div class="card-header mx-auto pb-0">
                                    <div class="row m-0">
                                        <div class="col-sm-12 text-center">
                                            <p style="font-weight: bold;  font-size: 18px; ">{{ $data->name }} {{ $data->midname }} {{ $data->lastname }}</p>
                                        </div> 
                                    </div>
                                </div>
                                <div class="card-content"> 
                                    <div class="card-body text-center mx-auto"> 
                                        <div class="avatar avatar-xl">
                                            @if($data->image)
                                            <img class="img-fluid" src="{{ asset('images/employee/'.$data->image)}}" alt="{{ $data->name }}">
                                            @else
                                                    @if($data->gender=="Male")
                                                
                                                    <img class="img-fluid" src="{{ asset('images/gender/male.png')}}" alt="{{ $data->name }}">
                                                    @else
                                                    <img class="img-fluid" src="{{ asset('images/gender/female.png')}}" alt="{{ $data->name }}">
                                                    @endif
                                            @endif
                                        </div>
                                        <div class="mt-2" style="display: flex;  justify-content: space-evenly;"> 
                                            <div class="followers">
                                                <p class="font-weight-bold font-medium-2 mb-0">
                                                    @if($data->status == 'holiday')
                                                    Urlaub
                                                    @else
                                                    Aktiv
                                                    @endif
                                                </p> 
                                                @if($data->status_msg)
                                                <span class="">{{ $data->status_msg}}</span>
                                                @endif
                                            </div> 
                                        </div>
                                        <button type="button" class="btn btn-primary btn-block mt-2 mb-2 waves-effect waves-light"  data-toggle="modal" data-target="#picture" >
                                        <i class="feather icon-image"></i> Foto</button> 
                                        <div class="modal fade text-left" id="picture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel1">Profilbild</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form novalidate method="post" action="{{ action('App\Http\Controllers\EmployeeController@profile_picture')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <fieldset> 
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                            <code><strong>Hinweis: Die Größe des Bildes wirkt sich auf die Leistung der Datenbank aus</strong></code>
                                                                            </label>
                                                                            <input type="hidden" name="id" value="{{ request()->id }}">
                                                                            <input type="file" class="form-control"  name="image"  required>
                                                                            @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit"  class="btn btn-primary">Einreichen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>     
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                        @php
                            $active = session('active_tab', 'profile'); // Use session instead of old()
                        @endphp

                        <div class="col-12">
                            <ul class="nav nav-pills flex-column mt-md-0 mt-1">
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'profile' ? 'active' : '' }}" id="account-pill-general" data-toggle="pill" href="#account-vertical-general" aria-expanded="true">
                                        <i class="feather icon-globe mr-50 font-medium-3"></i>
                                        General
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'department' ? 'active' : '' }}" id="account-pill-department" data-toggle="pill" href="#account-vertical-department" aria-expanded="false">
                                        <i class="fa fa-tree mr-50 font-medium-3"></i>
                                        Abteilung & Jobs
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a
                                        class="nav-link d-flex py-75 {{ $active == 'time-management' ? 'active' : '' }}"
                                        id="account-pill-time-management"
                                        data-toggle="pill"
                                        href="#account-vertical-time-management"
                                        aria-expanded="false"
                                    >
                                        <i class="fa fa-clock-o mr-50 font-medium-3"></i>
                                        Arbeitszeit
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'location' ? 'active' : '' }}" id="account-pill-location" data-toggle="pill" href="#account-vertical-location" aria-expanded="false">
                                        <i class="fa fa-map-pin mr-50 font-medium-3"></i>
                                        Standortdienst
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'address' ? 'active' : '' }}" id="account-pill-password" data-toggle="pill" href="#account-vertical-password" aria-expanded="false">
                                        <i class="fa fa-address-card-o mr-50 font-medium-3"></i>
                                        Kontakt & Adress
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'qualification' ? 'active' : '' }}" id="account-pill-info" data-toggle="pill" href="#account-vertical-info" aria-expanded="false">
                                        <i class="fa fa-graduation-cap mr-50 font-medium-3"></i>
                                        Qualifikation
                                    </a>
                                </li> 
                                 <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'skill' ? 'active' : '' }}" id="skill-tabs" data-toggle="pill" href="#skill-vertical-tab" aria-expanded="false">
                                        <i class="fa fa-calendar-times-o mr-50 font-medium-3"></i>
                                        Fähigkeiten 
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'recurring' ? 'active' : '' }}" id="recurring-tabs" data-toggle="pill" href="#recurring-vertical-tab" aria-expanded="false">
                                        <i class="fa fa-calendar-times-o mr-50 font-medium-3"></i>
                                       Wiederkehrend
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'leave' ? 'active' : '' }}" id="holiday-tabs" data-toggle="pill" href="#holiday-vertical-tab" aria-expanded="false">
                                        <i class="fa fa-calendar-times-o mr-50 font-medium-3"></i>
                                       Urlaub
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'sick' ? 'active' : '' }}" id="sick-tabs" data-toggle="pill" href="#sick-vertical-tab" aria-expanded="false">
                                        <i class="fa fa-thermometer-full mr-50 font-medium-3"></i>
                                        Krank 
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'handover' ? 'active' : '' }}" id="account-pill-notifications" data-toggle="pill" href="#account-vertical-notifications" aria-expanded="false">
                                        <i class="feather icon-message-circle mr-50 font-medium-3"></i>
                                        Gegenstände übergeben
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'license' ? 'active' : '' }}" id="account-pill-car" data-toggle="pill" href="#account-vertical-car" aria-expanded="false">
                                        <i class="fa fa-car mr-50 font-medium-3"></i>
                                        Mitarbeiterlizenz
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'cloth' ? 'active' : '' }}" id="account-pill-cloth" data-toggle="pill" href="#account-vertical-cloth" aria-expanded="false">
                                        <i class="fa fa-shirtsinbulk mr-50 font-medium-3"></i>
                                        Kleidungsgröße
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link d-flex py-75 {{ $active == 'document' ? 'active' : '' }}" id="account-pill-document" data-toggle="pill" href="#account-vertical-document" aria-expanded="false">
                                        <i class="fa fa-upload mr-50 font-medium-3"></i>
                                        Dukoment & File
                                    </a>
                                </li>

                                 
                            </ul>
                        </div> 
                    </div>
                    
                    <!-- right content section -->
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane {{ $active == 'profile' ? 'active show' : '' }}" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                            @include('admin.employee.employee.create.profile')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'department' ? 'active show' : '' }}" id="account-vertical-department" role="tabpanel" aria-labelledby="account-pill-department" aria-expanded="false">
                                            @include('admin.employee.employee.create.department')
                                        </div>

                                        <div class="tab-pane fade {{ $active == 'time-management' ? 'active show' : '' }}"
                                            id="account-vertical-time-management"
                                            role="tabpanel"
                                            aria-labelledby="account-pill-time-management"
                                            aria-expanded="false">

                                            @php $employeeId = $data->id; @endphp
                                            @include('admin.employee.employee.create.time_management', ['employeeId' => $employeeId])
                                        </div>


                                        <div class="tab-pane fade {{ $active == 'location' ? 'active show' : '' }}" id="account-vertical-location" role="tabpanel" aria-labelledby="account-pill-location" aria-expanded="false">
                                            @include('admin.employee.employee.create.location')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'address' ? 'active show' : '' }}" id="account-vertical-password" role="tabpanel" aria-labelledby="account-pill-password" aria-expanded="false">
                                            @include('admin.employee.employee.create.address')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'qualification' ? 'active show' : '' }}" id="account-vertical-info" role="tabpanel" aria-labelledby="account-pill-info" aria-expanded="false">
                                            @include('admin.employee.employee.create.qualification')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'skills' ? 'active show' : '' }}" id="skill-vertical-tab" role="tabpanel" aria-labelledby="skill-tabs" aria-expanded="false">
                                            @include('admin.employee.employee.create.skills')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'leave' ? 'active show' : '' }}" id="holiday-vertical-tab" role="tabpanel" aria-labelledby="holiday-tabs" aria-expanded="false">
                                            @include('admin.employee.employee.create.leave')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'sick' ? 'active show' : '' }}" id="sick-vertical-tab" role="tabpanel" aria-labelledby="sick-tabs" aria-expanded="false">
                                            @include('admin.employee.employee.create.sick')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'recurring' ? 'active show' : '' }}" id="recurring-vertical-tab" role="tabpanel" aria-labelledby="recurring-tabs" aria-expanded="false">
                                            @include('admin.employee.employee.create.recurring')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'handover' ? 'active show' : '' }}" id="account-vertical-notifications" role="tabpanel" aria-labelledby="account-pill-notifications" aria-expanded="false">
                                            @include('admin.employee.employee.create.handover')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'license' ? 'active show' : '' }}" id="account-vertical-car" role="tabpanel" aria-labelledby="account-pill-car" aria-expanded="false">
                                            @include('admin.employee.employee.create.license')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'cloth' ? 'active show' : '' }}" id="account-vertical-cloth" role="tabpanel" aria-labelledby="account-pill-cloth" aria-expanded="false">
                                            @include('admin.employee.employee.create.cloth')
                                        </div>
                                        <div class="tab-pane fade {{ $active == 'document' ? 'active show' : '' }}" id="account-vertical-document" role="tabpanel" aria-labelledby="account-pill-document" aria-expanded="false">
                                            @include('admin.employee.employee.create.documents')
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
    
 
@section('script')
  <!-- BEGIN: Page Vendor JS-->
  <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
  <script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
  <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
  <script src="{{ asset('app-assets/vendors/js/extensions/dropzone.min.js')}}"></script>
  <!-- END: Page Vendor JS-->
  <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

  <!-- BEGIN: Theme JS-->
  <script src="{{ asset('app-assets/js/core/app-menu.js')}}"></script>
  <script src="{{ asset('app-assets/js/core/app.js')}}"></script>
  <script src="{{ asset('app-assets/js/scripts/components.js')}}"></script>
  <!-- END: Theme JS-->

  <!-- BEGIN: Page JS-->
  <script src="{{ asset('app-assets/js/scripts/pages/account-setting.js')}}"></script>
  <script src="{{ asset('app-assets/js/scripts/cards/card-statistics.js') }}"></script>

  <script>
      $(document).ready(function() {
          $('#department').select2();
          $('#branch').select2();
          $('#grade').select2();
          $('#edit_grade').select2();
          $('#supervisor').select2();
          $('#leave_duration').select2({
                tags: true
            });
      });

      
  </script>

 

<script>
 $(document).ready(function(){
    // ✅ Set global Toastr options
    toastr.options = {
        closeButton: true,         // Show close button
        progressBar: true,         // Show progress bar
        timeOut: 5000,             // Duration in milliseconds (5 seconds)
        extendedTimeOut: 2000,     // Extra time when hovered
        showMethod: "fadeIn",      // Show animation
        hideMethod: "fadeOut",     // Hide animation
        positionClass: "toast-top-right" // Change position if needed
    };

    @if(Session::has('update_msg'))
        toastr.success("{{ session('update_msg') }}");
    @endif

    @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
    @endif

    @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
    @endif
});


</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let activeTab = "{{ session('active_tab', 'profile') }}";

        if (activeTab) {
            $('.nav-link').removeClass('active'); // Remove active class from all tabs
            $('.tab-pane').removeClass('active show'); // Remove active class from tab content

            let selectedTab = $('#account-pill-' + activeTab);
            let selectedContent = $('#account-vertical-' + activeTab);

            if (selectedTab.length && selectedContent.length) {
                selectedTab.addClass('active'); // Highlight the correct tab
                selectedContent.addClass('active show'); // Show corresponding content
            }
        }
    });
</script>



   
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const colors =  [
                    { "hex": "006139", "name": "Dunkelgrün" }, 
                    { "hex": "009640", "name": "Grün" }, 
                    { "hex": "8abd24", "name": "Hellgrün" }, 
                    { "hex": "838b2d", "name": "Oliv" }, 
                    { "hex": "583c7a", "name": "Lila" }, 
                    { "hex": "891e82", "name": "Dunkellila" }, 
                    { "hex": "d5007f", "name": "Magenta" }, 
                    { "hex": "e78cba", "name": "Rosa" }, 
                    { "hex": "cd1719", "name": "Rot" }, 
                    { "hex": "e55c70", "name": "Hellrot" }, 
                    { "hex": "e9500e", "name": "Orange" }, 
                    { "hex": "ef9500", "name": "Hellorange" }, 
                    { "hex": "283583", "name": "Dunkelblau" }, 
                    { "hex": "0070ba", "name": "Blau" }, 
                    { "hex": "009fe3", "name": "Himmelblau" }, 
                    { "hex": "71cbf4", "name": "Hellblau" }, 
                    { "hex": "7d91c9", "name": "Grau-Blau" }, 
                    { "hex": "009bb1", "name": "Türkis" },

                    // Additional Colors:
                    { "hex": "4b5320", "name": "Moosgrün" }, 
                    { "hex": "006400", "name": "Dunkles Waldgrün" }, 
                    { "hex": "a3d900", "name": "Neon-Grün" }, 
                    { "hex": "ff1493", "name": "Neonpink" }, 
                    { "hex": "800000", "name": "Kastanienbraun" }, 
                    { "hex": "8b0000", "name": "Dunkelrot" }, 
                    { "hex": "ff4500", "name": "Feuerrot" }, 
                    { "hex": "ff8c00", "name": "Dunkelorange" }, 
                    { "hex": "ffd700", "name": "Gold" }, 
                    { "hex": "ffff00", "name": "Gelb" }, 
                    { "hex": "c0c0c0", "name": "Silber" }, 
                    { "hex": "808080", "name": "Grau" }, 
                    { "hex": "000000", "name": "Schwarz" }, 
                    { "hex": "ffffff", "name": "Weiß" }, 
                    { "hex": "8b4513", "name": "Schokoladenbraun" }, 
                    { "hex": "a52a2a", "name": "Braun" }, 
                    { "hex": "ffdab9", "name": "Pfirsich" }, 
                    { "hex": "40e0d0", "name": "Türkisblau" }
                ];

            const colorPicker = document.getElementById("colorPicker");
            const colorStrip = document.getElementById("colorStrip");
            const colorIcon = document.getElementById("colorIcon");

            if (!colorPicker || !colorStrip || !colorIcon) {
                console.error("Required elements not found!");
                return;
            }

            // Populate the select options
            colors.forEach(color => {
                const option = document.createElement("option");
                option.value = `#${color.hex}`;
                option.textContent = color.name;
                option.style.backgroundColor = `#${color.hex}`;
                option.style.color = "#fff";
                option.style.padding = "5px";
                option.style.fontWeight = "bold";
                colorPicker.appendChild(option);
            });

            // Change background color on selection
            colorPicker.addEventListener("change", function() {
                if (this.value) {
                    colorStrip.style.backgroundColor = this.value;
                    colorIcon.style.backgroundColor = this.value;
                }
            });
        });
    </script>

 

@endsection