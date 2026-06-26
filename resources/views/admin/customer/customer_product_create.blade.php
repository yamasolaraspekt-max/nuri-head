@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@section('style')
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script> 
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
  
<style>
    .section_title {
      border-left: 8px solid #94c11f;
    color: #94c11f;
    padding: 6px;
    }
</style>
@endsection

@section('content')
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
                    <div class="col-md-4 col-12">
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
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body"> 
                                        <div class="row">  
                                            <div class="col-12">
                                                <div class="form-group row"> 
                                                    <div class="col-md-4">
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
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Title</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="text" id="first-name" class="form-control textbox"
                                                            value="{{ $customer->title }}" name="firma" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Firma</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="text" id="first-name" class="form-control textbox"
                                                            value="{{ $customer->firma }}" name="firma" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Name</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="text" class="form-control textbox"
                                                            value="{{ $customer->name }} {{ $customer->lastname }}"
                                                            name="lastname" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Straße / Nr.</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty ">
                                                        <input type="text" class="form-control textbox" name="street"
                                                            value="{{
                                                            $customer->street }}" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>PLZ / Ort</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="text" class="form-control textbox"
                                                            value="{{ $customer->postcode }} {{ $customer->city }}"
                                                            name="postcode" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Tel</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="text" id="contact-info"
                                                            class="form-control textbox" value="{{ $customer->phone }}"
                                                            name="phone" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>E-Mail</span>
                                                    </div>
                                                    <div class="col-md-10 textbox-container empty">
                                                        <input type="email" id="contact-info"
                                                            class="form-control textbox" name="email"
                                                            value="{{ $customer->email }}" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-2 float-left">
                                             <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#edit">
                                                Bearbeiten  
                                            </button>

                                            <div class="modal fade text-left" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel17">Kundenbearbeitung</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('customer.info.update') }}" method="post">
                                                        @csrf
                                                            <div class="modal-body">
                                                                <div class="row">  
                                                                    <div class="col-12">
                                                                        <div class="form-group row"> 
                                                                            <div class="col-md-4">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="customer_type" id="customer_type1"
                                                                                                    @if($customer->customer_type=="privat") checked   @endif
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
                                                                                                checked  @endif >
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
                                                                                                checked  @endif >
                                                                                                <label class="custom-control-label"
                                                                                                    for="customer_type3">Kummune</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li> 
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Title</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="hidden" name="id" value="{{$customer->id}}">
                                                                                <input type="text" id="title" class="form-control textbox"
                                                                                    value="{{ $customer->title }}" name="title"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Firma</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" id="first-name" class="form-control textbox"
                                                                                    value="{{ $customer->firma }}" name="firma"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Name</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" class="form-control textbox"
                                                                                    value="{{ $customer->name }} "
                                                                                    name="name"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                       <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Nachname</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" class="form-control textbox"
                                                                                    value=" {{ $customer->lastname }}"
                                                                                    name="lastname"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Straße / Nr.</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty ">
                                                                                <input type="text" class="form-control textbox" name="street"
                                                                                    value="{{  $customer->street }}"  >
                                                                                <div class="indicator"></div>
                                                                                <input type="hidden" id="latitude-input" name="latitude"
                                                                                value="{{ $alternative ? ($alternative->main == 0 ? $customer->lat : $alternative->lat) : $customer->lat }}">
                                                                            <input type="hidden" id="longitude-input" name="longitude"
                                                                                value="{{ $alternative ? ($alternative->main == 0 ? $customer->lon : $alternative->lon) : $customer->lon }}">

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>PLZ </span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" class="form-control textbox"
                                                                                    value="{{ $customer->postcode }} "
                                                                                    name="postcode"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                      <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Ort</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" class="form-control textbox"
                                                                                    value="{{ $customer->city }}"
                                                                                    name="city"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Tel</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="text" id="contact-info"
                                                                                    class="form-control textbox" value="{{ $customer->phone }}"
                                                                                    name="phone"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>E-Mail</span>
                                                                            </div>
                                                                            <div class="col-md-10 textbox-container empty">
                                                                                <input type="email" id="contact-info"
                                                                                    class="form-control textbox" name="email"
                                                                                    value="{{ $customer->email }}"  >
                                                                                <div class="indicator"></div>

                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 float-right">
                                            <label for="alternative">Bauvorhaben</label>
                                            <button type="button" name="alternative" id="add_alternative"
                                                onclick="toggleAlternativeAddress()"
                                                class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light">
                                                <i class="feather icon-plus"></i></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="col-md-8 col-12">
                        <div class="card-body"> 
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" style="font-size:23px" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">WEITERE INFORMATIONEN |</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link "style="font-size:23px" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">ENERGIEVERBRAUCH UND OBJEKTDATEN |</a>
                                </li>  

                                 <li class="nav-item">
                                    <a class="nav-link " style="font-size:23px"  id="gallary-tab" data-toggle="tab" href="#gallary" aria-controls="gallary" role="tab" aria-selected="false">GALERIE & DOKUMENTE</a>
                                </li> 
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                                    <div class="form-body">
                                        <div class="row">  
                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Quelle</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" value="{{ $customer->source }}"
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Anfrage-Datum</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="date" class="form-control" name="request_date"
                                                            value="{{ $customer->request_date }}" disabled>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <span>Info</span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" name="info"
                                                            value="{{ $customer->source_info }}" disabled>
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
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="document" id="customRadio1"
                                                                            @if($customer->document=="on") checked enabled @else
                                                                        disabled @endif>
                                                                        <label class="custom-control-label"
                                                                            for="customRadio1">Ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="document" id="customRadio2"
                                                                            @if($customer->document!="on") checked enabled @else
                                                                        disabled @endif>
                                                                        <label class="custom-control-label"
                                                                            for="customRadio2">Nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                            $user_name = DB::table('employees') 
                                                ->select('employees.name', 'employees.lastname')
                                                ->where('employees.id', '=', $customer->contact_person)
                                                ->first()
                                                @endphp

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Kontaktperson</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        @if($user_name)
                                                        <input type="text" class="form-control" name="contact_person"
                                                            value="{{ $user_name->name }} {{ $user_name->lastname }}" disabled>
                                                        @else
                                                        <input type="text" class="form-control"
                                                            name="" disabled>
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
                                                        <input type="date" class="form-control" name="date"
                                                            value="{{ $customer->date }}" disabled>
                                                    </div>
                                                </div>
                                            </div>
 
                                            
                                            <div class="col-12" style="display: flex; flex-wrap: wrap;">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-2">Interesse</span>
                                                            <div class="star-rating">
                                                                @for($i = 0; $i < $customer->interest_rating; $i++)
                                                                    <span class="fa fa-star primary"></span>
                                                                    @endfor
                                                                    @for($i = 0; $i < 5 - $customer->interest_rating; $i++)
                                                                        <span class="fa fa-star light"></span>
                                                                        @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-2">Ernsthaftigkeit</span>
                                                            <div class="star-rating">
                                                                @for($i = 0; $i < $customer->seriousness_rating; $i++)
                                                                    <span class="fa fa-star primary"></span>
                                                                    @endfor
                                                                    @for($i = 0; $i < 5 - $customer->seriousness_rating; $i++)
                                                                        <span class="fa fa-star light"></span>
                                                                        @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-2">Preisinformation</span>
                                                            <div class="star-rating">
                                                                @for($i = 0; $i < $customer->price_information_rating; $i++)
                                                                    <span class="fa fa-star primary"></span>
                                                                    @endfor
                                                                    @for($i = 0; $i < 5 - $customer->price_information_rating; $i++)
                                                                        <span class="fa fa-star light"></span>
                                                                        @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Notizen</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <textarea name="" class="form-control" id="" cols="30" rows="5"
                                                            disabled>{{ $customer->note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Priorisierung</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" disabled value="{{ $customer->periority }}"
                                                            class="form-control">
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
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="initial_consultation" id="consultation_ort"
                                                                            value="vor Ort"
                                                                            @if($customer->initial_consultation=="vor Ort") checked
                                                                        enabled @else disabled @endif>
                                                                        <label class="custom-control-label"
                                                                            for="consultation_ort">vor Ort</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="initial_consultation" id="consultation_tele"
                                                                            value="telefonisch"
                                                                            @if($customer->initial_consultation=="telefonisch")
                                                                        checked enabled @else disabled @endif>
                                                                        <label class="custom-control-label"
                                                                            for="consultation_tele">telefonisch</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input"
                                                                            name="initial_consultation" id="consultation_Vid"
                                                                            value="Video"
                                                                            @if($customer->initial_consultation=="Video") checked
                                                                        enabled @else disabled @endif>
                                                                        <label class="custom-control-label"
                                                                            for="consultation_Vid">Video</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div>
                                        <div class="col-md-2 float-left">
                                                <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#editinfo">
                                                    Bearbeiten  
                                                </button>

                                            <div class="modal fade text-left" id="editinfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel17">WEITERE INFORMATIONNEN</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('customer.info.energy') }}" method="post">
                                                        @csrf
                                                            <div class="modal-body">
                                                              <div class="row">  
                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Quelle</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="hidden" name="id" value="{{ $customer->id}}">
                                                                                <input type="text" class="form-control" name="source" value="{{ $customer->source }}" >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Anfrage-Datum</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="date" class="form-control" name="request_date"
                                                                                    value="{{ $customer->request_date }}"  >
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-1">
                                                                                <span>Info</span>
                                                                            </div>
                                                                            <div class="col-md-11">
                                                                                <input type="text" class="form-control" name="source_info"
                                                                                    value="{{ $customer->source_info }}"  >
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-4">
                                                                                <span>Kunde aufgefordert Unterlagen zu schicken</span>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="document" id="customer_document_ja"
                                                                                                    @if($customer->document=="on") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="customer_document_ja">Ja</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-2">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="document" id="customer_document_no"
                                                                                                    @if($customer->document!="on") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="customer_document_no">Nein</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                  

                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Datum</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <input type="date" class="form-control" name="date"
                                                                                    value="{{ $customer->date }}"  >
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Erstberatung hat stattgefunden</span>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="consultation" id="consultation_yes_edit"  
                                                                                                    value="Ja" @if($customer->consultation=="Ja") checked
                                                                                                enabled   @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_yes_edit">Ja</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="consultation" id="consultation_no_edit" value="Nein"
                                                                                                    @if($customer->consultation=="Nein") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_no_edit">Nein</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="consultation" id="consultation_persönlich_edit"
                                                                                                    value="persönlich"
                                                                                                    @if($customer->consultation=="persönlich") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_persönlich_edit">persönlich</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="consultation" id="consultation_telefonisch"
                                                                                                    value="telefonisch"
                                                                                                    @if($customer->consultation=="telefonisch") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_telefonisch">telefonisch</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="consultation" id="consultation_Video_edit"
                                                                                                    value="Video" @if($customer->consultation=="Video")
                                                                                                checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_Video_edit">Video</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-6" >
                                                                           <div class="col-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <span>Interesse</span>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                       <select name="interest_rating" id="" class="form-control"  style="width: 100px;">
                                                                                            <option value="1" @if($customer->interest_rating==1) selected @endif>  1  </option>
                                                                                            <option value="2" @if($customer->interest_rating==2) selected @endif>  2  </option>
                                                                                            <option value="3" @if($customer->interest_rating==3) selected @endif>  3  </option>
                                                                                            <option value="4" @if($customer->interest_rating==4) selected @endif>  4  </option>
                                                                                            <option value="5" @if($customer->interest_rating==5) selected @endif>  5  </option>
                                                                                        </select> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <span>Ernsthaftigkeit</span>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                       <select name="seriousness_rating" id="" class="form-control"  style="width: 100px;">
                                                                                            <option value="1" @if($customer->seriousness_rating==1) selected @endif>  1  </option>
                                                                                            <option value="2" @if($customer->seriousness_rating==2) selected @endif>  2  </option>
                                                                                            <option value="3" @if($customer->seriousness_rating==3) selected @endif>  3  </option>
                                                                                            <option value="4" @if($customer->seriousness_rating==4) selected @endif>  4  </option>
                                                                                            <option value="5" @if($customer->seriousness_rating==5) selected @endif>  5  </option>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                              <div class="col-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <span>Preisinformation</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                       <select name="price_information_rating" id="" class="form-control"  style="width: 100px;">
                                                                                            <option value="1" @if($customer->price_information_rating==1) selected @endif>  1  </option>
                                                                                            <option value="2" @if($customer->price_information_rating==2) selected @endif>  2  </option>
                                                                                            <option value="3" @if($customer->price_information_rating==3) selected @endif>  3  </option>
                                                                                            <option value="4" @if($customer->price_information_rating==4) selected @endif>  4  </option>
                                                                                            <option value="5" @if($customer->price_information_rating==5) selected @endif>  5  </option>
                                                                                        </select> 
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        
                                                                    </div>

                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <span>Notizen</span>
                                                                            </div>
                                                                            <div class="col-md-10">
                                                                                <textarea name="note" class="form-control" id="" cols="30" rows="5" >{{ $customer->note }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-6">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-4">
                                                                                <span>Priorisierung</span>
                                                                            </div>
                                                                            <div class="col-md-8">  
                                                                                <select name="periority" id="" class="form-control form-element"> 
                                                                                    <Option value="Normal" @if($customer->periority=="Normal") selected @endif>Normal</Option>
                                                                                    <Option value="Dringend"  @if($customer->periority=="Dringend") selected @endif>Dringend</Option>
                                                                                    <Option value="Sehr dringend"  @if($customer->periority=="Sehr dringend") selected @endif>Sehr dringend</Option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row">
                                                                            <div class="col-md-12">
                                                                                <span>Erstberatung hat stattgefunden</span>
                                                                            </div>
                                                                            <div class="col-md-12 mt-1">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="initial_consultation" id="consultation_ort_edit"
                                                                                                    value="vor Ort"
                                                                                                    @if($customer->initial_consultation=="vor Ort") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_ort_edit">vor Ort</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="initial_consultation" id="consultation_tele_edit"
                                                                                                    value="telefonisch"
                                                                                                    @if($customer->initial_consultation=="telefonisch")
                                                                                                checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_tele_edit">telefonisch</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input"
                                                                                                    name="initial_consultation" id="consultation_Vid_edit"
                                                                                                    value="Video"
                                                                                                    @if($customer->initial_consultation=="Video") checked  @endif>
                                                                                                <label class="custom-control-label"
                                                                                                    for="consultation_Vid_edit">Video</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                     <div class="card-body"> 
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <!-- Objektdaten Section -->
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
                                                                <label>{{ $customer->objective ?? 'Bitte wählen' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Baujahr Ihres Hauses?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->house_year ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieviel Wohneinheit hat das Objekt?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->number_we ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieviel Geschoß hat das Objekt?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->number_stories ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie groß ist die Beheizte Wohnfläche?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->living_space ?? 'N/A' }} m²</label>
                                                            </div>  
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie groß ist die Nutzfläche?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->unusable_space ?? 'N/A' }} m²</label>
                                                            </div>  
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieviel Personen wohnen in diesem Objekt?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->number_people ?? 'N/A' }}</label>
                                                            </div>  
                                                        </div>
                                                    </div>

                                                    <!-- Dach-Information Section -->
                                                    <div class="col-12">
                                                        <h2 class="primary"><strong>DACH-INFORMATION</strong></h2>
                                                        <hr>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welche Art vom Dach haben Sie?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->roof_type ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie alt ist Ihr Dach?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->roof_age ?? 'N/A' }} Jahr</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welche Dacheindeckung hat das Dach?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->tile_name ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                     <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welche dachneigung hat ihr Dach?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->roof_pitch ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                     <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welche himmelsausrechtung hat ihr Dach?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                              <select name="roof_direction" id="" class="form-control" disabled> 
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
                                                    <!-- Heizungs-Information Section -->
                                                    <div class="col-12">
                                                        <h2 class="primary"><strong>HEIZUNGS-INFORMATION</strong></h2>
                                                        <hr>
                                                    </div> 

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welche Art von Heizungsanlage haben Sie?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->heating_system_type ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie alt ist Ihre Heizungsanlage?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->heating_system_age ?? 'N/A' }} Jahr</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Baujahr der Heizungsanlage?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->heating_system_year ?? 'N/A' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Welches Heizsystem ist verbaut?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->heating_type ?? 'N/A' }}</label>
                                                            </div>  
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wo befindet sich die aktuelle Heizungsanlage?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->installation_location ?? 'N/A' }}</label>
                                                                <label>{{ $customer->installation_location_extra ?? '' }}</label>
                                                            </div>  
                                                        </div>
                                                    </div>

                                                    <!-- Stromverbrauch Section -->
                                                    <div class="col-12">
                                                        <h2 class="primary"><strong>STROMVERBRAUCH</strong></h2>
                                                        <hr>
                                                    </div> 

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie hoch ist Ihr jährlicher Stromverbrauch?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <label>{{ $customer->annual_consumption ?? 'N/A' }} kWh</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Heizenergie Verbrauch Section -->
                                                    <div class="col-12">
                                                        <h2 class="primary"><strong>HEIZENERGIE VERBRAUCH</strong></h2>
                                                        <hr>
                                                    </div> 

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie hoch ist Ihr jährlicher Verbrauch an Heizenergie?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                @if($customer->annual_heating_energy_consumption)<label>{{ $customer->annual_heating_energy_consumption ?? 'N/A' }} CMB</label>@endif
                                                                @if($customer->annual_heating_energy_consumption_kwh)<label>{{ $customer->annual_heating_energy_consumption_kwh ?? 'N/A' }} kWh</label>@endif
                                                            
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- E-Mobilität Section -->
                                                    <div class="col-12">
                                                        <h2 class="primary"><strong>E-MOBILITÄT</strong></h2>
                                                        <hr>
                                                    </div> 

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Haben Sie ein Elektroauto? Oder planen Sie, welche zukaufen?</h3>
                                                            </div>
                                                            <div class="col-md-6 flex_me">
                                                                <label>{{ $customer->electric_car ?? 'N/A' }}</label>
                                                            </div>
                                                            <div class="col-md-6 flex_me">
                                                                <label>{{ $customer->electric_car_plan ?? '' }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieviel Kilometer Fahren Sie pro PKW im Jahr?</h3>
                                                            </div>
                                                            <div class="col-md-6 flex_me">
                                                                <label>{{ $customer->car_kilo ?? 'N/A' }} km</label>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                          <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#editenergy">
                                                    Bearbeiten  
                                                </button>
                                        <div class="modal fade text-left" id="editenergy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel17">ENERGIEVERBRAUCH & OBJEKTDATEN</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                     <form action="{{ route('customer.info.data') }}" method="post">
                                                        @csrf
                                                        <div class="modal-body"> 
                                                        <input type="hidden" name="id" value="{{$customer->id}}"> 
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
                                                                                    <input type="text" class="form-control textbox" name="roof_covering" value="{{ old('roof_covering', $customer->roof_covering) }}"> 
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
                                                                                    <select class="form-control form-element" name="heating_system_type" id="heating_system_type_edit">
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
                                                                                    <select class="form-control form-element" name="electric_car" id="electric_car_edit">
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
                                                                                    <h3 class="bold">Wieviel Kilometer Fahren Sie pro PKW im Jahr?</h3>
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
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light"  >Speichern</button>
                                                            <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="gallary" aria-labelledby="gallary-tab" role="tabpanel">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-outline-warning waves-effect waves-light" data-toggle="modal" data-target="#large">
                                                 UPLOAD
                                                 </button>

                                                <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel17">UPLOAD</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                    <form action="{{ route('customer.upload') }}" method="POST" class="dropzone" id="file-dropzone" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                                                        @csrf
                                                                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                                        <input type="hidden" name="address_no" value="{{ request()->address_no }}">
                                                                        <input type="hidden" name="product_id" id="image_product_id" value="">
                                                                        <input type="hidden" name="stage_id" id="stage_id" value="">

                                                                        <div>
                                                                            <label for="article_group">Gewerke auswählen:</label>
                                                                            <select id="article_group" class="form-control">
                                                                                <option value="">-- Wählen Sie eine Artikelgruppe --</option> 
                                                                                <!-- Options will be dynamically populated -->
                                                                            </select>
                                                                        </div>
                                                                        <div>
                                                                            <label for="swal-stage">Stufe auswählen:</label>
                                                                            <select id="swal-stage" class="form-control">
                                                                                <option value="">-- Wählen Sie eine Stufe --</option>
                                                                                <option value="customer">Kunde</option>
                                                                                <option value="montage">Montage</option>
                                                                                <option value="end">Abnahme</option>
                                                                            </select>
                                                                        </div>
                                                                    </form>

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Accept</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>  
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="text-bold-600 font-medium-2">
                                                    <i class="feather icon-search"></i> Filter
                                                </div> 
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="filter_image">
                                                        <option >FILTER AUSWÄHLEN</option>                                                        
                                                        <option value="1">KUNDENBILD FILTERN</option>
                                                        <option value="2">MONTAGEBILD FILTERN</option>
                                                        <option value="3">ENDBILD FILTERN</option>
                                                        <option value="4">ARTIKELBILD FILTERN</option>
                                                        <option value="5">ALLE FOTOS</option>
                                                    </select>
                                                </fieldset>
                                            </div>

                                            <div class="photo" id="photo_image" >
                                                 <div class="col-12 mt-2">
                                                    <div class="divider">
                                                        <div class="divider-text">FOTO</div>
                                                    </div>
                                                </div>

                                                @if($customer->inquiry_screenshot)
                                                <div class="col-md-3">
                                                        <div class="card-content">
                                                            <img class="card-img-top img-fluid open-modal" src="{{ asset($customer->inquiry_screenshot) }}" alt="Screenshot von Anfrage" data-image="{{ asset($customer->inquiry_screenshot) }}">
                                                            <div class="card-body p-0">
                                                                <h6 class="card-title  mt-1" data-id="{{ $customer->id }}">Screenshot von Anfrage</h6>
                                                            </div> 
                                                        </div>
                                                    </div> 
                                                @endif
                                                 <div class="row mt-2">  
                                                    @foreach ($images as $image) 
                                                        @if(in_array(strtolower($image->file_type), ['jpeg', 'jpg', 'png', 'gif']))
                                                            <div class="col-md-3">
                                                                <div class="card-content">
                                                                    <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$image->image) }}" alt="{{ $image->image_name }}" data-image="{{ asset('images/customers/'.$image->image) }}">
                                                                    <div class="card-body p-0">
                                                                        <h6 class="card-title edit_image_name mt-1" data-id="{{ $image->id }}">{{ $image->image_name }}</h6>
                                                                        <input type="text" data-id="{{$image->id}}" name="image_name" value="{{$image->image_name}}" class="form-control" style="display:none;">
                                                                    </div>
                                                                    <div class="card-footer p-0 mt-1"> 
                                                                        <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{$image->id}}"><i class="feather icon-trash"></i> Löschen</button> 
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        @endif
                                                    @endforeach 
                                                </div> 
                                            </div>
                                            
                                            <div class="article_image col-12" id="article_image" style="display:none" >
                                                <div class="col-12 mt-2">
                                                    <div class="divider">
                                                        <div class="divider-text">SORTIEREN NACH GEWERK</div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12">  
                                                    @foreach ($image_p_sort as $group => $images) <!-- $group is the article_group name, $images is the array of images -->
                                                        <div class="default-collapse collapse-bordered">
                                                            <div class="cards collapse-header">
                                                                <div id="headingCollapse{{ $group }}" class="card-header" data-toggle="collapse" role="button" data-target="#collapse{{ $group }}" aria-expanded="false" aria-controls="collapse{{ $group }}"
                                                                    style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                    <div class="lead collapse-title col-12">
                                                                        <h2 class="primary bold">{{ $group }}</h2> <!-- Display the article_group name -->
                                                                    </div>
                                                                </div>
                                                                <div id="collapse{{ $group }}" role="tabpanel" aria-labelledby="headingCollapse{{ $group }}" class="collapse">
                                                                    <div class="card-content">
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                @foreach ($images as $pImage) <!-- Loop through each image in the article_group -->
                                                                                    <div class="col-md-3">
                                                                                        <div class="card-content">
                                                                                            <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                            <div class="card-body p-0">
                                                                                                <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                                <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                            </div>
                                                                                            <div class="card-footer p-0 mt-1"> 
                                                                                                <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                    <i class="feather icon-trash"></i> Löschen
                                                                                                </button> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
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
                                  
                                            <div class="customer_image col-12" id="customer_image"  style="display:none" >
                                                <div class="col-12 mt-2">
                                                    <div class="divider">
                                                        <div class="divider-text">SORTIEREN NACH KUNDE</div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12">
                                                    <div class="default-collapse collapse-bordered">
                                                        <div class="cards collapse-header">
                                                            <div id="headingCustomer" class="card-header " data-toggle="collapse" role="button" data-target="#collapseCustomer" aria-expanded="false" aria-controls="collapseCustomer"
                                                                style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                <div class="lead collapse-title col-12">
                                                                    <h2 class="primary bold">BILDER VOM KUNDE</h2> <!-- Display 'Customer' as the group header -->
                                                                </div>
                                                            </div>
                                                            <div id="collapseCustomer" role="tabpanel" aria-labelledby="headingCustomer" class="collapse show">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            @foreach ($image_c_sort['customer'] as $pImage) <!-- Loop through each image under 'customer' stage -->
                                                                                <div class="col-md-3">
                                                                                    <div class="card-content">
                                                                                        <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                        <div class="card-body p-0">
                                                                                            <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                            <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                        </div>
                                                                                        <div class="card-footer p-0 mt-1"> 
                                                                                            <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                <i class="feather icon-trash"></i> Löschen
                                                                                            </button> 
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="montage_image col-12" id="montage_image"  style="display:none" >
                                                <div class="col-12 mt-2">
                                                    <div class="divider">
                                                        <div class="divider-text">SORTIEREN NACH MONTAGE</div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12">
                                                    <div class="default-collapse collapse-bordered">
                                                        <div class="cards collapse-header">
                                                            <div id="headingCustomer" class="card-header" data-toggle="collapse" role="button" data-target="#collapseMontage" aria-expanded="false" aria-controls="collapseMontage"
                                                                style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                <div class="lead collapse-title col-12">
                                                                    <h2 class="primary bold">BILDER VOM MONTAGE</h2> <!-- Display 'Customer' as the group header -->
                                                                </div>
                                                            </div>
                                                            <div id="collapseMontage" role="tabpanel" aria-labelledby="headingMontage" class="collapse show">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            @foreach ($image_m_sort['montage'] as $pImage) <!-- Loop through each image under 'customer' stage -->
                                                                                <div class="col-md-3">
                                                                                    <div class="card-content">
                                                                                        <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                        <div class="card-body p-0">
                                                                                            <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                            <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                        </div>
                                                                                        <div class="card-footer p-0 mt-1"> 
                                                                                            <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                <i class="feather icon-trash"></i> Löschen
                                                                                            </button> 
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                  
                                            <div class="end_image col-12"id="end_image" style="display:none" >
                                                <div class="col-12 mt-2">
                                                    <div class="divider">
                                                        <div class="divider-text">SORTIEREN NACH ABNAHME</div>
                                                    </div>
                                                </div> 
                                                <div class="col-md-12">
                                                    <div class="default-collapse collapse-bordered">
                                                        <div class="cards collapse-header">
                                                            <div id="headingCustomer" class="card-header" data-toggle="collapse" role="button" data-target="#collapseend" aria-expanded="false" aria-controls="collapseCustomer"
                                                                style="background: transparent; border-bottom: 1px solid #8fc73e;">
                                                                <div class="lead collapse-title col-12">
                                                                    <h2 class="primary bold">BILDER VOM ABNAHME</h2> <!-- Display 'End' as the group header -->
                                                                </div>
                                                            </div>
                                                            <div id="collapseend" role="tabpanel" aria-labelledby="headingCustomer" class="collapse show">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            @foreach ($image_e_sort['end'] as $pImage) <!-- Loop through each image under 'customer' stage -->
                                                                                <div class="col-md-3">
                                                                                    <div class="card-content">
                                                                                        <img class="card-img-top img-fluid open-modal" src="{{ asset('images/customers/'.$pImage->image) }}" alt="{{ $pImage->image_name }}" data-image="{{ asset('images/customers/'.$pImage->image) }}">
                                                                                        <div class="card-body p-0">
                                                                                            <h6 class="card-title edit_image_name mt-1" data-id="{{ $pImage->id }}">{{ $pImage->image_name }}</h6>
                                                                                            <input type="text" data-id="{{ $pImage->id }}" name="image_name" value="{{ $pImage->image_name }}" class="form-control" style="display:none;">
                                                                                        </div>
                                                                                        <div class="card-footer p-0 mt-1"> 
                                                                                            <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{ $pImage->id }}">
                                                                                                <i class="feather icon-trash"></i> Löschen
                                                                                            </button> 
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>   
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
 
                                                <!-- Document Section with Click Event to Open Modal -->
                                            <div class="col-12">
                                                <div class="divider">
                                                    <div class="divider-text">DUKUMENT</div>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2 d-flex"> 
                                                @foreach ($images as $image) 
                                                    @if(in_array(strtolower($image->file_type), ['pdf', 'docx', 'xlsx', 'doc'])) 
                                                        <div class="col-md-5">
                                                            <div class="card-content">
                                                                <div class="file-preview" style="text-align: center; padding: 10px;">
                                                                    @if(strtolower($image->file_type) === 'pdf')
                                                                        <iframe src="{{ asset('images/customers/'.$image->image) }}" frameborder="0" style="width: 100%; height: 150px;"></iframe>
                                                                    @elseif(strtolower($image->file_type) === 'docx' || strtolower($image->file_type) === 'doc')
                                                                        <i class="fa fa-file-word-o primary" style="font-size: 50px; color: #007bff;"></i>
                                                                    @elseif(strtolower($image->file_type) === 'xlsx')
                                                                        <i class="fa fa-file-excel-o primary" style="font-size: 50px; color: #28a745;"></i>
                                                                    @endif
                                                                </div>
                                                                <div class="card-body">
                                                                    <h6 class="card-title edit_image_name" data-id="{{ $image->id }}">
                                                                        <span class="open-document" data-file-type="{{ $image->file_type }}" 
                                                                        data-file-name="{{ $image->image_name }}" data-file-url="{{ asset('images/customers/'.$image->image) }}" 
                                                                        data-toggle="tooltip" data-placement="top" title="" data-original-title="Klicken Sie hier, um die Datei zu öffnen">
                                                                        <strong > {{ $image->image_name }}</strong>
                                                                        </span> 
                                                                    </h6>  
                                                                    <input type="text" data-id="{{$image->id}}" name="image_name" value="{{$image->image_name}}" class="form-control">
                                                                </div>
                                                                <div class="card-footer"> 
                                                                    <button type="button" class="btn btn-icon btn-flat-danger mr-1 waves-effect waves-light" data-id="{{$image->id}}"><i class="feather icon-trash"></i> Delete</button>  
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    @endif
                                                @endforeach 
                                            </div>
                                    
                                            <!-- Modal with Range-Based Zoom and Dynamic Title -->
                                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="imageModalLabel">BILDVORSCHAU</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <div class="image-container" style="overflow: hidden; max-height: 80vh;">
                                                                <img id="modalImage" src="" alt="Preview" style="max-width: 100%; max-height: 100%; transform-origin: center center;">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="range" id="image_zoom" min="1" max="5" step="0.1" class="form-control" value="1" style="width: 100%;">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                            <!-- Image Preview Modal -->

                                            <!-- Document Modal with Icon or PDF Preview -->
                                            <div class="modal fade text-left" id="customer_document" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel16">DOKUMENT VIEWER</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-center" id="document_viewer_body">
                                                            <!-- Content will be loaded dynamically based on file type -->
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a id="download_button" href="#" download class="btn btn-success">Download Document</a>
                                                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Document Dialog: End  --> 
                                        </div> 
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </section>
            <div class="col-12">
                <br>
                <hr>
            </div>
            <section id="contents">
                <div class="col-12 alterative d-flex"   id="alternative_address">  
                    <div class="col-md-4 col-sm-12 col-12">
                        <!-- {{-- Product Selection Start --}}  -->
                            <div class="card-body">
                                <div class="card-title h4 mb-3">
                                    <h1 class="primary bold">GEWERKE DES OBJEKTS</h1>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-12 col-12">
                                        <form action="{{ route('customer.product.save') }}" method="post" style="width:100%;">
                                            @csrf
                                            <input type="hidden" id="customerId" name="customer_id" value="{{ $customer->id }}">
                                            @foreach ($articles as $item)
                                            <article style="display: flex; align-items: center;">
                                                <div
                                                    class="text-center bg-transparent products mt-1 mb-1 col-10 {{ in_array($item->id, $selectedProducts) ? 'selected' : '' }}"
                                                    data-product-id="{{ $item->id }}"> <!-- Added data-product-id attribute -->
                                                    <div class="card-content">

                                                        <div class="row product_card">
                                                            <div class="col-md-2 col-2" id="product_card_image">
                                                                <img src="{{ asset('images/articles/'.$item->image) }}" alt="{{ $item->article_group }}"
                                                                    style="width: 100px !important;" class="float-left mt-1">
                                                            </div>
                                                            <a href="{{ route('customer.phase.managment', ['customer' => request()->id,'postcode'=>request()->postcode,'address_no'=>request()->address_no, 'product' => $item->id]) }}">
                                                                <div class="col-md-10 col-10" id="product_card_details">
                                                                    <h2 class="card-title mt-1 mb-0 white title">{{ $item->article_group }}</h2>
                                                                    <p class="card-text">
                                                                        <a href="#" class="white">Projektdaten</a> | 
                                                                        <a href="#" class="white">Arbeitsschritte</a>
                                                                    </p>
                                                                    <p class="card-text white mb-1"> 
                                                                        Aktualler Status: 
                                                                        <span id="interested-{{ $loop->index }}">
                                                                            {{ in_array($item->id, $selectedProducts) ? 'Interessiert' : '' }}
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        <input type="checkbox" class="d-none" name="product_id[]" value="{{ $item->id }}"
                                                            {{ in_array($item->id, $selectedProducts) ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="settings col-2"
                                                    style="display: flex !important; align-items: flex-start;flex-direction: column; row-gap: 3px;">

                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-light waves-effect waves-light buttons heart-button"
                                                        style="width: 50px; height: 50px;" id="{{ $loop->index }}Like">
                                                        <i class="fa fa-heart icons heart-icon"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-light  waves-effect waves-light buttons"
                                                        id="{{ $loop->index }}MenuButton" style="width: 50px; height: 50px;"
                                                        onclick="toggleDiv('{{ $loop->index }}', '{{ $item->article_group }}')">
                                                        <i class="feather icon-align-justify icons menu-button"></i>
                                                    </button>
                                                </div>
                                            </article>
                                        @endforeach

                                        </form>
                                    </div>
                                </div> 
                            </div> 
                        <!-- {{-- Product Selection: End --}} -->
                    </div>

                    <!-- {{-- Alternative Address --}} -->
                    <div class="col-8 col-12" id="alternative" style="display: flex;flex-wrap: wrap;">
                        <!-- {{-- Alternative Address and Image --}} -->
                        <article class="col-md-12 col-sm-12 col-12 ">
                            <form method="post" action="{{ route('customer.product.save.alternative') }}">
                                @csrf
                                <div class="col-md-5 col-12">
                                    <div class="collapse-icon accordion-icon-rotate">
                                        <div class="card-body">
                                            <div class="card-title h4 mb-3">
                                                <h1 class="primary bold">ADDRESS INFO</h1>
                                            </div>

                                            <div class="col-12"> 
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <fieldset>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" name="same_address" id="same" checked>
                                                                <label class="custom-control-label" for="same">
                                                                    @if($alternative){
                                                                        @if($alternative->main==1)
                                                                            Die Postanschrift ist identisch mit der Hauptadresse
                                                                        @else
                                                                            Die Postanschrift ist die Differenz zur Hauptanschrift
                                                                            <br><code><small>zum Ändern das Häkchen entfernen</small></code>
                                                                        @endif
                                                                    } @else
                                                                        Die Postanschrift ist identisch mit der Hauptadresse
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Straße / Nr.</span>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input id="location-input" type="text" class="form-control" placeholder="Enter location" name="street"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->street : $alternative->street) : $customer->street }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>PLZ / Ort</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <!-- Hidden Fields -->
                                                        <input type="hidden" name="customer_id" value="{{ request()->id }}">
                                                        <input type="hidden" id="latitude-input" name="latitude"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->lat : $alternative->lat) : $customer->lat }}">
                                                        <input type="hidden" id="longitude-input" name="longitude"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->lon : $alternative->lon) : $customer->lon }}">
                                                        <input type="hidden" name="old_street"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->street : $alternative->street) : $customer->street }}">
                                                        <input type="hidden" name="old_postcode"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->postcode : $alternative->postcode) : $customer->postcode }}">
                                                        <input type="hidden" name="old_city"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->city : $alternative->city) : $customer->city }}">
                                                        <input type="hidden" name="old_elevation"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->elevation : $alternative->elevation) : $customer->elevation }}">
                                                        <input type="hidden" id="elevation-input" name="elevation">
                                                        <input type="hidden" name="request_address_no" id="request_address_no" value="{{ request()->address_no }}">

                                                        <!-- Postcode Input -->
                                                        <input type="text" class="form-control" name="postcode" id="postal_code-input"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->postcode : $alternative->postcode) : $customer->postcode }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <!-- City Input -->
                                                        <input type="text" class="form-control" name="city" id="locality-input"
                                                            value="{{ $alternative ? ($alternative->main == 0 ? $customer->city : $alternative->city) : $customer->city }}">
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-outline-primary mr-1 mb-1 mt-2 waves-effect waves-light" id="new_address">Neue Adresse speichern</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <div class="col-md-7 col-12">
                                <div class="card">
                                    <div class="card-content">
                                        <div class="card-body"> 
                                            <div class="card-title h4 mb-3">
                                                <h1 class="primary bold">FOTOS DES OBJEKTS</h1>
                                            </div>
                                            <div class="map" id="gmp-map" style="width: 100%;position: relative;overflow: hidden; height: 478px;">  </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>

                        </article>
                        <!-- {{-- Alternative Address and Image: End --}} -->
                    </div>
                    
                    <!-- {{-- Alternative Address: End --}} -->

                    <!-- {{-- Product Checklist and Forms: Start --}} -->

                    <!-- {{-- PV Checklist: Start --}} -->
                    <div class="col-md-8 col-12" id="pv" style="display: none !important;">
                        <article class="col-md-12 col-sm-12 col-12">
                            <div class="col-md-12 float-right card-title h4 mb-3 flex_me "
                                style="    justify-content: right;">
                                <div class="col-md-2">
                                    <span style="color: #74b2d3"> Bewertung Projekt: <span
                                            style="color:#e50056">4</span>/23 </span>
                                </div>
                                <div class="col-md-2">
                                    <div class="progress progress-bar-danger progress-lg">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" aria-valuenow="60" aria-valuemin="60"
                                            aria-valuemax="100" style="width:60%"></div>
                                    </div>
                                </div>
                            </div>
                            <form method="post" enctype="multipart/form-data" id="pvForm">
                                @csrf
                                <div class="container"
                                    style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; box-shadow: 0px 0px 10px 2px #a2a2a2;">
                                    <div class="col-md-12" id="section_1">
                                        <div class="cards">
                                            <div class="card-body d-flex">
                                                <div class="col-md-2 image">
                                                    <img src="{{ asset('images/articles/pv.png') }}"
                                                        alt="alternative" style="width: 128px;">
                                                </div>
                                                <div class="col-md-10 contents">
                                                    <input type="hidden" name="customer_id"
                                                        value="{{ $customer->id }}">
                                                    <h2 class="title" style="color: #74b2d3">PHOTOVOLTAIK</h2>
                                                    <div class="form-group row">
                                                        <div class="col-md-12 mb-2 mt-2">
                                                            <span>Intention</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_interest"
                                                                                value="Interesse">
                                                                            <label class="custom-control-label"
                                                                                for="intention_interest">Interesse</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_available"
                                                                                value="vorhanden">
                                                                            <label class="custom-control-label"
                                                                                for="intention_available">vorhanden</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_extension"
                                                                                value="Erweiterung">
                                                                            <label class="custom-control-label"
                                                                                for="intention_extension">Erweiterung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="intention"
                                                                                id="intention_spater"
                                                                                value="später">
                                                                            <label class="custom-control-label"
                                                                                for="intention_spater">später</label>
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

                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <section class="col-md-12" id="section_2">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold ">Objektart</h4>
                                                        </div>
                                                        <input type="hidden" name="customer_id"
                                                            value="{{ request()->id }}">
                                                        <input type="hidden" name="postcode"
                                                            value="{{ request()->postcode }}">
                                                        <input type="hidden" name="address_no"
                                                            value="{{ request()->address_no }}">


                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objective_EFH" checked
                                                                                value="EFH">
                                                                            <label class="custom-control-label"
                                                                                for="objective_EFH">EFH</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objectiveMFH" value="MFH">
                                                                            <label class="custom-control-label"
                                                                                for="objectiveMFH">MFH</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="objectiveNeubau"
                                                                                value="Neubau">
                                                                            <label class="custom-control-label"
                                                                                for="objectiveNeubau">Neubau</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="consultation_telefonisch"
                                                                                value="Sanierung">
                                                                            <label class="custom-control-label"
                                                                                for="consultation_telefonisch">Sanierung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="property_type"
                                                                                id="consultation_Einzelmassnahmen"
                                                                                value="Einzelmaßnahmen">
                                                                            <label class="custom-control-label"
                                                                                for="consultation_Einzelmassnahmen">Einzelmaßnahmen</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl WE</h4>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox"
                                                                name="number_of_units" value="">
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Zähler</h4>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox"
                                                                name="number_of_meters">
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row ">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Stromverbrauch</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me textbox-container empty ">
                                                            <input type="text" class="form-control textbox"
                                                                name="electricity_consumption" value="{{ old('electricity_consumption', $customer->annual_consumption) }}">
                                                            <div class="indicator"></div>
                                                            <span style="  margin-left: -45px;">
                                                                kWh</span>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold ">E-Auto</h4>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="electric_car"
                                                                                id="e_auto_no" @if($customer->electric_car=="Nein") checked @endif
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_no">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="electric_car"
                                                                                id="e_auto_yes" value="ja" @if($customer->electric_car=="Ja") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_yes">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-blocks mr-1 "
                                                                    style="width:330px">
                                                                    <div class="form-group row ">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Anzahl</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text"
                                                                                class="form-control"
                                                                                name="number_of_electric_cars" value="{{old('number_of_electric_cars', $customer->electric_car_plan)}}">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold ">Wallbox gewünscht</h4>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wallbox_desired"
                                                                                id="wall_box_no" checked
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="wall_box_no">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wallbox_desired"
                                                                                id="wall_box_yes" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="wall_box_yes">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-blocks mr-1 "
                                                                    style="width:330px">
                                                                    <div class="form-group row ">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Anzahl</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text"
                                                                                class="form-control"
                                                                                name="number_of_wallboxes">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </section>

                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    @php
                                    $roof_no= 1;
                                    @endphp
                                    <section class="col-md-12 dynamic-section" id="section_3">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important; flex-wrap: wrap;">
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h4 class="bold">Dach 1</h4>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <span>Bezeichnung</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control"
                                                                name="designation[0]" value="">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" id="add_more"
                                                                class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light">
                                                                <i class="feather icon-plus"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="remove_roof btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light d-none">
                                                                <i class="feather icon-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" style="margin-bottom: 40px;">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <ul class="list-unstyleds mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Satteldach.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:74px;"
                                                                            for="roof_Satteldach_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Satteldach_0"
                                                                                value="Satteldach" @if($customer->roof_type=="Satteldach") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Satteldach_0">Satteldach</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Flachdach.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:74px;"
                                                                            for="roof_Flachdach_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Flachdach_0"
                                                                                value="Flachdach" @if($customer->roof_type=="Flachdach") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Flachdach_0">Flachdach</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Garage.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:74px;"
                                                                            for="roof_Garage_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Garage_0"
                                                                                value="Garage" @if($customer->roof_type=="Garage") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Garage_0">Garage</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <img src="{{ asset('images/roofs/Carport.png') }}"
                                                                            alt="" srcset=""
                                                                            style="width:74px;"
                                                                            for="roof_Carport_0">
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof[0]"
                                                                                id="roof_Carport_0"
                                                                                value="Carport" @if($customer->roof_type=="Carport") checked @endif>
                                                                            <label class="custom-control-label"
                                                                                for="roof_Carport_0">Carport</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Dacheindeckung</h3>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="tiles" name="tiles[0]"
                                                                style="width:100%">
                                                                @foreach ($tiles as $tile)
                                                                <option value="{{ $tile->id }}"
                                                                    data-image="{{ asset('images/products/'.$tile->image) }}"
                                                                    data-roof-type="{{ $tile->roof_type }}">
                                                                    {{ $tile->product }} ->
                                                                    {{ $tile->roof_type }}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6" id="construction_fluid_section_0">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="construction_fluid[0]"
                                                                                id="construction_fluid_boton_0"
                                                                                value="Beton">
                                                                            <label class="custom-control-label"
                                                                                for="construction_fluid_boton_0">Beton</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="construction_fluid[0]"
                                                                                id="construction_fluid_ton_0"
                                                                                value="Ton">
                                                                            <label class="custom-control-label"
                                                                                for="construction_fluid_ton_0">Ton</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-md-6" id="tilt_section_0">
                                                            <div class="form-group row">
                                                                <div class="col-md-4">
                                                                    <h4 class="bold">Neigung</h4>
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <input type="text" class="form-control"
                                                                        name="tilt[0]">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="insulation_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Aufdachdämmung</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="pv_insulation[0]"
                                                                                id="insulation_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="insulation_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="pv_insulation[0]"
                                                                                id="insulation_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="insulation_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1"
                                                                    style="width:330px">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Stärke</h4>
                                                                        </div>
                                                                        <div
                                                                            class="col-md-8 textbox-container empty">
                                                                            <input type="text"
                                                                                class="form-control textbox"
                                                                                name="thickness_roof_insulation[0]"
                                                                                placeholder=" ">
                                                                            <div class="indicator"></div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="rafter_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Zwischen sparrendämmung</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="between_rafter_insulation[0]"
                                                                                id="rafter_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="rafter_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="between_rafter_insulation[0]"
                                                                                id="rafter_nein_0" value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="rafter_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1"
                                                                    style="width:330px">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Stärke</h4>
                                                                        </div>
                                                                        <div
                                                                            class="col-md-8 textbox-container empty">
                                                                            <input type="text"
                                                                                class="form-control textbox"
                                                                                name="thickness_between_rafter[0]">
                                                                            <div class="indicator"></div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="asbestos_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Asbesthaltig</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="asbestos[0]"
                                                                                id="asbestos_ja_0" value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="asbestos_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="asbestos[0]"
                                                                                id="asbestos_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="asbestos_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12" id="roof_renovation_section_0">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <h3 class="bold">Dachsanierung notwendig</h3>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof_renovation[0]"
                                                                                id="roof_renovation_ja_0"
                                                                                value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="roof_renovation_ja_0">ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="roof_renovation[0]"
                                                                                id="roof_renovation_nein_0"
                                                                                value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="roof_renovation_nein_0">nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <div class="col-12">
                                        <hr>
                                    </div>

                                    <section class="col-md-12" id="section_4">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">
                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Zählerschrank</h4>
                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap;flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_ok" checked
                                                                                value="ok">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_ok">ok</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_strengthen"
                                                                                value="strengthen">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_strengthen">ertüchtigen</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_neu" value="neu">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_neu">neu</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="meter_cabinet"
                                                                                id="cabinet_neuer"
                                                                                value="neuer Zählerschrank zwischen HAK und Zählerschrank">
                                                                            <label class="custom-control-label"
                                                                                style="width: 278px;"
                                                                                for="cabinet_neuer">neuer
                                                                                Zählerschrank zwischen HAK und
                                                                                Zählerschrank</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Größe</h4>
                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap;flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_550" checked
                                                                                value="550">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_550">550</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_800"
                                                                                value="800">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_800">800</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_1100"
                                                                                value="1100">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_1100">1100</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="cabinet_size"
                                                                                id="cabinet_size_son"
                                                                                value="Sonstiges">
                                                                            <label class="custom-control-label"
                                                                                for="cabinet_size_son">Sonstiges</label>
                                                                            <input type="text"
                                                                                name="cabinet_size_sonstiges">

                                                                        </div>
                                                                    </fieldset>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">

                                                        </div>
                                                        <div class="col-md-4 flex_me">
                                                            <fieldset>
                                                                <label>Hersteller</label>

                                                                <select name="meter_cabinet_company" id=""
                                                                    class="form-control">
                                                                    @foreach ($electro as $elec)
                                                                    <option value="{{ $elec->id }}">
                                                                        {{ $elec->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Einzubauende Komponenten</h4>
                                                        </div>
                                                        <div class="col-md-12 flex_me">
                                                            <ul class="mb-0"
                                                                style="display:flex; flex-wrap: wrap; flex-direction: column;">
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="meter_adapter_plate"
                                                                                id="meter_adapter_plate">
                                                                            <label class="custom-control-label"
                                                                                for="meter_adapter_plate">Zähleradapterplatte</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="ac_surge_protection"
                                                                                id="ac_surge_protection">
                                                                            <label class="custom-control-label"
                                                                                for="ac_surge_protection"
                                                                                style="width: 232px;">AC
                                                                                Überspannungsschutz</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="ac_switch" id="ac_switch">
                                                                            <label class="custom-control-label"
                                                                                for="ac_switch">SLS
                                                                                Schalter</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="apz_field" id="apz_field">
                                                                            <label class="custom-control-label"
                                                                                for="apz_field">APZ Feld</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="disconnect_relay"
                                                                                id="disconnect_relay">
                                                                            <label class="custom-control-label"
                                                                                for="disconnect_relay">Trenn-Relais</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="checkbox"
                                                                                class="custom-control-input"
                                                                                name="equipotential_bonding"
                                                                                id="equipotential_bonding_busbar">
                                                                            <label class="custom-control-label"
                                                                                for="equipotential_bonding_busbar">Potentialausgleichsschiene</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                    </section>

                                    <div class="col-12">
                                        <hr>
                                    </div>

                                    <section class="col-md-12 dynamic-section" id="section_5">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important; flex-wrap: wrap;">
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-12">
                                                            <h4 class="bold">Fotos/Unterlagen des Kunden
                                                                erforderlich</h4>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <ul class="mb-0"
                                                                style="display: flex; flex-wrap: wrap;">
                                                                @if($image_category)
                                                                @foreach ($image_category as $category =>
                                                                $catData)
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div
                                                                            class="vs-checkbox-con vs-checkbox-primary">
                                                                            <input type="checkbox"
                                                                                value="{{ $catData['category_id'] }}"
                                                                                checked disabled
                                                                                name="pv_image_category">
                                                                            <span
                                                                                class="vs-checkbox vs-checkbox-lg">
                                                                                <span
                                                                                    class="vs-checkbox--check">
                                                                                    <i
                                                                                        class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span>{{ $category }}</span> &nbsp;
                                                                            <i class="fa fa-image show_image light"
                                                                                data-category="{{ $catData['category_id'] }}"></i>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                @endforeach

                                                                @endif
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    @foreach ($image_category as $category => $catData)
                                                    <div id="carousel-keyboard-{{ $catData['category_id'] }}"
                                                        class="carousel slide d-none" data-keyboard="true"
                                                        data-category="{{ $catData['category_id'] }}">
                                                        <div class="carousel-inner" role="listbox">
                                                            @foreach ($catData['images'] as $index => $image)
                                                            <div
                                                                class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                                                <img class="img-fluid"
                                                                    src="{{ asset('images/customers/home/'.$image['image']) }}"
                                                                    alt="{{ $image['image_name'] }}"
                                                                    style=" text-align: center;">
                                                                <div class="carousel-caption d-none d-md-block"
                                                                    style="top: 0; bottom: auto;">
                                                                    <h3 class="title">{{ $category }}:
                                                                        {{ $image['image_name'] }}</h3>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                        <a class="carousel-control-prev custom-control-prev"
                                                            href="#carousel-keyboard-{{ $catData['category_id'] }}"
                                                            role="button" data-slide="prev">
                                                            <span class="carousel-control-prev-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="sr-only">Previous</span>
                                                        </a>
                                                        <a class="carousel-control-next custom-control-next"
                                                            href="#carousel-keyboard-{{ $catData['category_id'] }}"
                                                            role="button" data-slide="next">
                                                            <span class="carousel-control-next-icon"
                                                                aria-hidden="true"></span>
                                                            <span class="sr-only">Next</span>
                                                        </a>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div> <!-- Missing closing tag added here -->
                                        </div>
                                    </section>



                                    <div class="col-12 float-right">
                                        <button type="button" id="saveButton"
                                            class="btn btn-icon btn-icon btn-light mr-1 mb-1 waves-effect waves-light float-right">
                                            <i class="feather icon-save"></i> Daten Ubernahnen
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </article>
                    </div>
                    <!-- {{-- PV Checklist: End --}} -->

                    <!-- {{-- WP Checklist: Start --}} -->
                    <div class="col-md-8 col-12" id="wp" style="display: none !important;">
                        <article class="col-md-12 col-sm-12 col-12">
                            <form method="post" action="{{ action('App\Http\Controllers\WPChecklistController@store')}}">
                                @csrf
                                <div class="container"
                                    style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; box-shadow: 0px 0px 10px 2px #a2a2a2;">
                                    <div class="col-md-12" id="section_1">
                                        <div class="cards">
                                            <div class="card-body d-flex">
                                                <div class="col-md-2 image">
                                                    <img src="{{ asset('images/articles/icon-wp.png') }}"
                                                        alt="alternative" style="width: 128px;">
                                                </div>
                                                <input type="hidden" name="customer_id" value="{{ $customer->id}}">
                                                <input type="hidden" name="postcode" value="{{ request()->postcode }}">
                                                <div class="col-md-10 contents">
                                                    <h2 class="title" style="color: #74b2d3">WÄRMEPUMPE</h2>
                                                    <div class="form-group row">
                                                        <div class="col-md-12 mb-2 mt-2">
                                                            <span>Intention</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_interest"
                                                                                value="Interesse">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_interest">Interesse</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_available"
                                                                                value="vorhanden">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_available">vorhanden</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_extension"
                                                                                value="Erweiterung">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_extension">Erweiterung</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="wp_intention"
                                                                                id="wp_intention_spater"
                                                                                value="später">
                                                                            <label class="custom-control-label"
                                                                                for="wp_intention_spater">später</label>
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
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                    <section class="col-md-12" id="section_wp_2">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;"> 
                                                <div class="col-12">
                                                    <div class="form-group row">   
                                                        <h4 class="bold ">Objektart</h4> 
                                                        <select name="wp_objective" id="" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="EFH" @if($wp && $wp->wp_objective == "EFH") selected @endif>EFH</option>
                                                        <option value="MFH" @if($wp && $wp->wp_objective == "MFH") selected @endif>MFH</option>
                                                        <option value="Gewerbe" @if($wp && $wp->wp_objective == "Gewerbe") selected @endif>Gewerbe</option>
                                                        <option value="others" @if($wp && $wp->wp_objective == "Sonstigis") selected @endif>Sonstigis</option>
                                                    </select> 
                                                    </div>
                                                </div>

                                                <div class="col-6 col-md-6 col-12">
                                                    <div class="form-group">   
                                                        <h4 class="bold ">Objektzustand</h4>
                                                        <select name="wp_object" id="" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="new" @if($wp && $wp->wp_object == "new") selected @endif>Neubau</option>
                                                        <option value="renovation" @if($wp && $wp->wp_object == "renovation") selected @endif>Sanierung</option>
                                                        <option value="individual measures" @if($wp && $wp->wp_object == "individual measures") selected @endif>Einzelmaßnahmen</option>
                                                        <option value="others" @if($wp && $wp->wp_object == "others") selected @endif>Sonstigis</option>
                                                    </select>

                                                    </div>
                                                </div>

                                                    <div class="col-6 col-md-6 col-12">
                                                    <div class="form-group">   
                                                        <h4 class="bold ">Heizungsart</h4> 
                                                    <select name="wp_heating_type" id="wp_heating_type" class="form-control">
                                                        <option value="">Bitte wählen</option>
                                                        <option value="1" @if($wp && $wp->wp_heating_type == "1") selected @endif>Fußbodenheizung</option>
                                                        <option value="2" @if($wp && $wp->wp_heating_type == "2") selected @endif>Heizkörper</option>
                                                        <option value="3" @if($wp && $wp->wp_heating_type == "3") selected @endif>Fußbodenheizung + Heizkörper</option>
                                                        <option value="4" @if($wp && $wp->wp_heating_type == "4") selected @endif>Keine</option>
                                                    </select>

                                                    </div>
                                                </div>  
                                            </div> 

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered"> 
                                                                            <div class="cards collapse-header" id="underfloorC">
                                                                                <div id="heading6" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion6" aria-expanded="false" aria-controls="accordion6">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Fußboden Heizkreise</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion6" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading6" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                    <div class="table-responsive">
                                                                                                        <form id="heating_circuit_form">
                                                                                                            @csrf
                                                                                                            <table class="table" id="number_of_heating_circuits">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th>Anzahl Heizkreise</th> 
                                                                                                                        <th>Vorlauf ℃</th>
                                                                                                                        <th>Rücklauf ℃</th>
                                                                                                                        <th>Geschoß</th>
                                                                                                                        <th>Rohedeminsion</th>
                                                                                                                        <th>Rohematerial</th> 
                                                                                                                        <th>Aktion</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    <tr id="heating_circuit">
                                                                                                                        <th scope="row">
                                                                                                                            <input type="text" name="heating[0][heating_circuit_number]" class="form-control" value="1" readonly>
                                                                                                                        </th> 
                                                                                                                        <td>
                                                                                                                            <input type="text" class="form-control" placeholder="Vorlauf" name="heating[0][flow_temperature]">
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <input type="text" class="form-control" placeholder="Rücklauf" name="heating[0][return_flow_temperature]">
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][room_story]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="KG">KG</option>
                                                                                                                                <option value="EG">EG</option>
                                                                                                                                <option value="OG">OG</option>
                                                                                                                                <option value="DG">DG</option>
                                                                                                                            </select>
                                                                                                                        </td> 
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][pipe_dimension]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="12">12</option>
                                                                                                                                <option value="14">14</option>
                                                                                                                                <option value="16">16</option>
                                                                                                                                <option value="17">17</option>
                                                                                                                                <option value="18">18</option>
                                                                                                                                <option value="20">20</option>
                                                                                                                            </select>
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <select name="heating[0][pipe_material]" class="form-control">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="Kupfer">Kupfer</option>
                                                                                                                                <option value="Kunststoff">Kunststoff</option>
                                                                                                                            </select>
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <button type="button" class="btn btn-icon btn-warning add-heating-row">
                                                                                                                                <i class="feather icon-plus"></i>
                                                                                                                            </button>
                                                                                                                        </td>
                                                                                                                    </tr> 
                                                                                                                </tbody>
                                                                                                            </table> 
                                                                                                            <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light" id="save_heating_cercuit">Speichern</button>
                                                                                                        </form>

                                                                                                        </div>

                                                                                                    <div class="table-responsive">
                                                                                                        <table class="table" id="number_of_heating_circuits_details">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>Anzahl Heizkreise</th> 
                                                                                                                    <th>Vorlauf ℃</th>
                                                                                                                    <th>Rücklauf ℃</th>
                                                                                                                    <th>Geschoß</th>
                                                                                                                    <th>Rohedeminsion</th>
                                                                                                                    <th>Rohematerial</th> 
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                
                                                                                                            </tbody>
                                                                                                        </table> 

                                                                                                        <!-- Modal for adding/editing Heating Circuit -->
                                                                                                    

                                                                                                    <div class="modal fade" id="heatingCircuitModal" tabindex="-1" role="dialog" aria-labelledby="heatingCircuitModalLabel" aria-hidden="true">
                                                                                                        <div class="modal-dialog" role="document">
                                                                                                            <div class="modal-content">
                                                                                                                <div class="modal-header">
                                                                                                                    <h5 class="modal-title" id="heatingCircuitModalLabel">Heizkreis hinzufügen/bearbeiten</h5>
                                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                                <div class="modal-body">
                                                                                                                    <form id="heatingCircuitForm">
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Anzahl Heizkreise</label>
                                                                                                                            <input type="text" id="heating_circuit_number" class="form-control" name="heating_circuit_number" readonly>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Vorlauf ℃</label>
                                                                                                                            <input type="text" id="flow_temperature" class="form-control" name="flow_temperature">
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rücklauf ℃</label>
                                                                                                                            <input type="text" id="return_flow_temperature" class="form-control" name="return_flow_temperature">
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Geschoß</label>
                                                                                                                            <select id="room_story" class="form-control" name="room_story">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="KG">KG</option>
                                                                                                                                <option value="EG">EG</option>
                                                                                                                                <option value="OG">OG</option>
                                                                                                                                <option value="DG">DG</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rohedeminsion</label>
                                                                                                                            <select id="pipe_dimension" class="form-control" name="pipe_dimension">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="12">12</option>
                                                                                                                                <option value="14">14</option>
                                                                                                                                <option value="16">16</option>
                                                                                                                                <option value="18">18</option>
                                                                                                                                <option value="20">20</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="form-group">
                                                                                                                            <label>Rohematerial</label>
                                                                                                                            <select id="pipe_material" class="form-control" name="pipe_material">
                                                                                                                                <option value=""></option>
                                                                                                                                <option value="Kupfer">Kupfer</option>
                                                                                                                                <option value="Kunststoff">Kunststoff</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <input type="hidden" id="heating_circuit_id">
                                                                                                                    </form>
                                                                                                                </div>
                                                                                                                <div class="modal-footer">
                                                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                                                    <button type="button" id="saveHeatingCircuit" class="btn btn-primary">Speichern</button>
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

                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered">  

                                                                            <div class="cards collapse-header" id="rediatorC">
                                                                                <div id="headingr" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordionr" aria-expanded="false" aria-controls="accordionr">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Heizkörper</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordionr" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="headingr" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">  
                                                                                                    <div class="table-responsive">
                                                                                                        <a type="button" class="btn btn-outline-success waves-effect waves-light float-right mb-1" href="{{ url('radiator_config_create/'.$customer->id.'/'.$customer->postcode.'/'.request()->address_no) }}"> <i class="feather icon-plus"></i> Neue / Bearbiten </a>
                                                                                                        <table class="table" id="rediator_details">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>#</th> 
                                                                                                                    <th>ETAGE</th>
                                                                                                                    <th>RAUM</th>
                                                                                                                    <th>TYP</th>
                                                                                                                    <th>GRÖSSE <small><code>HxBxT</code></small></th>
                                                                                                                    <th>NISCHE</th> 
                                                                                                                    <th>ANSCHLÜSSE</th> 
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                                @foreach ($rediators as $red) 
                                                                                                                <tr>
                                                                                                                    <td>
                                                                                                                        <a href="" data-toggle="modal" data-target="#preview{{$red->id}}">
                                                                                                                        <div class="avatar mr-1 avatar-xl" >
                                                                                                                            <img src="{{ asset('images/radiators/'.$red->image) }}" >
                                                                                                                        </div> 
                                                                                                                        </a>
                                                                                                                        <div class="modal fade text-left" id="preview{{$red->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel150" style="display: none;" aria-hidden="true">
                                                                                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                                                                                <div class="modal-content">
                                                                                                                                    <div class="modal-header bg-dark white">
                                                                                                                                        <h5 class="modal-title" id="myModalLabel150">{{ $red->floor }} - {{ $red->room }}</h5>
                                                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                            <span aria-hidden="true">×</span>
                                                                                                                                        </button>
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-body">
                                                                                                                                        <img src="{{ asset('images/radiators/'.$red->image) }}"  style="width:300px;"> 
                                                                                                                                    </div>
                                                                                                                                    <div class="modal-footer">
                                                                                                                                        <button type="button" class="btn btn-dark waves-effect waves-light" data-dismiss="modal">OK</button>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </td>
                                                                                                                    <td>{{ $red->floor }}</td>
                                                                                                                    <td>{{ $red->room }}</td>
                                                                                                                    <td>{{ $red->type }}</td>
                                                                                                                    <td>{{ $red->height }} x {{ $red->width }} x {{ $red->depth }}</td>
                                                                                                                    <td>{{ $red->niche_right }} x {{ $red->niche_left }} x {{ $red->niche_top }} {{ $red->niche_bottom }}</td>
                                                                                                                    <td>
                                                                                                                        <ul> 
                                                                                                                            <li><strong>Vorlaufventil</strong> {{ $red->supply_valve }} @if($red->supply_valve_presettable) (voreinstellbar) @endif</li>
                                                                                                                            <li><strong>Rücklaufventil</strong> {{ $red->return_valve }} @if($red->return_valve_present) (vorhanden) @endif</li>
                                                                                                                            <li><strong>Bauform</strong> {{ $red->design }}</li>
                                                                                                                            <li><strong>Thermostatkopf</strong> {{ $red->renew_thermostat_head ? 'muss erneuert werden' : 'muss nicht erneuert werden' }}</li>
                                                                                                                            <li><strong>Steckdose</strong> @if($red->has_socket) vorhanden, Entfernung {{ $red->socket_distance }} m @else nicht vorhanden @endif</li>
                                                                                                                        </ul>
                                                                                                                    </td>  
                                                                                                                    <td>
                                                                                                                        <a type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light" href="{{ url('radiator_config_delete/'.$red->id) }}"><i class="feather icon-trash"></i></a>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                @endforeach

                                                                                                            </tbody>
                                                                                                        </table> 

                                                                                                        <!-- Modal for adding/editing Heating Circuit --> 
                                                                                                    
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
                                                <div class="col-12"><hr></div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <div id="accordionWrapa10" role="tablist" aria-multiselectable="true">
                                                            <div class="cards collapse-icon accordion-icon-rotate"> 
                                                                <div class="card-content">
                                                                    <div class="card-body p-0"> 
                                                                        <div class="default-collapse collapse-bordered"> 
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading11" class="card-header" data-toggle="collapse" role="button" data-target="#accordion10" aria-expanded="true" aria-controls="accordion10">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">OBJEKTDATEN</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion10" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading11" class="collapse show" style="">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Baujahr </h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 textbox-container">
                                                                                                     <input type="text" class="form-control" 
                                                                                                        name="construction_year" id="construction_year" 
                                                                                                        value="{{ old('construction_year', $wp ? $wp->construction_year : $customer->construction_year) }}">&nbsp;
                                                                                                        <label
                                                                                                                    id="house_age_label"> </label> </span>
                                                                                                            <div class="indicator"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">beheizte Wohnfläche</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="living_space" value="{{ old('living_space', $wp ? $wp->living_space: 0)}}">
                                                                                                            <span> m²</span> </span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Nutzfläche</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('unusable_space', $wp ? $wp->unusable_space: 0)}}"
                                                                                                                name="unusable_space">
                                                                                                            <span> m²</span> </span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row ">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl Personen</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="number_people" id="number_people"  value="{{ old('number_people', $wp ? $wp->number_people: 0)}}">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl WE</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8 textbox-container">
                                                                                                            <input type="text" class="form-control textbox"
                                                                                                                name="wp_number_we" value="{{ old('wp_number_we', $wp ? $wp->wp_number_we: 0)}}">
                                                                                                            <div class="indicator"></div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-6">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <h4 class="bold">Anzahl Geschoß</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-8">
                                                                                                            <input type="text" class="form-control" value="{{ old('wp_number_stories', $wp ? $wp->wp_number_stories: 0)}}"
                                                                                                                name="wp_number_stories">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="form-group row">
                                                                                                            <div class="col-md-2">
                                                                                                                <h4 class="bold ">Fensterverglasung</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-10">
                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="glass1" id="1-fach"
                                                                                                                                     @if($wp && $wp->glass1=="on") checked @endif >
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="1-fach">1-fach</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="glass2" id="glass_2" @if($wp && $wp->glass2=="on") checked @endif>
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="glass_2">2-fach</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="checkbox"
                                                                                                                                    class="custom-control-input"    @if($wp && $wp->glass3=="on") checked @endif
                                                                                                                                    name="glass3" id="glass_3" >
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="glass_3">3-fach</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-12">
                                                                                                        <div class="form-group row">
                                                                                                            <div class="col-md-2">
                                                                                                                <h4 class="bold ">Fensterrahmen</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-10">
                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_alu"  @if($wp && $wp->window_margin=="Alu") checked @endif
                                                                                                                                    value="Alu">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_alu">Alu</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_kunststoff" @if($wp && $wp->window_margin=="Kunststoff") checked @endif
                                                                                                                                    value="Kunststoff">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_kunststoff">Kunststoff</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="window_margin"
                                                                                                                                    id="window_margin_holz" @if($wp && $wp->window_margin=="Holz") checked @endif
                                                                                                                                    value="Holz">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="window_margin_holz">Holz</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-6">
                                                                                                        <div class="form-group row ">
                                                                                                            <div class="col-md-4">
                                                                                                                <h4 class="bold ">Aussendämmung Stärke</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-8 flex_me">
                                                                                                                <input type="text" class="form-control"
                                                                                                                    name="insulation_thickness" value="{{ old('insulation_thickness', $wp ? $wp->insulation_thickness: 0)}}">
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                    </div>

                                                                                                    <div class="col-6">
                                                                                                        <div class="form-group row ">
                                                                                                            <div class="col-md-4">
                                                                                                                <h4 class="bold ">Mauerart</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-8 flex_me">
                                                                                                                <select name="wall_type" class="form-control" id="">
                                                                                                                    <option value="Mauerwerk" @if($wp && $wp->wall_type == "Mauerwerk") selected @endif >Mauerwerk</option>
                                                                                                                    <option value="Holz" @if($wp && $wp->wall_type == "Holz") selected @endif>Holz</option>
                                                                                                                    <option value="Massivbau" @if($wp && $wp->wall_type == "Massivbau") selected @endif>Massivbau</option>
                                                                                                                </select>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-6">
                                                                                                        <div class="form-group row ">
                                                                                                            <div class="col-md-4">
                                                                                                                <h4 class="bold ">Mauer-stärke</h4>
                                                                                                            </div>
                                                                                                            <div class="col-md-8 flex_me">
                                                                                                                <input type="text" class="form-control" value="{{ old('wall_thickness', $wp ? $wp->wall_thickness: 0)}}"
                                                                                                                    name="wall_thickness">&nbsp;<span>cm</span>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="col-12">
                                                                                                        <div class="form-group row">
                                                                                                            <div class="col-md-2">
                                                                                                                <h3 class="bold">Aufdachdämmung</h3>
                                                                                                            </div>
                                                                                                            <div class="col-md-10">
                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_insulation"
                                                                                                                                    id="wp_insulation_ja" @if($wp && $wp->wp_insulation=="ja") checked @endif
                                                                                                                                    value="ja">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_insulation_ja">ja</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_insulation"
                                                                                                                                    id="wp_insulation_nein"  @if($wp && $wp->wp_insulation=="nein") checked @endif
                                                                                                                                    value="nein">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_insulation_nein">nein</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1"
                                                                                                                        style="width:330px">
                                                                                                                        <div class="form-group row">
                                                                                                                            <div class="col-md-4">
                                                                                                                                <h4 class="bold">Stärke</h4>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-8">
                                                                                                                                <input type="text"
                                                                                                                                    class="form-control" value="{{ old('wp_insulation_strength', $wp ? $wp->wp_insulation_strength: 0)}}"
                                                                                                                                    name="wp_insulation_strength">
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-12">
                                                                                                        <div class="form-group row">
                                                                                                            <div class="col-md-2">
                                                                                                                <h3 class="bold">Zwischen sparrendämmung</h3>
                                                                                                            </div>
                                                                                                            <div class="col-md-10">
                                                                                                                <ul class="list-unstyled mb-0">
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_rafter"  @if($wp && $wp->wp_rafter=="ja") checked @endif
                                                                                                                                    id="wp_rafter_ja" value="ja">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_rafter_ja">ja</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1">
                                                                                                                        <fieldset>
                                                                                                                            <div
                                                                                                                                class="custom-control custom-radio">
                                                                                                                                <input type="radio"
                                                                                                                                    class="custom-control-input"
                                                                                                                                    name="wp_rafter" @if($wp && $wp->wp_rafter=="nein") checked @endif
                                                                                                                                    id="wp_rafter_nein"
                                                                                                                                    value="nein">
                                                                                                                                <label class="custom-control-label"
                                                                                                                                    for="wp_rafter_nein">nein</label>
                                                                                                                            </div>
                                                                                                                        </fieldset>
                                                                                                                    </li>
                                                                                                                    <li class="d-inline-block mr-1"
                                                                                                                        style="width:330px">
                                                                                                                        <div class="form-group row">
                                                                                                                            <div class="col-md-4">
                                                                                                                                <h4 class="bold">Stärke</h4>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-8">
                                                                                                                                <input type="text"
                                                                                                                                    class="form-control" value="{{ old('wp_rafter_strength', $wp ? $wp->wp_rafter_strength: 0)}}"
                                                                                                                                    name="wp_rafter_strength">
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </li>
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div> 

                                                                                                        <div class="col-6">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-4">
                                                                                                                    <h4 class="bold">Anzahl Bäder</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-8">
                                                                                                                    <input type="text" class="form-control" value="{{ old('wp_bathrooms', $wp ? $wp->wp_bathrooms: 0)}}"
                                                                                                                        name="wp_bathrooms">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        

                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Badewanne</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_bathtub"
                                                                                                                                        id="wp_buthtub_no" @if($wp && $wp->wp_bathtub=="nein") checked @endif
                                                                                                                                        value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_buthtub_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_bathtub" @if($wp && $wp->wp_bathtub=="ja") checked @endif
                                                                                                                                        id="wp_buthtub_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_buthtub_yes">Ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1 "
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row ">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Anzahl</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <input type="text"
                                                                                                                                        class="form-control" value="{{ old('wp_bathtub_count', $wp ? $wp->wp_bathtub_count: 0)}}"
                                                                                                                                        name="wp_bathtub_count">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1 "
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row ">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Abmessung</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <input type="text" value="{{ old('wp_bathtub_measure', $wp ? $wp->wp_bathtub_measure: 0)}}"
                                                                                                                                        class="form-control"
                                                                                                                                        name="wp_bathtub_measure">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Schwimmbad</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_swimming_pool"
                                                                                                                                        id="wp_swimming_pool_no" @if($wp && $wp->wp_swimming_pool=="nein") checked @endif
                                                                                                                                        value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_swimming_pool_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="wp_swimming_pool" @if($wp && $wp->wp_swimming_pool=="ja") checked @endif
                                                                                                                                        id="wp_swimming_pool_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="wp_swimming_pool_yes">ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1 "
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row ">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Anzahl</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8">
                                                                                                                                    <input type="text" value="{{ old('wp_swimming_pool_count', $wp ? $wp->wp_swimming_pool_count: 0)}}"
                                                                                                                                        class="form-control"
                                                                                                                                        name="wp_swimming_pool_count">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div> 
                                                                                                        <div class="col-12">
                                                                                                            <hr>
                                                                                                        </div> 
                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Solarthermie vorhanden</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="solor" id="solar_no" @if($wp && $wp->solor=="nein") checked @endif
                                                                                                                                        checked value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="solar_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="solor" id="solor_yes" @if($wp && $wp->solor=="ja") checked @endif
                                                                                                                                        value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="solor_yes">ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1"
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Anzahl der
                                                                                                                                        Kollektoren</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8 flex_me">
                                                                                                                                    <input type="text" 
                                                                                                                                        class="form-control"  value="{{ old('number_collector', $wp ? $wp->number_collector: 0)}}"
                                                                                                                                        name="number_collector">
                                                                                                                                    &nbsp;<span>Stk</span>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Kamin vorhanden</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="chimney" id="chimney_no" @if($wp && $wp->chimney=="nein") checked @endif
                                                                                                                                        checked value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="chimney_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="chimney" id="chimney_yes" @if($wp && $wp->chimney=="ja") checked @endif
                                                                                                                                        value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="chimney_yes">ja</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>

                                                                                                                        <li class="d-inline-blocks mr-1"
                                                                                                                            style="width:330px">
                                                                                                                            <div class="form-group row">
                                                                                                                                <div class="col-md-4">
                                                                                                                                    <h4 class="bold">Verbrauch</h4>
                                                                                                                                </div>
                                                                                                                                <div class="col-md-8 flex_me">
                                                                                                                                    <input type="text"
                                                                                                                                        class="form-control" value="{{ old('chimney_usage', $wp ? $wp->chimney_usage: 0)}}"
                                                                                                                                        name="chimney_usage">
                                                                                                                                    &nbsp;<span> Holz/ m3 pro
                                                                                                                                        Jahr</span>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </li>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-12">
                                                                                                            <div class="form-group row">
                                                                                                                <div class="col-md-2">
                                                                                                                    <h4 class="bold ">Heizlastberechnung vorhanden</h4>
                                                                                                                </div>
                                                                                                                <div class="col-md-10">
                                                                                                                    <ul class="list-unstyled mb-0">
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="hlb_calc" id="hlb_calc_no" @if($wp && $wp->hlb_calc=="nein") checked @endif
                                                                                                                                          value="nein">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="hlb_calc_no">nein</label>
                                                                                                                                </div>
                                                                                                                            </fieldset>
                                                                                                                        </li>
                                                                                                                        <li class="d-inline-block mr-1">
                                                                                                                            <fieldset>
                                                                                                                                <div
                                                                                                                                    class="custom-control custom-radio">
                                                                                                                                    <input type="radio"
                                                                                                                                        class="custom-control-input"
                                                                                                                                        name="hlb_calc" @if($wp && $wp->hlb_calc=="ja") checked @endif
                                                                                                                                        id="hlb_calc_yes" value="ja">
                                                                                                                                    <label class="custom-control-label"
                                                                                                                                        for="hlb_calc_yes">ja</label>
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

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading2" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion2" aria-expanded="false" aria-controls="accordion2">
                                                                                    <span class="lead collapse-title">
                                                                                            <h2 class="bold section_title">Energiekosten, Verbrauch, Daten</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion2" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading2" class="collapse" style="">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr> 
                                                                                                                <th>Erstes Jahr</th>
                                                                                                                <th>Zweites Jahr</th>
                                                                                                                <th>Drittes Jahr</th>
                                                                                                                <th>Energieverbrauchseinheit</th>
                                                                                                                <th>Gesamt</th>
                                                                                                                <th>AVG</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <tr> 
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Erstes Jahr"  value="{{ old('energy_first_year_consumption', $wp ? $wp->energy_first_year_consumption: 0)}}"
                                                                                                                            name="energy_first_year_consumption" id="energy_first_year_consumption">
                                                                                                                </td>
                                                                                                                <td><input type="text" class="form-control" placeholder="Zweites Jahr" value="{{ old('energy_second_year_consumption', $wp ? $wp->energy_second_year_consumption: 0)}}"
                                                                                                                        name="energy_second_year_consumption" id="energy_second_year_consumption">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Drittes Jahr" value="{{ old('energy_third_year_consumption', $wp ? $wp->energy_third_year_consumption: 0)}}"
                                                                                                                        name="energy_third_year_consumption"  id="energy_third_year_consumption">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select name="energy_consumption_type" id="energy_consumption_type" class="form-control">
                                                                                                                        <option  >Energieverbrauchseinheit</option>
                                                                                                                        <option value="kWh">kWh</option>
                                                                                                                        <option value="m³">m³</option>
                                                                                                                        <option value="Liter">Liter</option>  
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control"    name="energy_total_year_consumption"  id="energy_total_year_consumption" placeholder="Gesamt" aria-describedby="energy_consumption_type_lable"  value="{{ old('energy_total_year_consumption', $wp ? $wp->energy_total_year_consumption: 0)}}">
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group"> 
                                                                                                                            <input type="text" class="form-control"  name="energy_avg_year_consumption"  id="energy_avg_year_consumption" placeholder="Durchschnittliche" aria-describedby="energy_consumption_type_lable" value="{{ old('energy_avg_year_consumption', $wp ? $wp->energy_avg_year_consumption: 0)}}">
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>  
                                                                                                                
                                                                                                                </td>
                                                                                                            </tr> 

                                                                                                            <tr> 
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Erstes Jahr Kosten" value="{{ old('energy_first_year_cost', $wp ? $wp->energy_first_year_cost: 0)}}"
                                                                                                                            name="energy_first_year_cost" id="energy_first_year_cost">
                                                                                                                </td>
                                                                                                                <td><input type="text" class="form-control" placeholder="Zweites Jahr Kosten"  value="{{ old('energy_second_year_cost', $wp ? $wp->energy_second_year_cost: 0)}}"
                                                                                                                        name="energy_second_year_cost" id="energy_second_year_cost" > 
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Drittes Jahr Kosten"  value="{{ old('energy_third_year_cost', $wp ? $wp->energy_third_year_cost: 0)}}"
                                                                                                                        name="energy_third_year_cost" id="energy_third_year_cost">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                <input type="text" value="Euro" class="form-control" readonly>  
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control mb-1" placeholder="Gesamt Kosten" id="energy_total_year_cost"  value="{{ old('energy_total_year_cost', $wp ? $wp->energy_total_year_cost: 0)}}"
                                                                                                                                    name="energy_total_year_cost"  >
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text"  >€</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                    <fieldset>
                                                                                                                        <div class="input-group mb-1"> 
                                                                                                                            <input type="text" class="form-control" placeholder="Durchschnittliche" id="energy_avg_year_cost"   value="{{ old('energy_avg_year_cost', $wp ? $wp->energy_avg_year_cost: 0)}}"
                                                                                                                        name="energy_avg_year_cost" >
                                                                                                                            <div class="input-group-prepend">
                                                                                                                                <span class="input-group-text">€</span>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </fieldset>

                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            
                                                                                                            
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading4" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion4" aria-expanded="false" aria-controls="accordion4">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">AKTUELLE HEIZUNG</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion4" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading4" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizungart</h4>  
                                                                                                    <select name="heatpump" id="heatpump" class="form-control"> 
                                                                                                            <option value="WP" @if(isset($wp) && $wp->heatpump == "Wärmepumpe" || isset($customer) && $customer->heating_system_type == "Wärmepumpe") selected @endif>Wärmepumpe</option>
                                                                                                            <option value="Gas" @if(isset($wp) && $wp->heatpump == "Gas" || isset($customer) && $customer->heating_system_type == "Gas") selected @endif>GAS</option>
                                                                                                            <option value="oil" @if(isset($wp) && $wp->heatpump == "oil" || isset($customer) && $customer->heating_system_type == "oil") selected @endif>Öl</option>
                                                                                                            <option value="Pellets" @if(isset($wp) && $wp->heatpump == "Pellets" || isset($customer) && $customer->heating_system_type == "Pellets") selected @endif>Pellets</option>
                                                                                                            <option value="Nachtspeicher" @if(isset($wp) && $wp->heatpump == "Nachtspeicher" || isset($customer) && $customer->heating_system_type == "Nachtspeicher") selected @endif>Nachtspeicher</option>
                                                                                                        </select>


                                                                                                </div>

                                                                                                <div class="col-md-3 "  >  
                                                                                                    <h4 class="bold ">Aufstellort</h4>  
                                                                                                    <select name="exhibition_location" id="exhibition_location" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="KG" @if($wp && $wp->exhibition_location == "KG") selected @endif>KG</option>
                                                                                                        <option value="EG" @if($wp && $wp->exhibition_location == "EG") selected @endif>EG</option>
                                                                                                        <option value="OG" @if($wp && $wp->exhibition_location == "OG") selected @endif> OG</option>
                                                                                                        <option value="DG" @if($wp && $wp->exhibition_location == "DG") selected @endif> DG</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-5 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control">
                                                                                                        {{ old('exhibation_location_note', $wp ? $wp->exhibation_location_note : "") }} 
                                                                                                    </textarea>
                                                                                        
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Alter der Heizung <label
                                                                                                                    id="heating_age_label"></label></h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control"
                                                                                                                name="heating_manufacture_year" value="{{  old('heating_manufacture_year', $wp ? $wp->heating_manufacture_year : $customer->heating_manufacture_year) }}"
                                                                                                                id="heating_manufacture_year"> &nbsp;
                                                                                                            <span id="heating_lables"
                                                                                                                style="align-content: center;">
                                                                                                                <span> Jahr(e)</span> </span>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <!-- Enable only for Oil and Gas  -->
                                                                                                <div class="col-3 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Heiztechnik</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select class="form-control" id="heating_type"
                                                                                                                name="heating_type"> 
                                                                                                                <option value="Brennwert" @if($wp && $wp->heating_type == "Brennwert") selected @endif>Brennwert</option>
                                                                                                                <option value="Heizwert" @if($wp && $wp->heating_type == "Heizwert") selected @endif>Heizwert</option>
                                                                                                                 
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Leistung der Anlage</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('system_performance', $wp ? $wp->system_performance : $customer->system_performance ) }}"
                                                                                                                name="system_performance">&nbsp;<span>kWh</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Hersteller</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control"  value="{{ old('heating_company', $wp ? $wp->heating_company : '') }}"
                                                                                                                name="heating_company"> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Typbezeichnung</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <input type="text" class="form-control" value="{{ old('type_designation', $wp ? $wp->type_designation : '') }}"
                                                                                                                name="type_designation"> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Warmwasseraufbereitung</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="hot_water_preparation" id="" class="form-control">
                                                                                                                <option value="">Bitte wählen</option>
                                                                                                                <option value="Heizung"  @if($wp && $wp->hot_water_preparation == "Heizung") selected @endif>Heizung</option>
                                                                                                                <option value="Durchlauferhitzer"  @if($wp && $wp->hot_water_preparation == "Durchlauferhitzer") selected @endif>Durchlauferhitzer</option>
                                                                                                                <option value="Sonstiges"  @if($wp && $wp->hot_water_preparation == "Sonstiges") selected @endif>Sonstiges</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Warmwasserverbrauch pro Person</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="number_hotWaterConsumptionPerPerson" id="number_hotWaterConsumptionPerPerson" class="form-control select2" >
                                                                                                                <option value="25" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "25") selected @endif>25 Liter (Niedrig)</option>
                                                                                                                <option value="50" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "50") selected @endif>50 Liter (Normal)</option>
                                                                                                                <option value="80" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "80") selected @endif>80 Liter (Hoch)</option>
                                                                                                                <option value="120" @if($wp && $wp->number_hotWaterConsumptionPerPerson == "120") selected @endif>120 Liter (Luxus)</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>


                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Heizsystem</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="general_heating_system" class="form-control" id="general_heating_system">     
                                                                                                                <option value="underfloor heating" @if($wp && $wp->general_heating_system == "underfloor heating") selected @endif>Fußbodenheizung</option>    
                                                                                                                <option value="radiator" @if($wp && $wp->general_heating_system == "radiator") selected @endif>Heizkörper</option> 
                                                                                                                <option value="underfloor heating and radiator"  @if($wp && $wp->general_heating_system == "underfloor heating and radiator") selected @endif>Fußbodenheizung + Heizkörper</option> 
                                                                                                                <option value="none" @if($wp && $wp->general_heating_system == "none") selected @endif>Keine</option> 
                                                                                                            </select> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-4 mt-1">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12 ">
                                                                                                            <h4 class="bold">Rohrsystem</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <select name="pipe_system" class="form-control" id="pipe_system">
                                                                                                                <option selected>Wählen...</option>     
                                                                                                                <option value="one" @if($wp && $wp->pipe_system == "one") selected @endif>Ein-Rohr-System</option>    
                                                                                                                <option value="two" @if($wp && $wp->pipe_system == "two") selected @endif>Zwei-Rohr-System</option> 
                                                                                                            </select> 
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                    
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                                <div class="cards collapse-header">
                                                                                <div id="heading5" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion5" aria-expanded="false" aria-controls="accordion5">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Fußbodenheizung & elektrischen Anschluß: </h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion5" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading5" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizkreisverteiler</h4>  
                                                                                                    <select name="heating_circuit_distributor" id="heating_circuit_distributor" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->heating_circuit_distributor == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->heating_circuit_distributor == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Stellantriebe</h4>  
                                                                                                    <select name="actuators" id="actuators" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja / 230 Volt" @if($wp && $wp->actuators == "Ja / 230 Volt") selected @endif>Ja / 230 Volt</option>
                                                                                                        <option value="Ja / 24 Volt" @if($wp && $wp->actuators == "Ja / 24 Volt") selected @endif>Ja / 24 Volt</option>
                                                                                                        <option value="Nein / 230 Volt" @if($wp && $wp->actuators == "Nein / 230 Volt") selected @endif> Nein / 230 Volt</option>
                                                                                                        <option value="Nein / 24 Volt" @if($wp && $wp->actuators == "Nein / 24 Volt") selected @endif> Nein / 24 Volt</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4"  >  
                                                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                                                    <select name="suitable_cooling_system" id="suitable_cooling_system" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja" @if($wp && $wp->suitable_cooling_system == "Ja") selected @endif>Ja</option>
                                                                                                        <option value="Nein" @if($wp && $wp->suitable_cooling_system == "Nein") selected @endif> Nein</option>
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-12 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control">
                                                                                                        {{ old('exhibation_location_note', $wp ? $wp->exhibation_location_note : '') }}
                                                                                                    </textarea>
                                                                                        
                                                                                                </div> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading7" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion7" aria-expanded="false" aria-controls="accordion7">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title ">Hydraulichen Eignungsprüfung Heizkörper</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion7" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading7" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="col-md-4 " >  
                                                                                                    <h4 class="bold ">Heizkörper</h4>  
                                                                                                    <select name="radiator" id="radiator" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes"  @if($wp && $wp->radiator == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->radiator == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Thermostate</h4>  
                                                                                                    <select name="thermostats" id="thermostats" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->thermostats == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no" @if($wp && $wp->thermostats == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                    <div class="col-md-4 "  >  
                                                                                                    <h4 class="bold ">Thermostatventile</h4>  
                                                                                                    <select name="thermostatic_valves" id="thermostatic_valves" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="yes" @if($wp && $wp->thermostatic_valves == "yes") selected @endif>Ja</option>
                                                                                                        <option value="no"  @if($wp && $wp->thermostatic_valves == "no") selected @endif>Nein</option> 
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-4"  >  
                                                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                                                    <select name="radiator_cooling_system" id="radiator_cooling_system" class="form-control">
                                                                                                        <option value="">Bitte wählen</option>
                                                                                                        <option value="Ja" @if($wp && $wp->radiator_cooling_system == "Ja") selected @endif>Ja</option>
                                                                                                        <option value="Nein" @if($wp && $wp->radiator_cooling_system == "Nein") selected @endif> Nein</option>
                                                                                                    </select>  
                                                                                                </div>

                                                                                                <div class="col-md-12 "  >  
                                                                                                    <h4 class="bold ">Notiz</h4>  
                                                                                                    <textarea name="radiator_note" id="radiator_note" cols="10" rows="2" class="form-control">
                                                                                                            {{ old('radiator_note', $wp ? $wp->radiator_note : '') }}
                                                                                                    </textarea>
                                                                                        
                                                                                                </div> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="cards collapse-header">
                                                                                <div id="heading8" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#accordion8" aria-expanded="false" aria-controls="accordion8">
                                                                                    <span class="lead collapse-title">
                                                                                            <h2 class="bold section_title">Welche Leitungen sind verlegt?</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion8" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading8" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                            
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>#</th>
                                                                                                                <th>Art</th>
                                                                                                                <th>Dimension</th>
                                                                                                                <th>Hersteller</th>
                                                                                                                <th>Typbezeichnung</th>
                                                                                                                <th>Notiz</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <!-- Heating -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Heizung" name="cable[0][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[0][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($heating && $heating->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($heating && $heating->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[0][dimension]" value="{{ old('dimension', $heating->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[0][company]" value="{{ old('company', $heating->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[0][designation]" value="{{ old('designation', $heating->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[0][note]" value="{{ old('note', $heating->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Cold Water -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Kalt-Wasser" name="cable[1][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[1][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($cold_water && $cold_water->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($cold_water && $cold_water->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[1][dimension]" value="{{ old('dimension', $cold_water->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[1][company]" value="{{ old('company', $cold_water->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[1][designation]" value="{{ old('designation', $cold_water->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[1][note]" value="{{ old('note', $cold_water->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Warm Water -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Warm-Wasser" name="cable[2][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[2][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($warm_water && $warm_water->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($warm_water && $warm_water->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[2][dimension]" value="{{ old('dimension', $warm_water->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[2][company]" value="{{ old('company', $warm_water->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[2][designation]" value="{{ old('designation', $warm_water->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[2][note]" value="{{ old('note', $warm_water->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>

                                                                                                            <!-- Circulation -->
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <input type="text" value="Zirkulation" name="cable[3][system]" class="form-control" readonly>
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="cable[3][type]" class="form-control">
                                                                                                                        <option value="Kupfer" @if($circulation && $circulation->type == "Kupfer") selected @endif>Kupfer</option>
                                                                                                                        <option value="Kunststoff" @if($circulation && $circulation->type == "Kunststoff") selected @endif>Kunststoff</option>
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="cable[3][dimension]" value="{{ old('dimension', $circulation->dimension ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name="cable[3][company]" value="{{ old('company', $circulation->company ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name="cable[3][designation]" value="{{ old('designation', $circulation->designation ?? '') }}">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input type="text" class="form-control" placeholder="Notiz" name="cable[3][note]" value="{{ old('note', $circulation->note ?? '') }}">
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>

                                                                                  
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading9" class="card-header" data-toggle="collapse" role="button" data-target="#accordion9" aria-expanded="false" aria-controls="accordion9">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Einbringmaße Zuwegung Heizraum</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion9" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading9" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <form id="room_dimension_form">
                                                                                                        @csrf
                                                                                                        <table class="table" id="room_dimension_table">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th>#</th>
                                                                                                                    <th>Dimensionstyp</th>
                                                                                                                    <th>Breite</th>
                                                                                                                    <th>Höhe</th>
                                                                                                                    <th>Deckenhöhe</th>
                                                                                                                    <th>Treppe Form</th>
                                                                                                                    <th>Treppe Breite</th>
                                                                                                                    <th>Geschoss</th>
                                                                                                                    <th>Aktion</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody id="room_dimension_tbody">
                                                                                                                <tr id="room_dimension_row_0">
                                                                                                                    <th scope="row">
                                                                                                                        <input type="text" name="room[0][room_number]" class="form-control" value="1" readonly>
                                                                                                                    </th>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][dimension_type]" class="form-control dimension_type">
                                                                                                                            <option></option>
                                                                                                                            <option value="Tür">Tür</option>
                                                                                                                            <option value="Wand">Wand</option>
                                                                                                                            <option value="Deche">Deche</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control" placeholder="Breite" name="room[0][width]"> 
                                                                                                                        <input type="hidden" class="form-control" name="customer_id" value="{{ $customer->id }}"> 
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control" placeholder="Höhe" name="room[0][height]"> 
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control ceiling_height" placeholder="Deckenhöhe" name="room[0][ceiling_height]">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][stair_form]" class="form-control stair_form">
                                                                                                                            <option></option>
                                                                                                                            <option value="L-Form">L-Form</option>
                                                                                                                            <option value="U-Form">U-Form</option>
                                                                                                                            <option value="Wendel">Wendel</option>
                                                                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input type="text" class="form-control stair_width" placeholder="Treppe Breite" name="room[0][stair_width]">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select name="room[0][room_story]" class="form-control">
                                                                                                                            <option></option>
                                                                                                                            <option value="KG">KG</option>
                                                                                                                            <option value="EG">EG</option>
                                                                                                                            <option value="OG">OG</option>
                                                                                                                            <option value="DG">DG</option>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <button type="button" class="btn btn-icon btn-warning add_dimension">
                                                                                                                            <i class="feather icon-plus"></i>
                                                                                                                        </button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                        <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light" id="save_dimension">Speichern</button>
                                                                                                    </form>
                                                                                                </div>

                                                                                                <div class="table-responsive">  
                                                                                                    <table class="table" id="room_dimension_data">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>#</th>
                                                                                                                <th>Dimensionstyp</th>
                                                                                                                <th>Breite</th>
                                                                                                                <th>Höhe</th>
                                                                                                                <th>Deckenhöhe</th>
                                                                                                                <th>Treppe Form</th>
                                                                                                                <th>Treppe Breite</th>
                                                                                                                <th>Geschoss</th>
                                                                                                                <th>Aktion</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody id="room_dimension_data">
                                                                                                            <!-- Rows will be dynamically inserted here -->
                                                                                                        </tbody>
                                                                                                    </table>

                                                                                                </div>

                                                                                                <!-- Room Dimension Modal for Editing -->
                                                                                                <div class="modal fade" id="editRoomDimensionModal" tabindex="-1" role="dialog" aria-labelledby="editRoomDimensionModalLabel" aria-hidden="true">
                                                                                                    <div class="modal-dialog" role="document">
                                                                                                        <div class="modal-content">
                                                                                                            <div class="modal-header">
                                                                                                                <h5 class="modal-title" id="editRoomDimensionModalLabel">Raumdetails bearbeiten</h5>
                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                </button>
                                                                                                            </div>
                                                                                                            <div class="modal-body">
                                                                                                                <form id="editRoomDimensionForm">
                                                                                                                    <!-- Hidden field to store room ID -->
                                                                                                                    <input type="hidden" id="edit_room_id">
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Türmaße#</label>
                                                                                                                        <input type="text" id="edit_room_number" class="form-control" name="room_number" readonly>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Dimensionstyp</label>
                                                                                                                        <select name="dimension_type" id="edit_dimension_type" class="form-control">
                                                                                                                            <option value="Tür">Tür</option>
                                                                                                                            <option value="Wand">Wand</option>
                                                                                                                            <option value="Deche">Deche</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Breite</label>
                                                                                                                        <input type="text" id="edit_width" class="form-control" name="width">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Höhe</label>
                                                                                                                        <input type="text" id="edit_height" class="form-control" name="height">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Deckenhöhe</label>
                                                                                                                        <input type="text" id="edit_ceiling_height" class="form-control" name="ceiling_height">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Treppe Form</label>
                                                                                                                        <select id="edit_stair_form" class="form-control" name="stair_form">
                                                                                                                            <option value="L-Form">L-Form</option>
                                                                                                                            <option value="U-Form">U-Form</option>
                                                                                                                            <option value="Wendel">Wendel</option>
                                                                                                                            <option value="Gradeluäfig">Gradeluäfig</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Treppe Breite</label>
                                                                                                                        <input type="text" id="edit_stair_width" class="form-control" name="stair_width">
                                                                                                                    </div>
                                                                                                                    <div class="form-group">
                                                                                                                        <label>Geschoss</label>
                                                                                                                        <select id="edit_room_story" class="form-control" name="room_story">
                                                                                                                            <option value="KG">KG</option>
                                                                                                                            <option value="EG">EG</option>
                                                                                                                            <option value="OG">OG</option>
                                                                                                                            <option value="DG">DG</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                </form>
                                                                                                            </div>
                                                                                                            <div class="modal-footer">
                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                                                <button type="button" id="updateRoomDimension" class="btn btn-primary">Speichern</button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading13" class="card-header" data-toggle="collapse" role="button" data-target="#accordion13" aria-expanded="false" aria-controls="accordion13">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Zustand Zählerschrank</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion13" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading13" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">  
                                                                                                <div class="col-md-4">
                                                                                                    <div class="form-group row">
                                                                                                        <label for=""><h4>Zählerschrank</h4></label>
                                                                                                        <select name="meter_cabinet" id="meter_cabinet" class="form-control" style="width:100% !important;">
                                                                                                            <option value="ok" @if($meter_cabinet && $meter_cabinet->meter_cabinet == "ok") selected @endif>OK</option>
                                                                                                            <option value="upgrade" @if($meter_cabinet && $meter_cabinet->meter_cabinet == "upgrade") selected @endif>ertüchtigen</option>
                                                                                                            <option value="new" @if($meter_cabinet &&  $meter_cabinet->meter_cabinet == "new") selected @endif>neu</option> 
                                                                                                        </select> 
                                                                                                    </div>

                                                                                                    <div class="form-group row" id="cabinet_size_div" @if(!$meter_cabinet || !$meter_cabinet->cabinet_size) style="display:none;" @endif>
                                                                                                        <label for=""><h4>Größe</h4></label> 
                                                                                                        <select name="cabinet_size" id="cabinet_size" class="form-control" style="width:100% !important;">
                                                                                                            <option value="550" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "550") selected @endif>550</option>
                                                                                                            <option value="800" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "800") selected @endif>800</option>
                                                                                                            <option value="1100" @if($meter_cabinet &&  $meter_cabinet->cabinet_size == "1100") selected @endif>1100</option> 
                                                                                                        </select> 
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-md-4">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <label>Hersteller</label> 
                                                                                                        </div>
                                                                                                        <div class="col-md-8 flex_me">
                                                                                                            <fieldset>
                                                                                                                <select name="meter_cabinet_company" id="meter_cabinet_company" class="form-control" style="width:100% !important;">
                                                                                                                    @foreach ($electro as $elec)
                                                                                                                        <option value="{{ $elec->id }}" @if($meter_cabinet && $meter_cabinet->meter_cabinet_company == $elec->id) selected @endif> 
                                                                                                                            {{ $elec->name }}
                                                                                                                        </option>
                                                                                                                    @endforeach
                                                                                                                </select>
                                                                                                            </fieldset>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="col-md-4" id="cabinet_settings_div" @if(!$meter_cabinet || !$meter_cabinet->wp_meter_adapter_plate) style="display:none;" @endif>
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-12">
                                                                                                            <h4 class="bold">Einzubauende Komponenten</h4>
                                                                                                        </div>
                                                                                                        <div class="col-md-12 flex_me">
                                                                                                            <ul class="mb-0" style="display:flex; flex-wrap: wrap; flex-direction: column;">
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_all" id="wp_all">
                                                                                                                            <label class="custom-control-label" for="wp_all">Alles</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_meter_adapter_plate" id="wp_meter_adapter_plate" @if($meter_cabinet && $meter_cabinet->wp_meter_adapter_plate) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_meter_adapter_plate">Zähleradapterplatte</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_ac_surge_protection" id="wp_ac_surge_protection" @if($meter_cabinet && $meter_cabinet->wp_ac_surge_protection) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_ac_surge_protection" style="width: 232px;">AC Überspannungsschutz</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_ac_switch" id="wp_ac_switch" @if($meter_cabinet && $meter_cabinet->wp_ac_switch) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_ac_switch">SLS Schalter</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_apz_field" id="wp_apz_field" @if($meter_cabinet && $meter_cabinet->wp_apz_field) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_apz_field">APZ Feld</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_disconnect_relay" id="wp_disconnect_relay" @if($meter_cabinet && $meter_cabinet->wp_disconnect_relay) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_disconnect_relay">Trenn-Relais</label>
                                                                                                                        </div>
                                                                                                                    </fieldset>
                                                                                                                </li>
                                                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                                                    <fieldset>
                                                                                                                        <div class="custom-control custom-radio">
                                                                                                                            <input type="checkbox" class="custom-control-input" name="wp_equipotential_bonding" id="wp_equipotential_bonding_busbar" @if($meter_cabinet && $meter_cabinet->wp_equipotential_bonding) checked @endif>
                                                                                                                            <label class="custom-control-label" for="wp_equipotential_bonding_busbar">Potentialausgleichsschiene</label>
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

                                                                            <div class="cards collapse-header">
                                                                                <div id="heading40" class="card-header" data-toggle="collapse" role="button" data-target="#accordion40" aria-expanded="false" aria-controls="accordion40">
                                                                                    <span class="lead collapse-title">
                                                                                        <h2 class="bold section_title">Lüftung</h2>
                                                                                    </span>
                                                                                </div>
                                                                                <div id="accordion40" role="tabpanel" data-parent="#accordionWrapa10" aria-labelledby="heading40" class="collapse" aria-expanded="false">
                                                                                    <div class="card-content">
                                                                                        <div class="card-body">
                                                                                            <div class="row">
                                                                                                <div class="table-responsive">
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>Lüftung</th>
                                                                                                                <th>Lüftungsystem</th>
                                                                                                                <th>Wärmerückgewinnung</th>
                                                                                                                <th>Hersteller</th>
                                                                                                                <th>Typ</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <tr>
                                                                                                                <th scope="row">
                                                                                                                    <select name="ventilation" id="ventilation" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Ja" @if($wp && $wp->ventilation == "Ja" ) selected @endif>Ja</option>
                                                                                                                        <option value="nein"@if($wp && $wp->ventilation == "nein" ) selected @endif>Nein</option> 
                                                                                                                        <option value="geplant"@if($wp && $wp->ventilation == "geplant" ) selected @endif>Geplant</option> 
                                                                                                                    </select>  
                                                                                                                </th>
                                                                                                                <td>
                                                                                                                    <select name="ventilation_system" id="ventilation_system" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Zentral"  @if($wp && $wp->ventilation_system == "Zentral" ) selected @endif>Zentral</option>
                                                                                                                        <option value="Dezentral" @if($wp && $wp->ventilation_system == "Dezentral" ) selected @endif>Dezentral</option>  
                                                                                                                    </select>  
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select name="heat_recovery" id="heat_recovery" class="form-control">
                                                                                                                        <option value="">Bitte wählen</option>
                                                                                                                        <option value="Ja" @if($wp && $wp->heat_recovery == "Ja" ) selected @endif>Ja</option>
                                                                                                                        <option value="Nein"  @if($wp && $wp->heat_recovery == "Nein" ) selected @endif>Nein</option>  
                                                                                                                    </select>  
                                                                                                                </td> 
                                                                                                                <td>
                                                                                                                    <input type="text" name="ventilation_company" class="form-control" placeholder="Hersteller" value="{{ old('ventilation_company', isset($wp) ? $wp->ventilation_company : '') }}"> 
                                                                                                                </td>

                                                                                                                <td>
                                                                                                                    <input type="text" name="ventilation_type" class="form-control" placeholder="Typ" value="{{ old('ventilation_type', isset($wp) ? $wp->ventilation_type : '') }}">
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                    
                                                                                                            
                                                                                                        </tbody>
                                                                                                    </table>
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
                                    </section> 
                                    <section class="col-md-12" id="section_wp_4">
                                        <div class="cards">
                                            <div class="card-body"
                                                style="display: flex !important;flex-wrap: wrap;">   
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                                <section class="col-md-12 dynamic-section" id="section_4">
                                                    <div class="cards">
                                                        <div class="card-body"
                                                            style="display: flex !important; flex-wrap: wrap;">
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-12">
                                                                        <h4 class="bold">Fotos/Unterlagen des
                                                                            Kunden
                                                                            erforderlich</h4>
                                                                    </div>

                                                                    <div class="md-12">
                                                                        <ul>
                                                                            <li>Architekten-Pläne des Hauses
                                                                                (Etagen +
                                                                                Schnitte) wenn vorhanden</li>
                                                                            <li>Fotos der gesamten Dachflächen
                                                                                (so dass
                                                                                diese vollständig sichtbar sind)
                                                                            </li>
                                                                            <li>Foto des geöffneten
                                                                                Zählerschranks</li>
                                                                            <li>Fotos der Typbezeichnung des
                                                                                Zählerschranks (Aufkleber in der
                                                                                Ecke
                                                                                der Tür)
                                                                            </li>
                                                                            <li>Heizlastberechnung</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </section>
                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-icon btn-icon  btn-light mr-1 mb-1 waves-effect waves-light float-right ">
                                                        <i class="feather icon-save"></i> Daten Ubernahnen
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </section>
                                </div>
                            </form>
                        </article>
                    </div>
                    <!-- {{-- WP Checklist: End --}} -->

                    <!-- {{-- Wallbox Checklist: Start --}} -->
                    <div class="col-md-8 col-12" id="wallbox" style="display: none !important;">
                        <article class="col-md-12 col-sm-12 col-12  ">
                            <div class="container"
                                style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; box-shadow: 0px 0px 10px 2px #a2a2a2;">
                                <div class="col-md-12" id="section_1">
                                    <div class="cards">
                                        <div class="card-body d-flex">
                                            <div class="col-md-2 image">
                                                <img src="{{ asset('images/articles/wallbox.png') }}"
                                                    alt="alternative" style="width: 128px;">
                                            </div>
                                            <div class="col-md-10 contents">
                                                <h2 class="title" style="color: #74b2d3">WALLBOX</h2>
                                                <div class="form-group row">
                                                    <div class="col-md-12 mb-2 mt-2">
                                                        <span>Intention</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="wallbox_intention"
                                                                            id="wallbox_intention_interest"
                                                                            value="Interesse">
                                                                        <label class="custom-control-label"
                                                                            for="wallbox_intention_interest">Interesse</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="wallbox_intention"
                                                                            id="wallbox_intention_available"
                                                                            value="vorhanden">
                                                                        <label class="custom-control-label"
                                                                            for="wallbox_intention_available">vorhanden</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="wallbox_intention"
                                                                            id="wallbox_intention_extension"
                                                                            value="Erweiterung">
                                                                        <label class="custom-control-label"
                                                                            for="wallbox_intention_extension">Erweiterung</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="intention"
                                                                            id="intention_spater"
                                                                            value="später">
                                                                        <label class="custom-control-label"
                                                                            for="intention_spater">später</label>
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
                                <div class="col-12">
                                    <hr>
                                </div>
                                <section class="col-md-12" id="section_2">
                                    <div class="cards">
                                        <div class="card-body"
                                            style="display: flex !important;flex-wrap: wrap;">
                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">E-Auto vorhanden</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">Firmenfahrzeug</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-2">
                                                        <h4 class="bold ">Anzahl Wallboxen</h4>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <ul class="list-unstyled mb-0">

                                                            <li class="d-inline-blocks mr-1 "
                                                                style="width:330px">
                                                                <div class="form-group row ">
                                                                    <div class="col-md-8">
                                                                        <input type="text" class="form-control"
                                                                            name="wall_box_count">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">Überschussladen</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">Montageort</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">Haus</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">Garage</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">Carport</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">Starkstromkabel vorhanden</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <h4 class="bold ">Erdarbeiten notwendig</h4>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_no" checked
                                                                            value="nein">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_no">ja</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio"
                                                                            class="custom-control-input"
                                                                            name="e_auto" id="e_auto_yes"
                                                                            value="ja">
                                                                        <label class="custom-control-label"
                                                                            for="e_auto_yes">nein</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </section>
                            </div>
                        </article>
                    </div>
                    <!-- {{-- Wallbox Checklist: End --}} -->

                    <!-- {{-- Battry Checklist: Start --}} -->
                    <div class="col-md-8 col-12" id="battery" style="display: none !important;">
                        <article class="col-md-12 col-sm-12 col-12   ">
                            <div class="container"
                                style="display: flex;flex-wrap: wrap;align-content: flex-start; background: white; box-shadow: 0px 0px 10px 2px #a2a2a2;">
                                <div class="col-md-12" id="section_1">
                                    <div class="cards">
                                        <div class="card-body d-flex">
                                            <div class="col-md-2 image">
                                                <img src="{{ asset('images/articles/battery.png') }}"
                                                    alt="alternative" style="width: 128px;">
                                            </div>
                                            <div class="col-md-10 contents">
                                                <h2 class="title" style="color: #74b2d3">BATTERIESPEICHER</h2>
                                                <div class="form-group row">
                                                    <div class="col-md-12 mb-2 mt-2">
                                                        <span>MODULE</span>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <h4 class="bold">Hersteller</h4>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    name="number_we" value="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <h4 class="bold">Typ</h4>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    name="number_we" value="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <h4 class="bold">Watt</h4>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    name="number_we" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <h4 class="bold">Gersamtleistung Der Anlage</h4>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control"
                                                                    name="number_we" value="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <section class="col-md-12" id="section_2">
                                    <div class="cards">
                                        <div class="card-body"
                                            style="display: flex !important;flex-wrap: wrap;">
                                            <div class="form-group row">
                                                <div class="col-md-12 mb-2 mt-2">
                                                    <span>WECHSELRICHTER</span>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Hersteller</h4>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Typ</h4>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">max DC-Leistung</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                            &nbsp; <span>kwp</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">max. AC-Leistung</h4>
                                                        </div>
                                                        <div class="col-md-8 flex_me">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                            &nbsp; <span>kwh</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Wo ist der Wechselrichter
                                                                aufgestellt?</h4>
                                                        </div>
                                                        <div class="col-md-8  ">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl MPP-Tracker</h4>
                                                        </div>
                                                        <div class="col-md-8  ">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-3">
                                                            <h4 class="bold ">Anschluss</h4>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="e_auto" id="e_auto_no"
                                                                                checked value="nein">
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_no">1-phasis</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div
                                                                            class="custom-control custom-radio">
                                                                            <input type="radio"
                                                                                class="custom-control-input"
                                                                                name="e_auto" id="e_auto_yes"
                                                                                value="ja">
                                                                            <label class="custom-control-label"
                                                                                for="e_auto_yes">3-phasis</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Der Strings</h4>
                                                        </div>
                                                        <div class="col-md-8  ">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <h4 class="bold">Anzahl Module Pro Strings</h4>
                                                        </div>
                                                        <div class="col-md-8  ">
                                                            <input type="text" class="form-control"
                                                                name="number_we" value="">
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                </section>
                            </div>
                        </article>
                    </div>
                    <!-- {{-- Battry Checklist: End --}} -->  
                </div>
                <!-- {{-- Product Checklist and Forms: End --}} -->
            </section>

            <section> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="cards">
                            <div class="card-title h4 mb-3">
                                <h1 class="primary bold">BEWERTUNG DES KUNDEN</h1>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach ($customerReview as $review )  
                                    <div class="col-md-2 col-md-2 col-12">
                                        <div class="card">
                                            <div class="card-header" style="flex-direction: column;flex-direction: column;border-bottom: 9px solid #f8f8f8;">
                                                <p>
                                                    <h2><strong>{{ $review->article_group }}</strong></h2>   
                                                </p>
                                                <p>
                                                    <span class="float-left mr-3" >{{ $review->name }} {{ $review->lastname }}</span>
                                                        <span class="float-right" >{{ $review->review_date }}</span>  
                                                </p>
                                                
                                            </div>
                                                @foreach ($ReviewList as $list)
                                                @if($list->review_id == $review->id)
                                                    <div class="card-body"> 
                                                        <div class="row">
                                                            <div class="col-xl-6 col-12">
                                                                {{ $list->review }}
                                                            </div>
                                                            <div class="col-xl-6 col-12">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $list->grade)
                                                                        <i class="feather icon-star primary h4"></i>
                                                                    @else
                                                                        <i class="feather icon-star light h4"></i>
                                                                    @endif
                                                                @endfor
                                                            </div> 
                                                        </div>  
                                                    </div>
                                                    
                                                @endif
                                            @endforeach
                                                <div class="card-body" style="border-top: 9px solid #f8f8f8; height: 200px;"> 
                                                <div class="row">
                                                    <div class="col-xl-6 col-12">
                                                        <strong><label for="" class="h3">Notizen</label></strong>
                                                        <p>
                                                            {{ $review->note }}
                                                        </p>
                                                    </div>

                                                </div> 
                                            </div> 
                                            <form id="delete-form-{{ $review->id }}" action="{{ url('customer_review_delete/'.$review->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-icon btn-icon waves-effect waves-light danger" onclick="confirmDelete({{ $review->id }})">
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            </form>                                                                  

                                        </div>
                                    </div> 
                                        @endforeach
                                        <div class="col-xl-1 col-xl-1 col-12">
                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#review">
                                            <i class="feather icon-plus"></i>
                                        </button> 
                                    </div>
                                    <div class="modal fade text-left" id="review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable " role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel17">BEWERTUNG DES KUNDEN</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('customer.review.store') }}" method="post">
                                                    @method('post')
                                                    @csrf
                                                    <input type="hidden" name="customer_id" value="{{ request()->id}}">
                                                    <div class="modal-body">
                                                        <label>Gewerke</label>
                                                        <div class="form-group">
                                                            <select name="product_id" id="product_id">
                                                                    <option></option> 
                                                                    @foreach ($articles as $item)
                                                                        @if(in_array($item->id, $selectedProducts))
                                                                            <option value="{{$item->id}}" selected>{{$item->article_group}}</option>
                                                                        @endif
                                                                    @endforeach
                                                            </select>
                                                        </div>
                                                        <label>Bewertet von</label>
                                                        <div class="form-group">
                                                            <select name="employee_id" id="employee_id">
                                                                <option></option> 
                                                                @foreach ($employees as $emp) 
                                                                    <option value="{{$emp->id}}" selected>{{$emp->name}} {{ $emp->lastname }}</option> 
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <label>Bewertet Datum</label>
                                                        <div class="form-group">                                                                    
                                                            <input type="date" placeholder="review" class="form-control" name="review_date">

                                                        </div>

                                                    <div class="table-responsive">
                                                            <table class="table table-striped table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Überprüfung Titel</th>
                                                                        <th>Bewertungen</th>
                                                                        <th>Aktionen</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="table-body">
                                                                    <tr id="review_row">
                                                                        <th>
                                                                            <input type="text" placeholder="review" class="form-control" name="reviewArray[0][review]"> 
                                                                        </th>
                                                                        <th>
                                                                            <select name="reviewArray[0][grade]" id="grade" class="form-control">
                                                                                <option></option>
                                                                                <option value="0">☆☆☆☆☆</option>
                                                                                <option value="1">★☆☆☆☆</option>
                                                                                <option value="2">★★☆☆☆</option>
                                                                                <option value="3">★★★☆☆</option>
                                                                                <option value="4">★★★★☆</option>
                                                                                <option value="5">★★★★★</option>
                                                                            </select>
                                                                        </th>
                                                                        <th>
                                                                            <button type="button" class="btn btn-icon btn-flat-success mr-1 mb-1 waves-effect waves-light" id="add">+</button>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        
                                                        <label>Notizen</label>
                                                        <div class="form-group">
                                                            <textarea name="note" id="" col="3" row="4" class="form-control"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light"  >Speichern</button>
                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Absagen</button>
                                                    </div>
                                                </form> 
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
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

<!-- deleting Review: -->
 <script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Das können Sie nicht rückgängig machen!",
            icon: 'Warnung',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja',
            cancelButtonText: 'Nein',
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

<!-- {{-- show and hide the photovoltaik section: start --}} -->

<script>
    $(document).ready(function() {
    $('[id$="Button"]').click(function() {
        var targetId = $(this).attr('id').replace('Button', ''); // Extract target ID from button ID
        var targetSection = $('#' + targetId);
        var alternativeSection = $('#alternative');

        // Toggle the visibility of the target section
        if (targetSection.hasClass('d-none')) {
            // Hide all sections except the target one
            $('article').not('#' + targetId).addClass('d-none');
            // Show the target section
            targetSection.removeClass('d-none');
            // Update button states
            $('[id$="Button"]').removeClass('btn-danger').addClass('btn-light');
            $(this).removeClass('btn-light').addClass('btn-danger');
        } else {
            // If the target section is visible, hide it and show the alternative section
            targetSection.addClass('d-none');
            alternativeSection.removeClass('d-none');
            // Reset button state
            $(this).removeClass('btn-danger').addClass('btn-light');
        }
    });
});
</script>
<!-- {{-- show and hide the photovoltaik section: end --}} -->


<!-- {{-- Same Location or Alterative new Location --}} -->
 <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const sameCheckbox = document.getElementById('same');
        const streetInput = document.getElementById('location-input');
        const postcodeInput = document.getElementById('postal_code-input');
        const cityInput = document.getElementById('locality-input');
        const latitude = document.getElementById('latitude-input');
        const longitude = document.getElementById('longitude-input');
        const submitButton = document.getElementById('new_address'); // Added the submit button

        const customerStreet = "{{ $customer->street }}";
        const customerPostcode = "{{ $customer->postcode }}";
        const customerCity = "{{ $customer->city }}";
        const lat = "{{ $customer->lat }}";
        const lon = "{{ $customer->lon }}";

        const mainAddress = "{{ $customer->main }}";
        const customerAlternativeStreet =
            "{{ $alternative ? ($alternative->main == 0 ? $customer->street : $alternative->street) : $customer->street }}";
        const customerAlternativePostcode =
            "{{ $alternative ? ($alternative->main == 0 ? $customer->postcode : $alternative->postcode) : $customer->postcode }}";
        const customerAlternativeCity =
            "{{ $alternative ? ($alternative->main == 0 ? $customer->city : $alternative->city) : $customer->city }}";
        const Alternativelat =
            "{{ $alternative ? ($alternative->main == 0 ? $customer->lat : $alternative->lat) : $customer->lat }}";
        const Alternativelon =
            "{{ $alternative ? ($alternative->main == 0 ? $customer->lon : $alternative->lon) : $customer->lon }}";

        function setFieldsDisabled(state) {
            streetInput.disabled = state;
            postcodeInput.disabled = state;
            cityInput.disabled = state;
        }

        function setFieldsToCustomerValues() {
            if (mainAddress === 1) {
                streetInput.value = customerStreet;
                postcodeInput.value = customerPostcode;
                cityInput.value = customerCity;
                latitude.value = lat;
                longitude.value = lon;
            } else {
                streetInput.value = customerAlternativeStreet;
                postcodeInput.value = customerAlternativePostcode;
                cityInput.value = customerAlternativeCity;
                latitude.value = Alternativelat;
                longitude.value = Alternativelon;
            }
        }

        function clearFields() {
            streetInput.value = '';
            postcodeInput.value = '';
            cityInput.value = '';
            latitude.value = '';
            longitude.value = '';
        }

        function toggleSubmitButton(show) {
            submitButton.style.display = show ? 'block' : 'none';
        }

        sameCheckbox.addEventListener('change', () => {
            if (sameCheckbox.checked) {
                setFieldsToCustomerValues();
                setFieldsDisabled(true);
                toggleSubmitButton(false); // Hide the button when checkbox is checked
            } else {
                clearFields();
                setFieldsDisabled(false);
                toggleSubmitButton(true); // Show the button when checkbox is unchecked
            }
        });

        // Initial state check
        if (sameCheckbox.checked) {
            setFieldsToCustomerValues();
            setFieldsDisabled(true);
            toggleSubmitButton(false); // Hide the button if checkbox is initially checked
        } else {
            clearFields();
            setFieldsDisabled(false);
            toggleSubmitButton(true); // Show the button if checkbox is initially unchecked
        }
    });
</script>


<!-- {{--Select Product and buttons --}} -->
   <script>
    document.addEventListener("DOMContentLoaded", function() {
        const customerId = {{ $customer->id }}; // Ensure this ID is available in the view

        // Function to collect selected product IDs
        function getSelectedProductIds() {
            const selectedProducts = [];
            document.querySelectorAll('.products').forEach((card) => {
                const heartIcon = card.querySelector('.heart-icon');
                if (heartIcon && heartIcon.classList.contains('selected')) {
                    selectedProducts.push(card.dataset.productId);
                }
            });
            console.log('Selected product IDs:', selectedProducts);
            return selectedProducts;
        }

        // Function to toggle product interest and update database
        document.querySelectorAll('.products').forEach((card, index) => {
            const checkbox = card.querySelector('input[type="checkbox"]');
            const statusSpan = card.querySelector(`#interested-${index}`);
            const heartButton = document.getElementById(`${index}Like`);
            const heartIcon = heartButton ? heartButton.querySelector('.heart-icon') : null;
            const productId = card.dataset.productId;

            // Initialize UI based on checkbox state
            if (checkbox.checked && heartIcon) {
                heartIcon.classList.add('selected');
                heartButton.classList.replace('btn-light', 'btns-primary');
            } else if (heartIcon) {
                heartIcon.classList.remove('selected');
                heartButton.classList.replace('btns-primary', 'btn-light');
            }

            // Heart icon toggle event
            heartButton.addEventListener('click', (event) => {
                checkbox.checked = !checkbox.checked;
                card.classList.toggle('selected', checkbox.checked);

                if (checkbox.checked && heartIcon) {
                    statusSpan.innerHTML = 'Interessiert';
                    heartIcon.classList.add('selected');
                    heartButton.classList.replace('btn-light', 'btns-primary');
                } else if (heartIcon) {
                    statusSpan.innerHTML = '';
                    heartIcon.classList.remove('selected');
                    heartButton.classList.replace('btns-primary', 'btn-light');
                }

                // AJAX request to save interest status
                fetch('/customer_product_save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        product_id: productId,
                        interested: checkbox.checked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Product status saved:', data);
                    toastr.success('Das Produkt wurde erfolgreich gespeichert');
                })
                .catch(error => {
                    console.error('Error saving product status:', error);
                    toastr.error('Fehler beim Speichern der Daten');
                });

                event.stopPropagation();
                getSelectedProductIds();
            });
        });

        // Prevent scroll on focus
        document.addEventListener('focusin', (event) => {
            if (event.target.closest('.products')) {
                event.preventDefault();
            }
        });

        // Function to load article groups
        

        // Load article groups and log selected product IDs on page load
     
        getSelectedProductIds();
    });
</script>





<!-- {{-- Google Map API --}} -->
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer>
</script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.7/dist/html2canvas.min.js"></script>

<!-- {{-- Google Map --}} -->
<script>
        "use strict";

        let map;
        let panorama;

        function initMap() {
            const lat_value = parseFloat(document.getElementById('latitude-input').value);
            const lon_value = parseFloat(document.getElementById('longitude-input').value);

            const CONFIGURATION = {
                mapOptions: {
                    center: {
                        lat: lat_value,
                        lng: lon_value
                    },
                    fullscreenControl: true,
                    mapTypeControl: true,
                    streetViewControl: true,
                    zoom: 15,
                    zoomControl: true,
                    mapTypeId: google.maps.MapTypeId.SATELLITE
                }
            };

            map = new google.maps.Map(document.getElementById('gmp-map'), CONFIGURATION.mapOptions);
            panorama = map.getStreetView();

            const marker = new google.maps.Marker({
                map: map,
                position: {
                    lat: lat_value,
                    lng: lon_value
                }
            });

            const autocomplete = new google.maps.places.Autocomplete(document.getElementById('location-input'), {
                fields: ['address_components', 'geometry', 'name'],
                types: ['address']
            });

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }
                renderAddress(place, map, marker);
                fillInAddress(place);
                // getElevation(place.geometry.location);
            });
 
        
        }

       function fillInAddress(place) {
                let street = '';
                let postal_code = '';
                let city = '';

                // Extract address components
                place.address_components.forEach(component => {
                    const types = component.types;

                    if (types.includes('street_number')) {
                        street = component.long_name + ' ';
                    }
                    if (types.includes('route')) {
                        street += component.long_name;
                    }
                    if (types.includes('postal_code')) {
                        postal_code = component.long_name;
                    }
                    if (types.includes('locality')) { // For city
                        city = component.long_name;
                    }
                });

                // Set the extracted values to the respective input fields
                const streetInput = document.getElementById('location-input');
                const postcodeInput = document.getElementById('postal_code-input');
                const cityInput = document.getElementById('locality-input');

                if (streetInput) {
                    streetInput.value = street;
                }

                if (postcodeInput) {
                    postcodeInput.value = postal_code;
                }

                if (cityInput) {
                    cityInput.value = city;
                }
            }


        function renderAddress(place, map, marker) {
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
            document.getElementById('latitude-input').value = place.geometry.location.lat();
            document.getElementById('longitude-input').value = place.geometry.location.lng();
        }
    

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
    

<script src="{{ asset('js/select2.min.js') }}"></script>

<!-- {{-- show and Hide the Alternative Address Section --}} -->
<!-- <script>
    function toggleAlternativeAddress() {
        var alternativeAddressDiv = document.getElementById("alternative_address");
        if (alternativeAddressDiv.style.display === "none" || alternativeAddressDiv.style.display === "") {
            alternativeAddressDiv.style.display = "flex";
            alternativeAddressDiv.style.flexWrap = "wrap";
        } else {
            alternativeAddressDiv.style.display = "none";
        }
    }
</script> -->

<!-- {{-- Message of Sissions --}} -->
<script>
    $(document).ready(function() {
        $('#product').select2();
        $('#number_hotWaterConsumptionPerPerson').select2({
            width: '100%', // Corrected syntax here
            placeholder: "Warmwasser pro Person",
            allowClear: true,
            tags: true
        });


        @if(Session::has('update_msg'))
        toastr.success("{{ session('update_msg') }}");
        @endif

        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    });
        // function initializeSelect2() {
        //             $('select').select2({
        //                 width: '100%',
        //                 placeholder: 'Wählen Sie eine Option',
        //                 allowClear: true,
        //             });
        //         }

                // Call initializeSelect2 initially to apply to all selects
                // initializeSelect2();
</script>


<!-- {{-- WP Calculation for efficiecy--}} -->
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const heatingYearInput = document.getElementById('heating_manufacture_year');
        const heatingAgeLabel = document.getElementById('heating_age_label');
        const houseYearInput = document.getElementById('construction_year');
        const houseAgeLable = document.getElementById('house_age_label');
        const efficiencyResult = document.getElementById('efficiency_result');
        const effectiveDisplay = document.getElementById('effective');
        const efficiencyDisplay = document.getElementById('efficiency');
        const consumption = document.getElementById('consumption');
        const warm = document.getElementById('warm_water');
        const warmwaterResult = document.getElementById('warm_water_result');
        const heatpumpResult = document.getElementById('heatpump_result');
        const numberPeople = document.getElementById('number_people');

        let previousNumberPeople = numberPeople.value;

        function updateHeatingAge() {
            const currentYear = new Date().getFullYear();
            const heatingYear = parseInt(heatingYearInput.value);
            if (!isNaN(heatingYear)) {
                const age = currentYear - heatingYear;
                heatingAgeLabel.textContent = ` ${age} Jahr(e) Alt`;
            } else {
                heatingAgeLabel.textContent = ``; // Handle invalid input
            }
        }

        function updateHouseAge() {
            const current_Year = new Date().getFullYear();
            const houseYear = parseInt(houseYearInput.value);

            if (!isNaN(houseYear)) {
                const house_age = current_Year - houseYear;
                houseAgeLable.textContent = ` ${house_age} Jahr(e) Alt`;
            } else {
                houseAgeLable.textContent = ``; // Handle invalid input
            }
        }


        function calculateEfficiency(year) {
            if (year < 1980) {
                return 65;
            } else if (year < 1990) {
                return 75;
            } else if (year < 2000) {
                return 83;
            } else if (year < 2010) {
                return 88;
            } else if (year < 2020) {
                return 92;
            } else {
                return 96;
            }
        }

        // function updateEfficiency() {
        //     const heatingYear = parseInt(heatingYearInput.value);

        //     if (!isNaN(heatingYear)) {
        //         const efficiencyValue = calculateEfficiency(heatingYear);
        //         efficiencyResult.innerText = `${efficiencyValue}%`;

        //         const consumptionValue = parseInt(consumption.value);
        //         if (!isNaN(consumptionValue)) {
        //             const effectiveTotal = (efficiencyValue / 100) * consumptionValue;
        //             const remainingAmount = consumptionValue - effectiveTotal;
        //             const effectivePercentage = (effectiveTotal / consumptionValue) * 100;
        //             const remainingPercentage = (remainingAmount / consumptionValue) * 100;

        //             if (warm.value === "Central") {
        //                 const heatpumpAmount = (82 / 100) * effectiveTotal;
        //                 const warmwaterAmount = (18 / 100) * effectiveTotal;
        //                 effectiveDisplay.textContent = `(Mit Warmwasser): ${effectiveTotal.toFixed(0)} (${effectivePercentage.toFixed(0)}%)`;
        //                 efficiencyDisplay.textContent = `${remainingAmount.toFixed(2)} (${remainingPercentage.toFixed(2)}%)`;
        //                 heatpumpResult.textContent = ` ${heatpumpAmount.toFixed(0)} (${(heatpumpAmount / effectiveTotal * 100).toFixed(0)}%)`;
        //                 warmwaterResult.textContent = ` ${warmwaterAmount.toFixed(0)} (${(warmwaterAmount / effectiveTotal * 100).toFixed(0)}%)`;
        //               numberPeople.value = previousNumberPeople;
        //                 numberPeople.disabled = false;
        //             } else {
        //                 effectiveDisplay.textContent = `(Ohne Warmwasser) ${effectiveTotal.toFixed(2)} (${effectivePercentage.toFixed(0)}%)`;
        //                 efficiencyDisplay.textContent = `${remainingAmount.toFixed(0)} (${remainingPercentage.toFixed(0)}%)`;
        //                 heatpumpResult.textContent = ``;
        //                 warmwaterResult.textContent = ``;

        //                 previousNumberPeople = numberPeople.value;
        //                 numberPeople.value = 0;
        //                 numberPeople.disabled = true;
        //             }
        //         } else {
        //             effectiveDisplay.textContent = '';
        //             efficiencyDisplay.textContent = '';
        //             heatpumpResult.textContent = '';
        //             warmwaterResult.textContent = '';
        //         }
        //     } else {
        //         efficiencyResult.innerText = '';
        //         effectiveDisplay.textContent = '';
        //         efficiencyDisplay.textContent = '';
        //         heatpumpResult.textContent = '';
        //         warmwaterResult.textContent = '';
        //     }
        // }

        heatingYearInput.addEventListener('input', () => {
            updateHeatingAge();
            // updateEfficiency();
        });

        houseYearInput.addEventListener('input', () => {
            updateHouseAge();
            // updateEfficiency();
        });
        // consumption.addEventListener('input', updateEfficiency);
        // warm.addEventListener('change', updateEfficiency);

        // Initial calculation
        updateHeatingAge();
        updateHouseAge();
        // updateEfficiency();
    });
</script>

<!-- {{-- Product Selection to show and hide the checklist divs --}} -->
<script>
    function toggleDiv(index, articleGroup) {
        const divIds = {
            "PHOTOVOLTAIK": "pv",
            "ELEKTRO": "el",
            "BATTERIESPEICHER": "battery",
            "SANITÄR": "sanitear",
            "WÄRMEPUMPE": "wp",
            "BAD": "bad",
            "WALLBOX": "wallbox",
            "BAUELEMENTE": "windows",
            "KÜCHE": "kichen",
            "INNENAUSSTATTUNG": "inner_design",
            "PLANUNG": "planing",
            "SOLAR CARPORT": "carport"
        };

        var contentDivId = divIds[articleGroup];
        var contentDiv = document.getElementById(contentDivId);
        var alternative = document.getElementById('alternative');
        var button = document.getElementById(index + 'MenuButton');

        if (!contentDiv || !button) {
            console.error('Element not found for article group:', articleGroup);
            return;
        }

        // Check if the clicked content div is currently shown
        var isCurrentlyShown = contentDiv.style.display === "block";

        // Hide all content divs and reset buttons
        for (const key in divIds) {
            const div = document.getElementById(divIds[key]);
            const btn = document.getElementById(key.toLowerCase() + 'MenuButton');
            if (div) {
                div.style.display = "none";
            }
            if (btn) {
                btn.classList.remove('btns-primary');
                btn.classList.add('btn-light');
            }
        }

        // Toggle the clicked content div
        var anyDivShown = false;
        if (!isCurrentlyShown) {
            contentDiv.style.display = "block";
            button.classList.remove('btn-light');
            button.classList.add('btns-primary');
            anyDivShown = true;
        }

        // Show or hide the alternative div
        if (alternative) {
            if (anyDivShown) {
                alternative.style.display = "none";
            } else {
                alternative.style.display = "block";
            }
        }
    }

    function initialize() {
        const buttons = document.querySelectorAll('button[id$="MenuButton"]');
        var alternative = document.getElementById('alternative');
        let anyDivShown = false;

        buttons.forEach(button => {
            const contentDivId = button.id.replace('MenuButton', '').toUpperCase();
            const contentDiv = document.getElementById(contentDivId.toLowerCase());
            if (contentDiv && contentDiv.style.display === "block") {
                anyDivShown = true;
                button.classList.remove('btn-light');
                button.classList.add('btns-primary');
            } else {
                button.classList.remove('btns-primary');
                button.classList.add('btn-light');
            }
        });

        if (!anyDivShown && alternative) {
            alternative.style.display = "block";
        }
    }

    // Run initialize function on page load
    document.addEventListener('DOMContentLoaded', initialize);
</script>

 
<!-- {{--WP Dropdown: Start --}} -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[name="heatpump"]');
    const selectDropdown = document.getElementById('heating_type');
    const options = Array.from(selectDropdown.options);

    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const selectedType = this.value;
            filterOptions(selectedType);
        });
    });

    function filterOptions(type) {
        // Clear the select dropdown
        selectDropdown.innerHTML = '';

        // Filter and append the options that match the selected type
        options.forEach(option => {
            if (option.text.toLowerCase().startsWith(type.toLowerCase())) {
                selectDropdown.appendChild(option);
            }
        });
    }
});
</script>


<!-- {{-- Customer Image Form Submit Button: Start --}} -->
<script>
    $(document).ready(function() {
        // Set up the CSRF token in the header for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#submitBtn').on('click', function(e) {
            e.preventDefault();

            var formData = new FormData();

            let name = $("input[name=image_name]").val();
            let category = $("select[name=category_id]").val();
            let customerId = $("input[name=customer_id]").val();
            let photoInput = $('#image')[0]; // Reference the file input element
            var photo = photoInput ? photoInput.files[0] : null; // Get the first file

            if (!photo) {
                alert('The image field is required.');
                return;
            }

            formData.append('image', photo);
            formData.append('image_name', name);
            formData.append('category_id', category);
            formData.append('customer_id', customerId);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ route("customer.image") }}',
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    console.log('Image submitted:', formData.get('image').name);
                    alert('Image uploaded successfully');
                    $('#new_image').modal('hide');
                },
                error: function(response) {
                    console.log(response);
                    alert('Failed to upload image');
                }
            });
        });
    });
</script>
<!-- {{-- Customer Image Form Submit Button: End --}} -->

<!-- 
{{-- Textbox validation: start --}} -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function checkTextboxes() {
            document.querySelectorAll('.textbox-container').forEach(function(container) {
                var textbox = container.querySelector('.textbox');
                if (textbox && textbox.value.trim() === "") {
                    container.classList.add('empty');
                } else if (textbox) {
                    container.classList.remove('empty');
                }
            });
        }

        document.querySelectorAll('.textbox').forEach(function(textbox) {
            textbox.addEventListener('input', checkTextboxes);
            textbox.addEventListener('focus', checkTextboxes);
            textbox.addEventListener('blur', checkTextboxes);
        });

        checkTextboxes(); // Initial check
    });
</script>
<!-- {{-- Textbox Validation: End --}} -->



<!-- {{-- saving PV Checklist: Start --}} -->
<script>
    $(document).ready(function() {
        // Set up the CSRF token in the header for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#saveButton').on('click', function(e) {
            e.preventDefault();

            var formData = new FormData($('#pvForm')[0]);

            $.ajax({
                url: '{{ route("customer.pv.store") }}',
                type: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function(response) {
                    console.log('Form data submitted successfully', response);
                    toastr.success('Form data saved successfully!');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Validation errors
                        var errors = xhr.responseJSON.errors;
                        console.log('Validation errors:', errors);
                        $.each(errors, function(key, value) {
                            toastr.error(value);
                        });
                    } else if (xhr.status === 500) {
                        toastr.error('Internal server error');
                    } else {
                        toastr.error('An unexpected error occurred');
                    }
                }
            });
        });
    });
</script>
 
<!-- {{-- saving PV Checking: End --}} -->


<!-- {{-- image slider --}} -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.show_image').forEach(function(icon) {
            icon.addEventListener('click', function() {
                let categoryId = this.getAttribute('data-category');
                let carousel = document.getElementById('carousel-keyboard-' + categoryId);

                if (carousel.classList.contains('d-none')) {
                    document.querySelectorAll('.carousel').forEach(function(c) {
                        c.classList.add('d-none');
                    });
                    document.querySelectorAll('.show_image').forEach(function(i) {
                        i.classList.remove('primary');
                        i.classList.add('light');
                    });
                    carousel.classList.remove('d-none');
                    this.classList.remove('light');
                    this.classList.add('primary');
                } else {
                    carousel.classList.add('d-none');
                    this.classList.remove('primary');
                    this.classList.add('light');
                }
            });
        });
    });
</script>
<!-- {{-- image slider --}} -->


<!-- {{-- roof type drop down:start --}} -->
<script>
    function initializeSelect2() {
        $('.tiles').select2({
            templateResult: formatOption,
            templateSelection: formatOption
        });
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }

        var $option = $('<div id="roof"><h3>' + option.text + ' </h3><img src="' + $(option.element).data('image') +
            '" class="img-flag" /> </div>');
        return $option;
    }

    function filterTiles() {
        var selectedRoofType = document.querySelector('input[name="roof[0]"]:checked').value;
        var tilesSelect = document.querySelector('.tiles');
        var options = tilesSelect.options;

        console.log(selectedRoofType);
        for (var i = 0; i < options.length; i++) {
            var roofType = options[i].getAttribute('data-roof-type');
            if (roofType === selectedRoofType || roofType === null || roofType === '') {
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }

        // Reset the select to the first visible option
        tilesSelect.selectedIndex = Array.prototype.findIndex.call(options, option => option.style.display === '');

        // Reinitialize Select2
        $('.tiles').select2('destroy').select2({
            templateResult: formatOption,
            templateSelection: formatOption
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Attach change event to all roof type radio buttons
        var roofTypeRadios = document.querySelectorAll('input[name="roof[0]"]');
        roofTypeRadios.forEach(function(radio) {
            radio.addEventListener('change', filterTiles);
        });

        // Initial filter based on the default selected radio button
        filterTiles();
    });
</script>
<!-- {{-- Roof type drop down: end --}} -->



<!-- {{-- Roof type datas: start --}} -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let roofIndex = 1;

        document.getElementById('add_more').addEventListener('click', function() {
            const section = document.createElement('div');
            section.classList.add('col-12', 'roof-section');
            section.innerHTML = `

                    <div class="form-group row">
                        <div class="col-md-2">
                            <h4 class="bold">Dach ${roofIndex + 1}</h4>
                        </div>
                        <div class="col-md-2">
                            <span>Bezeichnung</span>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="designation[${roofIndex}]" value="" >
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="remove_roof btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light">
                                <i class="feather icon-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <ul class="list-unstyleds mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Satteldach.png') }}" alt="" srcset="" style="width:74px;" for="roof_Satteldach_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Satteldach_${roofIndex}" value="Satteldach" checked>
                                            <label class="custom-control-label" for="roof_Satteldach_${roofIndex}">Satteldach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Flachdach.png') }}" alt="" srcset="" style="width:74px;" for="roof_Flachdach_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Flachdach_${roofIndex}" value="Flachdach">
                                            <label class="custom-control-label" for="roof_Flachdach_${roofIndex}">Flachdach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Garage.png') }}" alt="" srcset="" style="width:74px;" for="roof_Garage_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Garage_${roofIndex}" value="Garage">
                                            <label class="custom-control-label" for="roof_Garage_${roofIndex}">Garage</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Carport.png') }}" alt="" srcset="" style="width:74px;" for="roof_Carport_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Carport_${roofIndex}" value="Carport">
                                            <label class="custom-control-label" for="roof_Carport_${roofIndex}">Carport</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row" >
                        <div class="col-md-2">
                            <h3 class="bold">Dacheindeckung</h3>
                        </div>
                        <div class="col-md-4">
                            <select class="tiles" name="tiles[${roofIndex}]" style="width:100%" >
                                @foreach ($tiles as $tile)
                                <option value="{{ $tile->id }}" data-image="{{ asset('images/products/'.$tile->image) }}">{{ $tile->product }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6" id="construction_fluid_section_${roofIndex}">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="construction_fluid[${roofIndex}]" id="construction_fluid_boton_${roofIndex}" value="Beton">
                                            <label class="custom-control-label" for="construction_fluid_boton_${roofIndex}">Beton</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="construction_fluid[${roofIndex}]" id="construction_fluid_ton_${roofIndex}" value="Ton">
                                            <label class="custom-control-label" for="construction_fluid_ton_${roofIndex}">Ton</label>
                                        </div>
                                    </fieldset>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row" id="insulation_section_${roofIndex}">
                        <div class="col-md-2">
                            <h3 class="bold">Aufdachdämmung</h3>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="pv_insulation[${roofIndex}]" id="insulation_ja_${roofIndex}" value="ja">
                                            <label class="custom-control-label" for="insulation_ja_${roofIndex}">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="insulation[${roofIndex}]" id="insulation_nein_${roofIndex}" value="nein">
                                            <label class="custom-control-label" for="insulation_nein_${roofIndex}">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1" style="width:330px">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <h4 class="bold">Stärke</h4>
                                        </div>
                                        <div class="col-md-8 textbox-container empty">
                                            <input type="text" class="form-control textbox" name="thickness_roof_insulation[${roofIndex}]" >
                                            <div class="indicator"></div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row" id="rafter_section_${roofIndex}">
                        <div class="col-md-2">
                            <h3 class="bold">Zwischen sparrendämmung</h3>
                        </div>
                        <div class="col-md-10">
                            <ul class="list-unstyled mb-0">
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="between_rafter_insulation[${roofIndex}]" id="rafter_ja_${roofIndex}" value="ja">
                                            <label class="custom-control-label" for="rafter_ja_${roofIndex}">ja</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="rafter[${roofIndex}]" id="rafter_nein_${roofIndex}" value="nein">
                                            <label class="custom-control-label" for="rafter_nein_${roofIndex}">nein</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1" style="width:330px">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <h4 class="bold">Stärke</h4>
                                        </div>
                                        <div class="col-md-8 textbox-container empty">
                                            <input type="text" class="form-control textbox" name="thickness_between_rafter[${roofIndex}]" >
                                            <div class="indicator"></div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6"  id="tilt_section_${roofIndex}">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <h4 class="bold">Neigung</h4>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="tilt[${roofIndex}]">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6" id="asbestos_section_${roofIndex}">
                        <div class="form-group row" >
                            <div class="col-md-2">
                                <h3 class="bold">Asbesthaltig</h3>
                            </div>
                            <div class="col-md-10">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-1">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="asbestos[${roofIndex}]" id="asbestos_ja_${roofIndex}" value="ja">
                                                <label class="custom-control-label" for="asbestos_ja_${roofIndex}">ja</label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-1">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="asbestos[${roofIndex}]" id="asbestos_nein_${roofIndex}" value="nein">
                                                <label class="custom-control-label" for="asbestos_nein_${roofIndex}">nein</label>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="roof_renovation_section_${roofIndex}">
                        <div class="form-group row" >
                            <div class="col-md-2">
                                <h3 class="bold">Dachsanierung</h3>
                            </div>
                            <div class="col-md-10">
                                <ul class="list-unstyled mb-0">
                                    <li class="d-inline-block mr-1">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="roof_renovation[${roofIndex}]" id="roof_renovation_ja_${roofIndex}" value="ja">
                                                <label class="custom-control-label" for="roof_renovation_ja_${roofIndex}">ja</label>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-1">
                                        <fieldset>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" class="custom-control-input" name="roof_renovation[${roofIndex}]" id="roof_renovation_nein_${roofIndex}" value="nein">
                                                <label class="custom-control-label" for="roof_renovation_nein_${roofIndex}">nein</label>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
            document.getElementById('section_3').appendChild(section);
            initializeSelect2();
            initializeRadioButtons(roofIndex);
            roofIndex++;
        });

        function initializeSelect2() {
            $('.tiles').select2({
                templateResult: formatOption,
                templateSelection: formatOption
            });
        }

        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }

            var $option = $('<div id="roof"><h3>' + option.text + ' </h3><img src="' + $(option.element).data(
                'image') + '" class="img-flag" /> </div>');
            return $option;
        }

        // Initialize Select2 for the existing tiles select elements
        initializeSelect2();

        // Event delegation to handle removing a roof section
        document.getElementById('section_3').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove_roof')) {
                e.target.closest('.roof-section').remove();
            }
        });

        function initializeRadioButtons(index) {
            const roofRadioButtons = document.querySelectorAll(`input[name="roof[${index}]"]`);
            roofRadioButtons.forEach(button => {
                button.addEventListener("change", function() {
                    handleRoofSelection(this.value, index);
                });
            });
            document.querySelector(`input[name="roof[${index}]"]:checked`).dispatchEvent(new Event('change'));
        }

        function handleRoofSelection(value, index) {
            const constructionFluidSection = document.getElementById(`construction_fluid_section_${index}`);
            const insulationSection = document.getElementById(`insulation_section_${index}`);
            const rafterSection = document.getElementById(`rafter_section_${index}`);
            const tiltSection = document.getElementById(`tilt_section_${index}`);
            const asbestosSection = document.getElementById(`asbestos_section_${index}`);
            const roofRenovationSection = document.getElementById(`roof_renovation_section_${index}`);
            console.log(value);
            // Hide all sections initially
            constructionFluidSection.style.display = "none";
            insulationSection.style.display = "none";
            rafterSection.style.display = "none";
            tiltSection.style.display = "none";
            asbestosSection.style.display = "none";
            roofRenovationSection.style.display = "none";

            // Show specific sections based on the selected roof type
            if (value === "Satteldach") {
                constructionFluidSection.style.display = "block";
                insulationSection.style.display = "block";
                rafterSection.style.display = "block";
            } else if (value === "Flachdach") {
                tiltSection.style.display = "block";
                asbestosSection.style.display = "block";
            } else if (value === "Garage") {
                tiltSection.style.display = "block";
                asbestosSection.style.display = "block";
            } else if (value === "Carport") {
                tiltSection.style.display = "block";
                asbestosSection.style.display = "block";
                roofRenovationSection.style.display = "block";
            }
        }

        // Initialize radio buttons for the first set of inputs
        initializeRadioButtons(0);
    });
</script>


<!-- {{-- Roof Type Datas: end --}} -->

<!-- function for adding new Review : Start -->
 <script>
    var i = 0;

    // Function to add a new row
    $('#add').click(function(){
        i++;
        $('#table-body').append(
            '<tr> <th><input type="text" class="form-control" placeholder="review" name="reviewArray['+i+'][review]"></th><th><select name="reviewArray['+i+'][grade]" class="form-control"><option></option><option value="0">☆☆☆☆☆</option><option value="1">★☆☆☆☆</option><option value="2">★★☆☆☆</option><option value="3">★★★☆☆</option><option value="4">★★★★☆</option><option value="5">★★★★★</option></select></th><th><button type="button" class="btn btn-icon btn-flat-danger mr-1 mb-1 waves-effect waves-light remove-row"><i class="fa fa-trash"></i></button></th></tr>'
        );
    });

    // Function to remove a row
    $(document).on('click', '.remove-row', function(){
        $(this).closest('tr').remove();
    });
</script>

<!-- function for adding new Review : End -->

 
<!-- Customer Room Dimension Scripts: start -->
  
<script>
  $(document).ready(function() {
    let roomIndex = 1; // Start with room 1

    // Add new row
    $(document).on('click', '.add_dimension', function() {
        roomIndex++;
        $('#room_dimension_table tbody').append(`
            <tr id="room_dimension_row_${roomIndex}">
                <th scope="row">
                    <input type="text" name="room[${roomIndex}][room_number]" class="form-control" value="${roomIndex}" readonly>
                </th>
                <td>
                    <select name="room[${roomIndex}][dimension_type]" class="form-control dimension_type">
                        <option value="Tür">Tür</option>
                        <option value="Wand">Wand</option>
                        <option value="Decke">Decke</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Breite" name="room[${roomIndex}][width]">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Höhe" name="room[${roomIndex}][height]">
                </td>
                <td>
                    <input type="text" class="form-control ceiling_height" placeholder="Deckenhöhe" name="room[${roomIndex}][ceiling_height]">
                </td>
                <td>
                    <select name="room[${roomIndex}][stair_form]" class="form-control stair_form">
                        <option value=""></option>
                        <option value="L-Form">L-Form</option>
                        <option value="U-Form">U-Form</option>
                        <option value="Wendel">Wendel</option>
                        <option value="Gradeluäfig">Gradeluäfig</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control stair_width" placeholder="Treppe Breite" name="room[${roomIndex}][stair_width]">
                </td>
                <td>
                    <select name="room[${roomIndex}][room_story]" class="form-control">
                        <option value="KG">KG</option>
                        <option value="EG">EG</option>
                        <option value="OG">OG</option>
                        <option value="DG">DG</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-danger remove-dimension-row">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Remove row
    $(document).on('click', '.remove-dimension-row', function() {
        $(this).closest('tr').remove();
        updateRoomNumbers(); // Update room numbers after removal
    });

    // Update room numbers after deletion
    function updateRoomNumbers() {
        $('#room_dimension_table tbody tr').each(function(index, row) {
            $(row).find('input[name*="[room_number]"]').val(index + 1); // Adjust room numbers
        });
    }

    // Disable fields based on dimension_type selection
    $(document).on('change', '.dimension_type', function() {
        const row = $(this).closest('tr');
        const dimensionType = $(this).val();

        if (dimensionType === 'Wand') {
            row.find('.stair_form, .stair_width, .ceiling_height').prop('disabled', true);
        } else {
            row.find('.stair_form, .stair_width, .ceiling_height').prop('disabled', false);
        }
    });

    // Save room dimensions
    $('#save_dimension').click(function() {
        var roomData = $('#room_dimension_form').serialize();

            $.ajax({
                url: '/room_dimensions/store',
                method: 'POST',
                data: roomData,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Raumdimensionen erfolgreich gespeichert');
                        loadRoomDimensions();
                    } else {
                        toastr.error('Fehler beim Speichern der Raumdimensionen');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Display validation errors
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            toastr.error(value[0]); // Display each error
                            console.error('Validation Error: ' + value[0]);
                        });
                    } else {
                        toastr.error('Fehler beim Speichern der Raumdimensionen');
                    }
                }
            });
    });

    function loadRoomDimensions() {
        const customerId = {{ $customer->id }}; 

        $.ajax({
            url: `/room_dimensions/get/${customerId}`,  // Correct route with customer_id and room_id
            method: 'GET',
            success: function(response) {
                console.log('Server Response:', response); // Add this line to debug the response
                if (response.success) {
                    // Clear the existing table
                    $('#room_dimension_data tbody').empty();
                    
                    // Populate the table with the fetched data
                    $.each(response.data, function(index, room) {
                        $('#room_dimension_data tbody').append(`
                            <tr>
                                <td>${room.room_number}</td>
                                <td>${room.dimension_type}</td>
                                <td>${room.width}</td>
                                <td>${room.height}</td>
                                <td>${room.ceiling_height ? room.ceiling_height : ''}</td>
                                <td>${room.stair_form ? room.stair_form : ''}</td>
                                <td>${room.stair_width ? room.stair_width : ''}</td>
                                <td>${room.room_story}</td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-primary edit-room" data-id="${room.id}">
                                        <i class="feather icon-edit"></i> Bearbeiten
                                    </button>
                                    <button type="button" class="btn btn-icon btn-danger delete-room" data-id="${room.id}">
                                        <i class="feather icon-trash"></i> Löschen
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    toastr.error('Fehler beim Laden der Raumdimensionen');
                }
            },
            error: function(xhr) {
                console.error('Error Loading Room Dimensions:', xhr.responseText);  // Add this line for more detailed error information
                toastr.error('Fehler beim Laden der Raumdimensionen');
            }
        });
    }


    // Load room dimensions initially when the page loads
    loadRoomDimensions();

     // Edit room dimension
     // Function to enable/disable fields based on dimension_type selection
    function toggleFields(dimensionType) {
        if (dimensionType === 'Tür') {
            $('#edit_ceiling_height, #edit_stair_form, #edit_stair_width').prop('disabled', false);  // Enable ceiling height, stair form, and stair width
        } else if (dimensionType === 'Wand') {
            $('#edit_ceiling_height, #edit_stair_form, #edit_stair_width').prop('disabled', true);   // Disable ceiling height, stair form, and stair width
        }
    }

    // When the modal opens, we check the current dimension_type value and toggle fields
    $('#editRoomDimensionModal').on('shown.bs.modal', function() {
        const dimensionType = $('#edit_dimension_type').val();
        toggleFields(dimensionType);
    });

    // When dimension_type is changed, toggle fields dynamically
    $('#edit_dimension_type').on('change', function() {
        const dimensionType = $(this).val();
        toggleFields(dimensionType);
    });

    // Edit room dimension (fetch and show in the modal)
    $(document).on('click', '.edit-room', function() {
        const roomId = $(this).data('id');
        $.ajax({
            url: `/room_dimensions/edit/${roomId}`,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const room = response.data;

                    // Populate modal form with fetched data
                    $('#edit_room_id').val(room.id);
                    $('#edit_room_number').val(room.room_number);
                    $('#edit_dimension_type').val(room.dimension_type).trigger('change');  // Trigger change to enable/disable fields
                    $('#edit_width').val(room.width);
                    $('#edit_height').val(room.height);
                    $('#edit_ceiling_height').val(room.ceiling_height);
                    $('#edit_stair_form').val(room.stair_form);
                    $('#edit_stair_width').val(room.stair_width);
                    $('#edit_room_story').val(room.room_story);

                    // Open the modal
                    $('#editRoomDimensionModal').modal('show');
                } else {
                    toastr.error('Fehler beim Laden der Raumdetails');
                }
            },
            error: function(xhr) {
                toastr.error('Fehler beim Laden der Raumdetails');
            }
        });
    });

    // Update room dimension (on save button click)
    $('#updateRoomDimension').click(function() {
        const roomId = $('#edit_room_id').val();
        const roomData = {
            room_number: $('#edit_room_number').val(),
            dimension_type: $('#edit_dimension_type').val(),
            width: $('#edit_width').val(),
            height: $('#edit_height').val(),
            ceiling_height: $('#edit_ceiling_height').val(),
            stair_form: $('#edit_stair_form').val(),
            stair_width: $('#edit_stair_width').val(),
            room_story: $('#edit_room_story').val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: `/room_dimensions/update/${roomId}`,
            method: 'PUT',
            data: roomData,
            success: function(response) {
                if (response.success) {
                    toastr.success('Raumdetails erfolgreich aktualisiert');
                    $('#editRoomDimensionModal').modal('hide');  // Close the modal
                    loadRoomDimensions();  // Reload room dimensions
                } else {
                    toastr.error('Fehler beim Aktualisieren der Raumdetails');
                }
            },
            error: function(xhr) {
                toastr.error('Fehler beim Aktualisieren der Raumdetails');
            }
        });
    });

    // Delete room dimension
    $(document).on('click', '.delete-room', function() {
        const roomId = $(this).data('id');
        if (confirm('Sind Sie sicher, dass Sie diesen Raum löschen möchten?')) {
            $.ajax({
                url: `/room_dimensions/delete/${roomId}`,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Raum erfolgreich gelöscht');
                        loadRoomDimensions();  // Reload room dimensions
                    } else {
                        toastr.error('Fehler beim Löschen des Raums');
                    }
                },
                error: function(xhr) {
                    toastr.error('Fehler beim Löschen des Raums');
                }
            });
        }
    });

});
</script>
<!-- Customer Room Dimension Scripts: end -->

<!-- WP Checklist: adding room Measurement: end  -->

<!-- Calculation of Energy costs, consumption, data from WP  -->
 <script>
    function calculateEnergyTotals() {
        // Get consumption values
        var firstYearConsumption = parseFloat(document.getElementById("energy_first_year_consumption").value) || 0;
        var secondYearConsumption = parseFloat(document.getElementById("energy_second_year_consumption").value) || 0;
        var thirdYearConsumption = parseFloat(document.getElementById("energy_third_year_consumption").value) || 0;

        // Calculate total consumption
        var totalConsumption = firstYearConsumption + secondYearConsumption + thirdYearConsumption;
        document.getElementById("energy_total_year_consumption").value = totalConsumption;

        // Count how many years have non-zero consumption values
        var consumptionCount = 0;
        if (firstYearConsumption > 0) consumptionCount++;
        if (secondYearConsumption > 0) consumptionCount++;
        if (thirdYearConsumption > 0) consumptionCount++;

        // Calculate and display average consumption
        var averageConsumption = 0;
        if (consumptionCount > 0) {
            averageConsumption = totalConsumption / consumptionCount;
        }
        document.getElementById("energy_avg_year_consumption").value = averageConsumption.toFixed(2);

        // Get cost values
        var firstYearCost = parseFloat(document.getElementById("energy_first_year_cost").value) || 0;
        var secondYearCost = parseFloat(document.getElementById("energy_second_year_cost").value) || 0;
        var thirdYearCost = parseFloat(document.getElementById("energy_third_year_cost").value) || 0;

        // Calculate the total sum of costs
        var totalCost = firstYearCost + secondYearCost + thirdYearCost;

        // Count how many years have non-zero costs
        var costCount = 0;
        if (firstYearCost > 0) costCount++;
        if (secondYearCost > 0) costCount++;
        if (thirdYearCost > 0) costCount++;

        // Calculate and display the total cost
        document.getElementById("energy_total_year_cost").value = totalCost.toFixed(2); // Total cost

        // Calculate and display the average cost
        var averageCost = 0;
        if (costCount > 0) {
            averageCost = totalCost / costCount;
        }
        document.getElementById("energy_avg_year_cost").value = averageCost.toFixed(2); // Average cost
    }

    // Attach the calculate function to input change events
    document.querySelectorAll('input').forEach(function(input) {
        input.addEventListener('input', calculateEnergyTotals);
    });
</script>


<script>
    function updateConsumptionTypeLabel() {
        // Get the selected value from the dropdown
        var selectedType = document.getElementById("energy_consumption_type").value;

        // Update all elements with the label id (since you have two)
        var labels = document.querySelectorAll("#energy_consumption_type_lable");
        labels.forEach(function(label) {
            label.textContent = selectedType;
        });
    }

    // Attach event listener to the dropdown for the change event
    document.getElementById("energy_consumption_type").addEventListener("change", updateConsumptionTypeLabel);
</script>


<!-- Calculation of number_of_heating_circuits from WP section  -->

<!-- show and hide the underfloor and radiator checklist of WP: Start  -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const heatingTypeSelect = document.getElementById('wp_heating_type');
    const underfloorC = document.getElementById('underfloorC');
    const rediatorC = document.getElementById('rediatorC');

    // Check if the elements exist
    if (!heatingTypeSelect || !underfloorC || !rediatorC) {
        console.error('One or more required elements are missing!');
        return;
    }

    // Function to show or hide sections based on selected value
    function toggleHeatingOptions() {
        const selectedValue = heatingTypeSelect.value;

        // Log the selected value for debugging purposes
        console.log('Selected Value:', selectedValue);

        // Initially hide both sections
        underfloorC.style.display = 'none';
        rediatorC.style.display = 'none';

        // Remove collapse-related classes (if Bootstrap is interfering)
        underfloorC.classList.remove('collapse', 'show');
        rediatorC.classList.remove('collapse', 'show');

        if (selectedValue === '1') {
            // Show underfloor heating section
            underfloorC.style.display = 'block';
        } else if (selectedValue === '2') {
            // Show radiator section
            rediatorC.style.display = 'block';
        } else if (selectedValue === '3') {
            // Show both sections
            underfloorC.style.display = 'block';
            rediatorC.style.display = 'block';
        }
    }

    // Attach event listener to the select dropdown
    heatingTypeSelect.addEventListener('change', toggleHeatingOptions);

    // Initialize the correct display when the page loads
    toggleHeatingOptions();
});
</script>



<!-- show and hide the underfloor and radiator checklist of WP: Start  -->

 
<!-- Number of Underfloor Heating : start  -->
 <script>
    $(document).ready(function() {
    let heatingIndex = 1;

    // Add a new heating circuit row
    $('.add-heating-row').click(function() {
        heatingIndex++;
        $('#number_of_heating_circuits tbody').append(`
            <tr>
                <th scope="row">
                    <input type="text" name="heating[${heatingIndex}][heating_circuit_number]" class="form-control" value="${heatingIndex}" readonly>
                </th>
                <td>
                    <input type="text" class="form-control" placeholder="Vorlauf" name="heating[${heatingIndex}][flow_temperature]">
                </td>
                <td>
                    <input type="text" class="form-control" placeholder="Rücklauf" name="heating[${heatingIndex}][return_flow_temperature]">
                </td>
                <td>
                    <select name="heating[${heatingIndex}][room_story]" class="form-control">
                        <option value=""></option>
                        <option value="KG">KG</option>
                        <option value="EG">EG</option>
                        <option value="OG">OG</option>
                        <option value="DG">DG</option>
                    </select>
                </td>
                <td>
                    <select name="heating[${heatingIndex}][pipe_dimension]" class="form-control">
                        <option value=""></option>
                        <option value="12">12</option>
                        <option value="14">14</option>
                        <option value="16">16</option>
                        <option value="17">17</option>
                        <option value="18">18</option>
                        <option value="20">20</option>
                    </select>
                </td>
                <td>
                    <select name="heating[${heatingIndex}][pipe_material]" class="form-control">
                        <option value=""></option>
                        <option value="Kupfer">Kupfer</option>
                        <option value="Kunststoff">Kunststoff</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-danger remove-heating-row">
                        <i class="feather icon-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Remove heating circuit row
    $(document).on('click', '.remove-heating-row', function() {
        $(this).closest('tr').remove();
    });

    // Save heating circuits and load details in the details table
   $('#save_heating_cercuit').click(function() {
        let heatingData = [];
        let customerId = {{ $customer->id }};   


        $('#number_of_heating_circuits tbody tr').each(function(index, row) {
            let heatingCircuitNumber = $(row).find('input[name*="[heating_circuit_number]"]').val();
            let flowTemperature = $(row).find('input[name*="[flow_temperature]"]').val();
            let returnFlowTemperature = $(row).find('input[name*="[return_flow_temperature]"]').val();
            let roomStory = $(row).find('select[name*="[room_story]"]').val();
            let pipeDimension = $(row).find('select[name*="[pipe_dimension]"]').val();
            let pipeMaterial = $(row).find('select[name*="[pipe_material]"]').val();

            heatingData.push({
                heating_circuit_number: heatingCircuitNumber,
                flow_temperature: flowTemperature,
                return_flow_temperature: returnFlowTemperature,
                room_story: roomStory,
                pipe_dimension: pipeDimension,
                pipe_material: pipeMaterial
            });
        });

        $.ajax({
            url: "{{ route('heating.circuit.store') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                customer_id: customerId,
                heatingData: heatingData
            },
            success: function(response) {
                toastr.success('Heizkreisdaten erfolgreich gespeichert');
                loadHeatingCircuits();
            },
            error: function(xhr) {
                toastr.error('Fehler beim Speichern der Heizkreisdaten');
                console.error(xhr.responseText);
            }
        });
    });


    // Load heating circuits into the details table
    function loadHeatingCircuits(customerId, $roomId) {
        $.ajax({
            url: '/heating_circuit/get/' + {{ $customer->id }},
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Clear the current table content
                    $('#number_of_heating_circuits_details tbody').empty();

                    // Loop through the returned data and append it to the table
                    $.each(response.data, function(index, heatingCircuit) {
                        $('#number_of_heating_circuits_details tbody').append(`
                            <tr data-id="${heatingCircuit.id}">
                                <td>${heatingCircuit.heating_circuit_number}</td>
                                <td>${heatingCircuit.flow_temperature} ℃</td>
                                <td>${heatingCircuit.return_flow_temperature} ℃</td>
                                <td>${heatingCircuit.room_story}</td>
                                <td>${heatingCircuit.pipe_dimension}</td>
                                <td>${heatingCircuit.pipe_material}</td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-danger delete-heating-circuit" data-id="${heatingCircuit.id}">
                                        <i class="feather icon-trash"></i> Löschen
                                    </button>
                                    <button type="button" class="btn btn-icon btn-primary edit-heating-circuit" data-id="${heatingCircuit.id}">
                                        <i class="feather icon-edit"></i> Bearbeiten
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    toastr.error('Error loading heating circuits');
                }
            },
            error: function(xhr) {
                toastr.error('Error loading heating circuits: ' + xhr.responseText);
            }
        });
    }


    // Edit heating circuit
    $(document).ready(function() {
        // Edit button click event - open the modal and populate it with data
        $(document).on('click', '.edit-heating-circuit', function() {
            var heatingCircuitId = $(this).data('id');
            
            // Fetch the heating circuit details using AJAX and populate the modal
            $.ajax({
                url: '/heating_circuit/get_single/' + heatingCircuitId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        var heatingCircuit = response.data;

                        // Populate the modal with the existing heating circuit data
                        $('#heating_circuit_number').val(heatingCircuit.heating_circuit_number);
                        $('#flow_temperature').val(heatingCircuit.flow_temperature);
                        $('#return_flow_temperature').val(heatingCircuit.return_flow_temperature);
                        $('#room_story').val(heatingCircuit.room_story);
                        $('#pipe_dimension').val(heatingCircuit.pipe_dimension);
                        $('#pipe_material').val(heatingCircuit.pipe_material);
                        $('#heating_circuit_id').val(heatingCircuit.id); // Store the ID for later use

                        // Open the modal
                        $('#heatingCircuitModal').modal('show');
                    } else {
                        toastr.error('Error fetching data');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error loading heating circuit details.');
                }
            });
        });

        // Save button click event inside the modal
        $('#saveHeatingCircuit').click(function() {
            var heatingCircuitId = $('#heating_circuit_id').val();
            var url = heatingCircuitId ? '/heating_circuit/update/' + heatingCircuitId : '/heating_circuit/store';
            var method = heatingCircuitId ? 'PUT' : 'POST';

            // Collect form data
            var heatingCircuitData = {
                heating_circuit_number: $('#heating_circuit_number').val(),
                flow_temperature: $('#flow_temperature').val(),
                return_flow_temperature: $('#return_flow_temperature').val(),
                room_story: $('#room_story').val(),
                pipe_dimension: $('#pipe_dimension').val(),
                pipe_material: $('#pipe_material').val(),
                _token: "{{ csrf_token() }}"
            };

            // Send the form data to the server via AJAX
            $.ajax({
                url: url,
                method: method,
                data: heatingCircuitData,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Heizkreis erfolgreich gespeichert.');
                        $('#heatingCircuitModal').modal('hide'); // Close the modal
                        loadHeatingCircuits($('#customer_id').val()); // Reload the table with updated data
                    } else {
                        toastr.error('Fehler beim Speichern der Daten.');
                    }
                },
                error: function(xhr) {
                    toastr.error('Ein Fehler ist aufgetreten.');
                }
            });
        });
    });

    // Delete heating circuit
    $(document).on('click', '.delete-heating-circuit', function() {
        var heatingCircuitId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this heating circuit?')) {
            $.ajax({
                url: '/heating_circuit/delete/' + heatingCircuitId,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Heating circuit deleted successfully');
                        // Reload the heating circuits
                        loadHeatingCircuits($('#customer_id').val());
                    } else {
                        toastr.error('Error deleting heating circuit');
                    }
                },
                error: function(xhr) {
                    toastr.error('Error deleting heating circuit: ' + xhr.responseText);
                }
            });
        }
    });

    // Load heating circuits initially
    loadHeatingCircuits();
});

</script>

<!-- Number of Underfloor Heating: end -->


<!-- Meter Cabinet Script:start  -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const meter_cabinet = document.getElementById('meter_cabinet');
    const cabinetSizeDiv = document.getElementById('cabinet_size_div');
    const cabinetSettingsDiv = document.getElementById('cabinet_settings_div');
    const wpAllCheckbox = document.getElementById('wp_all');

    // Function to handle the visibility of fields and uncheck checkboxes when necessary
    function handleCabinetSelection() {
        const selectedValue = meter_cabinet.value;
        const checkboxes = cabinetSettingsDiv.querySelectorAll('input[type="checkbox"]');

        // If OK, hide both and uncheck all checkboxes
        if (selectedValue === 'ok') {
            cabinetSizeDiv.style.display = 'none';
            cabinetSettingsDiv.style.display = 'none';

            // Uncheck all checkboxes
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
        }
        // If Upgrade, show only settings
        else if (selectedValue === 'upgrade') {
            cabinetSizeDiv.style.display = 'none';
            cabinetSettingsDiv.style.display = 'block';
        }
        // If New, show only size and uncheck all checkboxes
        else if (selectedValue === 'new') {
            cabinetSizeDiv.style.display = 'block';
            cabinetSettingsDiv.style.display = 'none';

            // Uncheck all checkboxes
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
        }
    }

    // Add event listener for meter cabinet select
    meter_cabinet.addEventListener('change', handleCabinetSelection);

    // Handle 'Alles' checkbox functionality
    wpAllCheckbox.addEventListener('change', function () {
        const checkboxes = cabinetSettingsDiv.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(function (checkbox) {
            if (checkbox !== wpAllCheckbox) {
                checkbox.checked = wpAllCheckbox.checked;
            }
        });
    });

    // Initialize the visibility and uncheck on page load
    handleCabinetSelection();
});

  </script>
<!-- Meter Cabinet Script: End -->

 <script>
document.getElementById('electric_car_edit').addEventListener('change', function() {
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
    document.getElementById('heating_system_type_edit').addEventListener('change', function() {
        var unitSpan = document.getElementById('heat-energy');
        var selectedValue = this.value;
        
        switch (selectedValue) {
            case 'Gas':
                unitSpan.textContent = 'CAB';
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

<!-- Image Gallary :start  -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
$(document).ready(function() {
    const customer_id = {{ $customer->id }}; // Ensure customer_id is globally defined

    // Load article groups dynamically into the dropdown on page load or modal show
    function loadArticleGroups() {
        console.log("Loading article groups for customer_id:", customer_id);

        $.ajax({
            url: `/customer_product_list/${customer_id}`,
            type: 'GET',
            success: function(response) {
                console.log("Received article groups response:", response);

                const articleGroupSelect = $('#article_group');
                articleGroupSelect.find('option:not(:first)').remove(); // Clear existing options

                // Populate the select element with the data
                response.forEach(function(item) {
                    articleGroupSelect.append(new Option(item.article_group, item.id));
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading article groups:', error);
                console.log("Error details:", xhr.responseText);
            }
        });
    }

    // Initial load for article groups
    loadArticleGroups();

    // Double-click to edit image name
    $(document).on('dblclick', '.edit_image_name', function() {
        const imageId = $(this).data('id');
        console.log("Editing image name for ID:", imageId);

        $(this).hide();
        $('input[name="image_name"][data-id="' + imageId + '"]').show().focus();
    });

    $(document).on('blur', 'input[name="image_name"]', function() {
        const input = $(this);
        const imageId = input.data('id');
        const newName = input.val();

        console.log("Updating image name for ID:", imageId, "to:", newName);

        // AJAX request to update the image name
        $.ajax({
            url: '/customer_image_name',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: imageId,
                image_name: newName
            },
            success: function(response) {
                if (response.success) {
                    console.log("Image name updated successfully:", response);
                    $('.edit_image_name[data-id="' + imageId + '"]').text(newName).show();
                    input.hide();
                    toastr.success(response.success);
                } else {
                    toastr.error("Error updating image name");
                }
            },
            error: function(xhr) {
                console.error("Error updating image name:", xhr);
                alert('Error updating image name');
            }
        });
    });

    // Delete image
    $(document).on('click', '.btn-flat-danger', function() {
        const imageId = $(this).data('id');
        const imageCard = $(this).closest('.col-md-3');

        console.log("Attempting to delete image ID:", imageId);

        Swal.fire({
            title: 'Bist du sicher?',
            text: "Sie können dies nicht rückgängig machen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja, löschen!'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX request to delete image
                $.ajax({
                    url: `/customer_image_destroy/${imageId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log("Image deleted successfully:", response);
                            toastr.success(response.message);
                            imageCard.remove();
                        } else {
                            toastr.error("Error deleting image");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error deleting image:", xhr);
                        alert('Error deleting image');
                    }
                });
            }
        });
    });
});

 $(document).ready(function() {
        // When an option in the article_group dropdown is selected
        $('#article_group').on('change', function() {
            const selectedArticleGroup = $(this).val(); // Get the selected option value
            $('#image_product_id').val(selectedArticleGroup); // Set it in the hidden input
            console.log("Selected product_id (image_product_id):", selectedArticleGroup); // Debugging log
        });

        // When an option in the swal-stage dropdown is selected
        $('#swal-stage').on('change', function() {
            const selectedStage = $(this).val(); // Get the selected option value
            $('#stage_id').val(selectedStage); // Set it in the hidden input
            console.log("Selected stage_id:", selectedStage); // Debugging log
        });
    });
</script>

  <script>
    $(document).ready(function() {
    const modalImage = $('#modalImage');
    const zoomRange = $('#image_zoom');
    const modalTitle = $('#imageModalLabel');
    const imageContainer = $('.image-container');

    // Open modal, set image source, title, and reset zoom
    $(document).on('click', '.open-modal', function() {
        const imageUrl = $(this).data('image');
        const imageName = $(this).attr('alt'); // Use image's alt attribute as the name

        // Set image source and modal title
        modalImage.attr('src', imageUrl);
        modalTitle.text(imageName);

        // Reset zoom level and overflow
        zoomRange.val(1);
        modalImage.css('transform', 'scale(1)');
        imageContainer.css('overflow', 'hidden'); // Hide scroll by default

        // Show the modal
        $('#imageModal').modal('show');
    });

    // Update zoom level and enable scrolling if zoomed
    zoomRange.on('input', function() {
        const zoomLevel = $(this).val();
        modalImage.css('transform', `scale(${zoomLevel})`);
        
        // Enable scrollbars when zoom level exceeds 1
        if (zoomLevel > 1) {
            imageContainer.css('overflow', 'auto'); // Enable scrollbars
        } else {
            imageContainer.css('overflow', 'hidden'); // Hide scrollbars when zoom is 1
        }
    });
});
</script>

 <script>
$(document).ready(function() {
    const documentViewerBody = $('#document_viewer_body');
    const downloadButton = $('#download_button');
 

    // Open document in modal for preview based on file type
    $(document).on('click', '.open-document', function() {
        const fileType = $(this).data('file-type').toLowerCase();
        const fileName = $(this).data('file-name');
        const fileUrl = $(this).data('file-url');

        // Set the modal title with the file name
        $('#myModalLabel16').text(`DOKUMENT VIEWER: ${fileName}`);
        
        // Clear previous content
        documentViewerBody.empty();

        // Set download button link and file name
        downloadButton.attr('href', fileUrl);
        downloadButton.attr('download', fileName);

        // Load document preview based on file type
        if (fileType === 'pdf') {
            // Show PDF in an iframe
            documentViewerBody.html(`<iframe src="${fileUrl}" frameborder="0" style="width:100%; height:80vh;"></iframe>`);
        } else if (fileType === 'docx' || fileType === 'doc') {
            // Display Word document icon
            documentViewerBody.html(`
                <i class="fa fa-file-word-o" style="font-size: 100px; color: #007bff;"></i>
                <p>This document is a Word file. Click "Download Document" to view it.</p>
            `);
        } else if (fileType === 'xlsx') {
            // Display Excel document icon
            documentViewerBody.html(`
                <i class="fa fa-file-excel-o" style="font-size: 100px; color: #28a745;"></i>
                <p>This document is an Excel file. Click "Download Document" to view it.</p>
            `);
        } else {
            // For unsupported file types, show a message
            documentViewerBody.html(`<p>Preview not available for this document type.</p>`);
        }

        // Open the modal
        $('#customer_document').modal('show');
    });

       
});
</script>

 
<!-- Image Gallary :end  -->
 <script>
    $(document).ready(function() {
        // Initially hide all image sections
        $('.image-section').hide();

        // Listen for changes on the dropdown
        $('#filter_image').on('change', function() {
            // Get the selected value
            const selectedValue = $(this).val();

            // Hide all sections
            $('#customer_image, #montage_image, #end_image, #article_image, #photo_image').hide();

            // Show only the selected div based on the dropdown value
            if (selectedValue === "1") {
                $('#customer_image').show();
            } else if (selectedValue === "2") {
                $('#montage_image').show();
            } else if (selectedValue === "3") {
                $('#end_image').show();
            } else if (selectedValue === "4") {
                $('#article_image').show();
            } else if (selectedValue === "5") {
                $('#photo_image').show();
            }
        });
    });
</script>






@endsection