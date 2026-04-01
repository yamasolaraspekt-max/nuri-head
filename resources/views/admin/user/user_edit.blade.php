@extends('admin.layouts.app')
@section('title') {{auth()->user()->name}} Edit user @endsection

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- users edit start -->
                <section class="users-edit">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link d-flex align-items-center active" id="account-tab" data-toggle="tab" href="#account" aria-controls="account" role="tab" aria-selected="true">
                                            <i class="feather icon-user mr-25"></i><span class="d-none d-sm-block">Account</span>
                                        </a>
                                    </li>
                                 
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="account" aria-labelledby="account-tab" role="tabpanel">
                                        <!-- users edit media object start -->
                                        <div class="media mb-2">
                                            <a class="mr-2 my-25" href="#">
                                                <img src="{{ asset('images/user/'.auth()->user()->image)}}" alt="users avatar" class="users-avatar-shadow rounded" height="90" width="90">
                                            </a>
                                            <div class="media-body mt-50">
                                                <h4 class="media-heading">{{auth()->user()->name}}</h4>
                                                <div class="col-12 d-flex mt-1 px-0">
                                                    <a href="#" class="btn btn-primary d-none d-sm-block mr-75">Change</a>
                                                    <a href="#" class="btn btn-primary d-block d-sm-none mr-75"><i class="feather icon-edit-1"></i></a>
                                                    <a href="#" class="btn btn-outline-danger d-none d-sm-block">Remove</a>
                                                    <a href="#" class="btn btn-outline-danger d-block d-sm-none"><i class="feather icon-trash-2"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- users edit media object ends -->
                                        <!-- users edit account form start -->
                                        <form novalidate method="post" action="{{action('UserController@save_data')}}" enctype="multipart/form-data">
                                           @csrf
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>Username</label>
                                                            <input type="hidden" name="image" value="{{auth()->user()->image}}">
                                                            <input type="text" name="username" class="form-control" placeholder="Username" value="{{auth()->user()->name}}" required data-validation-required-message="This username field is required">
                                                        </div>
                                                    </div>
                                                 
                                                    <div class="form-group">
                                                        <div class="controls">
                                                            <label>E-mail</label>
                                                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{auth()->user()->email}}" required data-validation-required-message="This email field is required">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">

                                                    <div class="form-group" >
                                                        <label>Status</label>
                                                        @if(auth()->user()->is_active==1)
                                                        <select class="form-control" name="is_active">
                                                            <option value=1>Active</option>
                                                            <option value=2>Deactivated</option>
                                                        </select>
                                                        @else
                                                        <select class="form-control" disabled="true" name="is_active">
                                                            <option value=1>Active</option>
                                                            <option value=2>Deactivated</option>
                                                        </select>
                                                        @endif
                                                    </div>
                                                    <div class="form-group" >
                                                        <label>Role</label>
                                                        @if(auth()->user()->is_admin==1)
                                                        <select class="form-control" name="is_admin">
                                                            <option value=1>Admin</option>
                                                            <option value=2>User</option>
                                                        </select>
                                                        @else
                                                        <select class="form-control" disabled="true" name="is_admin">
                                                            <option value=1>Admin</option>
                                                            <option value=2>User</option>
                                                        </select>
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-12">
                                                    <div class="table-responsive border rounded px-1 ">
                                                        <h6 class="border-bottom py-1 mx-1 mb-0 font-medium-2"><i class="feather icon-lock mr-50 "></i>Permission</h6>
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
                                                                    <td>Users</td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox1" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox1"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox2" class="custom-control-input"><label class="custom-control-label" for="users-checkbox2"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox3" class="custom-control-input"><label class="custom-control-label" for="users-checkbox3"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox4" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox4"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Articles</td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox5" class="custom-control-input"><label class="custom-control-label" for="users-checkbox5"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox6" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox6"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox7" class="custom-control-input"><label class="custom-control-label" for="users-checkbox7"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox8" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox8"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Staff</td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox9" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox9"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox10" class="custom-control-input" checked>
                                                                            <label class="custom-control-label" for="users-checkbox10"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox11" class="custom-control-input"><label class="custom-control-label" for="users-checkbox11"></label>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="custom-control custom-checkbox"><input type="checkbox" id="users-checkbox12" class="custom-control-input"><label class="custom-control-label" for="users-checkbox12"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1">Save
                                                        Changes</button>
                                                    <a type="button" href="{{url('user_view').'/'.auth()->user()->id}}" class="btn btn-outline-warning">Cancel</a>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- users edit account form ends -->
                                    </div>
                                    
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- users edit ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
    <!-- BEGIN: Page JS-->
    <script src="app-assets/js/scripts/pages/app-user.js"></script>
    <script src="app-assets/js/scripts/navs/navs.js"></script>
    <!-- END: Page JS-->
    @stop