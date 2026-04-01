@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@section('style')
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/file-uploaders/dropzone.css') }}">
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
                        <div class="cards">
                            <div class="card-content">
                                <div class="card-body">
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
                                                    <div class="col-md-4">
                                                        <span>Title</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="text" id="first-name" class="form-control textbox"
                                                            value="{{ $customer->title }}" name="firma" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Firma</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="text" id="first-name" class="form-control textbox"
                                                            value="{{ $customer->firma }}" name="firma" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Name</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="text" class="form-control textbox"
                                                            value="{{ $customer->name }} {{ $customer->lastname }}"
                                                            name="lastname" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Straße / Nr.</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty ">
                                                        <input type="text" class="form-control textbox" name="street"
                                                            value="{{
                                                            $customer->street }}" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>PLZ / Ort</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="text" class="form-control textbox"
                                                            value="{{ $customer->postcode }} {{ $customer->city }}"
                                                            name="postcode" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Tel</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="text" id="contact-info"
                                                            class="form-control textbox" value="{{ $customer->phone }}"
                                                            name="phone" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>E-Mail</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty">
                                                        <input type="email" id="contact-info"
                                                            class="form-control textbox" name="email"
                                                            value="{{ $customer->email }}" readonly>
                                                        <div class="indicator"></div>

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


                    <div class="col-md-6 col-12">
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
                                                                @if($customer->document=="Ja") checked enabled @else
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
                                                                @if($customer->document=="Nein") checked enabled @else
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
                                ->join('users', 'users.name', '=', 'employees.id')
                                ->select('employees.name', 'employees.lastname')
                                ->where('users.name', '=', $customer->contact_person)
                                ->first()
                                @endphp

                                <div class="col-6">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Kontaktperson</span>
                                        </div>
                                        <div class="col-md-8">
                                            @if($user_name)
                                            <input type="text" class="form-control" name="{{ auth()->user()->name }}"
                                                value="{{ $user_name->name }} {{ $user_name->lastname }}" disabled>
                                            @else
                                            <input type="text" class="form-control"
                                                name="{{ $customer->contact_person }}" disabled>
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
                                                            <input type="radio" class="custom-control-input"
                                                                name="consultation" id="consultation_yes" checked
                                                                value="Ja" @if($customer->consultation=="Ja") checked
                                                            enabled @else disabled @endif>
                                                            <label class="custom-control-label"
                                                                for="consultation_yes">Ja</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input"
                                                                name="consultation" id="consultation_no" value="Nein"
                                                                @if($customer->consultation=="Nein") checked enabled
                                                            @else disabled @endif>
                                                            <label class="custom-control-label"
                                                                for="consultation_no">Nein</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input"
                                                                name="consultation" id="consultation_persönlich"
                                                                value="persönlich"
                                                                @if($customer->consultation=="persönlich") checked
                                                            enabled @else disabled @endif>
                                                            <label class="custom-control-label"
                                                                for="consultation_persönlich">persönlich</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input"
                                                                name="consultation" id="consultation_telefonisch"
                                                                value="telefonisch"
                                                                @if($customer->consultation=="telefonisch") checked
                                                            enabled @else disabled @endif>
                                                            <label class="custom-control-label"
                                                                for="consultation_telefonisch">telefonisch</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                                <li class="d-inline-block mr-1">
                                                    <fieldset>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input"
                                                                name="consultation" id="consultation_Video"
                                                                value="Video" @if($customer->consultation=="Video")
                                                            checked enabled @else disabled @endif>
                                                            <label class="custom-control-label"
                                                                for="consultation_Video">Video</label>
                                                        </div>
                                                    </fieldset>
                                                </li>
                                            </ul>
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
                        </div>
                    </div>
            </section>
            <div class="col-12">
                <br>
                <hr>
            </div>
            <section id="contents">
                        <div class="col-12 alterative" style="display:none;" id="alternative_address">  
                            <div class="col-md-4 col-sm-12 col-12">
                                {{-- Product Selection Start --}}
                              
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
                                                                    <a href="{{ route('customer.product.details', ['customer_id' => request()->id, 'product_id' => $item->id, 'address_no'=>request()->address_no]) }}">
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
                                {{-- Product Selection: End --}}
                            </div>

                            {{-- Alternative Address --}}
                            <div class="col-md-8 col-12" id="alternative" style="display: flex;flex-wrap: wrap;">
                                {{-- Alternative Address and Image --}}
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
                                                <div class="col-md-4 float-right" style="text-align: right;">
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
                                                                    <form id="image-upload-form" action="{{ route('images.store') }}" method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <img id="modal-screenshot-img" src="" alt="Map Screenshot" class="img-fluid mb-3" />
                                                                            <div class="col-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <span>KATEGORIE</span>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                    <input type="hidden" value="{{ $customer->id }}" name="customer_id">
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
                                                                            <div class="col-12">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <span>Name</span>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <input type="text" class="form-control" name="image_name" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Hochladen</button>
                                                                        </div>
                                                                        <input type="file" id="image-input" name="image" style="display: none;" />
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-title h4 mb-3">
                                                    <h1 class="primary bold">FOTOS DES OBJEKTS</h1>
                                                </div>
                                                <div class="map" id="gmp-map" style="width: 100%;position: relative;overflow: hidden; height: 478px;">
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="card mb-0">
                                                    <div class="card-content" style="text-align: -webkit-center;">
                                                        @foreach ($images as $img)
                                                        <div class="image-container" style="display:inline-block;">
                                                            <img class="card-img-top img-fluid mr-1 ml-1 mt-1 mb-1" src="{{ asset('images/customers/home/'.$img->image) }}" alt="{{ $img->image_name }}" style="width: 174px;">
                                                            <form action="{{ route('images.destroy', $img->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-primary">Delete</button>
                                                            </form>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="modal-dark mr-1 mb-1 d-inline-block">
                                                    <button type="button" class="btn btn-outline-dark waves-effect waves-light" data-toggle="modal" data-target="#new_image">
                                                        UPLOAD
                                                    </button>
                                                    <div class="modal fade text-left" id="new_image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel150" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-dark white">
                                                                    <h5 class="modal-title" id="myModalLabel150">Neue Foto - {{ $customer->name }} {{ $customer->lastname }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <form id="customer_image_form" class="custom-file-upload" action="{{ route('images.store') }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Name</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="hidden" value="{{ $customer->id }}" name="customer_id">
                                                                                    <input type="text" class="form-control" name="image_name">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>KATEGORIE</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <select name="category_id" class="form-control">
                                                                                        @foreach ($category as $cat)
                                                                                        <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Foto</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="file" class="form-control" name="image" id="image">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-dark waves-effect waves-light">UPLOAD</button>
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

                                </article>
                                <!-- {{-- Alternative Address and Image: End --}} -->
                            </div>
                          
                            {{-- Alternative Address: End --}}

                            {{-- Product Checklist and Forms: Start --}}

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
                                                                                    style="width:150px;"
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
                                                                                    style="width:150px;"
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
                                                                                    style="width:150px;"
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
                                                                                    style="width:150px;"
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
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Objektart</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <div class="form-group">
                                                                        <select name="wp_objective" id="" class="form-control">
                                                                            <option value="">Bitte wählen</option>
                                                                            <option value="EFH">EFH</option>
                                                                            <option value="MFH">MFH</option>
                                                                            <option value="Gewerbe">Gewerbe</option>
                                                                            <option value="others">Sonstigis</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group row">
                                                                <div class="col-md-2">
                                                                    <h4 class="bold ">Objektzustand</h4>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <div class="form-group">
                                                                        <select name="objec" id="" class="form-control">
                                                                            <option value="">Bitte wählen</option>
                                                                            <option value="new">Neubau</option>
                                                                            <option value="renovation">Sanierung</option>
                                                                            <option value="individual measures">Einzelmaßnahmen</option>
                                                                            <option value="others">Sonstigis</option>

                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
 
                                                            <div class="col-12"> 
                                                                <div class="table-responsive">
                                                                    <h2 class="bold section_title">Verwaltung der Wohnetagenwohnungen</h2> 
                                                                    <button type="button" class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light float-right" id="add_new_story">
                                                                        <i class="feather icon-plus"></i> Neugeschoss
                                                                    </button>   

                                                                    <table class="table" id="story_configuration">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>Wohneinheit</th>
                                                                                <th>Geschoß</th>
                                                                                <th>Nutzfläche</th>
                                                                                <th>Beheizte Wohnfläche</th>
                                                                                <th>Wohnfläche <code>m²</code></th>
                                                                                <th>Aktion</th> 
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="story_tbody">
                                                                            <tr>
                                                                                
                                                                            </tr>
                                                                        </tbody>
                                                                    </table> 
                                                                    <button type="button" class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light float-left" id="save_story">
                                                                        <i class="feather icon-save"></i> Wohnung speichern
                                                                    </button>   
                                                                    <button type="button" class="btn btn-icon btn-primary mr-1 mb-1 waves-effect waves-light float-left">
                                                                        <i class="feather icon-arrow-down"></i> Details
                                                                    </button>   
                                                                </div>
 
                                                                <div class="table-responsive"> 
                                                                    <table class="table" id="story_details">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>#</th>
                                                                                <th>Wohneinheit</th> 
                                                                                <th>Geschoß</th>
                                                                                <th>Nutzfläche <code>m²</code></th>
                                                                                <th>Beheizte Wohnfläche	 <code>m²</code></th>
                                                                                <th>Wohnfläche <code>m²</code></th>
                                                                                <th>Aktion</th> 
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                  
                                                                            </tr>  
                                                                        </tbody>  
                                                                    </table> 

                                                                    <!-- Edit Story Modal -->
                                                                        <div class="modal fade" id="editStoryModal" tabindex="-1" aria-labelledby="editStoryModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="editStoryModalLabel">Edit Story</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <form>
                                                                                    <div class="form-group">
                                                                                        <label for="story">Story</label>
                                                                                        <input type="text" class="form-control" id="story">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="unit">Unit</label>
                                                                                        <input type="text" class="form-control" id="unit">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="living_space">Nutzfläche</label>
                                                                                        <input type="text" class="form-control" id="usable_space">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="living_space">Beheizte Wohnfläche</label>
                                                                                        <input type="text" class="form-control" id="heating_living_space">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label for="living_space">Wohnfläche</label>
                                                                                        <input type="text" class="form-control" id="living_space">
                                                                                    </div>
                                                                                    </form>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                                                                                </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                </div> 

                                                                <div class="col-12"><hr></div>
                                                                   <h2 class="bold section_title">Raumverwaltung</h2> 
                                                                    <div class="table-responsive">
                                                                      <button type="button" class="btn btn-icon btn-icon   btn-primary mr-1 mb-1 waves-effect waves-light float-right" id="add_room"><i class="feather icon-plus"></i>Neuraum</button>   

                                                                        <table class="table" id="roomConfiguration">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>#</th>
                                                                                    <th>Wohneinheit</th>  
                                                                                    <th>Raumname</th> 
                                                                                    <th>Fläche <code>m²</code></th>
                                                                                    <th>Heizungsart</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                 
                                                                            </tbody>
                                                                        </table>
                                                                       <button type="button" class="btn btn-icon btn-icon   btn-primary mr-1 mb-1 waves-effect waves-light float-left" id="save_room" ><i class="feather icon-save"></i> Raum speichern</button>   

                                                                        
                                                                </div>
                                                                <div class="table-responsive">
                                                                <table class="table" id="roomDetails">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Geschoß</th>  
                                                                            <th>Raumname</th> 
                                                                            <th>Fläche <code>m²</code></th>
                                                                            <th>Heizungsart</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        
                                                                    </tbody>
                                                                </table>
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

        // Function to count and gather selected heart icons' product IDs
        function getSelectedProductIds() {
            const selectedProducts = [];
            // Loop through each selected heart-icon and get the corresponding product ID
            document.querySelectorAll('.products').forEach((card) => {
                const heartIcon = card.querySelector('.heart-icon');
                if (heartIcon && heartIcon.classList.contains('selected')) {
                    const productId = card.dataset.productId;
                    selectedProducts.push(productId); // Add product ID to the array
                }
            });

            console.log('Selected product IDs:', selectedProducts);
            return selectedProducts;
        }

        document.querySelectorAll('.products').forEach((card, index) => {
            const checkbox = card.querySelector('input[type="checkbox"]');
            const statusSpan = card.querySelector('#interested-' + index);
            const heartButton = document.getElementById(index + 'Like');
            const heartIcon = heartButton ? heartButton.querySelector('.heart-icon') : null; // Ensure heartIcon exists
            const menuButton = document.getElementById(index + 'MenuButton');
            const details = document.getElementById(index + 'show_details');

            // Set initial visibility based on checkbox state
            if (checkbox.checked && heartIcon) {
                heartIcon.classList.add('selected');
                heartButton.classList.remove('btn-light');
                heartButton.classList.add('btns-primary');
            } else if (heartIcon) {
                heartIcon.classList.remove('selected');
                heartButton.classList.remove('btns-primary');
                heartButton.classList.add('btn-light');
            }

            heartButton.addEventListener('click', (event) => {
                checkbox.checked = !checkbox.checked;
                card.classList.toggle('selected', checkbox.checked);

                // Update UI and status
                if (checkbox.checked && heartIcon) {
                    statusSpan.innerHTML = 'Interessiert';
                    heartIcon.classList.add('selected');
                    heartButton.classList.remove('btn-light');
                    heartButton.classList.add('btns-primary');
                } else if (heartIcon) {
                    statusSpan.innerHTML = '';
                    heartIcon.classList.remove('selected');
                    heartButton.classList.remove('btns-primary');
                    heartButton.classList.add('btn-light');
                }

                // Send AJAX request to store the product status
                const customerId = document.querySelector('#customerId').value;
                const productId = card.dataset.productId;

                fetch('/customer_product_save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        customer_id: customerId,
                        product_id: productId,
                        interested: checkbox.checked // Send the interested status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Product status saved:', data);
                    toastr.success('Das Produkt wurde erfolgreich gespeichert');
                })
                .catch(error => {
                    console.error('Error saving product status:', error);
                    toastr.error('Fehler beim Speichern der Daten', error);

                });

                event.stopPropagation(); // Prevent other click events from causing page scroll

                // Call function to get all selected product IDs and log them
                getSelectedProductIds();
            });

            // Prevent default focus scroll behavior
            document.addEventListener('focusin', (event) => {
                if (event.target.closest('.products')) {
                    event.preventDefault();
                }
            });
        });

        // Initial log of selected product IDs when the page loads
        getSelectedProductIds();
    });
