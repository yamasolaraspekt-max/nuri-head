@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@push('style')
<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">


<style>
     
body {
    margin: 0;
}
 
.sb-title {
    position: relative;
    top: -12px;
    font-family: Roboto, sans-serif;
    font-weight: 500;
}

.sb-title-icon {
    position: relative;
    top: -5px;
}

.card-container {
    display: flex;
    height: 500px;
    width: 600px;
}

.panel {
    background: white;
    width: 300px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
}

.half-input-container {
    display: flex;
    justify-content: space-between;
}

.half-input {
    max-width: 120px;
}

.map {
    width: 300px;
}

h2 {
    margin: 0;
    font-family: Roboto, sans-serif;
}

input {
    height: 30px;
}

input {
    border: 0;
    border-bottom: 1px solid black;
    font-size: 14px;
    font-family: Roboto, sans-serif;
    font-style: normal;
    font-weight: normal;
}

input:focus::placeholder {
    color: white;
}

 

body {
    margin: 0;
}

input[type="text"].textbox {
    padding-right: 30px;
    /* Adjust padding to accommodate the pink rectangle */
    border-radius: 0;
    /* Ensure border-radius is set to 0 */
}

h4 {
    font-size: 1rem !important;
}

h3 {

    font-size: 1rem !important;
}

.title {
    font-size: 10px !important;
    font-weight: bold !important;
}

 
 
 
.selected_heart {
    background: #95c11f !important;
    color:white; 
}

.btns-primary {
    background: #cfe09b !important;
}

.menu-button {
    color: #828282 !important;
}

/* Customer Product Selection: End */



.products {
    cursor: pointer;
}

.sb-title {
    position: relative;
    top: -12px;
    font-family: Roboto, sans-serif;
    font-weight: 500;
}

.sb-title-icon {
    position: relative;
    top: -5px;
}

.card-container {
    display: flex;
    height: 500px;
    width: 600px;
}

.panel {
    background: white;
    width: 300px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
}

.half-input-container {
    display: flex;
    justify-content: space-between;
}

.half-input {
    max-width: 120px;
}

.map {
    width: 300px;
}

h2 {
    margin: 0;
    font-family: Roboto, sans-serif;
}

input {
    height: 30px;
}

input {
    border: 0;
    border-bottom: 1px solid black;
    font-size: 14px;
    font-family: Roboto, sans-serif;
    font-style: normal;
    font-weight: normal;
}

input:focus::placeholder {
    color: white;
}

.star-rating {
    font-size: 2rem;
    cursor: pointer;
}

.star {
    color: #ccc;
}

.star.selected,
.star.hovered {
    color: #9cc136;
}

.flex_me {
    display: flex !important;
    flex-wrap: nowrap;
    align-items: center;
}

.img-flag {
    width: 60px !important;
    top: 200px;
}

#roof {
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    align-items: center;
}

#select2-selection__rendered span {
    display: flex !important;
    flex-wrap: nowrap !important;
    justify-content: space-between !important;
    align-items: center !important;
}

.select2-selection { 
    /* width: 100% !important;  */
    /* height: 40px !important; */
    background: transparent !important;
    /* color: white; */
    box-shadow: none !important;
    /* border:1px solid #73b1d4 !important; */
    border:0 !important;
    padding:0 !important;
    text-align: left !important;
    font-size: 11px !important;
}

 

.custom-control-label::before,
.custom-control-label::after {
    width: 1.5rem !important;
    height: 1.5rem !important;
    top: 0.03rem !important;
    border: 3px solid #73b1d4 !important;
    border-radius: 50% !important;
}

.custom-control-label {
    font-size: 16px !important;
}

.d-inline-block {
    width: 158px !important;
}

.list-unstyled {
    display: flex;
    flex-wrap: nowrap;
}

#submit_form {
    float: right;
    position: fixed;
    top: 819px;
    right: 9px;
    z-index: 100;
}

.form-reset {
    margin: 0;
    padding: 0;
    border: none;
}

.form-reset input,
.form-reset select,
.form-reset textarea,
.form-reset button {
    margin: 0;
    padding: 0;
    border: none;
    outline: none;
    box-shadow: none;
    background: none;
    font: inherit;
    color: inherit;
}

.form-reset input[type="checkbox"],
.form-reset input[type="radio"] {
    display: inline-block;
    width: auto;
    height: auto;
}

.form-reset button {
    background: none;
    cursor: pointer;
}

.buttons {
    width: 25px !important;
    height: 25px !important;
    padding: 0 !important;
}

.icons {
    font-size: 15px !important;
}

/* Textbox validation style: start */
.textbox-container {
    position: relative;
}

.indicator {
    width: 15px;
    height: 100%;
    background-color: #e50056;
    display: inline-block;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    display: none;
}

.textbox-container.empty .indicator {
    display: inline-block;
}

/* Textbox Validation: end */


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


.checklist-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.checklist-container h4 {
    color: #333;
    margin-bottom: 20px;
}

