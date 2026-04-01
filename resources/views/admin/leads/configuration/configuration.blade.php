@extends('admin.layouts.app')
@section('title')EMAIL KONFIGURATOR @stop
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
                                <h4 class="card-title">EMAIL KONFIGURATOR: </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                
                        
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\EmailConfigurationController@index')}}">
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

                                    @if(@Session('delete_msg'))
                                    <h5 class="alert alert-danger"><i class="fa fa-close"></i> {{Session('delete_msg')}}</h5>
                                    @endif
                                    @if(@Session('save_msg'))
                                    <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('save_msg')}}</h5>
                                    @endif
                                    @if(@Session('update_msg'))
                                    <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('update_msg')}}</h5>
                                    @endif
                                    @if(@Session('not_save'))
                                    <h5 class="alert alert-danger"><i class="fa fa-check"></i> {{Session('not_save')}}</h5>
                                    @endif
                                <div class="col-md-3 float-right">
                                        <div class="card-body">
                                            <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal" data-target="#default">
                                            Neue hinzufügen
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
                                                                    <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmailConfigurationController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <fieldset> 
                                                                            <div class="row">
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Name
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control" value="{{ old('name') }}"  name="name"  required>
                                                                                        @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-10">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Host
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control" value="{{ old('host') }}"  name="host"  required>
                                                                                        @if ($errors->has('host'))<p style="color:red;">{!!$errors->first('host')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-2">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Port
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control" value="993"   name="port"  required>
                                                                                        @if ($errors->has('port'))<p style="color:red;">{!!$errors->first('port')!!}</p>@endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            User Name/Email
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control"  value="{{ old('username') }}" name="username"  required>
                                                                                        @if ($errors->has('username'))<p style="color:red;">{!!$errors->first('username')!!}</p>@endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                           Password
                                                                                        </label>
                                                                                    
                                                                                        <input type="text" class="form-control" value="{{ old('password') }}" name="password"  required>
                                                                                        @if ($errors->has('password'))<p style="color:red;">{!!$errors->first('password')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Encryption
                                                                                        </label>
                                                                                    
                                                                                        <select class="form-control" name="encryption">
                                                                                            <option value="ssl" selected>SSL</option>
                                                                                            <option value="tls">TLS</option>
                                                                                        </select>
                                                                                        @if ($errors->has('encryption'))<p style="color:red;">{!!$errors->first('encryption')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Validation
                                                                                        </label>
                                                                                    
                                                                                        <select class="form-control" name="validate_cert">
                                                                                            <option value="true" selected>True</option>
                                                                                            <option value="false">False</option>
                                                                                        </select>
                                                                                        @if ($errors->has('validate_cert'))<p style="color:red;">{!!$errors->first('validate_cert')!!}</p>@endif
                                                                                    </div>
                                                                                </div>

                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="Title">
                                                                                            Protocol
                                                                                        </label>
                                                                                    
                                                                                        <select class="form-control" name="protocol">
                                                                                            <option value="imap" selected>IMAP</option>
                                                                                            <option value="pop">POP3</option>
                                                                                        </select>
                                                                                        @if ($errors->has('protocol'))<p style="color:red;">{!!$errors->first('protocol')!!}</p>@endif
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
                                                    <th scope="col">Name</th>
                                                    <th scope="col">User</th>
                                                    <th scope="col">Password</th>
                                                    <th scope="col">Host</th>
                                                    <th scope="col">Port</th>
                                                    <th scope="col">Encryption</th>
                                                    <th scope="col">Validation</th>
                                                    <th scope="col">Protocol</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr>
                                                    <th scope="row">{{ $item->id }}</th>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->username }}</td>
                                                    <td><input type="password" id="passwordField" disabled value="{{ $item->password }}" style="border: 0;border-style: none; background: transparent;" ondblclick="togglePasswordVisibility(this)"></td>
                                                    <td>{{ $item->host }}</td>
                                                    <td>{{ $item->port }}</td>
                                                    <td>{{ $item->encryption }}</td>
                                                    <td>{{ $item->validate_cert }}</td>
                                                    <td>{{ $item->protocol }}</td>
                                                    <td>
                                                        @if($item->status=="Published")
                                                        <div class="chip chip-success mr-1">
                                                            <div class="chip-body">
                                                                <span class="chip-text">Aktiv</span>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="chip chip-danger mr-1">
                                                            <div class="chip-body">
                                                                <span class="chip-text">Deaktiv</span>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        @if($item->test=="tested")
                                                        <div class="chip chip-success mr-1">
                                                            <div class="chip-body">
                                                                <span class="chip-text">GEPRÜFT IMAP</span>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <div class="chip chip-danger mr-1">
                                                            <div class="chip-body">
                                                                <span class="chip-text">NICHT GETESTET</span>
                                                            </div>
                                                        </div>
                                                        @endif
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
                                                                <h5>Aufzeichnung löschen</h5>
                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/email_configuration_destroy').'/'.$item->id}}" class="btn btn-primary">Ja</a>
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
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmailConfigurationController@update')}}">
                                                                @csrf

                                                                <fieldset> 
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Name
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="name" value="{{ $item->name }}" required>
                                                                                <input type="hidden" value="{{ $item->id }}" name="id">
                                                                                @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-10">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Host
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="host" value="{{ $item->host }}"  required>
                                                                                @if ($errors->has('host'))<p style="color:red;">{!!$errors->first('host')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-2">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Port
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="port"  value="{{ $item->port }}"required>
                                                                                @if ($errors->has('port'))<p style="color:red;">{!!$errors->first('port')!!}</p>@endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    User Name/Email
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="username" value="{{ $item->username }}" required>
                                                                                @if ($errors->has('username'))<p style="color:red;">{!!$errors->first('username')!!}</p>@endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                   Password
                                                                                </label>
                                                                            
                                                                                <input type="text" class="form-control"  name="password"  value="{{ $item->password }}" required>
                                                                                @if ($errors->has('password'))<p style="color:red;">{!!$errors->first('password')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Encryption
                                                                                </label>
                                                                            
                                                                                <select class="form-control" name="encryption">
                                                                                    <option>{{ $item->encryption }}</option>
                                                                                    <option>SSL</option>
                                                                                    <option>TLS</option>
                                                                                </select>
                                                                                @if ($errors->has('encryption'))<p style="color:red;">{!!$errors->first('encryption')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Validation
                                                                                </label>
                                                                            
                                                                                <select class="form-control" name="validate_cert">
                                                                                    <option>{{ $item->validate_cert }}</option>

                                                                                    <option>True</option>
                                                                                    <option>False</option>
                                                                                </select>
                                                                                @if ($errors->has('validate_cert'))<p style="color:red;">{!!$errors->first('validate_cert')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="Title">
                                                                                    Protocol
                                                                                </label>
                                                                            
                                                                                <select class="form-control" name="protocol">
                                                                                    <option>{{ $item->protocol }}</option>

                                                                                    <option value="imap">IMAP</option>
                                                                                    <option value="pop">POP3</option>
                                                                                </select>
                                                                                @if ($errors->has('protocol'))<p style="color:red;">{!!$errors->first('protocol')!!}</p>@endif
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

                                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1" href="{{ url('email_config_publish/'.$item->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Activating Email">
                                                    <i class="feather icon-check  "></i>
                                                </a>

                                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" href="{{ url('email_config_unpublish/'.$item->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Deactive the Email">
                                                    <i class="feather icon-pause-circle "></i>
                                                </a>

                                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-warning mr-1 mb-1" href="{{ url('email_config_test/'.$item->id) }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Test IMAP Connection">
                                                    <i class="feather icon-loader "></i>
                                                </a>
                                               
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
    function togglePasswordVisibility(element) {
        // Check if the input type is password
        if (element.type === 'password') {
            element.type = 'text'; // Change it to text to show the password
        } else {
            element.type = 'password'; // Change it back to password to hide the password
        }
    }
</script>
@endsection