</script>




<!-- {{-- Google Map API --}} -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
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
    <script>
        document.getElementById('screenshot-btn').addEventListener('click', function() {
            html2canvas(document.querySelector('#gmp-map')).then(canvas => {
                let dataURL = canvas.toDataURL('image/png');
                // Find the image input element and set its value
                let imageInput = document.getElementById('image-input');
                if (imageInput) {
                    imageInput.value = dataURL; // Assign the data URL to the input element
                } else {
                    console.error('File input element not found');
                }
            });
        });
    </script>

<script src="{{ asset('js/select2.min.js') }}"></script>

<!-- {{-- show and Hide the Alternative Address Section --}} -->
<script>
    function toggleAlternativeAddress() {
        var alternativeAddressDiv = document.getElementById("alternative_address");
        if (alternativeAddressDiv.style.display === "none" || alternativeAddressDiv.style.display === "") {
            alternativeAddressDiv.style.display = "flex";
            alternativeAddressDiv.style.flexWrap = "wrap";
        } else {
            alternativeAddressDiv.style.display = "none";
        }
    }
</script>

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
        function initializeSelect2() {
                    $('select').select2({
                        width: '100%',
                        placeholder: 'Wählen Sie eine Option',
                        allowClear: true,
                    });
                }

                // Call initializeSelect2 initially to apply to all selects
                initializeSelect2();
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
                                        <img src="{{ asset('images/roofs/Satteldach.png') }}" alt="" srcset="" style="width:150px;" for="roof_Satteldach_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Satteldach_${roofIndex}" value="Satteldach" checked>
                                            <label class="custom-control-label" for="roof_Satteldach_${roofIndex}">Satteldach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Flachdach.png') }}" alt="" srcset="" style="width:150px;" for="roof_Flachdach_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Flachdach_${roofIndex}" value="Flachdach">
                                            <label class="custom-control-label" for="roof_Flachdach_${roofIndex}">Flachdach</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Garage.png') }}" alt="" srcset="" style="width:150px;" for="roof_Garage_${roofIndex}">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="roof[${roofIndex}]" id="roof_Garage_${roofIndex}" value="Garage">
                                            <label class="custom-control-label" for="roof_Garage_${roofIndex}">Garage</label>
                                        </div>
                                    </fieldset>
                                </li>
                                <li class="d-inline-block mr-1">
                                    <fieldset>
                                        <img src="{{ asset('images/roofs/Carport.png') }}" alt="" srcset="" style="width:150px;" for="roof_Carport_${roofIndex}">
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


