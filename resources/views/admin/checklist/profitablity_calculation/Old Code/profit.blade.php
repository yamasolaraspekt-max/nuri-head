@extends('admin.layouts.app')
@section('title') WIRTSCHAFTLICHKEITSBERECHNUNG @endsection
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
                            <h2 class="content-header-title float-left mb-0">WIRTSCHAFTLICHKEITSBERECHNUNG</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ url('new_lead_view') }}">Kunde</a></li>
                                    <li class="breadcrumb-item"><a href="{{ url('/new_lead_profile/'.$customer->id) }}"> {{ $customer->name }} {{ $customer->lastname }}</a></li>
                                    <li class="breadcrumb-item">Liste</li>
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
                        <div class="col-9">
                            <form action=" ">
                                <fieldset>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Suchen..." aria-describedby="button-addon2">
                                        <div class="input-group-append" id="button-addon2">
                                            <button class="btn btn-primary" type="submit">Go</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <div class="col-3 d-flex">
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light"  data-toggle="modal" data-target="#new"><i class="feather icon-plus"></i></button>
                        
                            <div class="modal fade text-left" id="new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning white">
                                            <h5 class="modal-title" id="myModalLabel140">Neue Berechnung anlegen</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('customer.profit.save') }}">
                                            @csrf
                                            <div class="modal-body row">

                                                <!-- Titel -->
                                                <div class="col-md-12 mb-1">
                                                    <label for="title">Titel der Berechnung</label>
                                                    <input type="text" name="title" id="title" class="form-control" placeholder="z. B. PV + WP Simulation" required>
                                                </div>

                                                <div class="col-md-12 mb-1">
                                                    <label for="title">Produkt</label>
                                                    <select name="product_id" id="" class="form-control">
                                                        @foreach ($products as $product)
                                                            <option value="{{$product->id}}">{{$product->article_group}}</option> 
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <!-- Hidden Identifiers -->
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <input type="hidden" name="alternative_id" value="{{ request()->alternative_id }}"> 
                                                <input type="hidden" name="service_id" value="{{ request()->section_id }}">

                                                <!-- Confirmations -->
                                                <div class="col-md-12 mt-1">
                                                    <p class="text-muted">
                                                        Diese Berechnung wird für:<br>
                                                        <strong>Kunde:</strong> {{ $customer->name }} {{ $customer->lastname }}<br>
                                                        <strong>Produkt ID:</strong> {{ $customer->product_id }}<br>
                                                        <strong>Alternative ID:</strong> {{ request()->alternative_id }}<br>
                                                        <strong>Service ID:</strong> {{ request()->section_id }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div> 
                    </div>

                  
                    <!-- Step Content -->
                    <div class="row">
                   @php
                        // Define an array of available color classes for the icon
                        $colors = ['text-primary', 'text-success', 'text-warning', 'text-danger', 'text-info'];
                    @endphp

                    @foreach ($data as $item)
                        @php
                            // Select a random color class from the array
                            $randomColor = $colors[array_rand($colors)];
                        @endphp

                        <div class="col-xl-3 col-md-4 col-sm-6">
                            <div class="card text-center">
                                <div class="card-content">
                                    <div class="card-body">
                                        <a href="{{ url('customer_profit_report/'.$item->id.'/'.$item->product_id)}}">
                                            <div class="avatar bg-primary p-50 m-0 mb-1">
                                                <div class="avatar-content">
                                                    <!-- Apply the random color class to the folder icon -->
                                                 {{ $item->initial }}
                                                </div>
                                            </div>
                                            <p class="" style="color: black; font-size:12px;" >{{ $item->title }}</p>
                                        </a>
                                        <hr>
                                        <span> 
                                            <a type="button" class="btn btn-icon btn-flat-success mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#edit{{$item->id}}"><i class="feather icon-edit"></i></a>
                                            <a type="button" class="btn btn-icon btn-flat-danger mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#delete{{$item->id}}"><i class="feather icon-trash"></i></a> 
                                        </span>

                                        <div class="modal fade text-left" id="edit{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel140" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning white">
                                                        <h5 class="modal-title" id="myModalLabel140">BEARBEITEN ORDNER</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <form method="post" action="{{ route('customer.profit.edit')}}">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="col-xl-12 col-md-12 col-12 mb-1">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Titel</label>
                                                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                                    <input type="hidden" name="product_id" value="{{ $customer->product_id }}">
                                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                                    <input type="text" class="form-control" id="basicInput" value="{{ old('title', $item->title) }}" placeholder="Titel eingeben" name="title">
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary waves-effect waves-light"  >Speichern</button>
                                                            <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Stornieren</button>
                                                        </div>
                                                    </form> 
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade text-left" id="delete{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger white">
                                                        <h5 class="modal-title" id="myModalLabel120">Modal löschen</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Möchten Sie diesen Datensatz wirklich löschen?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a type="button" class="btn btn-danger waves-effect waves-light" href="{{ url('customer_profit_delete/'.$item->id) }}" >OK</a>
                                                        <button type="button" class="btn btn-success waves-effect waves-light" data-dismiss="modal">Absagen</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach 

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
    $(document).ready(function(){
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
  <!-- Product Drop down and Image -->
  <script>
     $(document).ready(function() {
        // Initialize Select2 with custom template for displaying images
        $('#product').select2({
            templateResult: formatProduct, // Custom formatting for dropdown list
            templateSelection: formatProductSelection, // Custom formatting for selected item
            escapeMarkup: function(m) { return m; } // Let Select2 handle HTML markup
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
                '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
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
                '<span><img src="' + imageUrl + '" class="img-thumbnail mr-2" style="width: 40px; height: 40px;" />' +
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


<script>
    // Function to show the current step and update the progress bar
    function showStep(step) {
        // Hide all step contents
        $('.step-content').addClass('d-none');
        
        // Show the selected step
        $('#step-' + step).removeClass('d-none');
        
        // Update the progress bar
        const progress = step * 20; // 5 steps, each step is 20% of the progress
        $('#progress-bar').css('width', progress + '%').text('Step ' + step + ' of 5');
        
        // Update the navigation active state
        $('.nav-link').removeClass('active');
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


@endsection
