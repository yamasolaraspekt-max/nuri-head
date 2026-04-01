@extends('admin.layouts.app')

@section('title') Branch Profile @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/users.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .new_task {
        position: absolute !important;
        width: 70%;
        top: 12%;
        right: -8px;
        z-index: 12;
    }
    .new_task_card {
        height: 500px !important;
    }
    .new_task_close {
        position: absolute;
        z-index: 4;
        left: -135px;
        top: 16%;
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
                            <h2 class="content-header-title float-left mb-0">Branch Details</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('branch.info') }}">Filialen</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        {{ $data->branch }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <div id="user-profile">
                    <section id="profile-info">
                        <div class="row">
                            {{-- LEFT: Branch info + addresses --}}
                            <div class="col-lg-9 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title mb-0">Branchenprofil</h4>
                                        <button
                                            class="btn btn-primary waves-effect waves-light create_new_task"
                                            type="button"
                                        >
                                            Neue Adresse
                                        </button>
                                    </div>

                                    <div class="card-content">
                                        {{-- Branch base info --}}
                                        <div class="card-body">
                                            <div class="mt-1">
                                                <h6 class="mb-0">Name</h6>
                                                <p class="mb-0">{{ $data->branch }}</p>
                                            </div>

                                            <div class="mt-1">
                                                <h6 class="mb-0">Adresse</h6>
                                                <p class="mb-0">
                                                    {{ $data->street }} {{ $data->postcode }}<br>
                                                    {{ $data->city }} / {{ $data->country }}
                                                </p>
                                            </div>

                                            <div class="mt-1">
                                                <h6 class="mb-0">E-Mail</h6>
                                                <p class="mb-0">{{ $data->email }}</p>
                                            </div>

                                            <div class="mt-1">
                                                <h6 class="mb-0">Telefon</h6>
                                                <p class="mb-0">{{ $data->phone }}</p>
                                            </div>
                                        </div>

                                        {{-- Branch addresses (tabs) --}}
                                        <div class="card-body">
                                            <div class="nav-vertical d-flex">
                                                {{-- Tabs list --}}
                                                <ul class="nav nav-tabs nav-left flex-column mr-2" role="tablist">
                                                    @forelse ($addresses as $index => $address)
                                                        <li class="nav-item">
                                                            <a
                                                                class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                                                id="tab-{{ $address->id }}"
                                                                data-toggle="tab"
                                                                href="#content-{{ $address->id }}"
                                                                role="tab"
                                                                aria-controls="content-{{ $address->id }}"
                                                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                                            >
                                                                {{ $address->city ?? $address->name ?? 'Adresse '.$address->id }}
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li class="nav-item">
                                                            <span class="nav-link disabled text-muted">
                                                                Keine zusätzlichen Adressen
                                                            </span>
                                                        </li>
                                                    @endforelse
                                                </ul>

                                                {{-- Tab contents --}}
                                                <div class="tab-content flex-grow-1">
                                                    @forelse ($addresses as $index => $address)
                                                        <div
                                                            class="tab-pane {{ $index === 0 ? 'active' : '' }}"
                                                            id="content-{{ $address->id }}"
                                                            role="tabpanel"
                                                            aria-labelledby="tab-{{ $address->id }}"
                                                        >
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>Branch Name</th>
                                                                            <th>Full Address</th>
                                                                            <th>Street</th>
                                                                            <th>City</th>
                                                                            <th>Phone</th>
                                                                            <th>Email</th>
                                                                            <th>Status</th>
                                                                            <th>Aktion</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>{{ $address->id }}</td>
                                                                            <td>{{ $address->name }}</td>
                                                                            <td>{{ $address->full_address }}</td>
                                                                            <td>{{ $address->street }}</td>
                                                                            <td>{{ $address->city }}</td>
                                                                            <td>{{ $address->phone }}</td>
                                                                            <td>{{ $address->email }}</td>
                                                                            <td>{{ $address->status }}</td>
                                                                            <td>
                                                                                <a
                                                                                    href="{{ route('branch.address.edit', $address->id) }}"
                                                                                    class="btn btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light"
                                                                                >
                                                                                    <i class="feather icon-edit"></i>
                                                                                </a>
                                                                                <button
                                                                                    type="button"
                                                                                    class="btn btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete-address"
                                                                                    data-id="{{ $address->id }}"
                                                                                >
                                                                                    <i class="feather icon-trash"></i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="tab-pane active">
                                                            <p class="text-muted mb-0">
                                                                Es sind noch keine Filialadressen hinterlegt. Klicken Sie auf
                                                                <strong>Neue Adresse</strong>, um eine anzulegen.
                                                            </p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT: Employees of this branch --}}
                            <div class="col-lg-3 col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">Mitarbeiter der Niederlassung</h4>
                                        <i class="feather icon-more-horizontal cursor-pointer"></i>
                                    </div>
                                    <div class="card-body">
                                        @forelse($branchEmployees as $emp)
                                            <div class="d-flex justify-content-start align-items-center mb-1">
                                                <div class="avatar mr-50">
                                                    @php
                                                        $img = $emp->image
                                                            ? asset('images/employee/'.$emp->image)
                                                            : asset('images/gender/male.png');
                                                    @endphp
                                                    <img
                                                        src="{{ $img }}"
                                                        alt="Mitarbeiter Bild"
                                                        height="35"
                                                        width="35"
                                                    >
                                                </div>
                                                <div class="user-page-info">
                                                    <h6 class="mb-0">
                                                        {{ $emp->name }} {{ $emp->lastname }}
                                                    </h6>
                                                    <span class="font-small-2 text-muted">
                                                        {{ $emp->email ?? 'Keine E-Mail hinterlegt' }}
                                                    </span>
                                                    @if($emp->phone)
                                                        <br>
                                                        <span class="font-small-2 text-muted">
                                                            Tel: {{ $emp->phone }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if(Route::has('employee.profile'))
                                                    <a
                                                        href="{{ route('employee.profile', $emp->id) }}"
                                                        class="btn btn-primary btn-icon ml-auto"
                                                        title="Mitarbeiterprofil öffnen"
                                                    >
                                                        <i class="feather icon-user"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">
                                                Es sind aktuell keine Mitarbeiter dieser Niederlassung zugeordnet.
                                            </p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>

    {{-- Slide-in: Neue Adresse --}}
    <div class="new_task" style="display:none">
        <div class="new_task_close"></div>

        <div class="card new_task_card">
            <div class="card-header" style="border-bottom: 1px solid #8fc73e;">
                <h3 class="title mt-1 ml-2 mb-0">Neue Adresse</h3>
            </div>
            <div class="card-body p-0">
                <form id="store-form">
                    @csrf
                    <div class="modal-body p-0">
                        <div class="card p-1 mb-0">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        {{-- Name --}}
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Zweig name</span>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="hidden" name="branch_id" value="{{ $data->id }}">
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="name"
                                                        value="{{ old('name') }}"
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Address / Autocomplete --}}
                                        <div class="col-12">
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <span>Adresse</span>
                                                </div>
                                                <div class="col-md-8">
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="full_address"
                                                        id="full_address"
                                                        value="{{ old('full_address') }}"
                                                        placeholder="Adresse eingeben"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="street"
                                                        id="street-input"
                                                        value="{{ old('street') }}"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="postcode"
                                                        id="postal_code-input"
                                                        value="{{ old('postcode') }}"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="city"
                                                        id="locality-input"
                                                        value="{{ old('city') }}"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="latitude"
                                                        id="latitude-input"
                                                        value="{{ old('latitude') }}"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="longitude"
                                                        id="longitude-input"
                                                        value="{{ old('longitude') }}"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        class="form-control"
                                                        name="elevation"
                                                        id="elevation-input"
                                                        value="{{ old('elevation') }}"
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
                                                        @if(session('customer_email'))
                                                            value="{{ session('customer_email') }}"
                                                        @else
                                                            value="{{ old('email') }}"
                                                        @endif
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
                                                        <select class="form-control" id="basicSelect" name="employee_id">
                                                            @foreach ($employee as $emp)
                                                                <option value="{{ $emp->id }}">
                                                                    {{ $emp->name }} {{ $emp->lastname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Map preview --}}
                                    <div class="col-3">
                                        <div id="gmp-map" style="height: 200px; width: 100%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-danger mr-1 waves-effect waves-light close_task_window"
                        >
                            <i class="feather icon-x"></i> abbrechen
                        </button>
                        <button type="button" class="btn btn-primary save-task">
                            <i class="feather icon-save"></i> speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('script')
{{-- Slide-in open/close --}}
<script>
    $(document).ready(function () {
        // Open
        $('.create_new_task').on('click', function () {
            $('.new_task').css({
                right: '-100%',
                display: 'block',
            }).animate({
                right: '0',
            }, 500);
        });

        // Close
        $('.new_task').on('click', '.close_task_window', function () {
            $('.new_task').animate({
                right: '-100%',
            }, 500, function () {
                $(this).hide();
            });
        });
    });