<!-- WP Checklist: adding room Measurement: start  -->
 <script>
    var j = 0;
    // Add a new room dimension row
    $('#room_dimension_table').on('click', '.btn-warning', function(){
        ++j;
        $('#room_dimension_table tbody').append(
            '<tr>' +
                '<th scope="row">' +
                    '<input type="text" name="room['+j+'][room_number]" class="form-control"  value="'+(j+1)+'" readonly>' +
                '</th>' +
                '<td>' +
                    '<input type="hidden" name="customer_id" value="{{request()->id}}">' +
                    '<input type="hidden" name="address_no" value="{{request()->address_no}}">' +
                    '<input type="text" class="form-control" placeholder="Breite" name="room['+j+'][door_dimension_width]">' +
                '</td>' +
                '<td>' +
                    '<input type="text" class="form-control" placeholder="Höhe" name="room['+j+'][door_dimension_height]">' +  
                '</td>' +
                '<td>' +
                    '<input type="text" class="form-control" placeholder="Deckenhöhe" name="room['+j+'][ceiling height]">' +
                '</td>' +
                '<td>' +
                    '<select name="room['+j+'][stair_form]" id="" class="form-control">' +
                        '<option  ></option>' +
                        '<option value="L-Form">L-Form</option>' +
                        '<option value="U-Form">U-Form</option>' +
                        '<option value="Wendel">Wendel</option>' +
                        '<option value="Gradeluäfig">Gradeluäfig</option>' +
                    '</select>'+
                '</td>'+
               ' <td>' +
                    '<input type="text" class="form-control" placeholder="Treppe Breite" name="room['+j+'][stair_width]">' +
               ' </td>' +
                '<td>' +
                    '<select name="room['+j+'][room_story]" class="form-control">' +
                        '<option></option>' +
                        '<option value="KG">KG</option>' +
                        '<option value="EG">EG</option>' +
                        '<option value="OG">OG</option>' +
                        '<option value="DG">DG</option>' +
                    '</select>' +
                '</td>' +
                '<td>' +
                    '<button type="button" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1 remove-room-row"><i class="fa fa-trash"></i></button>' +
                '</td>' +
            '</tr>'
        );
    });

    // Remove room dimension row
    $(document).on('click', '.remove-room-row', function(){
        $(this).closest('tr').remove();
    });
