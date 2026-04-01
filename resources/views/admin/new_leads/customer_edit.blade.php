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
             
        </div>
        <div class="content-body">
            
            <form class="form form-horizontal custom-file-upload" method="post" id="customer_form"
                action="{{ action('App\Http\Controllers\NewLeadsController@update') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" value="{{$id}}" name="id">  
                <input type="hidden" name="alternative_id" value="{{ request()->alternative }}">
                @php
                    $imagePath = 'images/employee/';
                    $male = 'images/gender/male.png';
                    $female = 'images/gender/female.png';
                @endphp 
            
            
                    <div class="row match-height"> 
                        <div class="col-xl-4 col-sm-4 col-md-4   ">
                            <div class="row">  
                                
                                <input type="hidden" value="{{$id}}" name="lead_id"> 
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

                                            <input id="input_name" type="hidden" name="name" value="{{ $leads->name }}">
                                            <input id="input_lastname" type="hidden" name="lastname" value="{{ $leads->lastname }}">



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
                                                value="{{ $alternative && $alternative->request_date ? \Carbon\Carbon::parse($alternative->request_date)->format('Y-m-d') : '' }}"> 
                                            </div>
                                        </div>
                                    </div>
                              
                                    <div class="col-12">
                                        <div class="form-group row form-element">
                                            <div class="col-md-12">
                                                <span>Objektname</span>
                                            </div>
                                            <div class="col-md-12 p-0">
                                                <input type="text" class="form-control text form-element" name="object_name" value="{{ $alternative->object_name ?? '' }}"
                                                />
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
                                                    <Option value="Normal" @if(isset($inquiry->periority) && $inquiry->periority == 'Normal') selected @endif>Normal</Option>
                                                    <Option value="Dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Dringend') selected @endif>Dringend</Option>
                                                    <Option value="Sehr dringend" @if(isset($inquiry->periority) && $inquiry->periority == 'Sehr dringend') selected @endif>Sehr dringend</Option>
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
                                                    <Option value="{{ $br->id }}"@if(isset($inquiry->branch_id) && $inquiry->branch_id == $br->id) selected @elseif($leads->branch == $br->id) selected  @endif >
                                                        {{ $br->branch }}
                                                    </Option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row form-element">
                                            <div class="col-md-12">
                                                <span>STR./NR./PLZ./ORT</span>
                                            </div>
                                            <div class="col-md-12 p-0">
                                                <div class="d-flex align-items-center">
                                                    <input id="full_address" type="text" class="form-control text form-element"
                                                        placeholder="Adresse eingeben" name="full_address"
                                                        value="{{ $alternative->street }}, {{ $alternative->postcode}} {{ $alternative->city }}">                                                    
                                                    <button type="button" class="btn btn-icon rounded-circle btn-warning ml-1" id="show_map">
                                                        <i class="feather icon-map"></i>
                                                    </button>
                                                </div>

                                                <input type="hidden" id="latitude-input" name="latitude"
                                                    value="{{ old('latitude', optional($alternative)->lat) }}">

                                                <input type="hidden" id="longitude-input" name="longitude"
                                                    value="{{ old('longitude', optional($alternative)->lon) }}">

                                                <input type="hidden" id="elevation-input" placeholder="Elevation in meters" name="elevation"
                                                    value="{{ old('elevation', optional($alternative)->elevation) }}">

                                                <input type="hidden" class="form-control text form-element"
                                                    value="{{ old('postcode', optional($alternative)->postcode) }}" name="postcode" id="postal_code-input">

                                                <input type="hidden" class="form-control text form-element"
                                                    value="{{ old('city', optional($alternative)->city) }}" name="city" id="locality-input">

                                                <input type="hidden" class="form-control text form-element"
                                                    value="{{ old('street', optional($alternative)->street) }}" name="street" id="street-input">
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="col-12">
                                        <div class="form-group row form-element">
                                            <div class="col-md-12">
                                                <span>Notizen</span>
                                            </div>
                                            <div class="col-md-12 p-0">
                                                    <textarea name="note" id="" cols="30" rows="3" class="form-control">
                                                     {{ old('note',$alternative->note ?? '') }} 
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
                    <div class="row match-height"> 
                        <div class="col-xl-12 col-sm-12 col-md-12">
                            <div class="row mt-2">
                                <div class="card mb-1 shadow-sm">
                                    <div class="card-header  d-flex justify-content-between align-items-center mb-2">
                                        <h2 class="content-header-title float-left primary ">PRODUKT & DIENSTLEISTUNG</h2>
                                        <button type="button" class="btn btn-primary" id="addRow">
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
                                                                <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"> <br>
                                                                Innendienst
                                                            </th>
                                                            <th>
                                                                <img src="{{ asset('images/icons/mitarbeiter.svg') }}" alt="" style="width: 62px;"> <br>
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
                        <button type="submit" class="btn btn-primary round mr-1 mb-1 waves-effect waves-light float-right"><i class="feather icon-arrow-right"></i> Nächste</button>
                    </div>     
                </div> 
            </form> 
        </div>
    </div>
