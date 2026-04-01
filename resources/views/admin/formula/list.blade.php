@extends('admin.layouts.app')

@section('title') Formulaliste @stop

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}">
 <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/customer_product.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <style>
    .field-group { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
    .field-group label { font-weight: bold; }
    .tab-content > .tab-pane:not(.active) { display: none; }
    .json-output { white-space: pre-wrap; background: #f8f9fa; padding: 10px; border: 1px solid #ccc; margin-top: 20px; }
    #formPreview input, #formPreview select, #formPreview textarea { margin-bottom: 10px; }
    .hidden-field { display: none !important; }
    .multi-group-wrapper .d-flex input { flex: 1; }
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
                        <h2 class="content-header-title float-left mb-0">FORMULARE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Liste</li>  
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="content-body">  
                    
                <div class="card-body">
                    <div class="col-9">
                        <form action="{{ route('product.formula.index') }}" method="GET" class="mb-2">
                            <fieldset>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Suche nach Bereich oder Produkt"
                                        value="{{ request('search') }}" aria-describedby="button-addon2">
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary" type="submit">Suchen</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>

                    </div>

                    <div class="col-md-3 float-right">
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal" data-target="#default">
                                Erstellen
                            </button>
                            <!-- Modal -->
                            <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form id="formulaSelectForm" method="post" action="{{ route('product.formula.store') }}" class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body"> 
                                                <fieldset>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Artikel Gruppe</label> 
                                                                <select name="product_id" id="product_id" class="form-control select2" style="width: 100%">
                                                                    <option value=""></option>
                                                                    @foreach ($products as $product)
                                                                        <option value="{{ $product->id }}"> {{ $product->article_group }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <p id="productError" style="color:red;display:none;">Bitte wählen Sie ein Produkt.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <div id="formPreviewContainer" class="mt-3"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" onclick="submitFormulaData()" class="btn btn-primary">Einreichen</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th> 
                                <th>Artikel Gruppe</th>
                                <th>Formulare</th>
                                <th>Status</th>
                                <th>Aktion</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="bg-white cursor-pointer" data-toggle="collapse" data-target="#product-{{ $product->id }}">
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->article_group }}</td>
                                    <td colspan="3"><strong>anzeigen / verbergen</strong></td>
                                </tr>
                                <tr id="product-{{ $product->id }}" class="collapse">
                                    <td colspan="5">
                                        @if($product->formulas->count())
                                            <ul class="list-group">
                                                @foreach ($product->formulas as $form)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $form->section_name }}</strong><br>
                                                            <small>Erstellt am: {{ $form->created_at->format('d.m.Y H:i') }}</small> <br>
                                                            <small>
                                                                🟢 <strong>Erstellt von:</strong> {{ $form->creator->name ?? 'Unbekannt' }}<br>
                                                                🟡 <strong>Bearbeitet von:</strong> {{ $form->editor->name ?? 'Nie bearbeitet' }}<br>
                                                                @if($form->deleter)
                                                                    🔴 <strong>Gelöscht von:</strong> <span class="text-danger">{{ $form->deleter->name }}</span><br>
                                                                @endif
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <a href="{{ url('edit/product-formula/'.$form->id.'/'.$form->product_id) }}" class="btn btn-sm btn-primary">Bearbeiten</a>
                                                            <a href="{{ url('/product-formula/'.$form->id.'/test') }}" class="btn btn-sm btn-warning">Testen</a>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $form->id }})">Löschen</button>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">Keine Formeln vorhanden für dieses Produkt.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->
@endsection
 
@section('script')
<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script> 
<script src="{{asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    $('#product_id').select2({
        placeholder: "Produkt auswählen", // Optional: Add your placeholder
        allowClear: true
    });
});

</script>
<script>
$('#product_id').select2({
    placeholder: 'Wählen Sie ein Produkt',
    allowClear: true
});

function submitFormulaData() {
    const productId = $('#product_id').val();
    if (!productId) {
        $('#productError').show();
        return;
    }

    $('#productError').hide();

    fetch(`/product-formula/store`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            section_name: 'Unbenannte Checkliste',
            fields: JSON.stringify([]) // Empty checklist
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.success && response.id) {
            window.location.href = `/product-formula/${response.id}/${response.product_id}/edit`;
        } else {
            Swal.fire('Fehler', 'Formel konnte nicht gespeichert werden.', 'error');
        }
    })
    .catch(() => {
        Swal.fire('Fehler', 'Serverfehler beim Speichern.', 'error');
    });
}
</script>

<script>
    function confirmDelete(id) {
    Swal.fire({
        title: 'Achtung!',
        text: 'Diese Aktion ist endgültig und löscht alle zugehörigen Daten. Bitte geben Sie Ihr Passwort zur Bestätigung ein.',
        icon: 'warning',
        input: 'password',
        inputLabel: 'Passwort eingeben',
        inputPlaceholder: 'Ihr Passwort...',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen!',
        cancelButtonText: 'Abbrechen',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            if (!password) {
                Swal.showValidationMessage('Passwort erforderlich!');
                return false;
            }
            return fetch(`/product-formula/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ password: password })
            }).then(response => {
                if (!response.ok) throw new Error('Löschen fehlgeschlagen');
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage(`Fehler: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire('Gelöscht!', 'Das Formular wurde erfolgreich gelöscht.', 'success')
                .then(() => location.reload());
        }
    });
}

</script>


@endsection
