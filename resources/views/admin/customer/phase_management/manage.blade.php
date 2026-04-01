@extends('admin.layouts.app')

@section('title')
   Phasenmanagement
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/checklist.css')}}">  -->


    <style>
        .app-content {
            padding: 20px;
        }

        .sidebar-left {
            border-right: 1px solid #ddd;
        }

        .todo-item {
            cursor: pointer;
        }

        .todo-item:hover {
            background-color: #f8f9fa;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #999;
        }

        .select2-selection {
            border: 0px !important;
        }

        .img-flag {
            width: 32px !important;
            height: 32px !important;
            border-radius: 50% !important;
        }

        .hidden {
            display: none;
        }

        .table-hover-animation thead th {
            background-color: #fff0 !important;
        }
        .table-hover-animation tbody tr {
            background-color: #fff0 !important;
        }
        tr {
            border-bottom: 1px solid #d8d6d6 !important;
        }
     .select2-container {
            width: 100% !important;
            font-size: 25px !important
        }
    </style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Phasenmanagement</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('customer_product_create/'.request()->customer.'/'.request()->postcode.'/'.request()->address_no) }}">Prudukt</a></li>
                            <li class="breadcrumb-item"><a href="">Phase</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <div class="container">
                    <div class="row match-height">
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <div class="card collapse-icon accordion-icon-rotate">
                                <div class="card-body">
                                   <div class="row">
                                         <div class="col-md-8">
                                            <div class="row match-height">
                                                <input type="hidden" name="product" value="{{$productList->product_id}}">
                                                <input type="hidden" name="customer" value="{{$customer->id}}">
                                                <input type="hidden" name="alternative" value="{{$customer->alt_id}}">
                                                <input type="hidden" name="service" value="{{$productList->service}}">
                                                <div class="col-12">
                                                    <div class="form-group row"> 
                                                        <div class="col-md-6">
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="d-inline-block mr-1">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type1"
                                                                                @if($customer->customer_type=="privat") checked @endif value="privat">
                                                                            <label class="custom-control-label" for="customer_type1">privat</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type2" value="Gewerbe"
                                                                                @if($customer->customer_type=="Gewerbe") checked @endif>
                                                                            <label class="custom-control-label" for="customer_type2">Gewerbe</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                                <li class="d-inline-block mr-2">
                                                                    <fieldset>
                                                                        <div class="custom-control custom-radio">
                                                                            <input type="radio" class="custom-control-input" name="customer_type" id="customer_type3" value="Kummune"
                                                                                @if($customer->customer_type=="Kummune") checked @endif>
                                                                            <label class="custom-control-label" for="customer_type3">Kummune</label>
                                                                        </div>
                                                                    </fieldset>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>Firma</span>
                                                        </div>
                                                        <div class="col-md-10 textbox-container empty">
                                                            <input type="text" id="first-name" class="form-control textbox" value="{{ $customer->firma }}" name="firma" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div> 
                                            
                                                <div class="col-12">
                                                    <div class="form-group row">
                                                        <div class="col-md-2">
                                                            <span>Name</span>
                                                        </div>
                                                        <div class="col-md-10 textbox-container empty">
                                                            <input type="text" class="form-control textbox" value="{{ $customer->title }}. {{ $customer->name }} {{ $customer->lastname }}" name="lastname" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Straße / Nr.</span>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox" name="street" value="{{ $customer->street }}" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>PLZ / Ort</span>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" class="form-control textbox" value="{{ $customer->postcode }} {{ $customer->city }}" name="postcode" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>Tel</span>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="text" id="contact-info" class="form-control textbox" value="{{ $customer->phone }}" name="phone" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group row">
                                                        <div class="col-md-4">
                                                            <span>E-Mail</span>
                                                        </div>
                                                        <div class="col-md-8 textbox-container empty">
                                                            <input type="email" id="contact-info" class="form-control textbox" name="email" value="{{ $customer->email }}" readonly>
                                                            <div class="indicator"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row ml-2" style=" background: #cfe09b; border-radius: 57px;">
                                                <div class="col-md-4">
                                                    <img src="{{ asset('images/articles/'.$productList->image) }}" alt="{{ $productList->article_group }}"
                                                        style="width: 100px !important;" class="float-left mt-1">
                                                </div>

                                                <div class="col-md-8">
                                                    <h2 class="card-title mt-1 mb-0 white title">{{ $productList->article_group }}</h2> 
                                                    <p class="card-text white mb-1"> 
                                                        Aktualler Status: 
                                                        <span id="">
                                                            Interessiert
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="center mt-2" style="    font-size: 20px; font-weight: bold;    justify-self: center;">
                                            
                                               <a href="{{ url('project_details') }}" class="btn btn-flat-success mr-1 mb-1 waves-effect waves-light"> 
                                                  <i class="feather icon-arrow-left"></i> zurück zur Übersicht
                                                </a> 
                                            </div> 
                                        </div>

                                   </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="cards">
                                <div class="card-header" style="background: #cfe09b; color: white;"><h2 style=" color: white;">Verfügbare Phasen der <strong>{{ $productList->article_group }}</strong></h2></div>
                                <form id="storeForm" method="POST">
                                    @csrf
                                     <input type="hidden" name="product" value="{{$productList->product_id}}">
                                    <input type="hidden" name="customer" value="{{$customer->id}}">
                                    <input type="hidden" name="alternative" value="{{$customer->alt_id}}">
                                    <input type="hidden" name="service" value="{{$productList->service}}">
                                    <select id="phaseSelect" class="select2 form-control" multiple style="width:100%;" name="phase_id[]">
                                        <!-- Options will be populated here by AJAX -->
                                    </select>
                                    <button type="submit" class="btn btn-primary mb-2 mt-1 float-right">Speichern</button>
                                </form> 
                            </div>
                        </div>
                        <hr>
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <hr>
                        <section> 
                            <div class="table-responsive ml-2">
                               <table id="dataTable" class="table">
                                    <thead>
                                        <tr style="background: #cfe09b;">
                                            <th>ID</th>
                                            <th>Phasenname</th>
                                            <th>Farbe</th>
                                            <th>Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <div class="modal fade" id="colorModal" tabindex="-1" role="dialog" aria-labelledby="colorModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning white">
                                                <h5 class="modal-title" id="colorModalLabel">Update Color</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" id="colorItemId">
                                                <label for="colorInput">Choose the Color</label>
                                                <input type="color" id="colorInput" class="form-control">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Schließen</button>
                                                <button type="button" class="btn btn-primary" onclick="saveColorChange()">Speichern</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card-footer" style="    background: transparent;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="float-left"> 
                                                <div class="alert alert-warning mb-2" role="alert">
                                                <i class="feather icon-info"></i> <strong>Achtung!</strong> Stellen Sie sicher, dass Sie alle Phasen für das Projektmanagement hinzufügen
                                                </div>
                                            </div>
                                            <div class="float-right">
                                                <button type="button" class="btn btn-outline-primary round mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#next"><i class="feather icon-arrow-right" ></i> Nächste</button> 
                                                    
                                                <div class="modal fade text-left" id="next" tabindex="-1" role="dialog" aria-labelledby="myModalLabel19" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel19">Phasenmanagement</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                               Akzeptieren Sie die aktuellen Einstellungen
                                                            </div>
                                                            <div class="modal-footer">
                                                              
                                                                <a type="button" class="btn btn-primary waves-effect waves-light" href="{{ url('project_details') }}" >Ja</a>
                                                            
                                                                <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Nein</button>
                                                            </div>
                                                        </div>
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
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
    <script>
       $(document).ready(function() {
            // Display validation errors and messages
            @if($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif
            @if(Session::has('update_msg'))
                toastr.success("{{ session('update_msg') }}");
            @endif
            @if(Session::has('save_msg'))
                toastr.success("{{ session('save_msg') }}");
            @endif
            @if(Session::has('delete_msg'))
                toastr.error("{{ session('delete_msg') }}");
            @endif

            // Initialize Select2
            function initializeSelect2() {
                $('select').select2({
                    width: '100%',
                    placeholder: 'Wählen Sie eine Option',
                    allowClear: true,
                });
            }

            // Call initializeSelect2 initially to apply to all selects
            initializeSelect2();

            // Handle the display of the Save dialog
            document.querySelectorAll('button[id^="save-btn-"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const taskId = this.id.split('save-btn-')[1];
                    const dialogRow = document.getElementById(`saveDialog${taskId}`);
                    if (dialogRow) {
                        dialogRow.style.display = dialogRow.style.display === 'none' ? 'table-row' : 'none';
                        initializeSelect2(); // Re-initialize Select2 for newly shown elements
                    }
                });
            });

            // Handle the display of the Update dialog
            document.querySelectorAll('button[id^="update-btn-"]').forEach(function(button) {
                button.addEventListener('click', function() {
                    const taskId = this.id.split('update-btn-')[1];
                    const UpdateRow = document.getElementById(`updateDialog${taskId}`);
                    if (UpdateRow) {
                        UpdateRow.style.display = UpdateRow.style.display === 'none' ? 'table-row' : 'none';
                        initializeSelect2(); // Re-initialize Select2 for newly shown elements
                    }
                });
            });

            // Listen for dynamic content to be displayed and re-initialize Select2
            $('tr[id^="saveDialog"], tr[id^="updateDialog"]').on('show', function() {
                initializeSelect2();
            });
        });

        
    </script>
  
 <script>
 $(document).ready(function() {
    // Load dropdown and table data initially
    getPhaseOptions();
    getPhaseData();

    function getPhaseOptions() {
        const customer = $('input[name="customer"]').val();
        const product = $('input[name="product"]').val();
        const service = $('input[name="service"]').val();
        const alternative = $('input[name="alternative"]').val();

        $.ajax({
            url: `/customer_phase_get_new/${customer}/${product}/${service}/${alternative}`,
            method: 'GET',
            success: function(data) {
                let options = '';
                data.forEach(function(item) {
                    options += `<option value="${item.id}">${item.phase_name}</option>`;
                });
                $('#phaseSelect').html(options);
            },
            error: function(error) {
                console.error(error);
                alert('Failed to load phases');
            }
        });
    }


    $('#storeForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("customer.phase.management.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#storeButton').prop('disabled', true);
            },
            success: function(response) {
                toastr.success('Data saved successfully');
                $('#storeForm')[0].reset();
                getPhaseData(); // Refresh table data
                getPhaseOptions(); // Update dropdown to exclude saved data
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, errorMessages) {
                        if (Array.isArray(errorMessages)) {
                            errorMessages.forEach(function(message) {
                                toastr.error(message);
                            });
                        } else {
                            toastr.error(errorMessages);
                        }
                    });
                } else {
                    toastr.error('Failed to save data due to an unexpected error.');
                    console.error(xhr);
                }
            },
            complete: function() {
                $('#storeButton').prop('disabled', false);
            }
        });
    });

   function getPhaseData() {
        const customer = $('input[name="customer"]').val();
        const product = $('input[name="product"]').val();
        const service = $('input[name="service"]').val();
        const alternative = $('input[name="alternative"]').val();

        // Ensure all required inputs have values before making the request
        if (!customer || !product || !service || !alternative) {
            console.error("Required input values are missing.");
            return;
        }

        $.ajax({
            url: `/customer_phase_get/${customer}/${product}/${service}/${alternative}`,
            method: 'GET',
            beforeSend: function () {
                $('#dataTable tbody').html('<tr><td colspan="6">Loading...</td></tr>');
            },
            success: function (data) {
                let tableBody = '';

                if (data.length === 0) {
                    tableBody = '<tr><td colspan="6">No data available</td></tr>';
                } else {
                    data.forEach(function (item) {
                        tableBody += `
                            <tr>
                                <td>${item.id}</td> 
                                <td>${item.phase_name}</td>
                                <td>
                                    <a href="#" class="color-chip" data-id="${item.id}" data-color="${item.color}">
                                        <div class="chip-body" style="background: ${item.color}; color: white;">
                                            <span class="chip-text">${item.color}</span>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-outline-danger" onclick="deletePhase(${item.id})">Löschen</button>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#dataTable tbody').html(tableBody);
            },
            error: function (error) {
                console.error(error);
                $('#dataTable tbody').html('<tr><td colspan="6">Failed to load data</td></tr>');
            }
        });
    }


    window.deletePhase = function(id) {
        Swal.fire({
            title: 'Sind Sie sicher?',
            text: "Sie können dies nicht rückgängig machen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer_phase_management_delete/${id}`,
                    method: 'Get',
                    data: {
                        "_method": "DELETE",
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire(
                            'Gelöscht!',
                            'Der Datensatz wurde gelöscht.',
                            'success'
                        );
                        getPhaseData(); // Refresh table data
                        getPhaseOptions(); // Update dropdown
                    },
                    error: function(error) {
                        console.error(error);
                        Swal.fire(
                            'Fehlgeschlagen!',
                            'Der Datensatz konnte nicht gelöscht werden.',
                            'error'
                        );
                    }
                });
            }
        });
    };

    $(document).on('click', '.color-chip', function(e) {
        e.preventDefault();
        const itemId = $(this).data('id');
        const itemColor = $(this).data('color');
        $('#colorItemId').val(itemId);
        $('#colorInput').val(itemColor);
        $('#colorModal').modal('show');
    });

    window.saveColorChange = function() {
        const itemId = $('#colorItemId').val();
        const newColor = $('#colorInput').val();

        $.ajax({
            url: '{{ route("customer.phase.management.color") }}',
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": itemId,
                "color": newColor
            },
            success: function(response) {
                toastr.success('Color updated successfully');
                $('#colorModal').modal('hide');
                getPhaseData();
                getPhaseOptions();
            },
            error: function(error) {
                console.error(error);
                alert('Failed to update color');
            }
        });
    };
});


</script>


 



 
 

 
 
@endsection
