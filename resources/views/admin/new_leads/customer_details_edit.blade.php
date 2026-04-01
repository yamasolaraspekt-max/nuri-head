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

.star-rating {
    font-size: 2rem;
    cursor: pointer;
}

.star {
    color: #ccc;
}

.star.selected,
.star.hovered {
    
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

.star {
    color: gray;
    cursor: pointer;
}

.star.selected_star {
    color: gold;
}

.star.hovered {
    color: orange;
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
    /* color: white; */
    box-shadow: none !important;
    /* border:1px solid #73b1d4 !important; */
    border:0 !important;
    padding:0 !important;
    text-align: left !important;
    font-size: 11px !important;
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
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                                </li>
                                 <li class="breadcrumb-item active"><a href="{{ url('/employee_dashboard') }}">Bearbeiten</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
       
        </div>
        <div class="content-body">
         <form class="form form-horizontal custom-file-upload" method="post" id="customer_form"
                action="{{ action('App\Http\Controllers\NewLeadsController@details_update') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}">
               
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                <div class="row match-height">
                     <input type="hidden" name="id" value="{{$id}}">
                    <div class="col-xl-8 col-sm-6 col-md-12 card-scrollable ">
                        <div class="row">  
                             <div class="col-6">
                                <div class="col-12">
                                    <div class="form-group form-element"> 
                                        <div class="col-md-6">
                                            <ul class="list-unstyled mb-0">
                                                @php
                                                    $type = $data->customer_type ?? 'privat';
                                                    $type = in_array($type, ['privat', 'Gewerbe']) ? $type : 'privat';
                                                @endphp

                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type1"
                                                                value="privat" @if($type == 'privat') checked @endif>
                                                            <label class="custom-control-label" for="customer_type1">Privat</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-2">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input form-element" name="customer_type" id="customer_type2"
                                                                value="Gewerbe" @if($type == 'Gewerbe') checked @endif>
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
                                            <select class="form-control select2-tags"
                                                    name="title"
                                                    data-placeholder="Anrede auswählen oder eingeben">
                                                <option></option>
                                                @php
                                                    $titles = ['Frau', 'Herr', 'An die', 'An den'];
                                                @endphp
                                                @foreach($titles as $t)
                                                    <option value="{{ $t }}" @selected(($data->title ?? '') === $t)>
                                                        {{ $t }}
                                                    </option>
                                                @endforeach

                                                {{-- allow saved custom value --}}
                                                @if(!empty($data->title) && !in_array($data->title, $titles))
                                                    <option value="{{ $data->title }}" selected>{{ $data->title }}</option>
                                                @endif
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
                                                @php
                                                    $academicTitles = ['Dr.', 'Prof.', 'Prof. Dr.', 'Dipl.-Ing.', 'Mag.'];
                                                @endphp
                                                @foreach($academicTitles as $t)
                                                    <option value="{{ $t }}" @selected(($data->academic_title ?? '') === $t)>
                                                        {{ $t }}
                                                    </option>
                                                @endforeach

                                                {{-- allow saved custom value --}}
                                                @if(!empty($data->academic_title) && !in_array($data->academic_title, $academicTitles))
                                                    <option value="{{ $data->academic_title }}" selected>{{ $data->academic_title }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 " id="firma-container">
                                    <div class="form-group row form-element">
                                        <div class="col-md-4">
                                            <span>Firma</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="firma" class="form-control form-element" value="{{old('firma', $data->firma)}}" name="firma">
                                        </div>
                                    </div>
                                </div>
                          

                                <div class="col-12 ">
                                    <div class="form-group row form-element">
                                        <div class="col-md-4">
                                            <span>Vorname</span>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <input type="text" id="name" class="form-control form-element" value="{{ old('name', $data->name) }}" name="name" autocomplete="off" list="name-options">
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
                                            <input type="text" id="lastname" class="form-control form-element" value="{{ old('lastname', $data->lastname) }}" name="lastname" autocomplete="off" list="lastname-options">
                                            <datalist id="lastname-options">
                                                <!-- Options will be populated by JavaScript -->
                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Main Address Inputs --> 

                                 <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-4">
                                            <span>STR./NR./PLZ./ORT</span>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <input id="full_address" type="text" class="form-control text form-element"
                                                placeholder="Adresse eingeben" name="full_address"
                                                value="{{ $data->street }}, {{ $data->postcode}} {{ $data->city }}">
                                            <input id="street-input" type="hidden" class="form-control form-element" name="street"
                                                value="{{ old('street', $data->street) }}">
                                            <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $data->latitude) }}">
                                            <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $data->longitude) }}">
                                            <input type="hidden" id="elevation-input" name="elevation"
                                                value="{{ old('elevation', $data->elevation) }}">
                                            <input type="hidden" class="form-control form-element" value="{{ old('postcode', $data->postcode) }}"
                                                name="postcode" id="postal_code-input">
                                            <input type="hidden" class="form-control form-element" value="{{ old('city', $data->city) }}" name="city"
                                                id="locality-input">
                                        </div>
                                    </div>
                                </div>

                                
  
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Festnetz</span>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <input type="text" class="form-control" value="{{old('telephone', $data->telephone)}}" id="telephone-input" name="telephone"  >
                                        </div> 
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Handy</span>
                                        </div> 
                                        <div class="col-md-8 p-0">
                                            <input type="text" class="form-control" value="{{old('phone', $data->phone)}}" name="phone" id="phone-input"  >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>E-Mail</span>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <input type="email" class="form-control" id="email-input" value="{{old('email', $data->email)}}" name="email">
                                        </div>
                                    </div>
                                </div>
                                  
                            </div>   

                            <div class="col-6">
                                 <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row form-element">
                                            <div class="col-md-4">
                                                <span>Quelle</span>
                                            </div>
                                            <div class="col-md-8 p-0">
                                                <select name="source" id="source" class="form-control form-element">
                                                    <option selected></option>
                                                    <option value="Telefonisch" @if($data->source=='Telefonisch') selected @endif>Telefonisch</option>
                                                    <option value="Persönlich" @if($data->source=='Persönlich') selected @endif>Persönlich</option>
                                                    <option value="Mail" @if($data->source=='Mail') selected @endif>Mail</option>
                                                    <option value="Nachbar" @if($data->source=='Nachbar') selected @endif>Nachbar</option>
                                                    <option value="Empfehlung" @if($data->source=='Empfehlung') selected @endif>Empfehlung</option>
                                                    <option value="Solarrechner" @if($data->source=='Solarrechner') selected @endif>Solarrechner</option>
                                                    <option value="Herstellerlead" @if($data->source=='Herstellerlead') selected @endif>Herstellerlead</option>
                                                    <option value="Kunde aus Vergangenheit" @if($data->source=='Kunde aus Vergangenheit') selected @endif>Kunde aus Vergangenheit</option>
                                                    <option value="Messe" @if($data->source=='Messe') selected @endif>Messe</option>
                                                    <option value="Messe" @if($data->source=='Messe/Veranstaltung') selected @endif>Messe/Veranstaltung</option>
                                                </select>
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
                                                   <div class="star-rating form-element" data-category="interest" data-rating="{{ $data->interest_rating ?? 0 }}">
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                        <span class="star form-element"><i class="fa fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" name="interest_rating" value="{{ $data->interest_rating ?? 0 }}">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="d-flex align-items-center">
                                                <div class="col-5">
                                                    <span class="mr-2">Ernsthaftigkeit</span>
                                                </div>
                                                <div class="col-7">
                                                    <div class="star-rating form-element" data-category="seriousness" data-rating="{{ $data->seriousness_rating }}">
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" name="seriousness_rating" value="{{ $data->seriousness_rating }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="d-flex align-items-center">
                                                <div class="col-5">
                                                    <span class="mr-2">Preisinformation</span>
                                                </div>
                                                <div class="col-7">
                                                    <div class="star-rating form-element" data-category="price_information" data-rating="{{ $data->price_information }}">
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                        <span class="star"><i class="fa fa-star"></i></span>
                                                    </div>
                                                    <input type="hidden" name="price_information" value="{{ $data->price_information }}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                     
                                    <div class="col-12 mt-2">
                                        <div class="form-group row form-element">
                                            <div class="col-md-2">
                                                <span>Notizen</span>
                                            </div>
                                            <div class="col-md-10">
                                                <textarea name="info"    style="justify-item: left;width: 100%;height: 200px;border-radius: 7px;border: 1px solid #c6c6c6;">
                                                    {{ old('info', $data->info) }}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                  
                                </div>
                            </div>
                        </div>  
                    </div> 
                    <!-- map section:start -->
                    <div class="col-xl-4 col-md-12"> 
                        <div class="col-12">
                            <!-- {{-- Map Start --}} -->
                            <div class="card">
                                <div class="card-header" style="align-self: center;">
                                    <h2 class="content-header-title float-left">OBJEKTBILDER</h2>
                                </div>
                                <div class="card-content">
                                    <div class="card-body"> 
                                        <div class="map" id="gmp-map" style="width: 100%; position: relative; overflow: hidden; height: 356px;"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- {{-- Map End --}} -->
                        </div>
                        <div class="col-lg-12">
                            <!-- <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn"  >
                                <i class="feather icon-camera"></i> Screenshot
                            </a>  -->
  
                         <div id="screenshot-preview"></div>

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

@push('scripts')
  

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
    $('#lastname, #name, #street-input, #postal_code-input, #locality-input').on('change', function() {
        // Collect form data
        var lastname = $('#lastname').val();
        var name = $('#name').val();
        var street = $('#street-input').val();
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
            const street = document.getElementById('street-input').value.trim();
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

     

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const starRatings = document.querySelectorAll('.star-rating');

    starRatings.forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        const ratingValue = parseInt(rating.dataset.rating, 10) || 0; // Parse data-rating as an integer
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
                star.classList.remove('selected_star');
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
        const ratingValue = parseInt(rating.dataset.rating, 10) || 0;
        updateStars(rating, ratingValue - 1);
    }

    function updateInput(rating) {
        const category = rating.dataset.category;
        const ratingValue = rating.dataset.rating;
        document.querySelector(`input[name=${category}_rating]`).value = ratingValue;
    }
});

