@extends('admin.layouts.app')

@section('title') KUNDEN UND OBJEKTDATEN @endsection
@section('style')
<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
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

body {
    margin: 0;
}

input[type="text"].textbox {
    padding-right: 30px;
    /* Adjust padding to accommodate the pink rectangle */
    border-radius: 0;
    /* Ensure border-radius is set to 0 */
}

h4 {
    font-size: 1rem !important;
}

h3 {

    font-size: 1rem !important;
}

.title {
    font-size: 30px !important;
    font-weight: bold !important;
}

/* Customer Product Selection: Start */
.product_card {
    /* border-radius: 71px;
        background: #f1f1f1 !important; */
}

#product_card_details {
    background: #bbb8b8 !important;
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
    /* background: #cfe09b !important; */
}

.heart-icon.selected {
    color: #95c11f !important;
    font-size: 25px !important;
}

.btns-primary {
    background: #cfe09b !important;
}

.menu-button {
    color: #828282 !important;
}

/* Customer Product Selection: End */



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

#submit_form {
    float: right;
    position: fixed;
    top: 819px;
    right: 9px;
    z-index: 100;
}

.form-reset {
    margin: 0;
    padding: 0;
    border: none;
}

.form-reset input,
.form-reset select,
.form-reset textarea,
.form-reset button {
    margin: 0;
    padding: 0;
    border: none;
    outline: none;
    box-shadow: none;
    background: none;
    font: inherit;
    color: inherit;
}

.form-reset input[type="checkbox"],
.form-reset input[type="radio"] {
    display: inline-block;
    width: auto;
    height: auto;
}

.form-reset button {
    background: none;
    cursor: pointer;
}

.buttons {
    width: 40px !important;
    height: 40px !important;
    padding: 0 !important;
}

.icons {
    font-size: 30px !important;
}

/* Textbox validation style: start */
.textbox-container {
    position: relative;
}

.indicator {
    width: 15px;
    height: 100%;
    background-color: #e50056;
    display: inline-block;
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    display: none;
}

.textbox-container.empty .indicator {
    display: inline-block;
}

/* Textbox Validation: end */


.progress {

    height: 23px !important;
    border: 1px solid gray !important;
    border-radius: 6px !important;

}

.progress-bar {
    width: 60%;
    height: 23px;
    border-radius: 0 !important;
    background-color: #e50056 !important;
}


