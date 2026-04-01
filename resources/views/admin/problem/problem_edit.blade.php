
@extends('admin.layouts.app')
@section('title') PROBLEM | Add @stop

@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<meta name="csrf-token" content="{{ csrf_token() }}">

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
                            <h2 class="content-header-title float-left mb-0">PROBLEM</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                     <li class="breadcrumb-item"><a href="{{url('/')}}">HOME</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{url('/problem_view')}}">TICKET</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Beartbeiten</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    
                </div>
            </div>
            <div class="content-body"> 
                <section id="nav-justified">
                    <div class="row">
                        <!-- LEFT: Form Section (8 Columns) -->
                        <div class="col-md-8">
                            <div class="card">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>Error!</strong> Something went wrong:<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="card-content">
                                <form class="wizard-circle" method="POST" action="{{ route('problem.update', $problems->id) }}">
                                            @csrf
                                            @method('PUT')


                                        <div class="card-body">
                                            <input type="hidden" name="start_user" value="{{ auth()->user()->name }}">

                                            <div class="row">
                                                <!-- Error Code -->
                                                <div class="col-md-6">
                                                    <label for="error_code">* Fehlercode</label>
                                                    <select class="select2 form-control" multiple name="error_code[]" id="error_code">
                                                    <option value="add_new_error" id="add_new_error_option">+ Add New Error</option>

                                                        @foreach ($error_codes as $error)
                                                            <option value="{{ $error->error_id }}" selected>
                                                                {{ $error->problem_types }}
                                                            </option>
                                                        @endforeach 

                                                    </select> 

                                                    @error('error_code')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>

                                                 <div class="col-md-6">
                                                    <label for="source">* Quelle</label>
                                                    <select class="select2 form-control" name="source" id="source">
                                                        <optgroup label="Quelle auswählen">
                                                            @php
                                                                $sources = [
                                                                    'Kunde', 'Mitarbeiter', 'System', 'Telefonisch', 'E-Mail', 'Vor Ort', 'Intern', 'Extern',
                                                                    'Webformular', 'Support-Portal', 'Live-Chat', 'API', 'Monitoring', 'Social Media', 'WhatsApp',
                                                                    'Fax', 'Slack', 'Teams', 'Besuch', 'Manuell erstellt', 'Weitergeleitet'
                                                                ];
                                                            @endphp

                                                            @foreach($sources as $src)
                                                                <option value="{{ $src }}" {{ old('source', $problems->source ?? '') == $src ? 'selected' : '' }}>
                                                                    {{ $src }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>

                                                    @error('source')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>


                                                <!-- Ticket Type -->
                                                <div class="col-md-6">
                                                    <label for="error_type">* Tickettyp</label>
                                                    <select class="select2 form-control" name="error_type" id="error_type">
                                                            <optgroup label="Typ auswählen">
                                                                @php
                                                                    $errorTypes = [
                                                                        'complaint' => 'REKLAMATION',
                                                                        'emergency_service' => 'NOTDIENST',
                                                                        'repair' => 'REPARATUR',
                                                                        'maintenance' => 'WARTUNG',
                                                                        'malfunction' => 'STÖRUNG',
                                                                        'installation' => 'INSTALLATION',
                                                                        'configuration_error' => 'KONFIGURATION',
                                                                        'system_outage' => 'SYSTEMAUSFALL',
                                                                        'security_issue' => 'SICHERHEITSPROBLEM',
                                                                        'user_error' => 'BEDIENUNGSFEHLER',
                                                                        'network_problem' => 'NETZWERKFEHLER',
                                                                        'software_bug' => 'SOFTWAREFEHLER',
                                                                        'hardware_defect' => 'HARDWAREFEHLER',
                                                                        'spare_part_request' => 'ERSATZTEILANFRAGE',
                                                                        'timeout' => 'ZEITÜBERSCHREITUNG',
                                                                        'communication_failure' => 'KOMMUNIKATIONSPROBLEM',
                                                                        'power_outage' => 'ENERGIEAUSFALL',
                                                                        'update_failure' => 'UPDATEFEHLER',
                                                                        'access_issue' => 'ZUGRIFFSPROBLEM',
                                                                        'other' => 'SONSTIGES',
                                                                    ];
                                                                @endphp

                                                                @foreach($errorTypes as $value => $label)
                                                                    <option value="{{ $value }}" {{ old('error_type', $problems->error_type ?? '') == $value ? 'selected' : '' }}>
                                                                        {{ $label }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>

                                                    @error('error_type')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>

                                                <!-- Customer -->
                                                <div class="col-md-6">
                                                    <label for="customer_id">* Kunden</label>
                                                    <a href="{{url('new_lead_create')}}" target="_blank"  id="add-customer-btn" style="display: none;">
                                                        <i class="feather icon-plus"></i> Neuen Kunden hinzufügen
                                                    </a>
                                                    <select class="select2 form-control" id="customer_id" name="customer_id" data-selected-id="{{ $problems->customer_id ?? '' }}">
                                                        <option disabled selected>Kunde suchen...</option>
                                                    </select>

                                                    @error('customer_id')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>

                                                <!-- Product -->
                                                <div class="col-md-3">
                                                    <label for="product_id">* Produkt</label>
                                                    <input type="hidden" name="alternative_id" id="alternative_id" value="{{ $problems->alternative_id ?? '' }}">

                                                    <select class="select2 form-control" name="product_id" id="product_id"
                                                        data-selected-product="{{ $problems->product_id ?? '' }}"
                                                        data-customer-id="{{ $problems->customer_id ?? '' }}">
                                                        <option disabled selected>Bitte wählen Sie einen Kunden zuerst</option>
                                                    </select>

                                                    @error('product_id')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>
 
                                                <!-- Artikelname -->
                                                    <div class="col-md-3">
                                                        <label for="article_name">Artikelname</label> 
                                                        <input type="text" name="article_name" class="form-control"
                                                            value="{{ old('article_name', $problems->article_name ?? '') }}">
                                                        @error('article_name')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                    <!-- Seriennummer -->
                                                    <div class="col-md-3">
                                                        <label for="article_sn">Artikel Seriennummer</label> 
                                                        <input type="text" name="article_sn" class="form-control"
                                                            value="{{ old('article_sn', $problems->article_sn ?? '') }}">
                                                        @error('article_sn')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                    <!-- Installationsdatum -->
                                                    <div class="col-md-3">
                                                        <label for="installation_date">Installationsdatum</label> 
                                                        <input type="date" name="installation_date" class="form-control"
                                                            value="{{ old('installation_date', $problems->installation_date ?? '') }}">
                                                        @error('installation_date')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                    <!-- Garantie / Gewährleistung -->
                                                    <div class="col-md-3">
                                                        <label for="warranty_type">Garantie / Gewährleistung</label> 
                                                        <select name="warranty_type" class="form-control">
                                                            <option disabled {{ old('warranty_type', $problems->warranty_type ?? '') == null ? 'selected' : '' }}>Wählen</option>
                                                            <option value="guarantee" {{ old('warranty_type', $problems->warranty_type ?? '') == 'guarantee' ? 'selected' : '' }}>Garantie</option>
                                                            <option value="warranty" {{ old('warranty_type', $problems->warranty_type ?? '') == 'warranty' ? 'selected' : '' }}>Gewährleistung</option>
                                                        </select>
                                                        @error('warranty_type')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                    <!-- Garantie Dauer -->
                                                    <div class="col-md-3">
                                                        <label for="warranty_duration">Gewährleistung duern</label> 
                                                        <select name="warranty_duration" class="form-control">
                                                            <option disabled {{ old('warranty_duration', $problems->warranty_duration ?? '') == null ? 'selected' : '' }}>Wählen</option>
                                                            @foreach([
                                                                '1 week' => '1 Woche',
                                                                '2 weeks' => '2 Wochen',
                                                                '1 month' => '1 Monat',
                                                                '2 months' => '2 Monate',
                                                                '3 months' => '3 Monate',
                                                                '6 months' => '6 Monate',
                                                                '9 months' => '9 Monate',
                                                                '1 year' => '1 Jahr',
                                                                '18 months' => '18 Monate',
                                                                '2 years' => '2 Jahre',
                                                                '3 years' => '3 Jahre',
                                                                '5 years' => '5 Jahre',
                                                                '10 years' => '10 Jahre',
                                                                'Lifetime' => 'Lebenslange Garantie'
                                                            ] as $value => $label)
                                                                <option value="{{ $value }}" {{ old('warranty_duration', $problems->warranty_duration ?? '') == $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('warranty_duration')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                    <!-- Restzeit -->
                                                    <div class="col-md-3">
                                                        <label for="remaining_waranty">Gewährleistung restzeit</label>
                                                        <input type="number" name="remaining_waranty" class="form-control" min="0"
                                                            value="{{ old('remaining_waranty', $problems->remaining_waranty ?? 0) }}">
                                                        <code id="warranty_status_label" style="font-size: 11px; display:none;"></code>
                                                        @error('remaining_waranty')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>



                                                    <div class="col-md-3">
                                                        <label for="finance_to">Kostenübernahme</label> 
                                                        <select name="finance_to" class="form-control">
                                                            <option disabled {{ old('finance_to', $problems->finance_to ?? '') == null ? 'selected' : '' }}>Wählen</option>
                                                            <option value="customer" {{ old('finance_to', $problems->finance_to ?? '') == 'customer' ? 'selected' : '' }}>Kunde</option>
                                                            <option value="our_company" {{ old('finance_to', $problems->finance_to ?? '') == 'our_company' ? 'selected' : '' }}>Unser Unternehmen</option>
                                                            <option value="product_company" {{ old('finance_to', $problems->finance_to ?? '') == 'product_company' ? 'selected' : '' }}>Hersteller</option>
                                                            <option value="third_party" {{ old('finance_to', $problems->finance_to ?? '') == 'third_party' ? 'selected' : '' }}>Drittanbieter</option>
                                                            <option value="expired" {{ old('finance_to', $problems->finance_to ?? '') == 'expired' ? 'selected' : '' }}>Abgelaufen</option>
                                                            <option value="extended" {{ old('finance_to', $problems->finance_to ?? '') == 'extended' ? 'selected' : '' }}>Erweiterte Garantie</option>
                                                            <option value="none" {{ old('finance_to', $problems->finance_to ?? '') == 'none' ? 'selected' : '' }}>Keine Garantie</option>
                                                        </select>
                                                        @error('finance_to')<p style="color:red;">{{ $message }}</p>@enderror
                                                    </div>

                                                <!-- Responsible -->
                                                <div class="col-md-6">
                                                    <label for="responsible">* Zuständig (Suche nach Abteilung, bzw. Personen)</label>
                                                    <select class="select2 form-control" multiple name="responsible[]" id="responsible">
                                                            @foreach ($responsibles as $res)
                                                                <option value="{{ $res->employee_id }}" selected>
                                                                    {{ $res->rname }} {{ $res->rlastname }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @error('responsible')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>

                                                <!-- First Contact -->
                                                <div class="col-md-6">
                                                    <label for="first_contact">Erstellt von</label>
                                                    <select class="select2 form-control" name="first_contact" id="first_contact">
                                                        <optgroup label="Verantwortlicher auswählen">
                                                            @foreach($responsible as $res)
                                                                <option value="{{ $res->id }}"
                                                                    {{ old('first_contact', $problems->first_contact ?? auth()->user()->name) == $res->id ? 'selected' : '' }}>
                                                                    {{ $res->name }} {{ $res->lastname }}
                                                                    @if(auth()->user()->name == $res->id) (Aktueller Benutzer) @endif
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    </select>

                                                    @error('first_contact')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>

                                                <!-- Date -->
                                                <div class="col-md-3">
                                                    <label for="date">Datum</label>
                                                    <input type="date" class="form-control required" id="date" name="date"
                                                        value="{{ old('date', $problems->date ?? date('Y-m-d')) }}">
                                                    @error('date')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>


                                                <div class="col-md-3">
                                                    <label for="priority">Priorität</label>
                                                    <select name="priority" id="priority" class="form-control select2">
                                                        <option value="normal" data-icon="fa fa-battery-empty"
                                                            {{ old('priority', $problems->priority ?? '') == 'normal' ? 'selected' : '' }}>Keiner</option> 
                                                        <option value="Dringend" data-icon="fa fa-battery-full"
                                                            {{ old('priority', $problems->priority ?? '') == 'Dringend' ? 'selected' : '' }}>Dringend</option>
                                                        <option value="Sehr Dringend" data-icon="fa fa-fire text-danger"
                                                            {{ old('priority', $problems->priority ?? '') == 'Sehr Dringend' ? 'selected' : '' }}>Sehr Dringend</option>
                                                    </select>
                                                    @error('priority')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>


                                                <!-- Repeated Checkbox -->
                                                <div class="col-md-3">
                                                    <label for="repeated">Tritt dieses Problem schon einmal auf?</label>
                                                    <div class="form-group">
                                                        <fieldset>
                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                <input type="checkbox" name="repeated" value="1"
                                                                    {{ old('repeated', $problems->repeated ?? false) ? 'checked' : '' }}>
                                                                <span class="vs-checkbox">
                                                                    <span class="vs-checkbox--check">
                                                                        <i class="vs-icon feather icon-check"></i>
                                                                    </span>
                                                                </span>
                                                                <span class="">Wiederholtes</span>
                                                            </div>
                                                        </fieldset>

                                                    </div>
                                                </div>

                                                <!-- Quill Editor -->
                                                <div class="col-12">
                                                    <label for="editor">Problem Beschreibung</label>
                                                    <div id="editor" class="form-control" style="height: 400px !important;">
                                                    {{ old('editor_text', $problems->problem) }}
                                                    </div>
                                                    <textarea name="editor_text" hidden id="editor_text" cols="30" rows="10">{{ old('editor_text', $problems->problem) }}</textarea>
                                                    @error('editor_text')<p style="color:red;">{{ $message }}</p>@enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn bg-primary mr-1 mb-1 waves-effect waves-light">Speichern</button>
                                            <a type="button" class="btn bg-danger mr-1 mb-1 waves-effect waves-light" href="{{ url('problem_view')}}">abbrechen</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Old Tickets Table (4 Columns) -->
                        <div class="col-md-4">
                            <h2></h2>

                            <div class="card"  >
                                <div class="card-header">
                                    <h4 class="card-title">Kundenhistorie</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body"> 
                                        <ul class="nav nav-pills nav-active-bordered-pill">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="base-pill31" data-toggle="pill" href="#pill31" aria-expanded="true">Alte Tickets </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="base-pill32" data-toggle="pill" href="#pill32" aria-expanded="false">Alle Produkte</a>
                                            </li>
                                            
                                        </ul>
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="pill31" aria-expanded="true" aria-labelledby="base-pill31">
                                                 <div class="table-responsive">
                                                        <table class="table table-bordered" id="old_ticket">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Ticket#</th>
                                                                    <th>Kunde</th>
                                                                    <th>Produkt</th>
                                                                    <th>Registiert am</th> 
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <!-- AJAX will populate here -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                            </div>
                                            <div class="tab-pane" id="pill32" aria-labelledby="base-pill32">
                                                <div class="table-responsive">
                                                    <table class="table" id="all_products">
                                                        <thead>
                                                            <tr> 
                                                                <th>Name</th> 
                                                                <th>Adress</th>
                                                                <th>Produkt</th>  
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                         
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>



                        <!-- Modal for adding new error -->
                            <div class="modal" id="addErrorModal" tabindex="-1" role="dialog" aria-labelledby="addErrorModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addErrorModalLabel">Add New Error</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="addErrorForm">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="new_error_code">Fehlercode</label>
                                                    <input type="text" class="form-control" id="new_error_code" name="error_code" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="new_problem_types">Fehlerbeschreibung</label>
                                                    <input type="text" class="form-control" id="new_problem_types" name="problem_types" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="new_solution">Fehlerursache	</label>
                                                    <textarea class="form-control" id="new_solution" name="solution"  ></textarea>
                                                </div>
                                                 <div class="form-group">
                                                    <label for="new_solution">Lösung</label>
                                                    <textarea class="form-control" id="new_reason" name="reason"  ></textarea>
                                                </div>
                                                <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee_id }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">abbrechen</button>
                                                <button type="submit" class="btn btn-primary">speichern</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal for adding new error -->
                    </div>
                </section>

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection



@section('script')
<!-- <script src="{{asset('app-assets/js/scripts/editors/editor-quill.js')}}"></script> -->
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>

  

<!-- Quill Other Editor -->
<script>
   $(document).ready(function () {
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'script': 'sub' }, { 'script': 'super' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
    ];

    var quill = new Quill('#editor', {
        modules: {
            toolbar: toolbarOptions
        },
        theme: 'snow'
    });

    // ✅ Load content for edit mode
    const existingContent = {!! json_encode(old('editor_text', $problems->problem ?? '')) !!};
    quill.root.innerHTML = existingContent;
    $('#editor_text').val(existingContent); // Also fill hidden textarea initially

    // ✅ Sync content to hidden textarea on change
    quill.on('text-change', function (delta, oldDelta, source) {
        if (source === 'user') {
            $('#editor_text').val(quill.root.innerHTML);
        }
    });
});


        </script>


<script>
$(document).ready(function() {
    $('#customer_id').select2();
    $('#product_id').select2();
    $('#error_type').select2();
    $('#responsible').select2();
  

    // $("#problem_types").select2({
    //     tags: true
    //     });
    
});
</script>

<script>
$(document).ready(function () {
    let lastTypedEditTerm = '';

    $('#error_code').select2({
        placeholder: 'Fehlercode auswählen',
        allowClear: true,
        width: '100%',
        tags: false,
        minimumInputLength: 0,
        ajax: {
            url: '/get-error-codes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term || '' };
            },
            processResults: function (data) {
                const selected = $('#error_code').val() || [];
                let results = data.results.filter(item => !selected.includes(item.id.toString()));

                // Add custom "+ New" option if missing
                const alreadyExists = results.some(r => r.id === 'add_new_error');
                if (!alreadyExists) {
                    results.push({
                        id: 'add_new_error',
                        text: '+ Neuen Fehler erstellen'
                    });
                }

                return { results };
            },
            cache: true
        }
    });

    // Capture the live typed term
    $(document).on('keyup', '.select2-search__field', function () {
        lastTypedEditTerm = $(this).val();
    });

    // When "+ Add New Error" is clicked
    $('#error_code').on('select2:select', function (e) {
        const selectedId = e.params.data.id;

        if (selectedId === 'add_new_error') {
            $('#new_problem_types').val(lastTypedEditTerm);
            $('#new_error_code').val('SA-' + Math.floor(1000 + Math.random() * 9000));
            $('#addErrorModal').modal('show');
        }
    });

    // Modal submit for new error
    $('#addErrorForm').on('submit', function (e) {
        e.preventDefault();

        const errorCode   = $('#new_error_code').val();
        const problemType = $('#new_problem_types').val();
        const solution    = $('#new_solution').val();
        const reason      = $('#new_reason').val();
        const employeeId  = $('#employee_id').val();

        $.ajax({
            url: '/add-new-error',
            method: 'POST',
            data: {
                error_code: errorCode,
                problem_types: problemType,
                solution: solution,
                reason: reason,
                employee_id: employeeId,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    // Remove the "+ New" fake option
                    $('#error_code option[value="add_new_error"]').remove();

                    // Add real error and select it
                    const newOption = new Option(errorCode, response.id, true, true);
                    $('#error_code').append(newOption).trigger('change');

                    $('#addErrorForm')[0].reset();
                    $('#addErrorModal').modal('hide');
                } else {
                    alert('Fehler beim Speichern!');
                }
            },
            error: function () {
                alert('Ein Fehler ist aufgetreten!');
            }
        });
    });
});
</script>

 
<script>
$(document).ready(function () {

    const csrf = '{{ csrf_token() }}';
    const customerSelect = $('#customer_id');
    const productSelect = $('#product_id');
    const selectedCustomerId = customerSelect.data('selected-id');
    const selectedProductId = productSelect.data('selected-product');

    const serviceNames = {
        'complete': 'Komplettlösung',
        'montage': 'Montage',
        'product': 'Produkt',
        'plan': 'Planung',
        'maintenance': 'Wartung',
        'repair': 'Reparatur',
        'emergency': 'Notdienst',
        'others': 'Sonstiges'
    };

    const statusNames = {
        'lead': 'Lead',
        'plan': 'Planung',
        'offer': 'Angebot',
        'deal': 'Deal',
        'project': 'Projekt',
        'completed': 'Abgeschlossen',
        'junk': 'Müll',
        'ticket': 'Ticket'
    };

    // 🟢 Load customer and all others on page load
    if (selectedCustomerId) {
        $.ajax({
            url: `/get/ticket/customer/${selectedCustomerId}`,
            dataType: 'json',
            success: function (res) {
                const { selected_customer, all_customers } = res;

                // Preselect current customer
                if (selected_customer) {
                    const label = `${selected_customer.customer_name} ${selected_customer.customer_lastname} - ${selected_customer.city || ''}`;
                    const selectedOption = new Option(label, selected_customer.customer_id, true, true);
                    customerSelect.append(selectedOption);
                }

                // Fill in other customers
                if (Array.isArray(all_customers)) {
                    all_customers.forEach(c => {
                        if (c.customer_id != selectedCustomerId) {
                            const text = `${c.customer_name || c.name} ${c.customer_lastname || c.lastname} - ${c.city || ''}`;
                            const option = new Option(text, c.customer_id, false, false);
                            customerSelect.append(option);
                        }
                    });
                }

                // Initialize select2
                customerSelect.select2({
                    placeholder: "Kunde suchen...",
                    allowClear: true
                });

                // Load products for selected customer
                loadProductsForCustomer(selectedCustomerId);
            },
            error: function (xhr) {
                console.error("Kunden konnten nicht geladen werden:", xhr.responseText);
            }
        });
    } else {
        // If no selected customer, load all
        initCustomerSelect2();
    }

    // 🔁 Initialize Select2 with live search
    function initCustomerSelect2() {
        customerSelect.select2({
            placeholder: "Kunde suchen...",
            ajax: {
                url: '{{ route("problem.all.customer") }}',
                dataType: 'json',
                delay: 300,
                processResults: function (data) {
                    if (data.status === 'empty') {
                        $('#add-customer-btn').show();
                        return { results: [] };
                    }
                    $('#add-customer-btn').hide();
                    return {
                        results: data.map(c => ({
                            id: c.customer_id || c.id,
                            text: `${c.customer_name || c.name} ${c.customer_lastname || c.lastname} - ${c.city || ''}`
                        }))
                    };
                },
                cache: true
            }
        });
    }

    // 🟣 Load products for a customer
    function loadProductsForCustomer(customerId) {
        if (!customerId) return;

        $.ajax({
            url: '/check/ticket/products',
            method: 'POST',
            data: { customer_id: customerId, _token: csrf },
            beforeSend: function () {
                productSelect.html('<option disabled selected>Produkte werden geladen...</option>');
            },
            success: function (data) {
                productSelect.empty();
                $('#all_products tbody').empty();

                if (data.length > 0) {
                    productSelect.append('<option disabled selected>Bitte wählen Sie ein Produkt</option>');
                    data.forEach(item => {
                        const address = `${item.street}, ${item.postcode} ${item.city}`;
                        const isSelected = item.product_id == selectedProductId ? 'selected' : '';
                        productSelect.append(
                            `<option value="${item.product_id}" 
                                    data-alternative-id="${item.alternative_id}" 
                                    data-status="${item.status}" ${isSelected}>
                                ${item.article_group} – ${statusNames[item.status] || item.status} (${address})
                            </option>`
                        );

                        const service = serviceNames[item.service] || item.service;
                        const status = statusNames[item.status] || item.status;

                        $('#all_products tbody').append(`
                            <tr>
                                <td>${item.article_group}</td>
                                <td>${address}</td>
                                <td>${item.product_id}</td>
                                <td>${service}</td>
                                <td>${status}</td>
                            </tr>
                        `);
                    });
                    productSelect.trigger('change');
                } else {
                    productSelect.append('<option disabled selected>Keine Produkte gefunden</option>');
                    $('#all_products tbody').html('<tr><td colspan="5">Keine Produkte gefunden</td></tr>');
                }
            },
            error: function () {
                productSelect.html('<option disabled selected>Fehler beim Laden der Produkte</option>');
            }
        });
    }

    // 🟡 When customer changes
    customerSelect.on('change', function () {
        const customerId = $(this).val();
        loadProductsForCustomer(customerId);
        loadOldTickets(customerId);
    });

    // 📦 When product changes
    productSelect.on('change', function () {
        const selectedOption = $(this).find('option:selected');
        $('#alternative_id').val(selectedOption.data('alternative-id') || '');
    });

    // 🧾 Load old tickets
    function loadOldTickets(customerId) {
        $.ajax({
            url: '{{ route("problem.check.ticket") }}',
            method: 'POST',
            data: { customer_id: customerId, _token: csrf },
            success: function (data) {
                const tableBody = $('#old_ticket tbody');
                tableBody.empty();

                if (data.length === 0) {
                    tableBody.append('<tr><td colspan="5" class="text-center">Keine früheren Tickets gefunden</td></tr>');
                } else {
                    data.forEach(ticket => {
                        tableBody.append(`
                            <tr>
                                <td>${ticket.ticket_no}
                                    <div class="badge badge-primary">${ticket.status}</div>
                                </td>
                                <td>${ticket.customer_name} ${ticket.customer_lastname}</td>
                                <td>${ticket.product_name}</td>
                                <td>${ticket.date}</td>
                            </tr>
                        `);
                    });
                }
            },
            error: function () {
                alert('Fehler beim Laden der alten Tickets.');
            }
        });
    }

    // ➕ Add new customer
    $('#add-customer-btn').on('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Neuen Kunden hinzufügen?',
            text: "Du wirst zur Seite zur Kundenerstellung weitergeleitet.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ja, weiter',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open('{{ url("new_lead_create") }}', '_blank');
                setTimeout(() => {
                    customerSelect.val(null).trigger('change');
                    customerSelect.select2('destroy');
                    initCustomerSelect2();
                }, 3000);
            }
        });
    });

});
</script>