</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async
    defer
></script>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

<script>
"use strict";

let map;
let marker;
let streetView;
let elevationService;
let autocomplete;
let mainAddressSelected = false;

// Called by Google Maps via callback=initMap
function initMap() {
    const latInput = document.getElementById("latitude-input");
    const lngInput = document.getElementById("longitude-input");

    const initialLat = parseFloat(latInput ? latInput.value : "") || 50.1109; // Frankfurt
    const initialLng = parseFloat(lngInput ? lngInput.value : "") || 8.6821;

    const mapEl = document.getElementById("gmp-map");
    if (!mapEl) {
        console.warn("gmp-map container not found.");
        return;
    }

    map = new google.maps.Map(mapEl, {
        center: { lat: initialLat, lng: initialLng },
        zoom: 18,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        streetViewControl: true
    });

    streetView = map.getStreetView();
    elevationService = new google.maps.ElevationService();

    marker = new google.maps.Marker({
        map: map,
        position: { lat: initialLat, lng: initialLng },
        draggable: false,
        visible: true
    });

    initAutocomplete();
}

// Setup Places Autocomplete on the address input
function initAutocomplete() {
    const addressInput = document.getElementById("full_address");
    if (!addressInput) {
        console.warn("full_address input not found.");
        return;
    }

    // Create the autocomplete widget (this is what shows the dropdown list)
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        fields: ["address_components", "geometry"],
        types: ["geocode"]      // address-like predictions
    });

    // When user selects an item from the autocomplete list
    autocomplete.addListener("place_changed", onPlaceChanged);

    // Fallback: user typed address but didn't click a suggestion
    addressInput.addEventListener("blur", function () {
        const value = addressInput.value.trim();
        if (!value) {
            mainAddressSelected = false;
            return;
        }

        // If user did not choose from list (no place_changed fired)
        if (!mainAddressSelected) {
            geocodeAddress(value);
        }
    });
}