</script>

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/pages/user-profile.js') }}"></script>
<!-- END: Page JS-->

{{-- Google Maps JS (no callback param to avoid initMap errors) --}}
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"
    defer
></script>

<script>
    let map;
    let marker;

    function initMap() {
        const mapEl = document.getElementById('gmp-map');
        if (!mapEl) return;

        map = new google.maps.Map(mapEl, {
            center: { lat: 50.8503, lng: 4.3517 }, // Default location (Brussels)
            zoom: 10,
        });

        initAutocomplete();
    }

    function initAutocomplete() {
        const fullAddressInput = document.getElementById("full_address");
        if (!fullAddressInput) return;

        const streetInput     = document.getElementById("street-input");
        const latitudeInput   = document.getElementById("latitude-input");
        const longitudeInput  = document.getElementById("longitude-input");
        const elevationInput  = document.getElementById("elevation-input");
        const postalCodeInput = document.getElementById("postal_code-input");
        const cityInput       = document.getElementById("locality-input");

        const elevationService = new google.maps.ElevationService();

        const autocomplete = new google.maps.places.Autocomplete(fullAddressInput, {
            fields: ["address_components", "geometry"],
            types: ["address"],
        });

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                alert("No details available for the selected address.");
                return;
            }

            const location = place.geometry.location;
            latitudeInput.value  = location.lat();
            longitudeInput.value = location.lng();

            updateMap(location);
            fetchElevation(location, elevationInput);

            const components = parseAddressComponents(place.address_components);

            streetInput.value     = `${components.route} ${components.street_number}`.trim();
            postalCodeInput.value = components.postal_code;
            cityInput.value       = components.locality || components.administrative_area_level_1 || components.administrative_area_level_2;

            fullAddressInput.value = `${components.route} ${components.street_number}, ${cityInput.value}, ${components.postal_code}`;
        });

        function fetchElevation(location, elevationInput) {
            elevationService.getElevationForLocations(
                { locations: [location] },
                (results, status) => {
                    if (status === google.maps.ElevationStatus.OK && results[0]) {
                        elevationInput.value = results[0].elevation.toFixed(2);
                    } else {
                        elevationInput.value = "";
                    }
                }
            );
        }

        function parseAddressComponents(components) {
            const address = {
                street_number: "",
                route: "",
                locality: "",
                postal_code: "",
                administrative_area_level_1: "",
                administrative_area_level_2: "",
            };

            components.forEach((component) => {
                if (component.types.includes("street_number")) {
                    address.street_number = component.long_name;
                }
                if (component.types.includes("route")) {
                    address.route = component.long_name;
                }
                if (component.types.includes("locality")) {
                    address.locality = component.long_name;
                }
                if (component.types.includes("administrative_area_level_1")) {
                    address.administrative_area_level_1 = component.long_name;
                }
                if (component.types.includes("administrative_area_level_2")) {
                    address.administrative_area_level_2 = component.long_name;
                }
                if (component.types.includes("postal_code")) {
                    address.postal_code = component.long_name;
                }
            });

            return address;
        }

        function updateMap(location) {
            if (!marker) {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    animation: google.maps.Animation.DROP,
                });
            } else {
                marker.setPosition(location);
            }

            map.panTo(location);
            map.setZoom(15);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            if (window.google && google.maps) {
                initMap();
            } else {
                console.error("Google Maps API failed to load.");
            }
        }, 500);
    });