.checklist-container .form-check {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.checklist-container .form-check-label {
    font-weight: 500;
    flex-grow: 1;
    margin-right: 15px;
}

.vs-checkbox-con {
    display: flex;
    align-items: center;
}

.vs-checkbox {
    margin-right: 10px;
}

.vs-icon {
    font-size: 1.5em;
}

.form-check {
    display: flex;
    justify-content: flex-start;
}

.custom-control-prev-icon,
.custom-control-next-icon {
    background-color: #e50056 !important;
}
.card-header {
        border: 0;
    background: transparent;
}
 

.card{
    width: 100%;
} 
span {
    font-size:13px !important;
}
 
</style>

<style>
    .card-fixed {
      width: 200px;
      height: 100vh;
      position: sticky;
      top: 0;
      background-color: #f1f1f1;
      overflow-y: auto;

      padding: 10px;
    }
    .card-scrollable {
      flex: 1;
      height: 100vh;
      overflow-y: auto;
      background-color: transparent;
  
      padding: 10px;
    }

     .text {
            border: 0;
            border-right: 1px solid gray;
            border-radius: 0;
            }
    .card {
        box-shadow: 0 0 !important;
        background: #f1f1f1 !important;
    }
     .map_section {
        display: none;
        position: absolute !important;
        right: 23px !important; 
        top: 0 !important;
        background: white !important;
        padding-top: 20px !important;
    }

      .energy-header:hover {
        background: #8fc63f  !important;
    }
</style>


<style>
    .wizard-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .wizard-step {
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #666;
        font-weight: normal;
    }

    .wizard-step img {
        width: 90px;
        margin-bottom: 5px;
        transition: transform 0.3s ease;
    }

    .wizard-step.active {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step.active .wizard-progress-count {
        color: #8fc73e;
        font-weight: bold;
    }

    .wizard-step:hover img {
        transform: scale(1.05);
    }

    .wizard-progress-count {
        display: block;
        font-size: 0.8rem;
        color: #aaa;
        font-weight: normal;
    }

    .tab-pane .row {
        padding: 0 20px;
    }
</style> 
@endpush
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
         <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDE</h2>
                        <div class="breadcrumb-wrapper col-12">
                               <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                </li>
                                 <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Bearbeiten</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                      <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" id="show_map"><i class="feather icon-map"></i></button>
                     
                </div>
            </div>
        </div>
        <div class="content-body">
         <form class="form form-horizontal custom-file-upload" method="post" id="customer_form"
                action="{{ action('App\Http\Controllers\NewLeadsController@object_store') }}"
                    enctype="multipart/form-data">
                    @csrf
                      <input type="hidden" name="lead_id" value="{{ $leads->id }}">

                    <input type="hidden" name="alternative_id" value="{{ request()->alternative }}">
                    @php
                        $imagePath = 'images/employee/';
                        $male = 'images/gender/male.png';
                        $female = 'images/gender/female.png';
                    @endphp 
               
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height"> 
                    <div class="col-xl-4 col-sm-4 col-md-4   ">
                        <div class="row">  
                            <div class="col-4">
                                <h2 class="content-header-title float-left primary ">KUNDENDATEN</h2>
                            </div>           
                            <div class="col-12">
                                    <hr>
                                </div>  
                            <input type="hidden" value="{{$leads->id}}" name="lead_id"> 
                            <div class="card">
                                <div class="row p-1 mb-2">
                                    <div class="col-6"> 
                                        <div class="col-12 mb-1"> 
                                            <div class="col-md-12">
                                                <span class="font-bold">Kunde-typ: </span> 
                                                {{ $leads->customer_type }}
                                            </div> 
                                        </div>

                                        <!-- Additional form fields go here -->
                                        <div class="col-12 mb-1"> 
                                            <div class="col-md-12">
                                                <span class="font-bold">Kunde-Nr:</span> 
                                                {{ $leads->customer_no }}
                                            </div> 
                                        </div>
                                        <div class="col-12 mb-1 " id="firma-container"> 
                                            <div class="col-md-12">
                                                <span class="font-bold">Firma:</span> 
                                                {{ $leads->firma ?? Null }}
                                            </div> 
                                        </div> 
                                        <div class="col-12 mb-1"> 
                                            <div class="col-md-12">
                                                <span  class="font-bold">Kunde</span> 
                                                    {{ $leads->title }}   {{ $leads->name }} {{ $leads->lastname }}
                                            </div> 
                                        </div>

                                        <div class="col-12 mb-1"> 
                                            <div class="col-md-12">
                                                <span class="font-bold">Adresse:</span> 
                                                {{ $leads->street }}  {{ $leads->postcode }},  {{ $leads->city }}
                                            </div> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-6">
                                        <div class="row">
                                            
                                            <div class="col-12"> 
                                                <div class="col-md-12">
                                                    <span class="font-bold">Quelle</span> 
                                                    {{ $leads->source }}
                                                </div> 
                                            </div>
                                            <div class="col-12"> 
                                                <div class="col-md-12">
                                                    <span class="font-bold">Info:</span> 
                                                    {{ $leads->info }}
                                                </div> 
                                            </div> 
                                            @php
                                            $user_name = DB::table('employees')
                                            ->join('users', 'users.name', '=', 'employees.id')
                                            ->select('employees.name', 'employees.lastname')
                                            ->where('users.name', '=', $leads->contact_person)
                                            ->first()
                                            @endphp

                                            @php
                                            $employee = DB::table('employees')
                                            ->select('employees.id','employees.name', 'employees.lastname')
                                            ->get()
                                            @endphp
                                            <div class="col-12"> 
                                                    <div class="col-md-12">
                                                        <span class="font-bold">erste Kontaktperson</span> 
                                                        @if($user_name)
                                                        {{ $user_name->name }} {{ $user_name->lastname }}
                                                        @else
                                                        <div class="alert alert-danger" role="alert">
                                                            <h4 class="alert-heading">Info</h4>
                                                            <p class="mb-0">
                                                                There is no Employee in the system!
                                                            </p>
                                                        </div>
                                                        @endif 
                                                    </div> 
                                            </div>

                                                <div class="col-12 mb-1 p-2"> 
                                                <div class="col-md-12">
                                                    <span class="font-bold">Kontakt:</span> 
                                                    <p style="margin:0; line-height:1px" class="mb-1"><i class="feather icon-phone-call" ></i> {{ $leads->telephone }}</p>
                                                    <p style="margin:0; line-height:1px" class="mb-1"><i class="feather icon-smartphone" ></i> {{ $leads->phone }} 
                                                    <p style="margin:0; line-height:1px"><i class="feather icon-mail" ></i> {{ $leads->email }}</p>
                                                </div> 
                                            </div>  
                                        </div> 
                                    </div> 
                                </div>
                            </div> 
                        </div> 
                    </div>

                     <div class="col-xl-8 col-sm-8 col-md-8  ">
                        <div class="row">
                            <div class="col-6">
                                 <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Anfrage-Datum</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                          <input type="date" class="form-control text form-element" name="request_date" 
                                            value="{{ old('request_date', \Carbon\Carbon::today()->toDateString()) }}" />

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Objektname</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <input type="text" class="form-control text form-element" name="object_name" value="{{ old('object_name', 'Privathaus' )}}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Dringlichkeit</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <select name="periority" id="" class="form-control text form-element"> 
                                                <Option value="Normal"  >Normal</Option>
                                                <Option value="Dringend"  >Dringend</Option>
                                                <Option value="Sehr dringend" >Sehr dringend</Option>
                                            </select>
                                         </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Betrieb</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <select name="branch_id" id="" class="form-control text form-element">  
                                                @foreach ($branch as $br)
                                                <Option value="{{ $br->id }}"  >
                                                    {{ $br->branch }}
                                                </Option> 
                                                @endforeach
                                            </select>
                                         </div>
                                    </div>
                                </div>
                                  
                                <!-- Alternative Address Inputs --> 
                                <div class="col-12" id="street2s">
                                    <div class="form-group row form-element">
                                        <div class="col-md-4">
                                            <span>STR./NR./PLZ./ORT</span>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <input id="full_address2" type="text" class="form-control text form-element" placeholder="Adresse eingeben" name="full_address" value="{{ old('full_address2') }}">
                                            <input type="hidden" id="latitude-input2" name="latitude" value="{{ old('latitude') }}">
                                            <input type="hidden" id="longitude-input2" name="longitude" value="{{ old('longitude') }}">
                                            <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation" value="{{ old('elevation') }}">
                                            <input type="hidden" class="form-control text form-element" value="{{old('postcode')}}" name="postcode" id="postal_code-input2">
                                            <input type="hidden" class="form-control text form-element" value="{{old('city2')}}" name="city" id="locality-input2">
                                            <input type="hidden" class="form-control text form-element" value="{{old('street')}}" name="street" id="street-input2">

                                        </div>
                                    </div>
                                </div> 

                                <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Notizen</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <textarea name="info" id="" cols="30" rows="3" class="form-control">
                                                            {{ old('note') }}
                                                </textarea>
                                         </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-6">
                                <div class="col-12">
                                    <!-- {{-- Map Start --}} -->
                                    <div class="card"> 
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <div class="map" id="gmp-map" style="width: 100%; position: relative; overflow: hidden; height: 356px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- {{-- Map End --}} -->
                                </div>
                                <div class="col-lg-3">
                                    <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn"  >
                                        <i class="feather icon-camera"></i> Screenshot
                                    </a> 
                                </div> 
                                <div id="screenshot-preview"></div>

                                <!-- Hidden file input to store the screenshot data -->
                                <input type="file" class="d-none" id="screenshot-file-input" name="screenshot_file" class="form-control text" /> 
                               
                            </div>
                        </div>
                    </div>

                </div>
                    
                    <div class="row mt-2">
                        <div class="col-xl-12 col-sm-12 col-md-12"> 
                            <div class="card mb-1 " style=" background: #f1f1f1;   " >
                                <div class="card-header  d-flex justify-content-between align-items-center mb-2 mt-2"  style=" background: #f1f1f1;   border-bottom: 2px solid #569ad8;" > 
                                    <h2 class="content-header-title float-left primary ">PRODUKT & DIENSTLEISTUNG</h2>

                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light" id="addRow">
                                        <i class="feather icon-plus"></i> 
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0" id="inquiryProductTable">
                                            <thead class="thead-light text-center">
                                                <tr>
                                                    <th>
                                                        <img src="{{ asset('images/icons/produkt.svg') }}" alt="" style="width: 62px;"><br>
                                                        Produkt
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/dienstleistung.svg') }}" alt="" style="width: 62px;"><br>
                                                        Dienstleistung
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/abteilung.svg') }}" alt="" style="width: 62px;"><br>
                                                        Abteilung
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                                        Innendienst
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                                        Außendienst
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/zaehler.svg') }}" alt="" style="width: 56px;"><br>
                                                        Realisierungszeit
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/kaufinteresse.svg') }}" alt="" style="width: 56px;"><br>
                                                        Interesse
                                                    </th>
                                                    <th>
                                                        <img src="{{ asset('images/icons/aktion.svg') }}" alt="" style="width: 56px;"><br>
                                                        Aktion
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <!-- JS will append rows here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

 
                  

                </div>
                <div class="col-md-2" style=" position: fixed; top: 85%; right: 20px; ">  
                    <div id="status-icon" class="text-right mt-3">
                        <!-- Status icon will be displayed here -->
                    </div>
                    <div class="button">
                      <button type="submit" class="btn btn-primary round mr-1 mb-1 waves-effect waves-light float-right"><i class="feather icon-arrow-right"></i> Nächste</button>
                    </div>     
                </div>
            </section>

          
        </form>

            <!-- // Basic Horizontal form layout section end -->
        </div>
    </div>
</div>
</div>
<!-- END: Content-->

@endsection

@section('script')

<!-- selecting Product with heart:start  -->
     
 
  


 <!-- Map Section: start -->
  <script>
    $(document).ready(function () {
    // Show the map section when "Kunde Ort Karte" is clicked
    $('#show_map').on('click', function (e) {
        e.preventDefault(); // Prevent default action of the anchor tag
        $('.map_section').show(); // Show the map section
    });

    // Hide the map section when the "Schließen" button is clicked
    $(document).on('click', '.close-map', function () {
        $('.map_section').hide(); // Hide the map section
    });
});

  </script>
   

<!-- {{-- Star slider Script --}} -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starRatings = document.querySelectorAll('.star-rating');

        starRatings.forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            const ratingValue = rating.dataset.rating;
            updateStars(rating, ratingValue - 1); // Initialize stars based on the initial rating value

            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    rating.dataset.rating = index + 1;
                    updateStars(rating, index);
                    updateInput(rating);
                });

                star.addEventListener('mouseover', () => {
                    highlightStars(rating, index);
                });

                star.addEventListener('mouseout', () => {
                    resetStars(rating);
                });
            });
        });

        function updateStars(rating, index) {
            const stars = rating.querySelectorAll('.star');
            stars.forEach((star, i) => {
                if (i <= index) {
                    star.classList.add('selected_star');
                    star.classList.remove('hovered');
                } else {
                    star.classList.remove('selected');
                    star.classList.remove('hovered');
                }
            });
        }

        function highlightStars(rating, index) {
            const stars = rating.querySelectorAll('.star');
            stars.forEach((star, i) => {
                if (i <= index) {
                    star.classList.add('hovered');
                } else {
                    star.classList.remove('hovered');
                }
            });
        }

        function resetStars(rating) {
            const ratingValue = rating.dataset.rating - 1;
            const stars = rating.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index <= ratingValue) {
                    star.classList.add('selected');
                    star.classList.remove('hovered');
                } else {
                    star.classList.remove('selected');
                    star.classList.remove('hovered');
                }
            });
        }

        function updateInput(rating) {
            const category = rating.dataset.category;
            const ratingValue = rating.dataset.rating;
            document.querySelector(`input[name=${category}_rating]`).value = ratingValue;
        }
    });
