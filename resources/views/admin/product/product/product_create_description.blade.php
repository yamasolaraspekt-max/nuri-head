@extends('admin.layouts.app')
@section('title') Produkt Typ @stop

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">PRODUKTS</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
                                </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/product') }}">PRODUKTDETAILS</a>
                                </li>

                                </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/product_details/'.$data->id) }}">{{ $data->product }} - {{ $data->model }}</a>
                                </li>
                                    <li class="breadcrumb-item active"> Beschreibung </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row mb-2">
                <div class="col-12">
                    <div class="d-flex flex-wrap align-items-center">
                        <form action="" method="GET" class="form-inline flex-grow-1 mr-2 mb-1">
                            <div class="input-group w-100">
                                <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein" name="search">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="feather icon-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <a href="{{ url('/product_details/'.$data->id) }}" class="btn btn-outline-warning mb-1">
                            <i class="feather icon-chevrons-left"></i> Zurück
                        </a>
                    </div>
                </div>
            </div>


            <!-- Start Card -->
            <div class="card"> 
                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                     <form action="{{ action('App\Http\Controllers\ProductDescriptionController@store')}}" method="post">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered" id="add_department">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Hersteller</th>
                                        <th>Art.name</th>
                                        <th>Überschrift</th>
                                        <th>Beschreibung</th>
                                        <th>Anmerkung</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <input type="hidden" name="product[0][product_id]" value="{{ $data->id }}">
                                        <td><input type="text" class="form-control" disabled value="{{ $brand->name }}"></td>
                                        <td><input type="text" class="form-control" disabled value="{{ $data->product }} - {{ $data->model }}"></td>
                                        <td><input type="text" class="form-control" name="product[0][field]" placeholder="Überschrift"></td>
                                        <td><textarea class="form-control" name="product[0][description]" placeholder="Beschreibung"></textarea></td>
                                        <td><textarea class="form-control" name="product[0][remark]" placeholder="Anmerkung"></textarea></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-2"> 
                            <div>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="feather icon-save"></i> Datensatz speichern
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="add_brand">
                                    <i class="feather icon-plus"></i> Add Row
                                </button>
                            </div>
                        </div>
                    </form>
                  

                    <!-- Existing Description Table -->
                    <div class="table-responsive mt-3">
                        <table class="table table-striped" id="brand_table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Unternehmen</th>
                                    <th>Art.name</th>
                                    <th>Überschrift</th>
                                    <th>Beschreibung</th>
                                    <th>Anmerkung</th>
                                    <th>Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($description as $desk)
                                    <tr>
                                        <td>{{ $desk->id }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $desk->product }} - {{ $desk->model }}</td>
                                        <td>{{ $desk->field }}</td>
                                        <td>{{ $desk->description }}</td>
                                        <td>{{ $desk->remark }}</td>
                                        <td>
                                            <a href="{{ route('product.discription.destroy', ['id' => $desk->id]) }}" class="btn btn-icon btn-outline-danger"><i class="feather icon-trash-2"></i></a>
                                            <button class="btn btn-icon btn-outline-primary" data-toggle="modal" data-target="#edit{{ $desk->id }}"><i class="feather icon-edit"></i></button>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="edit{{ $desk->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="post" action="{{ action('App\Http\Controllers\ProductDescriptionController@update')}}">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $desk->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Bearbeiten</h5>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Überschrift</label>
                                                                    <input type="text" name="field" class="form-control" value="{{ $desk->field }}">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Beschreibung</label>
                                                                    <textarea name="description" class="form-control">{{ $desk->description }}</textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Anmerkung</label>
                                                                    <textarea name="remark" class="form-control">{{ $desk->remark }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Modal -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $description->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
<script>
    let i = 0;
    $('#add_brand').click(function () {
        i++;
        $('#add_department tbody').append(`
            <tr>
                <input type="hidden" name="product[${i}][product_id]" value="{{ $data->id }}">
                <td><input type="text" class="form-control" disabled value="{{ $brand->name }}"></td>
                <td><input type="text" class="form-control" disabled value="{{ $data->product }} - {{ $data->model }}"></td>
                <td><input type="text" class="form-control" name="product[${i}][field]" placeholder="Überschrift"></td>
                <td><textarea class="form-control" name="product[${i}][description]" placeholder="Beschreibung"></textarea></td>
                <td><textarea class="form-control" name="product[${i}][remark]" placeholder="Anmerkung"></textarea></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
            </tr>
        `);
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    @if(Session::has('update_msg'))
    toastr.success("{{ session('update_msg') }}");
    @endif

    @if(Session::has('save_msg'))
    toastr.success("{{ session('save_msg') }}");
    @endif

    @if(Session::has('delete_msg'))
    toastr.error("{{ session('delete_msg') }}");
    @endif
</script>
@endsection
