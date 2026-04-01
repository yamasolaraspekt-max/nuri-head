@extends('admin.layouts.app')

@section('title') ANFRAGE AUFNAHME @endsection
@section('style')
<!-- Include stylesheet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<meta name="csrf-token" content="{{ csrf_token() }}"> 
<style>
#inquiryProductTable th, #inquiryProductTable td {
    vertical-align: middle;
}
</style>

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
    height: 40px !important;
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
.select2{
    width: 100% !important;
}

 
</style>



@endsection
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- BEGIN: Content-->
<div class="app-content content">
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

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif 

            <form class="leadForm form-horizontal custom-file-upload"
                method="POST"
                action="{{ route('inquiry.update') }}"
                enctype="multipart/form-data">
                @csrf
                @method('POST')

                <input type="hidden" name="id" value="{{ $data->id }}">


                <div class="row">
                    {{-- LEFT SIDE --}}
                    <div class="col-md-4">
                        <div class="card mb-1 shadow-sm">
                            <div class="card-header">
                                <h5><i class="feather icon-user"></i> Kontakt Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-1">
                                    <label>Art des Kontakts</label>
                                    <select name="pre_type" class="form-control select2">
                                        @foreach(['Kunde','Lieferant','Hersteller','Kooperationspartner','Architekt','Nachunternehmer','Bank','Versicherung','Bewerber','Sonstige'] as $type)
                                            <option value="{{ $type }}" {{ old('pre_type', $data->pre_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-1">
                                    <label>Betrieb</label>
                                    <select name="branch_id" class="form-control select2">
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $data->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group mb-1">
                                    <label>Quelle</label>
                                    <select name="source" id="source" class="form-control text form-element">
                                        <option selected>Quelle auswählen</option>
                                        <option value="Telefonisch" @if($data->source=="Telefonisch") selected @endif>Telefonisch</option>
                                        <option value="Persönlich" @if($data->source=="Persönlich") selected @endif>Persönlich</option>
                                        <option value="Mail" @if($data->source=="Mail") selected @endif>Mail</option>
                                        <option value="Nachbar" @if($data->source=="Nachbar") selected @endif>Nachbar</option>
                                        <option value="Empfehlung" @if($data->source=="Empfehlung") selected @endif>Empfehlung</option>
                                        <option value="Solarrechner" @if($data->source=="Solarrechner") selected @endif>Solarrechner</option>
                                        <option value="Herstellerlead" @if($data->source=="Herstellerlead") selected @endif>Herstellerlead</option>
                                        <option value="Kunde aus Vergangenheit" @if($data->source=="Kunde aus Vergangenheit") selected @endif>Kunde aus Vergangenheit</option>
                                        <option value="Termin" @if($data->source=="Termin") selected @endif>Terminkalender</option>
                                    </select>
                                </div>

                                <div class="form-group mb-1">
                                    <label>Priorität</label>
                                    <select name="periority" class="form-control select2">
                                        @foreach(['normal' => 'Keiner', 'Dringend' => 'Dringend', 'Sehr Dringend' => 'Sehr Dringend'] as $val => $text)
                                            <option value="{{ $val }}" {{ old('periority', $data->periority) == $val ? 'selected' : '' }}>{{ $text }}</option>
                                        @endforeach
                                    </select>
                                </div>
 
                            </div>
                        </div>

                           {{-- Map --}}
                            <div class="card mb-1 shadow-sm">
                                <div class="card-header ">
                                    <h5><i class="feather icon-map-pin"></i> Karte</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div id="gmp-map" style="width: 100%; height: 450px; border: 1px solid #ddd;"></div>
                                </div>
                            </div>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-md-8">
                        <div class="card mb-1 shadow-sm">
                            <div class="card-header">
                                <h5><i class="feather icon-users"></i> Person & Adresse</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-1">
                                    <label>Firma</label>
                                    <input type="text" class="form-control" name="firma" value="{{ old('firma', $data->firma) }}">
                                </div>

                                <div class="row mb-1">
                                    <div class="col-md-2">
                                        <label>Anrede</label>
                                        <select name="title" class="form-control">
                                            @foreach(['Frau', 'Herr', 'Dr.', 'Prof.'] as $t)
                                                <option value="{{ $t }}" {{ old('title', $data->title) == $t ? 'selected' : '' }}>{{ $t }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Vorname</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $data->name) }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label>Nachname</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname', $data->lastname) }}">
                                    </div>
                                </div>

                               <div class="form-group mb-1">
                                    <label>Adresse</label>
                                    <input type="text" id="full_address" class="form-control" name="full_address" value="{{ old('full_address', $data->full_address) }}">
                                    
                                    <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $data->latitude) }}">
                                    <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $data->longitude) }}">
                                    <input type="hidden" id="street-input" name="street" value="{{ old('street', $data->street) }}">
                                    <input type="hidden" id="postal_code-input" name="postcode" value="{{ old('postcode', $data->postcode) }}">
                                    <input type="hidden" id="locality-input" name="city" value="{{ old('city', $data->city) }}">
                                    <input type="hidden" id="elevation-input" name="elevation" value="{{ old('elevation', $data->elevation) }}">
                                </div>


                                <div class="row mb-1">
                                    <div class="col-md-6">
                                        <label>Festnetz</label>
                                        <input type="text" class="form-control" name="telephone" value="{{ old('telephone', $data->telephone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Mobil</label>
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $data->phone) }}">
                                    </div>
                                </div>

                                <div class="form-group mb-1">
                                    <label>E-Mail</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $data->email) }}">
                                </div>
 
                                <div class="form-group mb-1">
                                    <label>Notiz</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('note', $data->note) }}</textarea>
                                </div>
 
                            </div>
                        </div>

                        {{-- PRODUCT ROWS --}}
                        <div class="card mb-1 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="feather icon-box"></i> Bestehende Zuweisungen</h5>
                                <button type="button" class="btn btn-primary" id="addRow"><i class="feather icon-plus"></i></button>
                            </div>
                         <div class="card-body p-0">
                            <table class="table table-bordered mb-0" id="inquiryProductTable">
                                <thead class="thead-light text-center">
                                <tr>
                                    <th>Produkt</th>
                                    <th>Service</th>
                                    <th>Abteilung</th>
                                    <th>Innendienst</th>
                                    <th>Außendienst</th>
                                    <th>Termin</th>
                                    <th>Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($productList as $index => $item)
                                    <tr data-index="{{ $index }}">
                                    <td>
                                        <select class="form-select product-select" name="product_id[]" data-index="{{ $index }}">
                                        <option value="">Produkt wählen</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-img="{{ $product->image }}"
                                                    {{ (int)$item->product_id === (int)$product->id ? 'selected' : '' }}>
                                            {{ $product->article_group }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select class="form-select service-select" name="service_id[]" data-index="{{ $index }}">
                                        <option value="">Service wählen</option>
                                        @foreach($serviceList as $s)
                                            @if((int)$s->product_id === (int)$item->product_id)
                                            <option value="{{ $s->id }}" {{ (int)$item->service_id === (int)$s->id ? 'selected' : '' }}>
                                                {{ ucfirst($s->phase_section) }}
                                            </option>
                                            @endif
                                        @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select class="form-select department-select" name="department_id[]" data-index="{{ $index }}">
                                        <option value="">Abteilung wählen</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}" {{ (int)$item->department_id === (int)$d->id ? 'selected' : '' }}>
                                            {{ $d->department_name }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select class="form-select employee-select" name="employee_id[]" data-index="{{ $index }}">
                                        <option value="">Mitarbeiter wählen</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                    data-img="{{ $emp->image }}"
                                                    {{ (int)$item->employee_id === (int)$emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }} {{ $emp->lastname }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select class="form-select field-employee-select" name="field_employee[]" data-index="{{ $index }}">
                                        <option value="">Feld-Mitarbeiter wählen</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}"
                                                    data-img="{{ $emp->image }}"
                                                    {{ (int)($item->field_employee ?? 0) === (int)$emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }} {{ $emp->lastname }}
                                            </option>
                                        @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="datetime-local"
                                            class="form-control termin-input"
                                            name="appointment_date[]"
                                            data-index="{{ $index }}"
                                            value="{{ $item->appointment_date ? \Carbon\Carbon::parse($item->appointment_date)->format('Y-m-d\TH:i') : '' }}">
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                        <i class="feather icon-trash"></i>
                                        </button>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="text-center my-3">
                            <button type="submit" class="btn btn-success btn-lg"><i class="feather icon-save"></i> Aktualisieren</button>
                            <a href="{{ url('inquiry_view') }}" class="btn btn-secondary btn-lg"><i class="feather icon-x-circle"></i> Abbrechen</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
</div>
<!-- END: Content-->

@endsection

@section('script')

<script>
$(document).ready(function() {
    $('#next_step').select2({
        placeholder: 'Nächster Schritt auswählen',
        allowClear: true
    });
});
</script>


<script>
$(document).ready(function () {
    $('#periority').select2({
        placeholder: 'Priorität wählen',
        templateResult: formatPriority,
        templateSelection: formatPriority,
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    function formatPriority(state) {
        if (!state.id) return state.text;

        var icon = $(state.element).data('icon');
        return `<span><i class="${icon}" style="margin-right: 8px;"></i>${state.text}</span>`;
    }
});
</script>


<!-- Name and Lastname suggestions  -->
 <script>
    document.getElementById('lastname').addEventListener('input', function () {
        let input = this.value;

        if (input.length >= 2) { // Start searching after at least 2 characters are typed
            fetch(`/api/lead-lastname-suggestions?query=${input}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let options = data.map(name => `<option value="${name}">`).join('');
                    document.getElementById('lastname-options').innerHTML = options;
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    }); 

 </script> 
  <script>
    document.getElementById('name').addEventListener('input', function () {
        let input = this.value;

        if (input.length >= 2) { // Start searching after at least 2 characters are typed
            fetch(`/api/lead-name-suggestions?query=${input}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    let options = data.map(name => `<option value="${name}">`).join('');
                    document.getElementById('name-options').innerHTML = options;
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    }); 

 </script>

 <!-- check for duplciate or existing record -->
 <script>
   $(document).ready(function() {
    $('#lastname, #name, #location-input, #postal_code-input, #locality-input').on('change', function() {
        // Collect form data
        var lastname = $('#lastname').val();
        var name = $('#name').val();
        var street = $('#location-input').val();
        var postcode = $('#postal_code-input').val();
        var city = $('#locality-input').val();

        console.log("Collected Data:", {lastname, name, street}); // Debugging line

        // Check if all fields are filled
        if (lastname && name && street) {
            // Make an AJAX request to check if the customer exists
            $.ajax({
                url: '/check-new-leads',
                type: 'GET',
                data: {
                    lastname: lastname,
                    name: name,
                    street: street, 
                },
                success: function(response) {
                    console.log("Server Response:", response); // Debugging line

                    if (response.exists) {
                        // Show SweetAlert with a link to the customer profile
                        Swal.fire({
                            title: 'Der Kunde existiert bereits',
                            html: `<p>Name: ${response.customer_id}. ${response.customer_name} ${response.customer_lastname}</p>
                                   <p>Adresse: ${response.customer_street}, ${response.customer_postcode}, ${response.customer_city}</p>
                                   <p>Eindeutige Adressnummer: ${response.address_no}</p>
                                   <p>Klicken Sie unten, um das Kundenprofil anzuzeigen.</p>`,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Profil anzeigen',
                            cancelButtonText: 'Absagen'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to the customer profile page
                                window.location.href = `/new_lead_profile/${response.customer_id}/${response.customer_postcode}/${response.address_no}`;
                            }
                        });
                    } else {
                        console.log("No matching customer found."); // Debugging line
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error); // Debugging line
                }
            });
        } else {
            console.log("Not all fields are filled."); // Debugging line
        }
    });
});


 </script>
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
       

<!-- Map and screenshots  -->
 
  <!-- Google Maps API (make sure API key has Places and Maps JS enabled) -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initMap" async defer></script>

<script>
    let map;

   window.initMap = function () {
        const mapDiv = document.getElementById('gmp-map');
        if (!mapDiv) return;

        const latInput = document.getElementById('latitude-input');
        const lngInput = document.getElementById('longitude-input');

        const lat = parseFloat(latInput?.value) || 50.1109;
        const lng = parseFloat(lngInput?.value) || 8.6821;

        const position = { lat, lng };

        map = new google.maps.Map(mapDiv, {
            center: position,
            zoom: 15,
        });

        new google.maps.Marker({
            position: position,
            map: map,
            title: "Gespeicherte Adresse"
        });

        initAutocomplete(); // optional, but keep if you use autocomplete
    };


    function initAutocomplete() {
        const fullAddressInput = document.getElementById("full_address");
        if (!fullAddressInput) return;

        const streetInput = document.getElementById("street-input");
        const latitudeInput = document.getElementById("latitude-input");
        const longitudeInput = document.getElementById("longitude-input");
        const elevationInput = document.getElementById("elevation-input");
        const postalCodeInput = document.getElementById("postal_code-input");
        const cityInput = document.getElementById("locality-input");

        const elevationService = new google.maps.ElevationService();

        const autocomplete = new google.maps.places.Autocomplete(fullAddressInput, {
            fields: ["address_components", "geometry"],
            types: ["address"]
        });

        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                alert("Kein Standort für diese Adresse gefunden.");
                return;
            }

            const location = place.geometry.location;
            map.setCenter(location);
            map.setZoom(15);

            latitudeInput.value = location.lat();
            longitudeInput.value = location.lng();

            elevationService.getElevationForLocations({ locations: [location] }, (results, status) => {
                elevationInput.value = (status === google.maps.ElevationStatus.OK && results[0])
                    ? results[0].elevation.toFixed(2)
                    : "N/A";
            });

            const components = place.address_components || [];
            const get = (type) => components.find(c => c.types.includes(type))?.long_name || "";

            streetInput.value = `${get("route")} ${get("street_number")}`.trim();
            postalCodeInput.value = get("postal_code");
            cityInput.value = get("locality") || get("administrative_area_level_1") || get("administrative_area_level_2");
        });
    }
</script>




<!-- SISSION AND SELECT2 -->

<script src="{{ asset('js/select2.min.js') }}"></script> 
<script>
    
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

<!-- Showing and hiding the dpeatment and employee Dropdown: start -->
  <script>
$(document).ready(function () {
    $('.leadForm').on('submit', function (e) {
        e.preventDefault(); // Prevent normal form submission

        let formData = new FormData(this);
 

        // Client-side validation
        let errors = [];
 
        // if (!$('#name').val()?.trim()) {
        //     errors.push('Der Vorname darf nicht leer sein.');
        // }
       
        // if (!$('#full_address').val()?.trim()) {
        //     errors.push('Bitte geben Sie eine gültige Adresse ein.');
        // }


        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validierungsfehler',
                html: `<ul style="text-align: left;">${errors.map(error => `<li>${error}</li>`).join('')}</ul>`,
            });
            return;
        }

        // AJAX POST
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                Swal.fire({
                    title: 'Speichern...',
                    text: 'Ihre Anfrage wird verarbeitet.',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
          success: function (response) {
                console.log('AJAX Success Response:', response);

                Swal.fire({
                    icon: 'success',
                    title: 'Erfolg',
                    text: 'Die Anfrage wurde erfolgreich gespeichert!',
                }).then(() => {
                    window.location.href = "{{ url('inquiry_view') }}";
                });
            },
 
            error: function (xhr) {
                let serverErrors = xhr.responseJSON?.errors;
                let errorMessages = '';

                if (serverErrors) {
                    $.each(serverErrors, function (key, value) {
                        errorMessages += `<li> ${value}</li>`;
                    });
                } else {
                    errorMessages = 'Es ist ein unerwarteter Fehler aufgetreten.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    html: `<ul style="text-align: left;">${errorMessages}</ul>`,
                });
            }
        });
    });
});
</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Get all the "pre_type" radio buttons
    const preTypeRadios = document.querySelectorAll('input[name="pre_type"]');
    const preTypeNone = document.querySelector('input[name="pre_type"][value="None"]');

    // Get all the "type" radio buttons
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const typeNone = document.querySelector('input[name="type"][value="None"]');

    // Add event listener to all "pre_type" radio buttons
    preTypeRadios.forEach(function (preTypeRadio) {
        preTypeRadio.addEventListener('change', function () {
            console.log(`Clicked pre_type: ${this.value}`); // Debug: log clicked pre_type

            if (this.checked) {
                if (this.value === 'None') {
                    // Set "type" to "None" if "pre_type" is "None"
                    console.log('Setting type to None because pre_type is None'); // Debug message
                    if (typeNone) typeNone.checked = true;
                } else {
                    // Select "type" as "None" if "pre_type" is not "None"
                    console.log('Setting type to None because pre_type is not None'); // Debug message
                    if (typeNone) typeNone.checked = true;
                }
            }
        });
    });

    // Add event listener to all "type" radio buttons
    typeRadios.forEach(function (typeRadio) {
        typeRadio.addEventListener('change', function () {
            console.log(`Clicked type: ${this.value}`); // Debug: log clicked type

            if (this.checked) {
                if (this.value !== 'None') {
                    // Set "pre_type" to "None" if any "type" other than "None" is selected
                    console.log('Setting pre_type to None because a type other than None was selected'); // Debug message
                    if (preTypeNone) preTypeNone.checked = true;
                }
            }
        });
    });
});

