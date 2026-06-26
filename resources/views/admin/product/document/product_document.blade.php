@extends('admin.layouts.app')
@section('title') Produkt Documents @stop

@section('style')
<style>
  /* Bigger PDF modal (Bootstrap 4 compatible) */
  .modal-dialog.modal-pdf-xl {
    max-width: 96vw;
    width: 96vw;
    margin: 1.25rem auto;
  }
  .modal-content.modal-pdf-content {
    height: 92vh;
    display: flex;
    flex-direction: column;
  }
  .modal-body.modal-pdf-body {
    flex: 1 1 auto;
    overflow: hidden;
  }
  .pdf-frame {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
  }

  /* Upload footer buttons - consistent position */
  .doc-actions {
    display: flex;
    justify-content: flex-end;
    gap: .5rem;
    flex-wrap: wrap;
    padding-top: .75rem;
    border-top: 1px solid rgba(0,0,0,.08);
    margin-top: 1rem;
  }

  /* Small helper */
  .table td, .table th { vertical-align: middle; }
</style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>

  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Produkt Documents</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/product') }}">Liste</a></li>
                <li class="breadcrumb-item">
                  <a href="{{ url('/product_details/'.request()->id) }}">
                    {{ $data->product }} - {{ $data->model }}
                  </a>
                </li>
                <li class="breadcrumb-item active">Produkt Documents</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">

      {{-- Top actions --}}
      <div class="row">
        <div class="col-12 mb-2">
          <div class="d-flex flex-wrap align-items-center">
            <form action="" method="GET" class="form-inline flex-grow-1 mr-2 mb-2">
              <div class="input-group w-100">
                <input type="text"
                       class="form-control"
                       placeholder="Geben Sie die Details Ihrer Suche ein"
                       name="search"
                       value="{{ request('search') }}"
                       aria-describedby="button-addon2">

                <div class="input-group-append">
                  <button class="btn btn-primary waves-effect waves-light" type="submit">
                    <i class="feather icon-search"></i>
                  </button>
                </div>
              </div>
            </form>

            <a href="{{ url('/product_details/'.request()->id) }}" class="btn btn-outline-warning mb-2">
              <i class="feather icon-chevrons-left"></i> Zurück
            </a>
          </div>
        </div>
      </div>

      {{-- Main card --}}
      <div class="row" id="table-hover-animation">
        <div class="col-12">
          <div class="card">
            <div class="card-content">
              <div class="card-body">

                {{-- Errors --}}
                @if($errors->any())
                  <div class="alert alert-danger">
                    <ul class="mb-0">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                {{-- ===================== CREATE / UPLOAD ===================== --}}
                <form novalidate
                      action="{{ route('product.create.document', ['id'=>request()->id]) }}"
                      method="post"
                      enctype="multipart/form-data"
                      class="custom-file-upload">
                  @csrf

                  <div class="table-responsive">
                    <table class="table" id="add_department">
                      <thead>
                        <tr>
                          <th>Hersteller</th>
                          <th>Art.-Nr.</th>
                          <th>Dokumentenname</th>
                          <th>Datei</th>
                          <th style="width:60px;"></th>
                        </tr>
                      </thead>

                      <tbody id="add_department_body">
                        <tr>
                          <input type="hidden" name="product[0][product_id]" value="{{ $data->id }}">

                          <td>
                            <input type="text" class="form-control" disabled value="{{ $brand->name }}">
                          </td>

                          <td>
                            <input type="text" class="form-control" disabled value="{{ $data->product }} - {{ $data->model }}">
                          </td>

                          <td>
                            <input type="text" class="form-control" name="product[0][title]" placeholder="Title of Document">
                          </td>

                          <td>
                            <input type="file" class="form-control" name="product[0][document]">
                          </td>

                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="doc-actions">
                    <button type="button" class="btn btn-outline-primary" id="add_brand">
                      <i class="feather icon-plus"></i> Hinzufügen
                    </button>

                    <button type="submit" class="btn btn-success">
                      <i class="feather icon-save"></i> Speichern
                    </button>
                  </div>
                </form>

                <hr>

                {{-- ===================== LIST ===================== --}}
                <div class="table-responsive">
                  <table class="table" id="brand_table">
                    <thead>
                      <tr>
                        <th>Hersteller</th>
                        <th>Art.-Nr.</th>
                        <th>Dokumentenname</th>
                        <th>Datei</th>
                        <th>Aktionen</th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach($description as $desk)
                        <tr>
                          <td>{{ $brand->name }}</td>
                          <td>{{ $desk->product }} - {{ $desk->model }}</td>
                          <td>{{ $desk->title }}</td>

                          <td>
                            <button type="button"
                                    class="btn btn-icon rounded-circle btn-success mr-1 mb-1 waves-effect waves-light"
                                    data-toggle="modal"
                                    data-target="#pdf{{ $desk->id }}"
                                    title="PDF öffnen">
                              <i class="fa fa-file-pdf-o"></i>
                            </button>

                            <a class="btn btn-icon rounded-circle btn-outline-secondary mr-1 mb-1"
                               href="{{ url('images/products/document/'.$desk->document) }}"
                               target="_blank"
                               title="In neuem Tab öffnen">
                              <i class="feather icon-external-link"></i>
                            </a>

                            {{-- BIG PDF MODAL --}}
                            <div class="modal fade text-left" id="pdf{{ $desk->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered modal-pdf-xl" role="document">
                                <div class="modal-content modal-pdf-content">

                                  <div class="modal-header">
                                    <h5 class="modal-title mb-0">
                                      PDF: {{ $desk->title }}
                                    </h5>

                                    <div class="d-flex align-items-center">
                                      <a class="btn btn-sm btn-outline-secondary mr-1"
                                         href="{{ url('images/products/document/'.$desk->document) }}"
                                         target="_blank">
                                        <i class="feather icon-external-link"></i> Tab
                                      </a>

                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                  </div>

                                  <div class="modal-body modal-pdf-body">
                                    <div class="row">
                                      <div class="col-12 mb-1">
                                        <label class="mb-25">Title</label>
                                        <input type="text" class="form-control" disabled value="{{ $desk->title }}">
                                      </div>

                                      <div class="col-12" style="height: calc(92vh - 170px);">
                                        <iframe class="pdf-frame"
                                                src="{{ url('images/products/document/'.$desk->document) }}">
                                        </iframe>
                                      </div>
                                    </div>
                                  </div>

                                  <div class="modal-footer">
                                    <button type="button" data-dismiss="modal" class="btn btn-primary">
                                      Schließen
                                    </button>
                                  </div>

                                </div>
                              </div>
                            </div>
                          </td>

                          <td>
                            <a href="{{ route('product.document.destroy',['id'=>$desk->id]) }}"
                               class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"
                               title="Löschen">
                              <i class="feather icon-trash-2"></i>
                            </a>

                            <button type="button"
                                    class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"
                                    data-toggle="modal"
                                    data-target="#edit{{ $desk->id }}"
                                    title="Bearbeiten">
                              <i class="feather icon-edit"></i>
                            </button>

                            {{-- EDIT MODAL --}}
                            <div class="modal fade text-left" id="edit{{ $desk->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">

                                  <div class="modal-header">
                                    <h5 class="modal-title mb-0">Dokument bearbeiten</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>

                                  <form action="{{ action('App\Http\Controllers\ProductDocumentsController@update') }}"
                                        method="post"
                                        enctype="multipart/form-data"
                                        class="custom-file-upload">
                                    @csrf

                                    <div class="modal-body">
                                      <input type="hidden" name="id" value="{{ $desk->id }}">
                                      <input type="hidden" name="product_id" value="{{ $desk->product_id }}">

                                      <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="title" value="{{ $desk->title }}" required>
                                      </div>

                                      <div class="form-group">
                                        <label>Document (optional)</label>
                                        <input type="file" class="form-control" name="document">
                                      </div>
                                    </div>

                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                        Abbrechen
                                      </button>
                                      <button type="submit" class="btn btn-primary">
                                        Speichern
                                      </button>
                                    </div>
                                  </form>

                                </div>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endforeach

                      @if($description->count() === 0)
                        <tr>
                          <td colspan="5" class="text-center text-muted py-2">
                            Keine Dokumente gefunden.
                          </td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-1">
                  {{ $description->links() }}
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@stop

@section('script')
<script>
  $(document).ready(function () {
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
  // Dynamic rows (upload multiple)
  var i = 0;

  $('#add_brand').on('click', function () {
    i++;

    $('#add_department_body').append(`
      <tr>
        <input type="hidden" name="product[${i}][product_id]" value="{{ $data->id }}">

        <td>
          <input type="text" class="form-control" disabled value="{{ $brand->name }}">
        </td>

        <td>
          <input type="text" class="form-control" disabled value="{{ $data->product }} - {{ $data->model }}">
        </td>

        <td>
          <input type="text" class="form-control" placeholder="Title" name="product[${i}][title]">
        </td>

        <td>
          <input type="file" class="form-control" name="product[${i}][document]">
        </td>

        <td class="text-right">
          <button type="button" class="btn btn-icon rounded-circle btn-outline-danger" data-action="remove-row" title="Entfernen">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    `);
  });

  $(document).on('click', '[data-action="remove-row"]', function () {
    $(this).closest('tr').remove();
  });
</script>
@endsection