// Called when user selects a suggestion from autocomplete
function onPlaceChanged() {
    const place = autocomplete.getPlace();
    if (!place || !place.geometry) {
        mainAddressSelected = false;
        Swal.fire({
            icon: "warning",
            title: "Adresse nicht gefunden",
            text: "Bitte wählen Sie eine Adresse aus der Liste aus."
        });
        return;
    }

    handlePlaceResult(place);
    mainAddressSelected = true;
}

// Fallback: geocode manually typed address
function geocodeAddress(address) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address }, function (results, status) {
        if (status === "OK" && results && results[0]) {
            handlePlaceResult(results[0]);
            mainAddressSelected = true;
        } else {
            mainAddressSelected = false;
            Swal.fire({
                icon: "error",
                title: "Adresse nicht gefunden",
                text: "Die eingegebene Adresse konnte nicht automatisch gefunden werden."
            });
        }
    });
}

// Common handler for both autocomplete and geocoder results
function handlePlaceResult(place) {
    if (!place || !place.geometry) return;

    const components = extractAddressComponents(place.address_components);
    const street   = (components.route + " " + components.street_number).trim();
    const city     = components.locality;
    const postcode = components.postal_code;
    const latitude = place.geometry.location.lat();
    const longitude = place.geometry.location.lng();

    setIfExists("street-input", street);
    setIfExists("locality-input", city);
    setIfExists("postal_code-input", postcode);
    setIfExists("latitude-input", latitude);
    setIfExists("longitude-input", longitude);

    updateMap(latitude, longitude);
    updateElevation(latitude, longitude);
    checkCustomer(street, postcode, latitude, longitude);
}

