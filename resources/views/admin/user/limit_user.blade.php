@extends('admin.layouts.app')
@section('title') Limited User @endsection


@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
             <div class="content-wrapper">
                <div class="content-header row">
                    <div class="row" id="basic-table">
                        <div class="col-12">
                             <div class="card">
                                <div class="card-header">
                                     <h4 class="card-title">Limited</h4>
                                     <div class="col-md-2">
                                     @if(DB::table('user_rolls')
                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                ->where('user_rolls.item_id', '=', 'Users')
                                                ->where('user_rolls.is_add', '=', 'on')
                                                ->first())
                                                <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal" data-target="#default">
                                                    Add New
                                                </button>
                                                @endif

                                                <!-- Modal -->
                                          
                                                <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">User Management | Limited User</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\UserController@limit_store', app()->getLocale())}}">
                                                                @csrf
                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">User</h4>
                                                                    <input type="text" class="form-control" name="name" placeholder="User" required>
                                                                    @if($errors->has('user'))<p style="color:red">{!!$errors->first('user')!!}</p>@endif
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">Email</h4>
                                                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                                                    @if($errors->has('email'))<p style="color:red">{!!$errors->first('email')!!}</p>@endif
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">Password</h4>
                                                                    <input type="password" class="form-control" name="password"  required>
                                                                    @if($errors->has('password'))<p style="color:red">{!!$errors->first('password')!!}</p>@endif
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">Confirm Password</h4>
                                                                    <input type="password" class="form-control" name="confirm_p"  required>
                                                                    @if(Route::has('password.request'))<p style="color:red">Passowrd not matched</p>@endif
                                                                </div>
                      

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                <a type="submit" href="{{url('/limit_user')}}"class="btn btn-primary">Cancel</a>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                     
                                     
                                 </div>


                         <div class="card-content">
                                <div class="card-body">
                                @if(@Session('delete_msg'))
                                <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('delete_msg')}}</h5>
                                @endif
                                @if(@Session('save_msg'))
                                <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('save_msg')}}</h5>
                                @endif

                                @if(@Session('update_msg'))
                                <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('update_msg')}}</h5>
                                @endif
                                    <!-- Table with outer spacing -->
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                             
                                                <tr>
                                                    <th>ID</th>
                                                    <th width="300px">User Name</th>
                                                    <th width="300px">Email</th>
                                                    <th width="300px">Authority</th>
                                                    <th width="300px">Status</th>
                                                    <th width="190px" >Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           @if($data)
                                           @foreach($data as $ab)
                                                <tr>
                                                    <th scope="row">{{$ab->id}}</th>
                                                    <td>{{$ab->name}}</th>
                                                    <td>{{$ab->email}}</th>
                                                    @if($ab->is_admin==1)
                                                    <td>Admin</th>
                                                    @else
                                                    <td>User</th>
                                                    @endif

                                                    @if($ab->is_active==1)
                                                    <td>Active</td>
                                                    @else
                                                    <td>Deactive</td>

                                                    @endif
                                                    <td>
                                                    
                                                    
                                                <!-- Delete Modal -->
                                                @if(DB::table('user_rolls')
                                                ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                ->where('user_rolls.item_id', '=', 'Users')
                                                ->where('user_rolls.is_delete', '=', 'on')
                                                ->first())
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$ab->id}}">
                                                <i class="feather icon-trash"></i>
                                                </button>
                                                @endif

                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="delete-pro{{$ab->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>Delete Record</h5>
                                                                <p>Do you really want to delete this record?</p>
                                                                <p>The Recard Number is: {{$ab->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('/admin_destroy').'/'.$ab->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- End Delete Modal -->

                                            <!-- Edit Button-->
                                            @if(DB::table('user_rolls')
                                                ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                ->where('user_rolls.item_id', '=', 'Category')
                                                ->where('user_rolls.is_update', '=', 'on')
                                                ->first())
                                                 
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#edit_about{{$ab->id}}">
                                                <i class="feather icon-edit"></i>
                                                </button>
                                                @endif

                                                <!-- Modal -->
                                          
                                                <div class="modal fade text-left" id="edit_about{{$ab->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">Limited User - Edit</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\UserController@limit_edit', app()->getLocale())}}">
                                                                @csrf
                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">User</h4>
                                                                    <input type="hidden" name="id" value="{{$ab->id}}">
                                                                    <input type="text" class="form-control" value="{{$ab->name}}" name="name" placeholder="User" required>
                                                                    @if($errors->has('user'))<p style="color:red">{!!$errors->first('user')!!}</p>@endif
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <h4 class="modal-title" id="myModalLabel1">Email</h4>
                                                                   
                                                                    <input type="text" class="form-control" value="{{$ab->email}}" name="email" placeholder="Email" required>
                                                                    @if($errors->has('user'))<p style="color:red">{!!$errors->first('user')!!}</p>@endif
                                                                </div>
                             

                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                <a type="submit" href="{{url('/limit_user')}}"class="btn btn-primary">Cancel</a>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>

                                            @if(auth()->user()->is_admin==1)
                                                <a type="button" href="{{url('/make_admin').'/'.$ab->id}}" class="btn btn-primary">Make Admin</a>
                                            @endif
                                            <div style="margin-top:10px !important;">
                                            @if(auth()->user()->is_admin==1)
                                                
                                                <a type="button" href="{{url('/active').'/'.$ab->id}}" title="Activate User" class="btn btn-icon btn-icon rounded-circle btn-success mr-1 mb-1"><i class="feather icon-check"></i></a>
                                                
                                            @endif

                                            @if(auth()->user()->is_admin==1)
                                                <a type="button" href="{{url('/deactive').'/'.$ab->id}}" title="Deactive User" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1"><i class="feather icon-power"></i></a>
                                              
                                            @endif
                                                </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                            @endif   
                                           
                                            </tbody>
                                        </table>
                                        {{$data->links()}}
                                    </div>
                                         
                                                    
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic Vertical form layout section end -->
                             </div>
                        </div>
                   </div>
            </div>     
            </div>     
        </div>      
    </div>
</div>
@stop
