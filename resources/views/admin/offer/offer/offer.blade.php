@extends('admin.layouts.app')
@section('title') Product Offer @endsection

@section('style')
    <style>
        .menu_items {
        display: flex;
        justify-content: space-evenly;
        list-style: none;
        position: absolute;
        }
     #title {
    display: none;
    position: relative;
    bottom: 100%; /* Position it above the button */
    left: 50%; /* Center horizontally */
    transform: translateX(-50%); /* Center horizontally */
    background-color: #ffffff00;
    padding: 5px;
    border: 1px solid #cccccc00;  
    }
    
    #button:hover #title {
    display: block;
    }
    </style>
@endsection
@section('content') 
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-10 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                         @include('admin.offer.offer.menu')
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-2 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- account setting page start -->
                <section id="page-account-settings">
                    <div class="row">
                        <!-- left menu section -->
                        <!-- right content section -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane active" id="account-vertical-general" aria-labelledby="account-pill-general" aria-expanded="true">
                                               @yield('details')
                                            </div>
                                             
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- account setting page end -->
                <form method="post" action="{{ route('offer.print')}}">
                    @csrf
                <div class="col-8">
                    <button type="submit" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light"><i class="feather icon-home"></i> Create Offer</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    
@endsection

@section('script')

<script src="{{ asset('app-assets/js/scripts/popover/popover.js') }}"></script>
    <script>
        document.getElementById('button').addEventListener('mouseover', function() {
        document.getElementById('title').classList.add('active');
        });
        
        document.getElementById('button').addEventListener('mouseout', function() {
        document.getElementById('title').classList.remove('active');
        });
    </script>
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