@extends('admin.layouts.app')
@section('title') Contract Type @stop
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">NOTIZ KATEGORIE</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        </ol>
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
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\NoteCategoryController@index')}}">
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
                                    <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal" data-target="#default">
                                        Neu hinzufügen
                                    </button>
                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel1">New</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\NoteCategoryController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-body"> 
                                                            <fieldset> 
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                            Kategoriename
                                                                            </label> 
                                                                                <input type="text" class="form-control"  name="category_name"  required value="{{ old('category_name')}}">
                                                                                @if ($errors->has('category_name'))<p style="color:red;">{!!$errors->first('category_name')!!}</p>@endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                            Typ
                                                                            </label> 
                                                                                <input type="text" class="form-control"  name="type"    required value="{{ old('type', 'Normal')}}"> 
                                                                                @if ($errors->has('type'))<p style="color:red;">{!!$errors->first('type')!!}</p>@endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                            Farbe
                                                                            </label> 
                                                                                <input type="color" class="form-control"  name="color"  value="{{ old('color')}}"  required>
                                                                                @if ($errors->has('color'))<p style="color:red;">{!!$errors->first('color')!!}</p>@endif
                                                                        </div>
                                                                    </div> 
                                                                </div> 
                                                            </fieldset> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Submit</button> 
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
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Kategoriname</th>
                                                    <th scope="col">Typ</th>
                                                    <th scope="col">Farbe</th>
                                                    <th scope="col">Bearbeiten</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{$loop->index +1}}</th>
                                                    <td>{{ $item->category_name }}</td>
                                                    <td>{{ $item->type }}</td>
                                                    <td>
                                                      <div class="chip mr-1">
                                                            <div class="chip-body">
                                                                <div class="avatar bg-primary" style="background:{{$item->color}} !important">
                                                                    <span>FB</span>
                                                                </div>
                                                                <span class="chip-text">{{ $item->color }}</span>
                                                            </div>
                                                        </div>
                                                    </td> 
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
                                                                            <h5>Datensatz löschen</h5>
                                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                            <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                        <a type="button" href="{{url('/note_category_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
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
                                                                        <div class="modal-body">
                                                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\NoteCategoryController@update')}}">
                                                                            @csrf
                                                                                <input type="hidden" name="id" value="{{$item->id}}">
                                                                                <fieldset> 
                                                                                    <div class="row">
                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Kategoriename
                                                                                                </label> 
                                                                                                    <input type="text" class="form-control"  name="category_name"  required value="{{ old('category_name', $item->category_name) }}">
                                                                                                    @if ($errors->has('category_name'))<p style="color:red;">{!!$errors->first('category_name')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Typ
                                                                                                </label> 
                                                                                                    <input type="text" class="form-control"  name="type"  placeholder="normal" required value="{{ old('type', $item->type) }}">
                                                                                                    @if ($errors->has('type'))<p style="color:red;">{!!$errors->first('type')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-6">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Farbe
                                                                                                </label> 
                                                                                                    <input type="color" class="form-control"  name="color"   value="{{ old('color', $item->color) }}" required>
                                                                                                    @if ($errors->has('color'))<p style="color:red;">{!!$errors->first('color')!!}</p>@endif
                                                                                            </div>
                                                                                        </div> 
                                                                                    </div> 
                                                                                </fieldset> 
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Submit</button>

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
@endsection