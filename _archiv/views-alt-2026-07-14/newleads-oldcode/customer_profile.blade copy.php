@extends('admin.layouts.app')

@section('title') KUNDE PROFILE @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/card-analytics.css') }}">
<style>

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
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDE PROFILE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">ANFRAGELISTE</a>
                                </li>
                                <li class="breadcrumb-item active">PROFIL</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="container">
                <div id="accordion">
                    <!-- KUNDE PROFIL Section -->
                    <div class="cards">
                        <div class="card-header" id="headingOne" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h2 class="primary text-bold-700">{{ strtoupper($customer->name)  }} {{ strtoupper($customer->lastname) }}</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                            <div class="card-body">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <form novalidate method="post" action="{{ action('App\Http\Controllers\EmployeeController@add') }}" class="custom-file-upload" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <!-- Personal Information Fields -->
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="form-group row">
                                                <label for="lastname" class="col-sm-2 col-form-label">Vorname</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ $customer->lastname }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="street" class="col-sm-2 col-form-label">Straße</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="street" name="street" value="{{ $customer->street }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="postcode" class="col-sm-2 col-form-label">Postleitzahl</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="postcode" name="postcode" value="{{ $customer->postcode }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="city" class="col-sm-2 col-form-label">Stadt</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="phone" class="col-sm-2 col-form-label">Telefon</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $customer->phone }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-2 col-form-label">E-Mail</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="status" class="col-sm-2 col-form-label">Status</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="status" name="status" value="{{ $customer->status }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-12 col-sm-12 text-center">
                                            <!-- Profile Image -->
                                           <img src="{{ $images ? asset('images/lead/home/' . $images->name) : asset('images/default.png') }}" id="picture" alt="profile image" style="width:300px; height:400px;" data-toggle="modal" data-target="#image"> 

                                            <div class="modal-primary mr-1 mb-1 d-inline-block"> 

                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary white">
                                                                    <h5 class="modal-title" id="myModalLabel160">{{ $customer->name }} {{ $customer->lastname }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                 <img src="{{ $images ? asset('images/lead/home/' . $images->name) : asset('images/default.png') }}"  id="picture" alt="profile image" width="auto" height="auto"  > 
                                                                    
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Schließen</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>

                                    <!-- Alternative Address Section -->
                                    <div class="row mt-4">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="form-group row">
                                                <label for="street2" class="col-sm-2 col-form-label">Bauvorhaben Adresse</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="street2" name="street2" value="{{ $customer->street2 }} - {{ $customer->postcode2 }} , {{ $customer->city2 }}" readonly>
                                                </div>
                                            </div> 
                                            
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Products Section -->
                        <div class="cards">
                            <div class="card-header" id="heading3" style="background:transparent !important;">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    <h2 class="primary text-bold-700">GEWERKE</h2>
                                    </button>
                                </h5>
                            </div>
                            <div id="collapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordion">
                                <div class="card-body" style="    display: flex;  flex-wrap: wrap;">
                                    @foreach ($products as $product)
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <div class="card text-black  text-center" style="background:#e4e1e1;">
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <img src="{{asset('images/articles/'.$product->image)}}" alt="element 02" width="100" class="mb-1">
                                                        <h3 class="card-title text-primary">{{ $product->article_group }}</h3>
                                                        <p class="card-text">Interessiert</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>  
                                    @endforeach                            
                                </div>
                            </div>
                        </div>


                     <div class="cards">
                        <div class="card-header" id="headingTwo" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <h2 class="primary text-bold-700">WEITERE INFORMATIONEN</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                               <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Quelle</span>
                                                </div>
                                                <div class="col-md-8"> 
                                                    <input type="text" value="{{$customer->source }}" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Anfrage-Datum</span>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control form-element" name="request_date" readonly value="{{ old('request_date', $customer->request_date) }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row form-element">
                                                <div class="col-md-2">
                                                    <span>Info</span>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control form-element" name="info" readonly value="{{ old('info', $customer->info) }}">
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
                                                                    <input type="radio" class="custom-control-input form-element"  @if($customer->document =="on") checked disabled @endif  name="document"  id="customRadio1">
                                                                    <label class="custom-control-label" for="customRadio1">Ja</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input form-element"   @if($customer->document =="off") checked disabled @endif name="document" id="customRadio2">
                                                                    <label class="custom-control-label" for="customRadio2">Nein</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                        $id = $customer->contact_person;
                                        $contact_person = DB::table('employees') 
                                        ->select('employees.name', 'employees.lastname')
                                        ->where('employees.id', '=', $id)
                                        ->first()
                                        @endphp

                                     
                                        <div class="col-6">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Erste Kontaktperson</span>
                                                </div>
                                                <div class="col-md-8">
                                                     <input type="text" value="{{ $contact_person->name }} {{ $contact_person->lastname }}" class="form-control" disabled>
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
                                                    <textarea name="note" class="form-control form-element" readonly cols="30" rows="5">{{ $customer->note}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group row form-element">
                                                <div class="col-md-4">
                                                    <span>Wann können wir Sie Kontaktieren?</span>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="date" class="form-control form-element"readonly name="appointment" value="{{ $customer->appointment}}">
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
                                                                    <input type="radio" class="custom-control-input form-element" disabled name="appointment_by" @if($customer->appointment_by =="telefonisch") checked @endif id="appointment_by_telefonisch" value="telefonisch">
                                                                    <label class="custom-control-label" for="appointment_by_telefonisch">telefonisch</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-1">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input form-element" disabled name="appointment_by" @if($customer->appointment_by =="E-Mail") checked @endif  id="appointment_by_email" value="E-Mail">
                                                                    <label class="custom-control-label" for="appointment_by_email">E-Mail</label>
                                                                </div>
                                                            </fieldset>
                                                        </li>
                                                        <li class="d-inline-block mr-1">
                                                            <fieldset>
                                                                <div class="custom-control custom-radio">
                                                                    <input type="radio" class="custom-control-input form-element" disabled name="appointment_by" @if($customer->appointment_by =="Vor Ort Besuch") checked @endif id="appointment_by_ort" value="Vor Ort Besuch">
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

                        <!-- Products Section -->
                    <div class="cards">
                        <div class="card-header" id="heading4" style="background:transparent !important;">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                <h2 class="primary text-bold-700">ENERGIEVERBRAUCH UND OBJEKTDATEN</h2>
                                </button>
                            </h5>
                        </div>
                        <div id="collapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordion">
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
                                                            <select name="objective" id="" class="form-control" disabled>
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
                                                                <input type="text" class="form-control form-element" disabled name="house_year" id="house_year" value="{{ old('house_year', $customer->house_year) }}" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieveil Wohneinheit hat das Obejekt?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                                <input type="text" class="form-control textbox" disabled name="number_we" value="{{ old('number_we', $customer->number_we) }}">
                                                            
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wieviel Geschoß hat das Objekt?   </h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                            <input type="text" class="form-control" disabled  name="number_stories" value="{{ old('number_stories', $customer->number_stories) }}">
                                                            
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                            <div class="col-md-12">
                                                                <h3 class="bold">Wie groß ist die Beheizte Wohnfläche?</h3>
                                                            </div>
                                                            <div class="col-md-12 flex_me">
                                                            <input type="text" class="form-control" name="living_space" disabled value="{{ old('living_space', $customer->living_space) }}">
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
                                                            <input type="text" class="form-control" name="unusable_space" disabled value="{{ old('unusable_space', $customer->unusable_space) }}">
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
                                                            <input type="text" class="form-control" name="number_people"disabled id="number_people"  value="{{ old('number_people', $customer->number_people) }}" > 
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
                                                                <select class="form-control form-element" name="roof_type" id="roof" disabled>
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
                                                                <input type="text" class="form-control form-element"  disabled name="roof_age" id="roof_age" value="{{ old('roof_age', $customer->roof_age) }}" />
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
                                                                <input type="text" class="form-control textbox" disabled name="tile_name" value="{{ old('tile_name', $customer->tile_name) }}">
                                                            
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
                                                                <select class="form-control form-element" disabled name="heating_system_type" id="heating_system_type">
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
                                                                <input type="text" class="form-control form-element" disabled name="heating_system_age" id="heating_system_age" value="{{ old('heating_system_age', $customer->heating_system_age)}}"/>
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
                                                                <input type="text" class="form-control form-element" disabled name="heating_system_year" id="heating_system_year" value="{{ old('heating_system_year', $customer->heating_system_year)}}" />
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
                                                            <select name="heating_type" id="heating_type" class="form-control" disabled>
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
                                                            <select name="installation_location" id="installation_location" class="form-control" disabled>
                                                                <option value="">Bitte wählen</option>
                                                                <option value="KG" @if($customer->installation_location == "KG") selected @endif>KG</option>
                                                                <option value="EG" @if($customer->installation_location == "EG") selected @endif>EG</option>
                                                                <option value="OG" @if($customer->installation_location == "OG") selected @endif>OG</option>
                                                                <option value="DG" @if($customer->installation_location == "DG") selected @endif>DG</option>
                                                                <option value="SONSTIGES" @if($customer->installation_location == "SONSTIGES") selected @endif>SONSTIGES</option>
                                                            </select>

                                                                <input type="text" class="form-control" disabled name="installation_location_extra" id="installation_location_extra" value="{{ old('installation_location_extra', $customer->installation_location_extra)}}" placeholder="SONSTIGIES..">
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
                                                                <input type="text" class="form-control form-element" disabled name="annual_consumption" value="{{ old('annual_consumption', $customer->annual_consumption)}}"  />
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
                                                                <input type="text" class="form-control form-element mr-1"disabled  name="annual_heating_energy_consumption" id="annual_heating_energy_consumption" value="{{ old('annual_heating_energy_consumption', $customer->annual_heating_energy_consumption)}}" />
                                                                <span  id="heat-energy">CMB</span>
                                                                <input type="text" class="form-control form-element mr-1"disabled name="annual_heating_energy_consumption_kwh" id="annual_heating_energy_consumption_kwh"  value="{{ old('annual_heating_energy_consumption_kwh, , $customer->annual_heating_energy_consumption_kwh')}}" /> 
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
                                                                <select class="form-control form-element" disabled name="electric_car" id="electric_car">
                                                                    <option selected disabled></option>
                                                                    <option value="Ja" @if( $customer->electric_car)=="Ja" selected @endif>Ja</option>
                                                                    <option value="Nein" @if( $customer->electric_car)=="Nein" selected @endif>Nein</option>
                                                                </select>
                                                                <!-- When Nein, the below text box should be hidden -->
                                                            </div>
                                                            <div class="col-md-6 flex_me">
                                                                <input type="text" class="form-control form-element" disabled name="electric_car_plan" id="electric_car_plan" value="{{ old('electric_car_plan', $customer->electric_car_plan)}}" style="display:none;" />
                                                                <span style="position: absolute; right: 20px;"  disabled id="electric_car_plan_l">ANZAHLE</span>

                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>

                                        </div> 
                                </div>
                        </div>
                    </div>

                
                    <!-- Other sections can be added similarly based on the data available -->
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

@endsection

@section('script')
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js')}}"></script>
<script src="{{ asset('app-assets/js/core/app.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/components.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
<!-- END: Theme JS-->

<script>
$(document).ready(function() {
    $('#branch').select2();
    $('#supervisor').select2();
    $('#language').select2();
    $('#place_birth').select2();
    $('#nationality').select2();
});
</script>

<script>
var i = 0;

function loadPositions(departmentId, $positionsSelect) {
    $.ajax({
        url: '/get-positions/' + departmentId,
        type: 'GET',
        success: function(data) {
            $positionsSelect.empty();
            $.each(data, function(key, value) {
                $positionsSelect.append('<option value="' + value.id + '">' + value.position +
                    '</option>');
            });
            $positionsSelect.select2(); // Reinitialize select2
        }
    });
}
</script>

<script>
$(document).ready(function() {
    // Save language
    $('#save-language-button').click(function(e) {
        e.preventDefault();
        var language = $('input[name="language"]').val();

        $.ajax({
            url: '{{ route("save.language") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                language: language
            },
            success: function(response) {
                toastr.success("Language saved successfully");
                $('#new_lang').modal('hide');
                loadLanguages();
            },
            error: function(response) {
                toastr.error("Error: Language not saved");
            }
        });
    });

    // Load languages
    function loadLanguages() {
        $.ajax({
            url: '{{ route("load.languages") }}',
            type: 'GET',
            success: function(response) {
                var select = $('#language');
                select.empty(); // Clear existing options
                $.each(response, function(index, language) {
                    select.append('<option value="' + language.id + '">' + language.language + '</option>');
                });
            },
            error: function(response) {
                toastr.error("Error: Languages could not be loaded");
            }
        });
    }

    // Call loadLanguages on page load
    loadLanguages();
});
</script>
  

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

    // function highlightStars(rating, index) {
    //     const stars = rating.querySelectorAll('.star');
    //     stars.forEach((star, i) => {
    //         if (i <= index) {
    //             star.classList.add('hovered');
    //         } else {
    //             star.classList.remove('hovered');
    //         }
    //     });
    // }

    // function resetStars(rating) {
    //     const ratingValue = rating.dataset.rating - 1;
    //     const stars = rating.querySelectorAll('.star');
    //     stars.forEach((star, index) => {
    //         if (index <= ratingValue) {
    //             star.classList.add('selected');
    //             star.classList.remove('hovered');
    //         } else {
    //             star.classList.remove('selected');
    //             star.classList.remove('hovered');
    //         }
    //     });
    // }

    // function updateInput(rating) {
    //     const category = rating.dataset.category;
    //     const ratingValue = rating.dataset.rating;
    //     document.querySelector(`input[name=${category}_rating]`).value = ratingValue;
    // }
});
</script>

<!-- Age Calculation  -->
<script>
function calculateAge() {
    var birthDate = document.getElementById("dob").value;
    console.log(birthDate);
    if (birthDate) {
        var today = new Date();
        var birthDateObj = new Date(birthDate);
        var age = today.getFullYear() - birthDateObj.getFullYear();
        var monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
            age--;
        }
        document.getElementById("age").value = age;
    } else {
        document.getElementById("age").value = "";
    }
}

function calculateBirthDate() {
    var age = document.getElementById("age").value;
    if (age) {
        var today = new Date();
        var birthYear = today.getFullYear() - age;
        var birthDateObj = new Date(birthYear, today.getMonth(), today.getDate());
        var monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
            birthYear--;
            birthDateObj = new Date(birthYear, today.getMonth(), today.getDate());
        }
        var birthDateString = birthDateObj.toISOString().split('T')[0];
        document.getElementById("dob").value = birthDateString;
    } else {
        document.getElementById("dob").value = "";
    }
}
</script>

@endsection
