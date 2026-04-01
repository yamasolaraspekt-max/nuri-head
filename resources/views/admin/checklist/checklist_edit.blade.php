@extends('admin.layouts.app')
@section('title') Checkliste Konfiguration @endsection
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <style>
        .select2-container--default .select2-selection--single {
            height:51px;
        }

        .a4-page {
                width: 100%;
                max-width: 287mm;; /* A4 width */
                min-height: 297mm; /* A4 height */
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
                    max-width: 100%; /* Make it fit the screen width */
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

            .product_title {
                    font-size: 17px;
                    font-weight: bold;
                    text-transform: uppercase;
                    color: #add33d;
            }

            

    </style>

    <style>
    .section-container {
      border-top: 2px solid #8CC63F;
      border-bottom: 2px solid #8CC63F;
      padding: 20px 10px;
    }
    .section-divider {
      border-left: 1px solid #bbb;
    }
    .title {
      font-weight: bold;
      font-size: 1rem;
    }
    .form-line {
      border-bottom: 1px solid #999;
      display: inline-block;
      width: 150px;
    }
    .option-label {
      margin-left: 5px;
      margin-right: 15px;
    }
    .radio-group {
      margin-top: 10px;
    }
    .green-radio input[type="radio"] {
      accent-color: #8CC63F;
    }

    .section-active {
        background-color: #ffffff !important;
      
        padding: 10px;
        }
    .supplier_section , .cran_section, .old_facility, .photo_section {
         background-color:#f4f4f4;
    }

  </style>

  <style>
    .section-header {
      background-color: #d5e7c3;
      font-weight: bold;
      padding: 8px 12px;
      margin-bottom: 20px;
    }
    .form-line {
      border-bottom: 1px solid #999;
      display: inline-block;
      width: 150px;
    }
    .option-label {
      margin-left: 6px;
    }
    .green-checkbox input[type="checkbox"] {
      accent-color: #8CC63F;
    }
    .status-col {
      border-left: 1px solid #aaa;
    }
    .form-label {
      font-weight: 500;
      margin-bottom: 3px;
    }
    .comment-line {
      border-bottom: 1px solid #999;
      height: 1rem;
      margin-bottom: 10px;
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
                            <h2 class="content-header-title float-left mb-0">CHECKLISTE</h2>
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
                <div class="container">
                    <!-- Step Indicators -->
                    <div class="row">
                        <div class="col-12">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" id="progress-bar" style="width: 20%;">
                                    Schritt 1 - 5
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step Navigation Buttons -->
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <ul class="nav nav-pills justify-content-center">
                                <li class="nav-item">
                                    <a class="nav-link active" id="step-1-tab" href="#" onclick="showStep(1)"><i class="feather icon-grid"></i> Schritt 1: Produkt auswählen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step-2-tab" href="#" onclick="showStep(2)">Schritt 2: Aufgaben</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step-3-tab" href="#" onclick="showStep(3)">Schritt 3: Optionen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="step-4-tab" href="#" onclick="showStep(4)">Schritt 4: Speichern</a>
                                </li>
                                
                            </ul>
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="row mt-5">
                        
                        <div class="col-12">
                            <form id="storeForm">
                                @csrf
                                <!-- Step 1: Product Selection -->
                                <div id="step-1" class="step-content">
                                    <h4>Wählen Sie das Gewerk</h4>
                                    <form id="step-1-form">
                                        <div class="form-group">
                                            <label for="product">Produkt</label>
                                            <select class="form-control" id="product" name="product" style="width:100%">
                                                <option ></option>
                                                @foreach ($article as $product)
                                                    <option value="{{$product->id}}" data-image="{{ asset('images/articles/'.$product->image) }}"> {{$product->article_group}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">Nächste</button>
                                    </form>
                                </div> 

                                <!-- Step 2: Placeholder -->
                                <div id="step-2" class="step-content d-none">
                                    <h4>Schritt 2: Aufgaben</h4> 
                                        <div class="container mt-4" id="checklist-container">
                                            <!-- Dynamic content will load here -->
                                        </div> 
                                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">Nächste</button>
                                </div>

                                <!-- Step 3: Placeholder -->
                                <div id="step-3" class="step-content d-none">
                                    <h4 class="text-center">Schritt 3: Checklistenoptionen</h4>
                                    <div class="a4-page">
                                        <div class="container-fluid">
                                            <!-- First section: Customer -->
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <h4 style=" color: #0d4094;"><strong><span class="product-title"></span> // PROJEKT-CHECKLISTE</strong></h4>
                                                </div>
                                                <div class="col-md-3 float-right">
                                                    <img src="{{ asset('logo/logo.png') }}" alt="" style="width: 194px;">
                                                </div>
                                            </div>
                                            <hr style="background: #add33e;  height: 2px;">
                                            <div class="row section match-height" id="customer-section">
                                                <div class="col-6"> 
                                                    <div class="cards">
                                                        <div class="card-body">

                                                            <div class="row match-height">
                                                                <div class="col-xl-12 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">Kunde</label>
                                                                        <input type="text" class="form-control" value=" John Doe" disabled  />
                                                                    
                                                                </div>

                                                                <div class="col-xl-12 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">ADRESSE:</label>
                                                                        <input type="text" class="form-control" value="1234, Sample Str, Stadt, Land" disabled  />
                                                                    
                                                                </div>
                                                                <div class="col-xl-6 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">TEL:</label>
                                                                        <input type="text" class="form-control" value="+123 456 7890" disabled  />
                                                                    
                                                                </div>

                                                                <div class="col-xl-6 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">E-MAIL:</label>
                                                                        <input type="text" class="form-control" value="johndoe@example.com" disabled  />
                                                                    
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <div class="col-6">
                                                    <div class="card mt-2" id="montageDiv">
                                                        <div class="card-body">
                                                            <!-- Checkbox inside the div to enable/disable the fields -->
                                                            <div class="form-check form-switch d-inline-block float-right">
                                                                <input class="form-check-input" type="checkbox" id="toggleDiv" name="plan_montage" />
                                                                <label class="form-check-label" for="toggleDiv">Aktivieren</label>
                                                            </div>
                                                            
                                                            <h5>GEPLANTE MONTAGE</h5>

                                                            <div class="row">
                                                                <!-- von -->
                                                                <div class="col-xl-6 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">von</label>
                                                                        <input type="text" class="form-control montage-input" disabled id="inputVon" />
                                                                    
                                                                </div>
                                                                <!-- Bis -->
                                                                <div class="col-xl-6 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">Bis</label>
                                                                        <input type="text" class="form-control montage-input" disabled id="inputBis" />
                                                                    
                                                                </div>
                                                                <!-- Anzahl Tage -->
                                                                <div class="col-xl-6 col-md-6 col-12">
                                                                    
                                                                        <label for="basicInput">Anzahl Tage</label>
                                                                        <input type="text" class="form-control montage-input" disabled id="inputTage" />
                                                                    
                                                                </div>
                                                                <!-- Anzahl Monteure -->
                                                                <div class="col-xl-6 col-md-6 col-12 ">
                                                                    
                                                                        <label for="basicInput">Anzahl Monteure</label>
                                                                        <input type="text" class="form-control montage-input" disabled id="inputMonteure" />
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <p class="product_title"> <span class="product-title"></span> HERSTELLER & TYP: </p>
                                            </div>

                                            <div class="row">
                                                <div class="container section-container">
                                                    <div class="row text-start">
                                                        <!-- DIREKTLIEFERUNG LIEFERANT -->
                                                        <div class="col-md-4 supplier_section">
                                                        <input class="form-check-input" type="checkbox" id="supplier_section" name="supplier_section" />

                                                        <div class="title">DIREKTLIEFERUNG LIEFERANT</div>
                                                        <div class="mt-2">
                                                            am <span class="form-line"></span>
                                                        </div>
                                                        <div class="mt-2">
                                                            um <span class="form-line"></span>
                                                        </div>
                                                        <div class="radio-group green-radio mt-2">
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="lieferant" class="form-check-input">
                                                            <span class="option-label">Inneneinheit</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="lieferant" class="form-check-input">
                                                            <span class="option-label">Außeneinheit</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="lieferant" class="form-check-input">
                                                            <span class="option-label">Pufferspeicher</span>
                                                            </label>
                                                        </div>
                                                        </div>

                                                        <!-- KRAN-HUB -->
                                                        <div class="col-md-4 section-divider cran_section">
                                                        <input class="form-check-input" type="checkbox" id="cran_section" name="cran_section" />
                                                        <div class="title">KRAN-HUB</div>
                                                        <div class="mt-2">
                                                            am <span class="form-line"></span>
                                                        </div>
                                                        <div class="mt-2">
                                                            um <span class="form-line"></span>
                                                        </div>
                                                        <div class="radio-group green-radio mt-2">
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="kran" class="form-check-input">
                                                            <span class="option-label">Inneneinheit</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="kran" class="form-check-input">
                                                            <span class="option-label">Außeneinheit</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="kran" class="form-check-input">
                                                            <span class="option-label">Pufferspeicher</span>
                                                            </label>
                                                        </div>
                                                        </div>

                                                        <!-- ALTE ANLAGE -->
                                                        <div class="col-md-4 section-divider old_facility">
                                                        <input class="form-check-input" type="checkbox" id="old_facility" name="old_facility" />
                                                        <div class="title">ALTE ANLAGE</div>
                                                        <div class="radio-group green-radio mt-2">
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="altanlage1" class="form-check-input">
                                                            <span class="option-label">Ölheizung</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="altanlage1" class="form-check-input">
                                                            <span class="option-label">Nachtspeicher</span>
                                                            </label>
                                                        </div>
                                                        <div class="radio-group green-radio mt-2">
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="altanlage2" class="form-check-input">
                                                            <span class="option-label">Gasheizung</span>
                                                            </label>
                                                            <label class="form-check-inline">
                                                            <input type="radio" name="altanlage2" class="form-check-input">
                                                            <span class="option-label">alte Wärmepumpe</span>
                                                            </label>
                                                        </div>
                                                        </div>

                                                        <div class="col-md-4 section-divider photo_section mt-2">
                                                            <input class="form-check-input" type="checkbox" id="photo_section" name="photo_section" />
                                                            <div class="title">FOTO VOHER</div>
                                                            <div class="radio-group green-radio mt-2">
                                                                <label class="form-check-inline">
                                                                <input type="radio" name="altanlage1" class="form-check-input">
                                                                <span class="option-label">Aussen (Aufstellort Ausseneinheit) </span>
                                                                </label>
                                                                <label class="form-check-inline">
                                                                <input type="radio" name="altanlage1" class="form-check-input">
                                                                <span class="option-label">Innen (Bestandheizung mit allen Komponenten)</span>
                                                                </label>
                                                                <label class="form-check-inline">
                                                                <input type="radio" name="altanlage1" class="form-check-input">
                                                                <span class="option-label">Fotos upload</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary mt-3" onclick="nextStep(4)">Nächste</button>
                                </div>
 
                                <!-- Step 4: Placeholder -->
                                <div id="step-4" class="step-content d-none">
                                   <h4>Schritt 4: Bestätigung</h4>
                                    <p>Name für diese Checkliste einrichten</p>
                                    <input type="hidden" id="checklist_id" value="{{ $checklist->id ?? '' }}">

                                    <input type="text" class="form-control" id="list_name" placeholder="Meine Projekt-Checkliste">
                                    <button type="submit" class="btn btn-success data-save">speichern</button>
                                </div>
                            </form>
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
<script>
    window.selectedActivities = @json($selectedActivities);
</script>

<script>
    const checklistId = '{{ $checklist->id ?? '' }}';
    const checklistUpdateUrl = checklistId ? `{{ url('/checklist/update') }}/${checklistId}` : `{{ route('checklists.store') }}`;
    const checklistMethod = checklistId ? 'PUT' : 'POST';
</script>


  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleSection = (checkboxId, sectionClass) => {
      const checkbox = document.getElementById(checkboxId);
      const section = document.querySelector('.' + sectionClass);

      checkbox.addEventListener('change', function () {
        if (this.checked) {
          section.classList.add('section-active');
        } else {
          section.classList.remove('section-active');
        }
      });
    };

    toggleSection('supplier_section', 'supplier_section');
    toggleSection('cran_section', 'cran_section');
    toggleSection('old_facility', 'old_facility');
    toggleSection('photo_section', 'photo_section');
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

<script>
    $(document).ready(function () {
        // Initialize Select2 with image templates
        $('#product').select2({
            templateResult: formatProduct,
            templateSelection: formatProductSelection,
            escapeMarkup: function (m) { return m; }
        });

        function formatProduct(product) {
            if (!product.id) return product.text;

            var imageUrl = $(product.element).data('image');
            return '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
                   '<span>' + product.text + '</span></span>';
        }

        function formatProductSelection(product) {
            if (!product.id) return product.text;

            var imageUrl = $(product.element).data('image');
            return '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
                   '<span>' + product.text + '</span></span>';
        }

        // Show step 1 on load
        showStep(1);
    });

    function showStep(step) {
        $('.step-content').addClass('d-none');
        $('#step-' + step).removeClass('d-none');

        const progress = step * 20;
        $('#progress-bar').css('width', progress + '%').text('Step ' + step + ' of 5');

        $('.nav-link').removeClass('active');
        $('#step-' + step + '-tab').addClass('active');
    }

    function nextStep(step) {
        const selectedProduct = $('#product').val();

        if (step > 1 && (!selectedProduct || selectedProduct.length === 0)) {
            Swal.fire({
                icon: 'warning',
                title: 'Produkt erforderlich',
                text: 'Bitte wählen Sie zuerst ein Produkt aus.',
                confirmButtonText: 'OK'
            });
            return;
        }

        showStep(step);
    }
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

 

<script>
    // Complete Checklist Form Script with Select2, Phases, SweetAlert, and AJAX Save

$(document).ready(function () {
    const checklistContainer = $('#checklist-container');

    // Initialize Select2 with images
    $('#product').select2({
        templateResult: formatProduct,
        templateSelection: formatProductSelection,
        escapeMarkup: function (m) { return m; }
    });

    function formatProduct(product) {
        if (!product.id) return product.text;
        const imageUrl = $(product.element).data('image');
        return '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
            '<span>' + product.text + '</span></span>';
    }

    function formatProductSelection(product) {
        if (!product.id) return product.text;
        const imageUrl = $(product.element).data('image');
        return '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
            '<span>' + product.text + '</span></span>';
    }

    $('#product').on('change', function () {
        const productId = $(this).val();
        if (!productId) {
            checklistContainer.html('');
            return;
        }

        checklistContainer.html('<p class="text-muted">Lade Checkliste...</p>');

        $.get(`/checklist/phase/${productId}`, function (data) {
            checklistContainer.html('');

            if (data.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keine Checkliste gefunden!',
                    html: 'Bitte <a href="/task_phase" target="_blank" class="text-primary">klicken Sie hier</a>, um Phasen zu erstellen.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            showStep(2);

            Swal.fire({
                icon: 'success',
                title: 'Checkliste geladen!',
                timer: 1200,
                showConfirmButton: false
            });

            const grouped = {};
            data.forEach(item => {
                if (!grouped[item.phase_name]) grouped[item.phase_name] = [];
                grouped[item.phase_name].push(item);
            });

           for (const phase in grouped) {
            const phaseId = phase.replace(/\s+/g, '-').toLowerCase();
            const phaseBlock = $(`<div class="mb-4" data-phase="${phaseId}"></div>`);

            const header = $(`
                <div class="d-flex align-items-center mb-2">
                    <h5 class="fw-bold text-success me-2 mb-0">${phase}</h5>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input phase-toggle" id="check_${phaseId}" name="phase_id">
                        <label class="form-check-label" for="check_${phaseId}">Alle auswählen</label>
                    </div>
                </div>
            `);

            phaseBlock.append(header);

            // ✅ RENDER ACTIVITY CHECKBOXES
            grouped[phase].forEach(activity => {
                const inputId = `activity_${activity.active_id}`;
                const isChecked = window.selectedActivities?.includes(activity.active_id); // ✔️ Use only activity ID

                const checkbox = $(`
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input phase-checkbox" 
                            data-phase="${phaseId}" data-phase-id="${activity.phase_id}" 
                            id="${inputId}" name="activities[]" value="${activity.active_id}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="${inputId}">${activity.title}</label>
                        <div class="ms-4 text-muted small">${activity.description ?? ''}</div>
                    </div>
                `);

                phaseBlock.append(checkbox);
            });

            // ✅ 🔽 PLACE THIS HERE — after all activities are appended
            checklistContainer.append(phaseBlock);

            const allCheckboxes = $(`.phase-checkbox[data-phase="${phaseId}"]`);
            const checkedBoxes = allCheckboxes.filter(':checked');

            if (allCheckboxes.length && allCheckboxes.length === checkedBoxes.length) {
                $(`#check_${phaseId}`).prop('checked', true);
            }
        }

        }).fail(function () {
            checklistContainer.html('<p class="text-danger">Fehler beim Laden der Checkliste.</p>');
            Swal.fire({
                icon: 'error',
                title: 'Fehler!',
                text: 'Beim Laden ist ein Fehler aufgetreten.'
            });
        });
    });

    // Toggle all activities per phase
    $('#checklist-container').on('change', '.phase-toggle', function () {
        const phaseId = $(this).attr('id').replace('check_', '');
        const isChecked = $(this).is(':checked');
        $(`.phase-checkbox[data-phase="${phaseId}"]`).prop('checked', isChecked);
    });

    // Auto-check phase toggle if any activity is selected
    $('#checklist-container').on('change', '.phase-checkbox', function () {
        const phaseId = $(this).data('phase');
        const all = $(`.phase-checkbox[data-phase="${phaseId}"]`);
        const checked = all.filter(':checked').length > 0;
        $(`#check_${phaseId}`).prop('checked', checked);
    });

    // Form submit with SweetAlert and AJAX
   $('.data-save').on('click', function (e) {
        e.preventDefault();

        const activities = [];
        $('.phase-checkbox:checked').each(function () {
            const activityId = $(this).val();
            const phaseId = $(this).data('phase-id');
            if (activityId && phaseId) {
                activities.push({ phase_id: phaseId, activity_id: activityId });
            }
        });

        const data = {
            _method: checklistMethod, // Laravel will interpret PUT if set here
            product_id: $('#product').val(),
            employee_id: 1, // Set dynamically if needed
            list_name: $('#list_name').val() || 'Meine Projekt-Checkliste',
            plan_montage: $('#toggleDiv').is(':checked') ? 1 : 0,
            supplier_section: $('#supplier_section').is(':checked') ? 1 : 0,
            cran_section: $('#cran_section').is(':checked') ? 1 : 0,
            old_facility: $('#old_facility').is(':checked') ? 1 : 0,
            photo_section: $('#photo_section').is(':checked') ? 1 : 0,
            activities: activities
        };

        $.ajax({
            url: checklistUpdateUrl,
            method: 'POST', // Laravel will spoof PUT via _method
            contentType: 'application/json',
            data: JSON.stringify(data),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Gespeichert!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '/checklists'; // ✅ Redirect after success
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let message = 'Bitte überprüfen Sie Ihre Eingaben.';
                    if (errors) {
                        message = Object.values(errors).map(err => err[0]).join('<br>');
                    }
                    Swal.fire('Fehler!', message, 'error');
                } else {
                    Swal.fire('Fehler!', 'Ein unerwarteter Fehler ist aufgetreten.', 'error');
                }
            }
        });
    });


    // Step show function
    window.showStep = function (step) {
        $('.step-content').addClass('d-none');
        $('#step-' + step).removeClass('d-none');
        const progress = step * 20;
        $('#progress-bar').css('width', progress + '%').text('Step ' + step + ' of 5');
        $('.nav-link').removeClass('active');
        $('#step-' + step + '-tab').addClass('active');
    };

    // Show first step initially
    showStep(1);
});

</script>


<script>
    $(document).ready(function () {
        // Pre-select the product
        $('#product').val('{{ $checklist->product_id }}').trigger('change');

        // Pre-check options
        if ({{ $checklist->plan_montage }}) $('#toggleDiv').prop('checked', true).trigger('change');
        if ({{ $checklist->supplier_section }}) $('#supplier_section').prop('checked', true);
        if ({{ $checklist->cran_section }}) $('#cran_section').prop('checked', true);
        if ({{ $checklist->old_facility }}) $('#old_facility').prop('checked', true);
        if ({{ $checklist->photo_section }}) $('#photo_section').prop('checked', true);

        $('#list_name').val(`{{ $checklist->list_name }}`);

        // Optional: loop through $checklist->phaseLists to pre-check activities
    });
</script>



@endsection
