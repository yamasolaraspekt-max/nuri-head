@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@section('style')
<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/file-uploaders/dropzone.css') }}">
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
                        <h2 class="content-header-title float-left mb-0">KUNDEN ANLEGEN</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Kunden</a>
                                </li>
                                <li class="breadcrumb-item"><a href="#">Neu</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-6 col-12">
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form form-horizontal" method="post"
                                        action="{{ action('App\Http\Controllers\CustomerController@store')}}"
                                        class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-body">
                                            <div class="row">

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-6"> 
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type1"
                                                                                checked value="privat" >
                                                                            <label class="custom-control-label" for="customer_type1">privat</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type2"
                                                                                value="Gewerbe">
                                                                            <label class="custom-control-label" for="customer_type2">Gewerbe</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type"
                                                                                id="customer_type3" value="Kummune">
                                                                            <label class="custom-control-label" for="customer_type3">Kummune</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Title</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                                <select class="form-control" id="basicSelect"
                                                                    value="{{old('title')}}" name="title">
                                                                    <option value="Frau">Frau</option>
                                                                    <option value="Herr">Herr</option>
                                                                    <option value="Dr.">Dr.</option>
                                                                    <option value="Pro.">Pro.</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Firma</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" id="first-name" class="form-control"
                                                                value="{{old('firma')}}" name="firma">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Vorname</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"
                                                                value="{{old('lastname')}}" name="lastname">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Name</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"
                                                                @if(session('customer'))
                                                                value="{{ session('customer')}}" @else
                                                                value="{{old('name')}}" @endif name="name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Straße / Nr.</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input id="location-input" type="text" class="form-control"
                                                                placeholder="Enter location" name="street" value= {{ old('street') }} >
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude') }}"> 
                                                            <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude') }}">
                                                            <input type="hidden" id="polygon-height"
                                                                name="polygon_height" value="{{ old('polygon-height') }}">
                                                            <input type="hidden" id="polygon-width" name="polygon_width" value="{{ old('polygon_width') }}">
                                                            <input type="hidden" id="polygon-area" name="polygon_area" value="{{ old('polygon_area') }}">
                                                            <input type="hidden" id="elevation-input"
                                                                placeholder="Elevation in meters" name="elevation" value="{{ old('elevation') }}">
                                                            <input type="text" class="form-control"
                                                                value="{{old('postcode')}}" name="postcode"
                                                                id="postal_code-input">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control"
                                                                value="{{old('city')}}" name="city" id="locality-input">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Festnet/Mobile</span>
                                                        </div>
                                                      
                                                        <div class="col-md-4">
                                                            <input type="text" id="contact-info" class="form-control"
                                                                value="{{old('telephone')}}" name="telephone" placeholder="Festnet">
                                                        </div>
                                                          <div class="col-md-4">
                                                            <input type="text" id="contact-info" class="form-control"
                                                                value="{{old('phone')}}" name="phone" placeholder="Mobile">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>E-Mail</span>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="email" id="contact-info" class="form-control" @if(session('customer_email'))
                                                                value="{{ session('customer_email')}}" @else value="{{old('email')}}" @endif name="email">
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12 col-12">
                                                    <div class="form-body">
                                                        <div class="row"> 
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-12">
                                                                        <h3>WEITERE INFORMATIONEN</h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-6">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Quelle</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <select name="source" id="source" class="form-control">
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
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Anfrage-Datum</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="date" class="form-control" name="request_date" value="{{ \Carbon\Carbon::parse(now())->isoFormat('DD.MM.YYYY')}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                
                                                
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <span>Info</span>
                                                                    </div>
                                                                    <div class="col-md-10">
                                                                        <input type="text" class="form-control" name="info" value="{{ old('info') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-6">
                                                                        <span>Kunde aufgefordert Unterlagen zu schicken</span>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="document"
                                                                                            id="customRadio1">
                                                                                        <label class="custom-control-label" for="customRadio1">Ja</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-2">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="document"
                                                                                            id="customRadio2" checked>
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
                                                            ->where('users.id', '=', auth()->user()->name)
                                                            ->first()
                                                            @endphp
                                                
                                                               @php
                                                            $employee = DB::table('employees') 
                                                            ->select('employees.id','employees.name', 'employees.lastname') 
                                                            ->get()
                                                            @endphp
                                                            <div class="col-6">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Kontaktperson</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        @if($user_name)
                                                                        <input type="hidden" name="contact_person" class="form-control"
                                                                            name="{{ auth()->user()->name }}" value="{{ auth()->user()->name}}">
                                                                        <input type="text" class="form-control" name="{{ auth()->user()->name }}"
                                                                            value="{{ $user_name->name }} {{ $user_name->lastname }}">
                                                                        @else
                                                                            <select name="contact_person" id="contact_person" class="form-control">
                                                                                @foreach ($employee as $emp)
                                                                                <option value="{{ $emp->id }}"> {{ $emp->name }} {{ $emp->lastname }} </option>

                                                                                @endforeach
                                                                            </select>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-6">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Datum</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="date" class="form-control" name="date" value="{{ old('date') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-6">
                                                                        <span>Erstberatung hat stattgefunden</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="consultation"
                                                                                            id="consultation_yes" checked value="Ja">
                                                                                        <label class="custom-control-label" for="consultation_yes">Ja</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="consultation"
                                                                                            id="consultation_no" value="Nein" checked>
                                                                                        <label class="custom-control-label" for="consultation_no">Nein</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="consultation"
                                                                                            id="consultation_persönlich" value="persönlich">
                                                                                        <label class="custom-control-label"
                                                                                            for="consultation_persönlich">persönlich</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="consultation"
                                                                                            id="consultation_telefonisch" value="telefonisch">
                                                                                        <label class="custom-control-label"
                                                                                            for="consultation_telefonisch">telefonisch</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="consultation"
                                                                                            id="consultation_Video" value="Video">
                                                                                        <label class="custom-control-label" for="consultation_Video">Video</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-12" style="display: flex; flex-wrap: wrap;">
                                                                <div class="col-md-4"> 
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="mr-2">Interesse</span>
                                                                        <div class="star-rating" data-category="interest" data-rating="0">
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                        </div>
                                                                        <input type="hidden" name="interest_rating" value="0">
                                                                    </div> 
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="mr-2">Ernsthaftigkeit</span>
                                                                        <div class="star-rating" data-category="seriousness" data-rating="0" >
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                            <span class="star"><i class="fa fa-star"></i></span>
                                                                        </div>
                                                                        <input type="hidden" name="seriousness_rating" value="0">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="mr-2">Preisinformation</span>
                                                                        <div class="star-rating" data-category="price_information" data-rating="0" name="price_information">
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
                                                
                                                            <div class="col-6">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Notizen</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <textarea name="note" class="form-control" id="" cols="30" rows="5"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                
                                                            <div class="col-6">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Priorisierung</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <select name="periority" id="" class="form-control">
                                                                            <option value="Normal">Normal</option>
                                                                            <option value="Urgent">Urgent</option>
                                                                            <option value="Very Urgent">Very Urgent</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                
                                                                <div class="form-group row">
                                                                    <div class="col-md-6">
                                                                        <span>Erstberatung hat stattgefunden</span>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="initial_consultation"
                                                                                            id="consultation_ort" value="vor Ort">
                                                                                        <label class="custom-control-label" for="consultation_ort">vor Ort</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="initial_consultation"
                                                                                            id="consultation_tele" value="telefonisch">
                                                                                        <label class="custom-control-label" for="consultation_tele">telefonisch</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="initial_consultation"
                                                                                            id="consultation_Vid" value="Video">
                                                                                        <label class="custom-control-label" for="consultation_Vid">Video</label>
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
                                            <button type="submit" class="btn btn-primary">Nächste</button>
                                        </div> 
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> 
                    
                    <div class="col-md-6 col-12">
                        {{-- Map Start --}} 
                        <div class="card" >
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="col-md-6 float-left">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" value="false" id="multiple-roof-checkbox">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">Multiple Roofs</span>
                                            </div>
                                        </fieldset> 
                                    </div>
                                    <div class="col-md-6 float-right" style="text-align: right;">
                                        <button class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn"><i class="feather icon-camera"></i>
                                            Screenshot</button>
                                    </div>
                                    <div class="map" id="gmp-map" style="width: 100%;position: relative;overflow: hidden; height: 772px;"></div>
                                </div> 
                              
                            </div>
                        </div>
                        {{-- Map End --}} 
                    </div> 
                 

            </section>
            <!-- // Basic Horizontal form layout section end -->
        </div>
    </div>
</div>
</div>
<!-- END: Content-->

@endsection

@section('script')

{{-- Star slider Script --}}
  
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
<script>
    "use strict";

        let map; 
        let panorama;

        function initMap() {
            const CONFIGURATION = {
                mapOptions: {
                    center: {lat: 37.4221, lng: -122.0841},
                    fullscreenControl: true,
                    mapTypeControl: true,
                    streetViewControl: true,
                    zoom: 15,
                    zoomControl: true,
                    maxZoom: 22,
                    mapId: "DEMO_MAP_ID"
                }
            };

            map = new google.maps.Map(document.getElementById('gmp-map'), CONFIGURATION.mapOptions);
            panorama = map.getStreetView();

            const marker = new google.maps.Marker({map: map});
            const autocomplete = new google.maps.places.Autocomplete(document.getElementById('location-input'), {
                fields: ['address_components', 'geometry', 'name'],
                types: ['address']
            });
            const elevationService = new google.maps.ElevationService();

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }
                renderAddress(place, map, marker);
                fillInAddress(place);
                getElevation(place.geometry.location, elevationService);
            });

            initAreaMeasurement();

            document.getElementById('screenshot-btn').addEventListener('click', takeScreenshot);
        }

        function fillInAddress(place) {
            const addressMappings = [
                { key: 'street', types: ['street_number', 'route'] },
                { key: 'locality', types: ['locality'] },
                { key: 'region', types: ['administrative_area_level_1'] },
                { key: 'postal_code', types: ['postal_code'] },
                { key: 'country', types: ['country'] }
            ];

            addressMappings.forEach(mapping => {
                const element = document.getElementById(`${mapping.key}-input`);
                const component = place.address_components.find(c => mapping.types.some(type => c.types.includes(type)));
                if (element && component) {
                    element.value = component.long_name;
                }
            });
        }

        function renderAddress(place, map, marker) {
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
            document.getElementById('latitude-input').value = place.geometry.location.lat();
            document.getElementById('longitude-input').value = place.geometry.location.lng();
        }

        function getElevation(location, elevationService) {
            elevationService.getElevationForLocations({
                'locations': [location]
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        const elevation = results[0].elevation;
                        document.getElementById('elevation-input').value = elevation.toFixed(2);
                    } else {
                        console.log('No results found');
                    }
                } else {
                    console.log('Elevation service failed due to: ' + status);
                }
            });
        }

        function createMapLabel(map, text, position) {
            const label = new google.maps.InfoWindow({
                content: `<div style="color: black; font-size: 12px;">${text}</div>`,
                position: position,
                pixelOffset: new google.maps.Size(0, -20)
            });
            label.open(map);
        }

        function updateMeasurements(polygon) {
            const path = polygon.getPath();
            const area = google.maps.geometry.spherical.computeArea(path);
            const bounds = new google.maps.LatLngBounds();
            path.forEach(vertex => bounds.extend(vertex));

            const ne = bounds.getNorthEast();
            const sw = bounds.getSouthWest();
            const nw = new google.maps.LatLng(ne.lat(), sw.lng());

            const height = google.maps.geometry.spherical.computeDistanceBetween(nw, sw);
            const width = google.maps.geometry.spherical.computeDistanceBetween(ne, nw);

            document.getElementById('polygon-height').value = height.toFixed(2);
            document.getElementById('polygon-width').value = width.toFixed(2);
            document.getElementById('polygon-area').value = area.toFixed(2);

            createMapLabel(map, `Height: ${height.toFixed(2)}m`, sw);
            createMapLabel(map, `Width: ${width.toFixed(2)}m`, nw);
        }

        function initAreaMeasurement() {
            const drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    fillColor: 'red',
                    fillOpacity: 0.5,
                    strokeWeight: 3.5,
                    clickable: true,
                    editable: true,
                    draggable: true,
                    zIndex: 1,
                    geodesic: true
                }
            });

            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'overlaycomplete', (event) => {
                const polygon = event.overlay;
                updateMeasurements(polygon);

                polygon.getPath().addListener('set_at', () => updateMeasurements(polygon));
                polygon.getPath().addListener('insert_at', () => updateMeasurements(polygon));
                polygon.getPath().addListener('remove_at', () => updateMeasurements(polygon));
            });
        }

        function saveMeasurements(customerId, label, width, height, area) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const data = { customer_id: customerId, measure_label: label, width: width, height: height, area: area };
            
            fetch('/customer_measure', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function takeScreenshot() {
            const mapContainer = document.getElementById('gmp-map');
            html2canvas(mapContainer).then(canvas => {
                const dataURL = canvas.toDataURL('image/png');
                saveScreenshot(dataURL);
            });
        }

        function saveScreenshot(dataURL) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/save_screenshot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ image: dataURL })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Screenshot saved successfully:', data);
            })
            .catch((error) => {
                console.error('Error saving screenshot:', error);
            });
        }

        document.addEventListener('DOMContentLoaded', initMap);
</script>

<script>
    function displayMapScreenshot() {
        html2canvas(document.getElementById('gmp-map')).then(function(canvas) {
            // Convert canvas to base64 image
            const imgData = canvas.toDataURL('image/png');

            // Create an image element
            const img = document.createElement('img');
            img.src = imgData;

            // Append the image to a container
            document.getElementById('map-screenshot-container').appendChild(img);
        });
    }

    function displayStreetViewScreenshot() {
        html2canvas(document.getElementById('street-view')).then(function(canvas) {
            // Convert canvas to base64 image
            const imgData = canvas.toDataURL('image/png');

            // Create an image element
            const img = document.createElement('img');
            img.src = imgData;

            // Append the image to a container
            document.getElementById('street-view-screenshot-container').appendChild(img);
        });
    }
</script>
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
    $(document).ready(function() {
            $('#product').select2();
        });

  

   $(document).ready(function(){
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







@endsection