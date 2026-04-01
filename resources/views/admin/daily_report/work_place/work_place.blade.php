@extends('admin.layouts.app')
@section('title')
Arbeitsplatz
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">

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
                            <h2 class="content-header-title float-left mb-0">ARBEITSPLATZ</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Platz
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ url('daily_report') }}">Tagesbericht</a>
                                <a class="dropdown-item" href="{{ url('/employee/qr/code/plan') }}">QR COde</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body"> 
                <div class="container"> 
                    <form id="workPlaceForm">
                        @csrf
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">Arbeitsplatztyp auswählen</option>
                                <option value="branch">Zweig</option>
                                <option value="warehouse">Lager</option>
                                <option value="customer">Kunde</option>
                            </select>
                        </div>

                        <div id="branchSelect" class="form-group d-none">
                            <label>Zweigstelle auswählen</label>
                            <select id="branch_id" name="branch_id" class="form-control">
                                <option value="">Auswählen</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ortsname</label>
                            <input type="text" name="place_name" id="place_name" class="form-control">
                        </div>

                        <div id="addressGroup" class="form-group d-none">
                            <label>Adresse</label>
                            <input type="text" id="address" name="address" class="form-control">
                        </div>

                        <div id="latlonGroup" class="row d-none">
                            <div class="form-group col-md-6">
                                <label>Latitude</label>
                                <input type="text" id="lat" name="lat" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Longitude</label>
                                <input type="text" id="lon" name="lon" class="form-control">
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-primary">speichern</button>
                        </div>
                    </form>

                    <hr>

                    <h4>Records</h4>
                    <table class="table table-bordered" id="workPlaceTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Typ</th>
                                <th>Ortsname</th>
                                <th>Adresse</th>
                                <th>Breitengrad</th>
                                <th>Längengrad</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($places as $place)
                            <tr data-id="{{ $place->id }}">
                                <td>{{ $place->id }}</td>
                                <td>{{ $place->type }}</td>
                                <td>{{ $place->place_name }}</td>
                                <td class="show_map" 
                                    data-lat="{{ $place->lat }}" 
                                    data-lon="{{ $place->lon }}">
                                    <i class="feather icon-map-pin"></i> {{ $place->address }}
                                </td>
                                <td>{{ $place->lat }}</td>
                                <td>{{ $place->lon }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm editBtn">Bearbeiten</button>
                                    <button class="btn btn-danger btn-sm deleteBtn">Löschen</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (place.geometry) {
            $('#lat').val(place.geometry.location.lat());
            $('#lon').val(place.geometry.location.lng());
        }
    });

    $('#type').change(function () {
        const type = $(this).val();
        $('#branchSelect, #addressGroup, #latlonGroup').addClass('d-none');

        if (type === 'branch') {
            $('#branchSelect').removeClass('d-none');
        } else if (type === 'warehouse') {
            $('#addressGroup, #latlonGroup').removeClass('d-none');
            $('#branch_id').val('');
        } else {
            $('#branch_id').val('');
        }
    });

    $('#branch_id').change(function () {
        const branchId = $(this).val();
        if (branchId) {
            $.get(`/admin/daily_report/work_place/branch-address/${branchId}`, function (data) {
                $('#addressGroup, #latlonGroup').removeClass('d-none');
                $('#address').val(data.full_address);
                $('#lat').val(data.latitude);
                $('#lon').val(data.longitude);
            });
        }
    });

    $('#workPlaceForm').submit(function (e) {
        e.preventDefault();
        const id = $('#id').val();
        const url = id ? `/admin/daily_report/work_place/${id}` : `/admin/daily_report/work_place`;
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function () {
                location.reload();
            }
        });
    });

    $('.editBtn').click(function () {
        const id = $(this).closest('tr').data('id');
        $.get(`/admin/daily_report/work_place/${id}/edit`, function (data) {
            $('#id').val(data.id);
            $('#type').val(data.type).trigger('change');
            $('#address').val(data.address);
            $('#lat').val(data.lat);
            $('#lon').val(data.lon);
            $('#place_name').val(data.place_name);
            $('#branch_id').val(data.branch_id).trigger('change');
        });
    });

    $('.deleteBtn').click(function () {
        if (!confirm('Delete this record?')) return;
        const id = $(this).closest('tr').data('id');
        $.ajax({
            url: `/admin/daily_report/work_place/${id}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function () {
                location.reload();
            }
        });
    });
});
</script>


<script>
 document.querySelectorAll('.show_map').forEach(el => {
    el.addEventListener('click', function () {
        const destinationLat = this.dataset.lat;
        const destinationLon = this.dataset.lon;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const originLat = position.coords.latitude;
                const originLon = position.coords.longitude;

                const mapUrl = `https://www.google.com/maps/dir/?api=1&origin=${originLat},${originLon}&destination=${destinationLat},${destinationLon}&travelmode=driving`;

                Swal.fire({
                    title: 'Open Google Maps?',
                    text: 'Do you want to open directions in Google Maps?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, open it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(mapUrl, '_blank');
                    }
                });

            }, function (error) {
                Swal.fire('Error', 'Unable to get your current location.', 'error');
            });
        } else {
            Swal.fire('Error', 'Geolocation is not supported by your browser.', 'error');
        }
    });
});

</script>

@endsection