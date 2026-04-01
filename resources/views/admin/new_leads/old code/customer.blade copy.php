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
    font-size: 2rem; /* Adjust size of stars */
    cursor: pointer; /* Pointer cursor for interactivity */
    display: inline-flex; /* Inline layout for stars */
    gap: 0.5rem; /* Space between stars */
}

.star {
    color: #ccc; /* Default star color (gray) */
    transition: color 0.3s ease; /* Smooth color transition */
}

.star.selected,
.star.hovered {
    color: #9cc136; /* Highlighted color (green) */
}

.star:hover,
.star:hover ~ .star {
    color: #9cc136; /* Hover effect: highlight current and preceding stars */
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

.star.selected_star,
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
 
 .map_section {
    display: none;
}
</style>

<style>
    .card-fixed { 
      height: 100vh;
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
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
                        <h2 class="content-header-title float-left mb-0">KUNDE</h2>
                        <div class="breadcrumb-wrapper col-12">
                               <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                </li>
                                 <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Neue</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle waves-effect waves-light" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-settings"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item"  id="show_map">Kunde Ort Karte</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <form class="form form-horizontal custom-file-upload" method="post" id="customer_form"
                action="{{ action('App\Http\Controllers\NewLeadsController@store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <!-- Basic Horizontal form layout section start -->
                    <section id="basic-horizontal-layouts">
                        <div class="row match-height">
                            <div class="col-xl-5 col-md-12 col-sm-12  card-fixed">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                     
                                
                                <div class="row"> 
                                    <div class="col-12">
                                        <h2 class="content-header-title float-left mb-2 ">KUNDENINTERESSE</h2>
                                    </div> 
                                    <div class="col-12"> 
                                            <div class="card">
                                                @php
                                                        $imagePath = 'images/employee/';  
                                                        $male = 'images/gender/male.png';    
                                                        $female = 'images/gender/female.png';                                               
                                                    @endphp 
                                                    <div class="card-body">
                                                        <div class="table-responsive" >
                                                            <table class="table" id="product_table">
                                                                <button type="button" class="btn  btn-primary  mb-1 waves-effect waves-light float-right  "
                                                                    id="add_new">
                                                                    <i class="fa fa-plus"></i> Produkt und Dienstleistung hinzufügen
                                                                </button>
                                                                <thead>
                                                                    <tr>
                                                                        <th>Produkt</th>
                                                                        <th>Leistung</th>
                                                                        <th>Zuständigkeit</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody> 
                                                                  
                                                                </tbody>
                                                            </table> 
                                                        </div>
                                                    </div>
                                            </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-xl-7 col-md-12 col-sm-12 card-scrollable ">
                                <div class="row"> 
                                    <div class="col-4">
                                        <h2 class="content-header-title float-left ">KUNDENDATEN</h2>
                                    </div>
                                    <div class="col-8 mb-2"> 
                                        <div class="progress progress-bar-primary progress-lg">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
                                                <span id="percent">0%</span>
                                            </div>
                                        </div> 
                                    </div>
                                <input type="hidden" value="{{ old('id', $inquiry['id'] ?? 'normal') }}" name="from"> 
                                    <div class="col-6">
                                        <div class="col-12">
                                            <div class="form-group form-element"> 
                                                <div class="col-md-6">
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="d-inline-block mr-1">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type1" checked value="privat">
                                                                    <label class="custom-control-label" for="customer_type1">Privat</label>
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
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Additional form fields go here -->
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Anrede</span>
                                                </div>
                                                <div class="col-md-8 p-0"> 
                                                    <select class="form-control"  name="title">
                                                        <option selected></option>
                                                        <option value="Frau">Frau</option>
                                                        <option value="Herr">Herr</option>
                                                        <option value="Dr.">Dr.</option>
                                                        <option value="Prof.">Prof.</option>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 " id="firma-container">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Firma</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input type="text" id="firma" class="form-control form-element" value="{{ old('firma', $inquiry['firma'] ?? '') }}" name="firma">
                                                </div>
                                            </div>
                                        </div>
                                

                                        <div class="col-12 ">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Vorname</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input type="text" id="name" class="form-control form-element" value="{{ old('name', $inquiry['name'] ?? '') }}" name="name" autocomplete="off" list="name-options">
                                                    <datalist id="name-options">
                                                        <!-- Options will be populated by JavaScript -->
                                                    </datalist>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="col-12 ">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Nachname</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input type="text" id="lastname" class="form-control form-element" value="{{ old('lastname', $inquiry['lastname'] ?? '') }}" name="lastname" autocomplete="off" list="lastname-options">
                                                    <datalist id="lastname-options">
                                                        <!-- Options will be populated by JavaScript -->
                                                    </datalist>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Main Address Inputs -->
                                        <div class="col-12 ">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Straße / Nr.</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input id="location-input" type="text" class="form-control form-element" placeholder="Adresse eingeben" name="street" value="{{ old('street', $inquiry['street'] ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>PLZ</span>
                                                </div>
                                                <div class="col-md-8 p-0" style="padding-right: 4px !important;">
                                                    <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $inquiry['latitude'] ?? '') }}">
                                                    <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $inquiry['longitude'] ?? '') }}"> 
                                                    <input type="hidden" id="elevation-input"  name="elevation" value="{{ old('elevation', $inquiry['elevation'] ?? '') }}">
                                                    <input type="text" class="form-control form-element" value="{{ old('postcode', $inquiry['postcode'] ?? '') }}" name="postcode" id="postal_code-input">
                                                </div> 
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Ort</span>
                                                </div>
                                    
                                                <div class="col-md-8 p-0">
                                                    <input type="text" class="form-control form-element" value="{{ old('city', $inquiry['city'] ?? '') }}" name="city" id="city-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Festnetz</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input type="text" class="form-control" value="{{ old('telephone', $inquiry['telephone'] ?? '') }}" id="telephone-input" name="telephone"  >
                                                </div> 
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Handy</span>
                                                </div> 
                                                <div class="col-md-8 p-0">
                                                    <input type="text" class="form-control" value="{{ old('phone', $inquiry['phone'] ?? '') }}" name="phone" id="phone-input">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>E-Mail</span>
                                                </div>
                                                
                                                <div class="col-md-8 p-0">
                                                    <input type="email" class="form-control" id="email-input" value="{{ old('email', $inquiry['email'] ?? '') }}" name="email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Dringlichkeit</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <div class="star-rating form-element">
                                                        <select name="periority" id="" class="form-control form-element"> 
                                                            <Option value="Normal" @if(isset($inquiry->periority) && $inquiry->periority == 'Normal') selected @endif>Normal</Option>
                                                            <Option value="Dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Dringend') selected @endif>Dringend</Option>
                                                            <Option value="Sehr dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Sehr dringend') selected @endif>Sehr dringend</Option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Betrieb</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <div class="star-rating form-element">
                                                        <select name="branch_id" id="" class="form-control form-element">  
                                                            @foreach ($branch as $br)
                                                            <Option value="{{ $br->id }}"@if(isset($inquiry->branch_id) && $inquiry->branch_id == $br->id) selected  @endif>
                                                                {{ $br->branch }}
                                                            </Option> 
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 

                                        <!-- Alternative Address Inputs -->
                                        <div class="col-12">
                                            <div class="form-group row form-element">
                                                <fieldset>
                                                    <div class="vs-checkbox-con vs-checkbox-primary form-element">
                                                        <input type="checkbox" name="alternative_address" id="alternative_address" value="false">
                                                        <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <lable for="alternative_address">Das Bauvorhaben hat die gleiche Adresse</label>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="col-12" id="street2s">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Straße / Nr.</span>
                                                </div>
                                                <div class="col-md-8 p-0">
                                                    <input id="location-input2" type="text" class="form-control form-element" placeholder="Adresse eingeben" name="street2" value="{{ old('street2') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12" id="ort2s">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>PLZ / Ort</span>
                                                </div>
                                                <div class="col-md-4 p-0">
                                                    <input type="hidden" id="latitude-input2" name="latitude2" value="{{ old('latitude2') }}">
                                                    <input type="hidden" id="longitude-input2" name="longitude2" value="{{ old('longitude2') }}">
                                                    <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation2" value="{{ old('elevation2') }}">
                                                    <input type="text" class="form-control form-element" value="{{old('postcode2')}}" name="postcode2" id="postal_code-input2">
                                                </div>
                                                <div class="col-md-4" style="padding-right:0 !important;">
                                                    <input type="text" class="form-control form-element" value="{{old('city2')}}" name="city2" id="locality-input2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>   

                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-12 mt-3">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Quelle</span>
                                                    </div>
                                                    <div class="col-md-10 p-0">
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
                                            
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Info</span>
                                                    </div>
                                                    <div class="col-md-10 p-0">
                                                        <input type="hidden" class="form-control form-element" name="request_date" value="{{ now()->format('Y-m-d') }}" />
                                                        <input type="text" class="form-control form-element" name="info" value="{{ old('info') }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-6">
                                                        <span>Kunde aufgefordert Unterlagen zu schicken</span>
                                                    </div>
                                                    <div class="col-md-6">
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
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-4">
                                                        <span>erste Kontaktperson</span>
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
                                            <div class="row mb-2">
                                                <div class="col-md-5 col-6">
                                                    <span class="mr-2">Interesse</span>
                                                </div>
                                                <div class="col-md-7 col-6">
                                                    <div class="star-rating form-element d-flex justify-content-start" data-category="interest" data-rating="0">
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" name="interest_rating" value="0">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-5 col-6">
                                                    <span class="mr-2">Ernsthaftigkeit</span>
                                                </div>
                                                <div class="col-md-7 col-6">
                                                    <div class="star-rating form-element d-flex justify-content-start" data-category="seriousness" data-rating="0">
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" name="seriousness_rating" value="0">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-5 col-6">
                                                    <span class="mr-2">Preisinformation</span>
                                                </div>
                                                <div class="col-md-7 col-6">
                                                    <div class="star-rating form-element d-flex justify-content-start" data-category="price_information" data-rating="0">
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

                                            <div class="col-12" style="height: 20px;"></div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Notizen</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <textarea name="info"    style="text-align: left;width: 100%;height: 200px;border-radius: 7px;border: 1px solid #c6c6c6;"> 
                                                               {{ old('note', $inquiry['note'] ?? '') }}
                                                        </textarea>
                                                     
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
                                            <div class="col-12">
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
                                <div class="row">
                                    <div class="col-12">
                                        <div class="accordion" id="accordionExample" data-toggle-hover="true">  
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
                                                                            <h3 class="bold">Um welche Objektart handelt es sich?</h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                        <select name="objective" id="" class="form-control">
                                                                                <option value="">Bitte wählen</option>
                                                                                <option value="EFH">EFH</option>
                                                                                <option value="MFH">MFH</option>
                                                                                <option value="Gewerbe">Gewerbe</option>
                                                                                <option value="others">Sonstiges</option>
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
                                                                            <h3 class="bold">Wieviele Wohneinheiten hat das Objekt?</h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                            <input type="text" class="form-control textbox" name="number_we" value="{{ old('number_we') }}">
                                                                        
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row form-element">
                                                                        <div class="col-md-12">
                                                                            <h3 class="bold">Wieviele Geschosse hat das Objekt?   </h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                        <input type="text" class="form-control"  name="number_stories" value="{{ old('number_stories') }}">
                                                                        
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row form-element">
                                                                        <div class="col-md-12">
                                                                            <h3 class="bold">Wie groß ist die beheizte Wohnfläche?</h3>
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
                                                                            <h3 class="bold">Wieviele Personen wohnen in diesem Objekt?</h3>
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
                                                                            <h3 class="bold">Welche Art von Dach haben Sie?</h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                            <select class="form-control form-element" name="roof_type" id="roof">
                                                                                <option selected></option>
                                                                                <option value="Satteldach">Satteldach</option>
                                                                                <option value="Flachdach">Flachdach</option>
                                                                                <option value="Carport">Carport</option>
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
                                                                            <h3 class="bold">Welche Dachneigung hat ihr Dach?</h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                            <input type="text" class="form-control textbox" name="roof_pitch" value="{{ old('roof_pitch') }}"> 
                                                                        </div>
                                                                    </div>
                                                                </div> 

                                                                    <div class="col-12">
                                                                    <div class="form-group row form-element">
                                                                        <div class="col-md-12">
                                                                            <h3 class="bold">Welche Himmelsausrichtung hat ihr Dach?</h3>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                            <select name="roof_direction" id="" class="form-control"> 
                                                                                <option ></option>
                                                                                <option value="south">Süden </option>
                                                                                <option value="south-west">Süd-west </option>
                                                                                <option value="west">Westen </option>
                                                                                <option value="north-west">Nord-west </option>
                                                                                <option value="north">Norden </option>
                                                                                <option value="north-east">Nord-ost </option>
                                                                                <option value="east">Osten </option>
                                                                                <option value="south-east">Süd-ost </option>  
                                                                                <option value="east-west">Ost-West</option>  
                                                                                <option value="north-south">Nord-Süd</option>  
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
                                                                                <option value="underfloor_heating">Fußbodenheizung</option>
                                                                                <option value="heating_system">Heizkörper</option>
                                                                                <option value="both" >Fußbodenheizung + Heizkörper</option>
                                                                                <option value="none">Keine</option>
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
                                                                            <input type="text" class="form-control" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra')}}" placeholder="sonstiges">
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
                                                                            <span  id="heat-energy">m³</span>
                                                                            <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh')}}" /> 
                                                                            <span >kWh</span>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12"><h2 class="primary"><strong>E-MOBILITÄT</strong></h2><hr></div> 

                                                                <div class="col-12">
                                                                    <div class="form-group row form-element">
                                                                        <div class="col-md-12">
                                                                            <h3 class="bold" >Haben Sie ein Elektroauto? Oder planen Sie eins zu kaufen?</h3>
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
                                                                            <span style="display:none;position: absolute; right: 20px;"  id="electric_car_plan_l">Anzahl</span>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group row form-element">
                                                                        <div class="col-md-12">
                                                                            <h3 class="bold">Wieviele Kilometer hat das Auto gefahren? (Alle Kilometer addieren)</h3>
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
                                    </div>
                                </div>
                            </div>
                            
                            <!-- map section:start -->
                            <div class="col-xl-6 col-md-12 map_section " style="     position: absolute;   right: 41px;" > 
                                <div class="col-12">
                                    <!-- {{-- Map Start --}} -->
                                    <div class="card">
                                        <div class="card-header" style="align-self: center;">
                                            <h2 class="content-header-title float-left">OBJEKTBILDER</h2>
                                            <button type="button" class="btn btn-outline-warning ml-4 mb-1 waves-effect waves-light float-left close-map">Schließen</button>
                                        </div>
                                        <div class="card-content">
                                            <div class="card-body"> 
                                                <div class="map" id="gmp-map" style="width: 100%; position: relative; overflow: hidden; height: 356px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- {{-- Map End --}} -->
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
                                <input type="file" class="d-none" id="screenshot-file-input" name="screenshot_file" class="form-control" /> 
                            </div>
                            <!-- map section:end -->

                        </div>
                        <div class="col-md-2" style=" position: fixed; top: 85%; right: 20px; ">  
                            <div id="status-icon" class="text-right mt-3">
                                <!-- Status icon will be displayed here -->
                            </div>
                            <div class="button">
                            <button type="submit" class="btn btn-primary round mr-1 mb-1 waves-effect waves-light float-right"><i class="feather icon-arrow-right"></i> speichern</button>
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
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>