</script>

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




<!-- WP Section: Mehrfamilienhouse Part: adding new story and room details: start  -->
<script>
 
document.getElementById('add_new_story').addEventListener('click', function() {
    // Get the table body where the new rows should be added
    const tableBody = document.getElementById('story_tbody');
    const rowCount = tableBody.getElementsByTagName('tr').length;
    const newRow = document.createElement('tr');

    // Create columns for the new row
    const numberCell = document.createElement('th');
    numberCell.scope = 'row';
    numberCell.innerHTML = `<input type="text" name="mfh[${rowCount}][number]" class="form-control" value="${rowCount + 1}">`;

    const storyCell = document.createElement('td');
    storyCell.innerHTML = `<input type="text" name="mfh[${rowCount}][story]" class="form-control">`;

    const unitNumberCell = document.createElement('td');
    unitNumberCell.innerHTML = `<input type="text" name="mfh[${rowCount}][unit_number]" class="form-control">`;

    const nutzflacheCell = document.createElement('td');
    nutzflacheCell.innerHTML = `<input type="text" name="mfh[${rowCount}][usable_space]" class="form-control nutzflache" data-row="${rowCount}" placeholder="Nutzfläche">`;

    const beheizteWohnflacheCell = document.createElement('td');
    beheizteWohnflacheCell.innerHTML = `<input type="text" name="mfh[${rowCount}][heating_living_space]" class="form-control beheizte_wohnflache" data-row="${rowCount}" placeholder="Beheizte Wohnfläche">`;

    const wohnflacheCell = document.createElement('td');
    wohnflacheCell.innerHTML = `<input type="text" name="mfh[${rowCount}][living_space]" class="form-control wohnflache" data-row="${rowCount}" readonly placeholder="Wohnfläche">`;

    const actionCell = document.createElement('td');
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-icon btn-outline-danger mr-1 mb-1 waves-effect waves-light float-right delete_story';
    deleteButton.innerHTML = '<i class="feather icon-trash"></i> Löschen';

    // Add event listener to delete the row
    deleteButton.addEventListener('click', function() {
        tableBody.removeChild(newRow);
    });

    actionCell.appendChild(deleteButton);

    // Append cells to the new row
    newRow.appendChild(numberCell);
    newRow.appendChild(storyCell);
    newRow.appendChild(unitNumberCell);
    newRow.appendChild(nutzflacheCell);
    newRow.appendChild(beheizteWohnflacheCell);
    newRow.appendChild(wohnflacheCell);
    newRow.appendChild(actionCell);

    // Append the new row to the table body
    tableBody.appendChild(newRow);

    // Add event listeners to calculate "Wohnfläche" (living area)
    calculateWohnflache(rowCount);
});

