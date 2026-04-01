@extends('admin.layouts.app')

@section('title') ANFRAGE AUFNAHME @endsection
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
         <form class="form form-horizontal custom-file-upload" method="post" action="{{ action('App\Http\Controllers\InquiryController@update') }}"  enctype="multipart/form-data">
             @csrf
            <!-- Basic Horizontal form layout section start -->
            <section id="basic-horizontal-layouts">
                <div class="row match-height">
                    <div class="col-md-2 col-12">
                        <h4 class="primary">ART DES KONTAKTS</h4>
                        <input type="hidden" name="id" value="{{ $data->id}}">
                            <div class="form-group">
                                <label for="pre_type">Art des Kontakts:</label>
                                <select name="pre_type" id="pre_type" class="form-control">
                                    <option value="">Auswählen</option> 
                                    <option value="Lead" @if($data->pre_type == 'Lead') selected @endif>Lead</option>
                                    <option value="Lieferant" @if($data->pre_type == 'Lieferant') selected @endif>Lieferant</option>
                                    <option value="Hersteller" @if($data->pre_type == 'Hersteller') selected @endif>Hersteller</option>
                                    <option value="Kooperationspartner" @if($data->pre_type == 'Kooperationspartner') selected @endif>Kooperationspartner</option>
                                    <option value="Architekt" @if($data->pre_type == 'Architekt') selected @endif>Architekt</option>
                                    <option value="Nachunternehmer" @if($data->pre_type == 'Nachunternehmer') selected @endif>Nachunternehmer</option>
                                    <option value="Bank" @if($data->pre_type == 'Bank') selected @endif>Bank</option>
                                    <option value="Versicherung" @if($data->pre_type == 'Versicherung') selected @endif>Versicherung</option>
                                    <option value="Bewerber" @if($data->pre_type == 'Bewerber') selected @endif>Bewerber</option>
                                </select>
                            </div>
                                <h4 class="primary">Abteilung</h4>
                                    <div class="form-group">
                                        <select class="form-control select2" name="department_id" id="department">
                                            <option value="">Abteilung wählen</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept->id }}" 
                                                    {{ (isset($department_id) && $department_id == $dept->id) ? 'selected' : '' }}>
                                                    {{ $dept->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <h4 class="primary">Mitarbeiter</h4>
                                    <div class="form-group">
                                        <select class="form-control select2" name="direct_to" id="employee">
                                            <option value="">Mitarbeiter wählen</option>
                                            {{-- Options will be loaded via AJAX --}}
                                        </select>
                                    </div>


                            <h4 class="primary">Betrieb</h4>

                            <div class="form-group">
                                <select name="branch_id" id="" class="form-control">
                                    @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @if($data->branch_id == $branch->id) selected @endif>{{ $branch->branch }}</option>
                                    @endforeach
                                </select>
                            </div>

                                <h4 class="primary">Priorität</h4>
                                <div class="form-group">
                                    <select name="periority" id="periority" class="form-control select2">
                                        <option value="normal" data-icon="fa fa-battery-empty" {{ $data->periority == 'normal' ? 'selected' : '' }}>Keiner</option>
                                        <option value="medium" data-icon="fa fa-battery-half" {{ $data->periority == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" data-icon="fa fa-battery-full" {{ $data->periority == 'high' ? 'selected' : '' }}>Hoch</option>
                                        <option value="very high" data-icon="fa fa-fire text-danger" {{ $data->periority == 'very high' ? 'selected' : '' }}>Sehr Wichtig</option>
                                    </select>
                                </div>

                            

                            
                        </div>
                        <div class="col-md-5 col-12">
                            <div class="cards"> 
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="form-body">
                                            <div class="row"> 
                                                <!-- Additional form fields go here --> 
                                                
                                                <div class="col-12" id="firma-container">
                                                    <div class="form-group row form-element">
                                                        <div class="col-md-2">
                                                            <span>Firma</span>
                                                        </div>
                                                        <div class="col-md-10">
                                                            <input type="text" id="firma" class="form-control form-element" value="{{old('firma', $data->firma)}}" name="firma">
                                                        </div>
                                                    </div>
                                                </div> 

                                            <div class="col-12 ">
                                                <div class="form-group row form-element">
                                                    <div class="col-md-2">
                                                        <span>Name</span>
                                                    </div>
                                                    <div class="col-md-2 pr-0"> 
                                                        <select class="form-control text"  name="title">
                                                            <option selected></option>
                                                            <option value="Frau" @if($data->title == "Frau") checked @endif>Frau</option>
                                                            <option value="Herr"  @if($data->title == "Herr") checked @endif>Herr</option>
                                                            <option value="Dr."  @if($data->title == "Dr.") checked @endif>Dr.</option>
                                                            <option value="Prof."  @if($data->title == "Prof.") checked @endif>Prof.</option>
                                                        </select> 
                                                    </div>
                                                    <div class="col-md-4 p-0">
                                                            <input type="text" id="lastname" class="form-control form-element" value="{{ old('lastname', $data->lastname) }}" name="lastname" autocomplete="off" list="lastname-options">
                                                        <datalist id="name-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
                                                    </div>
                                                    <div class="col-md-4 pl-0 ">
                                                        <input type="text" id="name" class="form-control form-element" value="{{ old('name', $data->name) }}" name="name" autocomplete="off" list="name-options">
                                                        <datalist id="lastname-options">
                                                            <!-- Options will be populated by JavaScript -->
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Main Address Inputs -->
                                                
                
                                                    <div class="col-12">
                                                        <div class="form-group row form-element">
                                                                <div class="col-md-2">
                                                                    <span>STR./NR./PLZ./ORT</span>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <input id="full_address" type="text" class="form-control form-element" placeholder="Enter location" name="full_address" value="{{ old('full_address', $data->full_address) }}">
                                                                    <input type="hidden" id="latitude-input" name="latitude" value="{{ old('latitude', $data->latitude) }}">
                                                                    <input type="hidden" id="longitude-input" name="longitude" value="{{ old('longitude', $data->longitude) }}">
                                                                    <input id="street-input" type="hidden" class="form-control form-element"   name="street" value="{{ old('street') }}">
                                                                    <input type="hidden" id="elevation-input" name="elevation" value="{{ old('elevation', $data->elevation) }}">
                                                                    <input type="hidden" class="form-control form-element" placeholder="Postal Code" value="{{ old('postcode', $data->postcode) }}" name="postcode" id="postal_code-input">
                                                                    <input type="hidden" class="form-control form-element" placeholder="City" value="{{ old('city', $data->city) }}" name="city" id="locality-input">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Festnet/Mobile</span>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" value="{{old('telephone', $data->telephone)}}" id="telephone-input" name="telephone" placeholder="Festnet">
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" value="{{old('phone', $data->phone)}}" name="phone" id="phone-input" placeholder="Mobile">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>E-Mail</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="email" class="form-control" id="email-input"  value="{{old('email', $data->email)}}"  name="email">
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Anfragegrund</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="test" class="form-control" id="reason" 
                                                            value="{{old('reason, $data->reason')}}"   name="reason">
                                                            </div>
                                                        </div>
                                                    </div>

                                                      <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Notiz</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <textarea name="description" id="" cols="30" rows="10" class="form-control">
                                                                    {{ old('note', $data->note) }}
                                                                </textarea>
                                                            </div>
                                                        </div>
                                                    </div> 

                                                     <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Nächster Schritt</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                    <select name="next_step" id="next_step" class="form-control select2">
                                                                        <option value="">Bitte wählen</option>
                                                                        @php
                                                                            $steps = [
                                                                                'Rückruf erledigen',
                                                                                'Problem klären',
                                                                                'E-Mail senden',
                                                                                'Dokumente schicken (Angebot, Rechnung, Vertrag)',
                                                                                'An Abteilung weiterleiten',
                                                                                'Termin mit Geschäftsführung planen',
                                                                                'Follow-up in 3 Tagen',
                                                                                'Follow-up in 1 Woche',
                                                                                'Besuch vor Ort planen',
                                                                                'Weitere Unterlagen anfordern',
                                                                                'Telefonat planen',
                                                                                'Angebot nachfassen',
                                                                                'Interne Rücksprache erforderlich',
                                                                                'Projektbesprechung vorbereiten',
                                                                                'Kein weiterer Schritt',
                                                                            ];
                                                                        @endphp

                                                                        @foreach ($steps as $step)
                                                                            <option value="{{ $step }}" {{ $data->next_step == $step ? 'selected' : '' }}>
                                                                                {{ $step }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                            </div>
                                                        </div>
                                                    </div> 


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-2">
                                                                <span>Fälligkeitsdatum</span>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <input type="date" id=""   class="form-control" name="due_date" value="{{ old('due_date', $data->due_date) }}">
                                                            </div>
                                                        </div>
                                                    </div> 

                                           
                                            </div> 
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>   
                        <div class="col-md-5 col-12">
                            <div class="cards"> 
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="form-body">
                                            <div class="row"> 
                                                <!-- Additional form fields go here --> 
                                                <div class="col-12" id="firma-container">
                                                    <div class="form-group row form-element"> 
                                                        <div class="col-md-10">
                                                                    <div class="map" id="gmp-map" style="width: 100%; position: relative; overflow: hidden; height: 564px;"></div>
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
                
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-primary   mr-1 mb-1 waves-effect waves-light float-right">speichern</button>
                    <a type="button"  href="{{url('inquiry_show/'.$data->id)}}" class="btn btn-outline-danger   mr-1 mb-1 waves-effect waves-light float-right">abbrechen </a>
                </div>
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
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initAutocomplete"
    async
    defer>
</script>
 
<script>
    let map;
    let marker;

    function initMap() {
        // Get latitude and longitude from server-side PHP variables
        const latitude = parseFloat("{{ $data->latitude ?? 'NaN' }}");
        const longitude = parseFloat("{{ $data->longitude ?? 'NaN' }}");

        // Check if latitude and longitude are valid numbers
        if (isNaN(latitude) || isNaN(longitude)) {
            // Show a SweetAlert with an error message in German
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: 'Standort nicht gefunden. Bitte geben Sie eine gültige Adresse ein.',
                confirmButtonText: 'OK'
            });

            // Default to a fallback location (e.g., Brussels)
            map = new google.maps.Map(document.getElementById('gmp-map'), {
                center: { lat: 50.8503, lng: 4.3517 }, // Default location: Brussels
                zoom: 10
            });
        } else {
            // Initialize map centered at the provided location
            map = new google.maps.Map(document.getElementById('gmp-map'), {
                center: { lat: latitude, lng: longitude },
                zoom: 15
            });

            // Place the initial marker
            marker = new google.maps.Marker({
                position: { lat: latitude, lng: longitude },
                map: map
            });
        }

        // Initialize the autocomplete functionality
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

        // Initialize Google Places Autocomplete
        const autocomplete = new google.maps.places.Autocomplete(fullAddressInput, {
            fields: ["address_components", "geometry"],
            types: ["address"],
        });

        // Listen for place selection
        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: 'Keine Details für die ausgewählte Adresse verfügbar.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Get latitude and longitude
            const location = place.geometry.location;
            latitudeInput.value = location.lat();
            longitudeInput.value = location.lng();

            // Update the map and marker
            updateMap(location);

            // Get elevation
            fetchElevation(location, elevationInput);

            // Parse and populate address components
            const addressComponents = parseAddressComponents(place.address_components);
            streetInput.value = `${addressComponents.route} ${addressComponents.street_number}`;
            postalCodeInput.value = addressComponents.postal_code;
            cityInput.value = addressComponents.locality || addressComponents.administrative_area_level_1 || addressComponents.administrative_area_level_2;

            // Set the full address back to the input field
            fullAddressInput.value = `${addressComponents.route} ${addressComponents.street_number}, ${cityInput.value}, ${addressComponents.postal_code}`;
        });

        // Fetch elevation using the Google Maps Elevation API
        function fetchElevation(location, elevationInput) {
            elevationService.getElevationForLocations(
                { locations: [location] },
                (results, status) => {
                    if (status === google.maps.ElevationStatus.OK && results[0]) {
                        elevationInput.value = results[0].elevation.toFixed(2);
                    } else {
                        elevationInput.value = "Höhe nicht verfügbar";
                    }
                }
            );
        }

        // Parse address components
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

        // Update map and marker
        function updateMap(location) {
            if (!marker) {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            } else {
                marker.setPosition(location);
            }

            map.setCenter(location);
            map.setZoom(15); // Zoom to the selected location
        }
    }

    // Initialize the map on page load
    document.addEventListener("DOMContentLoaded", initMap);
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


<script>
$(document).ready(function() {
    const selectedDepartment = "{{ $department_id ?? '' }}";
    const selectedEmployee = "{{ $direct_to ?? '' }}";

    $('#department').select2({ placeholder: 'Abteilung wählen' });

    $('#employee').select2({
        placeholder: 'Mitarbeiter wählen',
        templateResult: formatEmployee,
        templateSelection: formatEmployeeSelection,
        escapeMarkup: function (markup) { return markup; }
    });

    function loadEmployees(deptId, selectedEmpId = null) {
        if (!deptId) return;

        $.ajax({
            url: '/inquiry/department/employees/' + deptId,
            method: 'GET',
            success: function(data) {
                let $employee = $('#employee');
                $employee.empty();

                $.each(data, function(i, emp) {
                    let positions = emp.positions.join(', ');
                    let option = new Option(`${emp.name} ${emp.lastname}`, emp.id, false, emp.id == selectedEmpId);
                    $(option).attr('data-img', emp.image).attr('data-positions', positions);
                    $employee.append(option);
                });

                $employee.trigger('change');
            }
        });
    }

    // Load employee list on department change
    $('#department').on('change', function() {
        const deptId = $(this).val();
        loadEmployees(deptId);
    });

    // If editing, pre-select department & employee
    if (selectedDepartment) {
        loadEmployees(selectedDepartment, selectedEmployee);
    }

    function formatEmployee(emp) {
        if (!emp.id) return emp.text;
        let image = $(emp.element).data('img') ? '/images/employee/' + $(emp.element).data('img') : '';
        let positions = $(emp.element).data('positions') || '';
        return `
            <div style="display:flex; align-items:center;">
                <img src="${image}" class="rounded-circle" width="40" height="40" style="margin-right:10px;" />
                <div>
                    <strong>${emp.text}</strong><br>
                    <small>[${positions}]</small>
                </div>
            </div>
        `;
    }

    function formatEmployeeSelection(emp) {
        return emp.text;
    }
});
</script>




<!-- Showing and hiding the dpeatment and employee Dropdown: start -->
 
 
@endsection