<!-- SISSION AND SELECT2 -->

<script src="{{ asset('js/select2.min.js') }}"></script> 
<script>
    $(document).ready(function() { 
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


<!-- selecting Product with heart:start  -->
  
<script>
    $(document).ready(function () {
        // Initialize Select2 with custom rendering for images
        $('.selectGroup').select2({ 
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
                    <span class="black">${option.text}</span>
                </div>`
            );
            return $option;
        }
    });
</script>




<!-- selecting Product with heart:end  -->

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
                    star.classList.add('selected_star');
                    star.classList.remove('hovered');
                } else {
                    star.classList.remove('selected_star');
                    star.classList.remove('hovered');
                }
            });
        }

        // Initialize the stars based on the current rating
        starRatings.forEach(rating => updateStars(rating));
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
<!-- <script>
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
</script> -->


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



<!-- Save operation: start  -->
 <script>
    $(document).ready(function () {
        // Handle form submission
        $('#customer_form').submit(function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Collect all form data
            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'), // Use the form's action attribute
                type: $(this).attr('method'), // Use the form's method attribute
                data: formData,
                processData: false, // Prevent jQuery from automatically transforming the data into a query string
                contentType: false, // Let the server set the Content-Type header
                success: function (response) {
                    if (response.success) {
                        // SweetAlert for success
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            // Redirect to the URL provided by the server
                            window.location.href = response.redirect;
                        });
                    } else {
                        // SweetAlert for generic error
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong: ' + response.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                },
                error: function (xhr) {
                    // SweetAlert for AJAX error
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'An error occurred.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                    console.error(xhr.responseText); // Log the error for debugging
                },
            });
        });
    });
</script>

<!-- Save operation: end -->

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
  <!-- Map Section: end  -->


  <!-- check the duplicate record of the lead: start -->
  


<!-- Map and screenshots  -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>
    
    <script>
    "use strict";

    let map;
    let marker;
    let elevationService;
    let autocompleteMain;

    // Initialize Google Map and Autocomplete
    function initMap() {
        const DEFAULT_LOCATION = { lat: 50.1109, lng: 8.6821 }; // Default location (Frankfurt, Germany)

        // Initialize map
        map = new google.maps.Map(document.getElementById("gmp-map"), {
            center: DEFAULT_LOCATION,
            zoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
        });

        // Initialize marker
        marker = new google.maps.Marker({
            map: map,
            position: DEFAULT_LOCATION,
            draggable: true,
        });

        // Initialize elevation service
        elevationService = new google.maps.ElevationService();

        // Initialize Autocomplete
        autocompleteMain = new google.maps.places.Autocomplete(document.getElementById("location-input"), {
            fields: ["address_components", "geometry", "name"],
            types: ["address"],
        });

        // Listener for autocomplete place change
        autocompleteMain.addListener("place_changed", handlePlaceChange);

        // Listener for marker drag
        marker.addListener("dragend", () => {
            const position = marker.getPosition();
            updateLatLng(position.lat(), position.lng());
            getElevation(position);
            checkCustomer();
        });
    }

    // Handle place change from autocomplete
    function handlePlaceChange() {
        const place = autocompleteMain.getPlace();

        if (!place.geometry) {
            Swal.fire({
                icon: "error",
                title: "Ungültige Adresse",
                text: `Keine Details verfügbar für "${place.name}". Bitte wählen Sie eine andere Adresse.`,
            });
            return;
        }

        const location = place.geometry.location;

        // Update map and marker position
        map.setCenter(location);
        marker.setPosition(location);

        // Update form fields
        fillInAddress(place);
        updateLatLng(location.lat(), location.lng());
        getElevation(location);

        // Check for duplicate or neighboring customers
        checkCustomer();
    }

    // Update latitude and longitude fields
    function updateLatLng(lat, lng) {
        document.getElementById("latitude-input").value = lat.toFixed(6);
        document.getElementById("longitude-input").value = lng.toFixed(6);
    }

    // Fill in address fields based on the selected place
    function fillInAddress(place) {
        const addressComponents = {
            street_number: "",
            route: "",
            locality: "", // City
            postal_code: "",
        };

        place.address_components.forEach((component) => {
            component.types.forEach((type) => {
                if (addressComponents[type] !== undefined) {
                    addressComponents[type] = component.long_name;
                }
            });
        });

        const streetNumber = addressComponents.street_number;
        const route = addressComponents.route;

        document.getElementById("location-input").value = `${route} ${streetNumber}`;
        document.getElementById("postal_code-input").value = addressComponents.postal_code;
        document.getElementById("city-input").value = addressComponents.locality; // Update city field
    }

    // Get elevation based on location
    function getElevation(location) {
        elevationService.getElevationForLocations(
            { locations: [location] },
            (results, status) => {
                if (status === "OK" && results[0]) {
                    const elevation = results[0].elevation;
                    document.getElementById("elevation-input").value = elevation.toFixed(2);
                } else {
                    console.error("Elevation service failed:", status);
                }
            }
        );
    }

    // Check for duplicate or neighboring customers
    function checkCustomer() {
        const street = $("#location-input").val().trim();
        const postcode = $("#postal_code-input").val().trim();
        const latitude = $("#latitude-input").val().trim();
        const longitude = $("#longitude-input").val().trim();

        if (!street || !postcode || !latitude || !longitude) {
            Swal.fire({
                icon: "warning",
                title: "Unvollständige Eingabe",
                text: "Bitte geben Sie alle erforderlichen Felder ein.",
            });
            return;
        }

        const url = `/check-new-leads/${encodeURIComponent(street)}/${postcode}/${latitude}/${longitude}`;

        $.ajax({
            url: url,
            method: "GET",
            success: function (response) {
                if (response.status === "neighbor") {
                    renderNeighborTable(response.customers);
                } else if (response.status === "duplicate") {
                    Swal.fire({
                        icon: "warning",
                        title: "Doppelte Kunden gefunden",
                        html: `
                            <strong>Name:</strong> ${response.customer.name} ${response.customer.lastname}<br>
                            <strong>Adresse:</strong> ${response.customer.street}, ${response.customer.postcode}<br>
                            <a href="/new_lead_profile/${response.customer.id}" class="btn btn-primary btn-sm" target="_blank">Profil ansehen</a>
                        `,
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Kein Treffer",
                        text: "Keine passenden oder benachbarten Einträge gefunden.",
                    });
                }
            },
            error: function (err) {
                console.error("AJAX Error:", err);
                Swal.fire({
                    icon: "error",
                    title: "Fehler",
                    text: "Ein Fehler ist beim Überprüfen des Kunden aufgetreten.",
                });
            },
        });
    }

    // Render the neighbor table
    function renderNeighborTable(neighbors) {
        if (!neighbors || neighbors.length === 0) {
            Swal.fire({
                icon: "info",
                title: "Keine Nachbarn gefunden",
                text: "Es wurden keine Nachbarn in der Nähe gefunden.",
            });
            return;
        }

        let tableRows = neighbors.map(neighbor => `
            <tr>
                <td>${neighbor.name} ${neighbor.lastname}</td>
                <td>${neighbor.street}</td>
                <td>${neighbor.postcode}</td>
                <td>${neighbor.city}</td>
                <td>${neighbor.distance.toFixed(2)} km</td>
                <td><a href="/new_lead_profile/${neighbor.id}" class="btn btn-primary btn-sm" target="_blank">Profil ansehen</a></td>
            </tr>
        `).join("");

        const tableHtml = `
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Street</th>
                        <th>Postcode</th>
                        <th>City</th>
                        <th>Distance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        `;

        Swal.fire({
            title: "Nachbarn gefunden",
            html: tableHtml,
            width: "80%",
            showCloseButton: true,
        });
    }

    // Initialize the map on DOM load
    document.addEventListener("DOMContentLoaded", () => {
        initMap();
    });
</script>


<!-- Map and screenshots: end  -->


<!-- new product script:  -->
 
<script>
    let rowIndex = 0; // Index for dynamic rows

    $(document).ready(function () {
        // Add a new row
        $('#add_new').click(function () {
            // Validate existing rows
            let isValid = true;

            $('#product_table tbody tr').each(function () {
                const product = $(this).find('[name*="[product_id]"]').val();
                const service = $(this).find('[name*="[service]"]').val();
                const employee = $(this).find('[name*="[employee_id]"]').val();

                if (!product || !service || !employee) {
                    isValid = false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validierungsfehler',
                    text: 'Bitte füllen Sie alle Felder in den vorhandenen Zeilen aus, bevor Sie eine neue Zeile hinzufügen!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Add a new row
            $('#product_table tbody').append(`
                <tr>
                    <td class="p-0">
                        <select name="product[${rowIndex}][product_id]" class="form-control select2" style="width: 100%;">
                            <option value="">Produkt auswählen</option>
                            @foreach ($articles as $item)
                                <option value="{{ $item->id }}" data-image="{{ asset('images/articles/'.$item->image) }}">
                                    {{ $item->article_group }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-0">
                        <select name="product[${rowIndex}][service]" class="form-control select2" style="width: 100%;">
                            <option value="">Leistung auswählen</option>
                            <option value="complete">Komplettlösung</option>
                            <option value="montage">Montage</option>
                            <option value="product">Produkt</option>
                            <option value="plan">Planung</option>
                            <option value="maintenance">Wartung</option>
                            <option value="repair">Reparatur</option>
                            <option value="others">Sonstiges</option>
                        </select>
                    </td>
                    <td class="p-0">
                        <select name="product[${rowIndex}][employee_id]" class="form-control select2" style="width: 100%;">
                            <option value="">Verantwortlicher auswählen</option>
                            @foreach ($employees as $emp)
                                @php
                                    $defaultImage = $emp->gender === "Male" ? $male : $female;
                                    $imageUrl = $emp->image ? asset($imagePath . $emp->image) : asset($defaultImage);
                                @endphp
                                <option value="{{ $emp->id }}" data-image="{{ $imageUrl }}">
                                    {{ $emp->name }} {{ $emp->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-0">
                        <button type="button" class="btn btn-icon rounded-circle btn-danger waves-effect waves-light" id="delete_row">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            // Reinitialize select2 on new rows
            $('.select2').select2();

            // Increment the row index
            rowIndex++;
        });

        // Remove a row
        $(document).on('click', '#delete_row', function () {
            $(this).closest('tr').remove();
        });

        // Initialize select2 on load
        $('.select2').select2();
    });
</script>

@endsection