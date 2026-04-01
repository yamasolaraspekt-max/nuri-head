@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@section('style')
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

/* Customer Product Selection: Start */
.product_card {
    /* border-radius: 71px;
        background: #f1f1f1 !important; */
}

#product_card_details {
    background: #bbb8b8 !important;
    border-radius: 83px;
    color: white;
}

.products.selected {
    background: #cfe09b !important;
    color: white !important;
    border-radius: 71px;
}

.products.selected #product_card_details {
    background: #95c11f !important;
}

.products.selected .product_card {
    /* background: #cfe09b !important; */
}

.heart-icon.selected {
    color: #95c11f !important;
    font-size: 25px !important;
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
    width: 100% !important; 
    height: 40px !important;
    background: transparent !important;
    color: white;
    box-shadow: none !important;
    border:0 !important;
    padding:0 !important;
        text-align: left !important;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    display: none;
    /* Hides the arrow */
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
    width: 40px !important;
    height: 40px !important;
    padding: 0 !important;
}

.icons {
    font-size: 30px !important;
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
</style>
@endsection
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
                        <h2 class="content-header-title float-left mb-0">ANFRAGE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
         <form class="form form-horizontal custom-file-upload" method="post"
      action="{{ action('App\Http\Controllers\NewLeadsController@store') }}"
      enctype="multipart/form-data">
    @csrf
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-xl-4 col-md-12">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                        <div class="cards">
                            <div class="card-header" style="align-self: center;">
                                <h2 class="content-header-title float-left mb-0">KUNDE INTERESSE</h2>
                            </div>
                            <br>
                          @foreach ($articles as $item)
                            <article style="display: flex; align-items: center;">
                                <div class="text-center products mt-1 mb-1 col-10" style="background: #ebe6e6; border-radius: 44px;">
                                    <div class="card-content">
                                        <div class="row product_card">
                                            <div class="col-md-2 col-2" id="product_card_image">
                                                <img src="{{ asset('images/articles/'.$item->image) }}" alt="{{ $item->article_group }}"
                                                    style="width: 58px !important;" class="float-left mt-2">
                                            </div>
                                            <div class="col-md-10 col-10" id="product_card_details" style="display: flex; align-items: center;">
                                                <div class="details mr-1 ml-2" style="width:200px;">
                                                    <div style="border-right: 2px solid; padding-right:2px;">
                                                        <h3 class="card-title mt-2 mb-0 white title">
                                                            {{ $item->article_group }}</h2>
                                                        <p class="card-text white mb-1" style="font-size:10px"> Aktualler Status: <span
                                                                id="interested-{{ $loop->index }}"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="checkbox" class="d-none" name="product_id[]" value="{{ $item->id }}" />

                                                <div style="  display: flex;  flex-wrap: wrap; align-items: center; ">
                                                     <div class="service" style="text-align: left">
                                                        <label for="service" class="white">Leistung</label>
                                                        <select name="service" class="service_select" id="service" style="background: transparent; border: 0px; color: white; font-weight: bolder; width:188px;" >
                                                            <option value="complete">Komplettlösung</option>
                                                            <option value="montage">Montage</option>
                                                            <option value="product">Produkt</option>
                                                            <option value="plan">Planung</option>
                                                            <option value="maintenance">Wartung</option>
                                                            <option value="repair">Reparetur</option>
                                                            <option value="others">Sonstiges</option>
                                                        </select>
                                                        <i class="fa fa-chevron-down" style="position: absolute; top: 27px; left: 296px;"></i>

                                                    </div>
 
                                                  @php
                                                        $imagePath = 'images/employee/';  
                                                        $male = 'images/gender/male.png';    
                                                        $female = 'images/gender/female.png';                                               
                                                    @endphp

                                                    <div class="employee_section"  style="text-align: left"> 
                                                        <label for="employee_id" class="white">Zuständigkeit</label>

                                                        <select class="employee" name="employee_id" id="employee" style="background: transparent; border: 0; color: white;">
                                                            <option value="">Verantwortlicher auswählen</option>
                                                            @foreach ($employees as $emp)
                                                                @php
                                                                    $defaultImage = $emp->gender === "Male" ? $male : $female; // Determine the default gender-based image
                                                                    $imageUrl = $emp->image ? asset($imagePath . $emp->image) : asset($defaultImage); // Use employee's image if available, otherwise fallback to gender-based image
                                                                @endphp
                                                                <option 
                                                                    value="{{ $emp->id }}" 
                                                                    data-image="{{ $imageUrl }}">
                                                                    {{ $emp->name }} {{ $emp->lastname }}
                                                                </option>
                                                            @endforeach     
                                                        </select>
                                                        <i class="fa fa-chevron-down" style="position: absolute; top: 83px; left: 296px;"></i>
                                                    </div>


                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                                <div class="settings col-2" style="display: flex !important; align-items: flex-start; flex-direction: column; row-gap: 3px;">
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-light waves-effect waves-light buttons heart-button"
                                        style="width: 50px; height: 50px;" id="{{ $loop->index }}Like">
                                        <i class="fa fa-heart icons heart-icon"></i>
                                    </button>
                                </div>
                            </article>
                            @endforeach  
                            <input type="hidden" name="products" id="products"> 
                        </div>
                    </div>
                    <div class="col-xl-5 col-md-12">
                        <div class="cards">
                            <div class="card-header">
                                <div class="col-4">
                                    <h2 class="content-header-title float-left ">KUNDE DATEN</h2>
                                </div>
                                <div class="col-md-8 card-title h4 flex_me">
                                    <div class="col-md-5"> 
                                        <span style="color:#e50056" class="d-flex">
                                               <span style="color: #74b2d3">Info grad:</span>
                                            <input type="text" id="answered_number" name="answered_number" readonly style="background: transparent;border: 0;     width: 20px;"/> / 
                                            <input type="number" id="total_number" name="total_number" readonly style="background: transparent;border: 0;     width: 20px;"/>
                                        </span>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="progress progress-bar-primary progress-lg">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                                                <span id="percent">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-6"></div>
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type1" checked value="privat">
                                                                        <label class="custom-control-label" for="customer_type1">privat</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type2" value="Gewerbe">
                                                                        <label class="custom-control-label" for="customer_type2">Gewerbe</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type3" value="Kummune">
                                                                        <label class="custom-control-label" for="customer_type3">Kummune</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Additional form fields go here -->
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Title</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <fieldset class="form-group form-element">
                                                            <select class="form-control form-element" id="basicSelect" value="{{old('title')}}" name="title">
                                                                <option selected></option>
                                                                <option value="Frau">Frau</option>
                                                                <option value="Herr">Herr</option>
                                                                <option value="Dr.">Dr.</option>
                                                                <option value="Pro.">Pro.</option>
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12" id="firma-container">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Firma</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" id="firma" class="form-control form-element" value="{{old('firma')}}" name="firma">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Nachname</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" id="lastname" class="form-control form-element" value="{{ old('lastname') }}" name="lastname" autocomplete="off" list="lastname-options">
                                                        <datalist id="lastname-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Vorname</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" id="name" class="form-control form-element" value="{{ old('name') }}" name="name" autocomplete="off" list="name-options">
                                                        <datalist id="name-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                           
                                          <!-- Main Address Inputs -->
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>Straße / Nr.</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input id="location-input" type="text" class="form-control form-element" placeholder="Enter location" name="street" value="{{ old('street') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude') }}">
                                                            <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude') }}">
                                                            <input type="hidden" id="polygon-height" name="polygon_height" value="{{ old('polygon-height') }}">
                                                            <input type="hidden" id="polygon-width" name="polygon_width" value="{{ old('polygon_width') }}">
                                                            <input type="hidden" id="polygon-area" name="polygon_area" value="{{ old('polygon_area') }}">
                                                            <input type="hidden" id="elevation-input" placeholder="Elevation in meters" name="elevation" value="{{ old('elevation') }}">
                                                            <input type="text" class="form-control form-element" value="{{old('postcode')}}" name="postcode" id="postal_code-input">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control form-element" value="{{old('city')}}" name="city" id="locality-input">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>Festnet/Mobile</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control" value="{{old('telephone')}}" id="telephone-input" name="telephone" placeholder="Festnet">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control" value="{{old('phone')}}" name="phone" id="phone-input" placeholder="Mobile">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>E-Mail</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="email" class="form-control" id="email-input"  value="{{old('email')}}" name="email">
                                                        </div>
                                                    </div>
                                                </div>
                                                  <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>Periority</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <div class="star-rating form-element">
                                                                <select name="periority" id="" class="form-control form-element"> 
                                                                    <Option value="Normal">Normal</Option>
                                                                    <Option value="Dringend">Dringend</Option>
                                                                    <Option value="Sehr dringend">Sehr dringend</Option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                

                                                <!-- Alternative Address Inputs -->
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-10">
                                                            <fieldset>
                                                                <div class="vs-checkbox-con vs-checkbox-primary form-element">
                                                                    <input type="checkbox" name="alternative_address" id="alternative_address" value="false">
                                                                    <span class="vs-checkbox">
                                                                        <span class="vs-checkbox--check">
                                                                            <i class="vs-icon feather icon-check"></i>
                                                                        </span>
                                                                    </span>
                                                                    <span>Die Postanschrift ist identisch mit der Hauptadresse?</span>
                                                                </div>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" id="street2s">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>Straße / Nr.</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input id="location-input2" type="text" class="form-control form-element" placeholder="Enter location" name="street2" value="{{ old('street2') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" id="ort2s">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="hidden" id="latitude-input2" name="latitude2" value="{{ old('latitude2') }}">
                                                            <input type="hidden" id="longitude-input2" name="longitude2" value="{{ old('longitude2') }}">
                                                            <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation2" value="{{ old('elevation2') }}">
                                                            <input type="text" class="form-control form-element" value="{{old('postcode2')}}" name="postcode2" id="postal_code-input2">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control form-element" value="{{old('city2')}}" name="city2" id="locality-input2">
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                            <div class="row">
                                               <div class="col-12">
                                                     <div class="accordion" id="accordionExample" data-toggle-hover="true"> 
                                                        <div class="">
                                                        <div class="card-header" id="heading4" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                            <span class="lead collapse-title collapsed primary" style="font-weight: bold;">
                                                                ENERGIEVERBRAUCH UND OBJEKTDATEN
                                                            </span>
                                                        </div>
                                                        <div id="collapse4" class="collapse show" aria-labelledby="heading4" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                            <div class="col-12">
                                                                                <h2 class="primary"><strong>OBJEKTDATEN</strong></h2>
                                                                                <hr>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche Objektart handelt es sich?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <select name="objective" id="" class="form-control">
                                                                                            <option value="">Bitte wählen</option>
                                                                                            <option value="EFH">EFH</option>
                                                                                            <option value="MFH">MFH</option>
                                                                                            <option value="Gewerbe">Gewerbe</option>
                                                                                            <option value="others">Sonstigis</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Baujahr Ihres Hauses?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="house_year" id="house_year" value="{{ old('house_year') }}" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                                
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wieveil Wohneinheit hat das Obejekt?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control textbox" name="number_we" value="{{ old('number_we') }}">
                                                                                    
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wieviel Geschoß hat das Objekt?   </h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control"  name="number_stories" value="{{ old('number_stories') }}">
                                                                                    
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie groß ist die Beheizte Wohnfläche?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control" name="living_space" value="{{ old('living_space') }}">
                                                                                        <span style="position: absolute; right: 20px;"> m²</span>
                                                                                    
                                                                                    </div>  
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie groß ist die Nutzfläche?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control" name="unusable_space"  value="{{ old('unusable_space') }}">
                                                                                        <span style="position: absolute; right: 20px;"> m²</span> 
                                                                                    </div>  
                                                                                </div>
                                                                            </div>


                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wieviel Personen wohnen in diesem Objekt?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control" name="number_people" id="number_people"  value="{{ old('number_people') }}" > 
                                                                                    </div>  
                                                                                </div>
                                                                            </div>
                                                                        
                                                                            <div class="col-12"><h2 class="primary"><strong>DACH-INFORMATION</strong></h2><hr></div> 
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche Art vom Dach haben Sie?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <select class="form-control form-element" name="roof_type" id="roof">
                                                                                            <option selected></option>
                                                                                            <option value="Satteldach">Satteldach</option>
                                                                                            <option value="Flachdach">Flachdach</option>
                                                                                            <option value="Carpot">Carpot</option>
                                                                                            <option value="Garage">Garage</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie alt ist Ihr Dach?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="roof_age" id="roof_age" value="{{ old('roof_age') }}" />
                                                                                        <span style="position: absolute; right: 20px;">Jahr</span>
                                                                                    
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <span id="roof_age_error" class="text-danger"></span>
                                                                                    </div>
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche Dacheindeckung hat das Dach? 
                                                                                            <i class="feather icon-info warning" 
                                                                                            data-toggle="popover" 
                                                                                            data-placement="top" 
                                                                                            data-container="body" 
                                                                                            data-original-title="Achtung" 
                                                                                            data-content="Der verfügbare Wert liegt zwischen 0,5, 10, 15, 20 und 60."></i>
                                                                                        </h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control textbox" name="roof_covering" value="{{ old('roof_covering') }}"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                        
                                                                            <!-- Make i button to show from which to which number can be  -->
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche dachneigung hat ihr Dach?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control textbox" name="roof_pitch" value="{{ old('roof_pitch') }}"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div> 

                                                                                <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche himmelsausrechtung hat ihr Dach?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <select name="roof_direction" id="" class="form-control"> 
                                                                                            <option value="0">Süden </option>
                                                                                            <option value="45">Süd-west </option>
                                                                                            <option value="90">Westen </option>
                                                                                            <option value="135">Nord-west </option>
                                                                                            <option value="180">Norden </option>
                                                                                            <option value="-135">Nord-ost </option>
                                                                                            <option value="-90">Osten </option>
                                                                                            <option value="-45">Süd-ost </option>  
                                                                                        </select> 
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                    </div>
                                                                    
                                                                    <div class="col-md-6">
                                                                        <div class="col-12"><h2 class="primary"><strong>HEIZUNGS-INFORMATION</strong></h2><hr></div> 
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welche Art von Heizungsanlage haben Sie?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <select class="form-control form-element" name="heating_system_type" id="heating_system_type">
                                                                                            <option selected disabled> </option>
                                                                                            <option value="Gas">Gas</option>
                                                                                            <option value="Öl">Öl</option>
                                                                                            <option value="Wärmepumpe">Wärmepumpe</option>
                                                                                            <option value="Nachtspeicher">Nachtspeicher</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie alt ist Ihre Heizungsanlage?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="heating_system_age" id="heating_system_age" value="{{ old('heating_system_age')}}"/>
                                                                                        <span style="position: absolute; right: 20px;">Jahr</span>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <span id="heating_system_age_error" class="text-danger"></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold"> Baujahr der Heizungsanlage?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="heating_system_year" id="heating_system_year" value="{{ old('heating_system_year')}}" />
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <span id="heatingYearError" class="text-danger"></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Welches Heizsystem ist verbaut?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <select name="heating_type" id="heating_type" class="form-control">
                                                                                            <option value="">Bitte wählen</option>
                                                                                            <option value="1">Fußbodenheizung</option>
                                                                                            <option value="2">Heizkörper</option>
                                                                                            <option value="3" >Fußbodenheizung + Heizkörper</option>
                                                                                            <option value="4">Keine</option>
                                                                                        </select>
                                                                                    </div>  
                                                                                </div>
                                                                            </div>
                                                                        

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wo befindet sich die aktuelle Heizungsanlage?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <select name="installation_location" id="" class="form-control">
                                                                                            <option value="">Bitte wählen</option>
                                                                                            <option value="KG">KG</option>
                                                                                            <option value="EG">EG</option>
                                                                                            <option value="OG"> OG</option>
                                                                                            <option value="DG"> DG</option> 
                                                                                            <option value="SONSTIGES"> SONSTIGES</option> 
                                                                                        </select>
                                                                                        <input type="text" class="form-control" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra')}}" placeholder="SONSTIGIES..">
                                                                                    </div>  
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12"><h2 class="primary"><strong>STROMVERBRAUCH</strong></h2><hr></div> 

                                                                        
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie hoch ist Ihr jährlicher Stromverbrauch?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="annual_consumption" value="{{ old('annual_consumption')}}"  />
                                                                                        <span style="position: absolute;right: 20px;">kWh</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12"><h2 class="primary"><strong>HEIZENERGIE VERBRAUCH</strong></h2><hr></div> 
                                                                            
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3>Wie hoch ist Ihr jährlicher Verbrauch an Heizenergie?</he>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <!-- Conersion of CMB to KWH, cmb * 10  -->
                                                                                        <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption')}}" />
                                                                                        <span  id="heat-energy">CMB</span>
                                                                                        <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh')}}" /> 
                                                                                        <span >kWh</span>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12"><h2 class="primary"><strong>E-MOBILITÄT</strong></h2><hr></div> 

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold" >Haben Sie ein Elektroauto? Oder planen Sie, welche zukaufen?</h3>
                                                                                    </div>
                                                                                    <br>
                                                                                    <div class="col-md-6 flex_me">
                                                                                        <select class="form-control form-element" name="electric_car" id="electric_car">
                                                                                            <option selected disabled></option>
                                                                                            <option value="Ja">Ja</option>
                                                                                            <option value="Nein">Nein</option>
                                                                                        </select>
                                                                                        <!-- When Nein, the below text box should be hidden -->
                                                                                    </div>
                                                                                    <div class="col-md-6 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="electric_car_plan" id="electric_car_plan" value="{{ old('electric_car_plan')}}" style="display:none;" />
                                                                                        <span style="display:none;position: absolute; right: 20px;"  id="electric_car_plan_l">ANZAHLE</span>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie viele Kilometer hat das Auto gefahren</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="car_kilo" value="{{ old('car_kilo')}}"  />
                                                                                        <span style="position: absolute;right: 20px;">km</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                   
                                                    <div class="">
                                                        <div class="card-header collapsed" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                            <span class="lead collapse-title collapsed primary" style="font-weight: bold;">
                                                                WEITERE INFORMATIONEN
                                                            </span>
                                                        </div>
                                                        <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordionExample" style="">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-4">
                                                                                <span>Quelle</span>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <select name="source" id="source" class="form-control form-element">
                                                                                    <option selected></option>
                                                                                    <option value="Telefonisch">Telefonisch</option>
                                                                                    <option value="Persönlich">Persönlich</option>
                                                                                    <option value="Mail">Mail</option>
                                                                                    <option value="Nachbar">Nachbar</option>
                                                                                    <option value="Empfehlung">Empfehlung</option>
                                                                                    <option value="Solarrechner">Solarrechner</option>
                                                                                    <option value="Herstellerlead">Herstellerlead</option>
                                                                                    <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-4">
                                                                                <span>Anfrage-Datum</span>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <input type="date" class="form-control form-element" name="request_date" value="{{ now()->format('Y-m-d') }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-2">
                                                                                <span>Info</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control form-element" name="info" value="{{ old('info') }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-6">
                                                                                <span>Kunde aufgefordert Unterlagen zu schicken</span>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element"   name="document"  id="customRadio1">
                                                                                                <label class="custom-control-label" for="customRadio1">Ja</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-2">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element"  checked name="document" id="customRadio2">
                                                                                                <label class="custom-control-label" for="customRadio2">Nein</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @php
                                                                    $user_name = DB::table('employees')
                                                                    ->join('users', 'users.name', '=', 'employees.id')
                                                                    ->select('employees.name', 'employees.lastname')
                                                                    ->where('users.name', '=', auth()->user()->name)
                                                                    ->first()
                                                                    @endphp

                                                                    @php
                                                                    $employee = DB::table('employees')
                                                                    ->select('employees.id','employees.name', 'employees.lastname')
                                                                    ->get()
                                                                    @endphp
                                                                    <div class="col-6">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-4">
                                                                                <span>Kontaktperson</span>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                @if($user_name)
                                                                                <input type="hidden" name="contact_person" class="form-control form-element" name="{{ auth()->user()->name }}" value="{{ auth()->user()->name}}" >
                                                                                <input type="text" class="form-control form-element" name="{{ auth()->user()->name }}" value="{{ $user_name->name }} {{ $user_name->lastname }}" disabled readonly>
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
                                                                    </div>
                                                                    
                                                                    <div class="col-12">
                                                                        <hr>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="col-md-12">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="col-5">
                                                                                    <span class="mr-2">Interesse</span>
                                                                                </div>
                                                                                <div class="col-7">
                                                                                    <div class="star-rating form-element" data-category="interest" data-rating="0">
                                                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                    </div>
                                                                                    <input type="hidden" name="interest_rating" value="0">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="col-5">
                                                                                    <span class="mr-2">Ernsthaftigkeit</span>
                                                                                </div>
                                                                                <div class="col-7">
                                                                                    <div class="star-rating form-element" data-category="seriousness" data-rating="0">
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                    </div>
                                                                                    <input type="hidden" name="seriousness_rating" value="0">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="col-5">
                                                                                    <span class="mr-2">Preisinformation</span>
                                                                                </div>
                                                                                <div class="col-7">
                                                                                    <div class="star-rating form-element" data-category="price_information" data-rating="0">
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                                                    </div>
                                                                                    <input type="hidden" name="price_information" value="0">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    <div class="col-12" style="height: 20px;"></div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-2">
                                                                                <span>Notizen</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <textarea name="note" class="form-control form-element" cols="30" rows="5"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-4">
                                                                                <span>Wann können wir Sie Kontaktieren?</span>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <input type="date" class="form-control form-element" name="appointment">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4"></div>
                                                                    <div class="col-8">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-12">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_telefonisch" value="telefonisch">
                                                                                                <label class="custom-control-label" for="appointment_by_telefonisch">telefonisch</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_email" value="E-Mail">
                                                                                                <label class="custom-control-label" for="appointment_by_email">E-Mail</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_ort" value="Vor Ort Besuch">
                                                                                                <label class="custom-control-label" for="appointment_by_ort">Vor Ort Besuch</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                </ul>
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
                    <div class="col-xl-3 col-md-12"> 
                        <div class="row">
                            <div class="col-12">
                                <!-- {{-- Map Start --}} -->
                            <div class="card">
                                    <div class="card-header" style="align-self: center;">
                                        <h2 class="content-header-title float-left">OBJEKT BILDER</h2>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body"> 
                                            <div class="map" id="gmp-map" style="width: 100%; position: relative; overflow: hidden; height: 356px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- {{-- Map End --}} -->
                            </div>
                        </div>
                    

                    <div class="col-lg-3">
                        <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn" data-target="#new_pic" data-toggle="modal">
                            <i class="feather icon-camera"></i> Screenshot
                        </a>
                        <div class="modal fade text-left" id="new_pic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel33">FOTO</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form id="image-upload-form" action="#" method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <img id="modal-screenshot-img" src="" alt="Map Screenshot" class="img-fluid mb-3" />

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>KATEGORIE</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <select name="category_id" class="form-control">
                                                           @foreach ($category as $cat)
                                                               <option value="{{$cat->id}}"> 
                                                                {{$cat->category}}
                                                               </option>
                                                           @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" id="upload-button">Hochladen</button>
                                        </div>
                                        <input type="hidden" id="image-location" name="image-location" value="">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="image-container" style="display:none">
                        <div class="card">
                            <div class="card-content">
                                <img class="card-img-top img-fluid" src="" alt="Kunde House">
                                <div class="card-body">
                                    <h4 class="card-title">KUNDEN</h4> 
                                    <a href="#" class="btn btn-outline-primary waves-effect waves-light" id="delete_screenshot">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden file input to store the screenshot data -->
                    <input type="file" id="screenshot-file-input" name="screenshot_file" class="form-control" /> 
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

<!-- Name and Lastname suggestions  -->
 <script>
    document.getElementById('lastname').addEventListener('input', function () {
        let input = this.value;

        if (input.length >= 2) { // Start searching after at least 2 characters are typed
            fetch(`/api/lead-lastname-suggestions?query=${input}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let options = data.map(name => `<option value="${name}">`).join('');
                    document.getElementById('lastname-options').innerHTML = options;
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    }); 

 </script> 
  <script>
    document.getElementById('name').addEventListener('input', function () {
        let input = this.value;

        if (input.length >= 2) { // Start searching after at least 2 characters are typed
            fetch(`/api/lead-name-suggestions?query=${input}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let options = data.map(name => `<option value="${name}">`).join('');
                    document.getElementById('name-options').innerHTML = options;
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    }); 

 </script>

 <!-- check for duplciate or existing record -->
 <script>
   $(document).ready(function() {
    $('#lastname, #name, #location-input, #postal_code-input, #locality-input').on('change', function() {
        // Collect form data
        var lastname = $('#lastname').val();
        var name = $('#name').val();
        var street = $('#location-input').val();
        var postcode = $('#postal_code-input').val();
        var city = $('#locality-input').val();

        console.log("Collected Data:", {lastname, name, street}); // Debugging line

        // Check if all fields are filled
        if (lastname && name && street) {
            // Make an AJAX request to check if the customer exists
            $.ajax({
                url: '/check-new-leads',
                type: 'GET',
                data: {
                    lastname: lastname,
                    name: name,
                    street: street, 
                },
                success: function(response) {
                    console.log("Server Response:", response); // Debugging line

                    if (response.exists) {
                        // Show SweetAlert with a link to the customer profile
                        Swal.fire({
                            title: 'Der Kunde existiert bereits',
                            html: `<p>Name: ${response.customer_id}. ${response.customer_name} ${response.customer_lastname}</p>
                                   <p>Adresse: ${response.customer_street}, ${response.customer_postcode}, ${response.customer_city}</p>
                                   <p>Eindeutige Adressnummer: ${response.address_no}</p>
                                   <p>Klicken Sie unten, um das Kundenprofil anzuzeigen.</p>`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Profil anzeigen',
                            cancelButtonText: 'Absagen'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to the customer profile page
                                window.location.href = `/new_lead_profile/${response.customer_id}/${response.customer_postcode}/${response.address_no}`;
                            }
                        });
                    } else {
                        console.log("No matching customer found."); // Debugging line
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error); // Debugging line
                }
            });
        } else {
            console.log("Not all fields are filled."); // Debugging line
        }
    });
});


 </script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
 <!-- script of showing the traffic light -->
 <script>
        document.addEventListener('input', function() {
            const street = document.getElementById('location-input').value.trim();
            const postcode = document.getElementById('postal_code-input').value.trim();
            const city = document.getElementById('locality-input').value.trim();
            const telephone = document.getElementById('telephone-input').value.trim();
            const phone = document.getElementById('phone-input').value.trim();
            const email = document.getElementById('email-input').value.trim();

            const statusIcon = document.getElementById('status-icon');
            
            if (street && postcode && city && (telephone || phone) && email) {
                statusIcon.innerHTML = `<img src="{{ asset('images/icons/ampel-gruen.svg') }}" alt="Icon" style="width:30px" data-content="DIE ANFRAGE IST BEREIT ZU QUALIFIZIEREN" data-trigger="hover" data-original-title="QUALIFIZIERT" class="float-right">`;
            } else if (street || postcode || city || telephone || phone || email) {
                statusIcon.innerHTML = `<img src="{{ asset('images/icons/ampel-gelb.svg') }}" alt="Icon" style="width:30px" data-content="NICHT QUALIFIZIERT" data-trigger="hover" class="float-right" data-original-title="NICHT QUALIFIZIERT">`;
            } else {
                statusIcon.innerHTML = `<img src="{{ asset('images/icons/ampel-rot.svg') }}" alt="Icon" style="width:30px" data-content="NICHT QUALIFIZIERT" data-trigger="hover" class="float-right" data-original-title="NICHT QUALIFIZIERT">`;
            }
        });
    </script>

<!-- Electronic Car -->
<script>
document.getElementById('electric_car').addEventListener('change', function() {
    var electricCarPlan = document.getElementById('electric_car_plan');
    var label = document.getElementById('electric_car_plan_l');
    if (this.value === 'Ja') {
        electricCarPlan.style.display = 'block';
        label.style.display = 'block';
    } else {
        electricCarPlan.style.display = 'none';
        label.style.display = 'none';
    }
});
</script>

<!-- Age of House -->
<script>
    document.getElementById('roof_age').addEventListener('input', function() {
        var roofAge = parseInt(this.value, 10);
        if (!isNaN(roofAge)) {
            var currentYear = new Date().getFullYear();
            var houseYear = currentYear - roofAge;
            document.getElementById('house_year').value = houseYear;
        }
    });

    // document.getElementById('house_year').addEventListener('input', function() {
    //     var houseYear = parseInt(this.value, 10);
    //     if (!isNaN(houseYear)) {
    //         var currentYear = new Date().getFullYear();
    //         var roofAge = currentYear - houseYear;
    //         document.getElementById('roof_age').value = roofAge;
    //     }
    // });
</script>
<!-- Age of Heating System -->
<script>
    document.getElementById('heating_system_age').addEventListener('input', function() {
        var roofAge = parseInt(this.value, 10);
        if (!isNaN(roofAge)) {
            var currentYear = new Date().getFullYear();
            var houseYear = currentYear - roofAge;
            document.getElementById('heating_system_year').value = houseYear;
        }
    });

    document.getElementById('heating_system_year').addEventListener('input', function() {
        var houseYear = parseInt(this.value, 10);
        if (!isNaN(houseYear)) {
            var currentYear = new Date().getFullYear();
            var roofAge = currentYear - houseYear;
            document.getElementById('heating_system_age').value = roofAge;
        }
    });
</script>

<!-- Heating Drop Down -->
<script>
    document.getElementById('heating_system_type').addEventListener('change', function() {
        var unitSpan = document.getElementById('heat-energy');
        var selectedValue = this.value;
        
        switch (selectedValue) {
            case 'Gas':
                unitSpan.textContent = 'CBM';
                break;
            case 'Öl':
                unitSpan.textContent = 'Liter';
                break;
            case 'Wärmepumpe':
                unitSpan.textContent = 'kWh';
                break;
            case 'Nachtspeicher':
                unitSpan.textContent = 'kWh';
                break;
            default:
                unitSpan.textContent = 'kWh';
        }
    });
</script>

<!-- {{-- Alternative Address Script --}} -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('alternative_address');
        const street2s = document.getElementById('street2s');
        const ort2s = document.getElementById('ort2s');

        // Function to toggle visibility
        function toggleAddressFields() {
            if (checkbox.checked) {
                street2s.style.display = 'none';
                ort2s.style.display = 'none';
            } else {
                street2s.style.display = 'block';
                ort2s.style.display = 'block';
            }
        }

        // Initial check on page load
        toggleAddressFields();

        // Add event listener to checkbox
        checkbox.addEventListener('change', toggleAddressFields);
    });
</script>


<!-- {{-- Star slider Script --}} -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starRatings = document.querySelectorAll('.star-rating');

        starRatings.forEach(rating => {
            const stars = rating.querySelectorAll('.star');
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    rating.dataset.rating = index + 1;
                    updateStars(rating);
                });

                star.addEventListener('mouseover', () => {
                    highlightStars(rating, index);
                });

                star.addEventListener('mouseout', () => {
                    resetStars(rating);
                });
            });
        });

        function updateStars(rating) {
            const stars = rating.querySelectorAll('.star');
            const ratingValue = rating.dataset.rating;
            const category = rating.dataset.category;
            document.querySelector(`input[name=${category}_rating]`).value = ratingValue;

            stars.forEach((star, index) => {
                if (index < ratingValue) {
                    star.classList.add('selected');
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
            const ratingValue = rating.dataset.rating;
            const stars = rating.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < ratingValue) {
                    star.classList.add('selected');
                    star.classList.remove('hovered');
                } else {
                    star.classList.remove('selected');
                    star.classList.remove('hovered');
                }
            });
        }

        // Initialize the stars based on the current rating
        starRatings.forEach(rating => updateStars(rating));
    });
