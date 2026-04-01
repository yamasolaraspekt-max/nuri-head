@extends('admin.layouts.app')
@section('title') Heizkörperkonfiguration @stop

@section('style') 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    
#slide {
    position: fixed;
    top: 143px;
    right: -100%; /* Start hidden off the right side */
    background: #f5f5f5;
    width: 61%;
    transition: right 0.5s ease; /* Smooth animation */
    z-index: 1000;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

#slide.show {
    right: 9px; /* Slide in from the right */
}

 
.close-slider { 
    background: #de1313;
    color: white;
    border: 0;
    height: 56px;
    border-radius: 10px 0 0 10px;
}

.slide-panel {
   position: fixed;
    top: 143px;
    right: -100%; /* Start hidden off the right side */
    background: #f5f5f5;
    width: 61%;
    transition: right 0.5s ease; /* Smooth animation */
    z-index: 1000;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.slide-panel.show {
    right: 9px; /* Slide in from the right */
}

.close {
    position: relative;
    float: left;
}

#close_slider_save {
    background: #de1313;
    color: white;
    border: 0;
    height: 56px;
    border-radius: 10px 0 0 10px;
}


.select2-selection--single {
        background: transparent !important;
    height: 81px !important;
}
.select2-results__option {
    height:106px !important;
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
                                <h2 class="content-header-title float-left mb-0">Heizkörperkonfiguration</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                          <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Neue</a>
                                        </li>
                                        
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body"> 
                <section id="all">
                    <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-icon btn-icon  btn-primary mr-1 mb-1 waves-effect waves-light float-right" id="add_more" ><i class="feather icon-plus"> </i>Neue</button>
                                        @if(url()->previous() == route('customer.product.create', ['id'=>$data->id, 'postcode'=>$data->postcode, 'address_no'=>$data->address_no]))
                                                <button type="button" class="btn btn-icon btn-icon btn-light mr-1 mb-1 waves-effect waves-light float-right" onclick="window.location.href='{{ url('customer_product_create/'.$data->id.'/'.$data->postcode.'/'.$data->address_no) }}'">
                                                    <i class="feather icon-arrow-left"></i> Zurück zum Kunden
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-icon btn-icon btn-light mr-1 mb-1 waves-effect waves-light float-right" onclick="window.history.back()">
                                                    <i class="feather icon-arrow-left"></i> Zurück
                                                </button>
                                            @endif

                                </div>
                            </div>   
                            <article>
                                    <div class="row">
                                <!-- Customer Information -->
                                <div class="col-md-12 mb-1">
                                    <label for="customer" class="form-label h3 primary"><strong>Kunde</strong></label>  
                                  <input type="text" class="form-control" value="{{$data->name}} {{$data->lastname }} - {{ $data->city}}" readonly> 
                                </div>
                            </div>
                            @foreach ($rediators as $red)
                                <div class="row">
                                    <!-- Radiator Image Section -->
                                    <div class="col-md-4 mb-1" style="border: 2px solid #c3c3c3; height: auto; display: flex; justify-content: center; align-items: center;">
                                        <div class="card" style="width: 100%; position: relative;">
                                            @if($red->image)
                                            <img class="card-img-top img-fluid" src="{{ asset('images/radiators/'.$red->image)}}" alt="Radiator Image">
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Radiator Information Section -->
                                    <div class="col-md-8 mb-1">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><strong>NR.</strong> {{ $red->number }}</p>
                                                <p><strong>ETAGE</strong> {{ $red->floor }}</p>
                                                <p><strong>RAUM</strong> {{ $red->room }}</p>
                                                <p><strong>TYP</strong> {{ $red->type }}</p>
                                                <p><strong>GRÖSSE</strong> B {{ $red->width }} x H {{ $red->height }} x T {{ $red->depth }} mm</p>
                                                <p><strong>NISCHE</strong> B {{ $red->niche_top }} x H {{ $red->niche_bottom }} x T {{ $red->niche_left }} mm</p>
                                                <p>{{ $red->has_window_sill ? 'Fensterbank vorhanden' : 'Keine Fensterbank' }}</p>
                                                <hr>
                                                <p class="h4 primary"><strong>ANSCHLÜSSE</strong></p>
                                                <p><strong>Vorlaufventil</strong> {{ $red->supply_valve }} @if($red->supply_valve_presettable) (voreinstellbar) @endif</p>
                                                <p><strong>Rücklaufventil</strong> {{ $red->return_valve }} @if($red->return_valve_present) (vorhanden) @endif</p>
                                                <p><strong>Bauform</strong> {{ $red->design }}</p>
                                                <p><strong>Thermostatkopf</strong> {{ $red->renew_thermostat_head ? 'muss erneuert werden' : 'muss nicht erneuert werden' }}</p>
                                                <p><strong>Steckdose</strong> @if($red->has_socket) vorhanden, Entfernung {{ $red->socket_distance }} m @else nicht vorhanden @endif</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-flat-primary mr-1 mb-1 waves-effect waves-light edit-btn" data-id="{{ $red->id }}"> <i class="feather icon-edit"></i> Bearbeiten</button>
                                            <a type="button" class="btn btn-flat-danger mr-1 mb-1 waves-effect waves-light delete-btn" data-id="{{ $red->id }}"><i class="feather icon-trash danger"></i> Löschen</a>

                                            <!-- Hidden Form for Deletion -->
                                            <form id="delete-form-{{ $red->id }}" action="{{ route('radiator.config.delete', $red->id) }}" method="GET" style="display: none;">
                                                @csrf
                                            </form>                                    
                                        </div>
                                </div>
                                <hr>

                                <!-- Edit Slider -->
                                <div id="editSlide{{ $red->id }}" class="slide-panel" style="overflow-y:auto; max-height:80vh; width:60%;">
                                    <div class="container">
                                        <form novalidate method="post" action="{{ action('App\Http\Controllers\RadiatorInstallationController@update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ $data->id }}" name="customer_id">
                                            <input type="hidden" value="{{ $data->postcode }}" name="postcode"> 
                                            <input type="hidden" value="{{ $red->id }}" name="id"> 
                                            <div class="close"> 
                                                <button type="button" class="close-slider" data-id="{{ $red->id }}"> <i class="feather icon-x white"></i> Schließen</button>
                                            </div>
                                            <div class="row" style="background: #8fc73e; color: white; height: 56px; align-content: center; justify-content: center;"> 
                                                <h2 class="title white"> Bearbeiten </h2>
                                            </div>
                                            <div class="row mt-2">
                                                <!-- Customer Information -->
                                                <div class="col-md-12 mb-1">
                                                    <label for="customer" class="form-label h3 primary"><strong>Kunde</strong></label>
                                                    <input type="text" class="form-control" value="{{$data->name }} {{$data->lastname }} - {{ $data->city}}" readonly> 
                                                </div>
                                            </div>

                                            <div class="row"> 
                                                <div class="col-md-6 mb-1">
                                                    <label for="type" class="form-label h3 primary"><strong>Art</strong></label>
                                                    <select class="form-control type" id="type" name="type" style="width:100%">
                                                            <option value="" disabled selected>Wählen...</option>
                                                            <option value="Vertikal profilierte Flachheizkörper" data-image="{{ asset('images/radiators/Radiator/csm_vertically_profiled_flat_radiator_isometric_b4099d875e.png') }}" @if($red->type == 'Vertikal profilierte Flachheizkörper') selected @endif>Vertikal profilierte Flachheizkörper</option>
                                                            <option value="Glattwandig profilierte Flachheizkörper" data-image="{{ asset('images/radiators/Radiator/csm_smoothly_profiled_flat_radiator_isometric_65fac7a2fd.png') }}" @if($red->type == 'Glattwandig profilierte Flachheizkörper') selected @endif>Glattwandig profilierte Flachheizkörper</option>
                                                            <option value="Kompaktheizkörper" data-image="{{ asset('images/radiators/Radiator/Kompakt.png') }}" @if($red->type == 'Kompaktheizkörper') selected @endif>Kompaktheizkörper</option>
                                                            <option value="Ventilheizkörper" data-image="{{ asset('images/radiators/Radiator/Ventil.png') }}" @if($red->type == 'Ventilheizkörper') selected @endif>Ventilheizkörper</option>
                                                            <option value="DIN Stahlrohr Radiator" data-image="{{ asset('images/radiators/Radiator/csm_steel_radiator_isometric_03e7ee2d16.png') }}" @if($red->type == 'DIN Stahlrohr Radiator') selected @endif>DIN Stahlrohr Radiator</option>
                                                            <option value="DIN Stahl Radiator" data-image="{{ asset('images/radiators/Radiator/csm_steel_pipe_radiator_isometric_ee41a0d27b.png') }}" @if($red->type == 'DIN Stahl Radiator') selected @endif>DIN Stahl Radiator</option>
                                                            <option value="Gußradiator" data-image="{{ asset('images/radiators/Radiator/csm_cast_iron_radiator_isometric_791cc17f49.png') }}" @if($red->type == 'Gußradiator') selected @endif>Gußradiator</option>
                                                            <option value="Handtuchheizkörper" @if($red->type == 'Handtuchheizkörper') selected @endif>Handtuchheizkörper</option> 
                                                    </select>

                                                </div>

                                                <!-- Art -->
                                                <div class="col-md-6 mb-1">
                                                    <label for="type" class="form-label h3 primary"><strong>Typ</strong></label>
                                                    <select class="form-control radiator_type" id="radiator_type" name="radiator_type" style="width:100%">
                                                        <option value="" disabled selected>Wählen...</option>
                                                        <option value="10" data-image="{{ asset('images/radiators/Radiator/10.png') }}" @if($red->radiator_type == '10') selected @endif>10</option>
                                                        <option value="11" data-image="{{ asset('images/radiators/Radiator/11.png') }}" @if($red->radiator_type == '11') selected @endif>11</option>  
                                                        <option value="21" data-image="{{ asset('images/radiators/Radiator/21.png') }}" @if($red->radiator_type == '21') selected @endif>21</option> 
                                                        <option value="22" data-image="{{ asset('images/radiators/Radiator/22.png') }}" @if($red->radiator_type == '22') selected @endif>22</option>  
                                                        <option value="33" data-image="{{ asset('images/radiators/Radiator/33.png') }}" @if($red->radiator_type == '33') selected @endif>33</option> 
                                                    </select>

                                                </div>
                                                <div class="col-md-6 mb-1" id="ifEmpty"></div>

                                        
                                                <div class="col-md-2 mb-1">
                                                    <label for="number" class="form-label h3 primary"><strong>Nr.</strong></label>
                                                    <input type="text" class="form-control" id="number" name="number" value="{{$red->number}}">
                                                </div>
                                                <!-- Floor -->
                                                <div class="col-md-3 mb-1">
                                                    <label for="floor" class="form-label h3 primary"><strong>Etage</strong></label>
                                                    <select class="form-control" id="floor" name="floor">
                                                        <option value="" disabled selected>Wählen...</option>
                                                        <option value="Keller" @if($red->floor == 'Keller') selected @endif>Keller</option>
                                                        <option value="Erdgeschoss" @if($red->floor == 'Erdgeschoss') selected @endif>Erdgeschoss</option>
                                                        <option value="Obergeschoss" @if($red->floor == 'Obergeschoss') selected @endif>Obergeschoss</option>
                                                        <option value="Dachgeschoss" @if($red->floor == 'Dachgeschoss') selected @endif>Dachgeschoss</option>
                                                        <option value="Anbau" @if($red->floor == 'Anbau') selected @endif>Anbau</option>
                                                        <option value="sontiges" @if($red->floor == 'sontiges') selected @endif>sontiges</option>
                                                    </select>

                                                </div>

                                                <!-- Room -->
                                                <div class="col-md-3 mb-1">
                                                    <label for="room" class="form-label h3 primary"><strong>Raum</strong></label>
                                                    <select class="form-control" id="room" name="room">
                                                        <option value="" disabled selected>Wählen...</option>
                                                        <option value="Keller" @if($red->room == 'Keller') selected @endif>Keller</option>
                                                        <option value="Abstellraum" @if($red->room == 'Abstellraum') selected @endif>Abstellraum</option>
                                                        <option value="Bad" @if($red->room == 'Bad') selected @endif>Bad</option>
                                                        <option value="Kinderzimmer" @if($red->room == 'Kinderzimmer') selected @endif>Kinderzimmer</option>
                                                        <option value="Wohnzimmer" @if($red->room == 'Wohnzimmer') selected @endif>Wohnzimmer</option>
                                                        <option value="Esszimmer" @if($red->room == 'Esszimmer') selected @endif>Esszimmer</option>
                                                        <option value="Gästezimmer" @if($red->room == 'Gästezimmer') selected @endif>Gästezimmer</option>
                                                        <option value="Flur" @if($red->room == 'Flur') selected @endif>Flur</option>
                                                        <option value="Dach" @if($red->room == 'Dach') selected @endif>Dach</option>
                                                        <option value="sontiges" @if($red->room == 'sontiges') selected @endif>sontiges</option>
                                                    </select>

                                                </div>
                                                <div class="col-md-2 mb-1">
                                                    <label for="room" class="form-label h3 primary"><strong>Fläche <small>(qm)</small></strong></label>
                                                    <input type="text" name="room_size" class="form-control" id="room_size" value="{{$red->size }}">
                                                </div>

                                                <div class="col-md-2 mb-1 limbs-container" data-id="{{ $red->id }}" id="limbs-{{ $red->id }}" style="display:none;">
                                                    <label for="limbs_{{ $red->id }}" class="form-label h3 primary"><strong>Glieder</strong></label>
                                                    <input type="text" name="limbs" class="form-control" value="{{ $red->limbs}}" >
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-1" style="border: 2px solid #c3c3c3; height: 434px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                                    <div class="card" style="width: 100%; position: relative;">
                                                        <span id="delete-image_edit" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px; display: none;">
                                                            <i class="feather icon-trash"></i>
                                                        </span>
                                                        <img class="card-img-top img-fluid" src="{{ asset('images/radiators/'.$red->image)}}" alt="Radiator Image" id="radiator_image_edit"  >
                                                        <div class="card-body text-center">
                                                            <h4 class="card-title">Heizkörper Bild</h4>
                                                            <!-- Image Upload Input -->
                                                            <input type="file" name="image" class="form-control" id="image_input_edit">
                                                        </div>
                                                    </div>
                                                </div> 
                                                    <div class="col-md-8 mb-1"> 
                                                        <div class="row">
                                                            <div class="col-md-6 mb-1">
                                                                <label class="form-label h3 primary"><strong>Größe</strong></label>
                                                                <fieldset class="form-group">
                                                                        <label for="basicInput">Breite</label>
                                                                        <input type="text" class="form-control" placeholder="Breite" name="width" value="{{ $red->width}}"> 
                                                                </fieldset>

                                                                <fieldset class="form-group">
                                                                        <label for="height">Höhe</label>
                                                                        <input type="text" class="form-control" placeholder="Höhe" name="height" value="{{ $red->height}}"> 
                                                                </fieldset>

                                                                <fieldset class="form-group">
                                                                        <label for="height">Tiefe</label>
                                                                        <input type="text" class="form-control" placeholder="Höhe" name="depth" value="{{ $red->depth}}"> 
                                                                </fieldset>  
                                                            </div>

                                                            <div class="col-md-6 mb-1">
                                                                <label class="form-label h3 primary"><strong>Nische</strong></label>
                                                                <fieldset class="form-group">
                                                                        <label for="oben">oben</label>
                                                                        <input type="text" class="form-control" placeholder="oben" name="niche_top" value="{{ $red->niche_top}}"> 
                                                                </fieldset>
                                                                <fieldset class="form-group">
                                                                        <label for="niche_bottom">unten</label>
                                                                        <input type="text" class="form-control" placeholder="unten" name="niche_bottom" value="{{ $red->niche_bottom}}"> 
                                                                </fieldset>
                                                                <fieldset class="form-group">
                                                                        <label for="niche_left">links</label>
                                                                        <input type="text" class="form-control" placeholder="links" name="niche_left" value="{{ $red->niche_left}}"> 
                                                                </fieldset>
                                                                <fieldset class="form-group">
                                                                        <label for="niche_right">rechts</label>
                                                                        <input type="text" class="form-control" placeholder="rechts" name="niche_right" value="{{ $red->niche_right}}"> 
                                                                </fieldset>  
                                                                <label class="form-label   mt-2"><strong>Fensterbank</strong></label> 
                                                                <ul class="list-unstyled mb-0">
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <div class="custom-control custom-radio">
                                                                                <input type="radio" class="custom-control-input" name="has_window_sill" id="has_window_still1" @if($red->has_window_sill== 1) checked @endif>
                                                                                <label class="custom-control-label" for="has_window_still1">Ja</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    </li>
                                                                    <li class="d-inline-block mr-2">
                                                                        <fieldset>
                                                                            <div class="custom-control custom-radio">
                                                                                <input type="radio" class="custom-control-input" name="has_window_sill" id="has_window_still2" @if($red->has_window_sill== 0) checked @endif>
                                                                                <label class="custom-control-label" for="has_window_still2">Nein</label>
                                                                            </div>
                                                                        </fieldset>
                                                                    </li> 
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="row">
                                                            <div class="title h3 primary mb-2"><strong>ANSCHLÜSSE</strong></div>
                                                            <div class="col-md-12 mb-1"> 
                                                                <div class="col-md-12 col-md-12 select d-flex"> 
                                                                    <div class="col-md-2">
                                                                            <label for="supply_valve" class="form-label">Vorlaufventil</label> 
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control" id="supply_valve" name="supply_valve">
                                                                                <option value="" disabled selected>Wählen...</option>
                                                                                <option value="oben links" @if($red->supply_valve == 'oben links') selected @endif>oben links</option>
                                                                                <option value="oben rechts" @if($red->supply_valve == 'oben rechts') selected @endif>oben rechts</option>
                                                                                <option value="unten links" @if($red->supply_valve == 'unten links') selected @endif>unten links</option>
                                                                                <option value="unten rechts" @if($red->supply_valve == 'unten rechts') selected @endif>unten rechts</option>
                                                                            </select>

                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <fieldset>
                                                                                <div class="custom-control custom-checkbox">
                                                                                    <input type="checkbox" class="custom-control-input" checked="" name="supply_valve_presettable" id="supply_valve_presettable" @if($red->supply_valve_presettable== "on") checked @endif>
                                                                                    <label class="custom-control-label" for="supply_valve_presettable">Voreinstellbar</label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </div> 
                                                                    </div>

                                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                                            <div class="col-md-2">
                                                                                <label for="return_valve" class="form-label mt-1">Rücklaufventil</label>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <select class="form-control" id="return_valve" name="return_valve">
                                                                                        <option value="" disabled selected>Wählen...</option>
                                                                                        <option value="unten links Wand" @if($red->return_valve == 'unten links Wand') selected @endif>unten links Wand</option>
                                                                                        <option value="unten links Rückwand" @if($red->return_valve == 'unten links Rückwand') selected @endif>unten links Rückwand</option>
                                                                                        <option value="unten links Boden" @if($red->return_valve == 'unten links Boden') selected @endif>unten links Boden</option>
                                                                                        <option value="unten rechts Wand" @if($red->return_valve == 'unten rechts Wand') selected @endif>unten rechts Wand</option>
                                                                                        <option value="unten rechts Rückwand" @if($red->return_valve == 'unten rechts Rückwand') selected @endif>unten rechts Rückwand</option>
                                                                                        <option value="unten rechts Boden" @if($red->return_valve == 'unten rechts Boden') selected @endif>unten rechts Boden</option>
                                                                                        <option value="Boden unten links" @if($red->return_valve == 'Boden unten links') selected @endif>Boden unten links</option>
                                                                                        <option value="Boden unten rechts" @if($red->return_valve == 'Boden unten rechts') selected @endif>Boden unten rechts</option>
                                                                                    </select>

                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <fieldset>
                                                                                    <div class="custom-control custom-checkbox">
                                                                                        <input type="checkbox" class="custom-control-input" checked="" name="return_valve_present" id="return_valve_present"  @if($red->return_valve_present== "on") checked @endif>
                                                                                        <label class="custom-control-label" for="return_valve_present">Vorhanden</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </div> 
                                                                    </div>

                                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                                        <div class="col-md-2">
                                                                            <label for="design" class="form-label mt-1">Bauform</label>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <select class="form-control" id="design" name="design">
                                                                                <option value="" disabled selected>Wählen...</option>
                                                                                <option value="Eck" @if($red->design == 'Eck') selected @endif>Eck</option>
                                                                                <option value="Durchgang" @if($red->design == 'Durchgang') selected @endif>Durchgang</option>
                                                                                <option value="Winkeleck" @if($red->design == 'Winkeleck') selected @endif>Winkeleck</option>
                                                                            </select>

                                                                        </div> 
                                                                    </div>

                                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                                        <div class="col-md-2">
                                                                            <label for="design" class="form-label mt-1">Thermostatkopf erneuern</label>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-2">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head1" @if($red->renew_thermostat_head=="on") checked @endif>
                                                                                            <label class="custom-control-label" for="renew_thermostat_head1">Ja</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-2">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head2" @if($red->renew_thermostat_head!="on") checked @endif>
                                                                                            <label class="custom-control-label" for="renew_thermostat_head2">Nein</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li> 
                                                                            </ul>
                                                                        </div> 
                                                                    </div>

                                                                    <div class="col-md-12 col-md-12 select d-flex mt-2">  
                                                                        <div class="col-md-4">
                                                                            <select class="form-control" id="design">
                                                                                <option selected>Wählen...</option>
                                                                                <option value="design1">Design 1</option>
                                                                                <option value="design2">Design 2</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <select class="form-control" id="design">
                                                                                <option selected>Wählen...</option>
                                                                                <option value="design1">Design 1</option>
                                                                                <option value="design2">Design 2</option>
                                                                            </select>
                                                                        </div> 

                                                                        <div class="col-md-4">
                                                                            <select class="form-control" id="design">
                                                                                <option selected>Wählen...</option>
                                                                                <option value="design1">Design 1</option>
                                                                                <option value="design2">Design 2</option>
                                                                            </select>
                                                                        </div> 
                                                                    </div>
                                
                                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                                        <div class="col-md-3">
                                                                            <label for="design" class="form-label mt-1">Steckdose vorhanden</label>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-2">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="has_socket" id="has_socket1" @if($red->has_socket=="on") checked @endif>
                                                                                            <label class="custom-control-label" for="has_socket1">Ja</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-2">
                                                                                    <fieldset>
                                                                                        <div class="custom-control custom-radio">
                                                                                            <input type="radio" class="custom-control-input" name="has_socket" id="has_socket2" @if($red->has_socket!="on") checked @endif>
                                                                                            <label class="custom-control-label" for="has_socket2">Nein</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li> 
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-md-4 d-flex">
                                                                            <input type="text" class="form-control" name="socket_distance" value="{{$red->socket_distance}}"> 
                                                                            <label for="socket_distance" class="form-label mt-1"> m</label>
                                                                        </div> 
                                                                    </div> 
                                                                </div>
                                                        </div>  
                                                    </div>  
                                                </div>  
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-icon btn-icon btn-light mr-1 mb-1 waves-effect waves-light float-right">Speichern</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach   
                    </div> 
                    <hr> 

                    <div id="slide" style="display:none;overflow-y:auto; max-height:80vh; width:60%;">
                        <div class="container">
                              <form novalidate method="post" action="{{ action('App\Http\Controllers\RadiatorInstallationController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $data->id }}" name="customer_id">
                                <input type="hidden" value="{{ $data->postcode }}" name="postcode"> 
                                 <div class="close"> 
                                        <button type="button"   id="close_slider_save"> <i class="feather icon-x white"></i> Schließen</button>
                                    </div>
                                <div class="row" style="background: #8fc73e;  color: white;     height: 56px;    align-content: center; justify-content: center;"> 
                                    <h2 class="title white"> Neu hinzufügen </h2>
                                </div> 

                                <div class="row mt-2">
                                    <!-- Customer Information -->
                                    <div class="col-md-12 mb-1">
                                        <label for="customer" class="form-label h3 primary"><strong>Kunde</strong></label>
                                          <input type="text" class="form-control" value="{{$data->name }} {{$data->lastname }} - {{ $data->city}}" readonly> 
                                    </div>
                                </div>

                                <div class="row"> 
                                    <div class="col-md-6 mb-1">
                                        <label for="type_{{ $data->id }}" class="form-label h3 primary"><strong>Art</strong></label>
                                        <select class="form-control type-selector type select22" data-id="{{ $data->id }}" name="type" style="width:100%">
                                            <option selected>Wählen...</option>
                                            <option value="Kompaktheizkörper" data-image="{{ asset('images/radiators/Radiator/csm_vertically_profiled_flat_radiator_isometric_b4099d875e.png') }}" >Vertikal profilierte Flachheizkörper</option>
                                            <option value="Kompaktheizkörper" data-image="{{ asset('images/radiators/Radiator/csm_smoothly_profiled_flat_radiator_isometric_65fac7a2fd.png') }}" >Glattwandig profilierte Flachheizkörper</option>
                                            <option value="Kompaktheizkörper" data-image="{{ asset('images/radiators/Radiator/Kompakt.png') }}" >Kompaktheizkörper</option>
                                            <option value="Ventilheizkörper" data-image="{{ asset('images/radiators/Radiator/Ventil.png') }}" >Ventilheizkörper</option>
                                            <option value="DIN Stahl Radiator" data-image="{{ asset('images/radiators/Radiator/csm_steel_radiator_isometric_03e7ee2d16.png') }}">DIN Stahlrohr  Radiator</option>
                                            <option value="DIN Stahl Radiator" data-image="{{ asset('images/radiators/Radiator/csm_steel_pipe_radiator_isometric_ee41a0d27b.png') }}">DIN Stahl Radiator</option>
                                            <option value="Gußradiator" data-image="{{ asset('images/radiators/Radiator/csm_cast_iron_radiator_isometric_791cc17f49.png') }}" >Gußradiator</option>
                                            <option value="Handtuchheizkörper">Handtuchheizkörper</option> 
                                        </select>
                                    </div>

                                    <!-- Art -->
                                     <div class="col-md-6 mb-1 radiator-type-container" data-id="{{ $data->id }}">
                                        <label for="radiator_type" class="form-label h3 primary"><strong>Typ</strong></label>
                                        <select class="form-control radiator-type-selector radiator_type select22" id="radiator_type_{{ $data->id }}" name="radiator_type" style="width:100%"> 
                                            <option selected>Wählen...</option>
                                            <option value="10" data-image="{{ asset('images/radiators/Radiator/10.png') }}">10</option>
                                            <option value="11" data-image="{{ asset('images/radiators/Radiator/11.png') }}">11</option>  
                                            <option value="21" data-image="{{ asset('images/radiators/Radiator/21.png') }}">21</option> 
                                            <option value="22" data-image="{{ asset('images/radiators/Radiator/22.png') }}">22</option>  
                                            <option value="33" data-image="{{ asset('images/radiators/Radiator/33.png') }}"> 33</option> 
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-1" id="ifEmpty-{{ $data->id }}" style="display:none"></div>

                             
                                      <div class="col-md-2 mb-1">
                                        <label for="number" class="form-label h3 primary"><strong>Nr.</strong></label>
                                        <input type="text" class="form-control" id="number" name="number">
                                    </div>
                                    <!-- Floor -->
                                    <div class="col-md-3 mb-1">
                                        <label for="floor" class="form-label h3 primary"><strong>Etage</strong></label>
                                        <select class="form-control" id="floor" name="floor">
                                            <option selected>Wählen...</option>
                                            <option value="Keller">Keller</option>
                                            <option value="Erdgeschoss">Erdgeschoss</option>
                                            <option value="Obergeschoss">Obergeschoss</option>
                                            <option value="Dachgeschoss">Dachgeschoss</option>
                                            <option value="Anbau">Anbau</option>
                                            <option value="sontiges">sontiges</option>
                                        </select>
                                    </div>

                                    <!-- Room -->
                                    <div class="col-md-3 mb-1">
                                        <label for="room" class="form-label h3 primary"><strong>Raum</strong></label>
                                        <select class="form-control" id="room" name="room">
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
                                            <option value="sontiges">sontiges</option> 
                                        </select>
                                    </div>
                                     <div class="col-md-2 mb-1">
                                        <label for="room" class="form-label h3 primary"><strong>Fläche <small>(qm)</small></strong></label>
                                        <input type="text" name="room_size" class="form-control" id="room_size">
                                    </div>

                                    <div class="col-md-2 mb-1 limbs-container" data-id="{{ $data->id }}" id="limbs-{{ $data->id }}" style="display:none;">
                                        <label for="limbs_{{ $data->id }}" class="form-label h3 primary"><strong>Glieder</strong></label>
                                        <input type="text" name="limbs" class="form-control" >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-1" style="border: 2px solid #c3c3c3; height: 434px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                        <div class="card" style="width: 100%; position: relative;">
                                            <span id="delete-image" class="btn btn-danger btn-sm position-absolute" style="top: 10px; right: 10px; display: none;">
                                                <i class="feather icon-trash"></i>
                                            </span>
                                            <img class="card-img-top img-fluid" src="" alt="Radiator Image" id="radiator_image" style="display: none;">
                                            <div class="card-body text-center">
                                                <h4 class="card-title">Heizkörper Bild</h4>
                                                <!-- Image Upload Input -->
                                                <input type="file" name="image" class="form-control" id="image_input">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-md-8 mb-1"> 
                                        <div class="row">
                                            <div class="col-md-6 mb-1">
                                                <label class="form-label h3 primary"><strong>Größe</strong></label>
                                                <fieldset class="form-group">
                                                        <label for="basicInput">Breite</label>
                                                        <input type="text" class="form-control" placeholder="Breite" name="width"> 
                                                </fieldset>

                                                <fieldset class="form-group">
                                                        <label for="height">Höhe</label>
                                                        <input type="text" class="form-control" placeholder="Höhe" name="height"> 
                                                </fieldset>

                                                <fieldset class="form-group">
                                                        <label for="height">Tiefe</label>
                                                        <input type="text" class="form-control" placeholder="Höhe" name="depth"> 
                                                </fieldset>  
                                            </div>

                                            <div class="col-md-6 mb-1">
                                                <label class="form-label h3 primary"><strong>Nische</strong></label>
                                                <fieldset class="form-group">
                                                        <label for="oben">oben</label>
                                                        <input type="text" class="form-control" placeholder="oben" name="niche_top"> 
                                                </fieldset>
                                                <fieldset class="form-group">
                                                        <label for="niche_bottom">unten</label>
                                                        <input type="text" class="form-control" placeholder="unten" name="niche_bottom"> 
                                                </fieldset>
                                                <fieldset class="form-group">
                                                        <label for="niche_left">links</label>
                                                        <input type="text" class="form-control" placeholder="links" name="niche_left"> 
                                                </fieldset>
                                                <fieldset class="form-group">
                                                        <label for="niche_right">rechts</label>
                                                        <input type="text" class="form-control" placeholder="rechts" name="niche_right"> 
                                                </fieldset>  
                                                <label class="form-label   mt-2"><strong>Fensterbank</strong></label> 
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="has_window_still" id="has_window_still4" checked="">
                                                                <label class="custom-control-label" for="has_window_still4">Ja</label>
                                                            </div>
                                                        </fieldset>
                                                    </li>
                                                    <li class="d-inline-block mr-2">
                                                        <fieldset>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" class="custom-control-input" name="has_window_still" id="has_window_still5">
                                                                <label class="custom-control-label" for="has_window_still5">Nein</label>
                                                            </div>
                                                        </fieldset>
                                                    </li> 
                                                </ul>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="title h3 primary mb-2"><strong>ANSCHLÜSSE</strong></div>
                                            <div class="col-md-12 mb-1"> 
                                                <div class="col-md-12 col-md-12 select d-flex"> 
                                                    <div class="col-md-2">
                                                            <label for="supply_valve" class="form-label">Vorlaufventil</label> 
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="supply_valve" name="supply_valve">
                                                                <option selected>Wählen...</option>
                                                                <option value="oben links">oben links</option>
                                                                <option value="oben rechts">oben rechts</option>
                                                                <option value="unten links">unten links</option>
                                                                <option value="unten rechts">unten rechts</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <fieldset>
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" class="custom-control-input" checked="" name="supply_valve_presettable" id="supply_valve_presettable1">
                                                                    <label class="custom-control-label" for="supply_valve_presettable1">Voreinstellbar</label>
                                                                </div>
                                                            </fieldset>
                                                        </div> 
                                                    </div>

                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                            <div class="col-md-2">
                                                                <label for="return_valve" class="form-label mt-1">Rücklaufventil</label>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select class="form-control" id="return_valve" name="return_valve">
                                                                        <option selected>Wählen...</option>
                                                                        <option value="unten links Wand">unten links Wand</option>
                                                                        <option value="unten links Rückwand">unten links Rückwand</option>
                                                                        <option value="unten links Boden">unten links Boden</option>
                                                                        <option value="unten rechts Wand">unten rechts Wand</option>
                                                                        <option value="unten rechts Rückwand">unten rechts Rückwand</option>
                                                                        <option value="unten rechts Boden">unten rechts Boden</option>
                                                                        <option value="Boden unten links">Boden unten links</option>
                                                                        <option value="Boden unten rechts">Boden unten rechts</option>
                                                                    </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <fieldset>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input" checked="" name="return_valve_present" id="return_valve_present1">
                                                                        <label class="custom-control-label" for="return_valve_present1">Vorhanden</label>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                    </div>

                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                        <div class="col-md-2">
                                                            <label for="design" class="form-label mt-1">Bauform</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="design" name="design" >
                                                                <option selected>Wählen...</option>
                                                                <option value="Eck">Eck</option>
                                                                <option value="Durchgang">Durchgang</option>
                                                                <option value="Winkeleck">Winkeleck</option> 
                                                            </select>
                                                        </div> 
                                                    </div>

                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                        <div class="col-md-2">
                                                            <label for="design" class="form-label mt-1">Thermostatkopf erneuern</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head3" checked="">
                                                                            <label class="custom-control-label" for="renew_thermostat_head3">Ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="renew_thermostat_head" id="renew_thermostat_head4">
                                                                            <label class="custom-control-label" for="renew_thermostat_head4">Nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li> 
                                                            </ul>
                                                        </div> 
                                                    </div>

                                                    <div class="col-md-12 col-md-12 select d-flex mt-2">  
                                                        <div class="col-md-4">
                                                            <select class="form-control" id="design">
                                                                <option selected>Wählen...</option>
                                                                <option value="design1">Design 1</option>
                                                                <option value="design2">Design 2</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select class="form-control" id="design">
                                                                <option selected>Wählen...</option>
                                                                <option value="design1">Design 1</option>
                                                                <option value="design2">Design 2</option>
                                                            </select>
                                                        </div> 

                                                        <div class="col-md-4">
                                                            <select class="form-control" id="design">
                                                                <option selected>Wählen...</option>
                                                                <option value="design1">Design 1</option>
                                                                <option value="design2">Design 2</option>
                                                            </select>
                                                        </div> 
                                                    </div>
                
                                                    <div class="col-md-12 col-md-12 select d-flex mt-2"> 
                                                        <div class="col-md-3">
                                                            <label for="design" class="form-label mt-1">Steckdose vorhanden</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="has_socket" id="has_socket4" checked="">
                                                                            <label class="custom-control-label" for="has_socket4">Ja</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="has_socket" id="has_socket5">
                                                                            <label class="custom-control-label" for="has_socket5">Nein</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li> 
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-4 d-flex">
                                                            <input type="text" class="form-control" name="socket_distance"> 
                                                            <label for="socket_distance" class="form-label mt-1"> m</label>
                                                        </div> 
                                                    </div> 
                                                </div>
                                        </div> 

                                    </div> 
                                
                                </div> 
                                
                                <!-- Save Button -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-icon btn-icon   btn-light mr-1 mb-1 waves-effect waves-light float-right"  >Speichern</button>
                                    </div>
                                </div>
                            </form>
                        </div> 
                        
                        <hr> 
                    </div> 
                </section>  
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();

        var id = $(this).data('id');
        var form = $('#delete-form-' + id);

        // SweetAlert2 confirmation
        Swal.fire({
            title: 'Bist du sicher?',
            text: "Diese Aktion kann nicht rückgängig gemacht werden!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja, löschen!',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form to delete the record
                form.submit();
            }
        });
    });
});

