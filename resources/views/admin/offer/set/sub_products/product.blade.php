@extends('admin.layouts.app')
@section('title') Set Product @stop
@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

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
                            <h2 class="content-header-title float-left mb-0">Sub Product</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    
                                    <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}"> {{ $title->article_group }}</a>
                                    </li>

                                    <li class="breadcrumb-item"><a href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}"> {{ $title->sub_article }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('sets/'.$title->master_id) }}">{{ $title->setname }}</a>
                                    </li>
                                  
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                          
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"> </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                                <div class="col-9">
                                        <form action="{{ route('add.sub.product.set', ['master'=>request()->master, 'phase'=>request()->phase]) }}">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append" id="button-addon2">
                                                        <button class="btn btn-primary" type="submit">Go</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>

                                <div class="col-md-3 float-right">
                                    <div class="card-body">
                                        <a type="button" class="btn btn-outline-primary block btn-lg" href="{{ route('add.sub.product', ['master'=>request()->master, 'phase'=>request()->phase]) }}">
                                                Neue hinzufügen
                                        </a>
                                
                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                        <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\AddProductToSetController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                @csrf
                                                                <fieldset> 
                                                                    <div class="row">
                                                                        
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                            Product
                                                                                </label>
                                                                                <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                                                <select class="form-control" id="product" name="product_id" style="width: 100% !important;">
                                                                                    @foreach ($product as $pro)
                                                                                        <option value="{{ $pro->id }}">{{ $pro->product }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                
                                                                                @if ($errors->has('product'))<p style="color:red;">{!!$errors->first('product')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                Produktanzahl
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="product_count"  required>
                                                                                @if ($errors->has('product_count'))<p style="color:red;">{!!$errors->first('product_count')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Maßeinheit
                                                                                </label>
                                                                                <select class="form-control" id="measure" name="measure_unit" style="width: 100% !important;">
                                                                                    @foreach ($measure as $me)
                                                                                        <option value="{{ $me->id }}">{{ $me->measure }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                
                                                                                @if ($errors->has('measure'))<p style="color:red;">{!!$errors->first('measure')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </fieldset>
                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Houptartikel</th>
                                                    <th scope="col">Zubehörartikel</th>
                                                    <th scope="col">Produktanzahl</th>
                                                    <th scope="col">UVP</th>
                                                    <th scope="col">Rabbat-Gruppe</th>
                                                    <th scope="col">Einkaufspreis + Rabbat</th>
                                                    <th scope="col">Gesamtpreis</th>
                                                    <th scope="col">Beschreibung</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <td>{{ $item->id }}</td>
                                                    <td>{{ $item->product }}</td>
                                                    <td>{{ $item->product_count }} {{ $item->measure }}</td>
                                                    <td>{{ number_format( $item->retail_price, 2, ',', '.') }}€</td>
                                                    <td>{{ $item->discount_group }}</td>
                                                    <td>{{ number_format( $item->purchase_price, 2, ',', '.') }}€</td>
                                                    <td>{{ number_format( $item->total, 2, ',', '.') }}€</td> 
                                                    <td>
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#description{{ $item->id }}">
                                                            <i class="feather icon-maximize-2"></i>
                                                        </button>

                                                        <div class="modal fade text-left" id="description{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Produktbeschreibung (ID: {{ $item->id }})</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-hover-animation mb-0">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th scope="col">Titel</th>
                                                                                        <th scope="col">Wert</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach ($product_description[$item->id] ?? [] as $des)
                                                                                        <tr>
                                                                                            <td>{{ $des->title }}</td>
                                                                                            <td>{{ $des->value }}</td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    
                                                    <td>
                                                        {{-- Delete Button --}}
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{ $item->id }}">
                                                            <i class="feather icon-trash"></i>
                                                        </button>

                                                        {{-- Delete Modal --}}
                                                        <div class="modal fade text-left" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Löschen bestätigen</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <h5>Aufzeichnung löschen</h5>
                                                                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                        <p>Datensatznummer: <strong>{{ $item->id }}</strong></p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ url('/add_product_delete/' . $item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Edit Button --}}
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#editmodel{{ $item->id }}">
                                                            <i class="feather icon-edit"></i>
                                                        </button>

                                                        {{-- Edit Modal --}}
                                                        <div class="modal fade text-left" id="editmodel{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <form method="POST" action="{{ action('App\Http\Controllers\ProductMasterSetController@update') }}">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Produkt bearbeiten</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                                                            <div class="form-group">
                                                                                <label>Produkt</label>
                                                                                <select class="form-control" name="product_id" style="width: 100%;">
                                                                                    @foreach ($product as $pro)
                                                                                        <option value="{{ $pro->id }}" @if($pro->id == $item->product_id) selected @endif>
                                                                                            {{ $pro->product }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label>Produktanzahl</label>
                                                                                <input type="text" class="form-control" name="product_count" value="{{ $item->product_count }}" required>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label>Maßeinheit</label>
                                                                                <select class="form-control" name="measure_unit" style="width: 100%;">
                                                                                    @foreach ($measure as $me)
                                                                                        <option value="{{ $me->id }}" @if($me->id == $item->measure_unit) selected @endif>
                                                                                            {{ $me->measure_unit }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Einreichen</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Add Description Button --}}
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#add_product{{ $item->id }}">
                                                            <i class="feather icon-log-in"></i>
                                                        </button>

                                                        {{-- Add Description Modal --}}
                                                        <div class="modal fade text-left" id="add_product{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <form method="POST" action="{{ action('App\Http\Controllers\AddProductToSetController@add') }}">
                                                                        @csrf
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Beschreibung hinzufügen</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>

                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="master_set" value="{{ $item->master_set_id }}">
                                                                            <input type="hidden" name="product_set" value="{{ $item->id }}">

                                                                            <div class="form-group">
                                                                                <label>Titel</label>
                                                                                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label>Wert</label>
                                                                                <input type="text" class="form-control" name="value" value="{{ old('value') }}" required>
                                                                            </div>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-success">Speichern</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
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
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
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
$(document).ready(function() {
    $('#product').select2();
    $('#measure').select2();
    
});
</script>
@endsection