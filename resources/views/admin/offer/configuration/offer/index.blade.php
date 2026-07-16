@extends('admin.layouts.app')
@section('title') Planungstool @endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<style>
.container {
    max-width: 100%;
    /* Ensures it stretches to the full viewport width */
    padding: 0 15px;
    /* Adds padding to the sides to ensure some spacing */
}

.select2-container--default .select2-selection--single {
    height: 51px;
}

.a4-page {
    width: 100%;
    max-width: 287mm;
    ;
    /* A4 width */
    min-height: 297mm;
    /* A4 height */
    padding: 50px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.section {
    margin-bottom: 20px;
}

/* Responsive styling */
@media screen and (max-width: 768px) {
    .a4-page {
        max-width: 100%;
        /* Make it fit the screen width */
    }
}

@media print {
    .a4-page {
        width: 210mm;
        height: 297mm;
        /* No border, padding, or shadow on print */
        padding: 0;
        border: none;
        box-shadow: none;
    }

    /* Hide elements that should not be printed */
    .btn {
        display: none;
    }
}

.items {
    border-right: 1px solid #c6c4c4;
    padding: 10px;
}

.nav-item {
    color: #808080;
    background: transparent;
}

.nav-item .active {
    color: #8fc73e;
    background: transparent;
}

.title {
    font-size: 53px;
    line-height: 1.2;
    text-align: center;
    margin-bottom: 40px;
}

.building.selected {
    background-color: #007bff;
    /* Change to the color you want for the selected state */
    color: white;
    /* Optional: change text color */
}

.building {
    transition: border 0.3s, background-color 0.3s;
    border: 2px solid transparent;
}

/* Show border on hover */
.building:hover {
    border: 2px solid #007bff;
    /* You can change this to any border color */
}

/* When card is selected, change the background color to green */
.building.selected {
    border-color: #8fc73e !important;

}

/* You can also adjust the transition effect for smoothness */
.building-content {
    transition: background-color 0.3s;
}

#step-2 {
    margin-top: 30px;
    /* Increase margin as needed */
    padding-bottom: 20px;
    /* Ensures enough space for the slider */
}


.step-content {
    position: relative;
    /* Ensures steps are properly positioned within the flow */
    overflow: visible;
    /* Allows elements within the steps to be displayed correctly */
}

.slider {
    position: relative;
    z-index: 10;
    /* Adjust accordingly */
    margin-top: 20px;
    /* Ensure there is space above the slider */
}
</style>