<script>
$(document).ready(function () {
    $('.wizard-circle').on('submit', function (e) {
        e.preventDefault();
        $('.text-danger').remove();

        let form = $(this);
        let formData = new FormData(this);
        formData.set('editor_text', $('#editor .ql-editor').html());

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: form.attr('action'), // ✅ dynamic to match your PUT route
            type: "POST",             // ✅ stays POST (because of method spoofing)
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Erfolgreich',
                    text: response.message
                }).then(() => {
                    window.location.href = '/problem_view';
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    let fields = [
                        'error_code', 'error_type', 'customer_id',
                        'product_id', 'responsible', 'first_contact', 'date'
                    ];

                    fields.forEach(function (fieldName) {
                        let field = $(`[name="${fieldName}"], [name="${fieldName}[]"]`);
                        if (field.length > 0 && errors[fieldName]) {
                            field.closest('.col-md-3, .col-md-6, .col-12')
                                .append(`<span class="text-danger">${errors[fieldName][0]}</span>`);
                        }
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: 'Bitte füllen Sie alle Pflichtfelder korrekt aus.'
                    });
                }
            }
        });
    });
});
</script>



<script>
    $(document).ready(function() {
        $('#source').select2({
            tags: true,
            placeholder: "Quelle auswählen oder neue eingeben",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Keine Ergebnisse gefunden";
                }
            }
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

<!-- Calcuate the remaining time of warranty  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const installInput = document.querySelector('[name="installation_date"]');
    const durationSelect = document.querySelector('[name="warranty_duration"]');
    const remainingField = document.querySelector('[name="remaining_waranty"]');
    const financeSelect = document.querySelector('[name="finance_to"]');
    const warrantyLabel = document.getElementById('warranty_status_label');

    function updateWarrantyInfo() {
        const installDateVal = installInput.value;
        const durationVal = durationSelect.value;

        if (!installDateVal || !durationVal) return;

        const installDate = new Date(installDateVal);
        let endDate = new Date(installDate);

        if (durationVal === "Lifetime") {
            remainingField.value = 9999;
            warrantyLabel.innerText = "Lebenslange Garantie";
            warrantyLabel.style.display = "inline-block";
            financeSelect.value = "our_company";
            return;
        }

        // Ensure durationVal is in the correct format (e.g., "2 weeks", "1 month", "1 year")
        const durationParts = durationVal.split(" ");
        if (durationParts.length !== 2) return; // If the format is not correct, exit the function

        const value = parseInt(durationParts[0], 10);
        const unit = durationParts[1];

        if (isNaN(value)) return; // If the value is not a valid number, exit the function

        // Handle different units
        if (unit.includes("week")) {
            endDate.setDate(endDate.getDate() + value * 7);
        } else if (unit.includes("month")) {
            endDate.setMonth(endDate.getMonth() + value);
        } else if (unit.includes("year")) {
            endDate.setFullYear(endDate.getFullYear() + value);
        } else {
            return; // If unit is unknown, exit the function
        }

        const today = new Date();
        const timeDiff = endDate - today;

        if (timeDiff > 0) {
            const totalDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            remainingField.value = totalDays;
            warrantyLabel.style.display = "inline-block";

            // Convert to years, months, days
            let years = Math.floor(totalDays / 365);
            let remainingDays = totalDays % 365;
            let months = Math.floor(remainingDays / 30);
            let days = remainingDays % 30;

            let resultText = "";
            if (years > 0) resultText += `${years} Jahr${years > 1 ? 'e' : ''}, `;
            if (months > 0) resultText += `${months} Monat${months > 1 ? 'e' : ''}, `;
            resultText += `${days} Tag${days !== 1 ? 'e' : ''}`;

            warrantyLabel.innerText = resultText;
            financeSelect.value = "our_company";
        } else {
            remainingField.value = 0;
            warrantyLabel.innerText = "Abgelaufen";
            warrantyLabel.style.display = "inline-block";
            financeSelect.value = "customer";
        }
    }

    installInput.addEventListener("change", updateWarrantyInfo);
    durationSelect.addEventListener("change", updateWarrantyInfo);

    updateWarrantyInfo(); // Initialize on load
});

