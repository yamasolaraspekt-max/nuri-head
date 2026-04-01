@extends('admin.layouts.app')
@section('title') {{auth()->user()->name}} User Roll @endsection

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
                                     <h4 class="card-title">User Roll</h4>
                                     <div class="col-md-6 col-12 mb-1">
                                     <form action="">
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
                                        <div class="col-md-4">
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
                                                                <h4 class="modal-title" id="myModalLabel1">User Management</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" enctype="multipart/form-data" novalidate method="post" action="{{action('App\Http\Controllers\UserRollController@store')}}">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <div class="col-12">
                                                                              
                                                                               <select class="select2 js-example-programmatic form-control" name="user_id" id="programmatic-single">
                                                                                    <optgroup label="Please define category first">
                                                                                    <option value="" selected>Select User Name</option>
                                                                                    @foreach($all_user as $users)
                                                                                    <option value="{{$users->eid}}" >{{ $users->ename}} {{ $users->lastname}}</option>
                                                                                    @endforeach
                                                                                    
                                                                                    </optgroup>
                                                                            </select>
                                                                            @if ($errors->has('user_id'))<p style="color:red;">{!!$errors->first('user_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                    <div class="col-12">
                                                                                
                                                                               <select class="select2 js-example-programmatic form-control" name="item_id" id="programmatic-single">
                                                                                    <optgroup label="Please define category first">
                                                                                    <option value="" selected>Select Field Item</option>
                                                                                    <option value="Administrator" >Administrator</option>
                                                                                    <option value="Employee" >Employee</option>
                                                                                    <option value="Customer" >Customer</option>
                                                                                    <option value="Problem" >Problem</option>
                                                                                    <option value="Product" >Product</option>
                                                                                    <option value="Error" >Errors</option>
                                                                                    <option value="Users" >Users</option>
                                                                                    <option value="Comment" >Comment</option>
                                                                                    <option value="Problems" >All Problems (Admin Only)</option>
                                                                                    <option value="Invoice" >Invoice</option>
                                                                                    <option value="Programmer" >Programmer</option>
                                                                                    <option value="Partner" >Partner</option>
                                                                                    <option value="Admin" >Admin</option>
                                                                                    <option value="Email" >Email</option>
                                                                                    <option value="Inquiry" >Inquiry</option>
                                                                                    <option value="Service" >Service</option>
                                                                                    <option value="Maintenance" >Maintenance</option> 
                                                                                    <option value="Organization" >Organization</option>
                                                                                    <option value="Finance" >Finance</option>
                                                                                    <option value="Super" >Super</option> 
                                                                                    </optgroup>
                                                                            </select>
                                                                            @if ($errors->has('item_id'))<p style="color:red;">{!!$errors->first('item_id')!!}</p>@endif
                                                                            </div>
                                                                        </div>
                                                                  <!-- permissions start -->
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header border-bottom mx-2 px-0">
                                                                                <h6 class="border-bottom py-1 mb-0 font-medium-2"><i class="feather icon-lock mr-50 "></i>Permission
                                                                                </h6>
                                                                            </div>
                                                                            <div class="card-body px-75">
                                                                                <div class="table-responsive users-view-permission">
                                                                                    <table class="table table-borderless">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Module</th>
                                                                                                <th>Read</th>
                                                                                                <th>Write</th>
                                                                                                <th>Create</th>
                                                                                                <th>Delete</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td>User Premission</td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox"  name="is_read" id="users-checkbox1" class="custom-control-input"  checked>
                                                                                                        <label class="custom-control-label" for="users-checkbox1"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_update" id="users-checkbox2" class="custom-control-input" ><label class="custom-control-label" for="users-checkbox2"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_add" id="users-checkbox3" class="custom-control-input" ><label class="custom-control-label" for="users-checkbox3"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_delete" id="users-checkbox4" class="custom-control-input">
                                                                                                        <label class="custom-control-label" for="users-checkbox4"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                         


                                                                                          

                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- permissions end -->

                                                               
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                <a type="submit" href="user_roll"class="btn btn-primary">Cancel</a>
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

                                @if(@Session('not_msg'))
                                <h5 class="alert alert-danger"><i class="fa fa-close"></i> {{Session('not_msg')}}</h5>
                                @endif
                                    <!-- Table with outer spacing -->
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                             
                                                <tr>
                                                    <th>ID</th>
                                                    <th width="400px">User Name</th>
                                                    <th width="400px">User Roll</th>
                                                    <th width="400px">Access Item</th>
                                                    <th width="400px">Read</th>
                                                    <th width="400px">Write</th>
                                                    <th width="400px">Update</th>
                                                    <th width="400px">Delete</th>
                                                    <th width="150px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                           @if($user) 
                                           @foreach($user as $ab)
                                                <tr>
                                                    <th scope="row">{{$ab->id}}</th>
                                                    <td>{{$ab->ename}} {{ $ab->elastname }}</td>
                                                    @if($ab->is_admin==1)
                                                    <td>Admin</td>
                                                    @else
                                                    <td>User</td>
                                                    @endif

                                                    <td>{{$ab->item_id}}</td>

                                                    <td>
                                                        @if($ab->is_read=='on')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @elseif($ab->is_read=='off')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled  unchecked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($ab->is_add=='on')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @elseif($ab->is_add=='off')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled  unchecked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                  
                                                    <td>
                                                        @if($ab->is_update=='on')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @elseif($ab->is_update=='off')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled unchecked >
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($ab->is_delete=='on')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @elseif($ab->is_delete=='off')
                                                        <div class="custom-control custom-checkbox ml-50">
                                                            <input type="checkbox" id="is_read" class="custom-control-input" disabled unchecked >
                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                        </div>
                                                        @endif
                                                    </td>


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
                                                              <a type="button" href="{{url('/user_roll_destroy').'/'.$ab->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- End Delete Modal -->

                                            <!-- Edit Button-->
                                            
                                            <!-- <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#default{{$ab->id}}">
                                            <i class="feather icon-edit"></i>
                                                </button> -->

                                                <!-- Modal -->
                                          
                                                <div class="modal fade text-left" id="default{{$ab->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel1">User Roll | Edit</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                            <form class="form-horizontal" novalidate method="get" action="">
                                                                @csrf
                                                                <div class="form-group">
                                                                    <div class="col-12">
                                                                              
                                                                               <select class="select2 js-example-programmatic form-control" name="user_id" id="programmatic-single">
                                                                                    <optgroup label="Please define category first">
                                                                                    <option value="" selected>Select User Name</option>
                                                                                    @foreach($all_user as $users)
                                                                                    <option value="{{$users->id}}" >{{ $users->name}}</option>
                                                                                    @endforeach
                                                                                    
                                                                                    </optgroup>
                                                                            </select>
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('user_id'))<p style="color:red;">{!!$errors->first('user_id')!!}</p>@endif


                                                                        <div class="form-group">
                                                                    <div class="col-12">
                                                                                
                                                                               <select class="select2 js-example-programmatic form-control" name="item_id" id="programmatic-single">
                                                                                    <optgroup label="Please define category first">
                                                                                    <option value="" selected>Select Field Item</option>
                                                                                             <option value="Employee" >Employee</option>
                                                                                            <option value="Customer" >Customer</option>
                                                                                            <option value="Problem" >Problem</option>
                                                                                            <option value="Product" >Product</option>
                                                                                            <option value="Error" >Errors</option>
                                                                                            <option value="Users" >Users</option>
                                                                                            <option value="Comment" >Comment</option>
                                                                                            <option value="Problems" >All Problems (Admin Only)</option>
                                                                                            <option value="Invoice" >Invoice</option>
                                                                                            <option value="Programmer" >Programmer</option>
                                                                                            <option value="Partner" >Partner</option>
                                                                                            <option value="Admin" >Admin</option>
                                                                                            <option value="Email" >Email</option>
                                                                                            <option value="Inquiry" >Inquiry</option>
                                                                                            <option value="Service" >Service</option>
                                                                                            <option value="Organization" >Organization</option>
                                                                                            <option value="Finance" >Finance</option>

                                                                                    
                                                                                    
                                                                                    </optgroup>
                                                                            </select>
                                                                            </div>
                                                                            @if ($errors->has('item_id'))<p style="color:red;">{!!$errors->first('item_id')!!}</p>@endif

                                                                        </div>
                                                                  <!-- permissions start -->
                                                                    <div class="col-12">
                                                                        <div class="card">
                                                                            <div class="card-header border-bottom mx-2 px-0">
                                                                                <h6 class="border-bottom py-1 mb-0 font-medium-2"><i class="feather icon-lock mr-50 "></i>Permission
                                                                                </h6>
                                                                            </div>
                                                                            <div class="card-body px-75">
                                                                                <div class="table-responsive users-view-permission">
                                                                                    <table class="table table-borderless">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Module</th>
                                                                                                <th>Read</th>
                                                                                                <th>Write</th>
                                                                                                <th>Create</th>
                                                                                                <th>Delete</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td>User Premission</td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox"  name="is_read" id="users-checkbox1" class="custom-control-input"  checked>
                                                                                                        <label class="custom-control-label" for="users-checkbox1"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_update" id="users-checkbox2" class="custom-control-input" ><label class="custom-control-label" for="users-checkbox2"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_add" id="users-checkbox3" class="custom-control-input" ><label class="custom-control-label" for="users-checkbox3"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <div class="custom-control custom-checkbox ml-50"><input type="checkbox" name="is_delete" id="users-checkbox4" class="custom-control-input">
                                                                                                        <label class="custom-control-label" for="users-checkbox4"></label>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                         


                                                                                          

                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- permissions end -->
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                <a type="submit" href="{{ url('user_roll')}}" class="btn btn-primary">Cancel</a>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                    </td>
                                                </tr>
                                            @endforeach
                                            @endif   
                                           
                                            </tbody>
                                        </table>
                                       
                                    </div>

                                {{$user->links()}}
                                         
                                                    
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