</script>

<!-- Map and screenshots  -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initAutocomplete&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer>
</script>

     
<script>
    "use strict";

    let autocomplete1, autocomplete2;
    let elevationService;
    let placeSelected1 = false, placeSelected2 = false; // Track user selection
    let streetView, map; // Declare Street View and Map globally

    function initAutocomplete() {
        // ✅ Initialize the Google Map
        map = new google.maps.Map(document.getElementById("gmp-map"), {
            center: { lat: 50.1109, lng: 8.6821 }, // Default location (Frankfurt, Germany)
            zoom: 15,
            streetViewControl: true, // Enable Street View control
        });

        // ✅ Initialize Street View correctly
        streetView = map.getStreetView();

        // ✅ Initialize Elevation Service
        elevationService = new google.maps.ElevationService();

        // ✅ Initialize autocomplete for both address fields
        autocomplete1 = new google.maps.places.Autocomplete(document.getElementById('full_address'), {
            fields: ['address_components', 'geometry'],
            types: ['geocode']
        });

        autocomplete2 = new google.maps.places.Autocomplete(document.getElementById('full_address2'), {
            fields: ['address_components', 'geometry'],
            types: ['geocode']
        });

        // ✅ Primary Address Selection
        autocomplete1.addListener('place_changed', function () {
            handlePlaceChange(autocomplete1, '');
        });

        // ✅ Alternative Address Selection
        autocomplete2.addListener('place_changed', function () {
            handlePlaceChange(autocomplete2, '2');
        });

        // ✅ Prevent users from typing an address manually
        document.getElementById('full_address').addEventListener('blur', function () {
            if (!placeSelected1) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
                document.getElementById('full_address').value = '';
            }
        });

        document.getElementById('full_address2').addEventListener('blur', function () {
            if (!placeSelected2) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
                document.getElementById('full_address2').value = '';
            }
        });
    }

    function initMap() {
    initAutocomplete();
}

    function handlePlaceChange(autocompleteInstance, suffix) {
        let place = autocompleteInstance.getPlace();
        if (!place.geometry) {
            showWarning('Bitte wählen Sie eine Adresse aus der Liste aus.');
            document.getElementById(`full_address${suffix}`).value = '';
            return;
        }

        // ✅ Mark the selection
        if (suffix === '') {
            placeSelected1 = true;
        } else {
            placeSelected2 = true;
        }

        const location = place.geometry.location;
        const addressComponents = extractAddressComponents(place.address_components);

        const street = `${addressComponents.route} ${addressComponents.street_number}`.trim();
        const city = addressComponents.locality;
        const postcode = addressComponents.postal_code;
        const latitude = location.lat();
        const longitude = location.lng();

        // ✅ Assign extracted values to respective fields
        setValueIfExists(`street-input${suffix}`, street);
        setValueIfExists(`locality-input${suffix}`, city);
        setValueIfExists(`postal_code-input${suffix}`, postcode);
        setValueIfExists(`latitude-input${suffix}`, latitude);
        setValueIfExists(`longitude-input${suffix}`, longitude);

        // ✅ Get elevation
        getElevation(latitude, longitude, suffix);

        // ✅ Check for duplicate customers
        checkCustomer(street, postcode, latitude, longitude);
    }

    function extractAddressComponents(components) {
        let addressComponents = {
            street_number: '',
            route: '',
            locality: '',
            postal_code: ''
        };

        components.forEach(component => {
            const type = component.types[0];
            if (addressComponents.hasOwnProperty(type)) {
                addressComponents[type] = component.long_name;
            }
        });

        return addressComponents;
    }

    function setValueIfExists(elementId, value) {
        const input = document.getElementById(elementId);
        if (input) {
            input.value = value;
        }
    }

    function getElevation(lat, lng, suffix) {
            elevationService.getElevationForLocations(
                { locations: [{ lat, lng }] },
                (results, status) => {
                    if (status === "OK" && results[0]) {
                        setValueIfExists(`elevation-input${suffix}`, results[0].elevation.toFixed(2));
                    } else {
                        console.error("Elevation service failed:", status);
                    }
                }
            );
        }

    function checkCustomer(street, postcode, latitude, longitude) {
        fetch(`/check-new-leads/${encodeURIComponent(street)}/${postcode}/${latitude}/${longitude}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'duplicate' || data.status === 'neighbor') {
                    let tableHTML = `<table class="table table-bordered mt-3">
                            <thead>
                                <tr><th>Name</th><th>Nachname</th><th>Adresse</th><th>Radius (km)</th><th>Aktion</th></tr>
                            </thead><tbody>`;

                    if (data.status === 'duplicate') {
                        tableHTML += `<tr><td>${data.customer.name}</td>
                                <td>${data.customer.lastname}</td>
                                <td>${data.customer.full_address}</td>
                                <td>-</td>
                                <td><a href="/new_lead_profile/${data.customer.id}" class="btn btn-primary">Profil anzeigen</a></td></tr>`;
                    }

                    if (data.status === 'neighbor') {
                        data.customers.forEach(customer => {
                            tableHTML += `<tr><td>${customer.name}</td>
                                <td>${customer.lastname}</td>
                                <td>${customer.full_address}</td>
                                <td>${customer.distance.toFixed(2)}</td>
                                <td><a href="/new_lead_profile/${customer.id}" class="btn btn-primary">Profil anzeigen</a></td></tr>`;
                        });
                    }

                    tableHTML += `</tbody></table>`;

                    Swal.fire({
                        title: data.status === 'duplicate' ? 'Doppelter Eintrag gefunden!' : 'Nachbarn gefunden!',
                        html: tableHTML,
                        icon: 'info',
                        width: '70%',
                        showCloseButton: true,
                    });
                }
            })
            .catch(error => console.error('Fehler beim Überprüfen:', error));
    }

    function showWarning(message) {
        Swal.fire({ icon: 'warning', title: 'Fehler!', text: message });
    }

    // ✅ **Street View Screenshot Function**
    document.getElementById("screenshot-btn").addEventListener("click", function () {
        if (!streetView.getVisible()) {
            showWarning("Bitte wechseln Sie zur Straßenansicht, bevor Sie einen Screenshot machen können.");
            return;
        }

        let panoId = streetView.getPano();
        let pov = streetView.getPov();
        let heading = pov.heading;
        let pitch = pov.pitch;

        let streetViewImageUrl = `https://maps.googleapis.com/maps/api/streetview?size=600x300&pano=${panoId}&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

        fetch(streetViewImageUrl)
            .then(response => response.blob())
            .then(blob => {
                let file = new File([blob], "street_view_screenshot.jpg", { type: "image/jpeg" });
                let fileInput = document.getElementById("screenshot-file-input");
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                let imgPreview = document.createElement("img");
                imgPreview.src = URL.createObjectURL(blob);
                imgPreview.style = "max-width: 100%; margin-top: 10px; border: 1px solid #ccc;";
                document.getElementById("screenshot-preview").innerHTML = "";
                document.getElementById("screenshot-preview").appendChild(imgPreview);
            });
    });

    document.addEventListener("DOMContentLoaded", initAutocomplete);