// Function to calculate "Wohnfläche"
function calculateWohnflache(row) {
    const nutzflacheInput = document.querySelector(`input[name="mfh[${row}][usable_space]"]`);
    const beheizteWohnflacheInput = document.querySelector(`input[name="mfh[${row}][heating_living_space]"]`);
    const wohnflacheInput = document.querySelector(`input[name="mfh[${row}][living_space]"]`);

    // Debugging: Check if the beheizteWohnflacheInput is correctly selected
    console.log(`Row ${row}:`, {
        nutzflacheInput,
        beheizteWohnflacheInput,
        wohnflacheInput
    });

    function updateWohnflache() {
        const nutzflache = parseFloat(nutzflacheInput.value) || 0;
        const beheizteWohnflache = parseFloat(beheizteWohnflacheInput.value) || 0;

        console.log(`Nutzfläche = ${nutzflache}, Beheizte Wohnfläche = ${beheizteWohnflache}`); // Debug log

        const totalWohnflache = nutzflache + beheizteWohnflache;

        // Debugging: Check if the final calculation is correct
        console.log(`Row ${row}: Total Wohnfläche = ${totalWohnflache}`);

        // Update the "Wohnfläche" input
        wohnflacheInput.value = totalWohnflache.toFixed(2);
    }

    // Add event listeners to update "Wohnfläche" when values change
    nutzflacheInput.addEventListener('input', updateWohnflache);
    beheizteWohnflacheInput.addEventListener('input', updateWohnflache);
}
</script>



