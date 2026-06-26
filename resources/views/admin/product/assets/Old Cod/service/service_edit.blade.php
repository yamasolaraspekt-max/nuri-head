@extends('admin.layouts.app')

@section('title')Maschinenservice @endsection
@section('style')
<!-- Include stylesheet -->

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

    .leasing {
        display: none;
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
            <div class="content-header-left col-md-9 col-12 mb-2 ">
                <div class="row breadcrumbs-top">
                    <div class="col-12 " style="">
                        <h2 class="content-header-title float-left mb-0 ">Maschinenservice</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a
                                        href="{{ url('machine_service_details/'.request()->id) }}">Machine</a>
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
                    <div class="col-md-12 col-12">

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
                                    <form class="form-horizontal" novalidate method="post"
                                        action="{{action('App\Http\Controllers\MachineServiceController@store')}}"
                                        class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf
                                        <fieldset>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Auto-/Maschinenname
                                                        </label>

                                                        <input type="hidden" class="form-control" name="machine_id"
                                                            value="{{ request()->machine_id }}" required>
                                                            <input type="hidden" class="form-control" name="id" value="{{ request()->id }}" required>
                                                        <input type="text" class="form-control" name=""
                                                            value="{{ $data->name }} {{ $data->model }}" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Serviceart
                                                        </label>

                                                        <select class="form-control" name="service_type">
                                                            <option value="{{ $data->service_type }}">{{ $data->service_type }}</option>
                                                            <option value="Reparatur">Reparatur</option>
                                                            <option value="TÜV">TÜV</option>
                                                            <option value="Inspektion">Inspektion</option>
                                                        </select>
                                                        @if ($errors->has('service_type'))<p style="color:red;">
                                                            {!!$errors->first('service_type')!!}</p>@endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Dienstleister
                                                        </label>

                                                        <fieldset class="form-group">
                                                            <select
                                                                class="select2-customize-result form-control required"
                                                                name="service_by" id="service_by" style="width:100%">
                                                                <option value="{{ old('service_by') }}">{{
                                                                    old('service_by') }}</option>

                                                                @foreach ($employees as $emp)
                                                                <option value="{{ $emp->id }}" @if($emp->id==$data->service_by) selected @endif>{{ $emp->name }} {{
                                                                    $emp->lastname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                        @if ($errors->has('service_by'))<p style="color:red;">
                                                            {!!$errors->first('service_by')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Servicedatum
                                                        </label>

                                                        <input type="date" class="form-control" name="service_date"
                                                            value="{{ $data->service_date }}" required>
                                                        @if ($errors->has('service_date'))<p style="color:red;">
                                                            {!!$errors->first('service_date')!!}</p>@endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6 kauf">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Kosten
                                                        </label>

                                                        <input type="number" class="form-control" name="price"
                                                            value="{{ $data->price }}" required>
                                                        @if ($errors->has('price'))<p style="color:red;">
                                                            {!!$errors->first('price')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Service Station
                                                        </label>

                                                        <input type="text" class="form-control" name="service_station"
                                                            value="{{ $data->service_station}}" required>
                                                        @if ($errors->has('service_station'))<p style="color:red;">
                                                            {!!$errors->first('service_station')!!}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Technikername
                                                        </label>

                                                        <input type="text" class="form-control" name="technician"
                                                            value="{{ $data->technician}}" required>
                                                        @if ($errors->has('technician'))<p style="color:red;">
                                                            {!!$errors->first('technician')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Standort
                                                        </label>

                                                        <input type="text" class="form-control" name="location"
                                                            value="{{$data->location}}" required>
                                                        @if ($errors->has('location'))<p style="color:red;">
                                                            {!!$errors->first('location')!!}</p>@endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            E-Mail
                                                        </label>

                                                        <input type="text" class="form-control" name="email"
                                                            value="{{ $data->email}}" required>
                                                        @if ($errors->has('email'))<p style="color:red;">
                                                            {!!$errors->first('email')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Telefon
                                                        </label>

                                                        <input type="text" class="form-control" name="phone"
                                                            value="{{ $data->phone }}" required>
                                                        @if ($errors->has('phone'))<p style="color:red;">
                                                            {!!$errors->first('phone')!!}</p>@endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Fehler entdeckt am
                                                        </label>

                                                        <input type="date" class="form-control" name="fault_detected_at"
                                                            value="{{ $data->fault_detected_at }}" required>
                                                        @if ($errors->has('fault_detected_at'))<p style="color:red;">
                                                            {!!$errors->first('fault_detected_at')!!}
                                                        </p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Fehler entdeckt von
                                                        </label>

                                                        <fieldset class="form-group">
                                                            <select
                                                                class="select2-customize-result form-control required"
                                                                name="fault_detected_by" id="fault_detected_by"
                                                                style="width:100%"> 

                                                                @foreach ($employees as $emp)
                                                                <option value="{{ $emp->id }}" @if($emp->id==$data->fault_detected_by) selected @endif>{{ $emp->name }} {{
                                                                    $emp->lastname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </fieldset>
                                                        @if ($errors->has('fault_detected_by'))<p style="color:red;">
                                                            {!!$errors->first('fault_detected_by')!!}
                                                        </p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Fehlerort
                                                        </label>

                                                        <input type="text" class="form-control"
                                                            name="fault_detected_location"
                                                            value="{{ old('fault_detected_location') }}" required>
                                                        @if ($errors->has('fault_detected_location'))<p
                                                            style="color:red;">
                                                            {!!$errors->first('fault_detected_location')!!}
                                                        </p>@endif
                                                    </div>
                                                </div>


                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Fehlerbeschreibung
                                                        </label>

                                                        <textarea class="form-control" name="fault_description"
                                                            required>{{ old('fault_description') }}</textarea>
                                                        @if ($errors->has('fault_description'))<p style="color:red;">
                                                            {!!$errors->first('fault_description')!!}
                                                        </p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Reparaturbeschreibung
                                                        </label>

                                                        <textarea class="form-control" name="repair_description"
                                                            required>{{ old('repair_description') }}</textarea>
                                                        @if ($errors->has('repair_description'))<p style="color:red;">
                                                            {!!$errors->first('repair_description')!!}
                                                        </p>@endif
                                                    </div>
                                                </div>

                                            </div>
                                        </fieldset>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                </form>
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
<!-- END: Content-->

@endsection

@section('script')

<script>
    "use strict";

const CONFIGURATION = {
  "ctaTitle": "Checkout",
  "mapOptions": {"center":{"lat":37.4221,"lng":-122.0841},"fullscreenControl":true,"mapTypeControl":true,"streetViewControl":true,"zoom":100,"zoomControl":true,"maxZoom":22,"mapId":""},
  "mapsApiKey": "{{ config('services.google.maps_key') }}",
  "capabilities": {"addressAutocompleteControl":true,"mapDisplayControl":true,"ctaControl":false}
};

const SHORT_NAME_ADDRESS_COMPONENT_TYPES =
    new Set(['street_number', 'administrative_area_level_1', 'postal_code']);

const ADDRESS_COMPONENT_TYPES_IN_FORM = [
  'location',
  'locality',
  'administrative_area_level_1',
  'postal_code',
  'country',
];

function getFormInputElement(componentType) {
  return document.getElementById(`${componentType}-input`);
}

function fillInAddress(place) {
  function getComponentName(componentType) {
    for (const component of place.address_components || []) {
      if (component.types[0] === componentType) {
        return SHORT_NAME_ADDRESS_COMPONENT_TYPES.has(componentType) ?
            component.short_name :
            component.long_name;
      }
    }
    return '';
  }

  function getComponentText(componentType) {
    return (componentType === 'location') ?
        `${getComponentName('street_number')} ${getComponentName('route')}` :
        getComponentName(componentType);
  }

  for (const componentType of ADDRESS_COMPONENT_TYPES_IN_FORM) {
    getFormInputElement(componentType).value = getComponentText(componentType);
  }
}

function renderAddress(place, map, marker) {
  if (place.geometry && place.geometry.location) {
    map.setCenter(place.geometry.location);
    marker.position = place.geometry.location;
  } else {
    marker.position = null;
  }
}

async function initMap() {
  const {Map} = google.maps;
  const {AdvancedMarkerElement} = google.maps.marker;
  const {Autocomplete} = google.maps.places;

  const mapOptions = CONFIGURATION.mapOptions;
  mapOptions.mapId = mapOptions.mapId || 'DEMO_MAP_ID';
  mapOptions.center = mapOptions.center || {lat: 37.4221, lng: -122.0841};

  const map = new Map(document.getElementById('gmp-map'), mapOptions);
  const marker = new AdvancedMarkerElement({map});
  const autocomplete = new Autocomplete(getFormInputElement('location'), {
    fields: ['address_components', 'geometry', 'name'],
    types: ['address'],
  });

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      // User entered the name of a Place that was not suggested and
      // pressed the Enter key, or the Place Details request failed.
      window.alert(`No details available for input: '${place.name}'`);
      return;
    }
    renderAddress(place, map, marker);
    fillInAddress(place);
  });
}

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

 

 



<script>
    $(document).ready(function() {
        $('#service_by').select2();
        $('#fault_detected_by').select2();
        $('#used_for').select2();
    });

    
</script>





@endsection