</script>



<!-- SISSION AND SELECT2 -->

<script src="{{ asset('js/select2.min.js') }}"></script> 
<script>
    $(document).ready(function() {
        $('#product').select2();  
        $('.service_select').select2();  
        });
        
    $(document).ready(function() {
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif



        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script> 
  
 
<!-- Save operation: start  -->
  <script>
$(function () {
    $('#customer_form').on('submit', function (e) {
        e.preventDefault();

        const form     = this;
        const formData = new FormData(form);

        // Strukturierte Produktdaten sammeln
        const products = [];
        $('#inquiryProductTable tbody tr').each(function () {
            const index = $(this).data('index');
            if (!index) return;

            const product_id     = $(`.product-select[data-index="${index}"]`).val();
            const service_id     = $(`.service-select[data-index="${index}"]`).val();
            const department_id  = $(`.department-select[data-index="${index}"]`).val();
            const employee_id    = $(`.employee-select[data-index="${index}"]`).val();
            const field_employee = $(`.field-employee-select[data-index="${index}"]`).val();
            const interest       = $(`.interest-select[data-index="${index}"]`).val();
            const realization    = $(`.realization-select[data-index="${index}"]`).val();

            // Leere Zeilen ignorieren
            if (!product_id && !service_id && !department_id && !employee_id && !field_employee) {
                return;
            }

            products.push({
                product_id,
                service_id,
                department_id,
                employee_id,
                field_employee,
                interest,
                realization_time: realization
            });
        });

        formData.append('product', JSON.stringify(products));

        $.ajax({
            url:  $(form).attr('action'),
            type: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Erfolg',
                        text:  response.message,
                        icon:  'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Fehler',
                        text:  'Etwas ist schiefgelaufen: ' + (response.message || ''),
                        icon:  'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Ein Fehler ist aufgetreten.';
                Swal.fire({
                    title: 'Fehler',
                    text:  msg,
                    icon:  'error',
                    confirmButtonText: 'OK'
                });
                console.error(xhr.responseText);
            }
        });
    });
});
</script>

  <script>
const STAGE           = 'lead'; // <<< stage filter for backend
const IMG_EMPLOYEE    = "{{ asset('images/employee/') }}";
const CSRF_TOKEN      = '{{ csrf_token() }}';
const ROUTE_EMPLOYEES = '{{ route("inquiry.department.employees") }}';

$(function () {
    let rowIndex = 0;

    const SERVICES    = @json($services);
    const PRODUCTS    = @json($products);
    const DEPARTMENTS = @json($departments);

    // =========================================================
    // INIT EXISTING ROWS (EDIT)
    // =========================================================
    $('#inquiryProductTable tbody tr').each(function () {
        const $row = $(this);
        rowIndex++;
        if (!$row.attr('data-index')) {
            $row.attr('data-index', rowIndex);
        }
        initRow($row, { initialLoad: true });
    });

    // =========================================================
    // ADD NEW ROW (CREATE)
    // =========================================================
    $('#addRow').on('click', function () {
        const $lastRow = $('#inquiryProductTable tbody tr:last');

        if ($lastRow.length) {
            const missing = [];
            const lastProduct   = $lastRow.find('.product-select').val();
            const lastService   = $lastRow.find('.service-select').val();
            const lastDept      = $lastRow.find('.department-select').val();
            const lastEmployee  = $lastRow.find('.employee-select').val();

            if (!lastProduct)  missing.push('Produkt');
            if (!lastService)  missing.push('Dienstleistung');
            if (!lastDept)     missing.push('Abteilung');
            if (!lastEmployee) missing.push('Innendienst');

            if (missing.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'Zeile unvollständig',
                    html: `Bitte füllen Sie folgende Felder aus: <strong>${missing.join(', ')}</strong>`,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-danger' },
                    buttonsStyling: false
                });
                return;
            }
        }

        rowIndex++;

        const rowHtml = `
            <tr data-index="${rowIndex}" class="align-middle">
                <td>
                    <select name="product_id[]" class="form-control product-select">
                        <option value="">Produkt wählen</option>
                        ${PRODUCTS.map(p =>
                            `<option value="${p.id}" data-img="${p.image || ''}">${p.article_group}</option>`
                        ).join('')}
                    </select>
                </td>
                <td>
                    <select name="service_id[]" class="form-control service-select">
                        <option value="">Service wählen</option>
                    </select>
                </td>
                <td>
                    <select name="department_id[]" class="form-control department-select">
                        <option value="">Abteilung wählen</option>
                        ${DEPARTMENTS.map(d =>
                            `<option value="${d.id}">${d.department_name}</option>`
                        ).join('')}
                    </select>
                </td>
                <td>
                    <select name="employee_id[]" class="form-control employee-select">
                        <option value="">Innendienst wählen</option>
                    </select>
                </td>
                <td>
                    <select name="field_employee[]" class="form-control field-employee-select">
                        <option value="">Außendienst wählen</option>
                    </select>
                </td>
                <td>
                    <select name="interest[]" class="form-control interest-select">
                        <option value="intent">Kaufabsicht</option>
                        <option value="interest">Kaufinteresse</option>
                        <option value="option">Kaufoption</option>
                    </select>
                </td>
                <td>
                    <select name="realization_time[]" class="form-control realization-select">
                        <option value="">Bitte auswählen</option>
                        <option value="soon">Schnellstmöglich</option>
                        <option value="3">3 Monate</option>
                        <option value="6">6 Monate</option>
                        <option value="other">Sonstiges</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="Entfernen">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        const $row = $(rowHtml);
        $('#inquiryProductTable tbody').append($row);
        initRow($row, { initialLoad: false });
    });

    // =========================================================
    // INITIALISE ONE ROW
    // =========================================================
    function initRow($row, options = {}) {
        const initialLoad = options.initialLoad === true;

        const $product = $row.find('.product-select');
        const $service = $row.find('.service-select');
        const $dept    = $row.find('.department-select');
        const $emp     = $row.find('.employee-select');
        const $field   = $row.find('.field-employee-select');
        const $interest= $row.find('.interest-select');
        const $real    = $row.find('.realization-select');

        // Basic Select2
        $product.select2({ width: '100%' });
        $service.select2({ width: '100%' });
        $dept.select2({ width: '100%' });
        $interest.select2({ width: '100%' });
        $real.select2({ width: '100%' });

        // Employee Select2 with template
        [$emp, $field].forEach($s => {
            $s.select2({
                width: '100%',
                templateResult:   formatEmployee,
                templateSelection:formatEmployeeSelection,
                escapeMarkup:     m => m
            });
        });

        // Events
        $product.off('change.inquiry').on('change.inquiry', function () {
            console.log('product changed row', $row.data('index'), '->', $product.val());
            loadServicesForRow($row, { keepCurrent: false });
            loadEmployeesForRow($row, { autofill: true });
        });

        $service.off('change.inquiry').on('change.inquiry', function () {
            console.log('service changed row', $row.data('index'), '->', $service.val());
            loadEmployeesForRow($row, { autofill: false });
        });

        $dept.off('change.inquiry').on('change.inquiry', function () {
            console.log('department changed row', $row.data('index'), '->', $dept.val());
            loadEmployeesForRow($row, { autofill: false });
        });

        // Edit mode: we have existing values, so:
        if (initialLoad) {
            if ($product.val()) {
                loadServicesForRow($row, { keepCurrent: true });
                loadEmployeesForRow($row, { autofill: false, silent: true });
            }
        }
    }

    // =========================================================
    // LOAD SERVICES FOR THAT ROW
    // =========================================================
    function loadServicesForRow($row, options = {}) {
        const keepCurrent = options.keepCurrent === true;

        const $product = $row.find('.product-select');
        const $service = $row.find('.service-select');

        const pid = $product.val();
        const current = keepCurrent ? $service.val() : null;

        $service.empty().append('<option value="">Service wählen</option>');

        if (!pid) {
            $service.trigger('change');
            return;
        }

        const list = SERVICES.filter(s => String(s.product_id) === String(pid));

        list.forEach(s => {
            $service.append(`<option value="${s.id}">${translateService(s.phase_section)}</option>`);
        });

        if (keepCurrent && current && $service.find(`option[value="${current}"]`).length) {
            $service.val(current).trigger('change');
        } else if (list.length === 1) {
            $service.val(list[0].id).trigger('change');
        } else {
            $service.trigger('change');
        }
    }

    // =========================================================
    // LOAD EMPLOYEES FOR THAT ROW
    // Backend: { department_id, service_id, internal_employees, external_employees }
    // =========================================================
    function loadEmployeesForRow($row, options = {}) {
        const autofill = options.autofill === true;
        const silent   = options.silent === true;

        const $product = $row.find('.product-select');
        const $dept    = $row.find('.department-select');
        const $service = $row.find('.service-select');
        const $emp     = $row.find('.employee-select');
        const $field   = $row.find('.field-employee-select');

        const pid = $product.val();
        let   did = $dept.val();
        let   sid = $service.val();

        if (!pid) {
            resetEmployeeSelects($emp, $field);
            return;
        }

        console.log('loadEmployees row', $row.data('index'), {
            product_id:   pid,
            department_id: did,
            service_id:    sid,
            stage:         STAGE
        });

        $.post(ROUTE_EMPLOYEES, {
            _token:       CSRF_TOKEN,
            product_id:   pid,
            department_id: did || null,
            service_id:    sid || null,
            stage:         STAGE
        })
        .done(res => {
            console.log('employees response row', $row.data('index'), res);

            let internalEmployees = [];
            let externalEmployees = [];

            if (Array.isArray(res)) {
                internalEmployees = res;
                externalEmployees = res;
            } else {
                // Auto-suggest dept/service if empty and allowed
                if (autofill && !did && res.department_id) {
                    did = res.department_id;
                    $dept.val(did).trigger('change.select2');
                }
                if (autofill && !sid && res.service_id) {
                    sid = res.service_id;

                    if (!$service.find(`option[value="${sid}"]`).length) {
                        loadServicesForRow($row, { keepCurrent: false });
                    }
                    if ($service.find(`option[value="${sid}"]`).length) {
                        $service.val(sid).trigger('change.select2');
                    }
                }

                internalEmployees = res.internal_employees || [];
                externalEmployees = res.external_employees || [];
            }

            fillEmployeeSelect($emp,   internalEmployees, 'Innendienst wählen');
            fillEmployeeSelect($field, externalEmployees, 'Außendienst wählen');

            if (!internalEmployees.length && !externalEmployees.length && !silent) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Mitarbeiter gefunden',
                    text: 'Für diese Auswahl existieren keine Mitarbeiter.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-warning' },
                    buttonsStyling: false
                });
            }
        })
        .fail(xhr => {
            console.error('loadEmployees error row', $row.data('index'), xhr);
            resetEmployeeSelects($emp, $field);
            if (!silent) {
                Swal.fire('Fehler', 'Mitarbeiter konnten nicht geladen werden.', 'error');
            }
        });
    }

    function fillEmployeeSelect($select, employees, placeholder) {
        $select.empty().append(`<option value="">${placeholder}</option>`);

        employees.forEach(emp => {
            $select.append(`
                <option value="${emp.id}"
                        data-img="${emp.image || ''}"
                        data-positions="${(emp.positions || []).join(', ')}">
                    ${emp.name} ${emp.lastname}
                </option>
            `);
        });

        $select.select2({
            width: '100%',
            templateResult:   formatEmployee,
            templateSelection:formatEmployeeSelection,
            escapeMarkup:     m => m
        });
    }

    function resetEmployeeSelects($emp, $field) {
        fillEmployeeSelect($emp,   [], 'Innendienst wählen');
        fillEmployeeSelect($field, [], 'Außendienst wählen');
    }

    // =========================================================
    // EMPLOYEE SELECT2 TEMPLATES
    // =========================================================
    function formatEmployee(opt) {
        if (!opt.id) return opt.text;

        const $el     = $(opt.element);
        const imgFile = $el.data('img');
        const img     = imgFile ? `${IMG_EMPLOYEE}/${imgFile}` : '';
        const pos     = $el.data('positions') || '';

        return `
            <div style="display:flex;align-items:center;">
                ${img
                    ? `<img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">`
                    : `<div class="me-2 rounded-circle" style="width:36px;height:36px;background:#e5e7eb;"></div>`
                }
                <div>
                    <strong>${opt.text}</strong><br>
                    <small>${pos}</small>
                </div>
            </div>
        `;
    }

    const formatEmployeeSelection = opt => opt.text;

    // =========================================================
    // SERVICE TRANSLATION
    // =========================================================
    function translateService(s) {
        if (!s) return '';
        const key = String(s).toLowerCase();
        const map = {
            complete:    'Komplettlösung',
            montage:     'Montage',
            product:     'Produkt',
            plan:        'Planung',
            maintenance: 'Wartung',
            repair:      'Reparatur',
            emergency:   'Notdienst',
            others:      'Sonstiges'
        };
        return map[key] || s;
    }

    // =========================================================
    // REMOVE ROW
    // =========================================================
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').fadeOut(150, function () {
            $(this).remove();
        });
    });
});
</script>


@endsection