</script>

<!-- Map and screenshots  -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

<script>
    "use strict";

    let map;
    let marker;
    let panorama;
    let autocompleteMain;
    let autocompleteAlt;
    let capturedImageDataURL;

    function initMap() {
        const CONFIGURATION = {
            mapOptions: { 
                fullscreenControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                zoom: 22,
                zoomControl: true,
                maxZoom: 20,
                mapId: "DEMO_MAP_ID",
                mapTypeId: google.maps.MapTypeId.SATELLITE
            }
        };

        map = new google.maps.Map(document.getElementById('gmp-map'), CONFIGURATION.mapOptions);
        marker = new google.maps.Marker({ map: map });

        autocompleteMain = new google.maps.places.Autocomplete(document.getElementById('location-input'), {
            fields: ['address_components', 'geometry', 'name'],
            types: ['address']
        });
        autocompleteAlt = new google.maps.places.Autocomplete(document.getElementById('location-input2'), {
            fields: ['address_components', 'geometry', 'name'],
            types: ['address']
        });

        const elevationService = new google.maps.ElevationService();

        autocompleteMain.addListener('place_changed', () => handlePlaceChange(autocompleteMain, 'main', elevationService));
        autocompleteAlt.addListener('place_changed', () => handlePlaceChange(autocompleteAlt, 'alt', elevationService));

        document.getElementById('screenshot-btn').addEventListener('click', takeScreenshot);
        document.getElementById('upload-button').addEventListener('click', uploadImage);
        document.getElementById('delete_screenshot').addEventListener('click', deleteScreenshot);
        document.getElementById('alternative_address').addEventListener('change', handleCheckboxChange);
    }

    function handlePlaceChange(autocomplete, type, elevationService) {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
        }

        renderAddress(place, map, marker, type);
        fillInAddress(place, type);
        getElevation(place.geometry.location, elevationService, type);
    }

   function fillInAddress(place, type) {
        const addressComponents = {
            street_number: '',
            route: '',
            locality: '',
            administrative_area_level_1: '',
            postal_code: '',
            country: ''
        };

        place.address_components.forEach(component => {
            component.types.forEach(type => {
                if (addressComponents[type] !== undefined) {
                    addressComponents[type] = component.long_name;
                }
            });
        });

        const streetNumber = addressComponents.street_number;
        const route = addressComponents.route;
        const prefix = type === 'main' ? '' : '2';

        // Swap street number and route order
        document.getElementById(`location-input${prefix}`).value = `${route} ${streetNumber}`;
        document.getElementById(`postal_code-input${prefix}`).value = addressComponents.postal_code;
        document.getElementById(`locality-input${prefix}`).value = addressComponents.locality;
        // document.getElementById(`region-input${prefix}`).value = addressComponents.administrative_area_level_1;
        // document.getElementById(`country-input${prefix}`).value = addressComponents.country;

        if (type === 'main' && document.getElementById('alternative_address').checked) {
            fillInAddress(place, 'alt');
        }
    }


    function renderAddress(place, map, marker, type) {
        map.setCenter(place.geometry.location);
        marker.setPosition(place.geometry.location);

        const prefix = type === 'main' ? '' : '2';
        document.getElementById(`latitude-input${prefix}`).value = place.geometry.location.lat();
        document.getElementById(`longitude-input${prefix}`).value = place.geometry.location.lng();
    }

    function getElevation(location, elevationService, type) {
        elevationService.getElevationForLocations({ 'locations': [location] }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const elevation = results[0].elevation;
                const prefix = type === 'main' ? '' : '2';
                document.getElementById(`elevation-input${prefix}`).value = elevation.toFixed(2);
            }
        });
    }

    function handleCheckboxChange() {
        const isChecked = document.getElementById('alternative_address').checked;
        if (isChecked) {
            const mainPlace = autocompleteMain.getPlace();
            if (mainPlace && mainPlace.geometry) {
                fillInAddress(mainPlace, 'alt');
                renderAddress(mainPlace, map, marker, 'alt');
                const elevationService = new google.maps.ElevationService();
                getElevation(mainPlace.geometry.location, elevationService, 'alt');
            }
        }
    }

    function takeScreenshot() {
        const mapContainer = document.getElementById('gmp-map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }

        // Hide controls before taking the screenshot
        const controls = document.querySelector('.gm-style .gmnoprint');
        if (controls) controls.style.display = 'none';

        html2canvas(mapContainer, {
            useCORS: true,
            allowTaint: true,
        }).then(canvas => {
            capturedImageDataURL = canvas.toDataURL('image/png');
            showScreenshotModal(capturedImageDataURL);

            // Show controls again after taking the screenshot
            if (controls) controls.style.display = '';
        }).catch(error => {
            console.error('Error capturing screenshot:', error);
            // Ensure controls are shown even if there's an error
            if (controls) controls.style.display = '';
        });
    }

    function showScreenshotModal(imageDataURL) {
        const modalImage = document.getElementById('modal-screenshot-img');
        if (modalImage) {
            modalImage.src = imageDataURL;
            $('#new_pic').modal('show');
        } else {
            console.error('Modal image element not found');
        }
    }

    function uploadImage() {
        const cardImage = document.querySelector('#image-container .card-img-top');
        const fileInput = document.getElementById('screenshot-file-input');
        const imageContainer = document.getElementById('image-container');

        if (cardImage) {
            cardImage.src = capturedImageDataURL;

            // Convert the dataURL to a Blob and set it as the value of the file input
            fetch(capturedImageDataURL)
                .then(res => res.blob())
                .then(blob => {
                    const file = new File([blob], "screenshot.png", { type: "image/png" });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                });

            // Show the image container
            imageContainer.style.display = 'block';
        } else {
            console.error('Card image element not found');
        }

        // Close the modal
        $('#new_pic').modal('hide');
    }

    function deleteScreenshot() {
        const cardImage = document.querySelector('#image-container .card-img-top');
        if (cardImage) {
            cardImage.src = '';
        } else {
            console.error('Card image element not found');
        }

        // Clear the hidden file input
        const fileInput = document.getElementById('screenshot-file-input');
        if (fileInput) {
            fileInput.value = '';
        }

        // Hide the image container
        const imageContainer = document.getElementById('image-container');
        if (imageContainer) {
            imageContainer.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMap();
        document.getElementById('screenshot-btn').addEventListener('click', takeScreenshot);
        document.getElementById('upload-button').addEventListener('click', uploadImage);
        document.getElementById('delete_screenshot').addEventListener('click', deleteScreenshot);
    });
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

<!-- Selecting the product with Heart Button: start  -->
 <script>
    document.addEventListener("DOMContentLoaded", function () {
        let selectedProducts = [];

        document.querySelectorAll('.products').forEach((card, index) => {
            const checkbox = card.querySelector('input[name="product_id[]"]');
            const serviceSelect = card.querySelector('select[name="service"]');
            const employeeSelect = $(card).find('select.employee'); // Use jQuery for Select2
            const statusSpan = card.querySelector(`#interested-${index}`);
            const heartButton = document.getElementById(`${index}Like`);
            const heartIcon = heartButton ? heartButton.querySelector('.heart-icon') : null;

            if (!checkbox || !serviceSelect || !employeeSelect.length || !heartButton || !heartIcon) {
                console.error(`Missing elements in card at index ${index}`);
                return;
            }

            updateHeartButton(checkbox.checked, heartIcon, heartButton);

            // Handle heart button click
            heartButton.addEventListener('click', (event) => {
                checkbox.checked = !checkbox.checked;
                card.classList.toggle('selected', checkbox.checked);

                const productId = checkbox.value;
                const service = serviceSelect.value;
                const employeeId = employeeSelect.val(); // Get selected employee ID

                if (checkbox.checked) {
                    statusSpan.innerHTML = 'Interessiert';
                    updateHeartButton(true, heartIcon, heartButton);
                    addOrUpdateProduct(productId, service, employeeId);
                } else {
                    statusSpan.innerHTML = '';
                    updateHeartButton(false, heartIcon, heartButton);
                    removeFromSelectedProducts(productId);
                }

                event.stopPropagation();
            });

            // Update service when it changes
            serviceSelect.addEventListener('change', () => {
                const productId = checkbox.value;
                const service = serviceSelect.value;
                updateServiceInSelectedProducts(productId, service);
            });

            // Update employee when it changes
            employeeSelect.on('change', () => {
                const productId = checkbox.value;
                const employeeId = employeeSelect.val(); // Get selected employee ID
                updateEmployeeInSelectedProducts(productId, employeeId);
            });
        });

        function updateHeartButton(isChecked, heartIcon, heartButton) {
            if (isChecked) {
                heartIcon.classList.add('selected');
                heartButton.classList.remove('btn-light');
                heartButton.classList.add('btns-primary');
            } else {
                heartIcon.classList.remove('selected');
                heartButton.classList.remove('btns-primary');
                heartButton.classList.add('btn-light');
            }
        }

        function addOrUpdateProduct(productId, service, employeeId) {
            const existingProduct = selectedProducts.find(item => item.product_id === productId);
            if (existingProduct) {
                existingProduct.service = service;
                existingProduct.employee_id = employeeId || null;
            } else {
                selectedProducts.push({ product_id: productId, service, employee_id: employeeId || null });
            }
            updateSelectedProductsInput();
        }

        function removeFromSelectedProducts(productId) {
            selectedProducts = selectedProducts.filter(item => item.product_id !== productId);
            updateSelectedProductsInput();
        }

        function updateServiceInSelectedProducts(productId, service) {
            const product = selectedProducts.find(item => item.product_id === productId);
            if (product) {
                product.service = service;
            }
            updateSelectedProductsInput();
        }

        function updateEmployeeInSelectedProducts(productId, employeeId) {
            const product = selectedProducts.find(item => item.product_id === productId);
            if (product) {
                product.employee_id = employeeId;
            } else {
                selectedProducts.push({ product_id: productId, service: null, employee_id: employeeId });
            }
            updateSelectedProductsInput();
        }

        function updateSelectedProductsInput() {
            console.log('Selected Products:', selectedProducts);

            const productsInput = document.querySelector('#products');
            if (productsInput) {
                productsInput.value = JSON.stringify(selectedProducts);
            } else {
                console.error('Products input element not found.');
            }
        }
    });
</script>


<!-- Selecting the product with Heart Button: end  -->

<script>
    $(document).ready(function () {
        // Initialize Select2 with custom rendering for images
        $('.employee').select2({
            theme: 'bootstrap4', // Optional: Use Bootstrap 4 theme if you have it configured
            templateResult: formatOption, // Function for formatting dropdown options
            templateSelection: formatOption, // Function for formatting the selected option
            placeholder: "Auswählen", // Placeholder for the dropdown
        });

        // Function to format options with images
        function formatOption(option) {
            if (!option.id) {
                // Return plain text for the placeholder or non-selectable options
                return option.text;
            }

            // Get the image URL from the `data-image` attribute
            const imgUrl = $(option.element).data('image') || 'default_image_url_here';

            // Create the custom HTML structure for the option
            const $option = $(
                `<div style="display: flex; align-items: center;">
                    <img src="${imgUrl}" alt="Employee" 
                         style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
                    <span>${option.text}</span>
                </div>`
            );
            return $option;
        }
    });
</script>

<!-- Saving IMAGE IN BROWSWER TEMPERORY -->

    <script>
        document.getElementById('upload-button').addEventListener('click', function() {
            const fileInput = document.getElementById('image-input');
            const files = fileInput.files;
            const imageContainer = document.getElementById('image-container');
            const imageLocationInput = document.getElementById('image-location');

            // Initialize the image array
            let imageArray = [];

            // Parse the existing image array from the hidden input if it exists
            if (imageLocationInput.value) {
                imageArray = JSON.parse(imageLocationInput.value);
            }

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                const uniqueName = `${Date.now()}-${file.name}`; // Generate a unique name using the current timestamp
                
                reader.onload = function(e) {
                    const cardDiv = document.createElement('div');
                    cardDiv.className = 'col-lg-3 col-md-6 col-sm-12';
                    cardDiv.innerHTML = `
                        <div class="card position-relative">
                            <div class="card-content">
                                <img class="card-img" src="${e.target.result}" alt="Card image">
                                
                            </div>
                            <button type="button" class="close delete-image" aria-label="Close" style="position: absolute;top: -1px;right: -9px;color: white;background: red;border: none;border-radius: 50%;width: 25px;height: 25px;display: flex;align-items: center;justify-content: center;">
                                &times;
                            </button>
                        </div> 
                        <input type="hidden" name="image-name[]" value="${uniqueName}"> 
                    `;
                    imageContainer.appendChild(cardDiv);

                    // Add image data to the array
                    imageArray.push({
                        name: uniqueName,
                        dataUrl: e.target.result
                    });

                    // Update the hidden input with the array as a JSON string
                    imageLocationInput.value = JSON.stringify(imageArray);

                    // Add event listener to the delete button
                    cardDiv.querySelector('.delete-image').addEventListener('click', function() {
                        imageContainer.removeChild(cardDiv);
                        // Remove the deleted image from the array
                        const index = imageArray.findIndex(image => image.name === uniqueName);
                        if (index !== -1) {
                            imageArray.splice(index, 1);
                            // Update the hidden input with the array as a JSON string
                            imageLocationInput.value = JSON.stringify(imageArray);
                        }
                    });
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        });

</script>
 
<!-- The progress bar var  -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('.form-element');
    const answer = document.getElementById('answered_number');
    const total = document.getElementById('total_number');
    const progressBar = document.querySelector('.progress-bar');
    const percentSpan = document.getElementById('percent');
    const totalElements = 30;

    function updateProgressBar() {
        let nonEmptyCount = 0;
        elements.forEach(element => {
            if ((element.type === 'checkbox' || element.type === 'radio') && element.checked) {
                nonEmptyCount++;
            } else if (element.type === 'text' || element.type === 'email' || element.tagName.toLowerCase() === 'select') {
                if (element.value.trim() !== "") {
                    nonEmptyCount++;
                }
            }
        });
        const percentage = (nonEmptyCount / totalElements) * 100;
        progressBar.style.width = `${percentage}%`;
        progressBar.setAttribute('aria-valuenow', percentage);
        answer.value = nonEmptyCount;
        total.value = totalElements;
        percentSpan.textContent = `${Math.round(percentage)}%`;
    }

    elements.forEach(element => {
        element.addEventListener('input', updateProgressBar);
        if (element.type === 'checkbox' || element.type === 'radio') {
            element.addEventListener('change', updateProgressBar);
        }
    });

    updateProgressBar(); // Initial call to set the progress bar based on any pre-filled values
}); 
</script>