</script>

{{-- Save new address via AJAX --}}
<script>
    $(document).ready(function() {
        $('.save-task').click(function(e) {
            e.preventDefault();

            // Reset validation UI
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            let formData = new FormData($('#store-form')[0]);

            $.ajax({
                url: "{{ route('branch.address.store') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('.save-task')
                        .prop('disabled', true)
                        .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message || 'Branch successfully added.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors || {};
                        let errorMessage = '';

                        for (let key in errors) {
                            const input = $(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            errorMessage += `${errors[key][0]}<br>`;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while saving. Please try again.',
                        });
                    }
                },
                complete: function() {
                    $('.save-task')
                        .prop('disabled', false)
                        .html('<i class="feather icon-save"></i> Speichern');
                }
            });
        });
    });
</script>

{{-- Delete address via AJAX --}}
<script>
    $(document).ready(function () {
        $('.delete-address').click(function (e) {
            e.preventDefault();

            let addressId = $(this).data('id');

            Swal.fire({
                title: 'Sind Sie sicher?',
                text: "Diese Aktion kann nicht rückgängig gemacht werden.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ja, löschen!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/branch_address_destroy/${addressId}`,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Löschen...',
                                text: 'Bitte warten.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Gelöscht!',
                                text: response.message || 'Filialadresse erfolgreich gelöscht.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Etwas ist schiefgelaufen. Bitte versuchen Sie es erneut.',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