</script>
<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');

        // Hide all sliders
        $('.slide-panel').removeClass('show').css('display', 'none');

        // Show the selected slider with animation
        $('#editSlide' + id).css('display', 'block');
        setTimeout(function() {
            $('#editSlide' + id).addClass('show');
        }, 10); // Small delay to ensure the transition is applied
    });

    // Add close functionality to each slider
    $('.close-slider').on('click', function() {
        var id = $(this).data('id');

        // Hide the specific slider with animation
        $('#editSlide' + id).removeClass('show');
        setTimeout(function() {
            $('#editSlide' + id).css('display', 'none');
        }, 500); // Match the CSS transition duration
    });
});


</script>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select22').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            allowClear: true,
        });

        // Image preview after upload
        $('#image_input').change(function(event) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    $('#radiator_image').attr('src', event.target.result);
                    $('#radiator_image').show();  // Show the image
                    $('#delete-image').show();  // Show delete button
                };
                reader.readAsDataURL(this.files[0]);
            });

            // Delete image and clear file input
            $('#delete-image').click(function() {
                $('#radiator_image').attr('src', '').hide(); // Hide and clear image
                $('#image_input').val(''); // Clear file input
                $(this).hide(); // Hide delete button
            });

        // Handle add_more button click event
       $('#add_more').click(function() {
            // Get selected values
            const customer = $('#customer option:selected').text();
            const number = $('#number').val();
            const type = $('#type option:selected').text();
            const floor = $('#floor option:selected').text();
            const room = $('#room option:selected').text();

            // Prepare the text to show
            const selectedInfo = `
                <strong>Kunde:</strong> ${customer}<br>
                <strong>Nr.:</strong> ${number}<br>
                <strong>Typ:</strong> ${type}<br>
                <strong>Etage:</strong> ${floor}<br>
                <strong>Raum:</strong> ${room}
            `;

            // Set the text in the sliding panel
            $('#selected-info').html(selectedInfo);

            // Show the slide panel with animation
            const slidePanel = $('#slide');
            slidePanel.show();
            setTimeout(() => {
                slidePanel.addClass('show');
            }, 10); // Small delay to ensure transition applies
        });

        // Handle close_slider button click event
        $('#close_slider_save').click(function() {
            const slidePanel = $('#slide');
            slidePanel.removeClass('show'); // Trigger the slide-out animation
            setTimeout(() => {
                slidePanel.hide(); // Hide after the animation completes
            }, 500); // Duration should match the CSS transition
        });
    });
