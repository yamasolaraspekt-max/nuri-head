@extends('admin.layouts.app')
@section('title') {{auth()->user()->name}} Profile @endsection
@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
           
            </div>
            <div class="content-body">
                <!-- page users view start -->
                @if(Session('save_msg'))
                                  <div class="alert alert-success" role="alert"> 
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <em>{{Session('save_msg')}}</em>
                                  </div>
                                @endif
                                @if(Session('not_msg'))
                                  <div class="alert alert-danger" role="alert"> 
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <em>{{Session('not_msg')}}</em>
                                  </div>
                                @endif
                <section class="page-users-view">
                    <div class="row">
                        <!-- account start -->
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Account</div>
                                </div>
                                <div class="card-body col-md-12">
                                    <div class="row">
                                        <div class="round "  style="width:150px; !impoartant">
                                            <img src="{{asset('images/user').'/'.auth()->user()->image}}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
                                        </div>
                                        <div class="col-15 col-sm-9 col-md-6 col-lg-5">
                                            <table>
                                                <tr>
                                                    <td class="font-weight-bold">Username: </td>
                                                    <td>{{auth()->user()->email}}</td>
                                                </tr>
                                                <tr> 
                                                    <td class="font-weight-bold">Name: </td>
                                                    <td>{{auth()->user()->name}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Created at: </td>
                                                    <td>{{\Carbon\Carbon::parse(auth()->user()->created_at)->diffForHumans()}}</td>
                                                </tr>
                                               
                                            </table>
                                        </div>
                                        <div class="col-14 col-md-12 col-lg-5">
                                            <table class="ml-0 ml-sm-0 ml-lg-0">
                                                <tr>
                                                    <td class="font-weight-bold">Status: </td>
                                                   @if(auth()->user()->is_active==1)
                                                    <td>active</td>
                                                    @else
                                                    <td>Deactived</td>
                                                    @endif
                                                </tr>
                                                <tr>
                                                    <td class="font-weight-bold">Role: </td>
                                                    @if(auth()->user()->is_admin==1)
                                                    <td>Administrator</td>
                                                    @else
                                                    <td>limited</td>
                                                    @endif
                                                </tr>
                                               
                                            </table>
                                        </div>
                                       
                                    </div>
                                    <div class="divider">
                                    <div class="col-md-15 float-right">
                                    <a href="{{url('/photo_create')}}" class="btn btn-primary "><i class="feather icon-user"></i> Change Photo</a>
                                    
                                    
                                    
                                    </div>

                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                        </div>
                        <!-- account start -->
                        <div class="col-6">
                            <div class="card">
                           
                                <div class="card-header">
                               
                                    <div class="card-title">Change Password</div>
                                </div>
                                    <div class="card-body col-md-12">
                                
                                        <form action="{{action('App\Http\Controllers\UserController@change_password', app()->getLocale())}}" method="post">
                                                    @csrf
                                                    <table>
                                                        <tr>
                                                            <td class="font-weight-bold">Password: </td>
                                                            <td style="width: 350px !important">
                                                                <input type="password" name="password" id=""  class="form-control">
                                                                @if($errors->has('password'))
                                                                    <div class="alert alert-danger" role="alert"> 
                                                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                                        <em>{{ $errors->first('password') }}</em>
                                                                    </div>
                                                                 @endif
                                                            </td>
                                                        </tr>
                                                        <tr> 
                                                            <td class="font-weight-bold">New password: </td>
                                                            <td style="width: 350px !important">
                                                                <input type="password" name="new_password" class="form-control">
                                                                @if($errors->has('new_password'))
                                                                    <div class="alert alert-danger" role="alert"> 
                                                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                                        <em>{{ $errors->first('new_password') }}</em>
                                                                    </div>
                                                                 @endif

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="font-weight-bold">Confirm password: </td>
                                                            <td style="width: 350px !important">
                                                                <input type="password" name="confirm_password" class="form-control">

                                                            </td>
                                                        </tr>
                                                    </table>
                                              
                                     
                                            <div class="divider">
                                            <div class="col-md-15 float-right">
                                            <button type="submit" class="btn btn-primary "><i class="feather icon-lock"></i> Change Password</button>
    
                                             </div>
                                             </form>

                                        </div>
                                            
                                    </div>
                                </div>
                                
                            </div>
                </div>
                        <!-- account end -->
                     
                       
                     
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
                                                    <th>Update</th>
                                                    <th>Create</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $pre)
                                                @if($pre->user_id==auth()->user()->id)
                                                <tr>
                                                    <td>{{ $pre->item_id}}</td>
                                                    <td>
                                                        @if($pre->is_read=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_update=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_add=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($pre->is_delete=='on')
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled checked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @else
                                                        <div class="custom-control custom-checkbox ml-50"><input type="checkbox" id="users-checkbox1" class="custom-control-input" disabled unchecked>
                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                        </div>
                                                        @endif
                                                    </td>
                                                   
                                                </tr>
                                                @endif
                                               @endforeach
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- permissions end -->
                    </div>
                </section>
                <!-- page users view end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    @stop

    @section('script')
    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/app-user.js"></script>
    <!-- END: Page JS-->
    @stop