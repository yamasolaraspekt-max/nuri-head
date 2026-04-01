@extends('admin.layouts.app')

@section('title') File Upload @endsection

@section('style')

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/ui/prism.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css')}}">
    <!-- END: Vendor CSS-->

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/file-uploaders/dropzone.css')}}">
    <!-- END: Page CSS-->

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
                            <h2 class="content-header-title float-left mb-0">File Uploader</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Extensions</a>
                                    </li>
                                    <li class="breadcrumb-item active">File Uploader
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Dropzone section start -->
                <section id="dropzone-examples">

                    <!-- warnings and info alerts starts -->
                    <div class="row">
                        <div class="col-12">
                        
                            <div class="alert alert-info" role="alert">
                                <strong>Info: </strong>
                                We have changed path of error and success mark from vendor's css.
                            </div>
                        </div>
                    </div>
                    <!-- warnings and info alerts ends -->

                    <!-- single file upload starts -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Single File Upload</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                    <form method="post" action="{{url('file')}}" enctype="multipart/form-data">
                                            @csrf      
                                            <div class="input-group demo control-group lst increment" >
                                                <input type="file" name="filenames[]" class="myfrm form-control">
                                                <div class="input-group-btn"> 
                                                    <button class="btn btn-success" type="button">Add</button>
                                                </div>
                                            </div>
                                            <div class="clone hide">
                                                <div class="demo control-group lst input-group" style="margin-top:10px">
                                                    <input type="file" name="filenames[]" class="myfrm form-control">
                                                    <div class="input-group-btn"> 
                                                        <button class="btn btn-danger remove_btn" type="button">Remove</button>
                                                    </div>
                                                </div>
                                            </div>      
                                            <button type="submit" class="btn btn-success submit_btn" style="margin-top:10px">Submit</button>      
                                        </form> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- single file upload ends --> 
                </section>
                <!-- // Dropzone section end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->
    @endsection

    @section('script')
    <script type="text/javascript">
            $(document).ready(function() {
              $(".submit_btn").click(function(){ 
                  var lsthmtl = $(".clone").html();
                  $(".increment").after(lsthmtl);
              });
              $("body").on("click",".remove_btn",function(){ 
                  $(this).parents(".demo").remove();
              });
            });
        </script>  



    @endsection