<!-- // Restriction of Age of house and heating system -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roofAgeInput = document.getElementById('roof_age');
        const heatingSystemAgeInput = document.getElementById('heating_system_age');
        const roofAgeError = document.getElementById('roof_age_error');
        const heatingSystemAgeError = document.getElementById('heating_system_age_error');

        function validateAges() {
            const roofAge = parseInt(roofAgeInput.value, 10);
            const heatingSystemAge = parseInt(heatingSystemAgeInput.value, 10);

            if (!isNaN(roofAge) && !isNaN(heatingSystemAge)) {
                if (heatingSystemAge > roofAge) {
                    heatingSystemAgeInput.style.borderColor = 'red';
                    heatingSystemAgeError.textContent = 'Das Alter der Heizungsanlage sollte nicht älter sein als das Alter des Daches.';
                } else {
                    heatingSystemAgeInput.style.borderColor = '';
                    heatingSystemAgeError.textContent = '';
                }
            }
        }

        roofAgeInput.addEventListener('input', validateAges);
        heatingSystemAgeInput.addEventListener('input', validateAges);
    });
 
        
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const heatingYearInput = document.getElementById('heating_system_year');
        const houseYearInput = document.getElementById('house_year');
        const heatingYearError = document.getElementById('heatingYearError');

        function validateYear(){
            const heatingYear = parseInt(heatingYearInput.value, 10);
            const houseYear = parseInt(houseYearInput.value, 10);

            if(!isNaN(heatingYear) && !isNaN(houseYear)){
                if(heatingYear < houseYear){
                    heatingYearInput.style.borderColor = 'red';
                    heatingYearError.textContent = 'Das Alter der Heizungsanlage sollte nicht älter sein als das Alter des Hauses.';
                } else {
                    heatingYearInput.style.borderColor = '';
                    heatingYearError.textContent = '';
                }
            }
        }

        heatingYearInput.addEventListener('input', validateYear);
        houseYearInput.addEventListener('input', validateYear);
    });
</script>


<!-- show the firma  -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
        const firmaContainer = document.getElementById('firma-container');

        function updateFirmaVisibility() {
            const selectedType = document.querySelector('input[name="customer_type"]:checked').value;
            if (selectedType === 'privat') {
                firmaContainer.style.display = 'none';
            } else {
                firmaContainer.style.display = 'block';
            }
        }

        customerTypeRadios.forEach(radio => {
            radio.addEventListener('change', updateFirmaVisibility);
        });

        updateFirmaVisibility(); // Initial call to set the visibility based on the pre-selected value
    });
</script>



 

@endsection