<style>
/* Basic Tab Styling */
.tab-container {
    padding: 10px;
    max-width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tab-container ul {
    display: flex;
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.tab-container ul li {
    margin-right: 20px;
}

.tab-container ul li a {
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    color: #1f2937;
    font-weight: bold;
}

.tab-container ul li a.active {
    color: #9acc55;
    border-bottom: 2px solid #9acc55;
}

.tab-content {
    padding: 20px;
    background-color: #f9f9f9;
    margin-top: 10px;
    border-top: 1px solid #ddd;
}

.input-group {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    padding: 10px;
}

.input-group input {
    border: none;
    font-size: 16px;
    outline: none;
    flex-grow: 1;
}

.input-group-addon {
    background-color: transparent;
    ;
    padding: 10px;
    font-size: 16px;
}

.radio-group {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.radio-group label {
    display: inline-block;
    font-weight: bold;
}

.radio-group input[type="radio"] {
    margin-right: 10px;
}

.tab-buttons {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.tab-buttons button {
    padding: 10px 20px;
    background-color: white;
    border: 1px solid #007bff;
    color: #007bff;
    border-radius: 5px;
    margin: 0 10px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.tab-buttons button i {
    margin-right: 5px;
}

.calculation-result {
    display: flex;
    align-items: center;
    margin-top: 20px;
}

.calculation-result .equal-symbol {
    font-size: 24px;
    margin: 0 15px;
}

.calculation-result .result-value {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}
</style>
<!-- For Poeple Handle -->
<!-- Add some custom CSS to style the tooltip -->
<style>
.App__HPAInputRangeTooltip__jsZNp {
    position: absolute;
    top: -54px;
    transform: translateX(-3%);
    background-color: transparent;
    border-radius: 4px;
    padding: 5px 10px;
    font-size: 14px;
    text-align: center;
    pointer-events: none;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    pointer-events: none;
    /* Ensure the tooltip does not interfere with slider actions */
}

input[type="range"] {
    -webkit-appearance: none;
    width: 100%;
    background: transparent;
}

input[type="range"]::-webkit-slider-runnable-track {
    width: 100%;
    height: 8px;
    background: #ddd;
    border-radius: 4px;
}

input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #007bff;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
}

input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #007bff;
    border-radius: 50%;
    cursor: pointer;
}
</style>
<!-- For Warm Water Consuption Handle -->
<style>
.slider-container {
    position: relative;
    width: 100%;
    max-width: auto;
    margin: 0 auto;
    padding: 50px 0;
}

.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 5px;
    background: #ddd;
    outline: none;
    border-radius: 5px;
    position: relative;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #007bff;
    cursor: pointer;
    position: relative;
}

.marks {
    display: flex;
    justify-content: space-between;
    position: relative;
    top: 64px;
    width: 100%;
    z-index: -1;
}

.mark {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #ddd;
    position: relative;
}

.mark.active {
    background-color: #007bff;
}

.labels {
    display: flex;
    justify-content: space-between;
    position: absolute;
    top: 50px;
    width: 100%;
    font-size: 14px;
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
                        <h2 class="content-header-title float-left mb-0">PLANUNGTOOL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a></li>
                                <li class="breadcrumb-item"><a href="#">NUE</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- BEGIN: Step Wizard -->
            <div class="container ">
                <div class="row">
                    <div class="col-md-9">
                        <!-- Step Indicators -->
                        <div class="row">
                            <div class="col-12">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                                        role="progressbar" id="progress-bar" style="width: 20%;">
                                        Step 1 of 6
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step Navigation Buttons -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <ul class="nav nav-pills justify-content-center">
                                    <li class="nav-item items">
                                        <a class="nav-item active" id="step-1-tab" href="#" onclick="showStep(1)"><i
                                                class="feather icon-grid"></i> 01 Projektübersicht</a>
                                    </li>
                                    <li class="nav-item items">
                                        <a class="nav-item" id="step-2-tab" href="#" onclick="showStep(2)">02
                                            Gebäudedaten</a>
                                    </li>
                                    <li class="nav-item items">
                                        <a class="nav-item" id="step-3-tab" href="#" onclick="showStep(3)">03
                                            Technologie</a>
                                    </li>
                                    <li class="nav-item items">
                                        <a class="nav-item" id="step-4-tab" href="#" onclick="showStep(4)">04
                                            Produktauswahl</a>
                                    </li>
                                    <li class="nav-item items">
                                        <a class="nav-item" id="step-5-tab" href="#" onclick="showStep(5)"><i
                                                class="fa fa-calculator"></i> 05 Kalkulation</a>
                                    </li>
                                    <li class="nav-item items">
                                        <a class="nav-item" id="step-6-tab" href="#" onclick="showStep(6)"><i
                                                class="feather icon-bar-chart"></i>06 Wirtschaftlichkeit</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Step Content -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <!-- Step 1: Product Selection -->
                                <div id="step-1" class="step-content">
                                    <div class="a4-page">
                                        <form id="step-1-form">
                                            <div class="col-6">
                                                @foreach ($data as $customer)
                                                <div class="cards">
                                                    <div class="card-body">
                                                        <div class="row match-height">
                                                            <div class="col-xl-12 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Kunde</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{$customer->title}} {{$customer->name}} {{$customer->lastname}}"
                                                                        disabled />
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-12 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">ADRESSE:</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $customer->street }} {{$customer->postcode}}, {{$customer->city}}"
                                                                        disabled />
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-6 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">TEL:</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{$customer->phone}}" disabled />
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-6 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">E-MAIL:</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{$customer->email}}" disabled />
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h4>Wählen Sie das Gewerk</h4>
                                                    <div class="form-group">
                                                        <label for="product">Produkt</label>
                                                        <select class="form-control" id="product" name="product">
                                                            <option></option>
                                                            @foreach ($article as $product)
                                                            <option value="{{$product->id}}"
                                                                data-image="{{ asset('images/articles/'.$product->image) }}">
                                                                {{$product->article_group}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @endforeach
                                                <button type="button" class="btn btn-primary"
                                                    onclick="nextStep(2)">Nächste</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- Step 2: Placeholder -->
                                <div id="step-2" class="step-content d-none">
                                    <div class="a4-page">
                                        <ul class="nav nav-tabs mb-3" id="configuratorTabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="tab-building-tab" data-toggle="tab"
                                                    href="#tab-building" role="tab">Projektart</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-sanirung-tab" data-toggle="tab"
                                                    href="#tab-sanirung" role="tab">Sanierung</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-heat-generators-tab" data-toggle="tab"
                                                    href="#tab-heat-generators" role="tab">Wärmeerzeuger</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-temperature-tab" data-toggle="tab"
                                                    href="#tab-temperature" role="tab">Temperatur</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-heating-requirement-tab" data-toggle="tab"
                                                    href="#tab-heating-requirement" role="tab">Heizbedarf</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-distribution-system-tab" data-toggle="tab"
                                                    href="#tab-distribution-system" role="tab">Verteilsystem</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-warm-water-tab" data-toggle="tab"
                                                    href="#tab-warm-water" role="tab">Warmwasser</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-water-amount-tab" data-toggle="tab"
                                                    href="#tab-water-amount" role="tab">Wassermenge</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="tab-technlogies-tab" data-toggle="tab"
                                                    href="#tab-technlogies" role="tab">Technologie</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="configuratorTabContent">
                                            <div class="tab-pane fade show active" id="tab-building" role="tabpanel">
                                                @include('admin.offer.configuration.partials.building')
                                            </div>
                                            <div class="tab-pane fade" id="tab-sanirung" role="tabpanel">
                                                @include('admin.offer.configuration.partials.sanirung')
                                            </div>
                                            <div class="tab-pane fade" id="tab-heat-generators" role="tabpanel">
                                                @include('admin.offer.configuration.partials.heat_generators')
                                            </div>
                                            <div class="tab-pane fade" id="tab-temperature" role="tabpanel">
                                                @include('admin.offer.configuration.partials.temperature')
                                            </div>
                                            <div class="tab-pane fade" id="tab-heating-requirement" role="tabpanel">
                                                @include('admin.offer.configuration.partials.heating_requirement')
                                            </div>
                                            <div class="tab-pane fade" id="tab-distribution-system" role="tabpanel">
                                                @include('admin.offer.configuration.partials.distribution_system')
                                            </div>
                                            <div class="tab-pane fade" id="tab-warm-water" role="tabpanel">
                                                @include('admin.offer.configuration.partials.warm_water')
                                            </div>
                                            <div class="tab-pane fade" id="tab-water-amount" role="tabpanel">
                                                @include('admin.offer.configuration.partials.water_amount')
                                            </div>
                                            <div class="tab-pane fade" id="tab-technlogies" role="tabpanel">
                                                @include('admin.offer.configuration.partials.technlogies')
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary mt-3"
                                            onclick="nextStep(3)">Nächste</button>
                                    </div>

                                </div>
                                <!-- Step 3: Placeholder -->
                                <div id="step-3" class="step-content d-none">
                                    <h4>Step 3: Placeholder Content</h4>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(4)">Nächste</button>
                                </div>

                                <!-- Step 4: Placeholder -->
                                <div id="step-4" class="step-content d-none">
                                    <h4>Step 4: Placeholder Content</h4>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(5)">Nächste</button>
                                </div>

                                <!-- Step 5: Confirmation -->
                                <div id="step-5" class="step-content d-none">
                                    <h4>Step 5: Confirmation</h4>
                                    <p>You're all set! Confirm your submission.</p>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                                <div id="step-6" class="step-content d-none">
                                    <h4>Step 6: Confirmation</h4>
                                    <p>You're all set! Confirm your submission.</p>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 sidebar">
                        <div class="card" style="height: 597.578px;">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Überblick</h4>
                                    <p class="card-text"> </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-primary float-right">4</span>
                                        Cras justo odio
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-info float-right">2</span>
                                        Dapibus ac facilisis in
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-warning float-right">1</span>
                                        Morbi leo risus
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-success float-right">3</span>
                                        Porta ac consectetur ac
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-danger float-right">8</span>
                                        Vestibulum at eros
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-pill bg-success float-right">4</span>
                                        Lorem ipsum dolor sit amet.
                                    </li>
                                </ul>
                                <div class="card-body">
                                    <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: Step Wizard -->
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<!-- Product Drop down and Image -->
<script>
$(document).ready(function() {
    // Initialize Select2 with custom template for displaying images
    $('#product').select2({
        templateResult: formatProduct, // Custom formatting for dropdown list
        templateSelection: formatProductSelection, // Custom formatting for selected item
        escapeMarkup: function(m) {
            return m;
        } // Let Select2 handle HTML markup
    });

    // Function to format the dropdown items with image on the left
    function formatProduct(product) {
        if (!product.id) {
            return product.text; // Return the default label for the item (without formatting)
        }

        // Get the image URL from the data attribute
        var imageUrl = $(product.element).data('image');

        // Create the HTML for the item with the image on the left
        var $productOption = $(
            '<span><img src="' + imageUrl +
            '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
            '<span>' + product.text + '</span></span>'
        );

        return $productOption;
    }

    // Function to format the selected item
    function formatProductSelection(product) {
        if (!product.id) {
            return product.text;
        }

        // Get the image URL from the data attribute for the selected item
        var imageUrl = $(product.element).data('image');

        // Create the HTML for the selected item with the image
        var $productSelected = $(
            '<span><img src="' + imageUrl +
            '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
            '<span>' + product.text + '</span></span>'
        );

        return $productSelected;
    }
});
</script>

<!-- Product Title for A4 Page -->
<script>
$(document).ready(function() {
    // Event listener for the select field
    $('#product').on('change', function() {
        // Get the selected product's text (article group)
        var selectedProduct = $("#product option:selected").text();

        // Update the #product-title span with the selected product name
        $('.product-title').text(selectedProduct);
    });
});
</script>

<!-- steps of part 2  -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section');
    let currentSectionIndex = 0;

    function showSection(index) {
        sections.forEach((section, i) => {
            section.style.display = (i === index) ? 'block' : 'none';
        });
    }

    document.getElementById('next-building').addEventListener('click', function() {
        currentSectionIndex++;
        showSection(currentSectionIndex);
    });

    document.getElementById('prev-sanierung').addEventListener('click', function() {
        currentSectionIndex--;
        showSection(currentSectionIndex);
    });

    document.getElementById('next-sanierung').addEventListener('click', function() {
        currentSectionIndex++;
        showSection(currentSectionIndex);
    });

    document.getElementById('prev-temperature').addEventListener('click', function() {
        currentSectionIndex--;
        showSection(currentSectionIndex);
    });

    document.getElementById('next-temperature').addEventListener('click', function() {
        currentSectionIndex++;
        showSection(currentSectionIndex);
    });

    document.getElementById('prev-heat-generators').addEventListener('click', function() {
        currentSectionIndex--;
        showSection(currentSectionIndex);
    });

    document.getElementById('next-heat-generators').addEventListener('click', function() {
        currentSectionIndex++;
        showSection(currentSectionIndex);
    });

    document.getElementById('prev-heating-requirement').addEventListener('click', function() {
        currentSectionIndex--;
        showSection(currentSectionIndex);
    });

    // Initial display
    showSection(currentSectionIndex);
});
</script>
<script>
// Function to show the current step and update the progress bar
function showStep(step) {
    // Hide all step contents
    $('.step-content').addClass('d-none');

    // Show the selected step
    $('#step-' + step).removeClass('d-none');

    // Update the progress bar
    const progress = step * 16.6666; // 5 steps, each step is 20% of the progress
    $('#progress-bar').css('width', progress + '%').text('Step ' + step + ' of 6');

    // Update the navigation active state
    $('.nav-item').removeClass('active');
    $('#step-' + step + '-tab').addClass('active');
}

// Function to go to the next step
function nextStep(step) {
    // Add any form validation if needed here
    // e.g., check if the product is selected in step 1
    if (step === 2 && $('#product').val() === null) {
        alert('Please select a product before proceeding.');
        return;
    }

    // Show the next step
    showStep(step);
}

// Show the first step on page load
$(document).ready(function() {
    showStep(1);
});
</script>

<script>
$(document).ready(function() {
    // Event listener for the toggle checkbox
    $('#toggleDiv').on('change', function() {
        // Check if the checkbox is checked or not
        if ($(this).is(':checked')) {
            // Enable all input fields within the div except the checkbox
            $('#montageDiv .montage-input').prop('disabled', false);
            // Remove the disabled background color
            $('#montageDiv').css('background', '');
        } else {
            // Disable all input fields within the div except the checkbox
            $('#montageDiv .montage-input').prop('disabled', true);
            // Apply the disabled background color
            $('#montageDiv').css('background', '#e7e7e775');
        }
    });

    // Initially set the background color as disabled
    $('#montageDiv').css('background', '#e7e7e775');
});
</script>


<!-- step 2 function of the buttons  -->
<script>
// Function to handle card selection within a group
function handleCardSelection(groupClass) {
    const cards = document.querySelectorAll(`${groupClass} .selectable-card`);

    cards.forEach(card => {
        card.addEventListener('click', function() {
            // Unselect all cards in the same group
            cards.forEach(c => {
                c.classList.remove('selected');
                c.querySelector('input[type="radio"]').checked = false;
            });

            // Select the clicked card
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
}

// Initialize selection logic for each group
handleCardSelection('.heat-generator-group');
handleCardSelection('.building-type-group');
handleCardSelection('.technology-generator-group');
handleCardSelection('.distribution-generator-group');
handleCardSelection('.water-generator-group');
</script>


<!-- script of range and showing time in temp section  -->

<script>
// Get references to the radio buttons and the time div
const blockingTimeRadios = document.getElementsByName('blockingTime');
const timeDiv = document.getElementById('time');
const rangeSlider = document.getElementById('rangeSlider');
const rangeValue = document.getElementById('rangeValue');

// Function to toggle the visibility of the time div based on the radio selection
blockingTimeRadios.forEach((radio) => {
    radio.addEventListener('change', function() {
        if (this.value === 'ja') {
            timeDiv.style.display = 'block'; // Show the time div when "Ja" is selected
        } else {
            timeDiv.style.display = 'none'; // Hide the time div when "Nein" is selected
        }
    });
});

// Function to synchronize the range slider value with the input box
rangeSlider.addEventListener('input', function() {
    rangeValue.value = rangeSlider.value; // Update the input box with the slider's value
});

// If the user changes the number input, update the slider as well
rangeValue.addEventListener('input', function() {
    if (rangeValue.value >= 0 && rangeValue.value <= 6) {
        rangeSlider.value = rangeValue.value; // Update the slider with the input box's value
    }
});
</script>


<!-- tab of Heating Requriements:  -->
<script>
function showTab(tabName) {
    // Hide all tab contents
    document.getElementById('tab-content-kw').style.display = 'none';
    document.getElementById('tab-content-watt').style.display = 'none';
    document.getElementById('tab-content-kwh').style.display = 'none';

    // Remove active class from all tabs
    document.getElementById('tab-kw').classList.remove('active');
    document.getElementById('tab-watt').classList.remove('active');
    document.getElementById('tab-kwh').classList.remove('active');

    // Show the selected tab content and add active class
    document.getElementById('tab-content-' + tabName).style.display = 'block';
    document.getElementById('tab-' + tabName).classList.add('active');
}
</script>


<!-- script to calculate the heat are  -->
<script>
// Function to calculate kW
function calculateKW() {
    // Get input values
    let heatedArea = parseFloat(document.getElementById('heated-area').value);
    let wattPerM2 = parseFloat(document.getElementById('watt-per-m2').value);

    // Validate inputs
    if (!isNaN(heatedArea) && !isNaN(wattPerM2)) {
        // Calculation: (heatedArea * wattPerM2) / 1000 = result in kW
        let result = (heatedArea * wattPerM2) / 1000;

        // Display the result with one decimal place
        document.getElementById('result-value').textContent = result.toFixed(1);
    } else {
        // Reset result if inputs are not valid
        document.getElementById('result-value').textContent = '0,0 kW';
    }
}
</script>


<!-- Gebäudehülle Calcualtion calculateTransmissionHeat  -->

<!-- Calculate the Transmission Heat -->
<script>
function calculateTransmissionHeat() {
    // Get input values
    let buildingEnvelopeA = parseFloat(document.getElementById('number_buildingEnvelopeA').value);
    let transmissionHeatLossH = parseFloat(document.getElementById('number_transmissionHeatLossH').value);
    let tempDifference = 30; // Assuming a constant temperature difference of 30K

    // Validate inputs (ensure inputs are numbers)
    if (!isNaN(buildingEnvelopeA) && !isNaN(transmissionHeatLossH)) {
        // Calculate Heizlast Transmission in kW
        let heizlastTransmission = (buildingEnvelopeA * transmissionHeatLossH * tempDifference) / 1000;

        // Format the result to one decimal place (period for decimal point)
        let formattedResult = heizlastTransmission.toFixed(1);

        // Display the result
        document.getElementById('heizlast-transmission').value = formattedResult;

        // Trigger result calculation
        result();
    } else {
        // Reset the result if inputs are invalid
        document.getElementById('heizlast-transmission').value = '0.0';
    }
}
</script>

<!-- Calculate the Lüftung Heat -->
<script>
function calculateLuftungHeat() {
    // Get input values
    let luftvolumenV = parseFloat(document.getElementById('number_heatedAirVolumeV').value);
    let luftwechselrateN = parseFloat(document.getElementById('number_airExchangeRateN').value);
    let temperatureDifference = 30; // Assuming a constant temperature difference
    let heatRecovery = document.getElementById('select_heatRecovery').value;

    // Set recovery factor based on the selection
    let recoveryFactor;
    switch (heatRecovery) {
        case 'low':
            recoveryFactor = 0.75; // 25% heat recovery
            break;
        case 'medium':
            recoveryFactor = 0.5; // 50% heat recovery
            break;
        case 'high':
            recoveryFactor = 0.3; // 70% heat recovery
            break;
        default:
            recoveryFactor = 1.0; // No recovery
    }

    // Validate inputs
    if (!isNaN(luftvolumenV) && luftvolumenV > 0 && !isNaN(luftwechselrateN) && luftwechselrateN > 0 && !isNaN(
            temperatureDifference)) {
        let airDensity = 1.2; // kg/m³ (approx. standard)
        let specificHeatCapacity = 1.005; // kJ/kg·K
        let heizlastLuftung = (luftvolumenV * luftwechselrateN * temperatureDifference * airDensity *
            specificHeatCapacity * recoveryFactor) / 3600;

        // Display the result
        document.getElementById('heizlast-luftung').value = heizlastLuftung.toFixed(1);

        // Trigger result calculation
        result();
    } else {
        // Reset the result if inputs are invalid
        document.getElementById('heizlast-luftung').value = '0.0';
    }
}
</script>

<!-- Sum Transmission and Lüftung Heats -->
<script>
// Automatically calculate the result when inputs change
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners to the input fields
    document.getElementById('heizlast-transmission').addEventListener('input', result);
    document.getElementById('heizlast-luftung').addEventListener('input', result);
});

function result() {
    // Get input values from the input fields
    let transmission = parseFloat(document.getElementById('heizlast-transmission').value);
    let luftung = parseFloat(document.getElementById('heizlast-luftung').value);

    // Validate inputs
    if (!isNaN(transmission) && transmission >= 0 && !isNaN(luftung) && luftung >= 0) {
        // Calculate the total heating load
        let heizlast = transmission + luftung;

        // Display the result, formatted to one decimal place
        document.getElementById('heizlast_result').value = heizlast.toFixed(1);
    } else {
        // If inputs are invalid, reset the result to 0.0 kW
        document.getElementById('heizlast_result').value = '0.0';
    }
}
</script>

<script>
function calculateTotalKwh() {
    // Oil Consumption (1 L of oil = 9.78 kWh)
    let oilConsumption = parseFloat(document.getElementById('oil').value) || 0;
    let oilEfficiency = parseFloat(document.getElementById('oil_efficiency').value);
    let oilKwh = oilConsumption * 10.4 * oilEfficiency;


    // Gas Consumption (1 m³ of gas = 10.2 kWh)
    let gasConsumption = parseFloat(document.getElementById('gas').value) || 0;
    let gasEfficiency = parseFloat(document.getElementById('gas_efficiency').value);
    let gasKwh = gasConsumption * 10.2 * gasEfficiency;
    let warmwasser = document.querySelector('input[name="warmwasser"]:checked').value;
    if (warmwasser === 'exclude') {
        gasKwh *= 0.85; // Apply a reduction if warm water is excluded
    }

    // Liquid Gas Consumption (1 m³ of liquid gas = 28.1 kWh)
    let liquidGasConsumption = parseFloat(document.getElementById('liquid_gas').value) || 0;
    let liquidGasEfficiency = parseFloat(document.getElementById('liquid_gas_efficiency').value);
    let liquidGasKwh = liquidGasConsumption * 28.1 * liquidGasEfficiency;

    // Wood Consumption (adjusted by 0.87)
    let woodConsumption = parseFloat(document.getElementById('wood').value) || 0;
    let woodCalorificValue = parseFloat(document.getElementById('wood_calorific_value').value) || 0;
    let woodKwh = woodConsumption * woodCalorificValue * 0.87;

    // Direct Electric Heating
    let directElectricHeating = parseFloat(document.getElementById('direct_electric_heating').value) || 0;

    // Calculate total estimated kWh
    let totalKwh = oilKwh + gasKwh + liquidGasKwh + woodKwh + directElectricHeating;

    // Update the estimated kWh field
    document.getElementById('estimated').value = totalKwh.toFixed(0);
}
</script>


<script>
function calculateInsulationEnergy() {
    // Get the size of the house
    let size = parseFloat(document.getElementById('size').value) || 0;

    // Get the selected insulation standard value (kWh/m²)
    let insulationFactor = parseFloat(document.getElementById('insulation_standard').value);

    // Calculate the estimated kWh based on the size and insulation standard
    let estimatedKwh = size * insulationFactor;

    // Update the insulation_estimated field
    document.getElementById('insulation_estimated').value = estimatedKwh.toFixed(0);
}
</script>

<!-- Section of Number of Poeple  -->
<script>
function updatePeopleCount() {
    // Get the value from the range slider and checkbox
    var peopleCount = document.getElementById("peopleRange").value;
    var onlyHeating = document.querySelector('input[name="only_heating"]').checked;
    var peopleTooltip = document.getElementById("peopleTooltip");
    var slider = document.getElementById("peopleRange");
    var numberOfPeopleInput = document.getElementById("number_of_people");

    // Update the number_of_people input field
    numberOfPeopleInput.value = peopleCount;

    // If "only heating" is checked, set peopleCount to 1, hide input, and disable the slider
    if (onlyHeating) {
        peopleCount = 1;
        slider.disabled = true;
        numberOfPeopleInput.style.display = "none"; // Hide the input field
        numberOfPeopleInput.value = peopleCount;
    } else {
        // Enable slider and show the input field if "only heating" is not checked
        slider.disabled = false;
        numberOfPeopleInput.style.display = "block"; // Show the input field
    }

    // Update the number shown in the tooltip
    document.getElementById("peopleCount").innerHTML = peopleCount;

    // Move the tooltip based on the slider thumb's position
    var percentage = ((peopleCount - slider.min) / (slider.max - slider.min)) * 100;
    peopleTooltip.style.left = `calc(${percentage}% - 20px)`; // Adjust tooltip position

    // Hide all person images
    var personImages = document.getElementsByClassName("person-image");
    for (var i = 0; i < personImages.length; i++) {
        personImages[i].style.display = "none";
    }

    // Show the corresponding image based on the number of people
    if (peopleCount >= 5) {
        document.getElementById("five").style.display = "block"; // Show five.svg for 5 or more people
    } else if (peopleCount == 4) {
        document.getElementById("four").style.display = "block";
    } else if (peopleCount == 3) {
        document.getElementById("three").style.display = "block";
    } else if (peopleCount == 2 || peopleCount == 1 || peopleCount == 0) {
        document.getElementById("two").style.display = "block"; // Show two.svg for 0, 1 or 2 people
    }
}

// Add an event listener to the checkbox to handle its changes
document.querySelector('input[name="only_heating"]').addEventListener('change', function() {
    updatePeopleCount();
});

// Initialize the slider and tooltip position on page load
window.onload = function() {
    updatePeopleCount();
};
</script>


<!-- Section of Warm Water Consumption -->
<script>
function updateWaterLevel(value) {
    console.log("Slider value: " + value); // Log the slider value for debugging

    // Hide all images first
    document.getElementById('low').style.display = 'none';
    document.getElementById('normal').style.display = 'none';
    document.getElementById('high').style.display = 'none';
    document.getElementById('luxury').style.display = 'none';

    // Show the right image and update the value based on range input
    let consumptionPerPerson;
    switch (value) {
        case '1':
            console.log("Displaying Low water image");
            document.getElementById('low').style.display = 'block';
            consumptionPerPerson = 25;
            break;
        case '2':
            console.log("Displaying Normal water image");
            document.getElementById('normal').style.display = 'block';
            consumptionPerPerson = 50;
            break;
        case '3':
            console.log("Displaying High water image");
            document.getElementById('high').style.display = 'block';
            consumptionPerPerson = 80;
            break;
        case '4':
            console.log("Displaying Luxury water image");
            document.getElementById('luxury').style.display = 'block';
            consumptionPerPerson = 120;
            break;
        default:
            console.warn("Unexpected value: " + value);
            consumptionPerPerson = 0;
            break;
    }

    // Update the value in the number_hotWaterConsumptionPerPerson input field
    document.getElementById('number_hotWaterConsumptionPerPerson').value = consumptionPerPerson;

    // Trigger the energy calculation after updating the value
    calculateEnergy(); // Ensure the calculation happens when the value is updated
}

function calculateEnergy() {
    // Get the input values
    const number_of_people = parseFloat(document.getElementById('number_of_people').value) || 0;
    const litersPerPerson = parseFloat(document.getElementById('number_hotWaterConsumptionPerPerson').value) || 0;
    const temperature = parseFloat(document.getElementById('number_hotWaterStorageTemperature').value) || 0;

    // Calculate the total water consumption
    const total_liters = number_of_people * litersPerPerson;
    console.log('Total Liters: ' + total_liters);

    if (total_liters === 0 || isNaN(total_liters)) {
        console.error("Total liters is invalid");
        return;
    }

    // Specific heat capacity of water (kJ/kg°C)
    const c = 4.186;

    // Baseline temperature (Cold water temp, assumed 10°C)
    const baselineTemp = 10;

    // Temperature difference
    const deltaT = temperature - baselineTemp;

    // Energy in kJ (m * c * deltaT) and converting from kJ to kWh
    const energyInKWh = (total_liters * c * deltaT) / 3600;

    // Custom factor based on the water consumption
    let customFactor;

    if (total_liters === 25) {
        customFactor = 373;
    } else if (total_liters === 50) {
        customFactor = 372.5;
    } else if (total_liters === 80) {
        customFactor = 370;
    } else if (total_liters === 120) {
        customFactor = 369.5;
    } else {
        customFactor = 373; // Default factor
    }

    // Apply the custom factor
    const adjustedEnergyInKWh = energyInKWh * customFactor;

    // Update the result in the input field
    document.getElementById('result_storage').value = adjustedEnergyInKWh.toFixed(0);
}

// Call calculateEnergy initially to set the initial value
window.onload = calculateEnergy;
</script>






@endsection