</div> 
<!-- END: Content-->

@endsection

@push('scripts')

<!-- selecting Product with heart:start  -->
<script>
const STAGE           = @json($stage ?? 'lead');
const IMG_PRODUCT     = "{{ asset('images/articles/') }}";
const IMG_EMPLOYEE    = "{{ asset('images/employee/') }}";
const CSRF_TOKEN      = '{{ csrf_token() }}';
const ROUTE_EMPLOYEES = '{{ route("inquiry.department.employees") }}';

$(function () {
    console.log('Inquiry edit script init');

    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 is not loaded – include js/select2.min.js before this script.');
        return;
    }

    let rowIndex = 0;

    const SERVICES     = @json($services);
    const PRODUCTS     = @json($products);
    const DEPARTMENTS  = @json($departments);
    const PRODUCT_LIST = @json($product_list);

    // ============================================
    // Template functions (MUST be defined before use)
    // ============================================
    function inqFormatEmployee(opt) {
        if (!opt.id) return opt.text;

        const $el     = $(opt.element);
        const imgFile = $el.data('img');
        const img     = imgFile ? `${IMG_EMPLOYEE}/${imgFile}` : '';
        const pos     = $el.data('positions') || '';

        return `
            <div style="display:flex;align-items:center;">
                ${
                    img
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

    function inqFormatEmployeeSelection(opt) {
        return opt && opt.text ? opt.text : '';
    }

    // ============================================
    // Load existing rows (edit mode)
    // ============================================
    if (Array.isArray(PRODUCT_LIST) && PRODUCT_LIST.length) {
        console.log('Loading PRODUCT_LIST rows:', PRODUCT_LIST.length);
        PRODUCT_LIST.forEach(item => addRow(item));
    } else {
        console.log('No PRODUCT_LIST rows; table body stays empty until + button.');
    }

    // ============================================
    // Add new row
    // ============================================
    $(document).on('click', '#addRow', function (e) {
        e.preventDefault();
        console.log('addRow clicked');
        addRow(); // always allow new row
    });

    // ============================================
    // Add row (new or from existing product_list)
    // ============================================
    function addRow(item = {}) {
        rowIndex++;
        const idx = rowIndex;

        console.log('addRow build rowIndex', idx, 'item:', item);

        const html = `
        <tr data-index="${idx}">
            <td>
                <select class="form-select product-select" name="product_id[]" data-index="${idx}" style="width:100%">
                    <option value="">Produkt wählen</option>
                    ${PRODUCTS.map(p => `
                        <option value="${p.id}"
                                data-img="${p.image || ''}"
                                ${String(p.id) === String(item.product_id || '') ? 'selected' : ''}>
                            ${p.article_group}
                        </option>
                    `).join('')}
                </select>
            </td>

            <td>
                <select class="form-select service-select" name="service_id[]" data-index="${idx}" style="width:100%">
                    <option value="">Dienstleistung wählen</option>
                    ${
                        item.product_id
                            ? SERVICES
                                .filter(s => String(s.product_id) === String(item.product_id))
                                .map(s => `
                                    <option value="${s.id}"
                                            ${String(s.id) === String(item.service_id || '') ? 'selected' : ''}>
                                        ${translateService(s.phase_section)}
                                    </option>
                                `).join('')
                            : ''
                    }
                </select>
            </td>

            <td>
                <select class="form-select department-select" name="department_id[]" data-index="${idx}" style="width:100%">
                    <option value="">Abteilung wählen</option>
                    ${DEPARTMENTS.map(d => `
                        <option value="${d.id}"
                                ${String(d.id) === String(item.department_id || '') ? 'selected' : ''}>
                            ${d.department_name}
                        </option>
                    `).join('')}
                </select>
            </td>

            <td>
                <select class="form-select employee-select" name="employee_id[]" data-index="${idx}" style="width:100%">
                    ${
                        item.employee_id
                            ? `
                                <option value="${item.employee_id}"
                                        selected
                                        data-img="${item.eimage || ''}"
                                        data-positions="${item.positions || ''}">
                                    ${item.ename || ''} ${item.elastname || ''}
                                </option>
                              `
                            : '<option value="">Innendienst wählen</option>'
                    }
                </select>
            </td>

            <td>
                <select class="form-select field-employee-select" name="field_employee[]" data-index="${idx}" style="width:100%">
                    ${
                        item.field_employee
                            ? `
                                <option value="${item.field_employee}"
                                        selected
                                        data-img="${item.feimage || ''}"
                                        data-positions="${item.fepositions || ''}">
                                    ${item.fename || ''} ${item.felastname || ''}
                                </option>
                              `
                            : '<option value="">Außendienst wählen</option>'
                    }
                </select>
            </td>

            <td>
                <select class="form-select interest-select" name="interest[]" data-index="${idx}" style="width:100%">
                    <option value="intent"   ${item.interest === 'intent' ? 'selected' : ''}>Kaufabsicht</option>
                    <option value="interest" ${item.interest === 'interest' ? 'selected' : ''}>Kaufinteresse</option>
                    <option value="option"   ${item.interest === 'option' ? 'selected' : ''}>Kaufoption</option>
                </select>
            </td>

            <td>
                <select class="form-select realization-select" name="realization_time[]" data-index="${idx}" style="width:100%">
                    <option value="">Bitte auswählen</option>
                    <option value="soon"  ${item.realization_time === 'soon'  ? 'selected' : ''}>Schnellstmöglich</option>
                    <option value="3"     ${item.realization_time === '3'     ? 'selected' : ''}>3 Monate</option>
                    <option value="6"     ${item.realization_time === '6'     ? 'selected' : ''}>6 Monate</option>
                    <option value="other" ${item.realization_time === 'other' ? 'selected' : ''}>Sonstiges</option>
                </select>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="Entfernen">
                    <i class="feather icon-trash"></i>
                </button>
            </td>
        </tr>`;

        $('#inquiryProductTable tbody').append(html);
        initSelects(idx, item);
    }

    // ============================================
    // Init Select2 + bind change events
    // ============================================
    function initSelects(idx, item = {}) {
        console.log('initSelects for row', idx, item);

        const $product = $(`.product-select[data-index="${idx}"]`);
        const $service = $(`.service-select[data-index="${idx}"]`);
        const $dept    = $(`.department-select[data-index="${idx}"]`);
        const $emp     = $(`.employee-select[data-index="${idx}"]`);
        const $field   = $(`.field-employee-select[data-index="${idx}"]`);
        const $interest= $(`.interest-select[data-index="${idx}"]`);
        const $real    = $(`.realization-select[data-index="${idx}"]`);

        // basic select2
        [$product, $service, $dept, $interest, $real].forEach($s => {
            if ($s.length) {
                $s.select2({ width: '100%' });
            }
        });

        // employees with templates – init once
        [$emp, $field].forEach($s => {
            if ($s.length && !$s.data('select2')) {
                $s.select2({
                    width: '100%',
                    templateResult:    inqFormatEmployee,
                    templateSelection: inqFormatEmployeeSelection,
                    escapeMarkup:      m => m
                });
            }
        });

        // Product change: reload services + employees (with auto-suggest dept/service)
        $product.off('change.inquiry').on('change.inquiry', () => {
            console.log('product changed row', idx, '->', $product.val());
            loadServices(idx);
            loadEmployees(idx, { autofill: true });
        });

        // Manual service/department change: reload employees only
        $service.off('change.inquiry').on('change.inquiry', () => {
            console.log('service changed row', idx, '->', $service.val());
            loadEmployees(idx, { autofill: false });
        });
        $dept.off('change.inquiry').on('change.inquiry', () => {
            console.log('department changed row', idx, '->', $dept.val());
            loadEmployees(idx, { autofill: false });
        });

        // Preload employees in edit mode
        if (item && item.product_id) {
            loadEmployees(idx, {
                autofill: false,
                presetEmployeeId:      item.employee_id || null,
                presetFieldEmployeeId: item.field_employee || null
            });
        }
    }

    // ============================================
    // Load services for product
    // ============================================
    function loadServices(idx) {
        const pid   = $(`.product-select[data-index="${idx}"]`).val();
        const $srv  = $(`.service-select[data-index="${idx}"]`);

        console.log('loadServices row', idx, 'product_id', pid);

        $srv.empty().append('<option value="">Dienstleistung wählen</option>');

        if (!pid) {
            $srv.trigger('change');
            return;
        }

        const list = SERVICES.filter(s => String(s.product_id) === String(pid));
        list.forEach(s => {
            $srv.append(`
                <option value="${s.id}">
                    ${translateService(s.phase_section)}
                </option>
            `);
        });

        if (list.length === 1) {
            $srv.val(list[0].id).trigger('change');
        } else {
            $srv.trigger('change');
        }
    }

    // ============================================
    // Load employees (Innendienst + Außendienst)
    // ============================================
    function loadEmployees(idx, options = {}) {
        const autofill = options.autofill === true;

        const $product = $(`.product-select[data-index="${idx}"]`);
        const $dept    = $(`.department-select[data-index="${idx}"]`);
        const $service = $(`.service-select[data-index="${idx}"]`);
        const $emp     = $(`.employee-select[data-index="${idx}"]`);
        const $field   = $(`.field-employee-select[data-index="${idx}"]`);

        const pid = $product.val();
        let   did = $dept.val();
        let   sid = $service.val();

        if (!pid) {
            clearEmployees($emp, $field);
            return;
        }

        const prevEmpId   = options.presetEmployeeId      || $emp.val();
        const prevFieldId = options.presetFieldEmployeeId || $field.val();

        console.log('loadEmployees edit row', idx, {
            product_id: pid,
            department_id: did,
            service_id: sid,
            stage: STAGE
        });

        $.post(ROUTE_EMPLOYEES, {
            _token:        CSRF_TOKEN,
            product_id:    pid,
            department_id: did || null,
            service_id:    sid || null,
            stage:         STAGE
        })
        .done(res => {
            console.log('employees response edit row', idx, res);

            let internalEmployees = [];
            let externalEmployees = [];

            if (Array.isArray(res)) {
                internalEmployees = res;
                externalEmployees = res;
            } else {
                if (autofill && !did && res.department_id) {
                    did = res.department_id;
                    $dept.val(did).trigger('change.select2');
                }
                if (autofill && !sid && res.service_id) {
                    sid = res.service_id;
                    if (!$service.find(`option[value="${sid}"]`).length) {
                        loadServices(idx);
                    }
                    if ($service.find(`option[value="${sid}"]`).length) {
                        $service.val(sid).trigger('change.select2');
                    }
                }

                internalEmployees = res.internal_employees || [];
                externalEmployees = res.external_employees || [];
            }

            updateEmployeeSelect($emp,   internalEmployees, 'Innendienst wählen', prevEmpId);
            updateEmployeeSelect($field, externalEmployees, 'Außendienst wählen', prevFieldId);

            if (!internalEmployees.length && !externalEmployees.length) {
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
            console.error('loadEmployees error edit row', idx, xhr);
            clearEmployees($emp, $field);
            Swal.fire('Fehler', 'Mitarbeiter konnten nicht geladen werden.', 'error');
        });
    }

    function updateEmployeeSelect($select, employees, placeholder, prevId) {
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

        if (prevId && $select.find(`option[value="${prevId}"]`).length) {
            $select.val(prevId).trigger('change.select2');
        } else {
            $select.val('').trigger('change.select2');
        }
    }

    function clearEmployees($emp, $field) {
        updateEmployeeSelect($emp,   [], 'Innendienst wählen', null);
        updateEmployeeSelect($field, [], 'Außendienst wählen', null);
    }

    // ============================================
    // Service translation helper
    // ============================================
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

    // ============================================
    // Remove row
    // ============================================
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').fadeOut(150, function () {
            $(this).remove();
        });
    });
});
</script>

    
  
 <!-- html2canvas for screenshot -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

<script>
"use strict";

let autocomplete, elevationService, streetView, map;
let placeSelected = false;

window.initAutocomplete = function () {
    const mapContainer = document.getElementById("gmp-map");
    if (!mapContainer) return;

    map = new google.maps.Map(mapContainer, {
        center: { lat: 50.1109, lng: 8.6821 },
        zoom: 18,
        mapTypeId: google.maps.MapTypeId.SATELLITE, // 👈 Satellite View by default

        streetViewControl: true,
    });

    streetView = map.getStreetView();
    elevationService = new google.maps.ElevationService();

    // Center map to user location
   // ✅ Try to center to user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userPos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(userPos);
                    map.setZoom(17); // optional: zoom in
                    // Optionally add a marker
                    new google.maps.Marker({
                        position: userPos,
                        map: map,
                        title: "Ihr Standort"
                    });
                },
                (error) => {
                    console.warn("Geolocation error:", error.message);
                    showWarning("Standort konnte nicht abgerufen werden. Bitte Standortfreigabe prüfen.");
                }
            );
        } else {
            showWarning("Geolocation wird von diesem Browser nicht unterstützt.");
        }


    const addrInput = document.getElementById('full_address');
    if (!addrInput) return;

    autocomplete = new google.maps.places.Autocomplete(addrInput, {
        fields: ['address_components', 'geometry'],
        types: ['geocode']
    });

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
            addrInput.value = '';
            return;
        }

        placeSelected = true;

        const location = place.geometry.location;
        const lat = location.lat();
        const lng = location.lng();

        const components = extractAddressComponents(place.address_components);
        const street = `${components.route} ${components.street_number}`.trim();
        const city = components.locality;
        const postcode = components.postal_code;

        setValueIfExists('street-input', street);
        setValueIfExists('locality-input', city);
        setValueIfExists('postal_code-input', postcode);
        setValueIfExists('latitude-input', lat);
        setValueIfExists('longitude-input', lng);

        getElevation(lat, lng);
        checkCustomer(street, postcode, lat, lng);
    });

    addrInput.addEventListener('blur', () => {
        if (!placeSelected) {
            showWarning('Bitte wählen Sie eine Adresse aus der Liste aus!');
            addrInput.value = '';
        }
    });
};

function extractAddressComponents(components) {
    let data = { street_number: '', route: '', locality: '', postal_code: '' };
    components.forEach(c => {
        const type = c.types[0];
        if (data.hasOwnProperty(type)) {
            data[type] = c.long_name;
        }
    });
    return data;
}

function setValueIfExists(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

function getElevation(lat, lng) {
    elevationService.getElevationForLocations({ locations: [{ lat, lng }] }, (results, status) => {
        if (status === "OK" && results[0]) {
            setValueIfExists("elevation-input", results[0].elevation.toFixed(2));
        }
    });
}

function checkCustomer(street, postcode, lat, lng) {
    const name = document.getElementById('input_name')?.value || 'Unbekannt';
    const lastname = document.getElementById('input_lastname')?.value || 'Unbekannt';

    const url = `/check-new-leads/${encodeURIComponent(name)}/${encodeURIComponent(lastname)}/${encodeURIComponent(street)}/${encodeURIComponent(postcode)}/${lat}/${lng}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'duplicate' || data.status === 'neighbor') {
                let tableHTML = `<table class="table table-bordered mt-3">
                    <thead><tr><th>Name</th><th>Nachname</th><th>Adresse</th><th>Radius (km)</th><th>Aktion</th></tr></thead><tbody>`;

                if (data.status === 'duplicate') {
                    const c = data.customer;
                    tableHTML += `<tr><td>${c.name}</td><td>${c.lastname}</td><td>${c.full_address}</td><td>-</td>
                    <td><a href="/new_lead_profile/${c.id}" class="btn btn-primary">Profil anzeigen</a></td></tr>`;
                }

                if (data.status === 'neighbor') {
                    data.customers.forEach(c => {
                        tableHTML += `<tr><td>${c.name}</td><td>${c.lastname}</td><td>${c.full_address}</td>
                        <td>${c.distance.toFixed(2)}</td><td><a href="/new_lead_profile/${c.id}" class="btn btn-primary">Profil anzeigen</a></td></tr>`;
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
        .catch(e => console.error('Überprüfung fehlgeschlagen:', e));
}

function showWarning(msg) {
    Swal.fire({ icon: 'warning', title: 'Fehler!', text: msg });
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
</script>

<!-- ✅ Google Maps API loaded last -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initAutocomplete" async defer></script>



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
 

 


@endpush