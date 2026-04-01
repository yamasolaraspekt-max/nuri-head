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
        position: absolute !important;
        right: 23px !important;
        top: 121px !important;
        background: white !important;
        padding-top: 20px !important;
    }
</style>

<style>
    .card-fixed {
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
    .card {
       box-shadow: 0 0 !important;
    }

    .text {
         
            border-radius: 0;
            border:0 !important;
    }

    .input-class {
        padding: 0;
        padding-right: 2px !important;
    }

    .form-group {
            margin-bottom: 8px !important;
    }
    .custom-control-label {
        font-size:13px !important;
        color:#626262;
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
        position: relative;
    }
    .wizard-step {
        position: relative;
        flex: 1;
        text-align: center;
        padding: 10px 5px;
        background: #e9ecef;
        border-radius: 5px;
        margin-right: 5px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .wizard-step.active {
        background: #8fc73e;
        color: white;
        font-weight: bold;
    }
    .wizard-step::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 4px;
        background: #ccc;
        z-index: 0;
    }
    .wizard-step:last-child::after {
        display: none;
    }
    .wizard-progress-count {
        display: block;
        font-size: 0.8rem;
        font-weight: normal;
    }

    .tab-pane .row { 
        padding-right: 20px;
        padding-left: 20px;
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
                        <h2 class="content-header-title float-left mb-0">LEADS</h2>
                        <div class="breadcrumb-wrapper col-12">
                               <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a>
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
                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light" id="show_map"><i class="feather icon-map"></i></button>
                     
                </div>
            </div>
        </div>
               
                                     
        <div class="content-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form class="form form-horizontal" method="post" id="customer_form"
                    action="{{ route('new.lead.store')}}"
                    enctype="multipart/form-data">
                    @csrf
                     @php
                        $imagePath = 'images/employee/';
                        $male = 'images/gender/male.png';
                        $female = 'images/gender/female.png';
                    @endphp 
                    <section id="basic-horizontal-layouts">
                        <div class="row match-height"> 
                            <div class="col-xl-12 col-md-12 col-sm-12 card-scrollable " id="customer_data">
                                <div class="row">
                                    <div class="col-4">
                                        <h2 class="content-header-title float-left primary ">KUNDENDATEN</h2>
                                    </div>
                                   <!-- <div class="col-8 mb-2">
                                        <input type="hidden" name="answer_input" id="answer_input" value="0">
                                        <input type="hidden" name="total_number_input" id="total_number_input" value="30">
                                        <label for="" id="answered_number">0</label> / <label for="" id="total_number">30</label>
                                        
                                        <div class="progress progress-bar-primary progress-lg">
                                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                                <span id="percent">0%</span>
                                            </div>
                                        </div>
                                    </div> -->

                                     <input type="hidden" value="{{ old('id', $inquiry['id'] ?? 'normal') }}" name="from">
                                    <div class="col-6 ">
                                        <div class="col-12">
                                            <div class="form-group form-element">
                                                <div class="col-md-6 mt-1"  style="padding-left: 0px !important;">
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
                                        
                                        <div class="col-12 " id="firma-container">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Firma</span>
                                                </div>
                                                <div class="col-md-8 input-class">
                                                    <input type="text" id="firma" class="form-control text form-element" value="{{ old('firma', $inquiry['firma'] ?? '') }}" name="firma">
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="col-12 ">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Vorname / Nachname</span>
                                                </div>
                                                <div class="col-md-2 input-class">
                                                    <select class="form-control text"  name="title">
                                                        <option selected></option>
                                                        <option value="Frau">Frau</option>
                                                        <option value="Herr">Herr</option>
                                                        <option value="Dr.">Dr.</option>
                                                        <option value="Prof.">Prof.</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3 input-class">
                                                    <input type="text" id="name" class="form-control text form-element" value="{{ old('name', $inquiry['name'] ?? '') }}"  placeholder="Vorname"  name="name" autocomplete="off" list="name-options">
                                                    <datalist id="name-options">
                                                        <!-- Options will be populated by JavaScript -->
                                                    </datalist>
                                                </div>
                                                <div class="col-md-3 input-class">
                                                    <input type="text" id="lastname" class="form-control text form-element" placeholder="Nachname" value="{{ old('lastname', $inquiry['lastname'] ?? '') }}" name="lastname" autocomplete="off" list="lastname-options">
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
                                                    <span>Str./Nr./PLZ/Ort</span>
                                                </div>
                                                <div class="col-md-8 input-class">
                                                    <input id="full_address" type="text" class="form-control text form-element" placeholder="Adresse eingeben" name="full_address" value="{{ old('full_address', $inquiry['full_address'] ?? '') }}">
                                                 
                                                    <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $inquiry['latitude'] ?? '') }}">
                                                    <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $inquiry['longitude'] ?? '') }}">
                                                    <input type="hidden" id="elevation-input"  name="elevation" value="{{ old('elevation', $inquiry['elevation'] ?? '') }}">
                                                    <input type="hidden" class="form-control text form-element" value="{{ old('postcode', $inquiry['postcode'] ?? '') }}" name="postcode" id="postal_code-input">
                                                    <input type="hidden" class="form-control text form-element" value="{{ old('street', $inquiry['street'] ?? '') }}" name="street" id="street-input">
                                                    <input type="hidden" class="form-control text form-element" value="{{ old('city', $inquiry['city'] ?? '') }}" name="city" id="city-input">
                                                </div>
                                             </div>
                                        </div>
                                         
                                
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Festnetz/Handy</span>
                                                </div>
                                                <div class="col-md-4 input-class">
                                                    <input type="text" class="form-control text" value="{{ old('telephone', $inquiry['telephone'] ?? '') }}" id="telephone-input" placeholder="Festnetz" name="telephone"  >
                                                </div>
                                                 <div class="col-md-4 input-class">
                                                    <input type="text" class="form-control text" value="{{ old('phone', $inquiry['phone'] ?? '') }}" name="phone" placeholder="Handy" id="phone-input">
                                                </div>
                                            </div>
                                        </div>
 
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>E-Mail</span>
                                                </div>
                                                
                                                <div class="col-md-8 input-class">
                                                    <input type="email" class="form-control text" id="email-input" value="{{ old('email', $inquiry['email'] ?? '') }}" name="email">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Dringlichkeit/Betrieb</span>
                                                </div>
                                                <div class="col-md-4 input-class">
                                                    <div class="form-element">
                                                        <select name="periority" id="" class="form-control text form-element">
                                                            <Option value="Normal" @if(isset($inquiry->periority) && $inquiry->periority == 'Normal') selected @endif>Normal</Option>
                                                            <Option value="Dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Dringend') selected @endif>Dringend</Option>
                                                            <Option value="Sehr dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Sehr dringend') selected @endif>Sehr dringend</Option>
                                                        </select>
                                                    </div>
                                                </div>
                                                 <div class="col-md-4 input-class">
                                                    <div class="form-element">
                                                        <select name="branch_id" id="" class="form-control text form-element" style="width:100%">
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
                                                        <input type="checkbox" name="alternative_address" id="alternative_address" value="true" checked>
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

                                        <!-- Alternative Address -->
                                        <div class="col-12" id="street2s">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Str./Nr./PLZ/Ort</span>
                                                </div>
                                                <div class="col-md-8 input-class">
                                                    <input id="full_address2" type="text" class="form-control text form-element" placeholder="Adresse eingeben" name="full_address2" value="{{ old('full_address2') }}">
                                                     <input type="hidden" id="street-input2" name="street2" value="{{ old('street2') }}">
                                                     <input type="hidden" id="latitude-input2" name="latitude2" value="{{ old('latitude2') }}">
                                                    <input type="hidden" id="longitude-input2" name="longitude2" value="{{ old('longitude2') }}">
                                                    <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation2" value="{{ old('elevation2') }}">
                                                    <input type="hidden" class="form-control text form-element" value="{{old('postcode2')}}" name="postcode2" id="postal_code-input2">
                                                    <input type="hidden" class="form-control text form-element" value="{{old('city2')}}" name="city2" id="locality-input2">

                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <div class="col-6">
                                        <div class="row">
                                            <div class="col-12 mt-3">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Quelle / Anfragedatum</span>
                                                    </div>
                                                    <div class="col-md-5 input-class">
                                                        <select name="source" id="source" class="form-control" style="width: 100%">
                                                            <option></option>
                                                            <option value="Telefonisch">Telefonisch</option>
                                                            <option value="Persönlich">Persönlich</option>
                                                            <option value="Mail">Mail</option>
                                                            <option value="Nachbar">Nachbar</option>
                                                            <option value="Empfehlung">Empfehlung</option>
                                                            <option value="Solarrechner">Solarrechner</option>
                                                            <option value="Herstellerlead">Herstellerlead</option>
                                                            <option value="Event">Event</option>
                                                            <option value="Messe">Messe</option>
                                                            <option value="Hausmesse">Hausmesse</option>
                                                            <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                                                        </select>


                                                    </div>
                                                    <div class="col-md-5 input-class">
                                                        <input type="date" class="form-control text form-element" name="request_date" value="{{ now()->format('Y-m-d') }}" />
                                                        <input type="hidden" name="contact_person" class="form-control text form-element"  value="{{ auth()->user()->name}}" >
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                    <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Info</span>
                                                    </div>
                                                    <div class="col-md-10 input-class">
                                                        <input type="text" class="form-control text form-element" name="info" value="{{ old('info') }}" placeholder="Info">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-6 mt-1">
                                                        <span>Kunde aufgefordert Unterlagen zu schicken</span>
                                                    </div>
                                                    <div class="col-md-6 mt-1">
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
                                            
                                            
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12">
                                                <div class="row mb-2">
                                                    <div class="col-md-4 col-6">
                                                         <span class="mr-2">Interesse</span>
                                                        <div class="star-rating form-element d-flex justify-content-start" data-category="interest" data-rating="0">
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                        </div>
                                                        <input type="hidden" name="interest_rating" value="0">
                                                    </div>
                                                    <div class="col-md-4 col-6">
                                                           <span class="mr-2">Ernsthaftigkeit</span>
                                                        <div class="star-rating form-element d-flex justify-content-start" data-category="seriousness" data-rating="0">
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                        </div>
                                                        <input type="hidden" name="seriousness_rating" value="0">
                                                    </div>
                                                     <div class="col-md-4 col-6">
                                                           <span class="mr-2">Preisinformation</span>
                                                        <div class="star-rating form-element d-flex justify-content-start" data-category="price_information" data-rating="0">
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                        </div>
                                                        <input type="hidden" name="price_information_rating" value="0">
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
                                                        <textarea name="info"    style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">
                                                               {{ old('note', $inquiry['note'] ?? '') }}
                                                        </textarea>
                                                     
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-4">
                                                        <span>Termin für die Erstberatung vorhanden?</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date" class="form-control text form-element" name="appointment">
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


                                <div class="row mt-2">
                                    <div class="card mb-1 shadow-sm">
                                        <div class="card-header  d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="mb-0"><i class="feather icon-box"></i> Produkt, Dienstleistung , Abteilung und  personal  hinzufügen</h5>
                                            <button type="button" class="btn btn-primary" id="addRow">
                                                <i class="feather icon-plus"></i> 
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover mb-0" id="inquiryProductTable">
                                                    <thead class="thead-light text-center">
                                                        <tr>
                                                            <th>Produkt</th>
                                                            <th>Dienstleistung</th>
                                                            <th>Abteilung</th>
                                                            <th>Mitarbeiter</th>
                                                            <th>Interesse</th>
                                                            <th>Aktion</th>
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
 

                                <div class="row mt-2"> 
                                    <div class="col-12 p-0">

                                        <div class="cards">

                                            <div class="card-header  d-flex justify-content-between align-items-center mb-2">
                                                 <h5 class="mb-0"><i class="feather icon-settings"></i> Energieverbrauch & Objektdaten</h5> 
                                               
                                            </div>

                                            <div class="card-body p-0">
                                                <div class="wizard-nav">
                                                    <div class="wizard-step active" onclick="showTab(1)">
                                                            <i class="feather icon-home mr-1"></i> Objektdaten 
                                                            <span class="wizard-progress-count" id="step1-count">(0/8)</span>
                                                        </div>
                                                        <div class="wizard-step" onclick="showTab(2)">
                                                            <i class="feather icon-layers mr-1"></i> Dachinformation 
                                                            <span class="wizard-progress-count" id="step2-count">(0/6)</span>
                                                        </div>
                                                        <div class="wizard-step" onclick="showTab(3)">
                                                            <i class="feather icon-zap mr-1"></i> Heizungsinformation
                                                            <span class="wizard-progress-count" id="step3-count">(0/5)</span>
                                                        </div>
                                                        <div class="wizard-step" onclick="showTab(4)">
                                                            <i class="feather icon-activity mr-1"></i> Energieverbrauch 
                                                            <span class="wizard-progress-count" id="step4-count">(0/3)</span>
                                                        </div>
                                                        <div class="wizard-step" onclick="showTab(5)">
                                                            <i class="feather icon-cpu mr-1"></i> E-Mobilität 
                                                            <span class="wizard-progress-count" id="step5-count">(0/3)</span>
                                                        </div>

                                                </div>

                                                <div class="tab-content pt-2">
                                                    <div class="tab-pane active" id="step1" role="tabpanel">
                                                        @include('admin.new_leads.partials.object_data')
                                                    </div>
                                                    <div class="tab-pane" id="step2" role="tabpanel">
                                                        @include('admin.new_leads.partials.roof_info')
                                                    </div>
                                                    <div class="tab-pane" id="step3" role="tabpanel">
                                                        @include('admin.new_leads.partials.heating_info')
                                                    </div>
                                                    <div class="tab-pane" id="step4" role="tabpanel">
                                                        @include('admin.new_leads.partials.energy_usage')
                                                    </div>
                                                    <div class="tab-pane" id="step5" role="tabpanel">
                                                        @include('admin.new_leads.partials.e_mobility')
                                                    </div>
                                                </div>

                                                <div class="mt-3 text-right">
                                                    <button class="btn btn-secondary" type="button" onclick="navigateTab(-1)">Zurück</button>
                                                    <button class="btn btn-primary" type="button" onclick="navigateTab(1)">Weiter</button>
                                                </div>
                                            </div>
                                           
                                        </div> 
                                    </div>
                                </div>
                            
                             
                            </div>
                            
                            <!-- map section:start -->
                            <div class="col-xl-9 col-md-12 map_section" >
                                <div class="col-12">
                                    <!-- {{-- Map Start --}} -->
                                    <div class="cards">
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
                                    <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn"  >
                                        <i class="feather icon-camera"></i> Screenshot
                                    </a>
                                </div>
                                <div id="screenshot-preview"></div>

                                <!-- Hidden file input to store the screenshot data -->
                                <input type="file" class="d-none" id="screenshot-file-input" name="screenshot_file" class="form-control text" />
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
 


 
<script>
const productImage = "{{ asset('images/articles/') }}";
const employeeImage = "{{ asset('images/employee/') }}";

