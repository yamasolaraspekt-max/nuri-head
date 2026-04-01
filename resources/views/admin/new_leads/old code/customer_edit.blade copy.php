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
    font-size: 15px !important;
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
    border: 2px !important;
    width: 100% !important;
    background: #efeded !important;
    height: 40px !important;
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
.select2{
    width: 100% !important;
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
      action="{{ action('App\Http\Controllers\NewLeadsController@update') }}"
      enctype="multipart/form-data">
    @csrf
        <input type="hidden" name="lead_id" value="{{ request()->id}}" >
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-4">
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
                                                    <div style="border-right: 2px solid;">
                                                        <h2 class="card-title mt-2 mb-0 white title">
                                                            {{ $item->article_group }}</h2>
                                                        <p class="card-text white mb-1" style="font-size:10px"> Aktualler Status: <span
                                                                id="interested-{{ $loop->index }}"></span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="service">
                                                    <select name="service" id="" style="background: transparent; border: 0px; color: white; font-weight: bolder;">
                                                        <option value="complete">Komplettlösung</option>
                                                        <option value="montage">Montage</option>
                                                        <option value="product">Produkt</option>
                                                        <option value="plan">Planung</option>
                                                        <option value="maintenance">Wartung</option>
                                                        <option value="repair">Reparetur</option>
                                                        <option value="others">Sonstiges</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="checkbox" class="d-none" name="product_id[]" value="{{ $item->id }}">
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

                            <!-- Single hidden input for selected products -->
                            <input type="hidden" name="products" id="products">


                        </div>
                    </div>
                    <div class="col-md-5 col-12">
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
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="customer_type" id="customer_type1"
                                                                            @if($customer->customer_type=="privat")
                                                                        checked enabled @else disabled @endif
                                                                        value="privat">
                                                                        <label class="custom-control-label"
                                                                            for="customer_type1">privat</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="customer_type" id="customer_type2"
                                                                            value="Gewerbe"
                                                                            @if($customer->customer_type=="Gewerbe")
                                                                        checked enabled @else disabled @endif >
                                                                        <label class="custom-control-label"
                                                                            for="customer_type2">Gewerbe</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="customer_type" id="customer_type3"
                                                                            value="Kummune"
                                                                            @if($customer->customer_type=="Kummune")
                                                                        checked enabled @else disabled @endif >
                                                                        <label class="custom-control-label"
                                                                            for="customer_type3">Kummune</label>
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
                                                            <select class="form-control form-element" id="basicSelect" name="title"> 
                                                                <option @if($customer->title=='Frau') checked @endif>Frau</option>
                                                                <option  @if($customer->title=='Herr') checked @endif>Herr</option>
                                                                <option  @if($customer->title=='Dr.') checked @endif>Dr.</option>
                                                                <option  @if($customer->title=='Pro.') checked @endif>Pro.</option>
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
                                                        <input type="text" id="firma" class="form-control form-element" value="{{old('firma', $customer->firma)}}" name="firma">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Nachname</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control form-element" value="{{old('lastname', $customer->lastname)}}" name="lastname">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Vorname</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control form-element" value="{{old('name', $customer->name)}}"  name="name">
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
                                                            <input id="location-input" type="text" class="form-control form-element" placeholder="Enter location" name="street" value="{{ old('street', $customer->street) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $customer->latitude) }}">
                                                            <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $customer->longitude) }}">
                                                            <input type="hidden" id="polygon-height" name="polygon_height" value="{{ old('polygon_height', $customer->polygon_height) }}">
                                                            <input type="hidden" id="polygon-width" name="polygon_width" value="{{ old('polygon_width', $customer->polygon_width) }}">
                                                            <input type="hidden" id="polygon-area" name="polygon_area" value="{{ old('polygon_area', $customer->polygon_area) }}">
                                                            <input type="hidden" id="elevation-input" placeholder="Elevation in meters" name="elevation" value="{{ old('elevation', $customer->elevation) }}">
                                                            <input type="text" class="form-control form-element" value="{{old('postcode', $customer->postcode)}}" name="postcode" id="postal_code-input">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control form-element" value="{{old('city', $customer->city)}}" name="city" id="locality-input">
                                                        </div>
                                                    </div>
                                                </div>
                                                  <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>Festnet/Mobile</span>
                                                        </div>
                                                      
                                                        <div class="col-md-5">
                                                            <input type="text"  class="form-control"
                                                              id="telephone-input" value="{{old('telephone', $customer->telephone)}}" name="telephone" placeholder="Festnet">
                                                        </div>
                                                          <div class="col-md-5">
                                                            <input type="text" id="phone-input" class="form-control"
                                                                value="{{old('phone', $customer->phone)}}" name="phone" placeholder="Mobile">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>E-Mail</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="email" id="email-input" class="form-control"  value="{{old('email', $customer->email)}}" name="email">
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
                                                                    <Option @if($customer->periority=='Normal') checked @endif>Normal</Option>
                                                                    <Option @if($customer->periority=='Dringend') checked @endif>Dringend</Option>
                                                                    <Option @if($customer->periority=='Sehr dringend') checked @endif>Sehr dringend</Option>
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
                                                                    <input type="checkbox" name="alternative_address" id="alternative_address"  @if($customer->alternative_address=='true') value="true" @else value="false" @endif>
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
                                                            <input id="location-input2" type="text" class="form-control form-element" placeholder="Enter location" name="street2" value="{{ old('street2', $customer->street2) }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12" id="ort2s">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="hidden" id="latitude-input2" name="latitude2" value="{{ old('latitude2', $customer->lat2) }}">
                                                            <input type="hidden" id="longitude-input2" name="longitude2" value="{{ old('longitude2', $customer->lon2) }}">
                                                            <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation2" value="{{ old('elevation2', $customer->elevation2) }}">
                                                            <input type="text" class="form-control form-element" value="{{old('postcode2', $customer->postcode2)}}" name="postcode2" id="postal_code-input2">
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control form-element" value="{{old('city2', $customer->city2)}}" name="city2" id="locality-input2">
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-12">
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xl-12 col-md-6 col-sm-12">
                                                <div class="accordion" id="accordionExample" data-toggle-hover="true"> 
                                                   
                                                    <div class="collapse-margin">
                                                        <div class="card-header" id="heading4" data-toggle="collapse" role="button" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                                            <span class="lead collapse-title collapsed">
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
                                                                                    <option value="EFH" @if($customer->objective == "EFH") selected @endif>EFH</option>
                                                                                    <option value="MFH" @if($customer->objective == "MFH") selected @endif>MFH</option>
                                                                                    <option value="Gewerbe" @if($customer->objective == "Gewerbe") selected @endif>Gewerbe</option>
                                                                                    <option value="others" @if($customer->objective == "others") selected @endif>Sonstigis</option>
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
                                                                                        <input type="text" class="form-control form-element" name="house_year" id="house_year" value="{{ old('house_year', $customer->house_year) }}" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                                
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wieveil Wohneinheit hat das Obejekt?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control textbox" name="number_we" value="{{ old('number_we', $customer->number_we) }}">
                                                                                    
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wieviel Geschoß hat das Objekt?   </h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control"  name="number_stories" value="{{ old('number_stories', $customer->number_stories) }}">
                                                                                    
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie groß ist die Beheizte Wohnfläche?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                    <input type="text" class="form-control" name="living_space" value="{{ old('living_space', $customer->living_space) }}">
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
                                                                                    <input type="text" class="form-control" name="unusable_space"  value="{{ old('unusable_space', $customer->unusable_space) }}">
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
                                                                                    <input type="text" class="form-control" name="number_people" id="number_people"  value="{{ old('number_people', $customer->number_people) }}" > 
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
                                                                                            <option value="Satteldach"   @if( $customer->roof_type)=="Satteldach" selected @endif >Satteldach</option>
                                                                                            <option value="Flachdach"  @if( $customer->roof_type)=="Flachdach" selected @endif >Flachdach</option>
                                                                                            <option value="Carpot"  @if( $customer->roof_type)=="Carpot" selected @endif >Carpot</option>
                                                                                            <option value="Garage"  @if( $customer->roof_type)=="Garage" selected @endif >Garage</option>
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
                                                                                        <input type="text" class="form-control form-element" name="roof_age" id="roof_age" value="{{ old('roof_age', $customer->roof_age) }}" />
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
                                                                                        <h3 class="bold">Welche Dacheindeckung hat das Dach?</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control textbox" name="tile_name" value="{{ old('tile_name', $customer->tile_name) }}">
                                                                                    
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
                                                                                        <input type="text" class="form-control textbox" name="roof_covering" value=""> 
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
                                                                                        <input type="text" class="form-control textbox" name="roof_pitch" value="{{ old('roof_pitch', $customer->roof_pitch) }}"> 
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
                                                                                            <option value="0" @if($customer->roof_direction == 0) selected @endif>Süden</option>
                                                                                            <option value="45" @if($customer->roof_direction == 45) selected @endif>Süd-west</option>
                                                                                            <option value="90" @if($customer->roof_direction == 90) selected @endif>Westen</option>
                                                                                            <option value="135" @if($customer->roof_direction == 135) selected @endif>Nord-west</option>
                                                                                            <option value="180" @if($customer->roof_direction == 180) selected @endif>Norden</option>
                                                                                            <option value="-135" @if($customer->roof_direction == -135) selected @endif>Nord-ost</option>
                                                                                            <option value="-90" @if($customer->roof_direction == -90) selected @endif>Osten</option>
                                                                                            <option value="-45" @if($customer->roof_direction == -45) selected @endif>Süd-ost</option>  
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
                                                                                            <option value="Gas" @if( $customer->heating_system_type)=="Gas" selected @endif>Gas</option>
                                                                                            <option value="Öl" @if( $customer->heating_system_type)=="Öl" selected @endif>Öl</option>
                                                                                            <option value="Wärmepumpe" @if( $customer->heating_system_type)=="Wärmepumpe" selected @endif>Wärmepumpe</option>
                                                                                            <option value="Nachtspeicher" @if( $customer->heating_system_type)=="Nachtspeicher" selected @endif>Nachtspeicher</option>
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
                                                                                        <input type="text" class="form-control form-element" name="heating_system_age" id="heating_system_age" value="{{ old('heating_system_age', $customer->heating_system_age)}}"/>
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
                                                                                        <input type="text" class="form-control form-element" name="heating_system_year" id="heating_system_year" value="{{ old('heating_system_year', $customer->heating_system_year)}}" />
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
                                                                                    <option value="1" @if($customer->heating_type == "1") selected @endif>Fußbodenheizung</option>
                                                                                    <option value="2" @if($customer->heating_type == "2") selected @endif>Heizkörper</option>
                                                                                    <option value="3" @if($customer->heating_type == "3") selected @endif>Fußbodenheizung + Heizkörper</option>
                                                                                    <option value="4" @if($customer->heating_type == "4") selected @endif>Keine</option>
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
                                                                                    <select name="installation_location" id="installation_location" class="form-control">
                                                                                        <option value="">Bitte wählen</option>
                                                                                        <option value="KG" @if($customer->installation_location == "KG") selected @endif>KG</option>
                                                                                        <option value="EG" @if($customer->installation_location == "EG") selected @endif>EG</option>
                                                                                        <option value="OG" @if($customer->installation_location == "OG") selected @endif>OG</option>
                                                                                        <option value="DG" @if($customer->installation_location == "DG") selected @endif>DG</option>
                                                                                        <option value="SONSTIGES" @if($customer->installation_location == "SONSTIGES") selected @endif>SONSTIGES</option>
                                                                                    </select>

                                                                                        <input type="text" class="form-control" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra', $customer->installation_location_extra)}}" placeholder="SONSTIGIES..">
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
                                                                                        <input type="text" class="form-control form-element" name="annual_consumption" value="{{ old('annual_consumption', $customer->annual_consumption)}}"  />
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
                                                                                        <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption', $customer->annual_heating_energy_consumption)}}" />
                                                                                        <span  id="heat-energy">CMB</span>
                                                                                        <input type="text" class="form-control form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh, , $customer->annual_heating_energy_consumption_kwh')}}" /> 
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
                                                                                            <option value="Ja" @if( $customer->electric_car)=="Ja" selected @endif>Ja</option>
                                                                                            <option value="Nein" @if( $customer->electric_car)=="Nein" selected @endif>Nein</option>
                                                                                        </select>
                                                                                        <!-- When Nein, the below text box should be hidden -->
                                                                                    </div>
                                                                                    <div class="col-md-6 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="electric_car_plan" id="electric_car_plan" value="{{ old('electric_car_plan', $customer->electric_car_plan)}}" style="display:none;" />
                                                                                        <span style="position: absolute; right: 20px;"  id="electric_car_plan_l">ANZAHLE</span>

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="form-group row form-element">
                                                                                    <div class="col-md-12">
                                                                                        <h3 class="bold">Wie viele Kilometer hat das Auto gefahren</h3>
                                                                                    </div>
                                                                                    <div class="col-md-12 flex_me">
                                                                                        <input type="text" class="form-control form-element" name="car_kilo" value="{{ old('car_kilo',  $customer->electric_car_plan)}}"  />
                                                                                        <span style="position: absolute;right: 20px;">km</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="collapse-margin">
                                                        <div class="card-header collapsed" id="headingOne" data-toggle="collapse" role="button" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                            <span class="lead collapse-title collapsed">
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
                                                                                    <option value="Telefonisch" @if($customer->source =="Telefonisch") selected @endif >Telefonisch</option>
                                                                                    <option value="Persönlich"  @if($customer->source =="Persönlich") selected @endif >Persönlich</option>
                                                                                    <option value="Mail"  @if($customer->source =="Mail") selected @endif >Mail</option>
                                                                                    <option value="Nachbar" @if($customer->source =="Nachbar") selected @endif >Nachbar</option>
                                                                                    <option value="Empfehlung" @if($customer->source =="Empfehlung") selected @endif >Empfehlung</option>
                                                                                    <option value="Solarrechner" @if($customer->source =="Solarrechner") selected @endif >Solarrechner</option>
                                                                                    <option value="Herstellerlead" @if($customer->source =="Herstellerlead") selected @endif >Herstellerlead</option>
                                                                                    <option value="Kunde aus Vergangenheit" @if($customer->source =="Kunde aus Vergangenheit") selected @endif >Kunde aus Vergangenheit</option>
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
                                                                                <input type="date" class="form-control form-element" name="request_date" value="{{ old('request_date', $customer->request_date) }}" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-2">
                                                                                <span>Info</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control form-element" name="info" value="{{ old('info', $customer->info) }}">
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
                                                                                                <input type="radio" class="custom-control-input form-element"  @if($customer->document =="on") checked @endif  name="document"  id="customRadio1">
                                                                                                <label class="custom-control-label" for="customRadio1">Ja</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-2">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element"  @if($customer->document =="off") checked @endif name="document" id="customRadio2">
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
                                                                                <div class="star-rating form-element" data-category="interest" data-rating="{{ $customer->interest_rating }}">
                                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                                </div>
                                                                                <input type="hidden" name="interest_rating" value="{{ old('interest_rating', $customer->interest_rating)}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="col-5">
                                                                                <span class="mr-2">Ernsthaftigkeit</span>
                                                                            </div>
                                                                            <div class="col-7">
                                                                                <div class="star-rating form-element" data-category="seriousness" data-rating="{{ $customer->seriousness_rating }}">
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                </div>
                                                                                <input type="hidden" name="seriousness_rating" value="{{ old('seriousness_rating', $customer->seriousness_rating)}}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="col-5">
                                                                                <span class="mr-2">Preisinformation</span>
                                                                            </div>
                                                                            <div class="col-7">
                                                                                <div class="star-rating form-element" data-category="price_information" data-rating="{{ $customer->price_information }}">
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                                </div>
                                                                                <input type="hidden" name="price_information" value="{{ old('price_information', $customer->price_information)}}">
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
                                                                                <textarea name="note" class="form-control form-element" cols="30" rows="5">{{ $customer->note}}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row form-element">
                                                                            <div class="col-md-4">
                                                                                <span>Wann können wir Sie Kontaktieren?</span>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <input type="date" class="form-control form-element" name="appointment" value="{{ $customer->appointment}}">
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
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" @if($customer->appointment_by =="telefonisch") checked @endif id="appointment_by_telefonisch" value="telefonisch">
                                                                                                <label class="custom-control-label" for="appointment_by_telefonisch">telefonisch</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" @if($customer->appointment_by =="E-Mail") checked @endif  id="appointment_by_email" value="E-Mail">
                                                                                                <label class="custom-control-label" for="appointment_by_email">E-Mail</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" @if($customer->appointment_by =="Vor Ort Besuch") checked @endif id="appointment_by_ort" value="Vor Ort Besuch">
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
                                                    <div class="collapse-margin">
                                                        <div class="card-header" id="headingTwo" data-toggle="collapse" role="button" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                            <span class="lead collapse-title collapsed">
                                                                VORGESCHLAGENE VERANTWORTLICHE
                                                            </span>
                                                        </div>
                                                        <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-lg-12 col-md-12">
                                                                        <div class="cards">
                                                                            <div class="card-header">
                                                                                <h4 class="card-title" id="product_title"> </h4>
                                                                            </div>
                                                                            <div class="card-content">
                                                                                <div class="card-body">   
                                                                                        @foreach ($product_list as $select)  
                                                                                            <div class="cards">
                                                                                                <div class="card-content"> 
                                                                                                    <div class="card-body">
                                                                                                        <h4 class="card-title">{{ $select->article_group }}</h4>
                                                                                                        <div class="cards"> 
                                                                                                            <div class="card-content">
                                                                                                                <div class="card-body"> 
                                                                                                                    @foreach ($selectedEmployees as $se)
                                                                                                                        @if($select->product_id == $se->product_id)
                                                                                                                        <div class="chip chip-primary mr-1">
                                                                                                                            <div class="chip-body">
                                                                                                                                <div class="avatar">
                                                                                                                                    <img class="img-fluid" src="{{ asset('images/employee/'.$se->image)}}" alt="{{ $se->name }}" height="20" width="20">
                                                                                                                                </div>
                                                                                                                                <span class="chip-text">{{ $se->name }} {{ $se->lastname }} </span>
                                                                                                                                <div class="chip-closeable">
                                                                                                                                    <i class="feather icon-trash" data-employee-id="{{ $se->employee_id }}" data-product-id="{{ $se->product_id }}" data-new-lead-id="{{ request()->id }}" id="delete"></i>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div> 
                                                                                                                        <div class="modal fade text-left" id="danger" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                <div class="modal-content">
                                                                                                                                    <div class="modal-header bg-danger white">
                                                                                                                                        <h5 class="modal-title" id="myModalLabel120">Danger Modal</h5>
                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                            <span aria-hidden="true">×</span>
                                                                                                                                        </button>
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-body">
                                                                                                                                        Sind Sie sicher, dass Sie diesen Mitarbeiter löschen möchten?
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-footer">
                                                                                                                                        <button type="button" class="btn btn-danger Waves-Effect Waves-Light" id="confirmDelete">Löschen</button>
                                                                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div> 
                                                                                                                        <p class="card-text">
                                                                                                                            <button type="button" data-employee-id="{{ $se->employee_id }}" data-product-id="{{ $se->product_id }}" data-new-lead-id="{{ request()->id }}" class="btn waves-effect waves-light" data-toggle="modal" data-target="#addEmployee">
                                                                                                                                <i class="feather icon-plus"></i> Hinzufügen
                                                                                                                            </button>   
                                                                                                                            <div class="modal fade text-left" id="addEmployee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                                                                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                    <div class="modal-content">
                                                                                                                                        <div class="modal-header bg-primary white">
                                                                                                                                            <h5 class="modal-title" id="myModalLabel160">Wählen Sie Mitarbeiter aus</h5>
                                                                                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                <span aria-hidden="true">×</span>
                                                                                                                                            </button>
                                                                                                                                        </div>
                                                                                                                                        <div class="modal-body">
                                                                                                                                            <select id="employeeSelect" name="employees"  style="width: 100%;">
                                                                                                                                                <!-- Options will be populated via AJAX -->
                                                                                                                                            </select>  
                                                                                                                                        </div>
                                                                                                                                        <div class="modal-footer">
                                                                                                                                            <button type="button" id="saveEmployees" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                                                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Absagen</button>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div> 
                                                                                                                        </p>
                                                                                                                        @else
                                                                                                                            <div class="alert alert-warning" Role="alert">
                                                                                                                                <h4 class="alert-heading"><i class="feather icon-info"></i> Das Produkt ist leer</h4>
                                                                                                                                <p class="mb-0">
                                                                                                                                    Dieses Produkt ist ausgewählt, verfügt aber über keine Mitarbeiter. Bitte wählen Sie das Produkt erneut aus der Liste aus, indem Sie auf die Herztaste klicken und verfügbare Mitarbeiter aus der Liste auswählen
                                                                                                                                </p>
                                                                                                                            </div>
                                                                                                                        @endif
                                                                                                                        
                                                                                                                    @endforeach 
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                        @endforeach


                                                                                        
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                    <div class="col-lg-12 col-md-12">
                                                                        <div class="cards">
                                                                            <div class="card-header">
                                                                                <h4 class="card-title" id="product_title"> </h4>
                                                                            </div>
                                                                            <div class="card-content">
                                                                                <div class="card-body">  
                                                                                        <input type="hidden" name="selectedEmployees" id="selectedEmployees" value="{}">
                                                                                        <div id="product-cards-container"  style="display: flex !important;flex-wrap: wrap;"></div>
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
                    </div> 
                    <div class="col-md-3 col-12"> 
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
                                <i class="feather icon-camera"></i> Hochladen
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
                            <div class="cards">
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
<!-- Deleteing Responsible People  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let employeeId, productId, newLeadId;

        document.querySelectorAll('.feather.icon-trash').forEach(function (deleteButton) {
            deleteButton.addEventListener('click', function () {
                // Get data from button
                employeeId = this.dataset.employeeId;
                productId = this.dataset.productId;
                newLeadId = this.dataset.newLeadId;

                // Show the modal
                $('#danger').modal('show');
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function () {
            // Perform the AJAX request
            fetch('/delete-responsible', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    product_id: productId,
                    new_lead_id: newLeadId
                })
            })
            .then(response => response.json())
            .then(data => {
                $('#danger').modal('hide');
                if (data.success) {
                    location.reload();
                } else {
                    toastr.error(data.message || 'Error in deleting Employee');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

<!-- Adding new Responsible  -->

 <script>
    $(document).ready(function() {
    let selectedEmployees = [];
    let newLeadId = null;
    let productId = null;

    // Open the modal and load employees
    $('[data-target="#addEmployee"]').on('click', function() {
        newLeadId = $(this).data('new-lead-id');
        productId = $(this).data('product-id');
        
        // Fetch available employees
        $.ajax({
            url: '/checkEmployeeAvailability',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                product_id: productId
            },
            success: function(response) {
                // Clear previous options
                $('#employeeSelect').empty();
                
                // Populate select2 with available employees or in case employees
                let employees = response.availableEmployees.length > 0 ? response.availableEmployees : response.inCaseEmployees;
                
                if (employees.length > 0) {
                    employees.forEach(employee => {
                        $('#employeeSelect').append(new Option(employee.name + ' ' + employee.lastname, employee.id));
                    });
                } else {
                    toastr.warning('No employees found for this product.');
                }

                // Initialize or refresh select2
                $('#employeeSelect').select2();
            }
        });
    });

    // Save selected employees
    $('#saveEmployees').on('click', function() {
        selectedEmployees = $('#employeeSelect').val();
        if (selectedEmployees.length > 0) {
            // Send the selected employees to the server
            $.ajax({
                url: '/saveSelectedEmployees',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    new_lead_id: newLeadId,
                    product_id: productId,
                    employee_ids: selectedEmployees
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Employees saved successfully.');
                        $('#addEmployee').modal('hide');
                    } else {
                        toastr.error(response.message || 'An error occurred.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred while saving employees.');
                }
            });
        } else {
            toastr.warning('Please select at least one employee.');
        }
    });
});

 </script>
<!-- Electronic Car -->
<script>
    document.getElementById('electric_car').addEventListener('change', function() {
        var electricCarPlan = document.getElementById('electric_car_plan');
        if (this.value === 'Ja') {
            electricCarPlan.style.display = 'block';
        } else {
            electricCarPlan.style.display = 'none';
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

    document.getElementById('house_year').addEventListener('input', function() {
        var houseYear = parseInt(this.value, 10);
        if (!isNaN(houseYear)) {
            var currentYear = new Date().getFullYear();
            var roofAge = currentYear - houseYear;
            document.getElementById('roof_age').value = roofAge;
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
    document.getElementById('heating_system_type').addEventListener('change', function() {
        var unitSpan = document.getElementById('heat-energy');
        var selectedValue = this.value;
        
        switch (selectedValue) {
            case 'Gas':
                unitSpan.textContent = 'COP';
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
<!-- MAP SCREEN SHOT -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
 
<script>
    "use strict";

    let map;
    let marker;
    let panorama;
    let autocompleteMain;
    let autocompleteAlt;
    let capturedImageDataURL; 
    // Get the elements from the document by their IDs
    const customer_lon = document.getElementById('longitude-input');
    const customer_lat = document.getElementById('latitude-input');
    const customer_lon2 = document.getElementById('longitude-input2');
    const customer_lat2 = document.getElementById('latitude-input2');

    // Initialize variables for selected latitude and longitude
    let selected_lat, selected_lon;

    // Check if the secondary longitude input exists and has a value
    if (customer_lon2 && customer_lat2) {
        // Use the secondary set of coordinates if available
        selected_lat = customer_lat2.value;
        selected_lon = customer_lon2.value;
    } else {
        // Otherwise, use the primary set of coordinates
        selected_lat = customer_lat.value;
        selected_lon = customer_lon.value;
    }
    console.log('lat: '+ selected_lat.value + ' Lon: ' + selected_lon);

    // Now, `selected_lat` and `selected_lon` contain the appropriate values
    let captureButton;
    function initMap() {
        const CONFIGURATION = {
            mapOptions: {
                center: { lat: selected_lat, lng: selected_lon},
                fullscreenControl: false,
                mapTypeControl: false,
                streetViewControl: false,
                zoom: 22,
                zoomControl: true,
                maxZoom: 50,
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
        document.getElementById(`region-input${prefix}`).value = addressComponents.administrative_area_level_1;
        document.getElementById(`country-input${prefix}`).value = addressComponents.country;

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
            $('#employees').select2(); 
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



<!-- {{--Select Product and buttons --}} -->  
 <script> 
    document.addEventListener("DOMContentLoaded", function () {
    let selectedProducts = [];
    let selectedEmployees = {};

    document.querySelectorAll('.products').forEach((card, index) => {
        const checkbox = card.querySelector('input[name="product_id[]"]');
        const serviceSelect = card.querySelector('select[name="service"]');
        const statusSpan = card.querySelector(`#interested-${index}`);
        const heartButton = document.getElementById(`${index}Like`);
        const heartIcon = heartButton ? heartButton.querySelector('.heart-icon') : null;

        console.log(`Card ${index}:`, {
            checkbox,
            serviceSelect,
            statusSpan,
            heartButton,
            heartIcon
        });

        if (!checkbox || !serviceSelect || !heartButton || !heartIcon) {
            console.error(`Missing elements in card at index ${index}`);
            return;
        }

        updateHeartButton(checkbox.checked, heartIcon, heartButton);

        heartButton.addEventListener('click', (event) => {
            checkbox.checked = !checkbox.checked;
            card.classList.toggle('selected', checkbox.checked);

            const productId = checkbox.value;
            const service = serviceSelect.value;

            if (checkbox.checked) {
                statusSpan.innerHTML = 'Interessiert';
                updateHeartButton(true, heartIcon, heartButton);
                addToSelectedProducts(productId, service);
                fetchEmployees(productId, index);
            } else {
                statusSpan.innerHTML = '';
                updateHeartButton(false, heartIcon, heartButton);
                removeFromSelectedProducts(productId);
                clearEmployees(productId, index);
            }

            event.stopPropagation();
        });

        serviceSelect.addEventListener('change', (event) => {
            const productId = checkbox.value;
            const service = event.target.value;

            if (checkbox.checked) {
                updateServiceInSelectedProducts(productId, service);
            }
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

    function addToSelectedProducts(productId, service) {
        selectedProducts.push({ product_id: productId, service });
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

    function updateSelectedProductsInput() {
        console.log('Selected Products:', selectedProducts);

        const productsInput = document.querySelector('#products');
        if (productsInput) {
            productsInput.value = JSON.stringify(selectedProducts);
        } else {
            console.error('Products input element not found.');
        }
    }

    function fetchEmployees(productId, index) {
        console.log('Sending request for product ID:', productId);

        fetch('/checkEmployeeAvailability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const cardTitle = data.cardTitle || 'Unknown Group';
            const availableEmployees = data.availableEmployees || [];
            const inCaseEmployees = data.inCaseEmployees || [];

            if (availableEmployees.length === 0 && inCaseEmployees.length > 0) {
                createProductCard(inCaseEmployees, index, productId, true, cardTitle);
            } else {
                createProductCard(availableEmployees, index, productId, false, cardTitle);
            }
        })
        .catch(error => console.error('Error fetching employees:', error));
    }

    function createProductCard(employees, index, productId, showWarning, cardTitle) {
    const container = document.getElementById('product-cards-container');

    // Create card element
    const card = document.createElement('div');
    card.className = 'card';
    card.id = `product-card-${index}`;

    // Card header
    const cardHeader = document.createElement('div');
    cardHeader.className = 'card-header';
    const cardTitleElement = document.createElement('h4');
    cardTitleElement.className = 'card-title';
    cardTitleElement.innerText = cardTitle; // Set the title directly from the backend response

    cardHeader.appendChild(cardTitleElement);
    card.appendChild(cardHeader);

    // Card content
    const cardContent = document.createElement('div');
    cardContent.className = 'card-content';
    const cardBody = document.createElement('div');
    cardBody.className = 'card-body';

    // Warning label
    if (showWarning) {
        const warningLabel = document.createElement('div');
        warningLabel.className = 'alert alert-warning';
        warningLabel.innerText = 'Warning: This product doesn\'t have available employees, showing in-case employees.';
        cardBody.appendChild(warningLabel);
    }

    // Employees container
    const employeesContainer = document.createElement('div');
    employeesContainer.id = `employees-container-${index}`;
    employeesContainer.className = 'mt-2';

    // Create Select2 for re-adding employees
    const selectContainer = document.createElement('div');
    const selectElement = document.createElement('select');
    selectElement.className = 'form-control select2';
    selectElement.setAttribute('multiple', 'multiple');
    selectElement.setAttribute('data-placeholder', 'Mitarbeiter hinzufügen');

    employees.forEach(employee => {
        const option = document.createElement('option');
        option.value = employee.id;
        option.text = `${employee.name} ${employee.lastname} - ${employee.position}`;
        selectElement.appendChild(option);
    });

    selectContainer.appendChild(selectElement);
    cardBody.appendChild(selectContainer);

    employees.forEach(employee => {
        const chip = document.createElement('div');
        chip.className = 'chip chip-danger mr-1';
        chip.innerHTML = `
            <div class="chip-body">
                <div class="avatar">
                    <img class="img-fluid" src="${assetPath('images/employee/' + employee.image)}" alt="generic img placeholder" height="20" width="20">
                </div>
                <span class="chip-text">${employee.name} ${employee.lastname} - ${employee.position}</span>
                <div class="chip-closeable" data-employee-id="${employee.id}" data-employee-name="${employee.name}" data-employee-position="${employee.position}" data-product-id="${productId}">
                    <i class="feather icon-x"></i>
                </div>
            </div>
        `;
        employeesContainer.appendChild(chip);

        // Add to selected employees
        if (!selectedEmployees[productId]) {
            selectedEmployees[productId] = [];
        }
        selectedEmployees[productId].push({ id: employee.id, name: employee.name, position: employee.position });
    });

    cardBody.appendChild(employeesContainer);
    cardContent.appendChild(cardBody);
    card.appendChild(cardContent);
    container.appendChild(card);

    // Initialize Select2
    $(selectElement).select2();

    // Add event listener for Select2 change
    $(selectElement).on('select2:select', function(e) {
        const employeeId = e.params.data.id;
        const employee = employees.find(emp => emp.id == employeeId);
        addEmployeeToList(employee, index, productId);
        // Remove option from Select2
        $(selectElement).find(`option[value="${employeeId}"]`).remove();
        $(selectElement).trigger('change');
    });

    // Add event listeners for removing employees
    card.querySelectorAll('.chip-closeable').forEach(closeButton => {
        closeButton.addEventListener('click', function(event) {
            const employeeId = closeButton.getAttribute('data-employee-id');
            const employeeName = closeButton.getAttribute('data-employee-name');
            const employeePosition = closeButton.getAttribute('data-employee-position');
            const productId = closeButton.getAttribute('data-product-id');
            removeEmployeeFromList(employeeId, employeeName, employeePosition, productId);
            closeButton.parentElement.parentElement.remove(); // Remove the chip
            updateSelectedEmployees();

            // Re-add employee to Select2
            const option = document.createElement('option');
            option.value = employeeId;
            option.text = `${employeeName} ${employeePosition}`;
            selectElement.appendChild(option);
            $(selectElement).trigger('change');
        });
    });

    updateSelectedEmployees();
    }   

    function addEmployeeToList(employee, index, productId) {
        const employeesContainer = document.getElementById(`employees-container-${index}`);
        const chip = document.createElement('div');
        chip.className = 'chip chip-danger mr-1';
        chip.innerHTML = `
            <div class="chip-body">
                <div class="avatar">
                    <img class="img-fluid" src="${assetPath('images/employee/' + employee.image)}" alt="EMP" height="20" width="20">
                </div>
                <span class="chip-text">${employee.name} ${employee.lastname} - ${employee.position}</span>
                <div class="chip-closeable" data-employee-id="${employee.id}" data-employee-name="${employee.name}" data-employee-position="${employee.position}" data-product-id="${productId}">
                    <i class="feather icon-x"></i>
                </div>
            </div>
        `;
        employeesContainer.appendChild(chip);

        if (!selectedEmployees[productId]) {
            selectedEmployees[productId] = [];
        }
        selectedEmployees[productId].push({ id: employee.id, name: employee.name, position: employee.position });

        // Add event listener for removing the re-added employee
        const closeButton = chip.querySelector('.chip-closeable');
        closeButton.addEventListener('click', function(event) {
            const employeeId = closeButton.getAttribute('data-employee-id');
            const employeeName = closeButton.getAttribute('data-employee-name');
            const employeePosition = closeButton.getAttribute('data-employee-position');
            const productId = closeButton.getAttribute('data-product-id');
            removeEmployeeFromList(employeeId, employeeName, employeePosition, productId);
            closeButton.parentElement.parentElement.remove(); // Remove the chip
            updateSelectedEmployees();
            // Re-add employee to Select2
            const option = document.createElement('option');
            option.value = employeeId;
            option.text = `${employeeName} ${employeePosition}`;
            const selectElement = document.querySelector(`#product-card-${index} .select2`);
            selectElement.appendChild(option);
            $(selectElement).trigger('change');
        });

        updateSelectedEmployees();
    }

    function removeEmployeeFromList(employeeId, employeeName, employeePosition, productId) {
        // Remove employee from selectedEmployees object based on id, name, and position
        if (selectedEmployees[productId]) {
            selectedEmployees[productId] = selectedEmployees[productId].filter(employee =>
                !(employee.id == employeeId && employee.name == employeeName && employee.position == employeePosition)
            );
            if (selectedEmployees[productId].length === 0) {
                delete selectedEmployees[productId];
            }
        }
    }

    function clearEmployeesList(index, productId) {
        const card = document.getElementById(`product-card-${index}`);
        if (card) {
            card.remove();
        }

        // Remove all employees associated with the productId
        delete selectedEmployees[productId];

        updateSelectedEmployees();
    }

    function updateSelectedEmployees() {
        document.getElementById('selectedEmployees').value = JSON.stringify(selectedEmployees);
    }

    function assetPath(path) {
        return '{{ asset('') }}' + path;
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
                if (heatingSystemAge < roofAge) {
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