.checklist-container {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.checklist-container h4 {
    color: #333;
    margin-bottom: 20px;
}

.checklist-container .form-check {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.checklist-container .form-check-label {
    font-weight: 500;
    flex-grow: 1;
    margin-right: 15px;
}

.vs-checkbox-con {
    display: flex;
    align-items: center;
}

.vs-checkbox {
    margin-right: 10px;
}

.vs-icon {
    font-size: 1.5em;
}

.form-check {
    display: flex;
    justify-content: flex-start;
}

.custom-control-prev-icon,
.custom-control-next-icon {
    background-color: #e50056 !important;
}
.card-header {
        border: 0;
    background: transparent;
}
</style>
@endsection
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- BEGIN: Content-->
<div class="app-content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ANFRAGE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
         <form class="form form-horizontal custom-file-upload" method="post"
      action="{{ action('App\Http\Controllers\NewLeadsController@store') }}"
      enctype="multipart/form-data">
    @csrf
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-4">
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
                            <div class="card-header" style="align-self: center;">
                                <h2 class="content-header-title float-left mb-0">KUNDE INTERESSE</h2>
                            </div>
                            <br>
                            @foreach ($articles as $item)
                            <article style="display: flex; align-items: center;">
                                <div class="text-center bg-transparent products mt-1 mb-1 col-10 ">
                                    <div class="card-content">
                                        <div class="row product_card">
                                            <div class="col-md-2 col-2" id="product_card_image">
                                                <img src="{{ asset('images/articles/'.$item->image) }}"
                                                    alt="{{ $item->article_group }}" style="width: 79px !important;"
                                                    class="float-left mt-2">
                                            </div>
                                            <div class="col-md-10 col-10" id="product_card_details">
                                                <h2 class="card-title mt-1 mb-0 white title">
                                                    {{ $item->article_group }}</h2>
                                                <p class="card-text white mb-1"> Aktualler Status: <span
                                                        id="interested-{{ $loop->index }}"> </span>
                                                </p>
                                            </div>
                                        </div>
                                        <input type="checkbox" class="d-none" name="product_id[]"
                                            value="{{ $item->id }}">
                                    </div>
                                </div>
                                <div class="settings col-2"
                                    style="display: flex !important; align-items: flex-start; flex-direction: column; row-gap: 3px;">
                                    <button type="button"
                                            class="btn btn-icon btn-icon rounded-circle btn-light waves-effect waves-light buttons heart-button"
                                            style="width: 50px; height: 50px;" id="{{ $loop->index }}Like">
                                        <i class="fa fa-heart icons heart-icon"></i>
                                    </button>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="cards"> 
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12"> 
                                                <div class="col-md-12 col-12 mb-1">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="search-input" placeholder="Name oder Position des Mitarbeiters" aria-describedby="button-addon2">
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary waves-effect waves-light" type="button" id="search-button">Suchen</button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div> 
                                                <div class="col-12">
                                                    <div class="card"> 
                                                        <div class="card-content">
                                                            <div class="table-responsive mt-1">
                                                                <table class="table table-hover-animation mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Mitarbeiter</th>
                                                                            <th>Berufsbezeichnung</th>
                                                                            <th>Abteilung</th>
                                                                            <th>Aktionen</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="employee-table-body">
                                                                        <!-- Content will be loaded via JavaScript -->
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
                    <div class="col-md-3 col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2 class="content-header-title float-left">Verantwortlich</h2>
                                    <button type="button" class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#employee" >Neue hinzufügen</button>
                                       <div class="modal fade text-left" id="employee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary white">
                                                    <h5 class="modal-title" id="myModalLabel160">Verantwortlich</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12"> 
                                                            <div class="col-md-12 col-12 mb-1">
                                                                <fieldset>
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control" id="search-input" placeholder="Name oder Position des Mitarbeiters" aria-describedby="button-addon2">
                                                                        <div class="input-group-append" id="button-addon2">
                                                                            <button class="btn btn-primary waves-effect waves-light" type="button" id="search-button">Suchen</button>
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </div> 
                                                            <div class="col-12">
                                                                <div class="card"> 
                                                                    <div class="card-content">
                                                                        <div class="table-responsive mt-1">
                                                                            <table class="table table-hover-animation mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>#</th>
                                                                                        <th>Mitarbeiter</th>
                                                                                        <th>Berufsbezeichnung</th>
                                                                                        <th>Abteilung</th>
                                                                                        <th>Aktionen</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="employee-table-body">
                                                                                    <!-- Content will be loaded via JavaScript -->
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>      
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Accept</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-content">
                                    <div class="table-responsive mt-1">
                                        <table class="table  mb-0">
                                            <thead>
                                                <tr> 
                                                    <th>Berufsbezeichnung</th>
                                                    <th>Bilder</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr> 
                                                    <td>
                                                        <p>
                                                            [ 
                                                                Marketter, 
                                                                Marketter,
                                                                Marketter,
                                                                Marketter,
                                                            ]
                                                        </p> 
                                                    </td>
                                                    <td class="p-1">
                                                        <ul class="list-unstyled users-list m-0  d-flex align-items-center">
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Vinnie Mostowy - Vertrib" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Elicia Rieske" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Julee Rossignol" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-10.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                            <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Darcey Nooner" class="avatar pull-up">
                                                                <img class="media-object rounded-circle" src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" height="30" width="30">
                                                            </li>
                                                        </ul>
                                                    </td> 
                                                </tr>
                                               
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                      <div class="row" id="image-container">
                        <!-- Dynamically added image cards will go here -->
                    </div>

                    <div class="col-lg-3">
                        <a class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" id="screenshot-btn" data-target="#new_pic" data-toggle="modal">
                            <i class="feather icon-camera"></i> Hochladen
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
                                    <form id="image-upload-form" action="#" method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <label>Die Größe des Bildes beeinflusst die Upload-Geschwindigkeit: </label>
                                            <div class="form-group">
                                                <input type="file" placeholder="" class="form-control" name="image[]" id="image-input" multiple>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary waves-effect waves-light" id="upload-button">Hochladen</button>
                                        </div>
                                        <input type="hidden" id="image-location" name="image-location" value="">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                      <button type="submit" class="btn btn-primary float-right">Nächste</button>
                </section>
          
        </form>

            <!-- // Basic Horizontal form layout section end -->
        </div>
    </div>
</div>
</div>
<!-- END: Content-->

@endsection

@section('script')

<!-- Electronic Car -->
<script>
document.getElementById('electric_car').addEventListener('change', function() {
    var electricCarPlan = document.getElementById('electric_car_plan');
    if (this.value === 'Ja') {
        electricCarPlan.style.display = 'block';
    } else {
        electricCarPlan.style.display = 'none';
    }
});
</script>

<!-- Age of House -->
<script>
document.getElementById('roof_age').addEventListener('input', function() {
    var roofAge = parseInt(this.value, 10);
    if (!isNaN(roofAge)) {
        var currentYear = new Date().getFullYear();
        var houseYear = currentYear - roofAge;
        document.getElementById('house_year').value = houseYear;
    }
});

document.getElementById('house_year').addEventListener('input', function() {
    var houseYear = parseInt(this.value, 10);
    if (!isNaN(houseYear)) {
        var currentYear = new Date().getFullYear();
        var roofAge = currentYear - houseYear;
        document.getElementById('roof_age').value = roofAge;
    }
});
</script>
<!-- Age of Heating System -->
<script>
document.getElementById('heating_system_age').addEventListener('input', function() {
    var roofAge = parseInt(this.value, 10);
    if (!isNaN(roofAge)) {
        var currentYear = new Date().getFullYear();
        var houseYear = currentYear - roofAge;
        document.getElementById('heating_system_year').value = houseYear;
    }
});

document.getElementById('heating_system_year').addEventListener('input', function() {
    var houseYear = parseInt(this.value, 10);
    if (!isNaN(houseYear)) {
        var currentYear = new Date().getFullYear();
        var roofAge = currentYear - houseYear;
        document.getElementById('heating_system_age').value = roofAge;
    }
});
</script>

<!-- Heating Drop Down -->
<script>
document.getElementById('heating_system_type').addEventListener('change', function() {
    var unitSpan = document.getElementById('heat-energy');
    var selectedValue = this.value;
    
    switch (selectedValue) {
        case 'Gas':
            unitSpan.textContent = 'COP';
            break;
        case 'Öl':
            unitSpan.textContent = 'Liter';
            break;
        case 'Wärmepumpe':
            unitSpan.textContent = 'kWh';
            break;
        case 'Nachtspeicher':
            unitSpan.textContent = 'kWh';
            break;
        default:
            unitSpan.textContent = 'kWh';
    }
});
</script>

<!-- {{-- Alternative Address Script --}} -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('alternative_address');
    const street2s = document.getElementById('street2s');
    const ort2s = document.getElementById('ort2s');

    // Function to toggle visibility
    function toggleAddressFields() {
        if (checkbox.checked) {
            street2s.style.display = 'none';
            ort2s.style.display = 'none';
        } else {
            street2s.style.display = 'block';
            ort2s.style.display = 'block';
        }
    }

    // Initial check on page load
    toggleAddressFields();

    // Add event listener to checkbox
    checkbox.addEventListener('change', toggleAddressFields);
});
</script>


