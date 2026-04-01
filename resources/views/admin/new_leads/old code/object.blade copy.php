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

.font-bold{
    font-weight: bold;
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
     .map_section {
    display: none;
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
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Lead-liste</a>
                                </li>
                                 <li class="breadcrumb-item active"><a  >Neue Objekt</a>
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
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                     <div class="col-xl-4 col-md-12 col-sm-12  card-fixed">
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
                    <div class="col-xl-7 col-sm-12 col-md-12 card-scrollable ">
                        <div class="row"> 
                            <div class="col-4">
                                <h2 class="content-header-title float-left ">KUNDENDATEN</h2>
                            </div>
                            <div class="col-8 mb-2"> 
                                <input type="hidden" name="answer_input" id="answer_input" value="0">
                                <input type="hidden" name="total_number_input" id="total_number_input" value="30">
                                <label for="" id="answered_number">0</label> / <label for="" id="total_number">30</label>
                                
                                <div class="progress progress-bar-primary progress-lg">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                        <span id="percent">0%</span>
                                    </div>
                                </div>
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
                                                ->where('users.name', '=', auth()->user()->name)
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

                                         <div class="col-12 p-2" style="    padding-top: 0px !important;">
                                                    <div class="col-md-12">
                                                        <div class="d-flex align-items-center">
                                                            <div class="col-5 p-0">
                                                                <span class="mr-2">Interesse</span>
                                                            </div>
                                                            <div class="col-7">
                                                                <div class="star-rating form-element" data-category="interest" data-rating="{{$leads->interest_rating}}">
                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                    <span class="star form-element"><i class="fa fa-star"></i></span>
                                                                </div>
                                                                <input type="hidden" name="interest_rating" value="{{$leads->interest_rating}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex align-items-center">
                                                            <div class="col-5 p-0">
                                                                <span class="mr-2">Ernsthaftigkeit  </span>
                                                            </div>
                                                            <div class="col-7">
                                                                <div class="star-rating form-element" data-category="seriousness" data-rating="{{$leads->seriousness_rating}}">
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                </div>
                                                                <input type="hidden" name="seriousness_rating" value="{{$leads->seriousness_rating}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex align-items-center">
                                                            <div class="col-5 p-0">
                                                                <span class="mr-2">Preisinformation</span>
                                                            </div>
                                                            <div class="col-7">
                                                                <div class="star-rating form-element" data-category="price_information" data-rating="{{$leads->price_information}}">
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                    <span class="star"><i class="fa fa-star"></i></span>
                                                                </div>
                                                                <input type="hidden" name="price_information" value="{{$leads->price_information}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div> 
                                       
                                   </div>
                            </div>

                        </div> 
                        <div class="row">
                            <div class="col-6">
                                 <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Anfrage-Datum</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <input type="date" class="form-control form-element" name="request_date" value="{{ now()->format('Y-m-d') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row form-element">
                                        <div class="col-md-12">
                                            <span>Objektname</span>
                                        </div>
                                        <div class="col-md-12 p-0">
                                            <input type="text" class="form-control form-element" name="object_name" value="Neue Objekt" />
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
                                                <input type="hidden" id="elevation-input2" placeholder="Elevation in meters" name="elevation" value="">
                                                <input type="hidden" class="form-control text form-element" value="{{old('postcode')}}" name="postcode" id="postal_code-input2">
                                                <input type="hidden" class="form-control text form-element" value="{{old('city2')}}" name="city" id="locality-input2">
                                                <input type="hidden" class="form-control text form-element" value="{{old('street')}}" name="street" id="street-input2">

                                            </div>
                                        </div> 
                                    </div> 
                                </div> 
                                
                            <div class="col-6">
                                 <div class="col-12">
                                        <div class="form-group row form-element">
                                            <div class="col-md-2">
                                                <span>Notizen</span>
                                            </div>
                                            <div class="col-md-10">
                                                <textarea name="note" class="form-control form-element" cols="30" rows="5">
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
                                                <ul class="list-unstyled mb-0" style="    display: flex;flex-direction: column;">
                                                    <li class="d-inline-block mb-1">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_telefonisch" value="telefonisch">
                                                                <label class="custom-control-label" for="appointment_by_telefonisch">telefonisch</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mb-1">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input form-element" name="appointment_by" id="appointment_by_email" value="E-Mail">
                                                                <label class="custom-control-label" for="appointment_by_email">E-Mail</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    
                                                </ul>
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
                                                        <div class="col-12 d-flex" >
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-4">
                                                                    <h3 class="bold">Objektart</h3>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <select name="objective" id="" class="form-control text">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="EFH">EFH</option>
                                                                        <option value="MFH">MFH</option>
                                                                        <option value="Gewerbe">Gewerbe</option>
                                                                        <option value="others">Sonstiges</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                                <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Baujahr</h3>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control text form-element" name="house_year" id="house_year" value="{{ old('house_year') }}" />
                                                                </div>
                                                            </div>
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Wohneinheiten</h3>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control text textbox" name="number_we" value="{{ old('number_we') }}">
                                                                
                                                                </div>
                                                            </div>

                                                                <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Geschosse  </h3>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                <input type="text" class="form-control text"  name="number_stories" value="{{ old('number_stories') }}">
                                                                
                                                                </div>
                                                            </div>
                                                        </div>

                                                    

                                                        <div class="col-12 d-flex">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Wohnfläche</h3>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                <input type="text" class="form-control text" name="living_space" value="{{ old('living_space') }}">
                                                                    <span style="position: absolute; right: 20px;"> m²</span>
                                                                
                                                                </div>  
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Nutzfläche</h3>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                <input type="text" class="form-control text" name="unusable_space"  value="{{ old('unusable_space') }}">
                                                                    <span style="position: absolute; right: 20px;"> m²</span> 
                                                                </div>  
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Personenanzahl</h3>
                                                                </div>
                                                                <div class="col-md-12 flex_me">
                                                                <input type="text" class="form-control text" name="number_people" id="number_people"  value="{{ old('number_people') }}" > 
                                                                </div>  
                                                            </div> 
                                                        </div>

                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <span>Bemerkung</span>
                                                            </div>
                                                            <!-- //Add this to database -->
                                                            <div class="col-md-12"> 
                                                                <textarea name="object_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">  
                                                                </textarea> 
                                                            </div>
                                                        </div>


                                                    
                                                        <div class="col-12"><h2 class="primary"><strong>DACH-INFORMATION</strong></h2><hr></div> 
                                                        <div class="col-12 d-flex">
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Dachform</h3>
                                                                </div>
                                                                <div class="col-md-12 ">
                                                                    <select class="form-control text form-element" name="roof_type" id="roof">
                                                                        <option selected></option>
                                                                        <option value="Satteldach">Satteldach</option>
                                                                        <option value="Flachdach">Flachdach</option>
                                                                        <option value="Carport">Carport</option>
                                                                        <option value="Garage">Garage</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Alter</h3>
                                                                </div>
                                                                <div class="col-md-12  ">
                                                                    <input type="text" class="form-control text form-element" name="roof_age" id="roof_age" value="{{ old('roof_age') }}" />
                                                                    <span style="position: absolute; right: 20px; top:10px;">Jahr</span>
                                                                
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <span id="roof_age_error" class="text-danger"></span>
                                                                </div>
                                                                
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Eindeckung </h3>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control text textbox" name="roof_covering" value="{{ old('roof_covering') }}"> 
                                                                </div>
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Neigung</h3>
                                                                        
                                                                </div>
                                                                <div class="col-md-12 flex_me"> 
                                                                    <select name="roof_pitch" id="" class="form-control text">
                                                                            <option value="">Auswählen</option>
                                                                            <option value="0">0</option>
                                                                            <option value="5">5</option>
                                                                            <option value="10">10</option>
                                                                            <option value="15"> 15</option>
                                                                            <option value="20"> 20</option> 
                                                                            <option value="25"> 25</option> 
                                                                            <option value="30"> 30</option> 
                                                                            <option value="35"> 35</option> 
                                                                            <option value="40"> 40</option> 
                                                                            <option value="45"> 45</option> 
                                                                            <option value="50"> 50</option> 
                                                                        </select>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <h3 class="bold">Ausrichtung</h3>
                                                                </div>
                                                                <div class="col-md-12 ">
                                                                    <select name="roof_direction" id="" class="form-control text"> 
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

                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <span>Bemerkung</span>
                                                                </div>
                                                                <!-- //Add this to database -->
                                                                <div class="col-md-12"> 
                                                                    <textarea name="roof_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">  
                                                                    </textarea> 
                                                                </div>
                                                            </div> 
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="col-12"><h2 class="primary"><strong>HEIZUNGS-INFORMATION</strong></h2><hr></div> 
                                                            <div class="col-12 d-flex">
                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold">Heiztechnik</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <select class="form-control text form-element" name="heating_system_type" id="heating_system_type">
                                                                            <option selected disabled> </option>
                                                                            <option value="Gas">Gas</option>
                                                                            <option value="Öl">Öl</option>
                                                                            <option value="Wärmepumpe">Wärmepumpe</option>
                                                                            <option value="Nachtspeicher">Nachtspeicher</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold">Alter</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <input type="text" class="form-control text form-element" name="heating_system_age" id="heating_system_age" value="{{ old('heating_system_age')}}"/>
                                                                        <input type="hidden" class="form-control text form-element" name="heating_system_year" id="heating_system_year" value="{{ old('heating_system_year')}}" />

                                                                        <span style="position: absolute; right: 20px;">Jahr</span>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <span id="heating_system_age_error" class="text-danger"></span>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold">Heizsystem</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                    <select name="heating_type" id="heating_type" class="form-control text">
                                                                            <option value="">Bitte wählen</option>
                                                                            <option value="underfloor_heating">Fußbodenheizung</option>
                                                                            <option value="heating_system">Heizkörper</option>
                                                                            <option value="both" >Fußbodenheizung + Heizkörper</option>
                                                                            <option value="none">Keine</option>
                                                                        </select>
                                                                    </div>  
                                                                </div>

                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold">Ort</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <select name="installation_location" id="" class="form-control text">
                                                                            <option value="">Bitte wählen</option>
                                                                            <option value="KG">KG</option>
                                                                            <option value="EG">EG</option>
                                                                            <option value="OG"> OG</option>
                                                                            <option value="DG"> DG</option> 
                                                                            <option value="SONSTIGES"> SONSTIGES</option> 
                                                                        </select>
                                                                        <input type="text" class="form-control text" name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra')}}" placeholder="sonstiges">
                                                                    </div>  
                                                                </div>

                                                                    
                                                            </div>
                                                            <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <span>Bemerkung</span>
                                                                </div>
                                                                <!-- //Add this to database -->
                                                                <div class="col-md-12"> 
                                                                    <textarea name="heating_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">  
                                                                    </textarea> 
                                                                </div>
                                                            </div> 
                                                            <div class="col-12"><h2 class="primary"><strong>ENERGIEVERBRAUCH</strong></h2><hr></div> 

                                                        
                                                            <div class="col-12 d-flex">
                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold">Stromverbrauch</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <input type="text" class="form-control text form-element" name="annual_consumption" value="{{ old('annual_consumption')}}"  />
                                                                        <span style="position: absolute;right: 20px;">kWh</span>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3>Heizenergie</he>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <!-- Conersion of CMB to KWH, cmb * 10  -->
                                                                        <input type="text" class="form-control text form-element mr-1" name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption')}}" />
                                                                        <span  id="heat-energy">m³</span>
                                                                        <input type="hidden" class="form-control text form-element mr-1" name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh')}}" />  

                                                                    </div>
                                                                </div> 
                                                            </div>

                                                                <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <span>Bemerkung</span>
                                                                </div>
                                                                <!-- //Add this to database -->
                                                                <div class="col-md-12"> 
                                                                    <textarea name="energy_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">  
                                                                    </textarea> 
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-12"><h2 class="primary"><strong>E-MOBILITÄT</strong></h2><hr></div> 

                                                            <div class="col-12 d-flex">
                                                                <div class="form-group row form-element">
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold" >Elektroauto</h3>
                                                                    </div>
                                                                    <br>
                                                                    <div class="col-md-12 flex_me">
                                                                        <select class="form-control text form-element" name="electric_car" id="electric_car">
                                                                            <option selected disabled></option>
                                                                            <option value="Ja">Ja</option>
                                                                            <option value="Nein">Nein</option>
                                                                            <option value="Geplant">Geplant</option>
                                                                        </select>
                                                                        <!-- When Nein, the below text box should be hidden -->
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row form-element" style="display:none;" id="electric_car_plan" > 
                                                                        <div class="col-md-12">
                                                                        <h3 class="bold" >Anzahl</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me">
                                                                        <input type="text" class="form-control text form-element" name="electric_car_plan" value="{{ old('electric_car_plan')}}"  />
                                                                        </div>
                                                                </div>

                                                                    <div class="form-group row form-element">  
                                                                    <div class="col-md-12">
                                                                        <h3 class="bold" >Fahrleistung</h3>
                                                                    </div>
                                                                    <div class="col-md-12 flex_me"> 
                                                                        <input type="text" class="form-control text form-element" name="car_kilo" value="{{ old('car_kilo')}}"  />
                                                                        <span style="position: absolute;right: 20px;">km</span>

                                                                    </div> 
                                                                </div>
                                                            </div>
                                                            
                                                                <div class="form-group row form-element">
                                                                <div class="col-md-12">
                                                                    <span>Bemerkung</span>
                                                                </div>
                                                                <!-- //Add this to database -->
                                                                <div class="col-md-12"> 
                                                                    <textarea name="car_remark" style="text-align: left;width: 100%;height: 50px;border-radius: 7px;border: 1px solid #c6c6c6;">  
                                                                    </textarea> 
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
                  <div class="col-xl-7 col-md-12 map_section " style="     position: absolute;   right: 41px;" > 
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
                        <div class="col-lg-9">
                            <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn">
                                <i class="feather icon-camera"></i> Screenshot
                            </a>
                            <div id="screenshot-preview"></div>
                            
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
    document.getElementById('heating_system_type').addEventListener('change', function() {
        var unitSpan = document.getElementById('heat-energy');
        var selectedValue = this.value;
        
        switch (selectedValue) {
            case 'Gas':
                unitSpan.textContent = 'CBM/kWh';
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
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>
    <script>
    "use strict";

    let autocomplete1, autocomplete2;
    let elevationService;
    let placeSelected1 = false, placeSelected2 = false; // Track user selection
    let streetView, map; // Declare Street View and Map globally

    function initAutocomplete() {
        elevationService = new google.maps.ElevationService();

        map = new google.maps.Map(document.getElementById("gmp-map"), {
            center: { lat: 50.1109, lng: 8.6821 }, // Default location (Frankfurt, Germany)
            zoom: 15,
            streetViewControl: true, // Enable Street View control
        });
        streetView = map.getStreetView();
        
        // Initialize autocomplete for both address fields
        autocomplete1 = new google.maps.places.Autocomplete(document.getElementById('full_address2'), {
            fields: ['address_components', 'geometry'],
            types: ['geocode']
        });

        autocomplete2 = new google.maps.places.Autocomplete(document.getElementById('full_address2'), {
            fields: ['address_components', 'geometry'],
            types: ['geocode']
        });

        // Primary Address Selection
        autocomplete1.addListener('place_changed', function () {
            placeSelected1 = true;
            let place = autocomplete1.getPlace();
            if (!place.geometry) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus.');
                document.getElementById('full_address2').value = '';
                placeSelected1 = false;
                return;
            }
            extractAddressComponents(place, ''); // No suffix for primary
        });

        // Alternative Address Selection
        autocomplete2.addListener('place_changed', function () {
            placeSelected2 = true;
            let place = autocomplete2.getPlace();
            if (!place.geometry) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus.');
                document.getElementById('full_address2').value = '';
                placeSelected2 = false;
                return;
            }
            extractAddressComponents(place, '2'); // "2" for alternative address
        });

        // Prevent users from typing an address manually
        document.getElementById('full_address2').addEventListener('blur', function () {
            if (!placeSelected1) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
                document.getElementById('full_address2').value = '';
            }
        });

        document.getElementById('full_address2').addEventListener('blur', function () {
            if (!placeSelected2) {
                showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
                document.getElementById('full_address2').value = '';
            }
        });
    }

    function extractAddressComponents(place, suffix) {
        let addressComponents = {
            street_number: '',
            route: '',
            locality: '',
            postal_code: ''
        };

        place.address_components.forEach(component => {
            const type = component.types[0];
            if (addressComponents.hasOwnProperty(type)) {
                addressComponents[type] = component.long_name;
            }
        });

        const street = `${addressComponents.route} ${addressComponents.street_number}`.trim();
        const city = addressComponents.locality;
        const postcode = addressComponents.postal_code;
        const latitude = place.geometry.location.lat();
        const longitude = place.geometry.location.lng();

        // Assign extracted values to respective fields
        document.getElementById(`street-input${suffix}`).value = street || '';
        document.getElementById(`locality-input${suffix}`).value = city || '';
        document.getElementById(`postal_code-input${suffix}`).value = postcode || '';
        document.getElementById(`latitude-input${suffix}`).value = latitude;
        document.getElementById(`longitude-input${suffix}`).value = longitude;

        // Get elevation
        elevationService.getElevationForLocations({
            locations: [{ lat: latitude, lng: longitude }]
        }, function (results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById(`elevation-input${suffix}`).value = results[0].elevation.toFixed(2);
            }
        });

        // Check for duplicates or neighbors
        checkCustomer(street, postcode, latitude, longitude);
    }

    function checkCustomer(street, postcode, latitude, longitude) {
        const url = `/check-new-leads/${encodeURIComponent(street)}/${postcode}/${latitude}/${longitude}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'duplicate' || data.status === 'neighbor') {
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

                    if (data.status === 'duplicate') {
                        tableHTML += `
                            <tr>
                                <td>${data.customer.name}</td>
                                <td>${data.customer.lastname}</td>
                                <td>${data.customer.full_address}</td>
                                <td>-</td>
                                <td><a href="/new_lead_profile/${data.customer.id}" class="btn btn-primary">Profil anzeigen</a></td>
                            </tr>`;
                    }

                    if (data.status === 'neighbor') {
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
        Swal.fire({
            icon: 'warning',
            title: 'Fehler!',
            text: message,
        });
    }

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

    document.addEventListener('DOMContentLoaded', initAutocomplete);
</script>





<script>
    let rowIndex = 0; // Index for dynamic rows

$(document).ready(function () {
    // Add a new row
    $('#add_new').click(function () {
        // Validate existing rows
        let isValid = true;
        let validationMessages = [];

        $('#product_table tbody tr').each(function (index) {
            const product = $(this).find('[name*="[product_id]"]').val();
            const service = $(this).find('[name*="[service]"]').val();
            const employee = $(this).find('[name*="[employee_id]"]').val();

            if (!product) {
                isValid = false;
                validationMessages.push(`❌ Zeile ${index + 1}: Produkt fehlt`);
            } else {
                validationMessages.push(`✅ Zeile ${index + 1}: Produkt ausgewählt`);
            }

            if (!service) {
                isValid = false;
                validationMessages.push(`❌ Zeile ${index + 1}: Service fehlt`);
            } else {
                validationMessages.push(`✅ Zeile ${index + 1}: Service ausgewählt`);
            }

            if (!employee) {
                isValid = false;
                validationMessages.push(`❌ Zeile ${index + 1}: Mitarbeiter fehlt`);
            } else {
                validationMessages.push(`✅ Zeile ${index + 1}: Mitarbeiter ausgewählt`);
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: `<ul>${validationMessages.map(msg => `<li>${msg}</li>`).join('')}</ul>`,
                confirmButtonText: 'OK'
            });
            return;
        }

        // Add a new row
        $('#product_table tbody').append(`
            <tr>
                <td class="p-0 d-flex align-items-center">
                    <img src="" alt="Product Image" class="product-image mr-2" style="width: 30px; height: 30px; border-radius: 50%; display: none;">
                    <select name="product[${rowIndex}][product_id]" class="form-control select2 product-select" style="width: 100%;">
                        <option value="">Auswählen</option>
                        @foreach ($articles as $item)
                            <option value="{{ $item->id }}" data-image="{{ asset('images/articles/'.$item->image) }}">
                                {{ $item->article_group }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="p-0">
                    <select name="product[${rowIndex}][service]" class="form-control select2" style="width: 100%;">
                        <option value="">Auswählen</option>
                        <option value="complete">Komplettlösung</option>
                        <option value="montage">Montage</option>
                        <option value="product">Produkt</option>
                        <option value="plan">Planung</option>
                        <option value="maintenance">Wartung</option>
                        <option value="repair">Reparatur</option>
                        <option value="emergency">Notdienst</option>
                        <option value="others">Sonstiges</option>
                    </select>
                </td>
                <td class="p-0 d-flex align-items-center">
                    <img src="" alt="Employee Image" class="employee-image mr-2" style="width: 30px; height: 30px; border-radius: 50%; display: none;">
                    <select name="product[${rowIndex}][employee_id]" class="form-control select2 employee-select" style="width: 100%;">
                        <option value="">Auswählen</option>
                        @foreach ($employees as $emp)
                            @php
                                $defaultImage = $emp->gender === "Male" ? $male : $female;
                                $imageUrl = $emp->image ? asset($imagePath . $emp->image) : asset($defaultImage);
                            @endphp
                            <option value="{{ $emp->id }}" data-image="{{ $imageUrl }}" data-shortname="{{ substr($emp->name, 0, 1) }}.{{ substr($emp->lastname, 0, 1) }}.">
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
        initializeSelect2();

        // Increment the row index
        rowIndex++;
    });

    // Remove a row
    $(document).on('click', '#delete_row', function () {
        $(this).closest('tr').remove();
    });

    // Initialize select2 with custom templates on load
    function initializeSelect2() {
        $('.select2').select2({
            templateResult: formatSelectOption,
            templateSelection: formatSelectOption
        });
    }

    // Custom template to show images inside select2 options
    function formatSelectOption(option) {
        if (!option.id) {
            return option.text;
        }

        const imageUrl = $(option.element).data('image');
        if (imageUrl) {
            return $(`<span><img src="${imageUrl}" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 8px;"> ${option.text}</span>`);
        } else {
            return option.text;
        }
    }

    // Initialize handlers on page load
    initializeSelect2();
});

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


@endsection