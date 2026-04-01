@extends('admin.layouts.app')

@section('title') PHOTOVOLTAIK @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>

    .dropdown-menu.show {
        display: block;
    }
    body {
        margin: 0;
    }

    h4 {
        font-size: 1rem !important;
    }

    h3 {

        font-size: 1rem !important;
    }

    .title {
       font-size: 17px !important;
        font-weight: bold !important;
    }

    .product_card {
        border-radius: 71px;
        background: #cfe09b!important;
    }

    #product_card_details {
        background: #95c11f!important;
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
        background: #cfe09b !important;

    }



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
        height: 70px !important;
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

    hr {
        border: 2px solid #73b1d4 !important;
    }
    .normal {
        border: 1px solid #d8d8d8 !important;
    }

    .tab-control {
        background: transparent !important;
        font-size: 24px !important;
        font-weight: bold !important;
        border-top: 3px solid #73b1d4;
        border-bottom: 3px solid #73b1d4;
        color: #95c11f !important;
    }
    .tab-control .active {
    color: #73b1d4 !important;
    background: transparent !important;
    font-size: 24px !important;
    font-weight: bold !important;
    }
    .titles {
        font-size: 23px !important;
        color: #73b1d4 !important;
        font-weight: bold !important;
        }
    @media (min-width: 766) {
    .right-border {
    border-right: 1px solid #d7d0d0;
    border-width: thin;
    }
    }

    label {
        line-height: 2 !important;
    }

    #accordion {
        font-size: 32px;
        color: #73b1d4;
        transform: rotate(90deg);
        position: static;
        float: right;
        margin-top: -24px;
    }

      .heart-radio {
            font-size: 1.5rem;
            cursor: pointer;
            color: grey;
            transition: color 0.3s ease;
        }
        .heart-radio.active {
            color: #94c11f;
        }
        .hidden-radio {
            display: none;
        }


</style>
@endsection