$(document).ready(function () {
    let rowIndex = 0;
    const services = @json($services);
    const products = @json($products);
    const departments = @json($departments);

    $('#addRow').click(function () {
        let lastRow = $('#inquiryProductTable tbody tr:last');
        if (lastRow.length > 0) {
            const index = lastRow.data('index');
            const product = $(`.product-select[data-index="${index}"]`).val();
            const service = $(`.service-select[data-index="${index}"]`).val();
            const department = $(`.department-select[data-index="${index}"]`).val();
            const employee = $(`.employee-select[data-index="${index}"]`).val();

            let missingFields = [];
            if (!product) missingFields.push('Produkt');
            if (!service) missingFields.push('Service');
            if (!department) missingFields.push('Abteilung');
            if (!employee) missingFields.push('Mitarbeiter');

            if (missingFields.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: `Zeile ${index + 1} unvollständig`,
                    html: `Bitte füllen Sie folgende Felder aus: <strong>${missingFields.join(', ')}</strong>`,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-danger' },
                    buttonsStyling: false
                });
                return;
            }
        }

        rowIndex++;
        const newRow = `
            <tr data-index="${rowIndex}" class="align-middle">
                <td>
                    <select class="form-select product-select" name="product_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Produkt wählen</option>
                        ${products.map(p => `<option value="${p.id}" data-img="${p.image}">${p.article_group}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-select service-select" name="service_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Service wählen</option>
                    </select>
                </td>
                <td>
                    <select class="form-select department-select" name="department_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Abteilung wählen</option>
                        ${departments.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-select employee-select" name="employee_id[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="">Mitarbeiter wählen</option>
                    </select>
                </td>

                <td>
                    <select class="form-select interest-select" name="interest[]" data-index="${rowIndex}" style="width:100% !important;">
                        <option value="intent">Kaufabsicht</option>
                        <option value="interest">Kaufinteresse</option>
                        <option value="option">Kaufoption</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="Entfernen">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>`;

        $('#inquiryProductTable tbody').append(newRow);
        initializeSelect2(rowIndex);
    });


    // Select2 initializer for all dropdowns
    function initializeSelect2(index) {
        const productSel = `.product-select[data-index="${index}"]`;
        const serviceSel = `.service-select[data-index="${index}"]`;
        const deptSel = `.department-select[data-index="${index}"]`;
        const empSel = `.employee-select[data-index="${index}"]`;
        const interestSel = `.interest-select[data-index="${index}"]`;

        $(productSel).select2().on('change', function () {
            loadServices(index);
            loadEmployees(index);
        });

        $(serviceSel).select2().on('change', function () {
            loadEmployees(index);
        });

        $(deptSel).select2().on('change', function () {
            loadEmployees(index);
        });


        $(interestSel).select2().on('change', function () {
            loadEmployees(index);
        });

        $(empSel).select2({
            templateResult: formatEmployee,
            templateSelection: formatEmployeeSelection,
            escapeMarkup: m => m
        });
    }

    // Load services for selected product
    function loadServices(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const $service = $(`.service-select[data-index="${index}"]`);

        $service.empty().append('<option value="">Service wählen</option>');
        services.forEach(s => {
            if (s.product_id == productId) {
                $service.append(`<option value="${s.id}">${translateService(s.phase_section)}</option>`);
            }
        });

        $service.trigger('change');
    }

    // Load employees based on product, service, and department
    function loadEmployees(index) {
        const productId = $(`.product-select[data-index="${index}"]`).val();
        const departmentId = $(`.department-select[data-index="${index}"]`).val();
        const serviceId = $(`.service-select[data-index="${index}"]`).val();
        const $employeeSelect = $(`.employee-select[data-index="${index}"]`);

        if (productId && departmentId && serviceId) {
            $.post('{{ route("inquiry.department.employees") }}', {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                department_id: departmentId,
                service_id: serviceId
            }, function (data) {
                $employeeSelect.empty().append('<option value="">Mitarbeiter wählen</option>');
                if (data.length > 0) {
                    data.forEach(emp => {
                        $employeeSelect.append(
                            `<option value="${emp.id}" data-img="${emp.image}" data-positions="${emp.positions.join(', ')}">${emp.name} ${emp.lastname}</option>`
                        );
                    });
                    setTimeout(() => {
                        $employeeSelect.val(data[0].id).trigger('change');
                    }, 100); // Let DOM update before setting default
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keine Mitarbeiter gefunden',
                        text: 'Für diese Auswahl existieren keine Mitarbeiter.',
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-warning' },
                        buttonsStyling: false
                    });
                }

                $employeeSelect.select2({
                    templateResult: formatEmployee,
                    templateSelection: formatEmployeeSelection,
                    escapeMarkup: m => m
                });
            });
        } else {
            $employeeSelect.empty().append('<option value="">Mitarbeiter wählen</option>').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployeeSelection,
                escapeMarkup: m => m
            });
        }
    }

    // Custom employee display
    function formatEmployee(emp) {
        if (!emp.id) return emp.text;
        const img = $(emp.element).data('img') ? `${employeeImage}/${$(emp.element).data('img')}` : '';
        const pos = $(emp.element).data('positions') || '';
        return `
            <div style="display:flex;align-items:center;">
                <img src="${img}" class="me-2 rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                <div><strong>${emp.text}</strong><br><small>${pos}</small></div>
            </div>`;
    }

    function formatEmployeeSelection(emp) {
        return emp.text;
    }

    function translateService(s) {
        switch (s?.toLowerCase()) {
            case 'complete': return 'komplettlösung';
            case 'montage': return 'Montage';
            case 'product': return 'Kaufen';
            case 'plan': return 'Planung';
            case 'maintenance': return 'Wartung';
            case 'repair': return 'Reparatur';
            case 'others': return 'Sonstiges';
            default: return s;
        }
    }

    // Proper delete handling (for both new & old rows)
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').fadeOut(200, function () {
            $(this).remove();
        });
    });

    // Optional: Initialize existing rows on page load
    $('tbody tr').each(function () {
        const idx = $(this).data('index');
        initializeSelect2(idx);
    });
});
</script>
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
    $('#lastname, #name, #street, #postal_code-input, #city-input').on('change', function() {
        // Collect form data
        var lastname = $('#lastname').val();
        var name = $('#name').val();
        var street = $('#street').val();
        var postcode = $('#postal_code-input').val();
        var city = $('#city-input').val();

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
            const street = document.getElementById('full_address').value.trim();
            const postcode = document.getElementById('postal_code-input').value.trim();
            const city = document.getElementById('city-input').value.trim();
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
    }
    else if(this.value === 'Geplant'){
         electricCarPlan.style.display = 'block';
        label.style.display = 'block';
    }
     else {
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
    const unitLabel = document.getElementById('unit_energy');
    const energyInput = document.getElementById('annual_heating_energy_consumption');
    const kwhInput = document.getElementById('annual_heating_energy_consumption_kwh');
    const systemSelect = document.getElementById('heating_system_type');

    let currentUnit = 'm³'; // default
    let factor = 10; // default for Gas: 1m³ = 10kWh

    const updateUnit = (value) => {
        switch (value) {
            case 'Gas':
                unitLabel.textContent = 'm³';
                currentUnit = 'm³';
                factor = 10;
                break;
            case 'Öl':
                unitLabel.textContent = 'Liter';
                currentUnit = 'Liter';
                factor = 10; // adjust if needed
                break;
            case 'Wärmepumpe':
            case 'Nachtspeicher':
                unitLabel.textContent = 'kWh';
                currentUnit = 'kWh';
                factor = 1;
                break;
            default:
                unitLabel.textContent = 'kWh';
                currentUnit = 'kWh';
                factor = 1;
        }
    };

    systemSelect.addEventListener('change', function () {
        updateUnit(this.value);
        convertBoth();
    });

    function convertBoth() {
        let energy = parseFloat(energyInput.value);
        let kwh = parseFloat(kwhInput.value);

        if (!isNaN(energy) && document.activeElement === energyInput) {
            kwhInput.value = (energy * factor).toFixed(2);
        } else if (!isNaN(kwh) && document.activeElement === kwhInput) {
            energyInput.value = (kwh / factor).toFixed(2);
        }
    }

    energyInput.addEventListener('input', convertBoth);
    kwhInput.addEventListener('input', convertBoth);

    // On page load
    updateUnit(systemSelect.value);
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
        const input = document.querySelector(`input[name=${category}_rating]`);

        if (!input) {
            console.warn(`Input field for category "${category}" not found.`);
            return;
        }

        input.value = ratingValue;

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

 
<!-- The progress bar var  -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elements = document.querySelectorAll('.form-element');
        const answeredNumberLabel = document.getElementById('answered_number');
        const totalNumberLabel = document.getElementById('total_number');
        const answerInput = document.getElementById('answer_input');
        const totalInput = document.getElementById('total_number_input');
        const progressBar = document.querySelector('.progress-bar');
        const percentSpan = document.getElementById('percent');
        const totalElements = elements.length;

        // Set total elements in hidden input and label
        totalInput.value = totalElements;
        totalNumberLabel.textContent = totalElements;

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
            percentSpan.textContent = `${Math.round(percentage)}%`;

            // Update labels and hidden inputs
            answerInput.value = nonEmptyCount;
            answeredNumberLabel.textContent = nonEmptyCount;
        }

        // Attach event listeners
        elements.forEach(element => {
            element.addEventListener('input', updateProgressBar);
            if (element.type === 'checkbox' || element.type === 'radio') {
                element.addEventListener('change', updateProgressBar);
            }
        });

        updateProgressBar(); // Initial call to set progress on page load
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

 

<!-- Map and screenshots  -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
 
   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    "use strict";

    let map, marker, autocompleteMain, autocompleteMain2;
    let elevationService;
    let mainAddressSelected = false;
    let streetView; // Declare Street View globally

     
    function initMap() {
    // Initialize map
    map = new google.maps.Map(document.getElementById("gmp-map"), {
        center: { lat: 50.1109, lng: 8.6821 }, // Default location (Frankfurt, Germany)
        zoom: 15,
        streetViewControl: true, // Enable Street View control
    });

    // Initialize Street View
    streetView = map.getStreetView();

    // Initialize marker
    marker = new google.maps.Marker({
        map: map,
        draggable: true,
    });

    // ✅ Initialize Elevation Service Properly
    elevationService = new google.maps.ElevationService();

    // Initialize Google Places Autocomplete for both address inputs
    autocompleteMain = new google.maps.places.Autocomplete(document.getElementById("full_address"), {
        fields: ["address_components", "geometry", "formatted_address"],
        types: ["address"],
    });

    autocompleteMain2 = new google.maps.places.Autocomplete(document.getElementById("full_address2"), {
        fields: ["address_components", "geometry", "formatted_address"],
        types: ["address"],
    });

    // Listener for place change
    autocompleteMain.addListener("place_changed", () => handlePlaceChange(autocompleteMain, "1"));
    autocompleteMain2.addListener("place_changed", () => handlePlaceChange(autocompleteMain2, "2"));

    // Listener for checkbox
    document.getElementById("alternative_address").addEventListener("change", handleCheckboxChange);

    // On input blur, check if the address was selected from autocomplete
    document.getElementById("full_address").addEventListener("blur", validateMainAddressSelection);

    handleCheckboxChange(); // Check initial checkbox state
}

    function handlePlaceChange(autocompleteInstance, suffix) {
        const place = autocompleteInstance.getPlace();
        if (!place.geometry) {
            Swal.fire({
                icon: "error",
                title: "Ungültige Adresse",
                text: `Keine Details verfügbar für "${place.name}". Bitte wählen Sie eine Adresse aus der Liste.`,
            });
            return;
        }

        mainAddressSelected = (suffix === "1"); // Mark that the main address was correctly selected from autocomplete

        const location = place.geometry.location;
        const addressComponents = extractAddressComponents(place.address_components);

        const suffixMain = suffix === "1" ? "" : "2";
        const fullStreet = `${addressComponents.street} ${addressComponents.street_number}`.trim();

        // Dynamically set input values (only if the element exists)
        setValueIfExists(`full_address${suffixMain}`, place.formatted_address);
        setValueIfExists(`street-input${suffixMain}`, fullStreet);
        setValueIfExists(`postal_code-input${suffixMain}`, addressComponents.postal_code);
        setValueIfExists(`city-input${suffixMain}`, addressComponents.city);
        setValueIfExists(`latitude-input${suffixMain}`, location.lat().toFixed(6));
        setValueIfExists(`longitude-input${suffixMain}`, location.lng().toFixed(6));

        // Update map
        marker.setPosition(location);
        map.setCenter(location);

        // Get elevation
        getElevation(location.lat(), location.lng(), suffixMain);

        // Copy main address to alternative if the checkbox is checked
        if (suffix === "1" && document.getElementById("alternative_address").checked) {
            copyMainAddressToAlternative();
        }


        // ✅ Insert this block RIGHT HERE ⬇️
        const name = document.getElementById("name")?.value?.trim();
        const lastname = document.getElementById("lastname")?.value?.trim();
        const street = addressComponents.street;
        const postcode = addressComponents.postal_code;
        const latitude = location.lat().toFixed(6);
        const longitude = location.lng().toFixed(6);

        if (name && lastname && street && postcode && latitude && longitude) {
            const checkUrl = `/check-new-leads/${encodeURIComponent(name)}/${encodeURIComponent(lastname)}/${encodeURIComponent(street)}/${postcode}/${latitude}/${longitude}`;

            fetch(checkUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.status === "duplicate") {
                        Swal.fire({
                            icon: "error",
                            title: "Doppelter Kunde gefunden!",
                            html: `
                                Ein identischer Eintrag existiert bereits:<br><br>
                                <strong>${data.customer.name} ${data.customer.lastname}</strong><br>
                                ${data.customer.street}, ${data.customer.postcode}
                            `,
                            showCancelButton: true,
                            confirmButtonText: "Profil anzeigen",
                            cancelButtonText: "Schließen",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open(`/new_lead_profile/${data.customer.id}`, "_blank");
                            }
                        });
                    } else if (data.status === "neighbor") {
                        renderNeighborTable(data.customers);
                    } else {
                        console.log("Kein doppelter Kunde oder Nachbar gefunden.");
                    }
                })
                .catch(err => {
                    console.error("Fehler beim Überprüfen von Kunden:", err);
                });
        }
    }


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
                <td>${parseFloat(neighbor.distance).toFixed(2)} km</td>
                <td><a href="/new_lead_profile/${neighbor.id}" class="btn btn-primary btn-sm" target="_blank">Profil ansehen</a></td>
            </tr>
        `).join("");

        const tableHtml = `
            <table class="table table-bordered" style="width: 100%; text-align: left;">
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


   function extractAddressComponents(components) {
        let addressComponents = {
            street: "",
            street_number: "",
            postal_code: "",
            city: ""
        };

        components.forEach((component) => {
            const types = component.types;

            if (types.includes("route")) {
                addressComponents.street = component.long_name;
            }
            if (types.includes("street_number")) {
                addressComponents.street_number = component.long_name;
            }
            if (types.includes("postal_code")) {
                addressComponents.postal_code = component.long_name;
            }
            // ✅ Prefer 'locality' (actual city) over broader area
            if (types.includes("locality")) {
                addressComponents.city = component.long_name;
            }
            // Use 'administrative_area_level_1' only if city is still not found
            else if (!addressComponents.city && types.includes("administrative_area_level_1")) {
                addressComponents.city = component.long_name;
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

    function handleCheckboxChange() {
        const isChecked = document.getElementById("alternative_address").checked;
        const alternativeFields = [
            "full_address2",
            "street-input22",
            "postal_code-input2",
            "city-input2",
            "latitude-input2",
            "longitude-input2",
            "elevation-input2"
        ];

        
    }

    function copyMainAddressToAlternative() {
        setValueIfExists("full_address2", document.getElementById("full_address").value);
        setValueIfExists("street-input2", document.getElementById("street-input").value);
        setValueIfExists("postal_code-input2", document.getElementById("postal_code-input").value);
        setValueIfExists("city-input2", document.getElementById("city-input").value);
        setValueIfExists("latitude-input2", document.getElementById("latitude-input").value);
        setValueIfExists("longitude-input2", document.getElementById("longitude-input").value);
        setValueIfExists("elevation-input2", document.getElementById("elevation-input").value);
    }
 

    function validateMainAddressSelection() {
        if (!mainAddressSelected) {
            Swal.fire({
                icon: "warning",
                title: "Wählen Sie die Adresse aus der Liste",
                text: "Bitte wählen Sie eine gültige Adresse aus der Autovervollständigungsliste aus, um fortzufahren.",
            });
        }
    }

    // ✅ **Updated Street View Screenshot Function**
    document.getElementById("screenshot-btn").addEventListener("click", function () {
        if (!streetView.getVisible()) {
            Swal.fire({
                icon: "warning",
                title: "Bitte wechseln Sie zur Straßenansicht",
                text: "Sie müssen zuerst zur Street View wechseln, bevor Sie einen Screenshot machen können.",
            });
            return;
        }

        // ✅ Get the current Street View camera position (heading & pitch)
        let panoId = streetView.getPano();
        let pov = streetView.getPov(); // Get the current viewpoint (camera direction)
        let heading = pov.heading; // Camera rotation angle (0°-360°)
        let pitch = pov.pitch; // Up-down tilt angle

        // ✅ Use the camera position in the Street View Image API
        let streetViewImageUrl = `https://maps.googleapis.com/maps/api/streetview?size=600x300&pano=${panoId}&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

        // Fetch the image as a Blob and store it as a file
        fetch(streetViewImageUrl)
            .then(response => response.blob()) // Convert response to Blob
            .then(blob => {
                let file = new File([blob], "street_view_screenshot.jpg", { type: "image/jpeg" });

                // ✅ Store the file in the hidden input field
                let fileInput = document.getElementById("screenshot-file-input");

                // Creating a DataTransfer object to assign the file to the input
                let dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // ✅ Show Screenshot Preview
                let imgPreview = document.createElement("img");
                imgPreview.src = URL.createObjectURL(blob);
                imgPreview.style = "max-width: 100%; margin-top: 10px; border: 1px solid #ccc;";

                let previewContainer = document.getElementById("screenshot-preview");
                previewContainer.innerHTML = "";
                previewContainer.appendChild(imgPreview);

                console.log("Screenshot saved as file:", file);
            })
            .catch(error => console.error("Screenshot failed:", error));
    });


    document.addEventListener("DOMContentLoaded", initMap);
</script>
 

<!-- saving the form  -->


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
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    } else {
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

<script>
    $(document).ready(function() {
        $('#source').select2({
            tags: true,
            placeholder: "Quelle auswählen",
            allowClear: true
        });
    });
</script>


<script>
    function showTab(index) {
        document.querySelectorAll('.tab-pane').forEach((pane, i) => {
            pane.classList.remove('active');
            if (i === index - 1) pane.classList.add('active');
        });
        document.querySelectorAll('.wizard-step').forEach((step, i) => {
            step.classList.remove('active');
            if (i === index - 1) step.classList.add('active');
        });
    }

    function navigateTab(direction) {
        const steps = document.querySelectorAll('.wizard-step');
        let currentIndex = [...steps].findIndex(step => step.classList.contains('active'));
        let nextIndex = currentIndex + direction;
        if (nextIndex >= 0 && nextIndex < steps.length) {
            showTab(nextIndex + 1);
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateProgressCounts() {
            const steps = [
                { fields: document.querySelectorAll('#step1 input, #step1 select, #step1 textarea'), total: 8, label: 'step1-count' },
                { fields: document.querySelectorAll('#step2 input, #step2 select, #step2 textarea'), total: 6, label: 'step2-count' },
                { fields: document.querySelectorAll('#step3 input, #step3 select, #step3 textarea'), total: 5, label: 'step3-count' },
                { fields: document.querySelectorAll('#step4 input, #step4 select, #step4 textarea'), total: 3, label: 'step4-count' },
                { fields: document.querySelectorAll('#step5 input, #step5 select, #step5 textarea'), total: 3, label: 'step5-count' },
            ];

            steps.forEach(step => {
                let filled = 0;
                step.fields.forEach(field => {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        if (field.checked) filled++;
                    } else if (field.value.trim() !== '') {
                        filled++;
                    }
                });
                document.getElementById(step.label).innerText = `(${filled}/${step.total})`;
            });
        }

        // Add event listener to all form fields
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', updateProgressCounts);
            el.addEventListener('change', updateProgressCounts);
        });

        updateProgressCounts(); // Initial check on page load
    });
</script>

@endsection
