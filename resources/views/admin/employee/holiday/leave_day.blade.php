@extends('admin.layouts.app')
@section('title') Urlaub @stop
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
                                <h4 class="card-title">Urlaub</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                        
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\LeaveDayController@index')}}">
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
                                                                    <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\LeaveDayController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <fieldset> 
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                        Jahr
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control"  name="year" value="{{ old('year') }}" required>
                                                                                        @if ($errors->has('year'))<p style="color:red;">{!!$errors->first('year')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Urlaub
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control"  name="leave_day" value="{{ old('leave_day') }}" required>
                                                                                        @if ($errors->has('leave_day'))<p style="color:red;">{!!$errors->first('leave_day')!!}</p>@endif
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
                                                    <th scope="col">Jahr</th>
                                                    <th scope="col">Urlaub</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Ackion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>{{ $item->year }}</td>
                                                    <td>{{ $item->leave_day }}</td>
                                                    <td>{{ $item->status }}</td>
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
                                                              <a type="button" href="{{url('/leave_day_destroy').'/'.$item->id}}" class="btn btn-primary">Yes</a>
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
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\LeaveDayController@update')}}">
                                                                @csrf

                                                                <fieldset> 
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                Jahr
                                                                                </label>
                                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                                <input type="text" class="form-control"  name="year"  value="{{ $item->year }}" required>
                                                                                @if ($errors->has('year'))<p style="color:red;">{!!$errors->first('year')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Feiertage
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="leave_day" value="{{ $item->leave_day }}"  required>
                                                                                @if ($errors->has('leave_day'))<p style="color:red;">{!!$errors->first('leave_day')!!}</p>@endif
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

                                    @if($item->status!="Published")
                                    <a type="button" href="{{ url('leave_day_active/'.$item->id) }}"    class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" >
                                        <i class="feather icon-check"></i>
                                        </a>
                                    @else
                                    <a type="button" href="{{ url('leave_day_deactive/'.$item->id) }}"    class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                        <i class="fa fa-power-off    "></i>
                                        </a>
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