@extends('admin.layouts.app')
@section('title') Produkt Images @stop

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt Bild</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a>  </li>
                                <li class="breadcrumb-item active"> <a href="{{ url('/product')}}">Liste</a>  </li>   
                                <li class="breadcrumb-item "><a href="{{ url('/product_details/'.request()->id)}}">{{$data->product}} -  {{ $data->model }}</a>  </li>   
                                <li class="breadcrumb-item active"> Produkt Bild </li>   
                                
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row mb-2">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <form action="" class="flex-grow-1 mr-2">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="feather icon-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <a href="{{ url('/product_details/'.request()->id)}}" class="btn btn-outline-warning">
                            <i class="feather icon-chevrons-left"></i> Zurück
                        </a>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Produkt Fotos</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Product')->where('is_add', 'on')->exists())
                        <form action="{{ action('App\Http\Controllers\ProductImageController@store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="table-responsive">
                                <table class="table" id="add_department">
                                    <thead>
                                        <tr>
                                            <th>Hersteller</th> 
                                            <th>Art.name</th> 
                                            <th>Titel</th> 
                                            <th>Bild</th> 
                                            <th>Aktion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <input type="hidden" name="product[0][product_id]" value="{{ $data->id }}">
                                            <td><input type="text" class="form-control" disabled value="{{ $brand->name }}"></td>
                                            <td><input type="text" class="form-control" disabled value="{{ $data->product }} - {{ $data->model }}"></td>
                                            <td><input type="text" class="form-control" name="product[0][title]" placeholder="Title of Image"></td>
                                            <td><input type="file" class="form-control" name="product[0][image]"></td>
                                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex mt-2">
                                <button type="submit" class="btn btn-outline-success mr-2">
                                <i class="feather icon-save"></i> Speichern
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="add_brand">
                                <i class="feather icon-plus"></i> Hinzufügen
                                </button>
                            </div>
                        </form>
                    @endif

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Hersteller</th> 
                                    <th>Art.name</th> 
                                    <th>Titel</th> 
                                    <th>Bild</th> 
                                    <th>Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($description as $desk)
                                    <tr>
                                        <td>{{ $brand->name }}</td>
                                        <td>{{ $desk->product }} - {{ $desk->model }}</td>
                                        <td>{{ $desk->name }}</td>
                                        <td><img src="{{ asset('images/products/'.$desk->image) }}" alt="{{ $desk->product }}" class="img-thumbnail" width="80"></td>
                                        <td>
                                            <a href="{{ route('product.image.destroy', ['id' => $desk->id]) }}" class="btn btn-outline-danger btn-sm"><i class="feather icon-trash-2"></i></a>
                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#edit{{ $desk->id }}"><i class="feather icon-edit"></i></button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="edit{{ $desk->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ action('App\Http\Controllers\ProductImageController@update') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Bearbeiten</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="{{ $desk->id }}">
                                                        <div class="form-group">
                                                            <label>Title</label>
                                                            <input type="text" class="form-control" name="name" value="{{ $desk->name }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Image</label>
                                                            <input type="file" class="form-control" name="image">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Aktualisieren</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
@endsection

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
                <td><input type="text" class="form-control" name="product[${i}][title]" placeholder="Title"></td>
                <td><input type="file" class="form-control" name="product[${i}][image]"></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-row"><i class="fa fa-trash"></i></button></td>
            </tr>
        `);
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });
</script>
@endsection