<!-- {{-- Star slider Script --}} -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const starRatings = document.querySelectorAll('.star-rating');

    starRatings.forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                rating.dataset.rating = index + 1;
                updateStars(rating);
            });

            star.addEventListener('mouseover', () => {
                highlightStars(rating, index);
            });

            star.addEventListener('mouseout', () => {
                resetStars(rating);
            });
        });
    });

    function updateStars(rating) {
        const stars = rating.querySelectorAll('.star');
        const ratingValue = rating.dataset.rating;
        const category = rating.dataset.category;
        document.querySelector(`input[name=${category}_rating]`).value = ratingValue;

        stars.forEach((star, index) => {
            if (index < ratingValue) {
                star.classList.add('selected');
                star.classList.remove('hovered');
            } else {
                star.classList.remove('selected');
                star.classList.remove('hovered');
            }
        });
    }

    function highlightStars(rating, index) {
        const stars = rating.querySelectorAll('.star');
        stars.forEach((star, i) => {
            if (i <= index) {
                star.classList.add('hovered');
            } else {
                star.classList.remove('hovered');
            }
        });
    }

    function resetStars(rating) {
        const ratingValue = rating.dataset.rating;
        const stars = rating.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < ratingValue) {
                star.classList.add('selected');
                star.classList.remove('hovered');
            } else {
                star.classList.remove('selected');
                star.classList.remove('hovered');
            }
        });
    }

    // Initialize the stars based on the current rating
    starRatings.forEach(rating => updateStars(rating));
});
</script>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places,marker,drawing&callback=initMap&solution_channel=GMP_QB_addressselection_v2_cAB"
    async defer></script>
