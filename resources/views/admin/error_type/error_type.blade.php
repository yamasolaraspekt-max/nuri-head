@extends('admin.layouts.app')
@section('title') FEHLERHANDBUCH @endsection
@section('style') 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .quill-editor {
        height: 150px;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: .25rem;
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
                            <h2 class="content-header-title float-left mb-0">FEHLERHANDBUCH</h2>
                            <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li> 
                                    <li class="breadcrumb-item active"><a >Fehlertyp</a></li>
                                    
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>   
            </div> 
                          
            <div class="content-body"> 
                 <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body"> 
                                            <div class="row mb-3">
                                                <div class="col-md-9 col-12">
                                                    <form action="{{ action('App\Http\Controllers\ErrorController@index') }}" method="GET">
                                                        <div class="input-group">
                                                            <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-primary" type="submit">Go</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-md-3 col-12 mt-2 mt-md-0 text-md-right text-left">
                                                    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#default">Neue hinzufügen</button>
                                                </div>
                                            </div>

                                            <!-- Create Modal -->
                                         <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="createLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                <div class="modal-content"> 
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="createLabel">Neuer Fehlereintrag</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="errorForm" enctype="multipart/form-data">
                                                            @csrf 
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="error_code">Fehlercode</label>
                                                                        <input type="text" class="form-control" name="error_code">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="problem_types">Fehlerbeschreibung</label>
                                                                        <input type="text" class="form-control" name="problem_types" required>
                                                                        @if ($errors->has('problem_types'))
                                                                            <p class="text-danger">{{ $errors->first('problem_types') }}</p>
                                                                        @endif
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="product_id">Hersteller</label>
                                                                       <select name="brand_id" id="brand_id" class="form-control select2">
                                                                            <option disabled selected>Hersteller auswählen</option>
                                                                            @foreach ($brands as $br)
                                                                                <option value="{{ $br->id }}">{{ $br->name }}</option> 
                                                                            @endforeach
                                                                        </select>


                                                                        @if ($errors->has('product_id'))
                                                                            <p class="text-danger">{{ $errors->first('product_id') }}</p>
                                                                        @endif
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="product_id">Produkt</label>
                                                                       <select name="product_id" id="product_id" class="form-control select2">
                                                                            <option disabled selected>Produkt auswählen</option>
                                                                            @foreach ($product as $pr)
                                                                                <option value="{{ $pr->id }}">{{ $pr->article_group }}</option> 
                                                                            @endforeach
                                                                        </select>


                                                                        @if ($errors->has('product_id'))
                                                                            <p class="text-danger">{{ $errors->first('product_id') }}</p>
                                                                        @endif
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="article_name">Produktbezeichnung</label>
                                                                        <input type="text" name="article_name" class="form-control">
                                                                        @if ($errors->has('article_name'))
                                                                            <p class="text-danger">{{ $errors->first('article_name') }}</p>
                                                                        @endif
                                                                    </div>

                                                                      <div class="form-group">
                                                                        <label for="article_name">Serienummer</label>
                                                                        <input type="text" name="serial_no" class="form-control">
                                                                        @if ($errors->has('article_name'))
                                                                            <p class="text-danger">{{ $errors->first('article_name') }}</p>
                                                                        @endif
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="file">Datei hochladen (optional)</label>
                                                                        <input type="file" name="file" class="form-control-file">
                                                                    </div>

                                                                     <div class="form-group">
                                                                        <label for="reason">Links</label> 
                                                                        <input type="text" name="links" id="links">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="status">Status</label>
                                                                        <select name="status" class="form-control">
                                                                            <option value="">Status auswählen</option>
                                                                            <option value="Published" selected>Aktiv</option> 
                                                                            <option value="Unpublished">Inaktiv</option> 
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="reason">Fehlerursache</label>
                                                                        <div id="reason_editor" class="quill-editor"></div>
                                                                        <input type="hidden" name="reason" id="reason_input">
                                                                    </div>


                                                                     <div class="form-group">
                                                                        <label for="latest_update">Letzte Enderung</label>
                                                                     
                                                                        <input type="date" class="form-control" name="latest_update" id="latest_update">
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="solution">Lösung</label>
                                                                        <div id="solution_editor" class="quill-editor"></div>
                                                                        <input type="hidden" name="solution" id="solution_input">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                                                                <button type="submit" class="btn btn-primary">speichern</button>
                                                            </div>
                                                        </form>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>



                                            <!-- Table -->
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Fehlercode</th>
                                                            <th>Fehlerbeschreibung</th>
                                                            <th>Produktdetails</th>
                                                            <th>Fehlerursache</th>
                                                            <th>Lösung</th>
                                                            <th>Datei</th>
                                                            <th>Status</th>
                                                            <th>Aktionen</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($data as $item)
                                                            <tr>
                                                                <td>{{ $item->id }}</td>
                                                                <td>{{ $item->error_code }}</td>
                                                                <td>{{ $item->problem_types }}</td>
                                                                <td>
                                                                    <p>{{ $item->article_group }}</p>
                                                                    <small><p class="m-0"><i class="fa fa-building-o"></i> Hersteller: {{ $item->brand_name }}</p></small>
                                                                    <small><p class="m-0"><i class="feather icon-codepen"></i> Artikel: {{ $item->article_name }}</p></small>
                                                                    <small><p class="m-0"><i class="feather icon-hash"></i>{{ $item->serial_no }}</p></small>
                                                                </td>

                                                                {{-- Reason Modal Trigger --}}
                                                                <td>
                                                                    <button class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#reasonModal{{ $item->id }}">
                                                                        Ansehen
                                                                    </button>
                                                                </td>

                                                                {{-- Solution Modal Trigger --}}
                                                                <td>
                                                                    <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#solutionModal{{ $item->id }}">
                                                                        Ansehen
                                                                    </button>
                                                                </td>

                                                                <td>
                                                                    @if($item->file)
                                                                        @php
                                                                            $fileUrl = asset('public/uploads/errors/' . $item->file);
                                                                            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $item->file);
                                                                            $isPdf = preg_match('/\.pdf$/i', $item->file);
                                                                        @endphp

                                                                        @if($isImage)
                                                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-info">
                                                                                <i class="feather icon-image"></i> Bild
                                                                            </a>
                                                                        @elseif($isPdf)
                                                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-secondary">
                                                                                <i class="feather icon-file-text"></i> PDF
                                                                            </a>
                                                                        @else
                                                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-dark">
                                                                                <i class="feather icon-file"></i> Datei
                                                                            </a>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>


                                                                <td>{{ $item->status=='Published' ? 'Aktiv' : 'Inaktiv' }}</td>

                                                                <td class="d-flex flex-wrap">
                                                      
                                                                         <button class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                                                            <i class="feather icon-edit"></i>
                                                                        </button>
                                                             
                                                                        <button class="btn btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete-btn" data-id="{{ $item->id }}">
                                                                            <i class="feather icon-trash"></i>
                                                                        </button>



                                                                        @if($item->status === 'Published')
                                                                           <button onclick="toggleStatus({{ $item->id }}, 'Unpublished')" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1 waves-effect waves-light">
                                                                                <i class="feather icon-download"></i>  
                                                                            </button>

                                                                        @else
                                                                            <button onclick="toggleStatus({{ $item->id }}, 'Published')" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light">
                                                                                <i class="feather icon-upload"></i>
                                                                            </button>
                                                                        @endif 
                                                                    
                                                                </td>
                                                            </tr>

                                                            {{-- Reason Modal --}}
                                                            <div class="modal fade" id="reasonModal{{ $item->id }}" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Grund für Fehler ID: {{ $item->id }}</h5>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {!! $item->reason !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Solution Modal --}}
                                                            <div class="modal fade" id="solutionModal{{ $item->id }}" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Lösung für Fehler ID: {{ $item->id }}</h5>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            {!! $item->solution !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Delete Modal --}}
                                                            <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog">
                                                                <div class="modal-dialog" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Löschen bestätigen</h5>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            Möchten Sie den Fehler <strong>#{{ $item->id }}</strong> wirklich löschen?
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <a href="{{ route('error.destroy', $item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            {{-- Edit Modal (Minimal Example) --}}
                                                            <div class="modal fade text-left" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editLabel{{ $item->id }}" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title" id="editLabel{{ $item->id }}">Fehler bearbeiten (ID: {{ $item->id }})</h4>
                                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <form class="edit-error-form" data-id="{{ $item->id }}" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="id" value="{{ $item->id }}">

                                                                                <div class="row">
                                                                                    {{-- LEFT COLUMN --}}
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="error_code">Fehlercode</label>
                                                                                            <input type="text" class="form-control" name="error_code" value="{{ $item->error_code }}">
                                                                                        </div>

                                                                                        <div class="form-group">
                                                                                            <label for="problem_types">Problemfall</label>
                                                                                            <input type="text" class="form-control" name="problem_types" value="{{ $item->problem_types }}" required>
                                                                                            <small class="text-danger error" data-error="problem_types"></small>
                                                                                        </div>

                                                                                            <div class="form-group">
                                                                                                    <label for="product_id">Produkt</label>
                                                                                                <select name="product_id" id="product_id" class="form-control select2">
                                                                                                        <option disabled selected>Produkt auswählen</option>
                                                                                                        @foreach ($product as $pr)
                                                                                                            <option value="{{ $pr->id }}" @if($pr->id == $item->product_id) selected @endif>{{ $pr->article_group }}</option> 
                                                                                                        @endforeach
                                                                                                    </select>


                                                                                                    @if ($errors->has('product_id'))
                                                                                                        <p class="text-danger">{{ $errors->first('product_id') }}</p>
                                                                                                    @endif
                                                                                                </div>

                                                                                                <div class="form-group">
                                                                                                    <label for="article_name">Artikelname</label>
                                                                                                    <input type="text" name="article_name" class="form-control" value="{{ $item->article_name }}">
                                                                                                    @if ($errors->has('article_name'))
                                                                                                        <p class="text-danger">{{ $errors->first('article_name') }}</p>
                                                                                                    @endif
                                                                                                </div>

                                                                                        <div class="form-group">
                                                                                            <label for="file">Datei (neu hochladen, optional)</label>
                                                                                            <input type="file" name="file" class="form-control-file">
                                                                                            @if($item->file)
                                                                                                <small>Aktuell: 
                                                                                                    <a href="{{ asset('public/uploads/errors/' . $item->file) }}" target="_blank">{{ $item->file }}</a>
                                                                                                </small>
                                                                                            @endif
                                                                                        </div>

                                                                                        <div class="form-group">
                                                                                            <label for="status">Status</label>
                                                                                            <select name="status" class="form-control">
                                                                                                <option value="">Status auswählen</option>
                                                                                                <option value="Published" {{ $item->status == 'Published' ? 'selected' : '' }}>Aktiv</option>
                                                                                                <option value="Unpublished" {{ $item->status == 'Unpublished' ? 'selected' : '' }}>Inaktiv</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>

                                                                                    {{-- RIGHT COLUMN --}}
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="reason">Grund</label>
                                                                                            <div id="reason_editor_{{ $item->id }}" class="quill-editor">{!! $item->reason !!}</div>
                                                                                            <input type="hidden" name="reason" id="reason_input_{{ $item->id }}">
                                                                                        </div>

                                                                                        <div class="form-group">
                                                                                            <label for="solution">Lösung</label>
                                                                                            <div id="solution_editor_{{ $item->id }}" class="quill-editor">{!! $item->solution !!}</div>
                                                                                            <input type="hidden" name="solution" id="solution_input_{{ $item->id }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                                                                                    <button type="submit" class="btn btn-primary">speichern</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                        @empty
                                                            <tr><td colspan="7" class="text-center">Keine Daten gefunden.</td></tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination (if used with paginate) -->
                                            <div class="mt-2">
                                                {{ $data->links() }}
                                            </div>

                                        </div> <!-- end card-body -->
                                    </div> <!-- end card-content -->
                                </div> <!-- end card -->
                            </div> <!-- end col-12 -->
                        </div> <!-- end row -->  
            </div>
        </div>
    </div>
    <!-- END: Content-->
