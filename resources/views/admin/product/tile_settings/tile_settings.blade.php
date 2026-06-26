@extends('admin.layouts.app')
@section('title') Dacheindeckung @stop
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
                          
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Dacheindeckung</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                        
                                <div class="col-9">
                                        <form action="{{route('tiles.view')}}">
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
                                            Add New
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
                                                                <div class="modal-body">
                                                                    <form class="form-horizontal" novalidate method="post" action="{{route('tiles.save')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <fieldset> 
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                        Name
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control"  name="name"  required>
                                                                                        @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                        Model
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control"  name="model"  required>
                                                                                        @if ($errors->has('model'))<p style="color:red;">{!!$errors->first('model')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                        Foto
                                                                                        </label>
                                                                                    
                                                                                        <input type="file" class="form-control"  name="image"  required>
                                                                                        @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
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
                                      <!-- Modal End -->
                                    
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Dacheindeckung</th>
                                                    <th scope="col">Ackion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>{{ $item->name }} {{$item->model }}</td>
                                                    <td>
                                                        <div class="avatar mr-1 avatar-xl">
                                                            <img src="{{ asset('images/products/tiles/'.$item->image) }}" alt="{{ $item->name }}}}">
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
                                                              <a type="button" href="{{url('/tiles_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
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
                                                                <h4 class="modal-title" id="myModalLabel1">Edit</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{route('tiles.update')}}">
                                                                @csrf

                                                                <fieldset> 
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                   Name
                                                                                </label>
                                                                                 <input type="text" class="form-control"  name="name" value="{{$item->name}}"  required>
                                                                                 <input type="hidden" class="form-control"  name="id" value="{{$item->id}}"  required>
                                                                                 @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                   Model
                                                                                </label>
                                                                                 <input type="text" class="form-control"  name="model" value="{{$item->model}}"  required> 
                                                                                 @if ($errors->has('model'))<p style="color:red;">{!!$errors->first('model')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                         <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                   Foto
                                                                                </label>
                                                                                 <input type="file" class="form-control"  name="image"    required> 
                                                                                 @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                       </div>
                                                                       
                                                                 </fieldset>
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
@endsection