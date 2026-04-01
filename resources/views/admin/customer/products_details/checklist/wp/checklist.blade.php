@extends('admin.layouts.app')
@section('title') Konfiguration @endsection
@section('style')
  
<style>
    .section_title {
      border-left: 8px solid #94c11f;
    color: #94c11f;
    padding: 6px;
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
                                <li class="breadcrumb-item"><a href=" ">PRODUCT</a>
                                </li>
                                <li class="breadcrumb-item active"> 
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
           
            <div class="container">
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
                                                            value="{{ $customer->title }}.{{ $customer->name }} {{ $customer->lastname }}"
                                                            name="lastname" readonly>
                                                        <div class="indicator"></div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <div class="col-md-4">
                                                        <span>Adresse</span>
                                                    </div>
                                                    <div class="col-md-8 textbox-container empty ">
                                                        <input type="text" class="form-control textbox" name="street"
                                                            value="{{ $customer->street }} {{ $customer->postcode }} {{ $customer->city }}" readonly>
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

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Geschoß</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" value="{{ $customer->source }}"
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Raumname</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" class="form-control" name="request_date"
                                                value="{{ $customer->request_date }}" disabled>
                                        </div>
                                    </div>
                                </div> 

                                  

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Fläche m²</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" class="form-control" name="date"
                                                value="{{ $customer->date }}" disabled>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <span>Heizungsart</span>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" class="form-control" name="date"
                                                value="{{ $customer->date }}" disabled>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            </section> 

            <div class="col-12"><hr></div>
                <div class="row">
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
                                                                                value="">&nbsp;
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
                                                                                name="living_space">
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
                                                                            <input type="text" class="form-control"
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
                                                                                name="number_people" id="number_people">
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
                                                                                name="wp_number_we" value="">
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
                                                                            <input type="text" class="form-control"
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
                                                                                                <input type="radio"
                                                                                                    class="custom-control-input"
                                                                                                    name="glass" id="glass_1"
                                                                                                    checked value="1-fach">
                                                                                                <label class="custom-control-label"
                                                                                                    for="glass_1">1-fach</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div
                                                                                                class="custom-control custom-radio">
                                                                                                <input type="radio"
                                                                                                    class="custom-control-input"
                                                                                                    name="glass" id="glass_2"
                                                                                                    value="2-fach">
                                                                                                <label class="custom-control-label"
                                                                                                    for="glass_2">2-fach</label>
                                                                                            </div>
                                                                                        </fieldset>
                                                                                    </li>
                                                                                    <li class="d-inline-block mr-1">
                                                                                        <fieldset>
                                                                                            <div
                                                                                                class="custom-control custom-radio">
                                                                                                <input type="radio"
                                                                                                    class="custom-control-input"
                                                                                                    name="glass" id="glass_3"
                                                                                                    value="3-fach">
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
                                                                                                    id="window_margin_alu" checked
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
                                                                                                    id="window_margin_kunststoff"
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
                                                                                                    id="window_margin_holz"
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
                                                                                    name="insulation_thickness">
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
                                                                                    <option value="Mauerwerk">Mauerwerk</option>
                                                                                    <option value="Holz">Holz</option>
                                                                                    <option value="Massivbau">Massivbau</option>
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
                                                                                <input type="text" class="form-control"
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
                                                                                                    id="wp_insulation_ja"
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
                                                                                                    id="wp_insulation_nein"
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
                                                                                                    class="form-control"
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
                                                                                                    name="wp_rafter"
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
                                                                                                    name="wp_rafter"
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
                                                                                                    class="form-control"
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
                                                                                    <input type="text" class="form-control"
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
                                                                                                        id="wp_buthtub_no" checked
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
                                                                                                        name="wp_bathtub"
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
                                                                                                        class="form-control"
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
                                                                                                    <input type="text"
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
                                                                                                        id="wp_swimming_pool_no" checked
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
                                                                                                        name="wp_swimming_pool"
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
                                                                                                    <input type="text"
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
                                                                                                        name="solor" id="solar_no"
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
                                                                                                        name="solor" id="solor_yes"
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
                                                                                                        class="form-control"
                                                                                                        name="rafter_strength">
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
                                                                                                        name="chimney" id="chimney_no"
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
                                                                                                        name="chimney" id="chimney_yes"
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
                                                                                                        class="form-control"
                                                                                                        name="rafter_strength">
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
                                                                                                        name="hlb_calc" id="hlb_calc_no"
                                                                                                        checked value="nein">
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
                                                                                                        name="hlb_calc"
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
                                                                                    <input type="number" class="form-control" placeholder="Erstes Jahr"
                                                                                            name="energy_first_year_consumption" id="energy_first_year_consumption">
                                                                                </td>
                                                                                <td><input type="number" class="form-control" placeholder="Zweites Jahr"
                                                                                        name="energy_second_year_consumption" id="energy_second_year_consumption">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" class="form-control" placeholder="Drittes Jahr"
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
                                                                                            <input type="number" class="form-control"    name="energy_total_year_consumption"  id="energy_total_year_consumption" placeholder="Gesamt" aria-describedby="energy_consumption_type_lable">
                                                                                            <div class="input-group-prepend">
                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>

                                                                                    <fieldset>
                                                                                        <div class="input-group"> 
                                                                                            <input type="number" class="form-control"  name="energy_avg_year_consumption"  id="energy_avg_year_consumption" placeholder="Durchschnittliche" aria-describedby="energy_consumption_type_lable">
                                                                                            <div class="input-group-prepend">
                                                                                                <span class="input-group-text" id="energy_consumption_type_lable">kWh</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>  
                                                                                
                                                                                </td>
                                                                            </tr> 

                                                                            <tr> 
                                                                                <td>
                                                                                    <input type="number" class="form-control" placeholder="Erstes Jahr Kosten"
                                                                                            name="energy_first_year_cost" id="energy_first_year_cost">
                                                                                </td>
                                                                                <td><input type="number" class="form-control" placeholder="Zweites Jahr Kosten"
                                                                                        name="energy_second_year_cost" id="energy_second_year_cost" > 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" class="form-control" placeholder="Drittes Jahr Kosten"
                                                                                        name="energy_third_year_cost" id="energy_third_year_cost">
                                                                                </td>
                                                                                <td>
                                                                                <input type="text" value="Euro" class="form-control" readonly>  
                                                                                </td>
                                                                                <td>
                                                                                    <fieldset>
                                                                                        <div class="input-group mb-1"> 
                                                                                            <input type="number" class="form-control mb-1" placeholder="Gesamt Kosten" id="energy_total_year_cost"
                                                                                                    name="energy_total_year_cost"  >
                                                                                            <div class="input-group-prepend">
                                                                                                <span class="input-group-text"  >€</span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </fieldset>

                                                                                    <fieldset>
                                                                                        <div class="input-group mb-1"> 
                                                                                            <input type="number" class="form-control" placeholder="Durchschnittliche" id="energy_avg_year_cost"
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
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="WP">Wärmepumpe</option>
                                                                        <option value="GAS">GAS</option>
                                                                        <option value="oil"> Öl</option>
                                                                        <option value="Pellets"> Pellets</option>
                                                                        <option value="Nachtspeicher">Nachtspeicher</option>
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-3 "  >  
                                                                    <h4 class="bold ">Aufstellort</h4>  
                                                                    <select name="exhibition_location" id="exhibition_location" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="KG">KG</option>
                                                                        <option value="EG">EG</option>
                                                                        <option value="OG"> OG</option>
                                                                        <option value="DG"> DG</option> 
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-5 "  >  
                                                                    <h4 class="bold ">Notiz</h4>  
                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control"></textarea>
                                                        
                                                                </div>

                                                                <div class="col-4 mt-1">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-12 ">
                                                                            <h4 class="bold">Alter der Heizung <label
                                                                                    id="heating_age_label"></label></h4>
                                                                        </div>
                                                                        <div class="col-md-12 flex_me">
                                                                            <input type="text" class="form-control"
                                                                                name="heating_manufacture_year"
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
                                                                                @foreach ($heating_types as $heating_type)
                                                                                <option value="{{ $heating_type->id }}"
                                                                                    data-type="{{ $heating_type->heating_type }}">{{
                                                                                    $heating_type->heating_type }}</option>
                                                                                @endforeach
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
                                                                            <input type="text" class="form-control"
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
                                                                            <input type="text" class="form-control"
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
                                                                            <input type="text" class="form-control"
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
                                                                            <select name="hot_water_preperation" id="" class="form-control">
                                                                                <option value="">Bitte wählen</option>
                                                                                <option value="Heizung">Heizung</option>
                                                                                <option value="Durchlauferhitzer">Durchlauferhitzer</option>
                                                                                <option value="Sonstiges">Sonstiges</option>
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
                                                                                <option value="25">25 Liter (Niedrig)</option>
                                                                                <option value="50">50 Liter (Normal)</option>
                                                                                <option value="80">80 Liter (Hoch)</option>
                                                                                <option value="120">120 Liter (Luxus)</option>
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
                                                                                <option selected>Wählen...</option>     
                                                                                <option value="underfloor heating">Fußbodenheizung</option>    
                                                                                <option value="radiator">Heizkörper</option> 
                                                                                <option value="underfloor heating and radiator">Fußbodenheizung + Heizkörper</option> 
                                                                                <option value="none">Keine</option> 
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
                                                                                <option value="one">Ein-Rohr-System</option>    
                                                                                <option value="two">Zwei-Rohr-System</option> 
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
                                                                        <option value="yes">Ja</option>
                                                                        <option value="no">Nein</option> 
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-4 "  >  
                                                                    <h4 class="bold ">Stellantriebe</h4>  
                                                                    <select name="actuators" id="actuators" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="Ja / 230 Volt">Ja / 230 Volt</option>
                                                                        <option value="Ja / 24 Volt">Ja / 24 Volt</option>
                                                                        <option value="Nein / 230 Volt"> Nein / 230 Volt</option>
                                                                        <option value="Nein / 24 Volt"> Nein / 24 Volt</option> 
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-4"  >  
                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                    <select name="suitable_cooling_system" id="suitable_cooling_system" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="Ja">Ja</option>
                                                                        <option value="Nein"> Nein</option>
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-12 "  >  
                                                                    <h4 class="bold ">Notiz</h4>  
                                                                    <textarea name="exhibation_location_note" id="exhibation_location_note" cols="10" rows="2" class="form-control"></textarea>
                                                        
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                <div class="cards collapse-header">
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
                                                                        <option value="yes">Ja</option>
                                                                        <option value="no">Nein</option> 
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-4 "  >  
                                                                    <h4 class="bold ">Thermostate</h4>  
                                                                    <select name="thermostats" id="thermostats" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="yes">Ja</option>
                                                                        <option value="no">Nein</option> 
                                                                    </select>  
                                                                </div>

                                                                    <div class="col-md-4 "  >  
                                                                    <h4 class="bold ">Thermostatventile</h4>  
                                                                    <select name="thermostatic_valves" id="thermostatic_valves" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="yes">Ja</option>
                                                                        <option value="no">Nein</option> 
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-4"  >  
                                                                    <h4 class="bold ">Kühlung Funktion</h4>  
                                                                    <select name="radiator_cooling_system" id="radiator_cooling_system" class="form-control">
                                                                        <option value="">Bitte wählen</option>
                                                                        <option value="Ja">Ja</option>
                                                                        <option value="Nein"> Nein</option>
                                                                    </select>  
                                                                </div>

                                                                <div class="col-md-12 "  >  
                                                                    <h4 class="bold ">Notiz</h4>  
                                                                    <textarea name="radiator_note" id="radiator_note" cols="10" rows="2" class="form-control"></textarea>
                                                        
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
                                                                                <th># </th>
                                                                                <th>Art</th>
                                                                                <th>Dimension</th>
                                                                                <th>Hersteller</th>
                                                                                <th>Typbezeichnung</th>
                                                                                <th>Notiz</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <th scope="row">Heizung</th>
                                                                                <td>
                                                                                    <select name="" id="" class="form-control">
                                                                                        <option value="Kupfer">Kupfer</option>
                                                                                        <option value="Kunststoff">Kunststoff</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Notiz" name=""> 
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th scope="row">Kalt-Wasser</th>
                                                                                <td>
                                                                                    <select name="" id="" class="form-control">
                                                                                        <option value="Kupfer">Kupfer</option>
                                                                                        <option value="Kunststoff">Kunststoff</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Notiz" name=""> 
                                                                                </td>
                                                                            </tr>

                                                                            <tr>
                                                                                <th scope="row">Warm-Wasser</th>
                                                                                <td>
                                                                                    <select name="" id="" class="form-control">
                                                                                        <option value="Kupfer">Kupfer</option>
                                                                                        <option value="Kunststoff">Kunststoff</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name=""> 
                                                                                </td>
                                                                                    <td>
                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Notiz" name=""> 
                                                                                </td>
                                                                            </tr>

                                                                                <tr>
                                                                                <th scope="row">Zirkulation</th>
                                                                                <td>
                                                                                    <select name="" id="" class="form-control">
                                                                                        <option value="Kupfer">Kupfer</option>
                                                                                        <option value="Kunststoff">Kunststoff</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Dimension" name="">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Hersteller" name=""> 
                                                                                </td>
                                                                                    <td>
                                                                                    <input type="text" class="form-control" placeholder="Typbezeichnung" name=""> 
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" class="form-control" placeholder="Notiz" name=""> 
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
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" class="form-control" placeholder="Breite" name="room[0][width]"> 
                                                                                        <input type="hidden" class="form-control" name="customer_id" value="{{ $customer->id }}">
                                                                                        <input type="hidden" class="form-control"  name="room_id" value="{{ request()->id}}">
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
                                                                            <option value="ok">OK</option>
                                                                            <option value="upgrade">ertüchtigen</option>
                                                                            <option value="new">neu</option> 
                                                                        </select> 
                                                                    </div>

                                                                    <div class="form-group row"> 
                                                                        <label for=""><h4>Größe</h4></label> 
                                                                        <select name="cabinet_size" id="cabinet_size" class="form-control" style="width:100% !important;">
                                                                            <option value="550">550</option>
                                                                            <option value="800">800</option>
                                                                            <option value="1100">1100</option> 
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

                                                                                <select name="meter_cabinet_company" id="meter_cabinet_company"
                                                                                    class="form-control" style="width:100% !important;>
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
                                                                                                name="wp_all"
                                                                                                id="wp_all">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_all">Alles</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="checkbox"
                                                                                                class="custom-control-input"
                                                                                                name="wp_meter_adapter_plate"
                                                                                                id="wp_meter_adapter_plate">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_meter_adapter_plate">Zähleradapterplatte</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="checkbox"
                                                                                                class="custom-control-input"
                                                                                                name="wp_ac_surge_protection"
                                                                                                id="wp_ac_surge_protection">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_ac_surge_protection"
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
                                                                                                name="wp_ac_switch" id="wp_ac_switch">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_ac_switch">SLS
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
                                                                                                name="wp_apz_field" id="wp_apz_field">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_apz_field">APZ Feld</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="checkbox"
                                                                                                class="custom-control-input"
                                                                                                name="wp_disconnect_relay"
                                                                                                id="wp_disconnect_relay">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_disconnect_relay">Trenn-Relais</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-1 mb-1 mt-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="checkbox"
                                                                                                class="custom-control-input"
                                                                                                name="wp_equipotential_bonding"
                                                                                                id="wp_equipotential_bonding_busbar">
                                                                                            <label class="custom-control-label"
                                                                                                for="wp_equipotential_bonding_busbar">Potentialausgleichsschiene</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                            </ul> 
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#meterCabinetModal">
                                                                    Neu/Bearbiten
                                                                </button>
                                                                <!-- Meter Cabinet Modal -->
                                                                <div class="modal fade" id="meterCabinetModal" tabindex="-1" role="dialog" aria-labelledby="meterCabinetModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="meterCabinetModalLabel">Zählerschrank bearbeiten</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form id="meterCabinetForm">
                                                                                    @csrf
                                                                                    <input type="hidden" id="meter_cabinet_id">

                                                                                    <!-- Meter Cabinet Type -->
                                                                                    <div class="form-group">
                                                                                        <label for="meter_cabinet">Zählerschrank</label>
                                                                                        <select name="meter_cabinet" id="meter_cabinet" class="form-control">
                                                                                            <option value="ok">OK</option>
                                                                                            <option value="upgrade">Ertüchtigen</option>
                                                                                            <option value="new">Neu</option>
                                                                                        </select>
                                                                                    </div>

                                                                                    <!-- Cabinet Size -->
                                                                                    <div class="form-group">
                                                                                        <label for="cabinet_size">Größe</label>
                                                                                        <select name="cabinet_size" id="cabinet_size" class="form-control">
                                                                                            <option value="550">550</option>
                                                                                            <option value="800">800</option>
                                                                                            <option value="1100">1100</option>
                                                                                        </select>
                                                                                    </div>

                                                                                    <!-- Manufacturer -->
                                                                                    <div class="form-group">
                                                                                        <label for="meter_cabinet_company">Hersteller</label>
                                                                                        <select name="meter_cabinet_company" id="meter_cabinet_company" class="form-control">
                                                                                            <!-- Manufacturer options should be dynamically populated -->
                                                                                            @foreach ($electro as $elec)
                                                                                                <option value="{{ $elec->id }}">{{ $elec->name }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </div>

                                                                                    <!-- Components -->
                                                                                    <div class="form-group">
                                                                                        <label>Einzubauende Komponenten</label>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_all" name="wp_all">
                                                                                            <label class="form-check-label" for="wp_all">Alles</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_meter_adapter_plate" name="wp_meter_adapter_plate">
                                                                                            <label class="form-check-label" for="wp_meter_adapter_plate">Zähleradapterplatte</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_ac_surge_protection" name="wp_ac_surge_protection">
                                                                                            <label class="form-check-label" for="wp_ac_surge_protection">AC Überspannungsschutz</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_ac_switch" name="wp_ac_switch">
                                                                                            <label class="form-check-label" for="wp_ac_switch">SLS Schalter</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_apz_field" name="wp_apz_field">
                                                                                            <label class="form-check-label" for="wp_apz_field">APZ Feld</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_disconnect_relay" name="wp_disconnect_relay">
                                                                                            <label class="form-check-label" for="wp_disconnect_relay">Trenn-Relais</label>
                                                                                        </div>
                                                                                        <div class="form-check">
                                                                                            <input type="checkbox" class="form-check-input" id="wp_equipotential_bonding" name="wp_equipotential_bonding">
                                                                                            <label class="form-check-label" for="wp_equipotential_bonding">Potentialausgleichsschiene</label>
                                                                                        </div>
                                                                                    </div>

                                                                                </form>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                                                                                <button type="button" id="saveMeterCabinet" class="btn btn-primary">Speichern</button>
                                                                            </div>
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
                                                                                        <option value="Ja">Ja</option>
                                                                                        <option value="nein">Nein</option> 
                                                                                        <option value="geplant">Geplant</option> 
                                                                                    </select>  
                                                                                </th>
                                                                                <td>
                                                                                    <select name="ventilation_system" id="ventilation_system" class="form-control">
                                                                                        <option value="">Bitte wählen</option>
                                                                                        <option value="Zentral">Zentral</option>
                                                                                        <option value="Dezentral">Dezentral</option>  
                                                                                    </select>  
                                                                                </td>
                                                                                <td>
                                                                                    <select name="ventilation_system" id="ventilation_system" class="form-control">
                                                                                        <option value="">Bitte wählen</option>
                                                                                        <option value="Ja">Ja</option>
                                                                                        <option value="Nein">Nein</option>  
                                                                                    </select>  
                                                                                </td> 
                                                                                <td>
                                                                                    <input type="text" name="ventilation_company" class="form-control" placeholder="Hersteller">
                                                                                </td>

                                                                                <td>
                                                                                    <input type="text" name="ventilation_type" class="form-control" placeholder="Typ">
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
        </div>
    </div>
</div>
@endsection

@section('script')
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
        let roomId = {{ request()->id }};   

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
                room_id: roomId,
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
            url: '/heating_circuit/get/' + {{ $customer->id }} +'/'+ {{ request()->id }},
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
        const roomId = {{ request()->id }};

        $.ajax({
            url: `/room_dimensions/get/${customerId}/${roomId}`,  // Correct route with customer_id and room_id
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
@endsection