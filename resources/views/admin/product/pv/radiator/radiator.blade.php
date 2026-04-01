@extends('admin.layouts.app')

@section('title')  WECHSELRICHTER @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

@endsection
@section('content')


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-6 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-6">
                            <h2 class="content-header-title float-left mb-0">PRODUCT</h2>
                            <div class="breadcrumb-wrapper col-6">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">WECHSELRICHTER</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Neu</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-6 col-6">
                            <form method="post" action="{{ action('App\Http\Controllers\RadiatorController@store') }}">

                            @csrf
                            <input type="hidden" name="product_id" value="{{ request()->id }}">
                            <div class="card">
                                @include('admin.product.pv.radiator.pages.profile')
                             </div>
                             <div class="card">
                                @include('admin.product.pv.radiator.pages.electric_dc')

                             </div>
                             <div class="card">
                                @include('admin.product.pv.radiator.pages.electric_ac')
                             </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="card">
                                @include('admin.product.pv.radiator.pages.others')

                             </div>
                       
                             <div class="card">
                                @include('admin.product.pv.radiator.pages.mpp')

                             </div>
                       <button type="submit" class="btn btn-primary">Nächste</button>
                           
                        </div>
                    </form>
                    </div>
                 </section>
    <!-- // Basic Horizontal form layout section end -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')


 
<script src="{{ asset('js/select2.min.js') }}"></script>

<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBgavU38-YXH1ar9VEneTHJIBQYcjC8PgI&callback=console.debug&libraries=maps,marker&v=beta">
</script>

<script>
        $(document).ready(function() {
            $('#product').select2();
        });

  

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