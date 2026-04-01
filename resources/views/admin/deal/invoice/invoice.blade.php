@extends('admin.layouts.app')

@section('title') Rechnungen @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet"> 
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" rel="stylesheet">
<style>
    .dropzone {
        border: 2px dashed #8fc73e;
        border-radius: 20px;
        background: #fafafa;
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
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Rechnungen</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/deal_all_list') }}">Rechnungen</a></li>
                                 
                                </li>
                                <li class="breadcrumb-item active">
                                    @if(Route::currentRouteName() == 'deal.junk.list') 
                                    <a href="{{ route('deal.junk.list') }}">JUNK</a>
                                    @elseif(Route::currentRouteName() == 'deal.invoice') 
                                      ALLE 
                                    @elseif(Route::currentRouteName() == 'deal.delete.list') 
                                     <a href="{{ route('deal.delete.list') }}">GELÖSCHTE</a>
                                    @else Neue @endif
                                </li>

                                    
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="content-body">
                <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section -->  
                                        <!-- Search Section -->
                                        <div class="row" style="display: flex ;flex-direction: row-reverse !important;"> 
                                            <div class="col-1">
                                               <!-- Trigger button -->
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#dealModal">Erstellen</button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="dealModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <form method="POST" action="{{ route('deal.invoice.store') }}">
                                                            @csrf
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="dealModalLabel">Neue Rechnung erstellen</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <!-- Customer -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Kunde</label>
                                                                                <select name="customer_id" id="customer_id" class="form-control select2" required>
                                                                                    <option value="">-- Wähle Kunde --</option>
                                                                                    @foreach($customers as $cust)
                                                                                    <option value="{{ $cust->id }}">{{ $cust->name }} {{ $cust->lastname }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <!-- Product (dynamic) -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Produkt</label>
                                                                                <select name="lead_product_list_id" id="product_list_id" class="form-control select2" required>
                                                                                    <option value="">-- Wähle Produkt --</option>
                                                                                </select>
                                                                            </div>

                                                                            <!-- Invoice Number -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Rechnungsnummer</label>
                                                                                <input type="text" name="invoice_number" class="form-control" required>
                                                                            </div>

                                                                            <!-- Invoice Type -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Rechnungstyp</label>
                                                                                <select name="invoice_type" class="form-control" required>
                                                                                    <option value="">-- wählen --</option>
                                                                                    <option value="Abschlag">Abschlag</option>
                                                                                    <option value="Schlussrechnung">Schlussrechnung</option>
                                                                                    <option value="Teilrechnung">Teilrechnung</option>
                                                                                </select>
                                                                            </div>

                                                                            <!-- Invoice Amount -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Rechnungsbetrag (€)</label>
                                                                                <input type="number" name="invoice_amount" step="0.01" class="form-control" required>
                                                                            </div>

                                                                            <!-- Issued Date -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Ausgestellt am</label>
                                                                                <input type="date" name="issued_at" class="form-control" required>
                                                                            </div>

                                                                            <!-- Due Date -->
                                                                            <div class="col-md-6 mb-2">
                                                                                <label>Fällig am</label>
                                                                                <input type="date" name="due_date" class="form-control" required>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Hidden: All IDs -->
                                                                        <input type="hidden" name="deal_id" id="deal_id">
                                                                        <input type="hidden" name="product_id" id="product_id">
                                                                        <input type="hidden" name="alternative_id" id="alternative_id">
                                                                        <input type="hidden" name="department_id" id="department_id">
                                                                        <input type="hidden" name="service_id" id="service_id">
                                                                        <input type="hidden" name="employee_id" id="employee_id">
                                                                        <input type="hidden" name="service" id="service_str">
                                                                    </div>

                                                                    <div class="model-footer  mb-2 mr-1"> 
                                                                        <button type="button"   class="btn btn-danger float-right" data-dismiss="modal">abbrechen</button> 
                                                                        <button type="button" id="previewInvoiceBtn" class="btn btn-primary float-right mr-1">Vorschau & Erstellen</button> 
                                                                    </div>

                                                                </div>

                                                              

                                                            </form>
                                                        </div>
                                                    </div>


                                            </div> 
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        
                                        <section id="basic-tabs-components">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card overflow-hidden"> 
                                                        <div class="card-content">
                                                            <div class="card-body">
                                                                <ul class="nav nav-tabs" role="tablist">
                                                                    <li class="nav-item">
                                                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Kanban</a>
                                                                    </li>
                                                                    <li class="nav-item">
                                                                        <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Liste</a>
                                                                    </li>
                                                                    
                                                                </ul>
                                                                <div class="tab-content">
                                                                    <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                        <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                                            @include('admin.deal.invoice.partials.kanban')
                                                                        </div>

                                                                    </div>
                                                                    <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                                         @include('admin.deal.invoice.partials.list')

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
                    <!-- Table head options end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->




<!-- Sidebar -->
<div id="noteSidebar" class="note-sidebar">
    <div class="sidebar-header">
        <input type="text" id="searchNotes" placeholder="Suche Notizen..." class="form-control">
        <button id="closeSidebar" class="btn btn-sm btn-danger float-right">✕</button>
        
    </div>

    <div id="notesList" class="note-messages px-2">
        <!-- Notes will be dynamically loaded here -->
    </div>

    <div class="sidebar-footer p-2">
        <textarea id="newNoteContent" class="form-control" rows="2" placeholder="Neue Notiz schreiben..."></textarea>
        <button id="sendNote" class="btn btn-success btn-sm mt-2 w-100">Senden</button>
    </div>
</div>


   <!-- 📁 Upload Sidebar -->
<!-- Upload Sidebar -->
<div id="uploadSidebar" class="upload-sidebar">
    <div class="upload-header d-flex justify-content-between align-items-center p-2">
        <h5 class="mb-0">📤 Datei-Upload</h5>
        <button class="btn btn-sm btn-danger" id="closeUploadSidebar"><i class="fa fa-times"></i></button>
    </div>

    <form action="{{ url('customer_upload') }}" method="POST" class="dropzone dropzone-custom" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="customer_id" id="uploadCustomerId">
        <input type="hidden" name="alternative_id" id="uploadAlternativeId">
        <input type="hidden" name="product_id" id="uploadProductId">
        <input type="hidden" name="status" value="deal">
        <input type="hidden" name="stage_id" id="uploadStage">

        <div class="p-2">
            <label for="uploadStage">Stufe:</label>
            <select class="form-control form-control-sm" name="stage_id">
                <option value="">-- wählen --</option>
                <option value="order">Kundenauftrag</option>
                <option value="confirmed_order">Auftragsbestätigung</option>
                <option value="offer">Angebot</option>
            </select>
        </div>
    </form>

    <div class="p-2">
        <label for="filterStage">Galerie filtern:</label>
        <select id="filterStage" class="form-control form-control-sm mb-2">
            <option value="">Alle</option>
            <option value="order">Kundenauftrag</option>
            <option value="confirmed_order">Auftragsbestätigung</option>
            <option value="offer">Angebot</option>
        </select>
    </div>

    <div class="upload-gallery px-2"></div>
</div>

<!-- Backdrop -->
<div id="uploadBackdrop"></div>

 


@endsection
 
@section('script')
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
 
<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });

    // 🔄 Load product list for selected customer
    $('#customer_id').on('change', function () {
        let customerId = $(this).val();
        $('#product_list_id').html('<option>-- Lade Produkte --</option>');

        $.get(`/get-product-lists/${customerId}`, function (data) {
            $('#product_list_id').html('<option value="">-- Wähle Produkt --</option>');
            data.forEach(d => {
                $('#product_list_id').append(`
                    <option value="${d.id}"
                        data-product="${d.product_id}"
                        data-alternative="${d.alternative_id}"
                        data-department="${d.department_id ?? ''}"
                        data-service="${d.service_id ?? ''}"
                        data-employee="${d.employee_id ?? ''}"
                        data-service-str="${d.service ?? ''}">
                        ${d.article_group}
                    </option>
                `);
            });
        });
    });

    // 🧠 Update hidden fields + lookup actual deal_id
    $('#product_list_id').on('change', function () {
        let selected = $(this).find('option:selected');

        $('#product_id').val(selected.data('product'));
        $('#alternative_id').val(selected.data('alternative'));
        $('#department_id').val(selected.data('department'));
        $('#service_id').val(selected.data('service'));
        $('#employee_id').val(selected.data('employee'));
        $('#service_str').val(selected.data('service-str'));

        // 🔍 Find real deal_id
        $.get('/get-deal-id', {
            customer_id: $('#customer_id').val(),
            product_id: selected.data('product'),
            alternative_id: selected.data('alternative'),
            service: selected.data('service-str')
        }, function (res) {
            if (res.success && res.deal_id) {
                $('#deal_id').val(res.deal_id);
            } else {
                $('#deal_id').val('');
                Swal.fire('Fehler', 'Kein zugehöriger Auftrag gefunden.', 'warning');
            }
        });
    });

    // 💾 Invoice preview + submission
    $('#previewInvoiceBtn').on('click', function () {
        const issued = $('input[name="issued_at"]').val();
        const due = $('input[name="due_date"]').val();

        // Optional client-side date check
        if (new Date(due) < new Date(issued)) {
            Swal.fire('Fehler', 'Das Fälligkeitsdatum muss nach dem Ausstellungsdatum liegen.', 'error');
            return;
        }

        const form = $('#dealModal form');
        const formData = form.serialize();

        Swal.fire({
            title: 'Rechnung prüfen',
            html: `
                <strong>Kunde:</strong> ${$('#customer_id option:selected').text()}<br>
                <strong>Produkt:</strong> ${$('#product_list_id option:selected').text()}<br>
                <strong>Rechnungsnummer:</strong> ${$('input[name="invoice_number"]').val()}<br>
                <strong>Typ:</strong> ${$('select[name="invoice_type"]').val()}<br>
                <strong>Betrag:</strong> ${$('input[name="invoice_amount"]').val()} €<br>
                <strong>Ausgestellt am:</strong> ${issued}<br>
                <strong>Fällig am:</strong> ${due}
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        Swal.fire('Erfolgreich!', response.msg, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul>';
                        for (const field in errors) {
                            errors[field].forEach(msg => {
                                errorHtml += `<li>${msg}</li>`;
                            });
                        }
                        errorHtml += '</ul>';

                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler beim Speichern',
                            html: errorHtml
                        });
                    }
                });
            }
        });
    });
});
</script>

@endsection