<!-- WP Section: Mehrfamilienhouse Part: adding new story and room details: End  -->

<!-- saving new apartment :start -->
<script>
    $(document).ready(function() {
    // Function to fetch and display data in the table
    function fetchStoryData() {
        var customer_id = $('input[name="customer_id"]').val();
        var address_no = $('input[name="address_no"]').val();

        $.ajax({
            url: '/checklist_apartment_get/' + customer_id + '/' + address_no,
            type: 'GET',
            success: function(response) {
                $('#story_details tbody').empty(); // Clear table
                $.each(response, function(index, item) {
                    $('#story_details tbody').append(
                        `<tr data-id="${item.id}">
                            <th scope="row">${item.id}</th>
                            <td>${item.story}</td>
                            <td>${item.unit}</td>
                            <td>${item.heating_living_space}</td>
                            <td>${item.usable_space}</td>
                            <td>${item.living_space}</td>
                            <td>
                                <button type="button" class="btn btn-icon btn-danger delete-btn" data-id="${item.id}">
                                    <i class="feather icon-trash"></i>
                                </button>
                                <button type="button" class="btn btn-icon btn-primary update-btn" data-id="${item.id}">
                                    <i class="feather icon-edit"></i>
                                </button>
                            </td>
                        </tr>`
                    );
                });
                attachDeleteFunction();
                attachUpdateFunction();
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('An error occurred while fetching data.');
            }
        });
    }

    // Save button click function
    $('#save_story').click(function() {
        let stories = [];

        $('#story_configuration tbody tr').each(function(index, row) {
            let story = $(row).find('input[name="mfh[' + index + '][story]"]').val();
            let unitNumber = $(row).find('input[name="mfh[' + index + '][unit_number]"]').val();
            let usableSpace = $(row).find('input[name="mfh[' + index + '][usable_space]"]').val();
            let heatedLivingSpace = $(row).find('input[name="mfh[' + index + '][heating_living_space]"]').val();
            let livingSpace = $(row).find('input[name="mfh[' + index + '][living_space]"]').val();

            // Debugging: Log the values
            console.log(`Row ${index}:`, {
                story,
                unitNumber,
                usableSpace,
                heatedLivingSpace,
                livingSpace
            });

            if (story && unitNumber && usableSpace && heatedLivingSpace && livingSpace) {
                stories.push({
                    story: story,
                    unit: unitNumber,
                    usable_space: usableSpace,
                    heating_living_space: heatedLivingSpace,
                    living_space: livingSpace
                });
            } else {
                console.warn(`Row ${index} has missing data and will be skipped.`);
            }
        });

        if (stories.length === 0) {
            toastr.error('Keine gültigen Daten zum Speichern vorhanden.');
            console.error('No valid data to save.');
            return;
        }

        console.log('Stories data to be sent:', stories);

        $.ajax({
            url: "{{ route('checklist.apartment.save') }}", 
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                stories: stories,
                customer_id: $('input[name="customer_id"]').val(),
                address_no: $('input[name="address_no"]').val()
            },
            success: function(response) {
                console.log('Server Response:', response);
                toastr.success('Die Wohnung wurde erfolgreich gespeichert');
                fetchStoryData();
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.status, xhr.statusText);
                console.error('Response Text:', xhr.responseText);

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        toastr.error(value[0]);
                        console.error(`Validation Error: ${key} - ${value[0]}`);
                    });
                } else {
                    toastr.error('Fehler beim Speichern der Daten.');
                }
            }
        });
    });

     // Function to handle delete button click
    function attachDeleteFunction() {
        $('.delete-btn').click(function() {
            var storyId = $(this).data('id'); // Get the story ID from the button data-id attribute

            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: '/checklist_apartment_delete/' + storyId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('The data was deleted successfully');
                            $('tr[data-id="' + storyId + '"]').remove(); // Remove the row from the table
                        } else {
                            toastr.error(response.error);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('An error occurred while deleting the data.');
                    }
                });
            }
        });
    }

   
    // Function to handle update button click
    function attachUpdateFunction() {
        $('#story_details').on('click', '.update-btn', function() {
            var storyId = $(this).data('id'); // Get the story ID from the button data-id attribute

            // Fetch the current row's data
            var row = $('tr[data-id="' + storyId + '"]');
            var story = row.find('td:eq(0)').text();
            var unit = row.find('td:eq(1)').text();
            var heating_living_space = row.find('td:eq(2)').text();
            var usable_space = row.find('td:eq(3)').text();
            var living_space = row.find('td:eq(4)').text();

            // Populate the modal with the current data
            $('#editStoryModal #story').val(story);
            $('#editStoryModal #unit').val(unit);
            $('#editStoryModal #heating_living_space').val(heating_living_space);
            $('#editStoryModal #usable_space').val(usable_space);
            $('#editStoryModal #living_space').val(living_space);

            // Add event listeners to sum usable_space + heating_living_space and update living_space
            function updateLivingSpace() {
                const usableSpace = parseFloat($('#editStoryModal #usable_space').val()) || 0;
                const heatingLivingSpace = parseFloat($('#editStoryModal #heating_living_space').val()) || 0;
                const totalLivingSpace = usableSpace + heatingLivingSpace;
                $('#editStoryModal #living_space').val(totalLivingSpace.toFixed(2));
            }

            // Attach the event listeners to recalculate the total living space when inputs change
            $('#editStoryModal #usable_space, #editStoryModal #heating_living_space').on('input', updateLivingSpace);

            // Trigger the calculation once to show the initial value
            updateLivingSpace();

            $('#editStoryModal').data('story-id', storyId); // Store the story ID in the modal
            $('#editStoryModal').modal('show');
        });

        // Handle save changes in the modal
        $('#saveChanges').click(function() {
            var storyId = $('#editStoryModal').data('story-id'); // Retrieve the story ID from the modal
            var updatedData = {
                _token: "{{ csrf_token() }}",
                story: $('#editStoryModal #story').val(),
                unit: $('#editStoryModal #unit').val(),
                heating_living_space: $('#editStoryModal #heating_living_space').val(),
                usable_space: $('#editStoryModal #usable_space').val(),
                living_space: $('#editStoryModal #living_space').val()
            };

            $.ajax({
                url: '/checklist_apartment_update/' + storyId,
                type: 'POST',
                data: updatedData,
                success: function(response) {
                    toastr.success('The data was updated successfully');
                    fetchStoryData(); // Refresh the data after updating
                    $('#editStoryModal').modal('hide'); // Close the modal
                },
                error: function(xhr) {
                    toastr.error('An error occurred while updating the data.');
                }
            });
        });
    }
    // Fetch data on load
    fetchStoryData();

    // Delete and update functions can be added similarly...
});