@endsection

@section('script')
<script>
    $(document).ready(function () {
    $('#product_id').select2({
        placeholder: 'Produkt auswählen',
        width: '100%',
        dropdownParent: $('#default') // important if used inside a modal
    });
});

</script>
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
  
<!-- Quill & AJAX -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    $(document).ready(function () {
        // 🖋️ Quill Editors Initialization (Create Form)
        const reasonEditor = new Quill('#reason_editor', { theme: 'snow' });
        const solutionEditor = new Quill('#solution_editor', { theme: 'snow' });

        // 🧾 File Size Check (2MB max)
        $('input[type="file"]').on('change', function () {
            const file = this.files[0];
            if (file && file.size > 2 * 1024 * 1024) {
                $(this).val('');
                Swal.fire({
                    icon: 'warning',
                    title: 'Datei zu groß!',
                    text: 'Die Datei darf nicht größer als 2 MB sein.'
                });
            }
        });

        // 💾 Create Form Submission
        $('#errorForm').on('submit', function (e) {
            e.preventDefault();
            $('.error').remove(); // Remove old errors

            // 🖋️ Sync Quill content
            $('#reason_input').val(reasonEditor.root.innerHTML);
            $('#solution_input').val(solutionEditor.root.innerHTML);

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('errors.store') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $('#submitErrorBtn').prop('disabled', true).text('Speichern...');
                },
                success: function (res) {
                    $('#submitErrorBtn').prop('disabled', false).text('Speichern');
                    $('#errorForm')[0].reset();
                    reasonEditor.setText('');
                    solutionEditor.setText('');
                    $('#default').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolg!',
                        text: 'Fehlereintrag wurde erfolgreich gespeichert.',
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    $('#submitErrorBtn').prop('disabled', false).text('Speichern');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const importantFields = ['error_code', 'problem_types', 'product_id'];
                        let alertMessages = [];

                        importantFields.forEach(field => {
                            if (errors[field]) {
                                alertMessages.push(errors[field][0]);
                            }
                        });

                        if (alertMessages.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validierungsfehler',
                                html: alertMessages.join('<br>')
                            });
                        } else {
                            let messages = '';
                            Object.keys(errors).forEach(key => {
                                messages += `${errors[key][0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehlerhafte Eingabe',
                                html: messages
                            });
                        }
                    } else {
                        Swal.fire('Fehler', 'Ein unerwarteter Fehler ist aufgetreten.', 'error');
                    }
                }
            });
        });

        // 🛠️ Quill Initialization for Edit Modals
        @foreach($data as $item)
            const reasonQuill{{ $item->id }} = new Quill('#reason_editor_{{ $item->id }}', { theme: 'snow' });
            const solutionQuill{{ $item->id }} = new Quill('#solution_editor_{{ $item->id }}', { theme: 'snow' });
        @endforeach

        // 📝 Edit Form Submission
        $('.edit-error-form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const id = form.data('id');

            // 🖋️ Sync Quill content
            $('#reason_input_' + id).val($('#reason_editor_' + id + ' .ql-editor').html());
            $('#solution_input_' + id).val($('#solution_editor_' + id + ' .ql-editor').html());

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('error.update') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    form.find('button[type="submit"]').prop('disabled', true).text('Speichern...');
                },
                success: function (res) {
                    form.find('button[type="submit"]').prop('disabled', false).text('Speichern');
                    Swal.fire({
                        icon: 'success',
                        title: 'Aktualisiert!',
                        text: 'Der Fehler wurde erfolgreich aktualisiert.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    form.find('button[type="submit"]').prop('disabled', false).text('Speichern');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const importantFields = ['error_code', 'problem_types', 'product_id'];
                        let alertMessages = [];

                        importantFields.forEach(field => {
                            if (errors[field]) {
                                alertMessages.push(errors[field][0]);
                            }
                        });

                        if (alertMessages.length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validierungsfehler',
                                html: alertMessages.join('<br>')
                            });
                        } else {
                            let messages = '';
                            Object.keys(errors).forEach(key => {
                                messages += `${errors[key][0]}<br>`;
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Fehlerhafte Eingabe',
                                html: messages
                            });
                        }
                    } else {
                        Swal.fire('Fehler', 'Etwas ist schief gelaufen.', 'error');
                    }
                }
            });
        });

        // 🚦 Status Toggle
        window.toggleStatus = function (id, status) {
            $.ajax({
                url: "{{ route('error.status') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    status: status
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status aktualisiert!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        Swal.fire('Fehler', 'Ungültiger Status oder fehlende ID.', 'error');
                    } else {
                        Swal.fire('Fehler', 'Unbekannter Fehler beim Statuswechsel.', 'error');
                    }
                }
            });
        }

        // 🗑️ Delete Handler
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Bist du sicher?',
                text: "Dieser Eintrag wird gelöscht!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen',
                confirmButtonColor: '#d33'
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/error/delete/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (res) {
                            Swal.fire('Gelöscht!', res.message, 'success').then(() => {
                                $(`button[data-id="${id}"]`).closest('tr').remove();
                            });
                        },
                        error: function () {
                            Swal.fire('Fehler!', 'Löschen fehlgeschlagen.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>


@endsection