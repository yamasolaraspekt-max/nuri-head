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

.select2-container {
    box-sizing: border-box;
    display: inline-block;
    margin: 0;
    position: relative;
    vertical-align: middle;
    background:white !important;
    padding:4px !important;
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

    .table .thead-light th  {
        color: #4e5154;
    background-color: #f1f1f1;
    border-color: #f1f1f1;
    border-bottom: 1px solid #d0d0d0
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
                                             
                                            
                                        <div class="col-12">
                                            <hr>
                                        </div>  
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

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Anrede</span>
                                                    </div>
                                                    <div class="col-md-8 p-0">
                                                        <select class="form-control select2-tags"
                                                                name="title"
                                                                data-placeholder="Anrede auswählen oder eingeben">
                                                            <option></option>
                                                            <option value="Frau">Frau</option>
                                                            <option value="Herr">Herr</option>
                                                            <option value="An die">An die</option>
                                                            <option value="An den">An den</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Akademischer Titel</span>
                                                    </div>
                                                    <div class="col-md-8 p-0">
                                                        <select class="form-control select2-tags"
                                                                name="academic_title"
                                                                data-placeholder="Titel auswählen oder eingeben">
                                                            <option></option>
                                                            <option value="Dr.">Dr.</option>
                                                            <option value="Prof.">Prof.</option>
                                                            <option value="Prof. Dr.">Prof. Dr.</option>
                                                            <option value="Dipl.-Ing.">Dipl.-Ing.</option>
                                                            <option value="Mag.">Mag.</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                
                                            <div class="col-12 ">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-4">
                                                        <span>Vorname / Nachname</span>
                                                    </div> 
                                                    <div class="col-md-4 input-class">
                                                        <input type="text" id="name" class="form-control text form-element" value="{{ old('name', $inquiry['name'] ?? '') }}"  placeholder="Vorname"  name="name" autocomplete="off" list="name-options">
                                                        <datalist id="name-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
                                                    </div>
                                                    <div class="col-md-4 input-class">
                                                        <input type="text" id="lastname" class="form-control text form-element" placeholder="Nachname" value="{{ old('lastname', $inquiry['lastname'] ?? '') }}" name="lastname" autocomplete="off" list="lastname-options">
                                                        <datalist id="lastname-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
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
                                                        <span>Objectname</span>
                                                    </div>
                                                    
                                                    <div class="col-md-8 input-class">
                                                        <input type="text" class="form-control text" id="object_name" value="{{ old('object_name','Privathaus')}}" name="object_name">
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
                                                        <span>Priorität/Betrieb</span>
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
                                                        <input type="hidden" class="form-control text form-element" value="{{old('city2')}}" name="city2" id="city-input2">

                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Quelle / Anfragedatum</span>
                                                    </div>
                                                    <div class="col-md-4 input-class">
                                                        <div class="form-element">
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
                                                                <option value="Messe/Veranstaltung">Messe/Veranstaltung</option>
                                                            </select>  
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 input-class">
                                                        <div class="form-element">
                                                            <input type="date" class="form-control text form-element" name="request_date" value="{{ now()->format('Y-m-d') }}" />
                                                            <input type="hidden" name="contact_person" class="form-control text form-element"  value="{{ auth()->user()->name}}" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Notizen</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <textarea name="note"  class="form-control"   >
                                                                {{ old('note', $inquiry['note'] ?? '') }}
                                                        </textarea>
                                                        
                                                    </div>
                                                </div>
                                            </div>

                                            
                                        </div> 
                                        
                                        
                                        <div class="col-6">
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
                                        </div>
                                        
                                </div>


                                <div class="row mt-2">
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
                                                                    <img src="{{ asset('images/icons/produkt.svg') }}" alt="" style="width: 62px;"> <br>
                                                                        Produkt</th>
                                                                <th>
                                                                <img src="{{ asset('images/icons/dienstleistung.svg') }}" alt="" style="width: 62px;"> <br>
                                                                    Dienstleistung</th>
                                                                <th>
                                                                    <img src="{{ asset('images/icons/abteilung.svg') }}" alt="" style="width: 62px;"> <br>
                                                                    Abteilung</th>
                                                                <th>
                                                                    <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                                                    Innendienst
                                                                </th>
                                                                <th>
                                                                    <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"><br>
                                                                    Außendienst
                                                                </th>

                                                                <th>
                                                                    <img src="{{ asset('images/icons/kaufinteresse.svg') }}" alt="" style="width: 56px;"> <br>
                                                                    Interesse
                                                                </th>
                                                              

                                                                <th>
                                                                    <img src="{{ asset('images/icons/zaehler.svg') }}" alt="" style="width: 56px;"> <br>
                                                                    Realisierungszeit
                                                                </th>
                                                                <th>
                                                                <img src="{{ asset('images/icons/aktion.svg') }}" alt="" style="width: 56px;"> <br>
                                                                    Aktion</th>
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
        zoom: 130,
        mapTypeId: google.maps.MapTypeId.SATELLITE, // 👈 Satellite View by default

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

    document.getElementById("full_address").addEventListener("keydown", function () {
        mainAddressSelected = false;
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

        if (suffix === "1") {
            mainAddressSelected = true;
        }

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
        const addressInput = document.getElementById("full_address").value.trim();
        if (!mainAddressSelected && addressInput !== "") {
            Swal.fire({
                icon: "warning",
                title: "Wählen Sie die Adresse aus der Liste",
                text: "Bitte wählen Sie eine gültige Adresse aus der Autovervollständigungsliste aus, um fortzufahren.",
            });
        }
    }


    // ✅ **Updated Street View Screenshot Function**
    document.getElementById("screenshot-btn").addEventListener("click", function () {
        if (streetView.getVisible()) {
            // 📸 STREET VIEW Screenshot
            const panoId = streetView.getPano();
            const pov = streetView.getPov();
            const heading = pov.heading;
            const pitch = pov.pitch;

            const streetViewImageUrl = `https://maps.googleapis.com/maps/api/streetview?size=600x300&pano=${panoId}&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

            fetch(streetViewImageUrl)
                .then(response => response.blob())
                .then(blob => handleScreenshotBlob(blob, "street_view_screenshot.jpg"))
                .catch(error => console.error("❌ Street View screenshot failed:", error));

        } else {
            // 🛰️ SATELLITE VIEW Screenshot (fallback)
            const center = map.getCenter();
            const zoom = map.getZoom();
            const mapType = 'satellite';

            const staticMapUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${center.lat()},${center.lng()}&zoom=${zoom}&size=600x300&maptype=${mapType}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

            fetch(staticMapUrl)
                .then(response => response.blob())
                .then(blob => handleScreenshotBlob(blob, "satellite_screenshot.png"))
                .catch(error => console.error("❌ Satellite screenshot failed:", error));
        }

        function handleScreenshotBlob(blob, filename) {
            const file = new File([blob], filename, { type: blob.type });

            const fileInput = document.getElementById("screenshot-file-input");
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            const imgPreview = document.createElement("img");
            imgPreview.src = URL.createObjectURL(blob);
            imgPreview.style = "max-width: 100%; margin-top: 10px; border: 1px solid #ccc;";

            const previewContainer = document.getElementById("screenshot-preview");
            previewContainer.innerHTML = "";
            previewContainer.appendChild(imgPreview);

            console.log("✅ Screenshot saved as file:", file.name);
        }
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

 

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        if (e.target.matches('[data-bs-toggle="collapse"]')) {
            const target = document.querySelector(e.target.dataset.bsTarget);
            if (target) {
                new bootstrap.Collapse(target, {
                    toggle: true
                });
            }
        }
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Optional: Trigger this if monthly inputs are available
        document.querySelectorAll('[name^="monthly_heat["]').forEach(input => {
            input.addEventListener('input', calculateHeatSum);
        });

        document.querySelectorAll('[name^="monthly_electricity["]').forEach(input => {
            input.addEventListener('input', calculateElectricitySum);
        });

        // If direct Verbrauch input is used
        const consumptionInput = document.querySelector('[name="consumption"]');
        if (consumptionInput) {
            consumptionInput.addEventListener('input', calculateHeatingLoad);
        }
    });

    function calculateHeatSum() {
        let total = 0;
        document.querySelectorAll('[name^="monthly_heat["]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('[name="total_heat_consumption"]').value = total.toFixed(2);
    }

    function calculateElectricitySum() {
        let total = 0;
        document.querySelectorAll('[name^="monthly_electricity["]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.querySelector('[name="total_electricity_consumption"]').value = total.toFixed(2);
    }

    function calculateHeatingLoad() {
        const val = parseFloat(this.value);
        if (!isNaN(val)) {
            const load = val / 180 * 1.1;
            document.querySelector('[name="heating_load_calculation"]').value = load.toFixed(2);
        }
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('electric_car_select');
        const countGroup = document.getElementById('electric_car_count_group');

        function toggleElectricCarCount() {
            const val = select.value;
            if (val === 'Ja' || val === 'Geplant') {
                countGroup.style.display = 'block';
            } else {
                countGroup.style.display = 'none';
            }
        }

        select.addEventListener('change', toggleElectricCarCount);
        toggleElectricCarCount(); // Run on load
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = ['power_household', 'power_heatpump', 'power_electric_car', 'power_other'];

    inputs.forEach(name => {
        document.querySelector(`[name="${name}"]`)?.addEventListener('input', calculateTotalPower);
    });

    function calculateTotalPower() {
        let total = 0;
        inputs.forEach(name => {
            const val = parseFloat(document.querySelector(`[name="${name}"]`)?.value || 0);
            total += !isNaN(val) ? val : 0;
        });
        document.getElementById('power_total').value = total.toFixed(2);
    }

    // Sync Wohneinheiten from objectdaten if available
    const mainWE = document.getElementById('objectdata_number_we'); // ID in objectdaten section
    const energyWE = document.getElementById('number_we');

    if (mainWE && energyWE) {
        energyWE.value = mainWE.value;
        mainWE.addEventListener('input', () => {
            energyWE.value = mainWE.value;
        });
    }
});
</script>
<script>
$(function () {
    $('.select2-tags').select2({
        theme: 'bootstrap4',   // remove if not using Bootstrap theme
        tags: true,
        allowClear: true,
        width: '100%',
        placeholder: function () {
            return $(this).data('placeholder');
        }
    });
});
</script>

@endsection