</script>



<script>
    $('select[name="next_step"]').select2({
        tags: true,
        placeholder: "Bitte wählen"
    });


    const inquirySteps = {
    Bank: [
        "Finanzierungsoptionen prüfen",
        "Zinssätze vergleichen",
        "Ansprechpartner finden",
        "Unterlagen vorbereiten",
        "Förderprogramme erfragen",
        "Kreditlinie beantragen",
        "Termin mit Bank vereinbaren"
    ],
    Lieferant: [
        "Preisliste anfordern",
        "Lieferbedingungen besprechen",
        "Vertrag prüfen",
        "Lieferzeiten klären",
        "Muster bestellen",
        "Bestellung vorbereiten",
        "Zahlungsziel abstimmen"
    ],
    Hersteller: [
        "Produktdaten anfragen",
        "Technische Beratung vereinbaren",
        "Sonderanfertigung prüfen",
        "Garantiebedingungen besprechen",
        "Zertifikate anfordern",
        "Besuch beim Hersteller planen",
        "Support kontaktieren"
    ],
    Kooperationspartner: [
        "Kooperationsmodell besprechen",
        "Projektvorschlag senden",
        "Konditionen verhandeln",
        "Vertragliches prüfen",
        "Ressourcen abstimmen",
        "Nächstes Meeting planen",
        "Ansprechpartner zuweisen"
    ],
    Architekt: [
        "Projektideen besprechen",
        "Visualisierung anfordern",
        "Materialvorschläge abstimmen",
        "Zeitplan definieren",
        "Technische Anforderungen senden",
        "Vor-Ort-Termin vereinbaren",
        "Planungsunterlagen prüfen"
    ],
    Nachunternehmer: [
        "Leistungsumfang definieren",
        "Kapazitäten abfragen",
        "Preisangebot einholen",
        "Bauzeit abstimmen",
        "Vertrag besprechen",
        "Qualifikationen prüfen",
        "Sicherheiten klären"
    ],
    Lead: [
        "Bedarf analysieren",
        "Angebot erstellen",
        "Kontaktzeitpunkt abstimmen",
        "Entscheider identifizieren",
        "Projektzeitrahmen klären",
        "Anforderungen prüfen",
        "Erstgespräch planen"
    ]
};

