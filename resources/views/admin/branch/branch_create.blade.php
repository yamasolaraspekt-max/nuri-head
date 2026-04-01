@extends('admin.layouts.app')

@section('title') NEUE ZWEIG @endsection

@section('style')
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
</style>
@endsection

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            {{-- Header + Breadcrumb --}}
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">ZWEIG DETAILS</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('branch.info') }}">ZWEIG</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        Neu
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
                        {{-- Left column: Form --}}
                        <div class="col-md-6 col-12">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form
                                            class="form form-horizontal custom-file-upload"
                                            method="POST"
                                            action="{{ route('branch.store') }}"
                                            enctype="multipart/form-data"
                                        >
                                            @csrf
                                            <div class="form-body">
                                                <div class="row">
                                                    {{-- Branch name --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Zweig name</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="branch"
                                                                    value="{{ old('branch') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Initial --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Anfänglich</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="initial"
                                                                    value="{{ old('initial') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Street --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Straße / Nr.</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="street"
                                                                    id="location-input"
                                                                    value="{{ old('street') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Postcode + City --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>PLZ / Ort</span>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="postcode"
                                                                    id="postal_code-input"
                                                                    value="{{ old('postcode') }}"
                                                                >
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="city"
                                                                    id="locality-input"
                                                                    value="{{ old('city') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Country --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Land</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="text"
                                                                    id="country-input"
                                                                    class="form-control"
                                                                    name="country"
                                                                    value="{{ old('country') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Phone --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Tel</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    name="phone"
                                                                    value="{{ old('phone') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Email --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>E-Mail</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input
                                                                    type="email"
                                                                    class="form-control"
                                                                    name="email"
                                                                    value="{{ old('email') }}"
                                                                >
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Kontaktperson --}}
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Kontaktperson</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group mb-0">
                                                                    <select
                                                                        class="form-control"
                                                                        id="basicSelect"
                                                                        name="chairman"
                                                                    >
                                                                        <option value="">Bitte wählen</option>
                                                                        @foreach ($employee as $emp)
                                                                            <option
                                                                                value="{{ $emp->id }}"
                                                                                @selected(old('chairman') == $emp->id)
                                                                            >
                                                                                {{ $emp->name }} {{ $emp->lastname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Submit --}}
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            Nächste
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div><!-- card-body -->
                                </div><!-- card-content -->
                            </div><!-- card -->
                        </div><!-- /col-md-6 -->

                        {{-- Right column: Map --}}
                        <div class="col-md-6 col-12">
                            <div class="card-container" style="width: 100%; height: 95%;">
                                <div class="map" id="gmp-map" style="width: 100%;"></div>
                            </div>

                            <script
                                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyByZgrvtQbWdEfRWf9hXRk4ZWiEP2mLFMk&libraries=places,marker&solution_channel=GMP_QB_addressselection_v2_cAB"
                                defer
                            ></script>
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
    ctaTitle: "Checkout",
    mapOptions: {
        center: {lat: 37.4221, lng: -122.0841},
        fullscreenControl: true,
        mapTypeControl: true,
        streetViewControl: true,
        zoom: 15,
        zoomControl: true,
        maxZoom: 22,
        mapId: ""
    },
    mapsApiKey: "AIzaSyByZgrvtQbWdEfRWf9hXRk4ZWiEP2mLFMk",
    capabilities: {
        addressAutocompleteControl: true,
        mapDisplayControl: true,
        ctaControl: false
    }
};

const SHORT_NAME_ADDRESS_COMPONENT_TYPES = new Set([
    "street_number",
    "administrative_area_level_1",
    "postal_code"
]);

// Only the fields we actually have inputs for
const ADDRESS_COMPONENT_TYPES_IN_FORM = [
    "location",
    "locality",
    "postal_code",
    "country"
];

function getFormInputElement(componentType) {
    return document.getElementById(`${componentType}-input`);
}

function fillInAddress(place) {
    function getComponentName(componentType) {
        for (const component of place.address_components || []) {
            if (component.types[0] === componentType) {
                return SHORT_NAME_ADDRESS_COMPONENT_TYPES.has(componentType)
                    ? component.short_name
                    : component.long_name;
            }
        }
        return "";
    }

    function getComponentText(componentType) {
        return componentType === "location"
            ? `${getComponentName("street_number")} ${getComponentName("route")}`.trim()
            : getComponentName(componentType);
    }

    for (const componentType of ADDRESS_COMPONENT_TYPES_IN_FORM) {
        const input = getFormInputElement(componentType);
        if (input) {
            input.value = getComponentText(componentType);
        }
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
    const { Map } = google.maps;
    const { AdvancedMarkerElement } = google.maps.marker;
    const { Autocomplete } = google.maps.places;

    const mapOptions = CONFIGURATION.mapOptions;
    mapOptions.mapId = mapOptions.mapId || "DEMO_MAP_ID";
    mapOptions.center = mapOptions.center || { lat: 37.4221, lng: -122.0841 };

    const map = new Map(document.getElementById("gmp-map"), mapOptions);
    const marker = new AdvancedMarkerElement({ map });
    const autocomplete = new Autocomplete(getFormInputElement("location"), {
        fields: ["address_components", "geometry", "name"],
        types: ["address"],
    });

    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert(`No details available for input: '${place.name}'`);
            return;
        }
        renderAddress(place, map, marker);
        fillInAddress(place);
    });
}

// 👇 add this AFTER the initMap definition
window.addEventListener('load', function () {
    if (window.google && google.maps) {
        initMap();
    } else {
        console.error('Google Maps JavaScript API not loaded.');
    }
});


    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert(`No details available for input: '${place.name}'`);
            return;
        }
        renderAddress(place, map, marker);
        fillInAddress(place);
    });
}
</script>

<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Enhance Kontaktperson dropdown
        $('#basicSelect').select2({
            placeholder: 'Kontaktperson wählen',
            allowClear: true,
            width: '100%'
        });

        @if(session('update_msg'))
            toastr.success("{{ session('update_msg') }}");
        @endif

        @if(session('save_msg'))
            toastr.success("{{ session('save_msg') }}");
        @endif

        @if(session('delete_msg'))
            toastr.error("{{ session('delete_msg') }}");
        @endif
    });
</script>
@endsection