@section('content')

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDEN INFORMATION</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('customer_product_details/'.$customer->id.'/'.$customer->postcode.'/'.$alternative->address_no)}}">PRODUCT</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $article->article_group }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle waves-effect waves-light"
                            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="feather icon-settings"></i></button>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a
                                class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body"> 
            <!-- information details -->
            <div class="col-12">
                <hr>
            </div>
            <section id="content-types">
                <div class="row match-height">

                        <div class="col-md-3 col-12" >
                            <div class="card-content">
                                <div class="card-body">
                                    <h3 class="title primary">KUNDE</h3>
                                    <p class="card-text">
                                        {{ $customer->name }} {{ $customer->lastname }}  <br>
                                        {{ $customer->street }} <br>
                                        {{ $customer->postcode }}, {{ $customer->city }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-12">
                            <div class="card-content">
                                <div class="card-body">
                                    <h3 class="title primary">BAUVORHABEN</h3>
                                    <p class="card-text">
                                    @if($alternative)
                                      {{ $alternative->street }} <br>
                                    {{ $alternative->postcode }}, {{ $alternative->city }}
                                    @else
                                    {{ $customer->street }} <br>
                                    {{ $customer->postcode }}, {{ $customer->city }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12 col-12">
                            <article style="display: flex; align-items: center;">
                                <div class="text-center bg-transparent products mt-1 mb-1 col-10" style="">
                                    <div class="card-content ">
                                        <div class="row product_card">
                                            <div class="col-md-2 col-2" id="product_card_image">
                                                <img src="{{ asset('images/articles/'.$article->image) }}" alt="{{ $article->article_group }}"
                                                    style="width: 71px !important;" class="float-left  mt-2">
                                            </div>

                                            <div class="col-md-10 col-10" id="product_card_details">
                                                <h2 class="card-title mt-1 mb-0 white title"> {{ $article->article_group }}</h2>
                                                <p class="card-text"><a href="#" class="white">Projektdaten</a>
                                                    | <a href="#" class="white">Arbeitsschritte</a></p>
                                                <p class="card-text white mb-1"> Aktualler Status: <span id="interested">Interesse</span>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="settings col-2">
                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light"
                                        style="width: 50px;height: 50px; display:none" id=" Button"><i class="feather icon-heart"></i>
                                    </button>

                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light"
                                        id=" MenuButton" style="width: 50px;height: 50px; display:none" onclick=" "><i
                                            class="feather icon-align-justify"></i>
                                    </button>
                                </div>
                            </article>
                        </div>

                        <div class="col-md-2 col-12" style="display:flex;">
                            @foreach ($article_icon as $ar_icon)
                            <a type="button" class="btn btn-icon btn-icon  rounded-circle btn-primary mr-1 mb-1" id="inactive"
                            style="height: 40px;  width: 40px; background:primary !important;">
                            <span style="font-size: 10px;font-weight: bold; color:white; margin:0;font-family: sans-serif !important;">{{ $ar_icon->initial }}</span>
                            </a>
                            @endforeach


                        </div>

                </div>
            </section>
            <!-- information details -->

            <section id="nav-justified">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="overflow-hidden">
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">

                                        <li class="nav-item tab-control">
                                            <a class="nav-link tab-control active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab"
                                                aria-controls="home-just" aria-selected="true">CHECKLISTE - BEARBEITEN</a>
                                        </li>
                                   
                                    </ul>
                                <form method="post" action="{{ route('customer.product.details.update') }}">
                                    @csrf
                                    <!-- Tab panes -->
                                    <div class="tab-content pt-1">
                                        <div class="tab-pane active" id="home-just" role="tabpanel"
                                            aria-labelledby="home-tab-justified">
                                            <article>
                                                <div class="col-md-12" style="display:flex;">
                                                    <div class="col-md-1 ">
                                                        <span>Intention</span>
                                                    </div>
                                                    <div class="col-md-7">
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="intention" id="intention_interest" {{ $pv_checklist->intention == 'Interesse' ? 'checked' : '' }}
                                                                            value="Interesse">
                                                                        <label class="custom-control-label" for="intention_interest">Interesse</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="intention" id="intention_available" {{ $pv_checklist->intention == 'vorhanden' ? 'checked' : '' }}
                                                                            value="vorhanden">
                                                                        <label class="custom-control-label" for="intention_available">vorhanden</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="intention" id="intention_extension" {{ $pv_checklist->intention == 'Erweiterung' ? 'checked' : '' }}
                                                                            value="Erweiterung">
                                                                        <label class="custom-control-label" for="intention_extension">Erweiterung</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input" name="intention" id="intention_spater" 
                                                                            value="später">
                                                                        <label class="custom-control-label" for="intention_spater">später</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                            <li class="d-inline-block mr-1">
                                                                <fieldset>
                                                                    <div class="custom-control custom-radio">
                                                                        <input type="radio" class="custom-control-input danger" name="intention" id="intention_absage" value="Absage">
                                                                        <label class="custom-control-label" for="intention_absage">Absage</label>
                                                                    </div>
                                                                </fieldset>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                     
                                                 </div>
                                            </article>
                                            <hr>

                                            <div class="col-md-12" style="display: flex !important;  flex-wrap: wrap; align-items: center;">
                                                <section class="col-md-8 right-border">
                                                    <div class="cards">
                                                        <div class="card-title">
                                                            <h4 class=" " style="color: #73b1d4;font-size: 24px !important;  font-weight: bold; ">KURZ-CHECKLISTE</h4>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>
                                            <article>
                                                <div class="col-md-12" style="display: flex !important;  flex-wrap: wrap; align-items: center;">
                                                    <section class="col-md-8 right-border">
                                                        <div class="cards"> 
                                                            <div class="card-body" style="display: flex !important;flex-wrap: wrap;">
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <h4 class="title">Objektart</h4>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="property_type" id="objective_EFH" value="EFH" {{ $pv_checklist->property_type == 'EFH' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="objective_EFH">EFH</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="property_type" id="objective_MFH" value="MFH" {{ $pv_checklist->property_type == 'MFH' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="objective_MFH">MFH</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="property_type" id="objective_Neubau" value="Neubau" {{ $pv_checklist->property_type == 'Neubau' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="objective_Neubau">Neubau</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="property_type" id="objective_Sanierung" value="Sanierung" {{ $pv_checklist->property_type == 'Sanierung' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="objective_Sanierung">Sanierung</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="property_type" id="objective_Einzelmassnahmen" value="Einzelmaßnahmen" {{ $pv_checklist->property_type == 'Einzelmaßnahmen' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="objective_Einzelmassnahmen">Einzelmaßnahmen</label>
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
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control" name="number_of_units" value="{{ $pv_checklist->number_of_units ?? '' }}" >
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Anzahl Zähler</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control" name="number_of_meters" value="{{ $pv_checklist->number_of_meters ?? '' }}" >
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-6">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">Stromverbrauch</h4>
                                                                        </div>
                                                                        <div class="col-md-8 flex_me">
                                                                            <input type="text" class="form-control" name="electricity_consumption" value="{{ $pv_checklist->electricity_consumption ?? '' }}" >&nbsp;<span>kWh</span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <h4 class="bold">E-Auto</h4>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="electric_car" id="e_auto_no" value="nein" {{ $pv_checklist->electric_car == 'nein' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="e_auto_no">nein</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="electric_car" id="e_auto_yes" value="ja" {{ $pv_checklist->electric_car == 'ja' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="e_auto_yes">ja</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1" style="width:330px">
                                                                                    <div class="form-group row">
                                                                                        <div class="col-md-4">
                                                                                            <h4 class="bold">Anzahl</h4>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" class="form-control" name="number_of_electric_cars" value="{{ $pv_checklist->number_of_electric_cars ?? '' }}" >
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
                                                                            <h4 class="bold">Wallbox gewünscht</h4>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="wallbox_desired" id="wall_box_no" value="nein" {{ $pv_checklist->wallbox_desired == 'nein' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="wall_box_no">nein</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="wallbox_desired" id="wall_box_yes" value="ja" {{ $pv_checklist->wallbox_desired == 'ja' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="wall_box_yes">ja</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1" style="width:330px">
                                                                                    <div class="form-group row">
                                                                                        <div class="col-md-4">
                                                                                            <h4 class="bold">Anzahl</h4>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" class="form-control" name="number_of_wallboxes" value="{{ $pv_checklist->number_of_wallboxes ?? '' }}" >
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section> 
                                                </div>
                                            </article>
                                                <hr>
                                            @foreach ($pv_roof as $index => $roof)
                                                <article>
                                                    <section class="col-md-12 dynamic-section flex_me" id="section_3">
                                                        <div class="col-md-6">
                                                            <div class="cards">
                                                                <div class="card-body" style="display: flex !important; flex-wrap: wrap;">
                                                                    <div class="col-12">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-2">
                                                                                <h4 class="bold">Dach {{ $index + 1 }}</h4>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <span>Bezeichnung</span>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <input type="text" class="form-control" name="designation[{{ $index }}]" value="{{ $roof->designation }}" >
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button type="button" id="add_more" class="btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light">
                                                                                    <i class="feather icon-plus"></i>
                                                                                </button>
                                                                                <button type="button" class="remove_roof btn btn-icon btn-icon rounded-circle btn-light mr-1 mb-1 waves-effect waves-light d-none">
                                                                                    <i class="feather icon-minus"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12" style="margin-bottom: 40px;">
                                                                        <div class="form-group row">
                                                                            <div class="col-md-12">
                                                                                <ul class="list-unstyled mb-0">
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <img src="{{ asset('images/roofs/Satteldach.png') }}" alt="" srcset="" style="width:100px;" for="roof_Satteldach_{{ $index }}">
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input" name="roof[{{ $index }}]" id="roof_Satteldach_{{ $index }}" value="Satteldach" {{ $roof->roof == 'Satteldach' ? 'checked' : '' }}>
                                                                                                <label class="custom-control-label" for="roof_Satteldach_{{ $index }}">Satteldach</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <img src="{{ asset('images/roofs/Flachdach.png') }}" alt="" srcset="" style="width:100px;" for="roof_Flachdach_{{ $index }}">
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input" name="roof[{{ $index }}]" id="roof_Flachdach_{{ $index }}" value="Flachdach" {{ $roof->roof == 'Flachdach' ? 'checked' : '' }}>
                                                                                                <label class="custom-control-label" for="roof_Flachdach_{{ $index }}">Flachdach</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <img src="{{ asset('images/roofs/Garage.png') }}" alt="" srcset="" style="width:100px;" for="roof_Garage_{{ $index }}">
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input" name="roof[{{ $index }}]" id="roof_Garage_{{ $index }}" value="Garage" {{ $roof->roof == 'Garage' ? 'checked' : '' }}>
                                                                                                <label class="custom-control-label" for="roof_Garage_{{ $index }}">Garage</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <img src="{{ asset('images/roofs/Carport.png') }}" alt="" srcset="" style="width:100px;" for="roof_Carport_{{ $index }}">
                                                                                            <div class="custom-control custom-radio">
                                                                                                <input type="radio" class="custom-control-input" name="roof[{{ $index }}]" id="roof_Carport_{{ $index }}" value="Carport" {{ $roof->roof == 'Carport' ? 'checked' : '' }}>
                                                                                                <label class="custom-control-label" for="roof_Carport_{{ $index }}">Carport</label>
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
                                                        <div class="col-md-6">
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-2">
                                                                        <h3 class="bold">Dacheindeckung</h3>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <select class="tiles" name="tiles[{{ $index }}]" style="width:100%" >
                                                                            @foreach ($tiles as $tile)
                                                                                <option value="{{ $tile->id }}" data-image="{{ asset('images/products/'.$tile->image) }}" {{ $roof->roof == $tile->id ? 'selected' : '' }}>{{ $tile->product }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="construction_fluid[{{ $index }}]" id="construction_fluid_boton_{{ $index }}" value="Beton" {{ $roof->roof_covering == 'Beton' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="construction_fluid_boton_{{ $index }}">Beton</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="construction_fluid[{{ $index }}]" id="construction_fluid_ton_{{ $index }}" value="Ton" {{ $roof->roof_covering == 'Ton' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="construction_fluid_ton_{{ $index }}">Ton</label>
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
                                                                        <h3 class="bold">Aufdachdämmung</h3>
                                                                    </div>
                                                                    <div class="col-md-10">
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="roof_insulation[{{ $index }}]" id="insulation_ja_{{ $index }}" value="ja" {{ $roof->roof_insulation == 'ja' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="insulation_ja_{{ $index }}">ja</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="roof_insulation[{{ $index }}]" id="insulation_nein_{{ $index }}" value="nein" {{ $roof->roof_insulation == 'nein' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="insulation_nein_{{ $index }}">nein</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1" style="width:330px">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <h4 class="bold">Stärke</h4>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <input type="text" class="form-control" name="thickness_roof_insulation[{{ $index }}]" value="{{ $roof->thickness_roof_insulation ?? '' }}">
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
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="between_rafter_insulation[{{ $index }}]" id="rafter_ja_{{ $index }}" value="ja" {{ $roof->between_rafter_insulation == 'ja' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="rafter_ja_{{ $index }}">ja</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" class="custom-control-input" name="between_rafter_insulation[{{ $index }}]" id="rafter_nein_{{ $index }}" value="nein" {{ $roof->between_rafter_insulation == 'nein' ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label" for="rafter_nein_{{ $index }}">nein</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-1" style="width:330px">
                                                                                <div class="form-group row">
                                                                                    <div class="col-md-4">
                                                                                        <h4 class="bold">Stärke</h4>
                                                                                    </div>
                                                                                    <div class="col-md-8">
                                                                                        <input type="text" class="form-control" name="thickness_between_rafter[{{ $index }}]" value="{{ $roof->thickness_between_rafter ?? '' }}">
                                                                                    </div>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                </article>
                                            @endforeach

                                            <!-- accordion Details -->
                                            <div class="col-md-12">
                                                <div class="card-title">
                                                    <h3 class="titles">LANG-CHECKLISTE</h3>
                                                </div> <i class="fa fa-chevron-right mt-1" id="accordion"></i>
                                            </div>
                                            <hr>
                                            <article id="longList" style="display: none">
                                                <section class="col-md-12 dynamic-section flex_me" id="section_3">
                                                    <input type="hidden" name="longCheck" id="longCheck"> 
                                                    <div class="col-md-12">
                                                        <div class="cards">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <h4 class="bold"><strong>Strom</strong></h4>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">gewünschte Größe</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control" name="desired_size" value="{{ $pv_checklist->desired_size ?? '' }}" >
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-4">
                                                                            <h4 class="bold">EVU</h4>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control" name="evu" value="{{ $pv_checklist->evu ?? '' }}" >
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                                <div class="col-md-6">
                                                                     <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <h3 class="bold">Einspeisezusage EVU Netzverträglichkeit</h3>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="pv_rafters" id="rafters_ja" value="ja" {{ $pv_checklist->pv_rafters == 'ja' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="rafters_ja">ja</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="pv_rafters" id="rafters_nein" value="nein" {{ $pv_checklist->pv_rafters == 'nein' ? 'checked' : '' }}>
                                                                                            <label class="custom-control-label" for="rafters_nein">nein</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1" style="width:542px !important;">
                                                                                    <div class="form-group row">
                                                                                        <div class="col-md-4">
                                                                                            <h4 class="bold">EVU max. Größe</h4>
                                                                                        </div>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" class="form-control" name="evu_max_size" value="{{ $pv_checklist->evu_max_size ?? '' }}" >
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div> 

                                                                    <div class="form-group row">
                                                                        <div class="col-md-2">
                                                                            <h3 class="bold">geplantes Messkonzept</h3>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <select name="planed_measurement" id="" class="form-control">
                                                                                <option value="Syna 1">Syna 1</option>
                                                                                <option value="Syna 2b">Syna 2b</option>
                                                                                <option value="Syna 3b">Syna 3b</option>
                                                                                <option value="Syna 4b">Syna 4b</option>
                                                                                <option value="Syna 5b">Syna 5b</option>
                                                                                <option value="Syna 6b">Syna 6b</option>
                                                                                <option value="Syna 7">Syna 7</option>
                                                                                <option value="Syna 8">Syna 8</option>
                                                                                <option value="sontiges">sontiges</option> 
                                                                            </select>
                                                                        </div>
                                                                    </div> 
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                 <div class="form-group row">
                                                                    <div class="col-md-1">
                                                                        <h4 class="bold">Notiz</h4>
                                                                    </div>
                                                                    <div class="col-md-11">
                                                                        <textarea name="note" id="note" class="form-control" cols="30" rows="5">{{ $pv_checklist->note ?? '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="cards">  
                                                            <hr class="normal">
                                                            <div class="card-body">  
                                                                <div class="default-collapse collapse-bordered">
                                                                @foreach ($pv_roof as $roof)  
                                                                    <div class="cards collapse-header" style="border: 0;background: #ffffff;">
                                                                        <div id="headingCollapse1" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#collapse{{ $loop->iteration }}" aria-expanded="false" aria-controls="collapse{{ $loop->iteration }}">
                                                                            <span class="lead collapse-title">
                                                                            <strong> Dach {{$loop->iteration}}</strong>
                                                                            </span>
                                                                        </div>
                                                                        <div id="collapse{{ $loop->iteration }}" role="tabpanel" aria-labelledby="headingCollapse{{ $loop->iteration }}" class="collapse" style="">
                                                                            <div class="card-content">
                                                                                <div class="card-body">
                                                                                    <form id="planRoof" action="{{ route('plan.roof.save') }}" method="POST" class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                        <div class="form-section">  
                                                                                            <input type="hidden" name="product_id" value="{{ request()->product_id }}">
                                                                                            <input type="hidden" name="roof_id" value="{{ $roof->id }}">

                                                                                            <div class="form-header">
                                                                                                <strong>Dach {{$loop->iteration}}: {{ $roof->designation }}</strong>
                                                                                            </div>
                                                                                            
                                                                                            <div class="row">
                                                                                                <div class="col-md-6">
                                                                                                    <div class="form-group">
                                                                                                        <label for="roof_dimensions">Maße Dachfläche</label>
                                                                                                        <textarea class="form-control" cols="30" rows="5" id="roof_dimensions" name="roof_dimensions"></textarea>
                                                                                                    </div>
                                                                                                </div>
                                                                                                
                                                                                                <div class="col-md-6">
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="rafter_left_overhang" class="col-md-4">Dachüberstand Sparren links</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <input type="text" class="form-control" id="rafter_left_overhang" name="rafter_left_overhang"  >
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="roof_width" class="col-md-4">Eindeckmaß in cm</label>
                                                                                                            <div class="col-md-8 d-flex">
                                                                                                                <input type="text" class="form-control me-2" id="roof_width" name="roof_width"  >
                                                                                                                <label for="roof_width" class="align-self-center">B</label>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="roof_height" class="col-md-4">H</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <input type="text" class="form-control" id="roof_height" name="roof_height" >
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="rafter_right_overhang" class="col-md-4">Dachüberstand Sparren rechts</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <input type="text" class="form-control" id="rafter_right_overhang" name="rafter_right_overhang"  >
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="rafter_thickness" class="col-md-4">Sparrenstärke</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <input type="text" class="form-control" id="rafter_thickness" name="rafter_thickness"  >
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="rafter_reinforcement_needed" class="col-md-6">Sparrenverstärkung notwendig</label>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="rafter_reinforcement_needed" id="rafter_reinforcement_needed_ja" value="ja"  >
                                                                                                                    <label class="form-check-label" for="rafter_reinforcement_needed_ja">ja</label>
                                                                                                                </div>
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="rafter_reinforcement_needed" id="rafter_reinforcement_needed_nein" value="nein"  >
                                                                                                                    <label class="form-check-label" for="rafter_reinforcement_needed_nein">nein</label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="row">
                                                                                                        <div class="col-md-6 d-flex align-items-center">
                                                                                                            <label for="statics_available" class="col-md-6">Statik vorhanden</label>
                                                                                                            <div class="col-md-6">
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="statics_available" id="statics_available_ja" value="ja" {{ $pv_checklist->statics_available == 'ja' ? 'checked' : '' }}>
                                                                                                                    <label class="form-check-label" for="statics_available_ja">ja</label>
                                                                                                                </div>
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="statics_available" id="statics_available_nein" value="nein" {{ $pv_checklist->statics_available == 'nein' ? 'checked' : '' }}>
                                                                                                                    <label class="form-check-label" for="statics_available_nein">nein</label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-section">
                                                                                                <div class="row">
                                                                                                    <div class="col-md-6">
                                                                                                        <div class="row">
                                                                                                            <label for="conduit_available" class="col-md-4">Leerohr vorhanden</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="conduit_available" id="conduit_available_ja" value="ja" {{ $pv_checklist->conduit_available == 'ja' ? 'checked' : '' }}>
                                                                                                                    <label class="form-check-label" for="conduit_available_ja">ja</label>
                                                                                                                </div>
                                                                                                                <div class="form-check form-check-inline">
                                                                                                                    <input class="form-check-input" type="radio" name="conduit_available" id="conduit_available_nein" value="nein" {{ $pv_checklist->conduit_available == 'nein' ? 'checked' : '' }}>
                                                                                                                    <label class="form-check-label" for="conduit_available_nein">nein</label>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        
                                                                                                        <div class="row">
                                                                                                            <label for="cable_routing_through" class="col-md-4">Kabelführung durch</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <select id="cable_routing_through" class="form-control" name="cable_routing_through">
                                                                                                                    <option value="Kamin" {{ $pv_checklist->cable_routing_through == 'Kamin' ? 'selected' : '' }}>Kamin</option>
                                                                                                                    <option value="Leerrohr" {{ $pv_checklist->cable_routing_through == 'Leerrohr' ? 'selected' : '' }}>Leerrohr</option>
                                                                                                                    <option value="Fallrohr" {{ $pv_checklist->cable_routing_through == 'Fallrohr' ? 'selected' : '' }}>Fallrohr</option>
                                                                                                                    <option value="sonstiges" {{ $pv_checklist->cable_routing_through == 'sonstiges' ? 'selected' : '' }}>sonstiges</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-md-6">
                                                                                                        <div class="row">
                                                                                                            <label for="lightning_protection" class="col-md-4">Blitzschutz auf dem Dach vorhanden</label>
                                                                                                            <div class="col-md-8">
                                                                                                                <select id="lightning_protection" class="form-control" name="lightning_protection">
                                                                                                                    <option value="ja" {{ $pv_checklist->lightning_protection == 'ja' ? 'selected' : '' }}>ja</option>
                                                                                                                    <option value="nein" {{ $pv_checklist->lightning_protection == 'nein' ? 'selected' : '' }}>nein</option>
                                                                                                                    <option value="geplant" {{ $pv_checklist->lightning_protection == 'geplant' ? 'selected' : '' }}>geplant</option>
                                                                                                                    <option value="entfernt" {{ $pv_checklist->lightning_protection == 'entfernt' ? 'selected' : '' }}>entfernt</option>
                                                                                                                    <option value="versetzt" {{ $pv_checklist->lightning_protection == 'versetzt' ? 'selected' : '' }}>versetzt</option>
                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-section">
                                                                                                <div class="row">
                                                                                                    <label class="col-sm-3 col-form-label">Dachsanierung notwendig</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="dachsanierung" id="dachsanierungJa" value="ja">
                                                                                                            <label class="form-check-label" for="dachsanierungJa">ja</label>
                                                                                                        </div>
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="dachsanierung" id="dachsanierungNein" value="nein">
                                                                                                            <label class="form-check-label" for="dachsanierungNein">nein</label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <label class="col-sm-3 col-form-label">Geplante Termin</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <input type="text" class="form-control" id="geplanteTermin" name="geplante_termin" placeholder="">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="row">
                                                                                                    <label class="col-sm-3 col-form-label">Dachdecker</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <input type="text" class="form-control" id="dachdecker" name="dachdecker" placeholder="">
                                                                                                    </div>
                                                                                                    <label class="col-sm-3 col-form-label">Dauer</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <input type="text" class="form-control" id="dauer" name="dauer" placeholder="">
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="row">
                                                                                                    <label class="col-sm-3 col-form-label">Ort</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <input type="text" class="form-control" id="ort" name="ort" placeholder="">
                                                                                                    </div>
                                                                                                    <label class="col-sm-3 col-form-label">Solarhalteziegel gewünscht</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="solarhalteziegel" id="solarhalteziegelJa" value="ja">
                                                                                                            <label class="form-check-label" for="solarhalteziegelJa">ja</label>
                                                                                                        </div>
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="solarhalteziegel" id="solarhalteziegelNein" value="nein">
                                                                                                            <label class="form-check-label" for="solarhalteziegelNein">nein</label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="row">
                                                                                                    <label class="col-sm-3 col-form-label">Ansprechpartner</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <input type="text" class="form-control" id="ansprechpartner" name="ansprechpartner" placeholder="">
                                                                                                    </div>
                                                                                                    <label class="col-sm-3 col-form-label">Geliefert durch</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <select class="form-control" id="geliefertDurch" name="geliefert_durch">
                                                                                                            <option selected value="Dachdecker">Dachdecker</option>
                                                                                                            <option value="Kunde">Kunde</option>
                                                                                                            <option value="uns">uns</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="row">
                                                                                                    <label class="col-sm-3 col-form-label">Gerüstnutzung</label>
                                                                                                    <div class="col-sm-3">
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="geruestnutzung" id="geruestnutzungJa" value="ja">
                                                                                                            <label class="form-check-label" for="geruestnutzungJa">ja</label>
                                                                                                        </div>
                                                                                                        <div class="form-check form-check-inline">
                                                                                                            <input class="form-check-input" type="radio" name="geruestnutzung" id="geruestnutzungNein" value="nein">
                                                                                                            <label class="form-check-label" for="geruestnutzungNein">nein</label>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-section">
                                                                                                <div class="table-responsive">
                                                                                                    <table class="table">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>Dachaufbauten</th>
                                                                                                                <th>geplante Aktion</th>
                                                                                                                <th>Notiz</th>
                                                                                                                <th>Aktion</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            @foreach ($roofPlan as $plan)
                                                                                                                @if($plan->roof_id == $roof->id && $plan->product_id == request()->product_id)
                                                                                                                <tr> 
                                                                                                                    <td>{{ $plan->roof_structures }}</td>
                                                                                                                    <td>{{ $plan->planned_action }}</td>
                                                                                                                    <td>{{ $plan->planned_note }}</td> 
                                                                                                                </tr>
                                                                                                                @endif
                                                                                                            @endforeach 
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                            
                                                                                            <div class="form-section">
                                                                                                <div class="row align-items-center mb-3">
                                                                                                    <label for="roof_structures" class="col-md-1">Dachaufbauten</label>
                                                                                                    <div class="col-md-2">
                                                                                                        <select id="roof_structures" class="form-control" name="plan[0][roof_structures]">
                                                                                                            <option value="Dachluke">Dachluke</option>
                                                                                                            <option value="Antenne">Antenne</option>
                                                                                                            <option value="Stromleitung">Stromleitung</option>
                                                                                                            <option value="Gaube">Gaube</option>
                                                                                                            <option value="SAT-Schüssel">SAT-Schüssel</option>
                                                                                                            <option value="Kamin">Kamin</option>
                                                                                                            <option value="Lüfter groß">Lüfter groß</option>
                                                                                                            <option value="Dachfenster">Dachfenster</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <label for="planned_action" class="col-md-1">geplante Aktion</label>
                                                                                                    <div class="col-md-2">
                                                                                                        <select id="planned_action" class="form-control" name="plan[0][planned_action]">
                                                                                                            <option value="erneuert">erneuert</option>
                                                                                                            <option value="entfernt">entfernt</option>
                                                                                                            <option value="versetzt">versetzt</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                    <label for="planned_note" class="col-md-1">Notiz</label>
                                                                                                    <div class="col-md-2">
                                                                                                        <textarea class="form-control" name="plan[0][planned_note]"></textarea>
                                                                                                    </div>
                                                                                                    <div class="col-md-2">
                                                                                                        <button type="button" class="btn btn-icon rounded-circle addRow"><i class="feather icon-plus-circle primary" style="font-size: 34px;"></i></button>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div id="rowsContainer"></div>
                                                                                            </div>

                                                                                            <div class="card-footer">
                                                                                                <button type="button" id="saveForm" class="btn btn-primary">Speichern</button>
                                                                                            </div> 

                                                                                        </div>
                                                                                    </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>   
                                                                 @endforeach 
                                                                </div>   
                                                            </div> 
                                                            <br>
                                                            <section> 
                                                                <label class="form-label mb-3"><strong>PV-Anlage vorhanden/Erweiterung</strong></label> 

                                                                <div class="row">

                                                                    <!-- Left Column -->
                                                                    <div class="col-md-4 col-12">
                                                                        <div class="form-group row ">
                                                                            <label for="year_of_construction" class="col-4 col-form-label">Baujahr</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="year_of_construction" name="year_of_construction" value="{{ $pv_checklist->year_of_construction ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row ">
                                                                            <label for="number_of_modules" class="col-4 col-form-label">Anzahl Module</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="number_of_modules" name="number_of_modules" value="{{ $pv_checklist->number_of_modules ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row ">
                                                                            <label for="module_manufacturer" class="col-4 col-form-label">Modulhersteller</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="module_manufacturer" name="module_manufacturer" value="{{ $pv_checklist->module_manufacturer ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row ">
                                                                            <label for="type_designation" class="col-4 col-form-label">Typ Bezeichnung</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="type_designation" name="type_designation" value="{{ $pv_checklist->type_designation ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row ">
                                                                            <label for="kwp_size" class="col-4 col-form-label">kWp Größe</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="kwp_size" name="kwp_size" value="{{ $pv_checklist->kwp_size ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row ">
                                                                            <label for="inverter" class="col-4 col-form-label">Wechselrichter</label>
                                                                            <div class="col-8">
                                                                                <input type="text" class="form-control" id="inverter" name="inverter" value="{{ $pv_checklist->inverter ?? '' }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Middle Column -->
                                                                    <div class="col-md-4 col-12">
                                                                        <div class="form-group ">
                                                                            <label>Anlage umbauen</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="system_conversion" id="system_conversion_ja" value="ja" {{ $pv_checklist->system_conversion == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="system_conversion_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="system_conversion" id="system_conversion_nein" value="nein" {{ $pv_checklist->system_conversion == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="system_conversion_nein">nein</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label>Schaden/Defekt</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="damage_defect" id="damage_defect_ja" value="ja" {{ $pv_checklist->damage_defect == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="damage_defect_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="damage_defect" id="damage_defect_nein" value="nein" {{ $pv_checklist->damage_defect == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="damage_defect_nein">nein</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label>komplette Demontage</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="complete_dismantling" id="complete_dismantling_ja" value="ja" {{ $pv_checklist->complete_dismantling == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="complete_dismantling_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="complete_dismantling" id="complete_dismantling_nein" value="nein" {{ $pv_checklist->complete_dismantling == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="complete_dismantling_nein">nein</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label>Versicherungsschaden</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="insurance_damage" id="insurance_damage_ja" value="ja" {{ $pv_checklist->insurance_damage == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="insurance_damage_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="insurance_damage" id="insurance_damage_nein" value="nein" {{ $pv_checklist->insurance_damage == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="insurance_damage_nein">nein</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label>Kunde behält Module</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="customer_keeps_modules" id="customer_keeps_modules_ja" value="ja" {{ $pv_checklist->customer_keeps_modules == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="customer_keeps_modules_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="customer_keeps_modules" id="customer_keeps_modules_nein" value="nein" {{ $pv_checklist->customer_keeps_modules == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="customer_keeps_modules_nein">nein</label>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group ">
                                                                            <label>Kunde behält WR</label><br>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="customer_keeps_inverter" id="customer_keeps_inverter_ja" value="ja" {{ $pv_checklist->customer_keeps_inverter == 'ja' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="customer_keeps_inverter_ja">ja</label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="customer_keeps_inverter" id="customer_keeps_inverter_nein" value="nein" {{ $pv_checklist->customer_keeps_inverter == 'nein' ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="customer_keeps_inverter_nein">nein</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Right Column -->
                                                                    <div class="col-md-4 col-12">
                                                                        <div class="form-group">
                                                                            <label for="note">Notiz</label>
                                                                            <textarea class="form-control" id="note" rows="5" name="note">{{ $pv_checklist->note ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </section> 
                                                            <hr> 
                                                            <section> 
                                                                <div class="row mb-3">
                                                                    <label class="col-sm-2 col-form-label fw-bold"><strong>AC-Seite</strong></label>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <label class="col-sm-1 col-form-label">Mieterstrommodell</label>
                                                                    <div class="col-sm-2">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="mieterstrommodell" id="mieterstromJa" value="ja">
                                                                            <label class="form-check-label" for="mieterstromJa">ja</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="mieterstrommodell" id="mieterstromNein" value="nein">
                                                                            <label class="form-check-label" for="mieterstromNein">nein</label>
                                                                        </div>
                                                                    </div>
                                                                    <label class="col-sm-1 col-form-label">Wärmepumpe</label>
                                                                    <div class="col-sm-2">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="waermepumpe" id="waermepumpeJa" value="ja">
                                                                            <label class="form-check-label" for="waermepumpeJa">ja</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="waermepumpe" id="waermepumpeNein" value="nein">
                                                                            <label class="form-check-label" for="waermepumpeNein">nein</label>
                                                                        </div>

                                                                        <div class="form-check form-check-inline">
                                                                            <input class="hidden-radio" type="radio" name="waermepumpeLike" id="waermepumpeLike" value="Like">
                                                                            <i id="heart-waermepumpe" class="fa fa-heart heart-radio"></i>
                                                                        </div>
                                                                    </div>
                                                                    <label class="col-sm-1 col-form-label">E-Check machen</label>
                                                                    <div class="col-sm-2">
                                                                        <input class="hidden-radio" type="radio" name="echeck" id="echeck" value="yes">
                                                                        <i id="heart-echeck" class="fa fa-heart heart-radio"></i>
                                                                    </div>
                                                                </div>

                                                                <div class="row mb-3">
                                                                    <label class="col-sm-2 col-form-label">Anzahl WE</label>
                                                                    <div class="col-sm-2">
                                                                        <input type="text" class="form-control" id="anzahlWE" placeholder="">
                                                                    </div>
                                                                    <label class="col-sm-1 col-form-label">Wallbox</label>
                                                                    <div class="col-sm-2">
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="wallbox" id="wallboxJa" value="ja">
                                                                            <label class="form-check-label" for="wallboxJa">ja</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="wallbox" id="wallboxNein" value="nein">
                                                                            <label class="form-check-label" for="wallboxNein">nein</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="hidden-radio" type="radio" name="wallboxLike" id="wallboxLike" value="Like">
                                                                            <i id="heart-wallbox" class="fa fa-heart heart-radio"></i>
                                                                        </div>
                                                                    </div>
                                                                </div> 
                                                            </section>
                                                            <hr class="normal">
                                                            <!-- ZahlerSchrank Section  -->

                                                            <section> 
                                                                <div class="row">
                                                                    <!-- Left Column -->
                                                                    <div class="col-md-8">
                                                                        <div class="">
                                                                            <label class="form-label"><strong>Zählerschrank</strong></label>
                                                                            <div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="zaehlerschrank" id="ok" value="ok">
                                                                                    <label class="form-check-label" for="ok">ok</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="zaehlerschrank" id="erüchtigen" value="erüchtigen">
                                                                                    <label class="form-check-label" for="erüchtigen">erüchtigen</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="zaehlerschrank" id="neu" value="neu">
                                                                                    <label class="form-check-label" for="neu">neu</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="zaehlerschrank" id="neuer_zaehlerschrank" value="neuer_zaehlerschrank">
                                                                                    <label class="form-check-label" for="neuer_zaehlerschrank">neuer Zählerschrank zwischen HAK und Zählerschrank</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="positionHAK" class="form-label">Position HAK</label>
                                                                            <input type="text" class="form-control" id="positionHAK">
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="abstandWechselrichter" class="form-label">Abstand Wechselrichter zum Zählerschrank</label>
                                                                            <input type="text" class="form-control" id="abstandWechselrichter">
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="abstandNeuerZaehlerschrank" class="form-label">Abstand neuer Zählerschrank zum alten Zählerschrank</label>
                                                                            <input type="text" class="form-control" id="abstandNeuerZaehlerschrank">
                                                                        </div>

                                                                        <div class="">
                                                                            <label class="form-label">Größe</label>
                                                                            <div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="groesse" id="groesse550" value="550">
                                                                                    <label class="form-check-label" for="groesse550">550</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="groesse" id="groesse800" value="800">
                                                                                    <label class="form-check-label" for="groesse800">800</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="">
                                                                            <label class="form-label">Erdung vorhanden</label>
                                                                            <div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="erdung" id="erdungJa" value="ja">
                                                                                    <label class="form-check-label" for="erdungJa">ja</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="erdung" id="erdungNein" value="nein">
                                                                                    <label class="form-check-label" for="erdungNein">nein</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="zaehlerAbmeldung" class="form-label">Zähler zur Abmeldung</label>
                                                                            <select class="form-control" id="zaehlerAbmeldung">
                                                                                <option value="1">1</option>
                                                                                <option value="2">2</option>
                                                                                <option value="3">3</option>
                                                                            </select>
                                                                        </div>

                                                                    </div>

                                                                    <!-- Right Column -->
                                                                    <div class="col-md-4">
                                                                        <label class="form-label">einzubauende Komponenten</label>

                                                                        <div class="">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="zaehleradapterplatte">
                                                                                <label class="form-check-label" for="zaehleradapterplatte">Zähleradapterplatte</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="acUeberspannungsschutz">
                                                                                <label class="form-check-label" for="acUeberspannungsschutz">AC Überspannungsschutz</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="slsSchalter">
                                                                                <label class="form-check-label" for="slsSchalter">SLS Schalter</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="apzFeld">
                                                                                <label class="form-check-label" for="apzFeld">APZ Feld</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="trennRelais">
                                                                                <label class="form-check-label" for="trennRelais">Trenn-Relais</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="potentialausgleichsschiene">
                                                                                <label class="form-check-label" for="potentialausgleichsschiene">Potentialausgleichsschiene</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="wlan">
                                                                                <label class="form-check-label" for="wlan">WLAN</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="lan">
                                                                                <label class="form-check-label" for="lan">LAN</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="steckdose">
                                                                                <label class="form-check-label" for="steckdose">Steckdose</label>
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" id="sonstiges1">
                                                                                <label class="form-check-label" for="sonstiges1">sonstiges</label>
                                                                            </div>
                                                                            <div class="">
                                                                                <input type="text" class="form-control" id="sonstigesInput1" placeholder="Sonstiges">
                                                                            </div> 
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="anzahlZaehlPlaetze" class="form-label">Anzahl Zählerplätze</label>
                                                                            <select class="form-control" id="anzahlZaehlPlaetze">
                                                                                <option value="1">1</option>
                                                                                <option value="2">2</option>
                                                                                <option value="3">3</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="">
                                                                            <label for="fiAnzahl" class="form-label">FI Anzahl</label>
                                                                            <select class="form-control" id="fiAnzahl">
                                                                                <option value="1">1</option>
                                                                                <option value="2">2</option>
                                                                                <option value="3">3</option>
                                                                            </select>
                                                                        </div>

                                                                        <label class="form-label">bei Anlagen > 25 kWp</label>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" id="naSchutz">
                                                                            <label class="form-check-label" for="naSchutz">NA Schutz</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" id="rundsteuerempfaenger">
                                                                            <label class="form-check-label" for="rundsteuerempfaenger">Rundsteuerempfänger</label>
                                                                        </div>

                                                                    </div>

                                                                </div>  
                                                                </div>  
                                                            </section>

                                                        </div>
                                                    </div>
                                                </section>
                                            </article> 
                                        <!-- accordion Details: End --> 
                                        </div> 
                                    </div>
                                    <div class="button" style="position: fixed;  top: 500px;  right: 20px;">
                                        <button type="submit" class="btn btn-primary"> <i class="feather icon-save"></i> Speichern</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>




@endsection


@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
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

            var $option = $(
                '<div id="roof">' +
                '<h3>' + option.text + '</h3>' +
                '<img src="' + $(option.element).data('image') + '" class="img-flag" />' +
                '</div>'
            );
            return $option;
        }

        // Initialize the Select2
        initializeSelect2();
    });
</script>
<script>
        $(document).ready(function() {
            var i = 0;

            $(".addRow").click(function() {
                i++;

                var row = `
                    <div class="row align-items-center mb-3">
                        <input type="hidden" name="plan[` + i + `][product_id]" value="{{ request()->id }}">
                        <input type="hidden" name="plan[` + i + `][roof_id]" value="{{ $roof->id }}">

                        <div class="col-md-1">
                            <label for="category">Dachaufbauten</label>
                        </div>
                        <div class="col-md-2">
                            <select name="plan[` + i + `][roof_structures]" class="form-control">
                                <option value="Dachluke">Dachluke</option>
                                <option value="Antenne">Antenne</option>
                                <option value="Stromleitung">Stromleitung</option>
                                <option value="Gaube">Gaube</option>
                                <option value="SAT-Schüssel">SAT-Schüssel</option>
                                <option value="Kamin">Kamin</option>
                                <option value="Lüfter groß">Lüfter groß</option>
                                <option value="Dachfenster">Dachfenster</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="category">geplante Aktion</label>
                        </div>
                        <div class="col-md-2">
                            <select name="plan[` + i + `][planned_action]" class="form-control">
                                <option value="erneuert">erneuert</option>
                                <option value="entfernt">entfernt</option>
                                <option value="versetzt">versetzt</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label for="category">Notiz</label>
                        </div>
                        <div class="col-md-3">
                            <textarea name="plan[` + i + `][planned_note]" class="form-control" rows="1"></textarea>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-icon rounded-circle remove-row">
                                <i class="feather icon-minus-circle danger" style="font-size: 34px;"></i>
                            </button>
                        </div>
                    </div>`;

                $("#rowsContainer").append(row);
            });

            $(document).on("click", ".remove-row", function() {
                $(this).closest('.row').remove();
            });
        });

       $(document).ready(function() {
    // Set up the CSRF token in the header for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#saveForm').on('click', function(e) {
        e.preventDefault();

        // Trim all input values
        $('#planRoof').find('input, textarea').each(function() {
            $(this).val($.trim($(this).val()));
        });

        // Create a FormData object from the form
        var formData = new FormData($('#planRoof')[0]);

        console.log("FormData before submission:");
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]);
        }

        $(this).attr("disabled", true).text("Saving...");

        $.ajax({
            url: "{{ route('plan.roof.save') }}",
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                $('#saveForm').attr("disabled", false).text("Speichern");
                console.log("Server response: ", response);
                alert(response.message);
            },
            error: function(xhr, status, error) {
                $('#saveForm').attr("disabled", false).text("Speichern");

                if (xhr.status === 422) {
                    var response = xhr.responseJSON;
                    let errors = "";
                    $.each(response.errors, function(key, value) {
                        errors += value.join("\n") + "\n";
                    });
                    alert('Validation errors:\n' + errors);
                } else {
                    alert('Something went wrong. Please try again.');
                }
            }
        });
    });
});


</script>


<!-- accordian script  -->
<script>
    document.getElementById('accordion').addEventListener('click', function () {
        var longList = document.getElementById('longList');
        var check = document.getElementById('longCheck');

        if (longList.style.display === 'none' || longList.style.display === '') {
            longList.style.display = 'block';
            check.value = 'Checked'; // Corrected assignment
            this.classList.remove('fa-chevron-right');
            this.classList.add('fa-chevron-down');
        } else {
            longList.style.display = 'none';
            check.value = 'Not Checked'; // Corrected assignment
            this.classList.remove('fa-chevron-down');
            this.classList.add('fa-chevron-right');
        }
    });
</script>


<!-- heart button script -->
 <script>
        document.querySelectorAll('.heart-radio').forEach(function(heart) {
            heart.addEventListener('click', function() {
                // Toggle active class for this heart
                this.classList.toggle('active');
                
                // Find corresponding radio button and set its checked state
                const associatedRadio = document.getElementById(this.id.replace('heart-', ''));
                associatedRadio.checked = this.classList.contains('active');

                // If there are other hearts in the same group, uncheck them
                document.querySelectorAll('.heart-radio').forEach(function(otherHeart) {
                    if (otherHeart !== heart && otherHeart.classList.contains('active') && otherHeart.name === heart.name) {
                        otherHeart.classList.remove('active');
                        document.getElementById(otherHeart.id.replace('heart-', '')).checked = false;
                    }
                });
            });
        });
    </script>

@endsection
