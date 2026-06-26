@extends('admin.layouts.app')
@section('title') Branch Profile @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/users.css') }}">
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
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Branch Details</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Unternehmensgruppe</a>
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
                            <div class="col-lg-9 col-12">
                               
                               <div class="card"  > 
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="mt-1">
                                                <h6 class="mb-0">Name</h6>
                                                <p>{{ $data->branch }}</p>
                                            </div>
                                            <div class="mt-1">
                                                <h6 class="mb-0">Adress</h6>
                                                <p>{{ $data->street }} {{ $data->postcode }}<br> {{ $data->city }}/{{ $data->country }}</p>
                                            </div>
                                            <div class="mt-1">
                                                <h6 class="mb-0">Email:</h6>
                                                <p>{{ $data->email }}</p>
                                            </div>
                                            <div class="mt-1">
                                                <h6 class="mb-0">Tel:</h6>
                                                <p>{{ $data->phone }}</p>
                                            </div>
                                        </div>
                                        <div class="card new_task_card">
                                            <div class="card-header" style="    border-bottom: 1px solid #8fc73e;">
                                                <h3 class="title mt-1 ml-2">Branchenprofil - Bearbeiten</h3> 
                                            </div>
                                            <div class="card-body p-0">
                                                <form  id="store-form">
                                                    @csrf
                                                    <div class="modal-body p-0">
                                                        <div class="card p-1">
                                                            <div class="form-body">
                                                            <div class="row">
                                                                    <div class="col-md-9">
                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Zweig name</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="hidden" name="branch_id" value="{{$data->branch_id}}">
                                                                                    <input type="hidden" name="id" value="{{$data->id}}">
                                                                                    <input type="text"   class="form-control" value="{{ old('name', $data->name) }}" name="name">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Address</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="text" class="form-control" value="{{ old('full_address', $data->full_address) }}" name="full_address" id="full_address" placeholder="Enter your address">
                                                                                    <input type="hidden" class="form-control" value="{{ old('street', $data->street) }}" name="street" id="street-input">
                                                                                    <input type="hidden" class="form-control" value="{{ old('postcode', $data->postcode) }}" name="postcode" id="postal_code-input">
                                                                                    <input type="hidden" class="form-control" value="{{ old('city', $data->city) }}" name="city" id="locality-input">
                                                                                    <input type="hidden" class="form-control" value="{{ old('latitude', $data->latitude) }}" name="latitude" id="latitude-input">
                                                                                    <input type="hidden" class="form-control" value="{{ old('longitude', $data->longitude) }}" name="longitude" id="longitude-input"> 
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Tel</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="text" id="contact-info" class="form-control" value="{{ old('phone',$data->phone) }}" name="phone">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>E-Mail</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <input type="email" id="contact-info" class="form-control"  value="{{ old('email', $data->email) }}" name="email">
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12">
                                                                            <div class="form-group row">
                                                                                <div class="col-md-4">
                                                                                    <span>Kontaktperson</span>
                                                                                </div>
                                                                                <div class="col-md-8">
                                                                                    <fieldset class="form-group">
                                                                                        <select class="form-control" id="basicSelect" name="employee_id">
                                                                                            @foreach ($employee as $emp)
                                                                                                <option value="{{ $emp->id }}" @if($data->employee_id == $emp->id) selected @endif>{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </fieldset>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-3">
                                                                        <div id="gmp-map" style="height: 200px; width: 100%;"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <div class="modal-footer"> 
                                                        <button type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" data-dismiss="modal"><i class="feather icon-x"></i> abbrechen</button>
                                                        <button type="button" class="btn btn-primary save-task"><i class="feather icon-save"></i> speichern</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                        </div>
                       
                    </section>
                </div>

            </div>
        </div>
    </div>


 
    <!-- END: Content-->
@endsection


@section('script')


<!-- Menu Close and Open Button: start  -->
  <script>
    $(document).ready(function () {
        // Show the .new_task when the "Erstellen" button is clicked
        $('.create_new_task').on('click', function () {
            $('.new_task').css({
                right: '-100%', // Start offscreen (adjust based on your layout)
                display: 'block', // Ensure it's visible
            }).animate({
                right: '0', // Slide into view
            }, 500); // Animation duration in ms
        });

        // Hide the .new_task when the "abbrechen" button is clicked
        $('.new_task').on('click', '.close_task_window', function () {
            $('.new_task').animate({
                right: '-100%', // Slide out of view
            }, 500, function () {
                $(this).hide(); // Hide after animation completes
            });
        });
    });
</script>
<!-- Menu Close and Open Button: end  -->
 
  <!-- BEGIN: Page JS-->
  <script src="{{ asset('app-assets/js/scripts/pages/user-profile.js') }}"></script>
  <!-- END: Page JS-->
 
 <script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places&callback=initMap"
    async defer>
</script> 

<script>
    let map;
    let marker;

    // Load default latitude and longitude from backend
    const defaultLatitude = {{ $data->latitude ?? 50.8503 }};  // Default: Brussels if no data
    const defaultLongitude = {{ $data->longitude ?? 4.3517 }};  // Default: Brussels if no data

    function initMap() {
        map = new google.maps.Map(document.getElementById('gmp-map'), {
            center: { lat: defaultLatitude, lng: defaultLongitude },
            zoom: 10,
        });

        // Place marker at the default location
        marker = new google.maps.Marker({
            position: { lat: defaultLatitude, lng: defaultLongitude },
            map: map,
        });

        initAutocomplete();
    }

    function initAutocomplete() {
        const fullAddressInput = document.getElementById("full_address");
        const streetInput = document.getElementById("street-input");
        const latitudeInput = document.getElementById("latitude-input");
        const longitudeInput = document.getElementById("longitude-input");
        const elevationInput = document.getElementById("elevation-input");
        const postalCodeInput = document.getElementById("postal_code-input");
        const cityInput = document.getElementById("locality-input");
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
            latitudeInput.value = location.lat();
            longitudeInput.value = location.lng();

            updateMap(location);
            fetchElevation(location, elevationInput);
            const addressComponents = parseAddressComponents(place.address_components);

            streetInput.value = `${addressComponents.route} ${addressComponents.street_number}`;
            postalCodeInput.value = addressComponents.postal_code;
            cityInput.value = addressComponents.locality || addressComponents.administrative_area_level_1 || addressComponents.administrative_area_level_2;

            fullAddressInput.value = `${addressComponents.route} ${addressComponents.street_number}, ${cityInput.value}, ${addressComponents.postal_code}`;
        });

        function fetchElevation(location, elevationInput) {
            elevationService.getElevationForLocations(
                { locations: [location] },
                (results, status) => {
                    if (status === google.maps.ElevationStatus.OK && results[0]) {
                        elevationInput.value = results[0].elevation.toFixed(2);
                    } else {
                        elevationInput.value = "Elevation not available";
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
            if (typeof google !== 'undefined') {
                initMap();
            } else {
                alert("Google Maps API failed to load.");
            }
        }, 500);
    });
</script>
 
<script>
    $(document).ready(function() {
        $('.save-task').click(function(e) {
            e.preventDefault();  // Prevent default form submission

            // Clear previous error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Collect form data
            let formData = new FormData($('#store-form')[0]);

            // AJAX POST Request
            $.ajax({
                url: "{{ route('branch.address.update') }}",  // Update with the actual store route
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('.save-task').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: response.message || 'Branch successfully updated.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to branch_profile/{branch_id}
                        window.location.href = `/branch_profile/${response.branch_id}`;
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = '';

                        for (let key in errors) {
                            let input = $(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            errorMessage += `${errors[key][0]}<br>`;
                        }

                        // Show validation errors in SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage,
                        });
                    } else {
                        // Generic error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while saving. Please try again.',
                        });
                    }
                },
                complete: function() {
                    $('.save-task').prop('disabled', false).html('<i class="feather icon-save"></i> Speichern');
                }
            });
        });
    });
</script>


@endsection