</script>



<script>
$(document).ready(function () {
    $('#responsible').select2({
        placeholder: 'Verantwortlich auswählen...',
        ajax: {
            url: '{{ route("problem.get.responsible") }}',
            dataType: 'json',
            delay: 300,
            processResults: function (data) {
                const selected = $('#responsible').val() || [];

                return {
                    results: data
                        .filter(emp => !selected.includes(emp.id.toString()))
                        .map(emp => ({
                            id: emp.id,
                            text: `${emp.name} ${emp.lastname}`,
                            full: emp
                        }))
                };
            }

        },
        templateResult: formatEmployeeOption,
        templateSelection: formatEmployeeSelection
    });


    function formatEmployeeOption(emp) {
        if (!emp.full) return emp.text;

        const departments = emp.full.departments.join(', ');
        const positions = emp.full.positions.join(', ');
        const leaveInfo = emp.full.on_leave
            ? `<span style="color:red;"><b>Abwesend</b> (${emp.full.leave_info.from} bis ${emp.full.leave_info.to})</span>`
            : `<span style="color:green;"><b>Verfügbar</b></span>`;

        const imageTag = emp.full.image
            ? `<img src="${emp.full.image}" style="width:30px;height:30px;border-radius:50%;margin-right:10px;">`
            : `<div style="width:30px;height:30px;background:#ccc;border-radius:50%;margin-right:10px;"></div>`;

        return $(`
            <div style="display:flex;align-items:center;gap:10px;">
                ${imageTag}
                <div style="line-height: 1.2;">
                    <strong>${emp.full.name} ${emp.full.lastname}</strong><br>
                    <small>${emp.full.email}</small><br>
                    <small><b>Abteilung:</b> ${departments}</small><br>
                    <small><b>Positionen:</b> ${positions}</small><br>
                    <small>${leaveInfo}</small>
                </div>
            </div>
        `);
    }


    function formatEmployeeSelection(emp) {
        if (!emp.full) return emp.text;
        return `${emp.full.name} ${emp.full.lastname}`;
    }
});
</script>


@endsection



