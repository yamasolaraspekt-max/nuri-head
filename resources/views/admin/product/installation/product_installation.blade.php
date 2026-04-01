@extends('admin.layouts.app')
@section('title')Produktinstallation @stop
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
                            <h2 class="content-header-title float-left mb-0">Produkt</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/')}}">Dashboard</a>  </li>
                                    <li class="breadcrumb-item "><a href="{{ url('/product') }}"> Liste </li></a> 
                                    <li class="breadcrumb-item active"> Produktinstallation </li>   
                                    
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
                            <div class="card-content">
                                <div class="card-body"> 
                                    
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                        <form action="{{ action('App\Http\Controllers\ProductInstallationCaseController@index', ['id'=>request()->id]) }}" method="GET" class="flex-grow-1 mr-2 mb-2">
                                            <div class="input-group">
                                                <a href="{{ url('product_details/' . request()->id) }}" class="btn btn-outline-primary">
                                                    <i class="fa fa-chevron-left"></i> Zurück
                                                </a>
                                                <input type="text" name="search" class="form-control mx-1" placeholder="Suchen...">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit"><i class="feather icon-search"></i></button>
                                                </div>
                                            </div>
                                        </form>

                                        <button type="button" class="btn btn-outline-primary btn-lg mb-2" data-toggle="modal" data-target="#default">
                                            erstellen
                                        </button>
                                    </div>

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
                                                    <form class="form-horizontal" novalidate method="post" action="{{ action('App\Http\Controllers\ProductInstallationCaseController@store') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                        @csrf
                                                        <fieldset> 
                                                            <div class="row">

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="Title">
                                                                            Produkt
                                                                        </label>
                                                                        <input type="text" disabled class="form-control" value="{{ DB::table('products')->where('products.id', '=', request()->id)->select('product')->value('product')}}" name="product_id" placeholder="Produkt " >
                                                                        <input type="hidden" class="form-control" value="{{ request()->id }}" name="id" >
                                                                    </div>
                                                                
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="Title">
                                                                            Schwierigkeitsgrad
                                                                        </label>
                                                                    
                                                                        <select class="form-control" id="case" name="case">
                                                                            <option>Einfach</option>
                                                                            <option>Normal</option>
                                                                            <option>Schwer</option>
                                                                            <option>Anspruchsvoll</option>
                                                                            <option>Kompliziert</option>
                                                                            <option>Benutzerdefiniert</option>
                                                                        </select>
                                                                
                                                                        @if ($errors->has('case'))<p style="color:red;">{!!$errors->first('case')!!}</p>@endif
                                                                    </div>
                                                                    <input type="text" class="form-control"  name="custom" placeholder="sonstiges "  id="custom" style="display:none">
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="Title">
                                                                            Beschreibung
                                                                        </label>
                                                                        <textarea class="form-control"  name="description"  required></textarea>
                                                                        @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="Title">
                                                                            Bewertung
                                                                        </label>
                                                                    
                                                                        <select class="form-control" id="rate" name="rate">
                                                                            <option>1</option>
                                                                            <option>2</option>
                                                                            <option>3</option>
                                                                            <option>4</option>
                                                                            <option>5</option>
                                                                            <option>6</option>
                                                                        </select>
                                                                        @if ($errors->has('rate'))<p style="color:red;">{!!$errors->first('rate')!!}</p>@endif
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
                        
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Produkt</th>
                                                <th scope="col">Fall</th>
                                                <th scope="col">Beschreibung</th>
                                                <th scope="col">Preis</th>
                                                <th scope="col">Aktion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{{ $item->product }}</td>
                                                    <td>{{ $item->case }}</td>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->rate }}</td>
                                                    <td> 
                                                        <!-- Delete Modal -->
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                        <i class="feather icon-trash"></i>
                                                        </button> 
                                                        <!-- Modal -->
                                                        <div class="modal fade text-left" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <h5>Aufzeichnung löschen</h5>
                                                                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                        <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    <a type="button" href="{{url('/product_installation_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <!-- End Delete Modal --> 
                                                        <!-- Begin: Edit -->
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                                             <i class="feather icon-edit"></i>
                                                        </button>
                                                        <!-- Modal -->
                                                        <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\ProductInstallationCaseController@update')}}">
                                                                        @csrf 
                                                                        <div class="modal-body"> 
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Artikel name
                                                                                        </label>
                                                                                        <input type="text" disabled class="form-control" value="{{ DB::table('products')->where('products.id', '=', request()->id)->select('product')->value('product')}}" name="product_id" placeholder="Produkt " >
                                                                                        <input type="hidden" class="form-control" value="{{ request()->id }}" name="product_id" >
                                                                                        <input type="hidden" class="form-control" value="{{ $item->id }}" name="id" >
                                                                                    </div>
                                                                                
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Schwierigkeitsgrad
                                                                                        </label>
                                                                                    
                                                                                        <select class="form-control" id="case" name="case">
                                                                                            <option selected >{{ $item->case }}</option>
                                                                                            <option>Einfach</option>
                                                                                            <option>Normal</option>
                                                                                            <option>Schwer</option>
                                                                                            <option>Anspruchsvoll</option>
                                                                                            <option>Kompliziert</option>
                                                                                            <option>Benutzerdefiniert</option>
                                                                                        </select>
                                                                            
                                                                                        @if ($errors->has('case'))<p style="color:red;">{!!$errors->first('case')!!}</p>@endif
                                                                                    </div>
                                                                                    <input type="text" class="form-control"  name="custom" placeholder="sonstiges "  value="{{ old('custom') }}" id="custom" style="display:none">
                                                                                </div>

                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Beschreibung
                                                                                        </label>
                                                                                        <textarea class="form-control"  name="description"  required>{{ $item->description }}</textarea>
                                                                                        @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Bewertung
                                                                                        </label>
                                                                                    
                                                                                        <select class="form-control" id="rate" name="rate">
                                                                                            <option>{{ $item->rate }}</option>
                                                                                            <option>1</option>
                                                                                            <option>2</option>
                                                                                            <option>3</option>
                                                                                            <option>4</option>
                                                                                            <option>5</option>
                                                                                            <option>6</option>
                                                                                        </select>
                                                                                        @if ($errors->has('rate'))<p style="color:red;">{!!$errors->first('rate')!!}</p>@endif
                                                                                    </div>
                                                                                </div> 
                                                                            </div> 
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Einreichen</button>

                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Edit Modal -->

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
    $('#case').change(function(){
        var selectedCase = document.getElementById('case').value;

        if (selectedCase == "Benutzerdefiniert") {
            $('#custom').show();
        } else {
            $('#custom').hide();
        }
        console.log('the Case ' + selectedCase + ' The visibility ' + $('#custom').is(':visible'));
    });
</script>
@endsection