// Extracts address components from Google response
function extractAddressComponents(addressComponents) {
    const data = {
        street_number: "",
        route: "",
        locality: "",
        postal_code: ""
    };

    (addressComponents || []).forEach(component => {
        const type = component.types && component.types[0];
        if (type && Object.prototype.hasOwnProperty.call(data, type)) {
            data[type] = component.long_name || "";
        }
    });

    return data;
}

// Helper: set value if element exists
function setIfExists(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.value = value != null ? value : "";
    }
}

// Move map + marker
function updateMap(lat, lng) {
    if (!map || !marker) return;

    const location = new google.maps.LatLng(lat, lng);
    map.setCenter(location);
    map.setZoom(19);
    marker.setPosition(location);
    marker.setVisible(true);
}

// Elevation
function updateElevation(lat, lng) {
    if (!elevationService) return;

    elevationService.getElevationForLocations(
        { locations: [{ lat: lat, lng: lng }] },
        function (results, status) {
            if (status === "OK" && results && results[0]) {
                setIfExists("elevation-input", results[0].elevation.toFixed(2));
            }
        }
    );
}

// Backend duplicate / neighbor check
function checkCustomer(street, postcode, latitude, longitude) {
    const url = `/check-new-leads/${encodeURIComponent(street)}/${postcode}/${latitude}/${longitude}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (!data || (data.status !== "duplicate" && data.status !== "neighbor")) {
                return;
            }

            let tableHTML = `
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Nachname</th>
                            <th>Adresse</th>
                            <th>Radius (km)</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>`;

            if (data.status === "duplicate" && data.customer) {
                const c = data.customer;
                tableHTML += `
                    <tr>
                        <td>${c.name}</td>
                        <td>${c.lastname}</td>
                        <td>${c.full_address}</td>
                        <td>-</td>
                        <td><a href="/new_lead_profile/${c.id}" class="btn btn-primary">Profil anzeigen</a></td>
                    </tr>`;
            }

            if (data.status === "neighbor" && Array.isArray(data.customers)) {
                data.customers.forEach(customer => {
                    tableHTML += `
                        <tr>
                            <td>${customer.name}</td>
                            <td>${customer.lastname}</td>
                            <td>${customer.full_address}</td>
                            <td>${customer.distance.toFixed(2)}</td>
                            <td><a href="/new_lead_profile/${customer.id}" class="btn btn-primary">Profil anzeigen</a></td>
                        </tr>`;
                });
            }

            tableHTML += `</tbody></table>`;

            Swal.fire({
                title: data.status === "duplicate"
                    ? "Doppelter Eintrag gefunden!"
                    : "Nachbarn gefunden!",
                html: tableHTML,
                icon: "info",
                width: "70%",
                showCloseButton: true
            });
        })
        .catch(error => {
            console.error("Fehler beim Überprüfen:", error);
        });
}

// Optional: call before submit
function validateMainAddressSelection() {
    if (!mainAddressSelected) {
        Swal.fire({
            icon: "warning",
            title: "Wählen Sie die Adresse aus der Liste",
            text: "Bitte wählen Sie eine gültige Adresse aus der Autovervollständigungsliste aus, um fortzufahren."
        });
        return false;
    }
    return true;
}
</script>


 
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
$(function () {
    $('.select2-tags').select2({
        theme: 'bootstrap4',   // remove if not using bootstrap theme
        tags: true,
        allowClear: true,
        width: '100%',
        placeholder: function () {
            return $(this).data('placeholder');
        }
    });
});
</script>

@endpush