</script>


 
<!-- saving new apartment :End -->


<!-- Adding New Rooms: start  -->
   
 <script>
   $(document).ready(function() {
    let customer_id = '{{$customer->id}}'; // Assuming customer_id is available
    let rowCount = 1; // Keep track of the number of rows

    // Function to fetch story data for dropdown
    function fetchStoryData(selectElement) {
        var address_no = $('input[name="address_no"]').val();

        console.log("Fetching story data for customer:", customer_id, "address:", address_no);

        $.ajax({
            url: '/checklist_apartment_get/' + customer_id + '/' + address_no,
            type: 'GET',
            success: function(response) {
                console.log("Story data fetched:", response);
                $(selectElement).empty(); // Clear previous options
                $(selectElement).append('<option selected>Wählen...</option>');
                $.each(response, function(index, item) {
                    $(selectElement).append('<option value="' + item.id + '">' + item.story + '</option>');
                });
            },
            error: function(xhr) {
                console.error('Error fetching story data:', xhr.responseText);
            }
        });
    }

    // Add new room row
    $('#add_room').click(function() {
        rowCount++; // Increment row count

        console.log("Adding new room row:", rowCount);

        let newRow = `
            <tr>
                <th scope="row">${rowCount}</th>
                <td>
                    <select name="room[${rowCount}][story_id]" class="form-control story-select">
                        <!-- Story options populated by AJAX -->
                    </select>
                </td>
                <td>
                    <select name="room[${rowCount}][unit]" class="form-control">
                        <option selected>Wählen...</option>
                        <option value="Keller">Keller</option>
                        <option value="Abstellraum">Abstellraum</option>
                        <option value="Bad">Bad</option>
                        <option value="Kinderzimmer">Kinderzimmer</option>
                        <option value="Wohnzimmer">Wohnzimmer</option>
                        <option value="Esszimmer">Esszimmer</option>
                        <option value="Gästezimmer">Gästezimmer</option>
                        <option value="Flur">Flur</option>
                        <option value="Dach">Dach</option>
                        <option value="sontiges">Sontiges</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="room[${rowCount}][room_size]" class="form-control">
                </td>
                <td>
                    <select name="room[${rowCount}][heating_type]" class="form-control">
                        <option selected>Wählen...</option>
                        <option value="underfloor heating">Fußbodenheizung</option>
                        <option value="radiator">Heizkörper</option>
                        <option value="underfloor heating and radiator">Fußbodenheizung + Heizkörper</option>
                        <option value="none">Keine</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-room">
                        <i class="feather icon-trash"></i> Entfernen
                    </button>
                </td>
            </tr>`;

        // Append new row to the table
        $('#roomConfiguration tbody').append(newRow);

        // Fetch story data for the new row
        fetchStoryData($('select[name="room[' + rowCount + '][story_id]"]'));
    });

    // Remove a room row
    $('#roomConfiguration').on('click', '.remove-room', function() {
        console.log("Removing room row");
        $(this).closest('tr').remove(); // Remove the row
    });

    // Save room data
    $('#save_room').click(function() {
        let roomData = [];

        // Loop through each row and gather the data
        $('#roomConfiguration tbody tr').each(function(index, row) {
            // Use generic selectors to find the inputs and selects
            let story_id = $(row).find('select[name^="room"][name$="[story_id]"]').val();
            let unit = $(row).find('select[name^="room"][name$="[unit]"]').val();
            let room_size = $(row).find('input[name^="room"][name$="[room_size]"]').val();
            let heating_type = $(row).find('select[name^="room"][name$="[heating_type]"]').val();

            console.log(`Row ${index + 1}:`, { story_id, unit, room_size, heating_type });

            // Ensure at least story_id and room_size are filled
            if (story_id && room_size) {
                roomData.push({
                    customer_id: customer_id,  // Include customer_id
                    story_id: story_id,
                    unit: unit,
                    room_size: room_size,
                    heating_type: heating_type
                });
            }
        });

        if (roomData.length === 0) {
            toastr.error("Keine gültigen Daten zum Speichern vorhanden.");
            console.error("No valid data to save.");
            return; // Stop the process if no valid data
        }

        console.log("Room data to be saved:", roomData);

        // Send the gathered data via AJAX to save it
        $.ajax({
            url: "{{ route('checklist.room.save') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                room: roomData // Ensure the key is 'room'
            },
            success: function(response) {
                toastr.success('Die Räume wurden erfolgreich gespeichert');
                console.log("Rooms saved successfully:", response);
                fetchRoomData(); // Refresh room data
            },
            error: function(xhr) {
                toastr.error('Ein Fehler ist beim Speichern der Daten aufgetreten.');
                console.error('Error saving room data:', xhr.responseText);
            }
        });
    });

    // Fetch and populate room data
    function fetchRoomData() {
        $.ajax({
            url: '/checklist_room_get/' + customer_id,
            type: 'GET',
            success: function(response) {
                console.log("Fetched room data:", response);
                $('#roomDetails tbody').empty(); // Clear existing rows
                $.each(response, function(index, item) {
                    $('#roomDetails tbody').append(
                        `<tr data-id="${item.id}">
                            <th scope="row">${index + 1}</th>
                            <td>${item.story}</td>
                            <td>${item.unit}</td>
                            <td>${item.room_size}</td>
                            <td>${item.heating_type}</td>
                            <td>
                                <button type="button" class="btn btn-danger delete-room" data-id="${item.id}">
                                    <i class="feather icon-trash"></i> Löschen
                                </button>
                                <a type="button" class="btn btn-primary" href="{{ url('checklist_room_details') }}/${item.id}/{{$customer->id}}">
                                    <i class="feather icon-settings"></i> Manage Checklist
                                </a>
                            </td>
                        </tr>`
                    );
                });
            },
            error: function(xhr) {
                toastr.error('Fehler beim Abrufen der Räume.');
                console.error('Error fetching room data:', xhr.responseText);
            }
        });
    }

    // Load room data on page load
    fetchRoomData();

    // Handle delete room functionality
    $('#roomDetails').on('click', '.delete-room', function() {
        let roomId = $(this).data('id');

        if (confirm('Möchten Sie diesen Raum wirklich löschen?')) {
            console.log("Deleting room:", roomId);

            $.ajax({
                url: '/checklist_room_delete/' + roomId,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    toastr.success('Der Raum wurde erfolgreich gelöscht');
                    fetchRoomData(); // Refresh room data after deletion
                },
                error: function(xhr) {
                    toastr.error('Ein Fehler ist beim Löschen der Daten aufgetreten.');
                    console.error('Error deleting room:', xhr.responseText);
                }
            });
        }
    });
});

 </script>
<!-- WP Check list, saving the room: End  -->


@endsection