</script>

<script>
    $(document).ready(function() {
    function formatOption(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span>' + state.text + '</span><img src="' + $(state.element).data('image') + '" class="img-right" style="float: right; width: 300px;"/>'
        );
        return $state;
    }

    $('.radiator_type').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        minimumResultsForSearch: Infinity // hide search box
    });
});

</script>

<script>
    $(document).ready(function() {
    function formatOption(state) {
        if (!state.id) {
            return state.text;
        }
        var $state = $(
            '<span>' + state.text + '</span><img src="' + $(state.element).data('image') + '" class="img-right" style="float: right; width: 60px;"/>'
        );
        return $state;
    }

    $('.type').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        minimumResultsForSearch: Infinity // hide search box
    });
});

</script>
<script>
  $(document).ready(function() {
    $('.type-selector').on('change', function() {
        var id = $(this).data('id');
        var selectedType = $(this).val();
        var showLimbs = ['Gußradiator', 'DIN Stahl Radiator', 'DIN Stahlrohr Radiator'].includes(selectedType);

        if (showLimbs) {
            // Show limbs input, hide and clear radiator type select
            $('#limbs-' + id).show();
            $('#radiator_type_' + id).val(''); // Clear the selected value
            $('#radiator_type_' + id).parent().hide();
            $('#ifEmpty-' + id).show();
        } else {
            // Hide limbs input, show radiator type select
            $('#limbs-' + id).hide();
            $('#radiator_type_' + id).parent().show();
            $('#ifEmpty-' + id).hide();
        }
    });

    // Trigger change event on page load to set initial visibility
    $('.type-selector').trigger('change');
});

</script>

<script> 
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