$('select[name="pre_type"]').on('change', function () {
    const selectedType = $(this).val();
    const nextStep = $('select[name="next_step"]');
    const options = inquirySteps[selectedType] || [];

    // Clear old dynamic entries (optional: keep static ones)
    nextStep.find('option.dynamic').remove();

    // Add new ones
    options.forEach(option => {
        if (nextStep.find(`option[value="${option}"]`).length === 0) {
            const newOption = new Option(option, option, false, false);
            $(newOption).attr('class', 'dynamic');
            nextStep.append(newOption);
        }
    });

    // Refresh Select2
    nextStep.trigger('change.select2');
});


</script>


<script>
    $(document).ready(function() {
        $('#source').select2({
            tags: true,
            placeholder: "Quelle auswählen",
            allowClear: true
        });
    });
</script>
{{-- FullCalendar (v5) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.3.0/locales-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.3.0/main.global.min.js"></script>
 
<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
const IMG_EMPLOYEE     = "{{ asset('images/employee/') }}";
const CSRF_TOKEN       = '{{ csrf_token() }}';
const ROUTE_EMPLOYEES  = '{{ route("inquiry.department.employees") }}';

const SERVICES         = @json($serviceList);
const PRODUCTS         = @json($products);
const DEPARTMENTS      = @json($departments);

$(function () {

    let rowIndex = $('#inquiryProductTable tbody tr').length;

    // -------------------------------------------------------
    // INIT existing rows
    // -------------------------------------------------------
    $('#inquiryProductTable tbody tr').each(function () {
        const idx = $(this).data('index');
        if (idx) initRow(idx);
    });

    // -------------------------------------------------------
    // ADD ROW
    // -------------------------------------------------------
    $('#addRow').on('click', function () {
        rowIndex++;
        const idx = rowIndex;

        $('#inquiryProductTable tbody').append(`
        <tr data-index="${idx}">
            <td>
                <select class="form-select product-select" name="product_id[]" data-index="${idx}">
                    <option value="">Produkt wählen</option>
                    ${PRODUCTS.map(p => `
                        <option value="${p.id}" data-img="${p.image || ''}">
                            ${p.article_group}
                        </option>`).join('')}
                </select>
            </td>

            <td>
                <select class="form-select service-select" name="service_id[]" data-index="${idx}">
                    <option value="">Service wählen</option>
                </select>
            </td>

            <td>
                <select class="form-select department-select" name="department_id[]" data-index="${idx}">
                    <option value="">Abteilung wählen</option>
                    ${DEPARTMENTS.map(d => `
                        <option value="${d.id}">${d.department_name}</option>`).join('')}
                </select>
            </td>

            <td>
                <select class="form-select employee-select" name="employee_id[]" data-index="${idx}">
                    <option value="">Innendienst wählen</option>
                </select>
            </td>

            <td>
                <select class="form-select field-employee-select" name="field_employee[]" data-index="${idx}">
                    <option value="">Außendienst wählen</option>
                </select>
            </td>

            <td>
                <input type="datetime-local" class="form-control termin-input"
                       name="appointment_date[]" data-index="${idx}">
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                    <i class="feather icon-trash"></i>
                </button>
            </td>
        </tr>
        `);

        initRow(idx);
        refreshCalendarDebounced();
    });

    // -------------------------------------------------------
    // REMOVE ROW
    // -------------------------------------------------------
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        refreshCalendarDebounced();
    });

    // -------------------------------------------------------
    // INIT SELECTS FOR ONE ROW
    // -------------------------------------------------------
    function initRow(i) {
        const $p = $(`.product-select[data-index="${i}"]`);
        const $s = $(`.service-select[data-index="${i}"]`);
        const $d = $(`.department-select[data-index="${i}"]`);
        const $iSel = $(`.employee-select[data-index="${i}"]`);
        const $fSel = $(`.field-employee-select[data-index="${i}"]`);
        const $t = $(`.termin-input[data-index="${i}"]`);

        $p.select2({ width: '100%' }).on('change', () => {
            loadServices(i);
            loadEmployees(i, true);   // auto-suggest dept & service on product choose
            refreshCalendarDebounced();
        });

        $s.select2({ width: '100%' }).on('change', () => {
            loadEmployees(i, false);  // do NOT auto-suggest now
            refreshCalendarDebounced();
        });

        $d.select2({ width: '100%' }).on('change', () => {
            loadEmployees(i, false);
            refreshCalendarDebounced();
        });

        $iSel.select2(employeeFormat()).on('change', refreshCalendarDebounced);
        $fSel.select2(employeeFormat()).on('change', refreshCalendarDebounced);

        $t.on('change', refreshCalendarDebounced);
    }

    // -------------------------------------------------------
    // Format for employee dropdown
    // -------------------------------------------------------
    function employeeFormat() {
        return {
            width: '100%',
            templateResult: e => {
                if (!e.id) return e.text;
                const $el = $(e.element);
                const img = $el.data('img') ? `${IMG_EMPLOYEE}/${$el.data('img')}` : '';
                const pos = $el.data('positions') || '';

                return $(`
                    <div style="display:flex;align-items:center;gap:8px">
                        ${ img ? `<img src="${img}" style="width:28px;height:28px;border-radius:50%">`
                               : `<div style="width:28px;height:28px;border-radius:50%;background:#eee"></div>`
                        }
                        <div>
                            <strong>${e.text}</strong><br>
                            <small>${pos}</small>
                        </div>
                    </div>
                `);
            },
            templateSelection: e => e.text,
            escapeMarkup: m => m
        };
    }

    // -------------------------------------------------------
    // LOAD SERVICES BASED ON PRODUCT
    // -------------------------------------------------------
    function loadServices(i) {
        const pid = $(`.product-select[data-index="${i}"]`).val();
        const $s  = $(`.service-select[data-index="${i}"]`);

        $s.empty().append('<option value="">Service wählen</option>');

        SERVICES.filter(x => String(x.product_id) === String(pid))
            .forEach(x => {
                $s.append(`<option value="${x.id}">${translateService(x.phase_section)}</option>`);
            });

        $s.trigger('change.select2');
    }

    function translateService(s) {
        if (!s) return '';
        const map = {
            complete:'Komplettlösung',
            montage:'Montage',
            product:'Kaufen',
            plan:'Planung',
            maintenance:'Wartung',
            repair:'Reparatur',
            reclaim:'Reklamation'
        };
        return map[s.toLowerCase()] || s;
    }

    // FIX: Initialize select2 for all loaded rows (edit mode)
        $('#inquiryProductTable tbody tr').each(function () {
            const idx = $(this).data('index');
            const $p = $(`.product-select[data-index="${idx}"]`);
            const $s = $(`.service-select[data-index="${idx}"]`);
            const $d = $(`.department-select[data-index="${idx}"]`);
            const $iSel = $(`.employee-select[data-index="${idx}"]`);
            const $fSel = $(`.field-employee-select[data-index="${idx}"]`);

            // Ensure all Select2 initialise correctly
            $p.select2({ width: "100%" });
            $s.select2({ width: "100%" });
            $d.select2({ width: "100%" });
            $iSel.select2(employeeFormat());
            $fSel.select2(employeeFormat());
        });

    // -------------------------------------------------------
    // EMPLOYEE LOADING + auto-suggest logic
    // -------------------------------------------------------
    function loadEmployees(i, autoSuggest) {

        const pid = $(`.product-select[data-index="${i}"]`).val();
        let did   = $(`.department-select[data-index="${i}"]`).val();
        let sid   = $(`.service-select[data-index="${i}"]`).val();

        const $I = $(`.employee-select[data-index="${i}"]`);
        const $F = $(`.field-employee-select[data-index="${i}"]`);

        if (!pid) {
            resetEmployees($I, $F);
            return;
        }

        console.log("REQUEST (edit page) →", {product_id: pid, department_id: did, service_id: sid});

        $.post(ROUTE_EMPLOYEES, {
            _token: CSRF_TOKEN,
            product_id: pid,
            department_id: did || null,
            service_id: sid || null
        })
        .done(res => {
            console.log("RESPONSE (edit page) ←", res);

            const internal = res.internal_employees || [];
            const external = res.external_employees || [];

            // Auto-suggest
            if (autoSuggest) {
                if (!did && res.department_id) {
                    $(`.department-select[data-index="${i}"]`)
                        .val(res.department_id).trigger('change.select2');
                }
                if (!sid && res.service_id) {
                    const $s = $(`.service-select[data-index="${i}"]`);
                    if (!$s.find(`option[value="${res.service_id}"]`).length)
                        loadServices(i);
                    $s.val(res.service_id).trigger('change.select2');
                }
            }

            fillEmployeeSelect($I, internal, 'Innendienst wählen');
            fillEmployeeSelect($F, external, 'Außendienst wählen');

            if (!internal.length && !external.length) {
                Swal.fire({
                    icon:'warning',
                    title:'Keine Mitarbeiter gefunden',
                    text:'Für diese Kombination existieren keine Mitarbeiter.'
                });
            }
        })
        .fail(xhr => {
            console.error("EMPLOYEE ERROR", xhr);
            resetEmployees($I, $F);
        });
    }

    function fillEmployeeSelect($sel, list, label) {
        $sel.empty().append(`<option value="">${label}</option>`);
        list.forEach(e => {
            $sel.append(`
                <option value="${e.id}"
                        data-img="${e.image || ''}"
                        data-positions="${(e.positions || []).join(', ')}">
                    ${e.name} ${e.lastname}
                </option>`);
        });
        $sel.select2(employeeFormat());
    }

    function resetEmployees($I, $F) {
        fillEmployeeSelect($I, [], 'Innendienst wählen');
        fillEmployeeSelect($F, [], 'Außendienst wählen');
    }

    // -------------------------------------------------------
    // MINI CALENDAR (unchanged)
    // -------------------------------------------------------
    let refreshCalendarDebounced = () => {};
    const calEl = document.getElementById('inquiry-mini-calendar');

    if (calEl) {
        const calendar = new FullCalendar.Calendar(calEl, {
            locale:'de',
            initialView:'timeGridWeek',
            firstDay:1,
            height:420,
            nowIndicator:true,
            allDaySlot:false,
            slotMinTime:'07:00',
            slotMaxTime:'21:00',
            headerToolbar:{left:'prev,next today',center:'title',right:''}
        });

        calendar.render();

        const debounce = (fn,ms) => { let t; return (...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),ms);} };

        const refreshCalendar = () => {
            const sel = collectSelection();
            calendar.gotoDate(sel.date);
            calendar.removeAllEvents();

            if (!sel.internal.length && !sel.external.length) return;

            const qs = new URLSearchParams();
            sel.internal.forEach(x => qs.append('internal_ids[]', x));
            sel.external.forEach(x => qs.append('external_ids[]', x));
            qs.append('date', sel.date);

            $.getJSON('{{ route("inquiries.calendar.availability") }}?' + qs.toString())
                .done(resp => {
                    (resp.events || []).forEach(ev => calendar.addEvent(ev));
                    if (resp.weekStart) calendar.gotoDate(resp.weekStart);
                });
        };

        refreshCalendarDebounced = debounce(refreshCalendar, 250);

        function collectSelection() {
            const internal = new Set();
            const external = new Set();
            const dates = [];

            $('#inquiryProductTable tbody tr').each(function () {
                const idx = $(this).data('index');
                const iId = $(`.employee-select[data-index="${idx}"]`).val();
                const fId = $(`.field-employee-select[data-index="${idx}"]`).val();
                const dt  = $(`.termin-input[data-index="${idx}"]`).val();

                if (iId) internal.add(parseInt(iId));
                if (fId) external.add(parseInt(fId));
                if (dt) dates.push(dt.split('T')[0]);
            });
            // Ensure Select2 appears in preloaded rows
                setTimeout(() => {
                    $('#inquiryProductTable tbody tr').each(function () {
                        const idx = $(this).data('index');
                        const $p = $(`.product-select[data-index="${idx}"]`);
                        const $s = $(`.service-select[data-index="${idx}"]`);
                        const $d = $(`.department-select[data-index="${idx}"]`);
                        const $iSel = $(`.employee-select[data-index="${idx}"]`);
                        const $fSel = $(`.field-employee-select[data-index="${idx}"]`);

                        $p.select2({ width:'100%' });
                        $s.select2({ width:'100%' });
                        $d.select2({ width:'100%' });
                        $iSel.select2(employeeFormat());
                        $fSel.select2(employeeFormat());
                    });
                }, 50);


            return {
                internal: [...internal],
                external: [...external],
                date: dates.length ? dates.sort()[0] : new Date().toISOString().slice(0,10)
            };
        }

        $(document).on('change', '.employee-select, .field-employee-select, .termin-input', refreshCalendarDebounced);
        setTimeout(refreshCalendarDebounced, 300);
    }

});
</script>


@endsection