<script>
    "use strict";

    let map;
    let panorama;

    function initMap() {
        const CONFIGURATION = {
            mapOptions: {
                center: {
                    lat: 37.4221,
                    lng: -122.0841
                },
                fullscreenControl: true,
                mapTypeControl: true,
                streetViewControl: true,
                zoom: 22,
                zoomControl: true,
                maxZoom: 50,
                mapId: "DEMO_MAP_ID"
            }
        };

        map = new google.maps.Map(document.getElementById('gmp-map'), CONFIGURATION.mapOptions);
        panorama = map.getStreetView();

        const marker = new google.maps.Marker({
            map: map
        });
        const autocomplete = new google.maps.places.Autocomplete(document.getElementById('location-input'), {
            fields: ['address_components', 'geometry', 'name'],
            types: ['address']
        });
        const elevationService = new google.maps.ElevationService();

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }
            renderAddress(place, map, marker);
            fillInAddress(place);
            getElevation(place.geometry.location, elevationService);
        });

        initAreaMeasurement();

        document.getElementById('screenshot-btn').addEventListener('click', takeScreenshot);
    }

    function fillInAddress(place) {
        const addressMappings = [{
                key: 'street',
                types: ['street_number', 'route']
            },
            {
                key: 'locality',
                types: ['locality']
            },
            {
                key: 'region',
                types: ['administrative_area_level_1']
            },
            {
                key: 'postal_code',
                types: ['postal_code']
            },
            {
                key: 'country',
                types: ['country']
            }
        ];

        addressMappings.forEach(mapping => {
            const element = document.getElementById(`${mapping.key}-input`);
            const component = place.address_components.find(c => mapping.types.some(type => c.types.includes(
                type)));
            if (element && component) {
                element.value = component.long_name;
            }
        });
    }

    function renderAddress(place, map, marker) {
        map.setCenter(place.geometry.location);
        marker.setPosition(place.geometry.location);
        document.getElementById('latitude-input').value = place.geometry.location.lat();
        document.getElementById('longitude-input').value = place.geometry.location.lng();
    }

    function getElevation(location, elevationService) {
        elevationService.getElevationForLocations({
            'locations': [location]
        }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    const elevation = results[0].elevation;
                    document.getElementById('elevation-input').value = elevation.toFixed(2);
                } else {
                    console.log('No results found');
                }
            } else {
                console.log('Elevation service failed due to: ' + status);
            }
        });
    }

    function createMapLabel(map, text, position) {
        const label = new google.maps.InfoWindow({
            content: `<div style="color: black; font-size: 12px;">${text}</div>`,
            position: position,
            pixelOffset: new google.maps.Size(0, -20)
        });
        label.open(map);
    }

    function updateMeasurements(polygon) {
        const path = polygon.getPath();
        const area = google.maps.geometry.spherical.computeArea(path);
        const bounds = new google.maps.LatLngBounds();
        path.forEach(vertex => bounds.extend(vertex));

        const ne = bounds.getNorthEast();
        const sw = bounds.getSouthWest();
        const nw = new google.maps.LatLng(ne.lat(), sw.lng());

        const height = google.maps.geometry.spherical.computeDistanceBetween(nw, sw);
        const width = google.maps.geometry.spherical.computeDistanceBetween(ne, nw);

        document.getElementById('polygon-height').value = height.toFixed(2);
        document.getElementById('polygon-width').value = width.toFixed(2);
        document.getElementById('polygon-area').value = area.toFixed(2);

        createMapLabel(map, `Height: ${height.toFixed(2)}m`, sw);
        createMapLabel(map, `Width: ${width.toFixed(2)}m`, nw);
    }

    function initAreaMeasurement() {
        const drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [google.maps.drawing.OverlayType.POLYGON]
            },
            polygonOptions: {
                fillColor: 'red',
                fillOpacity: 0.5,
                strokeWeight: 3.5,
                clickable: true,
                editable: true,
                draggable: true,
                zIndex: 1,
                geodesic: true
            }
        });

        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', (event) => {
            const polygon = event.overlay;
            updateMeasurements(polygon);

            polygon.getPath().addListener('set_at', () => updateMeasurements(polygon));
            polygon.getPath().addListener('insert_at', () => updateMeasurements(polygon));
            polygon.getPath().addListener('remove_at', () => updateMeasurements(polygon));
        });
    }

    function saveMeasurements(customerId, label, width, height, area) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const data = {
            customer_id: customerId,
            measure_label: label,
            width: width,
            height: height,
            area: area
        };

        fetch('/customer_measure', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    }

    function takeScreenshot() {
        const mapContainer = document.getElementById('gmp-map');
        html2canvas(mapContainer).then(canvas => {
            const dataURL = canvas.toDataURL('image/png');
            saveScreenshot(dataURL);
        });
    }

    function saveScreenshot(dataURL) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/save_screenshot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    image: dataURL
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Screenshot saved successfully:', data);
            })
            .catch((error) => {
                console.error('Error saving screenshot:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', initMap);
</script>

<!-- MAP SCREEN SHOT -->
<script>
function displayMapScreenshot() {
    html2canvas(document.getElementById('gmp-map')).then(function(canvas) {
        // Convert canvas to base64 image
        const imgData = canvas.toDataURL('image/png');

        // Create an image element
        const img = document.createElement('img');
        img.src = imgData;

        // Append the image to a container
        document.getElementById('map-screenshot-container').appendChild(img);
    });
}

function displayStreetViewScreenshot() {
    html2canvas(document.getElementById('street-view')).then(function(canvas) {
        // Convert canvas to base64 image
        const imgData = canvas.toDataURL('image/png');

        // Create an image element
        const img = document.createElement('img');
        img.src = imgData;

        // Append the image to a container
        document.getElementById('street-view-screenshot-container').appendChild(img);
    });
}
</script>
<!-- SISSION AND SELECT2 -->

<script src="{{ asset('js/select2.min.js') }}"></script> 
<script>
    $(document).ready(function() {
        $('#product').select2(); 

    $(document).ready(function() {
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



<!-- {{--Select Product and buttons --}} -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.products').forEach((card, index) => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        const statusSpan = card.querySelector('#interested-' + index);
        const heartButton = document.getElementById(index + 'Like');
        const heartIcon = heartButton.querySelector('.heart-icon');
        const menuButton = document.getElementById(index + 'MenuButton');
        const details = document.getElementById(index + 'show_details');

        // Set initial visibility based on checkbox state
        if (checkbox.checked) {
            heartIcon.classList.add('selected');
            heartButton.classList.remove('btn-light');
            heartButton.classList.add('btns-primary');
        } else {
            heartIcon.classList.remove('selected');
            heartButton.classList.remove('btns-primary');
            heartButton.classList.add('btn-light');
        }

        heartButton.addEventListener('click', (event) => {
            checkbox.checked = !checkbox.checked;
            card.classList.toggle('selected', checkbox.checked);

            if (checkbox.checked) {
                statusSpan.innerHTML = 'Interessiert';
                heartIcon.classList.add('selected');
                heartButton.classList.remove('btn-light');
                heartButton.classList.add('btns-primary');
            } else {
                statusSpan.innerHTML = '';
                heartIcon.classList.remove('selected');
                heartButton.classList.remove('btns-primary');
                heartButton.classList.add('btn-light');
            }

            event.stopPropagation(); // Prevent other click events from causing page scroll
        });

        // Prevent default focus scroll behavior
        document.addEventListener('focusin', (event) => {
            if (event.target.closest('.products')) {
                event.preventDefault();
            }
        });
    });
});
</script>



<!-- Saving IMAGE IN BROWSWER TEMPERORY -->

    <script>
        document.getElementById('upload-button').addEventListener('click', function() {
            const fileInput = document.getElementById('image-input');
            const files = fileInput.files;
            const imageContainer = document.getElementById('image-container');
            const imageLocationInput = document.getElementById('image-location');

            // Initialize the image array
            let imageArray = [];

            // Parse the existing image array from the hidden input if it exists
            if (imageLocationInput.value) {
                imageArray = JSON.parse(imageLocationInput.value);
            }

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                const uniqueName = `${Date.now()}-${file.name}`; // Generate a unique name using the current timestamp
                
                reader.onload = function(e) {
                    const cardDiv = document.createElement('div');
                    cardDiv.className = 'col-lg-3 col-md-6 col-sm-12';
                    cardDiv.innerHTML = `
                        <div class="card position-relative">
                            <div class="card-content">
                                <img class="card-img img-fluid" src="${e.target.result}" alt="Card image">
                                <div class="card-img-overlay overflow-hidden overlay-danger overlay-lighten-2">
                                    <h4 class="card-title text-white">Card Image Overlay</h4>
                                    <p class="card-text text-white">Sugar plum tiramisu sweet. Cake jelly marshmallow cotton candy chupa chups.</p>
                                    <p class="card-text"><small class="text-white">Last updated 3 mins ago</small></p>
                                </div>
                            </div>
                            <button type="button" class="close delete-image" aria-label="Close" style="position: absolute; top: 10px; right: 10px; color: red; background: white; border: none; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;">
                                &times;
                            </button>
                        </div> 
                        <input type="hidden" name="image-name[]" value="${uniqueName}"> 
                    `;
                    imageContainer.appendChild(cardDiv);

                    // Add image data to the array
                    imageArray.push({
                        name: uniqueName,
                        dataUrl: e.target.result
                    });

                    // Update the hidden input with the array as a JSON string
                    imageLocationInput.value = JSON.stringify(imageArray);

                    // Add event listener to the delete button
                    cardDiv.querySelector('.delete-image').addEventListener('click', function() {
                        imageContainer.removeChild(cardDiv);
                        // Remove the deleted image from the array
                        const index = imageArray.findIndex(image => image.name === uniqueName);
                        if (index !== -1) {
                            imageArray.splice(index, 1);
                            // Update the hidden input with the array as a JSON string
                            imageLocationInput.value = JSON.stringify(imageArray);
                        }
                    });
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        });

</script>

// Search employees in Model function 
<script>
document.getElementById('search-button').addEventListener('click', function() {
    const query = document.getElementById('search-input').value;
    fetch(`/search-employees?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('employee-table-body');
            tbody.innerHTML = '';
            data.forEach(emp => {
                tbody.innerHTML += `
                    <tr>
                        <td><input type="checkbox" class="form-control primary" name="selected_emp" id="selected_emp"></td>
                        <td class="p-1">
                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="${emp.name} ${emp.lastname}" class="avatar pull-up">
                                    <img class="media-object rounded-circle" src="/images/employee/${emp.image}" alt="Avatar" height="30" width="30">
                                </li>
                            </ul>
                            <p>${emp.name} ${emp.lastname}</p>
                        </td>
                        <td>${emp.position}</td>
                        <td>${emp.department_name}</td>
                        <td>
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light"><i class="feather icon-log-in"></i></button>
                        </td>
                    </tr>
                `;
            });
        });
});

// Trigger search on page load to show all records initially
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search-button').click();
});

</script>



@endsection